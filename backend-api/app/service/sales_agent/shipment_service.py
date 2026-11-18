from fastapi import HTTPException, status
from app.supabase_config.supabase import supabase_secondary

async def create_shipment_record(payload_dict: dict, current_user: dict, client_ip: str):
    try:
        inquiry_id = payload_dict.get("inquiry_id")
        
        # 1. I-check muna kung umiiral ang inquiry at kunin ang mga detalye nito
        inquiry_res = supabase_secondary.table("inquiries").select("*").eq("id", inquiry_id).maybe_single().execute()
        if not inquiry_res.data:
            raise HTTPException(
                status_code=status.HTTP_404_NOT_FOUND,
                detail=f"Inquiry with ID {inquiry_id} not found."
            )
        inquiry = inquiry_res.data
            
        # 2. I-insert ang bagong shipment record
        shipment_res = supabase_secondary.table("shipments").insert(payload_dict).execute()
        
        if not shipment_res.data:
            raise HTTPException(
                status_code=status.HTTP_400_BAD_REQUEST,
                detail="Failed to insert shipment record into database."
            )
            
        # 3. Automatic na i-update ang status ng inquiry sa 'closed_won'
        supabase_secondary.table("inquiries").update({"status": "closed_won"}).eq("id", inquiry_id).execute()
        
        # =========================================================================
        # 4. AUTOMATIC TICKET CREATION PARA SA SUPER ADMIN 
        # =========================================================================
        existing_ticket = supabase_secondary.table("customer_tickets").select("id").eq("inquiry_id", inquiry_id).maybe_single().execute()
        
        if not existing_ticket.data:
            ticket_payload = {
                "inquiry_id": inquiry_id,
                "company_name": inquiry.get("company_name"),
                "contact_person": inquiry.get("contact_person"),
                "email": inquiry.get("email"),
                "phone_number": inquiry.get("phone_number"),
                "pickup_datetime": inquiry.get("pickup_datetime"), # Dito kukunin ang date para sa countdown!
                "ticket_status": "for_account" # Default status para sa super admin dashboard
            }
            supabase_secondary.table("customer_tickets").insert(ticket_payload).execute()
        
        return shipment_res.data[0]
        
    except HTTPException as he:
        raise he
    except Exception as e:
        raise HTTPException(
            status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
            detail=f"Database error during shipment creation: {str(e)}"
        )