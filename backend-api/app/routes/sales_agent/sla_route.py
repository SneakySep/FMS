from fastapi import APIRouter, HTTPException, status, Request, Depends
from app.schemas.sales_agent.sla_schema import SLACreateRequest
from app.service.sales_agent.sla_service import SLAService
from app.middleware.security import RoleChecker

router = APIRouter(
    prefix="/api/sales/sla", 
    tags=["Sales SLA Contracts"]
)

@router.post("/", status_code=status.HTTP_201_CREATED)
async def create_sla_contract(
    payload: SLACreateRequest,
    request: Request,
    current_user = Depends(RoleChecker(["sales_agent", "manager", "super_admin"]))
):
    """
    Endpoint para sa paggawa ng SLA Contract at pag-generate ng PDF gamit ang supabase_secondary.
    - Protected ng RoleChecker para sa mga authorized sales roles.
    """
    try:
        service = SLAService()
        
        # Kunin ang employee ID 
        employee_id = current_user.get("id") if isinstance(current_user, dict) else getattr(current_user, "id", None)
        
        result = await service.create_contract_and_pdf(payload, employee_id=employee_id)
        
        return {
            "success": True,
            "message": "SLA Contract created and PDF uploaded successfully!",
            "data": result
        }
    
    except Exception as e:
        raise HTTPException(
            status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
            detail=f"Failed to create SLA contract: {str(e)}"
        )

@router.get("/check/{inquiry_id}", status_code=status.HTTP_200_OK)
async def check_sla_existence(
    inquiry_id: str,
    current_user = Depends(RoleChecker(["sales_agent", "manager", "super_admin"]))
):
    """
    I-check kung mayroon nang created na SLA contract ang isang specific inquiry.
    """
    try:
        service = SLAService()
        response = service.supabase.table("sla_contracts") \
            .select("id, contract_no, status, created_at, pdf_path") \
            .eq("inquiry_id", inquiry_id) \
            .execute()
            
        exists = len(response.data) > 0
        
        return {
            "success": True,
            "exists": exists,
            "data": response.data[0] if exists else None
        }
    except Exception as e:
        raise HTTPException(
            status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
            detail=f"Failed to check SLA existence: {str(e)}"
        )