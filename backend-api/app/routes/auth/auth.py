# Auth Endpoint
from fastapi import APIRouter, HTTPException, status, Request, Depends
from app.schemas.auth import LoginRequest, VerifyOTPRequest
from app.supabase_config.supabase import supabase, redis_client, supabase_secondary, get_auth_client_secondary
import random
import jwt
from datetime import datetime, timezone, timedelta
from app.middleware.helper import send_otp_email
from app.middleware.security import get_current_user, RoleChecker
from app.service.audit_service import log_audit
from app.config.config import settings

auth_router = APIRouter(
    prefix="/api/auth",
    tags=["Authentication"]
)

MAX_FAILED_ATTEMPTS = 5
LOCKOUT_MINUTES = 15
ALGORITHM = "HS256"

# ======= Login Endpoint =======
@auth_router.post("/login")
async def Login_user(payload: LoginRequest, request: Request):
    client_ip = request.client.host if request.client else "unknown"
    email = payload.email.strip().lower()

    try:
        # 1. Check muna kung naka-lockout ang user
        restriction_res = (
            supabase_secondary.table("login_restrictions")
            .select("*")
            .eq("email", email)
            .maybe_single()
            .execute()
        )

        if restriction_res and getattr(restriction_res, "data", None):
            data = restriction_res.data
            if data.get("is_locked"):
                lockout_until_str = data.get("lockout_until")
                if lockout_until_str:
                    lockout_until = datetime.fromisoformat(lockout_until_str)
                    if datetime.now(timezone.utc) < lockout_until:
                        await log_audit(email, "LOGIN_BLOCKED_LOCKED_OUT", client_ip, {"reason": "User is currently locked out."})
                        raise HTTPException(
                            status_code=status.HTTP_403_FORBIDDEN,
                            detail=f"Account is temporarily locked due to multiple failed attempts. Try again after {LOCKOUT_MINUTES} minutes."
                        )
            supabase_secondary.table("login_restrictions").update({
                "failed_attempts": 0,
                "is_locked": False,
                "lockout_until": None
            }).eq("email", email).execute()

        # 2. Kunin ang employee profile sa CRM database para makuha ang role
        emp_res = (
            supabase_secondary.table("employees")
            .select("*")
            .ilike("email", email)
            .maybe_single()
            .execute()
        )
        emp_data = emp_res.data if emp_res and getattr(emp_res, "data", None) else None

        if not emp_data:
            print(f"DEBUG LOGIN: Employee with email {email} not found in employees table.")
            await handle_failed_login(email, client_ip, "Employee not found")
            raise HTTPException(
                status_code=status.HTTP_401_UNAUTHORIZED,
                detail="Invalid email or password"
            )

        user_role = emp_data.get("role")
        print(f"========================================")
        print(f"DEBUG LOGIN ATTEMPT: Email -> {email} | Role -> {user_role}")
        print(f"========================================")

        # 3. Authenticate user sa Supabase 
        auth_client = get_auth_client_secondary()
        try:
            auth_response = auth_client.auth.sign_in_with_password({
                "email": email,
                "password": payload.password
            })
        except Exception as auth_err:
            print(f"SUPABASE AUTH ERROR for {email}: {str(auth_err)}")
            await handle_failed_login(email, client_ip, str(auth_err))
            raise HTTPException(
                status_code=status.HTTP_401_UNAUTHORIZED,
                detail="Invalid email or password"
            )

        if not auth_response or not auth_response.user:
            await handle_failed_login(email, client_ip, "No user returned from auth")
            raise HTTPException(
                status_code=status.HTTP_401_UNAUTHORIZED,
                detail="Invalid email or password"
            )

        user = auth_response.user

        # 4. Reset failed attempts matapos ang successful login
        supabase_secondary.table("login_restrictions").upsert({
            "email": email,
            "failed_attempts": 0,
            "is_locked": False,
            "lockout_until": None,
            "updated_at": datetime.now(timezone.utc).isoformat()
        }, on_conflict="email").execute()

        # 5. Generate OTP at i-save sa Redis (valid for 2 mins)
        otp_code = str(random.randint(100000, 999999))
        print(f"========================================")
        print(f"DEBUG OTP for {email} ({user_role}): {otp_code}")
        print(f"========================================")

        redis_client.setex(f"otp:{email}", 120, otp_code)

        # 6. Send OTP via email
        email_sent = send_otp_email(email, otp_code)
        if not email_sent:
            print(f"Warning: OTP email failed to send to {email}.")

        # 7. Log audit
        await log_audit(email, "LOGIN_SUCCESS_OTP_SENT", client_ip, {"auth_id": user.id, "role": user_role})

        return {
            "message": "Login successful. Please check your email for the OTP code verification (Valid for 2 minutes).",
            "requires_otp": True,
            "email": email,
            "role": user_role
        }

    except HTTPException as he:
        raise he
    except Exception as err:
        print(f"CRITICAL LOGIN ERROR: {str(err)}") 
        raise HTTPException(
            status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
            detail=f"Internal server error during login: {str(err)}"
        )
        

