import sys
from unittest.mock import MagicMock, AsyncMock, patch
sys.modules["weasyprint"] = MagicMock()

import pytest
from fastapi.testclient import TestClient
from app.main import app
from app.middleware.security import get_current_user
from app.service.sales_agent.kanban_service import get_kanban_board_data, update_lead_status_service

client = TestClient(app)

def override_sales_agent():
    mock_user = MagicMock()
    mock_user.id = "fake-agent-id"
    mock_user.email = "salesagent@example.com"
    mock_user.role = "sales_agent"
    return mock_user


# ==========================================
# 1. TEST KANBAN SERVICE LAYER
# ==========================================

@patch("app.service.sales_agent.kanban_service.supabase_secondary")
@pytest.mark.asyncio
async def test_get_kanban_board_data_success(mock_supabase_sec):
    mock_res = MagicMock()
    mock_res.data = [
        {"id": "1", "inquiry_code": "INQ-001", "status": "new_inquiry"},
        {"id": "2", "inquiry_code": "INQ-002", "status": "qualifying"},
        {"id": "3", "inquiry_code": "INQ-003", "status": "unknown_status"}  # Mag-f-fall back sa new_inquiry
    ]
    
    mock_supabase_sec.table.return_value.select.return_value.order.return_value.execute.return_value = mock_res

    board = await get_kanban_board_data()

    assert "new_inquiry" in board
    assert "qualifying" in board
    assert len(board["new_inquiry"]) == 2  
    assert len(board["qualifying"]) == 1


@patch("app.service.sales_agent.kanban_service.supabase_secondary")
@pytest.mark.asyncio
async def test_update_lead_status_service_success(mock_supabase_sec):
    # Mock existing lead lookup (gamit ang inquiry code)
    mock_existing_res = MagicMock()
    mock_existing_res.data = {"id": "uuid-1234"}

    # Mock update result
    mock_update_res = MagicMock()
    mock_update_res.data = [{"id": "uuid-1234", "status": "qualifying"}]

    mock_query = mock_supabase_sec.table.return_value.select.return_value
    mock_query.eq.return_value.maybe_single.return_value.execute.return_value = mock_existing_res

    mock_supabase_sec.table.return_value.update.return_value.eq.return_value.execute.return_value = mock_update_res

    updated = await update_lead_status_service("INQ-001", "qualifying")

    assert updated is not None
    assert updated["status"] == "qualifying"


@pytest.mark.asyncio
async def test_update_lead_status_service_invalid_status():
    from fastapi import HTTPException
    with pytest.raises(HTTPException) as exc_info:
        await update_lead_status_service("INQ-001", "invalid_status_xyz")
    
    assert exc_info.value.status_code == 400
    assert "Invalid status" in exc_info.value.detail


# ==========================================
# 2. TEST KANBAN API ENDPOINTS
# ==========================================

@patch("app.middleware.security.supabase_secondary")
@patch("app.routes.sales_agent.kanban.get_kanban_board_data", new_callable=AsyncMock)
def test_get_kanban_board_endpoint(mock_get_board, mock_security_supabase):
    mock_db_res = MagicMock()
    mock_db_res.data = {"role": "sales_agent"}
    mock_security_supabase.table.return_value.select.return_value.eq.return_value.maybe_single.return_value.execute.return_value = mock_db_res

    mock_get_board.return_value = {
        "new_inquiry": [],
        "qualifying": [],
        "negotiation": [],
        "closed_won": [],
        "closed_lost": []
    }

    app.dependency_overrides[get_current_user] = override_sales_agent

    try:
        response = client.get("/api/sales/kanban/")
        assert response.status_code == 200
        data = response.json()
        assert data["success"] is True
        assert "new_inquiry" in data["data"]
    finally:
        app.dependency_overrides.clear()


@patch("app.middleware.security.supabase_secondary")
@patch("app.routes.sales_agent.kanban.update_lead_status_service", new_callable=AsyncMock)
def test_update_kanban_lead_status_endpoint(mock_update_service, mock_security_supabase):
    mock_db_res = MagicMock()
    mock_db_res.data = {"role": "sales_agent"}
    mock_security_supabase.table.return_value.select.return_value.eq.return_value.maybe_single.return_value.execute.return_value = mock_db_res

    mock_update_service.return_value = {
        "id": "uuid-1234",
        "inquiry_code": "INQ-001",
        "status": "closed_won"
    }

    app.dependency_overrides[get_current_user] = override_sales_agent

    try:
        response = client.patch(
            "/api/sales/kanban/INQ-001/status",
            json={"status": "closed_won"}
        )
        assert response.status_code == 200
        data = response.json()
        assert data["success"] is True
        assert data["data"]["status"] == "closed_won"
        mock_update_service.assert_awaited_once_with("INQ-001", "closed_won")
    finally:
        app.dependency_overrides.clear()