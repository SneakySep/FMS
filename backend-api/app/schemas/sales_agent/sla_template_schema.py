from pydantic import BaseModel, Field
from typing import Optional

class SLATemplateCreateSchema(BaseModel):
    name: str = Field(..., min_length=2, description="Name of the SLA template")
    mode: str = Field(..., description="Mode of the SLA template")
    transit_days: int = Field(..., gt=0, description="Transit days for the SLA template")
    pickup_window_hours: int = Field(..., gt=0, description="Pickup window hours for the SLA template")
    ontime_target_pct: float = Field(..., ge=0, le=100, description="On-time target percentage for the SLA template")
    tracking_interval_hours: int = Field(..., gt=0, description="Tracking interval hours for the SLA template")
    claims_response_hours: int = Field(..., gt=0, description="Claim response hours for the SLA template")
    late_credit_pct_per_day: float = Field(..., ge=0, le=100, description="Late credit percentage for the SLA template")
    credit_cap_pct: float = Field(..., ge=0, le=100, description="Credit cap percentage for the SLA template")
    liability_limit_pct: float = Field(..., ge=0, le=100, description="Liability limit percentage for the SLA template")
    is_active: bool = Field(default=True, description="Indicates if the SLA template is active")

class SLATemplateUpdateSchema(BaseModel):
    name: Optional[str] = None
    mode: Optional[str] = None
    transit_days: Optional[int] = Field(None, gt=0)
    pickup_window_hours: Optional[int] = Field(None, gt=0)
    ontime_target_pct: Optional[float] = Field(None, ge=0, le=100)
    tracking_interval_hours: Optional[int] = Field(None, gt=0)
    claims_response_hours: Optional[int] = Field(None, gt=0)
    late_credit_pct_per_day: Optional[float] = Field(None, ge=0, le=100)
    credit_cap_pct: Optional[float] = Field(None, ge=0, le=100)
    liability_limit_pct: Optional[float] = Field(None, ge=0, le=100)
    is_active: Optional[bool] = None
