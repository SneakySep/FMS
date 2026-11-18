import sys
from unittest.mock import MagicMock, patch
sys.modules["weasyprint"] = MagicMock()

import pytest
from app.service.super_admin.security_service import (
    get_locked_accounts, 
    get_suspicious_activities,
    get_filtered_audit_logs,
    export_audit_logs_csv
)


# ==========================================
# 1. TEST GET LOCKED ACCOUNTS
# ==========================================

@patch("app.service.super_admin.security_service.supabase_secondary")
@pytest.mark.asyncio
async def test_get_locked_accounts_success(mock_supabase_sec):
    mock_res = MagicMock()
    mock_res.data = [
        {
            "email": "lockeduser@example.com",
            "failed_attempts": 5,
            "is_locked": True,
            "updated_at": "2026-11-07T12:00:00Z"
        }
    ]
    
    mock_supabase_sec.table.return_value.select.return_value.gt.return_value.order.return_value.execute.return_value = mock_res

    accounts = await get_locked_accounts()

    assert len(accounts) == 1
    assert accounts[0]["email"] == "lockeduser@example.com"
    assert accounts[0]["failed_attempts"] == 5
    assert accounts[0]["is_locked"] is True


@patch("app.service.super_admin.security_service.supabase_secondary")
@pytest.mark.asyncio
async def test_get_locked_accounts_exception(mock_supabase_sec):
    mock_supabase_sec.table.side_effect = Exception("Database connection error")
    accounts = await get_locked_accounts()
    assert accounts == []


# ==========================================
# 2. TEST GET SUSPICIOUS ACTIVITIES
# ==========================================

@patch("app.service.super_admin.security_service.supabase_secondary")
@pytest.mark.asyncio
async def test_get_suspicious_activities_success(mock_supabase_sec):
    mock_res = MagicMock()
    mock_res.data = [
        {
            "id": 101,
            "action": "LOGIN_FAILED",
            "email": "attacker@example.com",
            "created_at": "2026-11-07T12:30:00Z"
        }
    ]

    mock_supabase_sec.table.return_value.select.return_value.in_.return_value.order.return_value.limit.return_value.execute.return_value = mock_res

    activities = await get_suspicious_activities(limit=10)

    assert len(activities) == 1
    assert activities[0]["action"] == "LOGIN_FAILED"
    assert activities[0]["email"] == "attacker@example.com"
    mock_supabase_sec.table.return_value.select.return_value.in_.return_value.order.return_value.limit.assert_called_once_with(10)


@patch("app.service.super_admin.security_service.supabase_secondary")
@pytest.mark.asyncio
async def test_get_suspicious_activities_exception(mock_supabase_sec):
    mock_supabase_sec.table.side_effect = Exception("Timeout error")
    activities = await get_suspicious_activities(limit=20)
    assert activities == []


# ==========================================
# 3. TEST GET FILTERED AUDIT LOGS
# ==========================================

@patch("app.service.super_admin.security_service.supabase_secondary")
@pytest.mark.asyncio
async def test_get_filtered_audit_logs_success(mock_supabase_sec):
    mock_res = MagicMock()
    mock_res.data = [
        {
            "id": 1,
            "email": "user@example.com",
            "action": "LOGIN_SUCCESS",
            "created_at": "2026-11-07T10:00:00Z"
        }
    ]

    # Isama ang .ilike at iba pang filter methods sa mock query chain
    mock_query = mock_supabase_sec.table.return_value.select.return_value
    mock_query.ilike.return_value = mock_query
    mock_query.eq.return_value = mock_query
    mock_query.gte.return_value = mock_query
    mock_query.lte.return_value = mock_query
    mock_query.order.return_value.limit.return_value.execute.return_value = mock_res

    logs = await get_filtered_audit_logs(
        user_email="user@example.com",
        action="LOGIN_SUCCESS",
        start_date="2026-11-01T00:00:00Z",
        end_date="2026-11-07T23:59:59Z",
        limit=50
    )

    assert len(logs) == 1
    assert logs[0]["email"] == "user@example.com"
    assert logs[0]["action"] == "LOGIN_SUCCESS"


@patch("app.service.super_admin.security_service.supabase_secondary")
@pytest.mark.asyncio
async def test_get_filtered_audit_logs_exception(mock_supabase_sec):
    mock_supabase_sec.table.side_effect = Exception("Filter query failed")
    logs = await get_filtered_audit_logs(limit=10)
    assert logs == []


# ==========================================
# 4. TEST EXPORT AUDIT LOGS CSV
# ==========================================

@patch("app.service.super_admin.security_service.supabase_secondary")
@pytest.mark.asyncio
async def test_export_audit_logs_csv_success(mock_supabase_sec):
    mock_res = MagicMock()
    # Palitan ang "email" ng "user_email" (o kung ano ang key sa iyong CSV builder service)
    mock_res.data = [
        {
            "id": 1,
            "user_email": "test@example.com",  # <--- Dito nagkaiba kaya naging blank sa CSV output
            "action": "LOGIN_SUCCESS",
            "created_at": "2026-11-07T12:00:00Z"
        }
    ]

    mock_supabase_sec.table.return_value.select.return_value.order.return_value.limit.return_value.execute.return_value = mock_res

    csv_output = await export_audit_logs_csv()

    assert csv_output is not None
    assert "ID,User Email,Action" in csv_output
    assert "test@example.com" in csv_output
    assert "LOGIN_SUCCESS" in csv_output