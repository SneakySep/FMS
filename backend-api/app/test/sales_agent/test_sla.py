import sys
import unittest
from unittest.mock import MagicMock, AsyncMock, patch
sys.modules["weasyprint"] = MagicMock()

import pytest
from fastapi.testclient import TestClient
from datetime import date
from decimal import Decimal
from app.main import app
from app.middleware.security import get_current_user
from app.service.sales_agent.sla_service import SLAService

client = TestClient(app)

def override_sales_agent():
    mock_user = MagicMock()
    mock_user.id = "fake-employee-uuid-123"
    mock_user.email = "salesagent@example.com"
    mock_user.role = "sales_agent"
    return mock_user


# ==========================================
# 1. TEST SLA SERVICE LAYER
# ==========================================

@patch("app.service.sales_agent.sla_service.SLAService.generate_sla_pdf")
@patch("app.service.sales_agent.sla_service.supabase_secondary")
@pytest.mark.asyncio
async def test_create_contract_and_pdf_success(
    mock_supabase_sec, mock_generate_pdf
):
    # 1. Mock check existing SLA (Dapat ay walang existing contract pa)
    mock_existing_res = MagicMock()
    mock_existing_res.data = []

    # 2. Mock insert SLA contract response
    mock_insert_res = MagicMock()
    mock_insert_res.data = [
        {
            "id": "sla-uuid-1",
            "contract_no": "SLA-2026-001",
            "customer_id": "cust-uuid-111",
            "start_date": "2026-11-01",
            "end_date": "2027-11-01",
            "mode": "air",
            "origin": "Manila",
            "destination": "Cebu",
            "transit_days": 2,
            "ontime_target_pct": Decimal("98.5"),
            "claims_response_hours": 24,
            "currency": "PHP",
            "rate": Decimal("15000.00"),
            "rate_basis": "Per Shipment"
        }
    ]

    # 3. Mock customer profile lookup para sa email sending
    mock_customer_res = MagicMock()
    mock_customer_res.data = {
        "email": "customer@company.com",
        "company_name": "Test Company Inc"
    }

    # 4. Mock signed URL generation at storage upload
    mock_storage_client = MagicMock()
    mock_storage_client.from_.return_value.create_signed_url.return_value = {"signedURL": "https://supabase.co/signed-url"}
    mock_storage_client.from_.return_value.upload.return_value = {}
    mock_supabase_sec.storage = mock_storage_client

    # Konpigurasyon ng table side effects
    def table_side_effect(table_name):
        table_mock = MagicMock()
        if table_name == "sla_contracts":
            table_mock.select.return_value.eq.return_value.execute.return_value = mock_existing_res
            table_mock.insert.return_value.execute.return_value = mock_insert_res
            table_mock.update.return_value.eq.return_value.execute.return_value = MagicMock()
        elif table_name == "customer_profiles":
            table_mock.select.return_value.eq.return_value.maybe_single.return_value.execute.return_value = mock_customer_res
        return table_mock

    mock_supabase_sec.table.side_effect = table_side_effect

    # I-mock ang generate_sla_pdf para magbalik na lang ng dummy path at gumawa ng temporary file para hindi magka-FileNotFoundError
    dummy_pdf_path = "/tmp/sla_SLA-2026-001.pdf"
    mock_generate_pdf.return_value = dummy_pdf_path

    # Gamitin ang patch para sa built-in open at os operations na ginagamit sa file handling
    with patch("builtins.open", unittest.mock.mock_open(read_data=b"PDF content")), \
         patch("app.service.sales_agent.sla_service.os.path.exists", return_value=True), \
         patch("app.service.sales_agent.sla_service.os.remove", return_value=None):

        service = SLAService()
        
        from app.schemas.sales_agent.sla_schema import SLACreateRequest
        payload = SLACreateRequest(
            inquiry_id="inq-uuid-123",
            customer_id="cust-uuid-111",
            start_date=date(2026, 11, 1),
            end_date=date(2027, 11, 1),
            mode="air",
            origin="Manila",
            destination="Cebu",
            rate=Decimal("15000.00"),
            rate_basis="Per Shipment",
            payment_terms="Net 30",
            transit_days=2,
            pickup_window_hours=4,
            ontime_target_pct=Decimal("98.5"),
            tracking_interval_hours=6,
            claims_response_hours=24,
            late_credit_pct_per_day=Decimal("5.0"),
            credit_cap_pct=Decimal("20.0"),
            liability_limit_pct=Decimal("100.0")
        )

        result = await service.create_contract_and_pdf(payload, employee_id="emp-uuid-999")

        assert result is not None
        assert result["contract_no"] == "SLA-2026-001"
        assert "pdf_path" in result


