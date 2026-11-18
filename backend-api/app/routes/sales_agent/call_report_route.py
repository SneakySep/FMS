from fastapi import APIRouter, HTTPException, status, Depends, Query
from typing import Optional
from app.schemas.sales_agent.call_report_schema import CallReportCreateSchema
from app.service.sales_agent.call_report_service import CallReportService
from app.middleware.security import RoleChecker

call_report_router = APIRouter(
    prefix="/api/sales/call-reports",
    tags=["Sales Agent Call Reports"]
)

@call_report_router.post("/create", status_code=status.HTTP_201_CREATED)
async def create_call_report(
    payload: CallReportCreateSchema,
    current_user = Depends(RoleChecker(["sales_agent", "manager", "super_admin"]))
):
    """
    Endpoint para i-save ang call report. Awtomatikong hihilahin ng Scikit-Learn model 
    ang status at rating mula sa remarks na tinype mo.
    """
    try:
        service = CallReportService()
        agent_id = getattr(current_user, "id", None) or current_user.get("id")

        result = await service.create_call_report(agent_id=agent_id, data=payload)

        return {
            "success": True,
            "message": "Call report successfully logged and classified by Machine Learning model.",
            "data": result
        }
    except Exception as e:
        raise HTTPException(
            status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
            detail=f"Failed to create call report: {str(e)}"
        )

@call_report_router.get("/printable", status_code=status.HTTP_200_OK)
async def get_printable_reports(
    report_date: Optional[str] = Query(None, description="Filter sa petsa (YYYY-MM-DD) para sa pag-print"),
    current_user = Depends(RoleChecker(["sales_agent", "manager", "super_admin"]))
):
    """
    Endpoint para makuha ang listahan ng mga ulat para sa pag-print o PDF export.
    """
    try:
        service = CallReportService()
        reports = await service.get_printable_reports(report_date=report_date)

        return {
            "success": True,
            "count": len(reports),
            "data": reports
        }
    except Exception as e:
        raise HTTPException(
            status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
            detail=f"Failed to fetch printable reports: {str(e)}"
        )

@call_report_router.get("/", status_code=status.HTTP_200_OK)
async def get_all_call_reports(
    report_date: Optional[str] = Query(None, description="I-filter base sa petsa (YYYY-MM-DD)"),
    agent_id: Optional[str] = Query(None, description="I-filter base sa ID ng ahente"),
    current_user = Depends(RoleChecker(["sales_agent", "manager", "super_admin"]))
):
    """
    Endpoint para makuha ang lahat ng Daily Call Reports. 
    May opsyong mag-filter ayon sa petsa o partikular na sales agent.
    """
    try:
        service = CallReportService()
        
        # Kunin ang data mula sa Supabase gamit ang service
        query = service.supabase.table("daily_call_reports").select("*")
        
        if report_date:
            query = query.eq("report_date", report_date)
        if agent_id:
            query = query.eq("agent_id", agent_id)
            
        response = query.order("created_at", desc=True).execute()
        reports = response.data if response.data else []

        return {
            "success": True,
            "count": len(reports),
            "data": reports
        }
    except Exception as e:
        raise HTTPException(
            status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
            detail=f"Failed to fetch call reports: {str(e)}"
        )