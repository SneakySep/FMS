import uuid
from datetime import datetime
import dateutil.parser
from typing import Optional
from fastapi import APIRouter, HTTPException, Query, Path, status, BackgroundTasks
from app.supabase_config.supabase import supabase_secondary
from app.schemas.lead import (
    LeadResponseSchema, 
    PaginatedLeadResponseSchema, 
    LeadStatsResponseSchema, 
    StatusUpdateSchema,
    LeadCreateSchema,
    SendQuotationSchema
)
from app.middleware.quotation import send_quotation_email

import io
from reportlab.lib.pagesizes import letter
from reportlab.pdfgen import canvas

router = APIRouter(
    prefix="/api/v1/leads",
    tags=["Sales Agent Leads"]
)

@router.get("/", response_model=PaginatedLeadResponseSchema)
async def get_leads(
    status: Optional[str] = Query(None, description="Filter by status e.g. new_inquiry, qualifying, quote_sent"),
    search: Optional[str] = Query(None, description="Search by company_name or contact_person"),
    include_closed: bool = Query(False, description="Set to True para isama ang closed_won at closed_lost (para sa Kanban)"),
    page: int = Query(1, ge=1),
    limit: int = Query(10, ge=1, le=100)
):
    try:
        offset = (page - 1) * limit
        query = supabase_secondary.table("inquiries").select("*", count="exact")

        # 1. Filter sa specific status kung tinukoy
        if status and status.lower() != "all":
            query = query.eq("status", status)
        elif not include_closed:
            # Pag galing sa My Leads (include_closed=False), itatago ang closed items
            query = query.neq("status", "closed_won").neq("status", "closed_lost")

        if search:
            query = query.or_(f"company_name.ilike.%{search}%,contact_person.ilike.%{search}%")

        query = query.order("created_at", desc=True).range(offset, offset + limit - 1)
        response = query.execute()

        return {
            "total": response.count if response.count is not None else len(response.data),
            "data": response.data
        }
    
    except Exception as e:
        raise HTTPException(status_code=500, detail=str(e))


@router.get("/stats", response_model=LeadStatsResponseSchema)
async def get_lead_stats():
    try:
        res = supabase_secondary.table("inquiries").select("status").execute()
        data = res.data or []

        return {
            "all": len([item for item in data if item.get("status") not in ["closed_won", "closed_lost"]]),
            "new_inquiry": sum(1 for item in data if item.get("status") == "new_inquiry"),
            "qualifying": sum(1 for item in data if item.get("status") == "qualifying"),
            "quote_sent": sum(1 for item in data if item.get("status") == "quote_sent"),
            "negotiation": sum(1 for item in data if item.get("status") == "negotiation"),
            "closed_won": sum(1 for item in data if item.get("status") == "closed_won"),
            "closed_lost": sum(1 for item in data if item.get("status") == "closed_lost"),
        }

    except Exception as e:
        raise HTTPException(status_code=500, detail=str(e))


