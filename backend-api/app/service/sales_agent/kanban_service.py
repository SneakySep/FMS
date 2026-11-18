from fastapi import HTTPException, status
from app.supabase_config.supabase import supabase_secondary

async def get_kanban_board_data():
    try:
        # Kunin ang lahat ng inquiries mula sa database
        response = supabase_secondary.table("inquiries").select("*").order("created_at", desc=True).execute()
        leads = response.data if response and response.data else []
        
        # I-group 
        kanban_columns = {
            "new_inquiry": [],
            "qualifying": [],
            "negotiation": [],
            "closed_won": [],
            "closed_lost": []
        }
        
        for lead in leads:
            lead_status = lead.get("status", "new_inquiry")
            # Kung sakaling may lumang status o hindi pamilyar, ilagay sa new_inquiry by default
            if lead_status in kanban_columns:
                kanban_columns[lead_status].append(lead)
            else:
                kanban_columns["new_inquiry"].append(lead)
                
        return kanban_columns
    except Exception as e:
        raise HTTPException(
            status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
            detail=f"Failed to fetch kanban board data: {str(e)}"
        )

async def update_lead_status_service(inquiry_code_or_id: str, new_status: str):
    valid_statuses = ["new_inquiry", "qualifying", "negotiation", "closed_won", "closed_lost"]
    if new_status not in valid_statuses:
        raise HTTPException(
            status_code=status.HTTP_400_BAD_REQUEST,
            detail=f"Invalid status. Must be one of: {', '.join(valid_statuses)}"
        )
        
    try:
        # Alamin kung UUID o Inquiry Code ang ginamit
        query = supabase_secondary.table("inquiries").select("id")
        if "-" in inquiry_code_or_id and len(inquiry_code_or_id) > 20:
            query = query.eq("id", inquiry_code_or_id)
        else:
            query = query.eq("inquiry_code", inquiry_code_or_id)
            
        existing = query.maybe_single().execute()
        if not existing.data:
            raise HTTPException(
                status_code=status.HTTP_404_NOT_FOUND,
                detail=f"Inquiry '{inquiry_code_or_id}' not found."
            )
            
        actual_uuid = existing.data.get("id")
        
        # I-update ang status ng lead
        update_res = (
            supabase_secondary.table("inquiries")
            .update({"status": new_status})
            .eq("id", actual_uuid)
            .execute()
        )
        
        return update_res.data[0] if update_res and update_res.data else None
    except HTTPException as he:
        raise he
    except Exception as e:
        raise HTTPException(
            status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
            detail=f"Failed to update lead status: {str(e)}"
        )