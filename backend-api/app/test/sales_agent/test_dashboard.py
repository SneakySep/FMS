import sys
from unittest.mock import MagicMock, AsyncMock, patch

# Mock muna ang weasyprint sakaling kailanganin sa test suite config
sys.modules["weasyprint"] = MagicMock()

import pytest
from fastapi.testclient import TestClient
from datetime import datetime, timezone, timedelta
from app.main import app
from app.middleware.security import get_current_user
from app.service.sales_agent.dashboard_service import SalesDashboardService

client = TestClient(app)

def override_sales_agent():
    mock_user = MagicMock()
    mock_user.id = "fake-employee-uuid-888"
    mock_user.email = "salesagent@example.com"
    mock_user.role = "sales_agent"
    return mock_user


# ==========================================
# 1. TEST DASHBOARD SERVICE LAYER
# ==========================================

@patch("app.service.sales_agent.dashboard_service.supabase_secondary")
@pytest.mark.asyncio
async def test_get_sales_kpis_success_with_ml(mock_supabase_sec):
    now_iso = datetime.now(timezone.utc).isoformat()
    
    # Mock inquiries data na may sapat na entries (>= 5) para ma-trigger ang Scikit-Learn LogisticRegression model
    mock_inquiries_res = MagicMock()
    mock_inquiries_res.data = [
        {"id": "1", "status": "new_inquiry", "created_at": now_iso, "email": "a@test.com", "phone_number": "123", "estimated_amount": 10000.0},
        {"id": "2", "status": "qualifying", "created_at": now_iso, "email": "b@test.com", "phone_number": "456", "estimated_amount": 20000.0},
        {"id": "3", "status": "closed_won", "created_at": now_iso, "email": "c@test.com", "phone_number": "789", "estimated_amount": 50000.0},
        {"id": "4", "status": "closed_lost", "created_at": now_iso, "email": None, "phone_number": None, "estimated_amount": 0.0},
        {"id": "5", "status": "closed_won", "created_at": now_iso, "email": "d@test.com", "phone_number": "111", "estimated_amount": 30000.0},
    ]

    mock_supabase_sec.table.return_value.select.return_value.execute.return_value = mock_inquiries_res

    service = SalesDashboardService()
    kpis = await service.get_sales_kpis(agent_id=None)

    assert kpis is not None
    assert kpis["total_leads_current_month"] == 5
    assert kpis["active_leads"] == 2  
    assert kpis["qualifying_opportunities"] == 1
    assert kpis["total_closed_won"] == 2
    assert kpis["total_closed_lost"] == 1
    assert "predicted_conversion_health_score" in kpis


@patch("app.service.sales_agent.dashboard_service.supabase_secondary")
@pytest.mark.asyncio
async def test_get_sales_kpis_empty_data(mock_supabase_sec):
    # Mock kapag walang nahanap na inquiries sa database
    mock_inquiries_res = MagicMock()
    mock_inquiries_res.data = []

    mock_supabase_sec.table.return_value.select.return_value.execute.return_value = mock_inquiries_res

    service = SalesDashboardService()
    kpis = await service.get_sales_kpis()

    assert kpis["total_leads_current_month"] == 0
    assert kpis["active_leads"] == 0
    assert kpis["conversion_health_score"] == 0.0 or kpis.get("predicted_conversion_health_score", 0.0) == 0.0


# ==========================================
# 2. TEST DASHBOARD API ENDPOINT
# ==========================================

@patch("app.middleware.security.supabase_secondary")
@patch("app.service.sales_agent.dashboard_service.SalesDashboardService.get_sales_kpis", new_callable=AsyncMock)
def test_get_sales_dashboard_kpis_endpoint(mock_get_kpis, mock_security_supabase):
    # Mock RoleChecker database check para maging valid sales_agent
    mock_db_res = MagicMock()
    mock_db_res.data = {"role": "sales_agent", "email": "salesagent@example.com"}
    mock_security_supabase.table.return_value.select.return_value.eq.return_value.maybe_single.return_value.execute.return_value = mock_db_res

    # Mock service return KPIs
    mock_get_kpis.return_value = {
        "total_leads_current_month": 12,
        "total_leads_last_month": 10,
        "lead_growth_percentage": 20.0,
        "active_leads": 8,
        "qualifying_opportunities": 3,
        "total_closed_won": 3,
        "total_closed_lost": 1,
        "predicted_conversion_health_score": 75.5
    }

    app.dependency_overrides[get_current_user] = override_sales_agent

    try:
        response = client.get("/api/sales/dashboard/kpis")

        assert response.status_code == 200
        data = response.json()
        assert data["success"] is True
        assert "Sales dashboard KPIs successfully retrieved" in data["message"]
        assert data["data"]["total_leads_current_month"] == 12
        assert data["data"]["predicted_conversion_health_score"] == 75.5
        mock_get_kpis.assert_awaited_once_with(agent_id=None)
    finally:
        app.dependency_overrides.clear()