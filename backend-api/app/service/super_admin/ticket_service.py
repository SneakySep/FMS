from fastapi import HTTPException, status
from app.supabase_config.supabase import supabase_secondary
from datetime import datetime, timezone

async def get_all_customer_tickets_service():
    try:
        # Kunin ang lahat ng tickets mula sa table
        res = supabase_secondary.table("customer_tickets").select("*").order("created_at", desc=True).execute()
        tickets = res.data if res and res.data else []
        
        now = datetime.now(timezone.utc)
        processed_tickets = []
        
        for ticket in tickets:
            pickup_dt_str = ticket.get("pickup_datetime")
            ticket_status = ticket.get("ticket_status")
            
            days_remaining = None
            is_urgent_or_expired = False
            
            if pickup_dt_str:
                # Parse ang pickup datetime at kalkulahin ang natitirang araw
                try:
                    pickup_dt = datetime.fromisoformat(pickup_dt_str.replace("Z", "+00:00"))
                    delta = pickup_dt - now
                    days_remaining = delta.days
                    
                    # Kung ang status ay 'for_account' pa at malapit na o lumampas na sa pickup date (<= 3 araw o negative)
                    if ticket_status == "for_account" and days_remaining <= 3:
                        is_urgent_or_expired = True
                except Exception:
                    pass
            
            # Isama ang computed countdown 
            ticket_info = {
                **ticket,
                "days_remaining": days_remaining,
                "is_urgent": is_urgent_or_expired
            }
            processed_tickets.append(ticket_info)
            
        return processed_tickets
    except Exception as e:
        raise HTTPException(
            status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
            detail=f"Failed to fetch customer tickets: {str(e)}"
        )

async def update_ticket_status_service(ticket_id: str, new_status: str):
    if new_status not in ["for_account", "created", "inactive"]:
        raise HTTPException(
            status_code=status.HTTP_400_BAD_REQUEST,
            detail="Invalid status. Must be 'for_account', 'created', or 'inactive'."
        )
        
    try:
        res = supabase_secondary.table("customer_tickets").update({"ticket_status": new_status}).eq("id", ticket_id).execute()
        if not res.data:
            raise HTTPException(
                status_code=status.HTTP_404_NOT_FOUND,
                detail=f"Ticket with ID {ticket_id} not found."
            )
        return res.data[0]
    except HTTPException as he:
        raise he
    except Exception as e:
        raise HTTPException(
            status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
            detail=f"Failed to update ticket status: {str(e)}"
        )