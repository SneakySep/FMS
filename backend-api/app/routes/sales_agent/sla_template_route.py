from fastapi import APIRouter, Request, status, Depends, Query
from typing import Optional
from app.schemas.sales_agent.sla_template_schema import SLATemplateCreateSchema, SLATemplateUpdateSchema
from app.service.sales_agent.sla_template_service import SLATemplateService
from app.middleware.security import RoleChecker

sla_template_router = APIRouter(
    prefix="/api/sales/sla-templates",
    tags=["Sales SLA Templates"])

@sla_template_router.post("/create", status_code=status.HTTP_201_CREATED)
async def create_sla_template(
    payload: SLATemplateCreateSchema,
    request: Request,
    current_user = Depends(RoleChecker(["sales_agent", "manager", "super_admin"]))
):
    service = SLATemplateService()
    client_ip = request.client.host if request.client else "unknown"
    user_email = getattr(current_user, "email", None) or current_user.get("email")

    created = await service.create_template(payload.model_dump(), user_email, client_ip)
    return {
        "success": True,
        "message": "SLA Template created successfully!",
        "data": created
    }

@sla_template_router.get("/", status_code=status.HTTP_200_OK)
async def list_sla_template(
    mode: Optional[str] = Query(None, description="Filter by mode"),
    active_only: bool = Query(True, description="Filter by active status"),
    current_user = Depends(RoleChecker(["sales_agent", "manager", "super_admin"]))
):
    service = SLATemplateService()
    templates = await service.get_templates(mode=mode, active_only=active_only)
    return {
        "success": True,
        "data": templates,
        "count": len(templates)
    }

@sla_template_router.put("/{template_id}", status_code=status.HTTP_200_OK)
async def update_sla_template(
    template_id: str,
    payload: SLATemplateUpdateSchema,
    request: Request,
    current_user = Depends(RoleChecker(["sales_agent", "manager", "super_admin"]))
):
    service = SLATemplateService()
    client_ip = request.client.host if request.client else "unknown"
    user_email = getattr(current_user, "email", None) or current_user.get("email")

    updated = await service.update_template(template_id, payload.model_dump(exclude_unset=True), user_email, client_ip)
    return {
        "success": True,
        "message": "SLA Template updated successfully!",
        "data": updated
    }