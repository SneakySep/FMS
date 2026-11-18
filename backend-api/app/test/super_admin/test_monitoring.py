import sys
from unittest.mock import MagicMock, AsyncMock
sys.modules["weasyprint"] = MagicMock()

import pytest
from fastapi.testclient import TestClient
from unittest.mock import patch
from app.main import app
from app.middleware.security import get_current_user

client = TestClient(app)

# ==========================================
# 1. TEST DATABASE HEALTH ENDPOINT
# ==========================================

@patch("app.middleware.security.supabase_secondary")  
@patch("app.routes.super_admin.monitoring.check_database_health", new_callable=AsyncMock)
def test_get_db_health_success(mock_check_health, mock_security_supabase):
    # Mock database health service response
    mock_check_health.return_value = {
        "status": "healthy",
        "latency_ms": 15.4
    }

    # Mock database query ng RoleChecker para magbalik ng super_admin role
    mock_db_res = MagicMock()
    mock_db_res.data = {"role": "super_admin", "email": "superadmin@example.com"}
    mock_security_supabase.table.return_value.select.return_value.eq.return_value.maybe_single.return_value.execute.return_value = mock_db_res

    mock_user = MagicMock()
    mock_user.id = "fake-auth-id"
    mock_user.email = "superadmin@example.com"

    app.dependency_overrides[get_current_user] = lambda: mock_user

    try:
        response = client.get("/api/admin/monitoring/database-health")

        assert response.status_code == 200
        data = response.json()
        assert data["success"] is True
        assert data["data"]["status"] == "healthy"
        assert data["data"]["latency_ms"] == 15.4
        mock_check_health.assert_awaited_once()
    finally:
        app.dependency_overrides.clear()


# ==========================================
# 2. TEST ALERT LOGS ENDPOINT
# ==========================================

@patch("app.middleware.security.supabase_secondary")  
@patch("app.routes.super_admin.monitoring.get_recent_audit_logs", new_callable=AsyncMock)
def test_get_alert_logs_success(mock_get_logs, mock_security_supabase):
    # Mock audit logs list response
    mock_get_logs.return_value = [
        {
            "id": 1,
            "action": "LOGIN_SUCCESS",
            "email": "test@example.com",
            "timestamp": "2026-11-07T12:00:00Z"
        }
    ]

    mock_db_res = MagicMock()
    mock_db_res.data = {"role": "super_admin", "email": "superadmin@example.com"}
    mock_security_supabase.table.return_value.select.return_value.eq.return_value.maybe_single.return_value.execute.return_value = mock_db_res

    mock_user = MagicMock()
    mock_user.id = "fake-auth-id"
    mock_user.email = "superadmin@example.com"

    app.dependency_overrides[get_current_user] = lambda: mock_user

    try:
        response = client.get("/api/admin/monitoring/alert-logs?limit=10")

        assert response.status_code == 200
        data = response.json()
        assert data["success"] is True
        assert data["count"] == 1
        assert len(data["logs"]) == 1
        assert data["logs"][0]["action"] == "LOGIN_SUCCESS"
        mock_get_logs.assert_awaited_once_with(10)
    finally:
        app.dependency_overrides.clear()