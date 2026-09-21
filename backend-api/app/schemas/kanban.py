from pydantic import BaseModel
from typing import List, Optional

class KanbanCard(BaseModel):
    id: str
    inquiry_code: Optional[str] = None
    company_name: str
    contact_person: Optional[str] = None
    cargo_details: Optional[str] = None
    service_type: Optional[str] = None
    estimated_amount: Optional[float] = None
    created_at: Optional[str] = None

class KanbanColumn(BaseModel):
    title: str
    status_key: str
    count: int
    items: List[KanbanCard]

class MoveCardRequest(BaseModel):
    card_id: str
    new_status: str