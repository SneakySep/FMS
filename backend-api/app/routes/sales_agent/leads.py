from fastapi import APIRouter, HTTPException, status, Request, Depends, Query
from typing import Optional
from app.schemas.sales_agent.lead_schema import InquiryUpdateSchema
from app.middleware.security import RoleChecker
from app.service.sales_agent.lead_service import get_filtered_inquiries, update_inquiry_by_id

leads_router = APIRouter(
    prefix="/api/sales/leads",
    tags=["Leads & Inquiries Management"]
)

@leads_router.get("/", status_code=status.HTTP_200_OK)
async def list_and_filter_leads(
    status: Optional[str] = Query(None, description="Filter by inquiry status (e.g., new_inquiry)"),
    search: Optional[str] = Query(None, description="Search by company name, contact person, or inquiry code"),
    service_type: Optional[str] = Query(None, description="Filter by service type"),
    limit: int = Query(50, ge=1, le=100),
    offset: int = Query(0, ge=0),
    current_user = Depends(RoleChecker(["sales_agent", "manager", "super_admin"]))
):
    """
    Endpoint para makuha ang listahan ng inquiries na may kasamang filtering at search.
    """
    leads = await get_filtered_inquiries(
        status_filter=status,
        search=search,
        service_type=service_type,
        limit=limit,
        offset=offset
    )
    
    return {
        "success": True,
        "count": len(leads),
        "data": leads
    }

@leads_router.put("/{inquiry_id}", status_code=status.HTTP_200_OK)
async def update_lead(
    inquiry_id: str,
    payload: InquiryUpdateSchema,
    request: Request,
    current_user = Depends(RoleChecker(["sales_agent", "manager", "super_admin"]))
):
    """
    Endpoint para ma-edit o ma-update ng sales agent ang mga detalye ng inquiry.
    """
    client_ip = request.client.host if request.client else "unknown"
    payload_dict = payload.dict()
    
    updated_lead = await update_inquiry_by_id(inquiry_id, payload_dict, current_user, client_ip)
    
    return {
        "success": True,
        "message": f"Inquiry {inquiry_id} updated successfully.",
        "data": updated_lead
    }