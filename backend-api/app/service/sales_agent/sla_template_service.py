from typing import Optional
from fastapi import HTTPException, status
from app.supabase_config.supabase import supabase_secondary
from app.service.audit_service import log_audit

class SLATemplateService:
    def __init__(self):
        self.supabase = supabase_secondary
        self.table = "sla_templates"

    async def create_template(self, payload_dict: dict, user_email: str, client_ip: str):
        try:
            response = self.supabase.table(self.table).insert(payload_dict).execute()
            created = response.data[0] if response and response.data else None
            if not created:
                raise Exception("Failed to create SLA template in the database.")

            await log_audit(
                user_email=user_email,
                action="Created SLA Template",
                ip_address=client_ip,
                details={"template_id": created.get("id"), "template_name": created.get("name")}
            )
        except Exception as e:
            raise HTTPException(
                status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
                detail=f"Failed to create SLA template: {str(e)}"
            )

    async def get_templates(self, mode: Optional[str] = None, active_only: bool = True):
        try:
            query = self.supabase.table(self.table).select("*")
            if mode:
                query = query.eq("mode", mode)
            if active_only:
                query = query.eq("is_active", True)

            response = query.order("name", desc=False).execute()
            return response.data if response and response.data else []

        except Exception as e:
            raise HTTPException(
                status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
                detail=f"Failed to retrieve SLA templates: {str(e)}"
            )

    async def get_template_by_id(self, template_id: str):
        try:
            response = self.supabase.table(self.table).select("*").eq("id", template_id).maybe_single().execute()

            if not response or not response.data:
                raise HTTPException(
                    status_code=status.HTTP_404_NOT_FOUND,
                    detail=f"SLA template with ID {template_id} not found."
                )
            
            return response.data

        except Exception as e:
            raise HTTPException(
                status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
                detail=f"Failed to retrieve SLA template by ID: {str(e)}"
            )

    async def update_template(self, template_id: str, update_data: dict, user_email: str, client_ip: str):
        try:
            clean_data = {k: v for k, v in update_data.items() if v is not None}
            if not clean_data:
                raise HTTPException(
                    status_code=status.HTTP_400_BAD_REQUEST,
                    detail="No valid fields provided for update."
                )

            response = self.supabase.table(self.table).update(clean_data).eq("id", template_id).execute()
            updated = response.data[0] if response and response.data else None
            if not updated:
                raise HTTPException(
                    status_code=status.HTTP_404_NOT_FOUND,
                    detail=f"SLA template with ID {template_id} not found for update."
                )

            await log_audit(
                user_email=user_email,
                action="Updated SLA Template",
                ip_address=client_ip,
                details={"template_id": template_id, "updated_fields": list(clean_data.keys())}
            )

            return updated

        except Exception as e:
            raise HTTPException(
                status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
                detail=f"Failed to update SLA template: {str(e)}"
            )