@router.patch("/{lead_id}/status", response_model=LeadResponseSchema)
async def update_lead_status(
    payload: StatusUpdateSchema,
    lead_id: str = Path(..., description="UUID ng lead record")
):
    try:
        # 1. Kunin muna ang kasalukuyang inquiry record mula sa DB
        lead_res = supabase_secondary.table("inquiries").select("*").eq("id", lead_id).execute()
        if not lead_res.data:
            raise HTTPException(status_code=404, detail="Lead ID not found.")
        
        current_lead = lead_res.data[0]

        # 2. I-prep ang dictionary para sa inquiries table update
        update_data = {"status": payload.status}

        if payload.cargo_details is not None:
            update_data["cargo_details"] = payload.cargo_details

        if payload.estimated_amount is not None:
            update_data["estimated_amount"] = payload.estimated_amount

        if payload.pickup_address:
            update_data["pickup_address"] = payload.pickup_address

        if payload.pickup_datetime:
            update_data["pickup_datetime"] = payload.pickup_datetime.isoformat()

        if payload.notes is not None:
            update_data["notes"] = payload.notes


        # I-update ang assigned agent info sa inquiry kung may pumasok mula sa payload
        if payload.agent_id:
            update_data["assigned_agent_id"] = payload.agent_id
        if payload.agent_name:
            update_data["assigned_agent_name"] = payload.agent_name
        if payload.agent_email:
            update_data["assigned_agent_email"] = payload.agent_email

        # 3. Validation at Ticket Insertion kapag CLOSED_WON
        if payload.status == "closed_won":
            if not payload.pickup_address or not payload.pickup_datetime:
                raise HTTPException(
                    status_code=400, 
                    detail="Pickup address and datetime are required when closing a lead as WON."
                )

            # Helper function para masiguradong valid UUID ang ipapasa sa DB
            def sanitize_uuid(val):
                if not val:
                    return None
                try:
                    return str(uuid.UUID(str(val)))
                except ValueError:
                    return None  

            # Dynamic Ticket Code Generation 
            ticket_code = f"TKT-{datetime.now().year}-{str(uuid.uuid4())[:6].upper()}"

            # Kunin ang safe values para sa non-nullable columns
            raw_agent_id = payload.agent_id or current_lead.get("assigned_agent_id")
            safe_agent_id = sanitize_uuid(raw_agent_id)

            ticket_payload = {
                "ticket_code": ticket_code,
                "inquiry_id": lead_id,
                "company_name": current_lead.get("company_name") or "N/A",
                "contact_person": current_lead.get("contact_person") or "N/A",
                "email": current_lead.get("email") or "N/A",
                "phone_number": current_lead.get("phone_number") or "N/A",
                "service_type": current_lead.get("service_type"),
                "origin": current_lead.get("origin"),
                "destination": current_lead.get("destination"),
                "cargo_details": payload.cargo_details if payload.cargo_details is not None else current_lead.get("cargo_details"),
                "pickup_address": payload.pickup_address,
                "pickup_datetime": payload.pickup_datetime.isoformat() if payload.pickup_datetime else None,
                "agreed_amount": payload.estimated_amount or current_lead.get("estimated_amount") or 0.0,
                "ticket_status": "for account",
                "agent_id": safe_agent_id,  
                "agent_name": payload.agent_name or current_lead.get("assigned_agent_name"),
                "agent_email": payload.agent_email or current_lead.get("assigned_agent_email")
            }

            try:
                supabase_secondary.table("tickets").insert(ticket_payload).execute()
            except Exception as ticket_err:
                raise HTTPException(
                    status_code=400, 
                    detail=f"Error creating ticket: {str(ticket_err)}"
                )

        # 4. Execute the update in Supabase 'inquiries' table
        response = (
            supabase_secondary.table("inquiries")
            .update(update_data)
            .eq("id", lead_id)
            .execute()
        )

        return response.data[0]

    except HTTPException:
        raise
    except Exception as e:
        raise HTTPException(status_code=500, detail=str(e))
    


@router.get("/dashboard-kpis")
async def get_dashboard_kpis(agent_id: Optional[str] = Query(None)):
    try:
        # 1. Fetch lahat ng inquiries na closed_won
        query = supabase_secondary.table("inquiries").select("*").eq("status", "closed_won")
        
        # Pag nagpasa ng agent_id, subukang i-filter (optional)
        if agent_id:
            query = query.eq("assigned_agent_id", agent_id)

        res = query.execute()
        closed_leads = res.data or []

        now = datetime.now()
        current_year = now.year
        current_month = now.month

        # Calculate Last Month target
        if current_month == 1:
            last_month = 12
            last_month_year = current_year - 1
        else:
            last_month = current_month - 1
            last_month_year = current_year

        revenue_this_month = 0.0
        revenue_last_month = 0.0
        customers_this_month = 0
        customers_last_month = 0

        for lead in closed_leads:
            # Siguraduhing may float value kahit NULL sa DB
            amount = float(lead.get("estimated_amount") or lead.get("agreed_amount") or 0.0)
            created_at_raw = lead.get("created_at") or lead.get("updated_at")

            if not created_at_raw:
                revenue_this_month += amount
                customers_this_month += 1
                continue

            created_dt = dateutil.parser.parse(str(created_at_raw))

            # Match August 2026
            if created_dt.year == current_year and created_dt.month == current_month:
                revenue_this_month += amount
                customers_this_month += 1
            elif created_dt.year == last_month_year and created_dt.month == last_month:
                revenue_last_month += amount
                customers_last_month += 1
            else:
                revenue_this_month += amount
                customers_this_month += 1

        return {
            "status": "success",
            "data": {
                "revenue": {
                    "current": revenue_this_month,
                    "previous": revenue_last_month,
                    "diff": revenue_this_month - revenue_last_month
                },
                "customers_closed": {
                    "current": customers_this_month,
                    "previous": customers_last_month,
                    "diff": customers_this_month - customers_last_month
                }
            }
        }

    except Exception as e:
        raise HTTPException(status_code=500, detail=str(e))


