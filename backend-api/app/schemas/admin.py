from pydantic import BaseModel, EmailStr, Field, model_validator
from typing import Optional, List, Literal
from datetime import datetime

# Schema para sa pag list ng close won tickets
class CloseWonTicketResponseSchema(BaseModel):
    id: str
    inquiry_id: Optional[str] = None
    company_name: Optional[str] = None
    contact_person: Optional[str] = None
    email: EmailStr
    phone_number: Optional[str] = None
    agreed_amount: Optional[float] = 0.0
    created_at: Optional[str] = None
    customer_id: Optional[str] = None       
    ticket_status: Optional[str] = "for account" # Default status: 'for account' o 'created'
    pickup_datetime: Optional[str] = None
    pickup_address: Optional[str] = None

    class Config:
        from_attributes = True

# Schema para sa Admin account creation form
class CreateCustomerFromTicketSchema(BaseModel):
    ticket_id: str  # Id ng closed won ticket table
    email: EmailStr # Pre-filled email address ng customer
    password: str
    first_name: str
    last_name: str
    company_name: Optional[str] = None
    phone_number: Optional[str] = None

class CustomerUserResponse(BaseModel):
    id: str
    email: str
    first_name: Optional[str] = None
    last_name: Optional[str] = None
    company_name: Optional[str] = None
    phone_number: Optional[str] = None
    created_at: Optional[str] = None

    class Config:
        from_attributes = True


# Schema para sa Admin "Add New Customer" form (customers.php)
#   account_type = 'business'   -> Business to Business (B2B): company_name REQUIRED
#   account_type = 'individual' -> Customer to Business (B2C): walang company fields
class AdminCreateCustomerSchema(BaseModel):
    account_type: Literal["business", "individual"] = Field(
        ..., description="Segment ng account: 'business' (B2B) or 'individual' (B2C)."
    )

    # --- Shared / mandatory for BOTH segments -------------------------------
    first_name: str = Field(..., min_length=1, max_length=80)
    last_name: str = Field(..., min_length=1, max_length=80)
    email: EmailStr
    phone_number: str = Field(
        ...,
        min_length=7,
        max_length=20,
        pattern=r"^\+?[0-9][0-9\s\-().]{5,19}$",
        description="Phone number (7-20 digits, optional leading +, spaces/dashes allowed).",
    )
    password: str = Field(..., min_length=8, description="Password must be at least 8 characters.")

    # --- B2B only (company_name is mandatory when account_type = business) --
    company_name: Optional[str] = Field(None, max_length=160)
    contact_person: Optional[str] = Field(None, max_length=160)
    address: Optional[str] = Field(None, max_length=255)
    tax_id: Optional[str] = Field(None, max_length=40, description="TIN / DTI-SEC registration number.")
    website: Optional[str] = Field(None, max_length=160)

    @model_validator(mode="after")
    def _enforce_segment_requirements(self):
        company = (self.company_name or "").strip()

        if self.account_type == "business":
            if not company:
                raise ValueError("Company name is required for a Business to Business account.")
            self.company_name = company
        else:
            # Individual / B2C: huwag i-save ang company fields
            self.company_name = None
            self.contact_person = None
            self.tax_id = None
            self.website = None

        self.first_name = self.first_name.strip()
        self.last_name = self.last_name.strip()
        if not self.first_name or not self.last_name:
            raise ValueError("First name and last name are required.")

        return self


# -----------------------------------------------------------------------------
# Admin Customer Management - Actions workflow (customers.php)
#   PATCH /api/v1/admin/customer-accounts/status  -> CustomerStatusUpdateSchema
#   POST  /api/v1/admin/delete-customer           -> AdminDeleteCustomerSchema
# -----------------------------------------------------------------------------

# Hard-coded so the API, the SQL CHECK constraint in
# backend-api/database/customer_status.sql and the UI badges all agree.
CUSTOMER_STATUSES = ("Active", "Inactive", "Deactivated")


class CustomerStatusUpdateSchema(BaseModel):
    user_id: str = Field(..., min_length=1, description="public.users.id (auth uid) of the customer.")
    status: Literal["Active", "Inactive", "Deactivated"] = Field(
        ...,
        description=(
            "Active = normal access; Inactive = parked (data kept, sign-in still works); "
            "Deactivated = locked out and permanently banned in Auth."
        ),
    )

    @model_validator(mode="after")
    def _normalize(self):
        self.user_id = self.user_id.strip()
        if not self.user_id:
            raise ValueError("user_id is required.")
        return self


class AdminDeleteCustomerSchema(BaseModel):
    user_id: str = Field(..., min_length=1, description="public.users.id (auth uid) to erase permanently.")
    privacy_ack: bool = Field(
        False,
        description="Must be true - the admin confirms the deletion follows the Privacy Policy.",
    )
    deletion_reason: str = Field(
        ...,
        min_length=10,
        max_length=500,
        description="Audit note recorded in the server log: why this record is being erased.",
    )

    @model_validator(mode="after")
    def _enforce_privacy_ack(self):
        self.user_id = self.user_id.strip()
        if not self.user_id:
            raise ValueError("user_id is required.")

        if self.privacy_ack is not True:
            raise ValueError(
                "You must acknowledge the Privacy Policy before permanently deleting this account."
            )

        reason = (self.deletion_reason or "").strip()
        if len(reason) < 10:
            raise ValueError(
                "A deletion reason of at least 10 characters is required for the audit trail."
            )
        self.deletion_reason = reason
        return self

