from typing import Optional
from fastapi import HTTPException, status
from app.supabase_config.supabase import supabase_secondary

class NotificationService:
    def __init__(self):
        self.supabase = supabase_secondary

    async def create_notification(self, data: dict):
        """
        Gumawa at mag-save ng bagong notification sa Supabase table.
        Automatic magti-trigger ng Supabase Realtime papunta sa frontend.
        """
        try:
            response = self.supabase.table("notifications").insert(data).execute()
            if not response.data:
                raise Exception("Failed to insert notification into database.")
            return response.data[0]
        except Exception as e:
            raise HTTPException(
                status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
                detail=f"Notification service error: {str(e)}"
            )

    async def get_notifications_for_user(self, user_role: str, user_id: str, limit: int = 20):
        """
        Kumukuha ng mga notif batay sa Role (RBAC) at User ID.
        Makukuha nito ang mga notif na para sa pangkalahatang role nila o kaya'y para sa kanila mismo.
        """
        try:
            # Query: Kunin kung ang target_role ay kapareho ng user o kaya naman ay para sa recipient_id nila
            response = (
                self.supabase.table("notifications")
                .select("*")
                .or_(f"target_role.eq.{user_role},recipient_id.eq.{user_id}")
                .order("created_at", desc=True)
                .limit(limit)
                .execute()
            )
            return response.data if response.data else []
        except Exception as e:
            raise HTTPException(
                status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
                detail=f"Failed to fetch notifications: {str(e)}"
            )

    async def mark_as_read(self, notification_id: str):
        """
        I-update ang notification para maging 'is_read = true'
        """
        try:
            response = (
                self.supabase.table("notifications")
                .update({"is_read": True})
                .eq("id", notification_id)
                .execute()
            )
            if not response.data:
                raise HTTPException(status_code=404, detail="Notification not found.")
            return response.data[0]
        except HTTPException as he:
            raise he
        except Exception as e:
            raise HTTPException(
                status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
                detail=f"Failed to update notification: {str(e)}"
            )