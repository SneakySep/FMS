from pydantic import BaseModel, Field
from typing import Optional

class NotificationCreateSchema(BaseModel):
    recipient_id: Optional[str] = Field(None, description="UUID ng particular na user")
    target_role: str = Field(..., description="Role na makatatanggap ('sales_agent', 'manager', 'super_admin', 'customer')")
    title: str = Field(..., min_length=2, description="Notification title")
    message: str = Field(..., min_length=5, description="full detailed message ng notification")
    type: str = Field(..., description="Uri ng notification (e.g., 'new_inquiry', 'call_report', 'shipment_update')")
    reference_id: Optional[str] = Field(None, description="ID ng record (inquiry_id o report_id)")

class NotificationUpdateSchema(BaseModel):
    is_read: bool = Field(True, description="Mark as read or unread")