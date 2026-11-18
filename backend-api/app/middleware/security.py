# RBAC File (Pure CRM Database & Local JWT Verification)

from fastapi import Depends, HTTPException, status
from fastapi.security import HTTPBearer, HTTPAuthorizationCredentials
import jwt
from app.supabase_config.supabase import supabase_secondary
from app.config.config import settings

SUPABASE_JWT_SECRET = settings.SUPABASE_JWT_SECRET

security_scheme = HTTPBearer()

ALGORITHM = "HS256"

# 1. VERIFY JWT TOKEN  GAMIT ANG SUPABASE_JWT_SECRET
def get_current_user(credentials: HTTPAuthorizationCredentials = Depends(security_scheme)):
    token = credentials.credentials

    try:
        # I-decode ang token gamit ang secret key mula sa .env 
        payload = jwt.decode(token, SUPABASE_JWT_SECRET, algorithms=[ALGORITHM])
        
        email: str = payload.get("sub")
        if not email:
            raise HTTPException(
                status_code=status.HTTP_401_UNAUTHORIZED,
                detail="Invalid authentication credentials (missing subject)"
            )
        
        # Gumawa ng simpleng user object/dict 
        class AuthUser:
            def __init__(self, email: str, user_id: str, role: str):
                self.email = email
                self.id = user_id
                self.role = role

        return AuthUser(
            email=email,
            user_id=payload.get("id"),
            role=payload.get("role")
        )

    except jwt.ExpiredSignatureError:
        raise HTTPException(
            status_code=status.HTTP_401_UNAUTHORIZED,
            detail="Token has expired"
        )
    except jwt.PyJWTError as e:
        print(f"JWT validation error: {str(e)}")
        raise HTTPException(
            status_code=status.HTTP_401_UNAUTHORIZED,
            detail="Could not validate credentials"
        )
    except Exception as e:
        print(f"Auth token validation error: {str(e)}")
        raise HTTPException(
            status_code=status.HTTP_401_UNAUTHORIZED,
            detail="Could not validate credentials"
        )


# 2. RBAC GUARD CLASS
class RoleChecker:
    def __init__(self, allowed_roles: list[str]):
        self.allowed_roles = allowed_roles

    def __call__(self, current_user = Depends(get_current_user)):
        try:
            # Kunin ang profile sa CRM database 
            profile = (
                supabase_secondary.table('employees')
                .select('role, employee_id, email, id')
                .eq('email', current_user.email)
                .maybe_single()
                .execute()
            )
        except Exception as err:
            print(f"RBAC DB query failed: {str(err)}")
            raise HTTPException(
                status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
                detail="Error checking user permissions"
            )

        # I-check kung may nahanap na profile data
        if not profile or not profile.data:
            print(f"RBAC Error: No employee profile found for email -> {current_user.email}")
            raise HTTPException(
                status_code=status.HTTP_403_FORBIDDEN,
                detail="Employee profile not found"
            )

        user_role = profile.data.get("role")
        print(f"RBAC Check: User Role is '{user_role}', Allowed Roles are {self.allowed_roles}")

        # Verify permission
        if user_role not in self.allowed_roles:
            raise HTTPException(
                status_code=status.HTTP_403_FORBIDDEN,
                detail="You don't have permission to access this resource."
            )

        return current_user