@auth_router.post("/verify-otp")
async def verify_otp(payload: VerifyOTPRequest, request: Request):
    client_ip = request.client.host if request.client else "unknown"
    email = payload.email.strip().lower()
    otp = payload.otp.strip()

    # 1. Verify OTP mula sa Redis
    stored_otp = redis_client.get(f"otp:{email}")
    stored_otp_str = stored_otp.decode("utf-8") if isinstance(stored_otp, bytes) else str(stored_otp or "")

    if not stored_otp_str or stored_otp_str != otp:
        await log_audit(email, "OTP_VERIFICATION_FAILED", client_ip, {"error": "Invalid or expired OTP"})
        raise HTTPException(
            status_code=status.HTTP_400_BAD_REQUEST,
            detail="Invalid or expired OTP code."
        )

    redis_client.delete(f"otp:{email}")

    try:
        # 2. Kunin ang employee profile direkta sa CRM database 
        emp_res = (
            supabase_secondary.table('employees')
            .select('*')
            .ilike('email', email)
            .maybe_single()
            .execute()
        )

        emp_data = emp_res.data if emp_res and getattr(emp_res, "data", None) else None

        if not emp_data:
            raise HTTPException(
                status_code=status.HTTP_404_NOT_FOUND,
                detail=f"Employee profile with email '{email}' not found in CRM database."
            )

        # 3. Gumawa ng JWT Access Token gamit ang settings.SUPABASE_JWT_SECRET
        token_data = {
            "sub": emp_data.get("email"),
            "id": emp_data.get("id"),
            "role": emp_data.get("role")
        }
        access_token_expires = timedelta(hours=8)
        expire = datetime.now(timezone.utc) + access_token_expires
        token_data.update({"exp": expire})
        
        access_token = jwt.encode(token_data, settings.SUPABASE_JWT_SECRET, algorithm=ALGORITHM)

        await log_audit(email, "OTP_VERIFIED_SUCCESS", client_ip, {"employee_id": emp_data.get("employee_id")})

        return {
            "message": "OTP verified successfully.",
            "access_token": access_token,
            "token_type": "bearer",
            "employee": emp_data
        }

    except HTTPException as he:
        raise he
    except Exception as err:
        import traceback
        traceback.print_exc()  
        print(f"VERIFY OTP ERROR: {str(err)}")
        await log_audit(email, "VERIFY_OTP_ERROR", client_ip, {"error": str(err)})
        raise HTTPException(
            status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
            detail=f"Failed to verify OTP: {str(err)}"
        )
    
# ====== RBAC Employee Endpoint ======
@auth_router.get("/me")
async def get_current_employee_profile(current_user = Depends(get_current_user)):
    try:
        profile_res = (
            supabase_secondary.table('employees')
            .select('*')
            .eq('auth_id', current_user.id)
            .maybe_single()
            .execute()
        )
        
        return {
            "auth_user": current_user,
            "employee_profile": profile_res.data if profile_res and getattr(profile_res, "data", None) else None
        }
    except Exception as e:
        raise HTTPException(
            status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
            detail=f"Failed to fetch profile: {str(e)}"
        )

#========= Super Admin Endpoint =========
@auth_router.get("/admin/analytics")
async def admin_analytics(current_user = Depends(RoleChecker(["super_admin"]))):
    return {
        "message": "Welcome to the Super Admin analytics dashboard!",
        "admin_email": current_user.email
    }

# ========= Helper Function for Failed Login Attempts =========
async def handle_failed_login(email: str, client_ip: str, error_detail: str = None):
    try:
        res = (
            supabase_secondary.table('login_restrictions')
            .select('*')
            .eq('email', email)
            .maybe_single()
            .execute()
        )

        current_attempts = 1
        if res and getattr(res, "data", None):
            current_attempts = res.data.get("failed_attempts", 0) + 1

        is_locked = current_attempts >= MAX_FAILED_ATTEMPTS
        lockout_until = (
            (datetime.now(timezone.utc) + timedelta(minutes=LOCKOUT_MINUTES)).isoformat()
            if is_locked else None
        )

        supabase_secondary.table('login_restrictions').upsert({
            "email": email,
            "failed_attempts": current_attempts,
            "is_locked": is_locked,
            "lockout_until": lockout_until,
            "updated_at": datetime.now(timezone.utc).isoformat()
        }, on_conflict="email").execute()

        action = "ACCOUNT_LOCKED" if is_locked else "LOGIN_FAILED"
        await log_audit(email, action, client_ip, {
            "failed_attempts": current_attempts,
            "error": error_detail
        })

    except Exception as db_err:
        print(f"Error updating login restrictions: {str(db_err)}")