from pydantic import BaseModel, Field
from typing import Optional
from datetime import date
from decimal import Decimal

class SLACreateRequest(BaseModel):
    inquiry_id: str
    customer_id: str
    template_id: Optional[str] = None
    start_date: date
    end_date: date
    auto_renew: bool = True
    notice_days: int = 30
    mode: str
    origin: str
    destination: str
    rate: Decimal
    rate_basis: str
    currency: str = "PHP"
    fuel_surcharge_pct: Optional[Decimal] = None
    payment_terms: str
    
    transit_days: int
    pickup_window_hours: int
    ontime_target_pct: Decimal
    tracking_interval_hours: int
    claims_response_hours: int
    late_credit_pct_per_day: Decimal
    credit_cap_pct: Decimal
    liability_limit_pct: Decimal

class SLAResponse(BaseModel):
    id: str
    contract_no: str
    status: str
    pdf_path: Optional[str] = None
    created_at: str

    class Config:
        from_attributes = True