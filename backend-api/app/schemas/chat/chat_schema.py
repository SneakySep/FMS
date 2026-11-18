from pydantic import BaseModel, Field
from typing import Optional

class ChatMessageCreate(BaseModel):
    customer_id: str
    message: str = Field(..., min_length=1, description="Customer Message")

class AIAnalysisResponse(BaseModel):
    sentiment: str  # "positive", "neutral", "negative", o "frustrated"
    escalate: bool
    reply: str