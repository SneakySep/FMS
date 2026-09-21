from fastapi import APIRouter, HTTPException, status
from app.service.admin.analytics import get_admin_dashboard_kpis
from app.service.admin.kanban import get_kanban_board_data, update_card_status
from app.schemas.kanban import MoveCardRequest
from app.supabase_config.supabase import supabase_secondary

router = APIRouter(
    prefix="/api/v1/admin/analytics", 
    tags=["Admin Analytics"]
)

@router.get("/cards")
async def fetch_admin_cards():
    inquiries_res = supabase_secondary.table("inquiries").select("*").execute()
    raw_inquiries = inquiries_res.data if inquiries_res.data else []

    return get_admin_dashboard_kpis(raw_inquiries)

@router.get("")
async def fetch_kanban_board():
    try:
        data = get_kanban_board_data()
        return {"status": "success", "data": data}
    except Exception as e:
        raise HTTPException(
            status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
            detail=f"Failed to fetch kanban board: {str(e)}"
        )

@router.patch("/move")
async def move_kanban_card(payload: MoveCardRequest):
    updated = update_card_status(payload.card_id, payload.new_status)
    if not updated:
        raise HTTPException(
            status_code=status.HTTP_400_BAD_REQUEST,
            detail="Failed to update inquiry status"
        )
    return {"status": "success", "message": "Inquiry status updated successfully"}