from fastapi import APIRouter, HTTPException, status, Depends
from app.middleware.security import RoleChecker
from app.schemas.sales_agent.lead_schema import KanbanStatusUpdateSchema
from app.service.sales_agent.kanban_service import get_kanban_board_data, update_lead_status_service

kanban_router = APIRouter(
    prefix="/api/sales/kanban",
    tags=["Sales Kanban Board"]
)

@kanban_router.get("", status_code=status.HTTP_200_OK)
async def get_kanban_board(
    current_user = Depends(RoleChecker(["sales_agent", "manager", "super_admin"]))
):
    """
    Endpoint para makuha ang mga inquiries na naka-grupo sa 5 columns:
    new_inquiry, qualifying, negotiation, closed_won, closed_lost.
    """
    board_data = await get_kanban_board_data()
    return {
        "success": True,
        "data": board_data
    }

@kanban_router.patch("/{inquiry_id}/status", status_code=status.HTTP_200_OK)
async def update_kanban_lead_status(
    inquiry_id: str,
    payload: KanbanStatusUpdateSchema,
    current_user = Depends(RoleChecker(["sales_agent", "manager", "super_admin"]))
):
    """
    Endpoint para sa paglipat ng status ng lead 
    """
    updated_lead = await update_lead_status_service(inquiry_id, payload.status)
    return {
        "success": True,
        "message": f"Lead status updated to '{payload.status}' successfully.",
        "data": updated_lead
    }