# Create new leads
@router.post("/leads", status_code=status.HTTP_201_CREATED)
async def create_new_leads(payload: LeadCreateSchema):
    try:
        data = payload.model_dump()

        # Insert papunta sa supabase inquire table
        res = supabase_secondary.table("inquiries").insert(data).execute()

        if not res.data:
            raise HTTPException(status_code=400, detail="Field to create lead")

        return {
            "status": "success",
            "message": "Lead created successfully",
            "data": res.data[0]
        }

    except Exception as e:
        raise HTTPException(status_code=500, detail=str(e))


def generate_quotation_pdf(lead_info: dict, quote_data: SendQuotationSchema) -> bytes:
    """Gumagawa ng in-memory PDF file gamit ang ReportLab"""
    buffer = io.BytesIO()
    p = canvas.Canvas(buffer, pagesize=letter)
    
    p.setFont("Helvetica-Bold", 18)
    p.drawString(50, 750, "SWIFTFREIGHT LOGISTICS")
    
    p.setFont("Helvetica-Bold", 14)
    p.drawString(50, 720, "OFFICIAL FREIGHT QUOTATION")
    
    p.setFont("Helvetica", 10)
    p.drawString(50, 690, f"Customer: {lead_info.get('company_name') or lead_info.get('contact_person')}")
    p.drawString(50, 675, f"Email: {quote_data.customer_email}")
    p.drawString(50, 660, f"Route: {lead_info.get('origin')} -> {lead_info.get('destination')}")
    p.drawString(50, 645, f"Service: {lead_info.get('service_type')}")
    
    final_amount = quote_data.base_amount - (quote_data.discount_amount or 0.0)
    p.setFont("Helvetica-Bold", 12)
    p.drawString(50, 610, f"Total Amount: PHP {final_amount:,.2f}")
    
    if quote_data.remarks:
        p.setFont("Helvetica-Oblique", 9)
        p.drawString(50, 580, f"Remarks: {quote_data.remarks}")
        
    p.showPage()
    p.save()
    
    pdf_out = buffer.getvalue()
    buffer.close()
    return pdf_out


@router.post("/{lead_id}/send-quotation")
async def send_lead_quotation(
    lead_id: str,
    payload: SendQuotationSchema,
    background_tasks: BackgroundTasks
):
    try:
        # 1. Kunin ang Inquiry record sa Supabase
        res = supabase_secondary.table("inquiries").select("*").eq("id", lead_id).execute()
        if not res.data:
            raise HTTPException(status_code=404, detail="Lead not found")
        
        lead_info = res.data[0]
        company_name = lead_info.get("company_name") or lead_info.get("contact_person") or "Customer"
        final_amount = payload.base_amount - (payload.discount_amount or 0.0)

        # 2. I-generate ang PDF
        pdf_bytes = generate_quotation_pdf(lead_info, payload)

        # 3. I-send ang email gamit ang helper
        email_sent = send_quotation_email(
            to_email=payload.customer_email,
            company_name=company_name,
            pdf_bytes=pdf_bytes,
            filename=f"Quotation_{company_name.replace(' ', '_')}.pdf"
        )

        if not email_sent:
            raise HTTPException(status_code=500, detail="Failed to send quotation email. Check SMTP settings.")

        # 4. Pag matagumpay ang email, i-update ang status sa Supabase papuntang 'quote_sent'
        supabase_secondary.table("inquiries").update({
            "status": "quote_sent",
            "estimated_amount": final_amount,
            "notes": payload.remarks or lead_info.get("notes")
        }).eq("id", lead_id).execute()

        return {
            "status": "success",
            "message": f"Quotation successfully emailed to {payload.customer_email} and status updated to quote_sent."
        }

    except HTTPException:
        raise
    except Exception as e:
        raise HTTPException(status_code=500, detail=str(e))