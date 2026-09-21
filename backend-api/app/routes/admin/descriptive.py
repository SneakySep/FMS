from fastapi import APIRouter, HTTPException, status
from app.service.admin.descriptive import get_descriptive_analytics
from app.service.admin.diagnostic import get_diagnostic_analytics
from app.service.admin.predictive import get_predictive_analytics
from app.service.admin.prescriptive import get_prescriptive_analytics

router = APIRouter(
    prefix="/api/v1/admin/analytics/bi",
    tags=["Admin Descriptive Analytics"]
)

@router.get("")
async def fetch_descriptive_analytics():
    """
    Endpoint para sa Descriptive Analytics:
    - KPI Snapshot (total inquiries, quotes, won revenue, avg deal value)
    - Monthly Revenue Trend
    - Closed Won vs Closed Lost (Win Rate %)
    - Sales by Service Type (Air, Sea, Land, etc.)
    - Top 5 Customers by Revenue
    """
    try:
        data = get_descriptive_analytics()
        return {
            "status": "success",
            "message": "Descriptive analytics fetched successfully",
            "data": data
        }
    except Exception as e:
        raise HTTPException(
            status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
            detail=f"Failed to fetch descriptive analytics: {str(e)}"
        )

@router.get("/diagnostic")
async def fetch_diagnostic_analytics():
    """
    Endpoint para sa Diagnostic Analytics:
    - Lead Source Effectiveness (Scikit-Learn ML Quality Score)
    - Deal Aging Report (Average Days to Close)
    - Lost Deal Reasons Breakdown (NLP Analysis sa Sales Notes)
    - Win Rate per Service & Route
    - Customer Segment Performance
    """
    try:
        data = get_diagnostic_analytics()
        return {
            "status": "success",
            "message": "Diagnostic analytics fetched successfully",
            "data": data
        }
    except Exception as e:
        raise HTTPException(
            status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
            detail=f"Failed to fetch diagnostic analytics: {str(e)}"
        )

@router.get("/predictive")
async def fetch_predictive_analytics():
    """
    Endpoint para sa Predictive Analytics (Future Trends, Churn, Lead Scoring, at Capacity Forecast)
    """
    try:
        data = get_predictive_analytics()
        return {
            "status": "success",
            "message": "Predictive analytics fetched successfully",
            "data": data
        }
    except Exception as e:
        raise HTTPException(
            status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
            detail=f"Failed to fetch predictive analytics: {str(e)}"
        )

@router.get("/prescriptive")
async def fetch_prescriptive_analytics():
    """
    Endpoint para sa Prescriptive Analytics
    """
    try:
        data = get_prescriptive_analytics()
        return {
            "status": "success",
            "message": "Prescriptive analytics generated successfully",
            "data": data
        }
    except Exception as e:
        raise HTTPException(
            status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
            detail=f"Failed to fetch prescriptive analytics: {str(e)}"
        )