import sys
from unittest.mock import MagicMock, patch
sys.modules["weasyprint"] = MagicMock()
import pytest
from fastapi.testclient import TestClient
from app.main import app

client = TestClient(app)

# ==========================================
# TEST LOGISTIC API ENDPOINT 
# ==========================================

@patch("app.routes.logistic_api.LOGISTIC_API_KEY", "test-secret-logistic-key")
@patch("app.routes.logistic_api.supabase_secondary")
def test_get_closed_won_for_logistics_with_sla_success(mock_supabase_sec):
    # 1. Mock inquiries result 
    mock_inquiries_res = MagicMock()
    mock_inquiries_res.data = [
        {
            "id": "inq-uuid-001",
            "inquiry_code": "INQ-2026-001",
            "company_name": "SLA Express Corp",
            "contact_person": "Juan Santos",
            "phone_number": "09181234567",
            "email": "sla@express.com",
            "cargo_details": "Secure cargo",
            "pickup_address": "Manila Hub",
            "pickup_datetime": "2026-11-20T08:00:00Z",
            "contract_type": "sla",
            "sla_reason": "High volume client"
        }
    ]

    # 2. Mock shipment result
    mock_shipment_res = MagicMock()
    mock_shipment_res.data = {
        "consignment_note_number": "CN-SLA-999",
        "consignee_company_name": "Receiver Corp",
        "consignee_contact_person": "Maria Reyes",
        "consignee_address": "Cebu Hub",
        "consignee_telephone": "09229876543",
        "package_type": "PARCEL",
        "parcel_size": "Medium",
        "dimensions_cms": "30x30x30",
        "chargeable_weight": 50.0,
        "transport_mode": "AIR",
        "delivery_speed": "NEXT_DAY",
        "handling_terms": "DDP",
        "notes": "Handle with extreme care"
    }

    # 3. Mock SLA contract result galing sa sla_contracts table
    mock_sla_res = MagicMock()
    mock_sla_res.data = {
        "contract_no": "SLA-CON-2026-01",
        "transit_days": 1,
        "pickup_window_hours": 2,
        "ontime_target_pct": 99.5,
        "tracking_interval_hours": 4,
        "claims_response_hours": 24,
        "late_credit_pct_per_day": 10.0,
        "credit_cap_pct": 50.0,
        "liability_limit_pct": 100.0,
        "rate": 1500.0,
        "currency": "PHP",
        "pdf_path": "/storage/sla/contract_01.pdf"
    }

    # I-configure ang table side effect para sa inquiries, shipments, at sla_contracts tables
    def table_side_effect(table_name):
        table_mock = MagicMock()
        if table_name == "inquiries":
            table_mock.select.return_value.eq.return_value.execute.return_value = mock_inquiries_res
        elif table_name == "shipments":
            table_mock.select.return_value.eq.return_value.maybe_single.return_value.execute.return_value = mock_shipment_res
        elif table_name == "sla_contracts":
            # Chaining para sa .select().eq().eq().maybe_single().execute()
            table_mock.select.return_value.eq.return_value.eq.return_value.maybe_single.return_value.execute.return_value = mock_sla_res
        return table_mock

    mock_supabase_sec.table.side_effect = table_side_effect

    headers = {"X-API-Key": "test-secret-logistic-key"}
    response = client.get("/api/logistic/closed-won-shipments", headers=headers)

    assert response.status_code == 200
    data = response.json()
    assert data["success"] is True
    assert data["count"] == 1
    
    record = data["data"][0]
    assert record["inquiry_code"] == "INQ-2026-001"
    assert record["contract_type"] == "sla"
    assert record["has_sla"] is True
    assert record["sla_details"]["contract_no"] == "SLA-CON-2026-01"
    assert record["sla_details"]["transit_days"] == 1


@patch("app.routes.logistic_api.LOGISTIC_API_KEY", "test-secret-logistic-key")
@patch("app.routes.logistic_api.supabase_secondary")
def test_get_closed_won_for_logistics_standard_tc_success(mock_supabase_sec):
    # Test kapag standard T&C ang contract_type 
    mock_inquiries_res = MagicMock()
    mock_inquiries_res.data = [
        {
            "id": "inq-uuid-002",
            "inquiry_code": "INQ-2026-002",
            "company_name": "Standard Corp",
            "contract_type": "standard_tc",
            "sla_reason": None
        }
    ]

    mock_shipment_res = MagicMock()
    mock_shipment_res.data = {"consignment_note_number": "CN-STD-123"}

    def table_side_effect(table_name):
        table_mock = MagicMock()
        if table_name == "inquiries":
            table_mock.select.return_value.eq.return_value.execute.return_value = mock_inquiries_res
        elif table_name == "shipments":
            table_mock.select.return_value.eq.return_value.maybe_single.return_value.execute.return_value = mock_shipment_res
        return table_mock

    mock_supabase_sec.table.side_effect = table_side_effect

    headers = {"X-API-Key": "test-secret-logistic-key"}
    response = client.get("/api/logistic/closed-won-shipments", headers=headers)

    assert response.status_code == 200
    data = response.json()
    record = data["data"][0]
    assert record["contract_type"] == "standard_tc"
    assert record["has_sla"] is False
    assert record["sla_details"] is None


@patch("app.routes.logistic_api.LOGISTIC_API_KEY", "test-secret-logistic-key")
def test_get_closed_won_for_logistics_unauthorized_missing_key():
    response = client.get("/api/logistic/closed-won-shipments")
    assert response.status_code == 401
    assert "Invalid or missing Logistic API Key" in response.json()["detail"]


@patch("app.routes.logistic_api.LOGISTIC_API_KEY", "test-secret-logistic-key")
def test_get_closed_won_for_logistics_unauthorized_wrong_key():
    headers = {"X-API-Key": "wrong-key-xyz"}
    response = client.get("/api/logistic/closed-won-shipments", headers=headers)
    assert response.status_code == 401
    assert "Invalid or missing Logistic API Key" in response.json()["detail"]


@patch("app.routes.logistic_api.LOGISTIC_API_KEY", "test-secret-logistic-key")
@patch("app.routes.logistic_api.supabase_secondary")
def test_get_closed_won_for_logistics_server_error(mock_supabase_sec):
    mock_supabase_sec.table.side_effect = Exception("DB Connection timeout")

    headers = {"X-API-Key": "test-secret-logistic-key"}
    response = client.get("/api/logistic/closed-won-shipments", headers=headers)

    assert response.status_code == 500
    assert "Failed to fetch data for logistics" in response.json()["detail"]