from fastapi import FastAPI
from fastapi.middleware.cors import CORSMiddleware
from app.routes.auth.auth import auth_router
from app.routes.portal import router as portal_router
from app.routes.sales_agent.leads import router as leads_router
from app.routes.admin.admin import router as admin_router
from app.routes.customers.customers import router as customer_router
from app.routes.customers.chat import router as chat_router

# 1. I-initialize ang FastAPI app
app = FastAPI(
    title="CRM & Business Control API",
    description="Backend API for Customer Relationship and Business Control System",
    version="1.0.0"
)

# 2. CORS Middleware 
app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],  # Sa production, palitan  ito ng mismong URL ng PHP frontend 
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

# Dito natin lalagay mga routes
app.include_router(auth_router)
app.include_router(portal_router)
app.include_router(leads_router)
app.include_router(admin_router)
app.include_router(customer_router)
app.include_router(chat_router)

# 3. Simple Root Route 
@app.get("/")
def read_root():
    return {
        "status": "online",
        "message": "Welcome to CRM & Business Control API!",
        "version": "1.0.0"
    }

