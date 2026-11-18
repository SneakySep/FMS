from fastapi import APIRouter, HTTPException, status, Depends, Query
from app.schemas.notification_schema import NotificationCreateSchema, NotificationUpdateSchema
from app.service.notification_service import NotificationService
from app.middleware.security import RoleChecker

notification_router = APIRouter(
    prefix="/api/notifications",
    tags=["Notification Hub"]
)

@notification_router.get("/", status_code=status.HTTP_200_OK)
async def get_my_notifications(
    limit: int = Query(20, ge=1, le=100),
    current_user = Depends(RoleChecker(["sales_agent", "manager", "super_admin", "customer"]))
):
    """
    Kunin ang mga notification para sa naka-login na user batay sa kanilang role (RBAC) at ID.
    """
    service = NotificationService()
    
    # Kunin ang role at id mula sa current_user object o dictionary
    user_role = getattr(current_user, "role", None) or current_user.get("role", "customer")
    user_id = getattr(current_user, "id", None) or current_user.get("id")

    notifications = await service.get_notifications_for_user(user_role=user_role, user_id=user_id, limit=limit)

    return {
        "success": True,
        "count": len(notifications),
        "data": notifications
    }

@notification_router.patch("/{notification_id}/read", status_code=status.HTTP_200_OK)
async def mark_notification_read(
    notification_id: str,
    current_user = Depends(RoleChecker(["sales_agent", "manager", "super_admin", "customer"]))
):
    """
    Markahan ang isang partikular na notification bilang nabasa na (is_read = true).
    """
    service = NotificationService()
    updated = await service.mark_as_read(notification_id=notification_id)

    return {
        "success": True,
        "message": "Notification marked as read.",
        "data": updated
    }