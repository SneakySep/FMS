from pydantic import BaseModel, EmailStr
from typing import Optional, List
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
   