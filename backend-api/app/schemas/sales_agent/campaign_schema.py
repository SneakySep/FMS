from pydantic import BaseModel, Field
from typing import Optional
from datetime import datetime

class CampaignCreateSchema(BaseModel):
    title: str = Field(..., min_length=3, description="Title")
    description: str = Field(..., min_length=5, description="Description")
    image_url: Optional[str] = Field(None, description="Supabase storage public URL ng poster")
    target_audience: str = Field("all_customers", description="'all_customers', 'active_sla', o 'standard_tc'")
    is_permanent: bool = Field(True, description="Example: True kung permanent, False kung may expiration")
    expires_at: Optional[datetime] = Field(None, description="Petsa at oras kung kailan mag-e-expire ang kampanya")