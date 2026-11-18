import sys
from unittest.mock import MagicMock, AsyncMock, patch
sys.modules["weasyprint"] = MagicMock()

import pytest
from fastapi.testclient import TestClient
from app.main import app
from app.middleware.security import get_current_user
from app.service.sales_agent.shipment_service import create_shipment_record

client = TestClient(app)

def override_sales_agent():
    mock_user = MagicMock()
    mock_user.id = "fake-agent-id"
    mock_user.email = "salesagent@example.com"
    mock_user.role = "sales_agent"
    return mock_user


# ==========================================
# 1. TEST SHIPMENT SERVICE LAYER
# ==========================================

@patch("app.service.sales_agent.shipment_service.supabase_secondary")
@pytest.mark.asyncio
async def test_create_shipment_record_success(mock_supabase_sec):
    # 1. Mock inquiry lookup (Existing inquiry)
    mock_inquiry_res = MagicMock()
    mock_inquiry_res.data = {
        "id": "inquiry-uuid-123",
        "company_name": "Mega Freight Inc",
        "contact_person": "Juan dela Cruz",
        "email": "juan@mega.com",
        "phone_number": "09123456789",
        "pickup_datetime": "2026-11-15T10:00:00Z"
    }

    # 2. Mock shipment insert result
    mock_shipment_res = MagicMock()
    mock_shipment_res.data = [
        {
            "id": "shipment-uuid-1",
            "inquiry_id": "inquiry-uuid-123",
            "consignment_note_number": "CN-999888"
        }
    ]

    # 3. Mock customer ticket check (Wala pang existing ticket)
    mock_ticket_res = MagicMock()
    mock_ticket_res.data = None

    # I-configure ang table side effect para sa inquiries, shipments, at customer_tickets tables
    def table_side_effect(table_name):
        table_mock = MagicMock()
        if table_name == "inquiries":
            # Pwedeng magsilbi para sa select, update, at maybe_single
            table_mock.select.return_value.eq.return_value.maybe_single.return_value.execute.return_value = mock_inquiry_res
            table_mock.update.return_value.eq.return_value.execute.return_value = MagicMock()
        elif table_name == "shipments":
            table_mock.insert.return_value.execute.return_value = mock_shipment_res
        elif table_name == "customer_tickets":
            table_mock.select.return_value.eq.return_value.maybe_single.return_value.execute.return_value = mock_ticket_res
            table_mock.insert.return_value.execute.return_value = MagicMock()
        return table_mock

    mock_supabase_sec.table.side_effect = table_side_effect

    payload_dict = {
        "inquiry_id": "inquiry-uuid-123",
        "consignment_note_number": "CN-999888",
        "consignee_company_name": "Receiver Corp",
        "consignee_address": "Cebu City"
    }

    current_user = override_sales_agent()
    result = await create_shipment_record(payload_dict, current_user, "127.0.0.1")

    assert result is not None
    assert result["consignment_note_number"] == "CN-999888"
    assert result["inquiry_id"] == "inquiry-uuid-123"


@patch("app.service.sales_agent.shipment_service.supabase_secondary")
@pytest.mark.asyncio
async def test_create_shipment_inquiry_not_found(mock_supabase_sec):
    # Mock inquiry lookup na nagbabalik ng None (Hindi nahanap)
    mock_inquiry_res = MagicMock()
    mock_inquiry_res.data = None

    mock_supabase_sec.table.return_value.select.return_value.eq.return_value.maybe_single.return_value.execute.return_value = mock_inquiry_res

    payload_dict = {
        "inquiry_id": "nonexistent-id",
        "consignment_note_number": "CN-123",
        "consignee_company_name": "Test",
        "consignee_address": "Test"
    }

    current_user = override_sales_agent()

    with pytest.raises(Exception) as exc_info:
        await create_shipment_record(payload_dict, current_user, "127.0.0.1")

    assert exc_info.value.status_code == 404
    assert "not found" in exc_info.value.detail


# ==========================================
# 2. TEST SHIPMENT API ENDPOINT
# ==========================================

@patch("app.middleware.security.supabase_secondary")
@patch("app.routes.sales_agent.shipment_router.create_shipment_record", new_callable=AsyncMock)
def test_create_shipment_endpoint(mock_create_service, mock_security_supabase):
    mock_db_res = MagicMock()
    mock_db_res.data = {"role": "sales_agent"}
    mock_security_supabase.table.return_value.select.return_value.eq.return_value.maybe_single.return_value.execute.return_value = mock_db_res

    mock_create_service.return_value = {
        "id": "shipment-uuid-10",
        "inquiry_id": "inquiry-uuid-123",
        "consignment_note_number": "CN-555444"
    }

    app.dependency_overrides[get_current_user] = override_sales_agent

    try:
        response = client.post(
            "/api/sales/shipments/",
            json={
                "inquiry_id": "inquiry-uuid-123",
                "consignment_note_number": "CN-555444",
                "consignee_company_name": "Destination Corp",
                "consignee_address": "Davao City"
            }
        )

        assert response.status_code == 201
        data = response.json()
        assert data["success"] is True
        assert "Shipment created successfully" in data["message"]
        assert data["data"]["consignment_note_number"] == "CN-555444"
        mock_create_service.assert_awaited_once()
    finally:
        app.dependency_overrides.clear()