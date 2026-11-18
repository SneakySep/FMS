from typing import Optional
from fastapi import HTTPException, status
from app.supabase_config.supabase import supabase_secondary
from datetime import datetime, timezone

async def get_filtered_inquiries(
    status_filter: Optional[str] = None,
    search: Optional[str] = None,
    service_type: Optional[str] = None,
    limit: int = 50,
    offset: int = 0
):
    try:
        query = supabase_secondary.table("inquiries").select("*")
        
        if status_filter:
            query = query.eq("status", status_filter)
        if service_type:
            query = query.eq("service_type", service_type)
        if search:
            query = query.or_(f"company_name.ilike.%{search}%,contact_person.ilike.%{search}%,inquiry_code.ilike.%{search}%")
            
        response = query.order("created_at", desc=True).range(offset, offset + limit - 1).execute()
        
        return response.data if response and response.data else []
    except Exception as e:
        raise HTTPException(
            status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
            detail=f"Database error while fetching inquiries: {str(e)}"
        )

async def update_inquiry_by_id(inquiry_code_or_id: str, update_data: dict, current_user: dict, client_ip: str):
    try:
        query = supabase_secondary.table("inquiries").select("*")
        
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
        
        # Linisin ang mga None values 
        clean_data = {k: v for k, v in update_data.items() if v is not None}
        
        update_res = supabase_secondary.table("inquiries").update(clean_data).eq("id", actual_uuid).execute()
        
        return update_res.data[0] if update_res and update_res.data else None
    except HTTPException as he:
        raise he
    except Exception as e:
        raise HTTPException(
            status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
            detail=f"Failed to update inquiry: {str(e)}"
        )