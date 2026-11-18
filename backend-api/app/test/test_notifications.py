import sys
from unittest.mock import MagicMock, AsyncMock, patch

# Mock muna ang weasyprint sakaling kailanganin sa test suite config
sys.modules["weasyprint"] = MagicMock()

import pytest
from fastapi.testclient import TestClient
from app.main import app
from app.middleware.security import get_current_user
from app.service.notification_service import NotificationService

client = TestClient(app)

def override_customer_user():
    mock_user = MagicMock()
    mock_user.id = "cust-uuid-555"
    mock_user.email = "customer@example.com"
    mock_user.role = "customer"
    return mock_user


# ==========================================
# 1. TEST NOTIFICATION SERVICE LAYER
# ==========================================

@patch("app.service.notification_service.supabase_secondary")
@pytest.mark.asyncio
async def test_create_notification_success(mock_supabase_sec):
    mock_insert_res = MagicMock()
    mock_insert_res.data = [
        {
            "id": "notif-uuid-1",
            "target_role": "customer",
            "title": "Welcome",
            "message": "Welcome to Secufreight portal!",
            "type": "welcome",
            "is_read": False
        }
    ]

    mock_supabase_sec.table.return_value.insert.return_value.execute.return_value = mock_insert_res

    service = NotificationService()
    payload = {
        "target_role": "customer",
        "title": "Welcome",
        "message": "Welcome to Secufreight portal!",
        "type": "welcome"
    }

    result = await service.create_notification(payload)

    assert result is not None
    assert result["id"] == "notif-uuid-1"
    assert result["title"] == "Welcome"


@patch("app.service.notification_service.supabase_secondary")
@pytest.mark.asyncio
async def test_get_notifications_for_user_success(mock_supabase_sec):
    mock_query_res = MagicMock()
    mock_query_res.data = [
        {
            "id": "notif-uuid-1",
            "target_role": "customer",
            "recipient_id": "cust-uuid-555",
            "title": "Shipment Update",
            "message": "Your cargo is in transit.",
            "type": "shipment_update",
            "is_read": False
        }
    ]

    # I-chain ang mock query para sa table -> select -> or_ -> order -> limit -> execute
    mock_query = mock_supabase_sec.table.return_value.select.return_value
    mock_query.or_.return_value.order.return_value.limit.return_value.execute.return_value = mock_query_res

    service = NotificationService()
    notifications = await service.get_notifications_for_user(user_role="customer", user_id="cust-uuid-555", limit=10)

    assert len(notifications) == 1
    assert notifications[0]["title"] == "Shipment Update"


@patch("app.service.notification_service.supabase_secondary")
@pytest.mark.asyncio
async def test_mark_as_read_success(mock_supabase_sec):
    mock_update_res = MagicMock()
    mock_update_res.data = [
        {
            "id": "notif-uuid-1",
            "is_read": True
        }
    ]

    mock_supabase_sec.table.return_value.update.return_value.eq.return_value.execute.return_value = mock_update_res

    service = NotificationService()
    updated = await service.mark_as_read("notif-uuid-1")

    assert updated is not None
    assert updated["is_read"] is True


@patch("app.service.notification_service.supabase_secondary")
@pytest.mark.asyncio
async def test_mark_as_read_not_found(mock_supabase_sec):
    mock_update_res = MagicMock()
    mock_update_res.data = None  # Walang nahanap na notification ID

    mock_supabase_sec.table.return_value.update.return_value.eq.return_value.execute.return_value = mock_update_res

    service = NotificationService()

    from fastapi import HTTPException
    with pytest.raises(HTTPException) as exc_info:
        await service.mark_as_read("nonexistent-id")

    assert exc_info.value.status_code == 404
    assert "Notification not found" in exc_info.value.detail


# ==========================================
# 2. TEST NOTIFICATION API ENDPOINTS
# ==========================================

@patch("app.middleware.security.supabase_secondary")
@patch("app.routes.notification_route.NotificationService.get_notifications_for_user", new_callable=AsyncMock)
def test_get_my_notifications_endpoint(mock_get_notifs, mock_security_supabase):
    # Mock RoleChecker database check para maging valid customer user
    mock_db_res = MagicMock()
    mock_db_res.data = {"role": "customer", "email": "customer@example.com"}
    mock_security_supabase.table.return_value.select.return_value.eq.return_value.maybe_single.return_value.execute.return_value = mock_db_res

    mock_get_notifs.return_value = [
        {
            "id": "notif-uuid-1",
            "title": "New Message",
            "message": "You have a new inquiry update.",
            "is_read": False
        }
    ]

    app.dependency_overrides[get_current_user] = override_customer_user

    try:
        response = client.get("/api/notifications/?limit=10")

        assert response.status_code == 200
        data = response.json()
        assert data["success"] is True
        assert data["count"] == 1
        assert data["data"][0]["title"] == "New Message"
        mock_get_notifs.assert_awaited_once()
    finally:
        app.dependency_overrides.clear()


@patch("app.middleware.security.supabase_secondary")
@patch("app.routes.notification_route.NotificationService.mark_as_read", new_callable=AsyncMock)
def test_mark_notification_read_endpoint(mock_mark_read, mock_security_supabase):
    mock_db_res = MagicMock()
    mock_db_res.data = {"role": "customer", "email": "customer@example.com"}
    mock_security_supabase.table.return_value.select.return_value.eq.return_value.maybe_single.return_value.execute.return_value = mock_db_res

    mock_mark_read.return_value = {
        "id": "notif-uuid-1",
        "is_read": True
    }

    app.dependency_overrides[get_current_user] = override_customer_user

    try:
        response = client.patch("/api/notifications/notif-uuid-1/read")

        assert response.status_code == 200
        data = response.json()
        assert data["success"] is True
        assert "Notification marked as read" in data["message"]
        assert data["data"]["is_read"] is True
        mock_mark_read.assert_awaited_once_with(notification_id="notif-uuid-1")
    finally:
        app.dependency_overrides.clear()