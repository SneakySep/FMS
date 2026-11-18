from pydantic import BaseModel, Field, EmailStr
from typing import Optional

class CallReportCreateSchema(BaseModel):
    call_time: str = Field(..., description="Oras ng tawag (e.g., '10:50 AM')")
    client_name: str = Field(..., min_length=2, description="Pangalan ng kumpanya o agency")
    contact_person: Optional[str] = Field(None, description="Pangalan ng nakausap")
    contact_tel_no: Optional[str] = Field(None, description="Numero ng telepono")
    contact_email: Optional[EmailStr] = Field(None, description="Email address ng kliyente")
    remarks: str = Field(..., min_length=5, description="Detalye o notes ng napag-usapan")

class CallReportResponseSchema(CallReportCreateSchema):
    id: str
    agent_id: Optional[str]
    report_date: str
    status: str
    rating: str
    confidence_score: Optional[float] = None
    is_overridden: bool = False

    class Config:
        from_attributes = True