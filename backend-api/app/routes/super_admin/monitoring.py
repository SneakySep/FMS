from fastapi import APIRouter, HTTPException, status, Depends
from app.middleware.security import RoleChecker
from app.service.super_admin.monitoring_service import check_database_health, get_recent_audit_logs

monitoring_router = APIRouter(
    prefix="/api/admin/monitoring",
    tags=["Super Admin Monitoring"]
)

@monitoring_router.get("/database-health")
async def get_db_health(current_user = Depends(RoleChecker(["super_admin"]))):
    """
    Endpoint para sa PHP frontend para i-check ang database status at latency.
    """
    health_status = await check_database_health()
    return {
        "success": True,
        "data": health_status
    }

@monitoring_router.get("/alert-logs")
async def get_alert_logs(limit: int = 50, current_user = Depends(RoleChecker(["super_admin"]))):
    """
    Endpoint para sa PHP frontend para makita ang real-time alert at audit logs.
    """
    logs = await get_recent_audit_logs(limit)
    return {
        "success": True,
        "count": len(logs),
        "logs": logs
    }