# ==========================================
# 2. TEST SLA API ENDPOINTS
# ==========================================

@patch("app.middleware.security.supabase_secondary")
@patch("app.service.sales_agent.sla_service.SLAService.create_contract_and_pdf", new_callable=AsyncMock)
def test_create_sla_contract_endpoint(mock_create_service, mock_security_supabase):
    # Mock RoleChecker database check
    mock_db_res = MagicMock()
    mock_db_res.data = {"role": "sales_agent", "email": "salesagent@example.com"}
    mock_security_supabase.table.return_value.select.return_value.eq.return_value.maybe_single.return_value.execute.return_value = mock_db_res

    mock_create_service.return_value = {
        "contract_id": "sla-uuid-1",
        "contract_no": "SLA-2026-001",
        "pdf_path": "contracts/2026/SLA-2026-001.pdf"
    }

    app.dependency_overrides[get_current_user] = override_sales_agent

    try:
        response = client.post(
            "/api/sales/sla/",
            json={
                "inquiry_id": "inq-uuid-123",
                "customer_id": "cust-uuid-111",
                "start_date": "2026-11-01",
                "end_date": "2027-11-01",
                "mode": "air",
                "origin": "Manila",
                "destination": "Cebu",
                "rate": 15000.00,
                "rate_basis": "Per Shipment",
                "payment_terms": "Net 30",
                "transit_days": 2,
                "pickup_window_hours": 4,
                "ontime_target_pct": 98.5,
                "tracking_interval_hours": 6,
                "claims_response_hours": 24,
                "late_credit_pct_per_day": 5.0,
                "credit_cap_pct": 20.0,
                "liability_limit_pct": 100.0
            }
        )

        assert response.status_code == 201
        data = response.json()
        assert data["success"] is True
        assert "SLA Contract created" in data["message"]
        assert data["data"]["contract_no"] == "SLA-2026-001"
        mock_create_service.assert_awaited_once()
    finally:
        app.dependency_overrides.clear()


@patch("app.middleware.security.supabase_secondary")
@patch("app.routes.sales_agent.sla_route.SLAService")
def test_check_sla_existence_endpoint(mock_sla_service_class, mock_security_supabase):
    mock_db_res = MagicMock()
    mock_db_res.data = {"role": "sales_agent"}
    mock_security_supabase.table.return_value.select.return_value.eq.return_value.maybe_single.return_value.execute.return_value = mock_db_res

    # Mock service instance at supabase query para sa check existence
    mock_instance = mock_sla_service_class.return_value
    mock_query_res = MagicMock()
    mock_query_res.data = [
        {"id": "sla-uuid-1", "contract_no": "SLA-2026-001", "status": "active"}
    ]
    mock_instance.supabase.table.return_value.select.return_value.eq.return_value.execute.return_value = mock_query_res

    app.dependency_overrides[get_current_user] = override_sales_agent

    try:
        response = client.get("/api/sales/sla/check/inq-uuid-123")

        assert response.status_code == 200
        data = response.json()
        assert data["success"] is True
        assert data["exists"] is True
        assert data["data"]["contract_no"] == "SLA-2026-001"
    finally:
        app.dependency_overrides.clear()