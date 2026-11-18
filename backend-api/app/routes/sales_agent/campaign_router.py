from fastapi import APIRouter, HTTPException, status, Depends
from app.schemas.sales_agent.campaign_schema import CampaignCreateSchema
from app.service.sales_agent.campaign_service import CampaignService
from app.middleware.security import RoleChecker

campaign_router = APIRouter(
    prefix="/api/sales/campaigns",
    tags=["Sales Agent Campaigns"]
)

@campaign_router.post("/create", status_code=status.HTTP_201_CREATED)
async def create_campaign(
    payload: CampaignCreateSchema,
    current_user = Depends(RoleChecker(["sales_agent", "manager", "super_admin"]))
):
    """
    Endpoint para sa Sales Agent para mag-post ng campaign:
    - Sine-save sa database (may pagpipilian kung permanent o may expiration).
    - Awtomatikong nagpapadala ng email broadcast sa lahat ng customer.
    - Protektado ng RoleChecker.
    """
    try:
        service = CampaignService()
        # Kunin ang ID ng kasalukuyang nag-log in na employee mula sa current_user object
        agent_id = getattr(current_user, "id", None) or current_user.get("id")

        result = await service.create_and_broadcast_campaign(
            agent_id=agent_id,
            data=payload
        )

        return {
            "success": True,
            "message": f"Campaign successfully published and broadcasted to {result['emails_sent']} customers.",
            "data": result["campaign"]
        }

    except Exception as e:
        raise HTTPException(
            status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
            detail=f"Failed to create campaign: {str(e)}"
        )