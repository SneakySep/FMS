from app.supabase_config.supabase import supabase_secondary
from datetime import datetime, timezone

async def check_database_health():
    """
     check ang connection at status ng CRM database tables
    """
    try:
        start_time = datetime.now(timezone.utc)
        
        #  mag-query ng isang table (hal. employees limit 1) para masukat ang response time
        res = supabase_secondary.table('employees').select('id', count='exact').limit(1).execute()
        
        end_time = datetime.now(timezone.utc)
        latency_ms = round((end_time - start_time).total_seconds() * 1000, 2)
        
        return {
            "status": "healthy",
            "database": "supabase_secondary (CRM)",
            "latency_ms": latency_ms,
            "connected": True,
            "checked_at": end_time.isoformat()
        }
    except Exception as e:
        return {
            "status": "unhealthy",
            "database": "supabase_secondary (CRM)",
            "error": str(e),
            "connected": False,
            "checked_at": datetime.now(timezone.utc).isoformat()
        }

async def get_recent_audit_logs(limit: int = 50):
    """
    Kinukuha ang real-time alert / audit logs mula sa audit_logs table
    """
    try:
        logs_res = (
            supabase_secondary.table('audit_logs')
            .select('*')
            .order('created_at', desc=True)
            .limit(limit)
            .execute()
        )
        
        return logs_res.data if logs_res and logs_res.data else []
    except Exception as e:
        print(f"Error fetching audit logs: {str(e)}")
        return []