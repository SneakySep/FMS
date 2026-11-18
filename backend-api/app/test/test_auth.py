import sys
from unittest.mock import MagicMock, AsyncMock
sys.modules["weasyprint"] = MagicMock()
import pytest
from fastapi.testclient import TestClient
from unittest.mock import patch
from app.main import app 

client = TestClient(app)

# ==========================================
# 1. TEST LOGIN ENDPOINT (Updated para sa bagong flow)
# ==========================================

@patch("app.routes.auth.auth.get_auth_client_secondary")
@patch("app.routes.auth.auth.supabase_secondary")
@patch("app.routes.auth.auth.redis_client")
@patch("app.routes.auth.auth.send_otp_email")
@patch("app.routes.auth.auth.log_audit", new_callable=AsyncMock)
def test_login_success(mock_log_audit, mock_send_email, mock_redis, mock_supabase_sec, mock_get_auth_client):
    # 1. Mock restriction check (Wala pang lockout)
    mock_restriction_res = MagicMock()
    mock_restriction_res.data = None

    # 2. Mock employee table lookup (Para makuha ang role na 'staff')
    mock_emp_res = MagicMock()
    mock_emp_res.data = {
        "employee_id": "EMP-001",
        "email": "testemployee@example.com",
        "role": "staff"
    }

    # I-configure ang mock ng supabase_secondary 
    def supabase_table_side_effect(table_name):
        table_mock = MagicMock()
        if table_name == "login_restrictions":
            table_mock.select.return_value.eq.return_value.maybe_single.return_value.execute.return_value = mock_restriction_res
        elif table_name == "employees":
            table_mock.select.return_value.ilike.return_value.maybe_single.return_value.execute.return_value = mock_emp_res
        return table_mock

    mock_supabase_sec.table.side_effect = supabase_table_side_effect

    # 3. Mock Supabase Secondary Auth sign-in success
    mock_user = MagicMock()
    mock_user.id = "fake-auth-uuid-123"
    mock_auth_response = MagicMock()
    mock_auth_response.user = mock_user
    
    mock_auth_client_instance = MagicMock()
    mock_auth_client_instance.auth.sign_in_with_password.return_value = mock_auth_response
    mock_get_auth_client.return_value = mock_auth_client_instance

    # Mock email sending & redis
    mock_send_email.return_value = True
    mock_redis.setex.return_value = True

    response = client.post("/api/auth/login", json={
        "email": "testemployee@example.com",
        "password": "SecurePassword123!"
    })

    assert response.status_code == 200
    data = response.json()
    assert data["requires_otp"] is True
    assert data["email"] == "testemployee@example.com"
    assert data["role"] == "staff"
    assert "Login successful" in data["message"]


@patch("app.routes.auth.auth.supabase_secondary")
@patch("app.routes.auth.auth.handle_failed_login", new_callable=AsyncMock)
@patch("app.routes.auth.auth.log_audit", new_callable=AsyncMock)
def test_login_employee_not_found(mock_log_audit, mock_handle_failed, mock_supabase_sec):
    # Mock restriction check (Wala pang lockout)
    mock_restriction_res = MagicMock()
    mock_restriction_res.data = None

    # Mock employee table lookup na nagbabalik ng None (Wala sa CRM employees table)
    mock_emp_res = MagicMock()
    mock_emp_res.data = None

    def supabase_table_side_effect(table_name):
        table_mock = MagicMock()
        if table_name == "login_restrictions":
            table_mock.select.return_value.eq.return_value.maybe_single.return_value.execute.return_value = mock_restriction_res
        elif table_name == "employees":
            table_mock.select.return_value.ilike.return_value.maybe_single.return_value.execute.return_value = mock_emp_res
        return table_mock

    mock_supabase_sec.table.side_effect = supabase_table_side_effect

    response = client.post("/api/auth/login", json={
        "email": "notfound@example.com",
        "password": "SomePassword123!"
    })

    assert response.status_code == 401
    assert response.json()["detail"] == "Invalid email or password"
    mock_handle_failed.assert_awaited_once()


