from pydantic import BaseModel, EmailStr
from typing import Optional

class InquiryUpdateSchema(BaseModel):
    company_name: Optional[str] = None
    contact_person: Optional[str] = None
    email: Optional[EmailStr] = None
    phone_number: Optional[str] = None
    service_type: Optional[str] = None
    origin: Optional[str] = None
    destination: Optional[str] = None
    cargo_details: Optional[str] = None
    status: Optional[str] = None
    estimated_amount: Optional[float] = None
    pickup_address: Optional[str] = None
    pickup_datetime: Optional[str] = None
    handling: Optional[str] = None
    service: Optional[str] = None

    customer_id: Optional[str] = None
    contract_type: Optional[str] = None  # e.g., 'standard_tc' o 'sla'
    sla_reason: Optional[str] = None

class KanbanStatusUpdateSchema(BaseModel):
    status: str