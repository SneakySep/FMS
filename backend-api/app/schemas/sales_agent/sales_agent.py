from pydantic import BaseModel
from typing import Optional
from datetime import datetime

class ManualInquiryCreate(BaseModel):
    company_name: str
    contact_person: str
    email: str
    phone_number: Optional[str] = None
    service_type: Optional[str] = None
    origin: Optional[str] = None
    destination: Optional[str] = None
    cargo_details: Optional[str] = None
    estimated_amount: Optional[float] = 0.00
    pickup_address: Optional[str] = None
    pickup_datetime: Optional[datetime] = None
    handling: Optional[str] = None
    service: Optional[str] = None