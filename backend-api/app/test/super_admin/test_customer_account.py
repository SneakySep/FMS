import sys
from unittest.mock import MagicMock, AsyncMock, patch
sys.modules["weasyprint"] = MagicMock()

import pytest
from fastapi.testclient import TestClient
from app.main import app
from app.middleware.security import get_current_user
from app.service.super_admin.customer_account_service import create_customer_portal_account_service

client = TestClient(app)

def override_super_admin():
    mock_user = MagicMock()
    mock_user.id = "fake-admin-uuid"
    mock_user.email = "superadmin@example.com"
    mock_user.role = "super_admin"
    return mock_user


# ==========================================
# 1. TEST CUSTOMER ACCOUNT SERVICE LAYER
# ==========================================

@patch("app.service.super_admin.customer_account_service.log_audit", new_callable=AsyncMock)
@patch("app.service.super_admin.customer_account_service.send_customer_credentials_email")
@patch("app.service.super_admin.customer_account_service.generate_strong_password")
@patch("app.service.super_admin.customer_account_service.supabase_secondary")
@pytest.mark.asyncio
async def test_create_customer_portal_account_success(
    mock_supabase_sec, mock_gen_pwd, mock_send_email, mock_log_audit
):
    # 1. Mock ticket lookup result (Umiiral at 'for_account' pa ang status)
    mock_ticket_res = MagicMock()
    mock_ticket_res.data = {
        "id": "ticket-uuid-123",
        "email": "customer@company.com",
        "company_name": "Company X",
        "contact_person": "Jane Doe",
        "phone_number": "09123456789",
        "ticket_status": "for_account"
    }

    # 2. Mock Supabase Auth admin create_user response
    mock_auth_user = MagicMock()
    mock_auth_user.id = "auth-user-uuid-999"
    mock_auth_response = MagicMock()
    mock_auth_response.user = mock_auth_user

    # 3. Mock customer_profiles insert result
    mock_profile_res = MagicMock()
    mock_profile_res.data = [{"customer_id": 501, "email": "customer@company.com"}]

    # 4. Mock ticket update result
    mock_update_res = MagicMock()
    mock_update_res.data = [{"id": "ticket-uuid-123", "ticket_status": "created"}]

    # I-configure ang table side effect para sa inquiries, customer_tickets, at customer_profiles
    def table_side_effect(table_name):
        table_mock = MagicMock()
        if table_name == "customer_tickets":
            table_mock.select.return_value.eq.return_value.maybe_single.return_value.execute.return_value = mock_ticket_res
            table_mock.update.return_value.eq.return_value.execute.return_value = mock_update_res
        elif table_name == "customer_profiles":
            table_mock.insert.return_value.execute.return_value = mock_profile_res
        return table_mock

    mock_supabase_sec.table.side_effect = table_side_effect
    mock_supabase_sec.auth.admin.create_user.return_value = mock_auth_response

    mock_gen_pwd.return_value = "SecureTemp123!"
    mock_send_email.return_value = True

    admin_user = {"email": "superadmin@example.com"}
    result = await create_customer_portal_account_service(
        ticket_id="ticket-uuid-123",
        provided_password=None,
        current_admin_user=admin_user,
        client_ip="127.0.0.1"
    )

    assert result is not None
    assert result["success"] is True
    assert result["customer_id"] == 501
    assert result["user_id"] == "auth-user-uuid-999"
    assert result["email_sent"] is True
    mock_log_audit.assert_awaited_once()


@patch("app.service.super_admin.customer_account_service.supabase_secondary")
@pytest.mark.asyncio
async def test_create_customer_account_ticket_not_found(mock_supabase_sec):
    mock_ticket_res = MagicMock()
    mock_ticket_res.data = None  # Walang nahanap na ticket

    mock_supabase_sec.table.return_value.select.return_value.eq.return_value.maybe_single.return_value.execute.return_value = mock_ticket_res

    with pytest.raises(Exception) as exc_info:
        await create_customer_portal_account_service("nonexistent-ticket")

    assert exc_info.value.status_code == 404
    assert "not found" in exc_info.value.detail


@patch("app.service.super_admin.customer_account_service.supabase_secondary")
@pytest.mark.asyncio
async def test_create_customer_account_already_created(mock_supabase_sec):
    mock_ticket_res = MagicMock()
    mock_ticket_res.data = {
        "id": "ticket-uuid-123",
        "email": "customer@company.com",
        "ticket_status": "created"  # Naisumite na o nagawa na ang account dati pa
    }

    mock_supabase_sec.table.return_value.select.return_value.eq.return_value.maybe_single.return_value.execute.return_value = mock_ticket_res

    with pytest.raises(Exception) as exc_info:
        await create_customer_portal_account_service("ticket-uuid-123")

    assert exc_info.value.status_code == 400
    assert "already been created" in exc_info.value.detail


# ==========================================
# 2. TEST CUSTOMER ACCOUNT API ENDPOINT
# ==========================================

@patch("app.middleware.security.supabase_secondary")
@patch("app.routes.super_admin.customer_account_router.create_customer_portal_account_service", new_callable=AsyncMock)
def test_create_customer_account_endpoint(mock_create_service, mock_security_supabase):
    mock_db_res = MagicMock()
    mock_db_res.data = {"role": "super_admin"}
    mock_security_supabase.table.return_value.select.return_value.eq.return_value.maybe_single.return_value.execute.return_value = mock_db_res

    mock_create_service.return_value = {
        "success": True,
        "message": "Customer portal account successfully created for customer@company.com.",
        "customer_id": 101,
        "user_id": "auth-uuid-111",
        "email_sent": True
    }

    app.dependency_overrides[get_current_user] = override_super_admin

    try:
        response = client.post(
            "/api/super-admin/customer-accounts/create",
            json={
                "ticket_id": "ticket-uuid-123",
                "password": "CustomPassword999!"
            }
        )

        assert response.status_code == 201
        data = response.json()
        assert data["success"] is True
        assert data["customer_id"] == 101
        mock_create_service.assert_awaited_once()
    finally:
        app.dependency_overrides.clear()