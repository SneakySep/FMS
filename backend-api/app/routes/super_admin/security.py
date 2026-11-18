
from fastapi import APIRouter, HTTPException, status, Depends, Query
from fastapi.responses import Response
from app.middleware.security import RoleChecker
from app.service.super_admin.security_service import (
    get_locked_accounts, 
    get_suspicious_activities, 
    get_filtered_audit_logs,
    export_audit_logs_csv
)

security_management_router = APIRouter(
    prefix="/api/admin/security",
    tags=["Super Admin Security Management"]
)

@security_management_router.get("/failed-logins")
async def view_failed_logins(current_user = Depends(RoleChecker(["super_admin"]))):
    """
    Endpoint para makita ang mga multiple failed login attempts at naka-lock out na accounts.
    """
    accounts = await get_locked_accounts()
    return {
        "success": True,
        "count": len(accounts),
        "data": accounts
    }

@security_management_router.get("/suspicious-activities")
async def view_suspicious_activities(limit: int = 50, current_user = Depends(RoleChecker(["super_admin"]))):
    """
    Endpoint para sa real-time suspicious activities at security audit logs.
    """
    activities = await get_suspicious_activities(limit)
    return {
        "success": True,
        "count": len(activities),
        "data": activities
    }

@security_management_router.get("/audit-logs/filter")
async def filter_audit_logs(
    user_email: str = Query(None, description="Filter by user email"),
    action: str = Query(None, description="Filter by action type"),
    start_date: str = Query(None, description="Filter start date (ISO format)"),
    end_date: str = Query(None, description="Filter end date (ISO format)"),
    limit: int = 100,
    current_user = Depends(RoleChecker(["super_admin"]))
):
    """
    Endpoint para sa: Filter logs by User, Action, Date, Status.
    """
    logs = await get_filtered_audit_logs(user_email, action, start_date, end_date, limit)
    return {
        "success": True,
        "count": len(logs),
        "data": logs
    }

@security_management_router.get("/audit-logs/export-csv")
async def export_audit_logs_csv_endpoint(current_user = Depends(RoleChecker(["super_admin"]))):
    """
    Endpoint para sa: Export audit log in CSV.
    """
    csv_data = await export_audit_logs_csv()
    if not csv_data:
        raise HTTPException(status_code=500, detail="Failed to generate CSV export.")
        
    return Response(
        content=csv_data,
        media_type="text/csv",
        headers={"Content-Disposition": "attachment; filename=audit_logs_export.csv"}
    )