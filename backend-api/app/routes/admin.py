from fastapi import APIRouter, HTTPException, BackgroundTasks # Gagamitin para hindi mag-hang ang API response habang nagse-send ng email
from typing import Optional, List
import secrets
from app.supabase_config.supabase import supabase_secondary
from app.schemas.admin import CloseWonTicketResponseSchema, CreateCustomerFromTicketSchema
from app.service.email_service import send_customer_welcome_email

router = APIRouter(
    prefix="/api/v1/admin",
    tags=["Admin Management"]
)

# 1. Kunin ang lahat na close won tickets
@router.get("/close-won-tickets", response_model=list[CloseWonTicketResponseSchema])
async def get_close_won_tickets():

    try:
        # Kunin ang tickets na galing sa inquiries na closed_won
        res = supabase_secondary.table("tickets").select("*").execute()
        tickets = res.data or []

        return tickets
    except Exception as e:
        raise HTTPException(status_code=500, detail=str(e))


# 2. Create customer portal from close won ticket
@router.post("/create-customer-from-ticket")
async def create_customer_from_ticket(
    payload: CreateCustomerFromTicketSchema, 
    background_tasks: BackgroundTasks
):
    try:
        # 1. Create Auth account sa Secondary supabase
        auth_res = supabase_secondary.auth.admin.create_user({
            "email": payload.email,
            "password": payload.password,
            "email_confirm": True,
            "user_metadata": {
                "first_name": payload.first_name,
                "last_name": payload.last_name,
                "role": "customer"
            }
        })

        if not auth_res.user:
            raise HTTPException(status_code=400, detail="Failed to create Auth account.")

        new_user_id = auth_res.user.id

        # 2. Mag-insert sa public.users table (Secondary DB)
        user_data = {
            "id": new_user_id,
            "email": payload.email,
            "first_name": payload.first_name,
            "last_name": payload.last_name,
            "company_name": payload.company_name,
            "phone_number": payload.phone_number,
            "role": "customer"
        }

        supabase_secondary.table("users").insert(user_data).execute()

        # 3. I-update ang ticket 
        supabase_secondary.table("tickets").update({"customer_id": new_user_id}).eq("id", payload.ticket_id).execute()

        # 4. Background task para sa welcome email
        background_tasks.add_task(
            send_customer_welcome_email,
            to_email=payload.email,
            first_name=payload.first_name,
            password=payload.password,
            customer_id=new_user_id,
            company_name=payload.company_name or "SwiftFreight Client"
        )

        return {
            "status": "success",
            "message": "Customer portal account created and notification email sent!",
            "user_id": new_user_id
        }

    except HTTPException as http_err:
        # Re-raise explicit HTTP exceptions (e.g. 400 Bad Request)
        raise http_err
    except Exception as e:
        # Catch-all for unexpected database/server errors
        raise HTTPException(status_code=500, detail=str(e))