from fastapi import APIRouter, status, Depends, Request
from app.schemas.super_admin.customer_account_schema import CreateCustomerAccountSchema
from app.service.super_admin.customer_account_service import create_customer_portal_account_service
from app.middleware.security import RoleChecker

customer_account_router = APIRouter(
    prefix="/api/super-admin/customer-accounts",
    tags=["Super Admin Customer Accounts"]
)

@customer_account_router.post("/create", status_code=status.HTTP_201_CREATED)
async def create_customer_account(
    payload: CreateCustomerAccountSchema,
    request: Request,
    current_user = Depends(RoleChecker(["super_admin"]))
):
    """
    Gumawa ng Supabase Auth account, Customer Profile, magpadala ng email credentials
    """
    client_ip = request.client.host if request.client else None

    result = await create_customer_portal_account_service(
        ticket_id=payload.ticket_id, 
        provided_password=payload.password,
        current_admin_user=current_user,
        client_ip=client_ip
    )
    return result