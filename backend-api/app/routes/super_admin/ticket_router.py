from fastapi import APIRouter, HTTPException, status, Depends
from app.schemas.super_admin.ticket_schema import CustomerTicketUpdateStatusSchema
from app.service.super_admin.ticket_service import (
    get_all_customer_tickets_service,
    update_ticket_status_service
)
from app.middleware.security import RoleChecker

super_admin_ticket_router = APIRouter(
    prefix="/api/super-admin/tickets",
    tags=["Super Admin Customer Tickets"]
)

@super_admin_ticket_router.get("/", status_code=status.HTTP_200_OK)
async def get_customer_tickets(
    current_user = Depends(RoleChecker(["super_admin"]))
):
    """
    Kunin ang lahat ng customer account tickets kasama ang dynamic countdown batay sa pickup_datetime.
    Protektado para sa super_admin lamang.
    """
    tickets = await get_all_customer_tickets_service()
    return {
        "success": True,
        "count": len(tickets),
        "data": tickets
    }

@super_admin_ticket_router.patch("/{ticket_id}/status", status_code=status.HTTP_200_OK)
async def update_ticket_status(
    ticket_id: str,
    payload: CustomerTicketUpdateStatusSchema,
    current_user = Depends(RoleChecker(["super_admin"]))
):
    """
    I-update ang status ng ticket 
    """
    updated_ticket = await update_ticket_status_service(ticket_id, payload.ticket_status)
    return {
        "success": True,
        "message": f"Ticket status successfully updated to '{payload.ticket_status}'.",
        "data": updated_ticket
    }