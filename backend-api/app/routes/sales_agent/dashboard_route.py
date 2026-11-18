from fastapi import APIRouter, HTTPException, status, Depends, Query
from typing import Optional
from app.service.sales_agent.dashboard_service import SalesDashboardService
from app.middleware.security import RoleChecker

dashboard_router = APIRouter(
    prefix="/api/sales/dashboard",
    tags=["Sales Dashboard & KPIs"]
)

@dashboard_router.get("/kpis", status_code=status.HTTP_200_OK)
async def get_sales_dashboard_kpis(
    agent_id: Optional[str] = Query(None, description="I-filter ang KPIs para sa employee/agent ID"),
    current_user = Depends(RoleChecker(["sales_agent", "manager", "super_admin"]))
):
    """
    Endpoint para makuha ang Sales Dashboard KPIs:
    - Total leads ngayong buwan kumpara noong nakaraang buwan 
    - Active leads
    - Qualifying opportunities
    - Closed-Won vs Closed-Lost totals
    - Scikit-Learn Predicted Conversion Health Score
    """
    try:
        service = SalesDashboardService()
        kpis = await service.get_sales_kpis(agent_id=agent_id)

        return {
            "success": True,
            "message": "Sales dashboard KPIs successfully retrieved.",
            "data": kpis
        }
    except Exception as e:
        raise HTTPException(
            status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
            detail=f"Failed to fetch dashboard KPIs: {str(e)}"
        )