from pydantic import BaseModel, Field
from typing import Optional

class CreateCustomerAccountSchema(BaseModel):
    ticket_id: str = Field(..., description="UUID ng ticket galing sa customer_tickets table")
    password: Optional[str] = Field(None, description="Optinal")