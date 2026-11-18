from fastapi import HTTPException, status
from app.supabase_config.supabase import supabase_secondary
from app.middleware.email_middleware import generate_strong_password, send_customer_credentials_email
from app.service.audit_service import log_audit  
from typing import Optional

async def create_customer_portal_account_service(
    ticket_id: str, 
    provided_password: Optional[str] = None,
    current_admin_user: Optional[dict] = None,
    client_ip: Optional[str] = None
):
    try:
        # 1. Kunin ang detalye ng ticket mula sa customer_tickets table
        ticket_res = supabase_secondary.table("customer_tickets").select("*").eq("id", ticket_id).maybe_single().execute()
        
        if not ticket_res.data:
            raise HTTPException(
                status_code=status.HTTP_404_NOT_FOUND,
                detail=f"Ticket with ID {ticket_id} not found."
            )
            
        ticket = ticket_res.data
        email = ticket.get("email")
        company_name = ticket.get("company_name")
        contact_person = ticket.get("contact_person")
        phone_number = ticket.get("phone_number")
        
        if ticket.get("ticket_status") == "created":
            raise HTTPException(
                status_code=status.HTTP_400_BAD_REQUEST,
                detail="Customer account for this ticket has already been created."
            )

        # 2. Gamitin ang binigay na password o gumawa ng secure random password
        password_to_use = provided_password if provided_password else generate_strong_password()

        # 3. Gumawa ng user sa Supabase Auth
        auth_response = supabase_secondary.auth.admin.create_user({
            "email": email,
            "password": password_to_use,
            "email_confirm": True,
            "user_metadata": {
                "company_name": company_name,
                "contact_person": contact_person,
                "role": "customer"
            }
        })
        
        if not auth_response.user:
            raise HTTPException(
                status_code=status.HTTP_400_BAD_REQUEST,
                detail="Failed to create user in Supabase Auth."
            )
            
        auth_user_id = auth_response.user.id

        # 4. I-save ang profile sa customer_profiles table 
        profile_payload = {
            "auth_user_id": auth_user_id,
            "company_name": company_name,
            "contact_person": contact_person,
            "email": email,
            "phone_number": phone_number
        }
        
        profile_res = supabase_secondary.table("customer_profiles").insert(profile_payload).execute()
        created_profile = profile_res.data[0] if profile_res.data else {}
        customer_id = created_profile.get("customer_id")

        # 5. I-update ang ticket_status sa 'created'
        update_res = (
            supabase_secondary.table("customer_tickets")
            .update({"ticket_status": "created"})
            .eq("id", ticket_id)
            .execute()
        )

        # 6. I-send ang credentials email 
        email_sent = send_customer_credentials_email(
            to_email=email,
            company_name=company_name,
            customer_id=customer_id,
            temp_password=password_to_use
        )

        # =========================================================================
        # 7. Audit logs
        # =========================================================================
        admin_email = current_admin_user.get("email") if current_admin_user else "system_admin"
        
        await log_audit(
            user_email=admin_email,
            action="CREATE_CUSTOMER_PORTAL_ACCOUNT",
            ip_address=client_ip,
            details={
                "ticket_id": ticket_id,
                "customer_email": email,
                "customer_id": customer_id,
                "company_name": company_name,
                "email_sent": email_sent
            }
        )

        return {
            "success": True,
            "message": f"Customer portal account successfully created for {email}.",
            "customer_id": customer_id,
            "user_id": auth_user_id,
            "email_sent": email_sent,
            "ticket_data": update_res.data[0] if update_res.data else None
        }

    except HTTPException as he:
        raise he
    except Exception as e:
        raise HTTPException(
            status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
            detail=f"Error creating customer account: {str(e)}"
        )