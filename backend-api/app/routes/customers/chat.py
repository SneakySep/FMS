from fastapi import APIRouter, HTTPException, status
from app.schemas.customer import ChatRequest
from app.service.ai_service import process_customer_chat
from app.supabase_config.database import chat_collection

router = APIRouter(
    prefix="/api/v1/chat",
    tags=["Customer AI Portal"]
)

@router.post("", status_code=status.HTTP_200_OK)
async def chat_endpoint(payload: ChatRequest):

    try:

        reply = await process_customer_chat(
            customer_id=payload.customer_id,
            user_message=payload.message
        )

        return {
            "status": "success",
            "reply": reply
        }

    except Exception as e:
        raise HTTPException(
            status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
            detail=str(e)
        )

# 2. GET ACTIVE CONVERSATIONS Para sa Inbox list ng Sales Agent
@router.get("/active-conversations", status_code=status.HTTP_200_OK)
async def get_active_conversations():
    try:
        cursor = chat_collection.find({}, {"customer_id": 1, "history": {"$slice": -1}, "status": 1, "last_updated": 1})
        conversations = []
        async for doc in cursor:
            history = doc.get("history", [])
            last_msg = history[-1]["parts"][0]["text"] if history else "No messages"
            conversations.append({
                "customer_id": doc.get("customer_id"),
                "last_message": last_msg,
                "status": doc.get("status", "ai_active")
            })
        return conversations
    except Exception as e:
        raise HTTPException(status_code=500, detail=str(e))

# 3. GET SPECIFIC CHAT HISTORY Para kapag pinalitan ni Sales Agent ang napiling Customer
@router.get("/history/{customer_id}", status_code=status.HTTP_200_OK)
async def get_chat_history(customer_id: str):
    try:
        doc = await chat_collection.find_one({"customer_id": customer_id})
        if not doc:
            return {"history": []}
        return {"customer_id": customer_id, "history": doc.get("history", [])}
    except Exception as e:
        raise HTTPException(status_code=500, detail=str(e))

# 4. MANUAL HANDOVER BACK TO AI Button para ma-reset agad kay AI
@router.post("/handover/{customer_id}", status_code=status.HTTP_200_OK)
async def handover_to_ai(customer_id: str):
    try:
        await chat_collection.update_one(
            {"customer_id": customer_id},
            {"$set": {"status": "ai_active"}}
        )
        return {"status": "success", "message": "Handed back to AI successfully."}
    except Exception as e:
        raise HTTPException(status_code=500, detail=str(e))

# 5. CLEAR CHAT HISTORY
@router.delete("/history/{customer_id}", status_code=status.HTTP_200_OK)
async def clear_chat_history(customer_id: str):
    try:
        await chat_collection.delete_one({"customer_id": customer_id})
        return {"status": "success", "message": "Chat history cleared successfully."}
    except Exception as e:
        raise HTTPException(status_code=500, detail=str(e))

