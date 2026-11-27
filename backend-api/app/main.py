from fastapi import FastAPI
from fastapi.middleware.cors import CORSMiddleware
from app.routes.auth.auth import auth_router
from app.routes.logistic_api import logistic_api_router

#======== Sales Agent Routes ========#
from app.routes.sales_agent.kanban import kanban_router
from app.routes.sales_agent.leads import leads_router
from app.routes.sales_agent.call_report_route import call_report_router
from app.routes.sales_agent.shipment_router import shipment_router
from app.routes.sales_agent.campaign_router import campaign_router
from app.routes.sales_agent.sla_template_route import sla_template_router

#======== End of Sales Agent Routes ========#
from app.routes.admin.admin import router as admin_router
from app.routes.admin.descriptive import router as admin_descriptive_router
from app.routes.customers.customers import router as customer_router
from app.routes.chat.chat import agent_chat_router
from app.routes.notification_route import notification_router
from app.routes.admin.analytics import router as admin_analytics_router
from app.routes.sales_agent.dashboard_route import dashboard_router
from app.routes.sales_agent.send_docs import router as send_docs_router
from app.routes.sales_agent.inquiry import sales_inquiry_router
from app.routes.sales_agent.sla_route import router as sla_router
#======== Super Admin Routes ========#
from app.routes.super_admin.security import security_management_router
from app.routes.super_admin.audit import router as super_admin_audit_router
from app.routes.super_admin.dashboard import router as super_admin_dashboard_router
from app.routes.super_admin.monitoring import monitoring_router
from app.routes.super_admin.ticket_router import super_admin_ticket_router
from app.routes.super_admin.customer_account_router import customer_account_router
#======== End of Super Admin Routes ========#
from app.routes.customers.dashboard import router as customer_dashboard_router


# 1. I-initialize ang FastAPI app
app = FastAPI(
    title="CRM & Business Control API",
    description="Backend API for Customer Relationship and Business Control System",
    version="1.0.0"
)


# 2. CORS Middleware 
app.add_middleware(
    CORSMiddleware,
    allow_origins=[
        "https://core3.priority-handling.com",
        "http://localhost:8080",
        "http://127.0.0.1:8080"
    ], 
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

# Dito natin lalagay mga routes
app.include_router(auth_router)
app.include_router(logistic_api_router)
app.include_router(leads_router)
app.include_router(admin_router)
app.include_router(customer_router)
app.include_router(agent_chat_router)
app.include_router(notification_router)
app.include_router(call_report_router)
app.include_router(kanban_router)
app.include_router(shipment_router)
app.include_router(sla_router)
app.include_router(sla_template_router)
app.include_router(campaign_router)
app.include_router(sales_inquiry_router)
app.include_router(admin_analytics_router)
app.include_router(admin_descriptive_router)
app.include_router(dashboard_router)
app.include_router(send_docs_router)
app.include_router(security_management_router)
app.include_router(super_admin_audit_router)
app.include_router(super_admin_dashboard_router)
app.include_router(monitoring_router)
app.include_router(super_admin_ticket_router)
app.include_router(customer_account_router)
app.include_router(customer_dashboard_router)
# 3. Simple Root Route 
@app.get("/")
def read_root():
    return {
        "status": "online",
        "message": "Welcome to CRM & Business Control API!",
        "version": "1.0.0"
    }

