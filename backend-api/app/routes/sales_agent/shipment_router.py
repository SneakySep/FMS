from fastapi import APIRouter, HTTPException, status, Request, Depends
from app.schemas.sales_agent.shipment_schema import ShipmentCreateSchema
from app.middleware.security import RoleChecker
from app.service.sales_agent.shipment_service import create_shipment_record

shipment_router = APIRouter(
    prefix="/api/sales/shipments",
    tags=["Sales Shipments & Waybill"]
)

@shipment_router.post("/", status_code=status.HTTP_201_CREATED)
async def create_shipment(
    payload: ShipmentCreateSchema,
    request: Request,
    current_user = Depends(RoleChecker(["sales_agent", "manager", "super_admin"]))
):
    """
    Endpoint para i-convert ang closed_won inquiry patungong official Shipment/Waybill record.
    Gumagamit ng 'consignment_note_number' bilang tracking number.
    """
    client_ip = request.client.host if request.client else "unknown"
    payload_dict = payload.dict()
    
    new_shipment = await create_shipment_record(payload_dict, current_user, client_ip)
    
    return {
        "success": True,
        "message": "Shipment created successfully and inquiry marked as closed_won.",
        "data": new_shipment
    }