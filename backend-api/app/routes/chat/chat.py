from fastapi import APIRouter, HTTPException, status, Depends
from pydantic import BaseModel
from datetime import datetime, timezone
from app.supabase_config.supabase import supabase_secondary
from app.middleware.security import RoleChecker 

agent_chat_router = APIRouter(
    prefix="/api/agent/chat",
    tags=["Sales Agent Chat Management"]
)

class AgentReplySchema(BaseModel):
    conversation_id: str
    agent_id: str
    message: str

@agent_chat_router.post("/reply", status_code=status.HTTP_200_OK)
async def agent_reply_to_customer(
    payload: AgentReplySchema,
    current_user = Depends(RoleChecker(["sales_agent", "manager", "super_admin"]))
):
    """
    Endpoint para sa Sales Agent reply:
    - Protektado ng RoleChecker para sa mga authorized sales personnel lang.
    - Nagse-save ng mensahe at nag-re-reset ng 3-hour timer.
    """
    try:
        now_utc = datetime.now(timezone.utc)
        
        # 1. I-save ang mensahe ng ahente sa chat_messages table
        supabase_secondary.table("chat_messages").insert({
            "conversation_id": payload.conversation_id,
            "sender_type": "sales_agent",
            "sender_id": payload.agent_id,
            "message": payload.message,
            "sentiment": "neutral"
        }).execute()
        
        # 2. I-update ang conversation para i-reset ang timer at i-set ang status pabalik sa active
        supabase_secondary.table("chat_conversations").update({
            "sales_agent_id": payload.agent_id,
            "last_agent_reply_at": now_utc.isoformat(),
            "status": "active"
        }).eq("id", payload.conversation_id).execute()
        
        return {
            "success": True,
            "message": "Agent reply sent successfully and timer reset."
        }
        
    except Exception as e:
        raise HTTPException(
            status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
            detail=f"Failed to send agent reply: {str(e)}"
        )