from pydantic import BaseModel, EmailStr
from typing import Optional

# 1. Schema para sa pag-login ng user
class LoginRequest(BaseModel):
    email: EmailStr
    password: str

# 2. Schema para sa Token response galing sa Supabase Auth
class TokenResponse(BaseModel):
    access_token: str
    token_type: str = "bearer"
    refresh_token: Optional[str] = None

# 3. Schema para sa Employee Profile data 
class EmployeeResponse(BaseModel):
    employee_id: str        
    auth_id: str            
    first_name: str
    last_name: str
    email: EmailStr
    phone: Optional[str] = None
    position: Optional[str] = None
    role: str               
    created_at: str

    class Config:
        from_attributes = True

# 4. Combined response pag successful ang login 
class AuthResponse(BaseModel):
    message: str
    token: TokenResponse
    employee: EmployeeResponse

# 5. Sync sa crm project
class SyncAuthResponse(BaseModel):
    message: str
    token: TokenResponse
    employee: EmployeeResponse
    synced_to_crm: bool

class EmployeeCreateRequest(BaseModel):
    email: EmailStr
    password: str
    first_name: str
    last_name: str
    position: str
    role: str 
    employee_id: str

class VerifyOTPRequest(BaseModel):
    email: EmailStr
    otp: str