import sys
from unittest.mock import MagicMock, AsyncMock, patch
sys.modules["weasyprint"] = MagicMock()

import pytest
from fastapi.testclient import TestClient
from datetime import datetime, timezone, timedelta
from app.main import app
from app.middleware.security import get_current_user
from app.service.super_admin.ticket_service import (
    get_all_customer_tickets_service,
    update_ticket_status_service
)

client = TestClient(app)

def override_super_admin():
    mock_user = MagicMock()
    mock_user.id = "fake-super-admin-id"
    mock_user.email = "superadmin@example.com"
    mock_user.role = "super_admin"
    return mock_user


# ==========================================
# 1. TEST TICKET SERVICE LAYER
# ==========================================

@patch("app.service.super_admin.ticket_service.supabase_secondary")
@pytest.mark.asyncio
async def test_get_all_customer_tickets_service(mock_supabase_sec):
    future_date = (datetime.now(timezone.utc) + timedelta(days=2)).isoformat()
    past_date = (datetime.now(timezone.utc) - timedelta(days=1)).isoformat()

    mock_res = MagicMock()
    mock_res.data = [
        {
            "id": "ticket-1",
            "company_name": "Company A",
            "ticket_status": "for_account",
            "pickup_datetime": future_date  # Urgent dahil 2 days na lang (< 3 days)
        },
        {
            "id": "ticket-2",
            "company_name": "Company B",
            "ticket_status": "created",
            "pickup_datetime": past_date   # Hindi na urgent dahil 'created' na ang status
        }
    ]

    mock_supabase_sec.table.return_value.select.return_value.order.return_value.execute.return_value = mock_res

    tickets = await get_all_customer_tickets_service()

    assert len(tickets) == 2
    # Ticket 1 dapat ay urgent dahil 'for_account' at 2 days remaining
    assert tickets[0]["is_urgent"] is True
    assert tickets[0]["days_remaining"] == 1 or tickets[0]["days_remaining"] == 2
    
    # Ticket 2 hindi urgent dahil status ay 'created'
    assert tickets[1]["is_urgent"] is False


@patch("app.service.super_admin.ticket_service.supabase_secondary")
@pytest.mark.asyncio
async def test_update_ticket_status_service_success(mock_supabase_sec):
    mock_update_res = MagicMock()
    mock_update_res.data = [
        {"id": "ticket-123", "ticket_status": "created"}
    ]

    mock_supabase_sec.table.return_value.update.return_value.eq.return_value.execute.return_value = mock_update_res

    updated = await update_ticket_status_service("ticket-123", "created")

    assert updated is not None
    assert updated["ticket_status"] == "created"


@pytest.mark.asyncio
async def test_update_ticket_status_service_invalid_status():
    from fastapi import HTTPException
    with pytest.raises(HTTPException) as exc_info:
        await update_ticket_status_service("ticket-123", "invalid_status")
    
    assert exc_info.value.status_code == 400
    assert "Invalid status" in exc_info.value.detail


@patch("app.service.super_admin.ticket_service.supabase_secondary")
@pytest.mark.asyncio
async def test_update_ticket_status_service_not_found(mock_supabase_sec):
    mock_update_res = MagicMock()
    mock_update_res.data = None  # Walang nahanap na ticket ID

    mock_supabase_sec.table.return_value.update.return_value.eq.return_value.execute.return_value = mock_update_res

    with pytest.raises(Exception) as exc_info:
        await update_ticket_status_service("nonexistent-ticket", "inactive")

    assert exc_info.value.status_code == 404
    assert "not found" in exc_info.value.detail


# ==========================================
# 2. TEST TICKET API ENDPOINTS
# ==========================================

@patch("app.middleware.security.supabase_secondary")
@patch("app.routes.super_admin.ticket_router.get_all_customer_tickets_service", new_callable=AsyncMock)
def test_get_customer_tickets_endpoint(mock_get_tickets, mock_security_supabase):
    mock_db_res = MagicMock()
    mock_db_res.data = {"role": "super_admin"}
    mock_security_supabase.table.return_value.select.return_value.eq.return_value.maybe_single.return_value.execute.return_value = mock_db_res

    mock_get_tickets.return_value = [
        {"id": "ticket-1", "company_name": "Test Company", "ticket_status": "for_account", "is_urgent": True}
    ]

    app.dependency_overrides[get_current_user] = override_super_admin

    try:
        response = client.get("/api/super-admin/tickets/")
        assert response.status_code == 200
        data = response.json()
        assert data["success"] is True
        assert data["count"] == 1
        assert data["data"][0]["company_name"] == "Test Company"
    finally:
        app.dependency_overrides.clear()


@patch("app.middleware.security.supabase_secondary")
@patch("app.routes.super_admin.ticket_router.update_ticket_status_service", new_callable=AsyncMock)
def test_update_ticket_status_endpoint(mock_update_service, mock_security_supabase):
    mock_db_res = MagicMock()
    mock_db_res.data = {"role": "super_admin"}
    mock_security_supabase.table.return_value.select.return_value.eq.return_value.maybe_single.return_value.execute.return_value = mock_db_res

    mock_update_service.return_value = {
        "id": "ticket-1",
        "ticket_status": "inactive"
    }

    app.dependency_overrides[get_current_user] = override_super_admin

    try:
        response = client.patch(
            "/api/super-admin/tickets/ticket-1/status",
            json={"ticket_status": "inactive"}
        )
        assert response.status_code == 200
        data = response.json()
        assert data["success"] is True
        assert "successfully updated" in data["message"]
        assert data["data"]["ticket_status"] == "inactive"
        mock_update_service.assert_awaited_once_with("ticket-1", "inactive")
    finally:
        app.dependency_overrides.clear()