import sys
from unittest.mock import MagicMock, AsyncMock, patch
sys.modules["weasyprint"] = MagicMock()

import pytest
from fastapi.testclient import TestClient
from app.main import app
from app.middleware.security import get_current_user
from app.service.sales_agent.lead_service import get_filtered_inquiries, update_inquiry_by_id

client = TestClient(app)

def override_sales_agent():
    mock_user = MagicMock()
    mock_user.id = "fake-agent-id"
    mock_user.email = "salesagent@example.com"
    mock_user.role = "sales_agent"
    return mock_user


# ==========================================
# 1. TEST LEADS SERVICE LAYER
# ==========================================

@patch("app.service.sales_agent.lead_service.supabase_secondary")
@pytest.mark.asyncio
async def test_get_filtered_inquiries_success(mock_supabase_sec):
    mock_res = MagicMock()
    mock_res.data = [
        {"id": "uuid-1", "inquiry_code": "INQ-001", "company_name": "ABC Corp", "status": "new_inquiry"}
    ]

    # I-chain ang mock query para sa table -> select -> filters -> order -> range -> execute
    mock_query = mock_supabase_sec.table.return_value.select.return_value
    mock_query.eq.return_value = mock_query
    mock_query.or_.return_value = mock_query
    mock_query.order.return_value.range.return_value.execute.return_value = mock_res

    leads = await get_filtered_inquiries(
        status_filter="new_inquiry",
        search="ABC",
        service_type="Sea Freight",
        limit=10,
        offset=0
    )

    assert len(leads) == 1
    assert leads[0]["inquiry_code"] == "INQ-001"
    assert leads[0]["company_name"] == "ABC Corp"


@patch("app.service.sales_agent.lead_service.supabase_secondary")
@pytest.mark.asyncio
async def test_update_inquiry_by_id_success(mock_supabase_sec):
    # Mock existing lead lookup result
    mock_existing_res = MagicMock()
    mock_existing_res.data = {"id": "uuid-1234", "company_name": "Old Name"}

    # Mock update result
    mock_update_res = MagicMock()
    mock_update_res.data = [{"id": "uuid-1234", "company_name": "New Name Corp"}]

    mock_query = mock_supabase_sec.table.return_value.select.return_value
    mock_query.eq.return_value.maybe_single.return_value.execute.return_value = mock_existing_res

    mock_supabase_sec.table.return_value.update.return_value.eq.return_value.execute.return_value = mock_update_res

    update_payload = {"company_name": "New Name Corp", "contact_person": None}
    current_user = override_sales_agent()

    updated = await update_inquiry_by_id("INQ-001", update_payload, current_user, "127.0.0.1")

    assert updated is not None
    assert updated["company_name"] == "New Name Corp"


@patch("app.service.sales_agent.lead_service.supabase_secondary")
@pytest.mark.asyncio
async def test_update_inquiry_by_id_not_found(mock_supabase_sec):
    mock_existing_res = MagicMock()
    mock_existing_res.data = None

    mock_query = mock_supabase_sec.table.return_value.select.return_value
    mock_query.eq.return_value.maybe_single.return_value.execute.return_value = mock_existing_res

    current_user = override_sales_agent()

    with pytest.raises(Exception) as exc_info:
        await update_inquiry_by_id("NONEXISTENT", {"company_name": "Test"}, current_user, "127.0.0.1")
    
    # Siguraduhing 404 Not Found ang lumabas
    assert exc_info.value.status_code == 404
    assert "not found" in exc_info.value.detail


# ==========================================
# 2. TEST LEADS API ENDPOINTS
# ==========================================

@patch("app.middleware.security.supabase_secondary")
@patch("app.routes.sales_agent.leads.get_filtered_inquiries", new_callable=AsyncMock)
def test_list_and_filter_leads_endpoint(mock_get_leads, mock_security_supabase):
    mock_db_res = MagicMock()
    mock_db_res.data = {"role": "sales_agent"}
    mock_security_supabase.table.return_value.select.return_value.eq.return_value.maybe_single.return_value.execute.return_value = mock_db_res

    mock_get_leads.return_value = [
        {"id": "uuid-1", "company_name": "Global Logistics"}
    ]

    app.dependency_overrides[get_current_user] = override_sales_agent

    try:
        response = client.get("/api/sales/leads/?status=new_inquiry&limit=10")
        assert response.status_code == 200
        data = response.json()
        assert data["success"] is True
        assert data["count"] == 1
        assert data["data"][0]["company_name"] == "Global Logistics"
    finally:
        app.dependency_overrides.clear()


@patch("app.middleware.security.supabase_secondary")
@patch("app.routes.sales_agent.leads.update_inquiry_by_id", new_callable=AsyncMock)
def test_update_lead_endpoint(mock_update_service, mock_security_supabase):
    mock_db_res = MagicMock()
    mock_db_res.data = {"role": "sales_agent"}
    mock_security_supabase.table.return_value.select.return_value.eq.return_value.maybe_single.return_value.execute.return_value = mock_db_res

    mock_update_service.return_value = {
        "id": "uuid-1234",
        "inquiry_code": "INQ-001",
        "company_name": "Updated Corp"
    }

    app.dependency_overrides[get_current_user] = override_sales_agent

    try:
        response = client.put(
            "/api/sales/leads/INQ-001",
            json={"company_name": "Updated Corp"}
        )
        assert response.status_code == 200
        data = response.json()
        assert data["success"] is True
        assert "updated successfully" in data["message"]
        assert data["data"]["company_name"] == "Updated Corp"
        mock_update_service.assert_awaited_once()
    finally:
        app.dependency_overrides.clear()