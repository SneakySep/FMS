import sys
from unittest.mock import MagicMock, AsyncMock, patch

# Mock muna ang weasyprint sakaling kailanganin sa test suite config
sys.modules["weasyprint"] = MagicMock()

import pytest
from fastapi.testclient import TestClient
from datetime import datetime, timezone
from app.main import app
from app.middleware.security import get_current_user
from app.service.sales_agent.campaign_service import CampaignService

client = TestClient(app)

def override_sales_agent():
    mock_user = MagicMock()
    mock_user.id = "fake-employee-uuid-777"
    mock_user.email = "salesagent@example.com"
    mock_user.role = "sales_agent"
    return mock_user


# ==========================================
# 1. TEST CAMPAIGN SERVICE LAYER
# ==========================================

@patch("app.service.sales_agent.campaign_service.smtplib.SMTP_SSL")
@patch("app.service.sales_agent.campaign_service.supabase_secondary")
@pytest.mark.asyncio
async def test_create_and_broadcast_campaign_success(mock_supabase_sec, mock_smtp_ssl):
    # 1. Mock campaign insert response
    mock_campaign_res = MagicMock()
    mock_campaign_res.data = [
        {
            "id": "camp-uuid-1",
            "title": "Holiday Promo 2026",
            "description": "Enjoy 20% discount on all air freight services this December!",
            "status": "published",
            "created_by": "fake-employee-uuid-777",
            "is_permanent": True
        }
    ]

    # 2. Mock customer profiles list para sa email broadcast
    mock_customers_res = MagicMock()
    mock_customers_res.data = [
        {"email": "customer1@company.com"},
        {"email": "customer2@company.com"}
    ]

    # Konpigurasyon ng table side effects para sa campaigns at customer_profiles
    def table_side_effect(table_name):
        table_mock = MagicMock()
        if table_name == "campaigns":
            table_mock.insert.return_value.execute.return_value = mock_campaign_res
        elif table_name == "customer_profiles":
            table_mock.select.return_value.execute.return_value = mock_customers_res
        return table_mock

    mock_supabase_sec.table.side_effect = table_side_effect

    # Mock SMTP server login at sendmail
    mock_smtp_instance = MagicMock()
    mock_smtp_ssl.return_value.__enter__.return_value = mock_smtp_instance
    mock_smtp_instance.sendmail.return_value = {}

    service = CampaignService()

    from app.schemas.sales_agent.campaign_schema import CampaignCreateSchema
    payload = CampaignCreateSchema(
        title="Holiday Promo 2026",
        description="Enjoy 20% discount on all air freight services this December!",
        image_url="https://example.com/poster.jpg",
        target_audience="all_customers",
        is_permanent=True
    )

    result = await service.create_and_broadcast_campaign(
        agent_id="fake-employee-uuid-777",
        data=payload
    )

    assert result is not None
    assert result["campaign"]["title"] == "Holiday Promo 2026"
    assert result["emails_sent"] == 2
    # Siguraduhing na-trigger ang email sending sa SMTP para sa dalawang customer
    assert mock_smtp_instance.sendmail.call_count == 2


# ==========================================
# 2. TEST CAMPAIGN API ENDPOINT
# ==========================================

@patch("app.middleware.security.supabase_secondary")
@patch("app.service.sales_agent.campaign_service.CampaignService.create_and_broadcast_campaign", new_callable=AsyncMock)
def test_create_campaign_endpoint(mock_broadcast_service, mock_security_supabase):
    # Mock RoleChecker database check para maging valid sales_agent
    mock_db_res = MagicMock()
    mock_db_res.data = {"role": "sales_agent", "email": "salesagent@example.com"}
    mock_security_supabase.table.return_value.select.return_value.eq.return_value.maybe_single.return_value.execute.return_value = mock_db_res

    # Mock service return result
    mock_broadcast_service.return_value = {
        "campaign": {
            "id": "camp-uuid-1",
            "title": "New Year Advisory",
            "status": "published"
        },
        "emails_sent": 5
    }

    app.dependency_overrides[get_current_user] = override_sales_agent

    try:
        response = client.post(
            "/api/sales/campaigns/create",
            json={
                "title": "New Year Advisory",
                "description": "Important schedule adjustments for the upcoming holiday season.",
                "image_url": None,
                "target_audience": "all_customers",
                "is_permanent": True
            }
        )

        assert response.status_code == 201
        data = response.json()
        assert data["success"] is True
        assert "Campaign successfully published" in data["message"]
        assert data["data"]["title"] == "New Year Advisory"
        mock_broadcast_service.assert_awaited_once()
    finally:
        app.dependency_overrides.clear()