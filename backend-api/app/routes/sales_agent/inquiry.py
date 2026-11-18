from fastapi import APIRouter, HTTPException, status, Request, Depends
from app.schemas.sales_agent.sales_agent import ManualInquiryCreate
from app.middleware.security import RoleChecker
from app.service.sales_agent.inquiry_service import create_manual_inquiry

sales_inquiry_router = APIRouter(
    prefix="/api/sales/inquiries",
    tags=["Sales Agent Inquiries"]
)

@sales_inquiry_router.post("/manual-entry", status_code=status.HTTP_201_CREATED)
async def add_manual_inquiry(
    payload: ManualInquiryCreate,
    request: Request,
    current_user = Depends(RoleChecker(["sales_agent", "manager", "super_admin"]))
):
    """
    Endpoint para sa manual customer/inquiry entry ng mga sales agent.
    - Automatic na magse-set ng platform_used sa 'manual_entry'
    - Automatic na magse-set ng status sa 'new_inquiry'
    - May kasamang auto-generated audit log
    """
    client_ip = request.client.host if request.client else "unknown"
    
    try:
        # I-convert ang pydantic model patungong dictionary
        payload_dict = payload.dict(exclude_unset=True)
        
        new_inquiry = await create_manual_inquiry(payload_dict, current_user, client_ip)
        
        return {
            "success": True,
            "message": "Manual inquiry created successfully.",
            "data": new_inquiry
        }
    except Exception as e:
        raise HTTPException(
            status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
            detail=f"Failed to create manual inquiry: {str(e)}"
        )