@patch("app.routes.auth.auth.get_auth_client_secondary")
@patch("app.routes.auth.auth.supabase_secondary")
@patch("app.routes.auth.auth.handle_failed_login", new_callable=AsyncMock)
@patch("app.routes.auth.auth.log_audit", new_callable=AsyncMock)
def test_login_invalid_credentials(mock_log_audit, mock_handle_failed, mock_supabase_sec, mock_get_auth_client):
    # Mock restriction check
    mock_restriction_res = MagicMock()
    mock_restriction_res.data = None

    # Mock employee table lookup (Valid employee pero mali ang password sa Supabase Auth)
    mock_emp_res = MagicMock()
    mock_emp_res.data = {
        "employee_id": "EMP-001",
        "email": "testemployee@example.com",
        "role": "staff"
    }

    def supabase_table_side_effect(table_name):
        table_mock = MagicMock()
        if table_name == "login_restrictions":
            table_mock.select.return_value.eq.return_value.maybe_single.return_value.execute.return_value = mock_restriction_res
        elif table_name == "employees":
            table_mock.select.return_value.ilike.return_value.maybe_single.return_value.execute.return_value = mock_emp_res
        return table_mock

    mock_supabase_sec.table.side_effect = supabase_table_side_effect

    # Mock Supabase Auth exception (Invalid password)
    mock_auth_client_instance = MagicMock()
    mock_auth_client_instance.auth.sign_in_with_password.side_effect = Exception("Invalid credentials")
    mock_get_auth_client.return_value = mock_auth_client_instance

    response = client.post("/api/auth/login", json={
        "email": "testemployee@example.com",
        "password": "WrongPassword"
    })

    assert response.status_code == 401
    assert response.json()["detail"] == "Invalid email or password"
    mock_handle_failed.assert_awaited_once()

# ==========================================
# 2. TEST VERIFY OTP ENDPOINT (/api/auth/verify-otp)
# ==========================================

@patch("app.routes.auth.auth.jwt.encode")
@patch("app.routes.auth.auth.settings")
@patch("app.routes.auth.auth.supabase_secondary")
@patch("app.routes.auth.auth.redis_client")
@patch("app.routes.auth.auth.log_audit", new_callable=AsyncMock)
def test_verify_otp_success(mock_log_audit, mock_redis, mock_supabase_sec, mock_settings, mock_jwt_encode):
    # Mock Redis stored OTP
    mock_redis.get.return_value = b"123456"

    # Mock JWT secret and encoded token return value
    mock_settings.SUPABASE_JWT_SECRET = "fake-jwt-secret-key"
    mock_jwt_encode.return_value = "mocked.jwt.access.token.string"

    # Mock employee lookup in CRM database
    mock_emp_res = MagicMock()
    mock_emp_res.data = {
        "id": "uuid-db-123",
        "employee_id": "EMP-001",
        "email": "testemployee@example.com",
        "role": "staff"
    }
    mock_supabase_sec.table.return_value.select.return_value.ilike.return_value.maybe_single.return_value.execute.return_value = mock_emp_res

    response = client.post("/api/auth/verify-otp", json={
        "email": "testemployee@example.com",
        "otp": "123456"
    })

    assert response.status_code == 200
    data = response.json()
    assert data["message"] == "OTP verified successfully."
    assert data["access_token"] == "mocked.jwt.access.token.string"
    assert data["token_type"] == "bearer"
    assert data["employee"]["employee_id"] == "EMP-001"
    
    mock_redis.delete.assert_called_once_with("otp:testemployee@example.com")
    mock_jwt_encode.assert_called_once()

# ==========================================
# 3. TEST RBAC PROTECTED ENDPOINT (/api/auth/me)
# ==========================================

@patch("app.routes.auth.auth.supabase_secondary")
def test_get_current_employee_profile_success(mock_supabase_sec):
    # 1. Gumawa tayo ng mock user object
    mock_user = MagicMock()
    mock_user.id = "fake-auth-uuid-123"
    mock_user.email = "testemployee@example.com"

    # 2. I-override ang FastAPI dependency para hindi mag-fail ang authentication
    from app.middleware.security import get_current_user
    app.dependency_overrides[get_current_user] = lambda: mock_user

    try:
        # Mock employee table profile result
        mock_profile_res = MagicMock()
        mock_profile_res.data = {
            "employee_id": "EMP-001",
            "auth_id": "fake-auth-uuid-123",
            "email": "testemployee@example.com"
        }
        mock_supabase_sec.table.return_value.select.return_value.eq.return_value.maybe_single.return_value.execute.return_value = mock_profile_res

        response = client.get("/api/auth/me")

        assert response.status_code == 200
        data = response.json()
        assert data["employee_profile"]["employee_id"] == "EMP-001"
    finally:
        # 3. clear ang dependency overrides pagkatapos ng test 
        app.dependency_overrides.clear()