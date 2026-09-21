from fastapi import FastAPI
from fastapi.testclient import TestClient
from unittest.mock import patch, MagicMock
import pytest

from app.routes.sales_agent.leads import router, generate_quotation_pdf

app = FastAPI()
app.include_router(router)

client = TestClient(app)


def test_generate_quotation_pdf_unit():
    """Sinusubukan kung nakakagawa ng valid PDF bytes ang ReportLab function."""
    lead_info = {
        "company_name": "Swift Logistics Inc.",
        "origin": "Manila",
        "destination": "Davao",
        "service_type": "Air Freight"
    }
    
    class MockSchema:
        customer_email = "test@swift.com"
        base_amount = 5000.0
        discount_amount = 500.0
        remarks = "Special discount applied."

    pdf_bytes = generate_quotation_pdf(lead_info, MockSchema())
    
    assert isinstance(pdf_bytes, bytes)
    assert len(pdf_bytes) > 0
    assert pdf_bytes.startswith(b"%PDF")


@patch("app.routes.sales_agent.leads.supabase_secondary")
@patch("app.routes.sales_agent.leads.send_quotation_email")
def test_send_lead_quotation_success(mock_send_email, mock_supabase):
    """Sinusubukan ang matagumpay na daloy ng pag-generate ng PDF, pag-email, at pag-update sa Supabase."""
    
    mock_select = MagicMock()
    mock_select.data = [{
        "id": "lead-abc-123",
        "company_name": "ProEdge Computing Corp.",
        "origin": "Manila",
        "destination": "Cebu",
        "service_type": "Sea Freight",
        "notes": "Initial inquiry notes"
    }]
    mock_supabase.table.return_value.select.return_value.eq.return_value.execute.return_value = mock_select
    
    mock_send_email.return_value = True
    
    mock_update = MagicMock()
    mock_supabase.table.return_value.update.return_value.eq.return_value.execute.return_value = mock_update

    payload = {
        "lead_id": "lead-abc-123",
        "customer_email": "chrmasong@gmail.com",
        "base_amount": 12000.0,
        "discount_amount": 1000.0,
        "remarks": "Approved freight quotation."
    }

    response = client.post("/api/v1/leads/lead-abc-123/send-quotation", json=payload)
    
    assert response.status_code == 200
    data = response.json()
    assert data["status"] == "success"
    assert "successfully emailed" in data["message"]
    mock_send_email.assert_called_once()


@patch("app.routes.sales_agent.leads.supabase_secondary")
def test_send_lead_quotation_not_found(mock_supabase):
    """Sinusubukan ang error handling kapag ang lead_id ay walang makitang rekord sa database."""
    
    mock_select = MagicMock()
    mock_select.data = []  
    mock_supabase.table.return_value.select.return_value.eq.return_value.execute.return_value = mock_select

    payload = {
        "lead_id": "non-existent-id",
        "customer_email": "chrmasong@gmail.com",
        "base_amount": 5000.0
    }

    response = client.post("/api/v1/leads/non-existent-id/send-quotation", json=payload)
    
    assert response.status_code == 404
    assert response.json()["detail"] == "Lead not found"


@patch("app.routes.sales_agent.leads.supabase_secondary")
@patch("app.routes.sales_agent.leads.send_quotation_email")
def test_send_lead_quotation_email_failure(mock_send_email, mock_supabase):
    """Sinusubukan ang HTTP 500 exception kapag pumalya ang pag-padala ng email (SMTP failure)."""
    
    mock_select = MagicMock()
    mock_select.data = [{
        "id": "lead-abc-123",
        "company_name": "Test Company",
        "origin": "Manila",
        "destination": "Cebu",
        "service_type": "Air"
    }]
    mock_supabase.table.return_value.select.return_value.eq.return_value.execute.return_value = mock_select
    
    mock_send_email.return_value = False

    payload = {
        "lead_id": "lead-abc-123",
        "customer_email": "chrmasong@gmail.com",
        "base_amount": 8000.0
    }

    response = client.post("/api/v1/leads/lead-abc-123/send-quotation", json=payload)
    
    assert response.status_code == 500
    assert "Failed to send quotation email" in response.json()["detail"]