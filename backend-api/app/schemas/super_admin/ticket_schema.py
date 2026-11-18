from pydantic import BaseModel, Field, EmailStr
from typing import Optional
from datetime import datetime

class CustomerTicketCreateSchema(BaseModel):
    inquiry_id: str = Field(..., description="UUID ng closed_won inquiry")
    company_name: str
    contact_person: str
    email: EmailStr
    phone_number: Optional[str] = None
    pickup_datetime: Optional[datetime] = None

class CustomerTicketUpdateStatusSchema(BaseModel):
    ticket_status: str = Field(..., description="'for_account', 'created', o 'inactive'")