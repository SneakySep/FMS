import sys
from unittest.mock import MagicMock, AsyncMock, patch
sys.modules["weasyprint"] = MagicMock()

import pytest
from fastapi.testclient import TestClient
from app.main import app
from app.middleware.security import get_current_user
from app.service.sales_agent.inquiry_service import create_manual_inquiry

client = TestClient(app)

# Helper para sa authorized user (RoleChecker mock)
def override_sales_agent():
    mock_user = MagicMock()
    mock_user.id = "fake-agent-id"
    mock_user.email = "salesagent@example.com"
    mock_user.role = "sales_agent"
    return mock_user


# ==========================================
# 1. TEST SERVICE LAYER (create_manual_inquiry)
# ==========================================

@patch("app.service.sales_agent.inquiry_service.log_audit", new_callable=AsyncMock)
@patch("app.service.sales_agent.inquiry_service.supabase_secondary")
@pytest.mark.asyncio
async def test_create_manual_inquiry_service_success(mock_supabase_sec, mock_log_audit):
    # Mock employee lookup result
    mock_emp_res = MagicMock()
    mock_emp_res.data = {
        "employee_id": "EMP-999",
        "first_name": "Juan",
        "last_name": "Dela Cruz",
        "email": "salesagent@example.com"
    }

    # Mock inquiry insert result
    mock_insert_res = MagicMock()
    mock_insert_res.data = [
        {
            "id": 1,
            "inquiry_code": "INQ-2026-001",
            "company_name": "Logistics Corp",
            "status": "new_inquiry"
        }
    ]

    # I-configure ang table mock side_effect 
    def table_side_effect(table_name):
        table_mock = MagicMock()
        if table_name == 'employees':
            table_mock.select.return_value.eq.return_value.maybe_single.return_value.execute.return_value = mock_emp_res
        elif table_name == 'inquiries':
            table_mock.insert.return_value.execute.return_value = mock_insert_res
        return table_mock

    mock_supabase_sec.table.side_effect = table_side_effect

    current_user = override_sales_agent()
    payload_dict = {
        "company_name": "Logistics Corp",
        "contact_person": "Maria Santos",
        "email": "maria@logistics.com",
        "estimated_amount": 15000.00
    }

    result = await create_manual_inquiry(payload_dict, current_user, "127.0.0.1")

    assert result is not None
    assert result["inquiry_code"] == "INQ-2026-001"
    assert result["company_name"] == "Logistics Corp"
    
    #  audit log
    mock_log_audit.assert_awaited_once()


# ==========================================
# 2. TEST API ENDPOINT (/api/sales/inquiries/manual-entry)
# ==========================================

@patch("app.middleware.security.supabase_secondary")  # Para sa RoleChecker database check
@patch("app.routes.sales_agent.inquiry.create_manual_inquiry", new_callable=AsyncMock)
def test_add_manual_inquiry_endpoint_success(mock_create_service, mock_security_supabase):
    # Mock database check ng RoleChecker para maging valid sales_agent
    mock_db_res = MagicMock()
    mock_db_res.data = {"role": "sales_agent", "email": "salesagent@example.com"}
    mock_security_supabase.table.return_value.select.return_value.eq.return_value.maybe_single.return_value.execute.return_value = mock_db_res

    # Mock service return value
    mock_create_service.return_value = {
        "id": 10,
        "inquiry_code": "INQ-2026-010",
        "company_name": "Test Company",
        "status": "new_inquiry",
        "platform_used": "manual_entry",
        "assigned_agent_id": "EMP-999"
    }

    # I-override ang get_current_user para mag-return ng sales agent
    app.dependency_overrides[get_current_user] = override_sales_agent

    try:
        response = client.post(
            "/api/sales/inquiries/manual-entry",
            json={
                "company_name": "Test Company",
                "contact_person": "John Doe",
                "email": "john@test.com",
                "phone_number": "09123456789",
                "service_type": "Sea Freight",
                "origin": "Manila",
                "destination": "Cebu",
                "estimated_amount": 25000.0
            }
        )

        assert response.status_code == 201
        data = response.json()
        assert data["success"] is True
        assert data["message"] == "Manual inquiry created successfully."
        assert data["data"]["inquiry_code"] == "INQ-2026-010"
        mock_create_service.assert_awaited_once()
    finally:
        app.dependency_overrides.clear()