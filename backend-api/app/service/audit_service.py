from app.supabase_config.supabase import supabase_secondary
from datetime import datetime, timezone

async def log_audit(user_email: str, action: str, ip_address: str = None, details: dict = None):
   
    try:
        audit_payload = {
            "user_email": user_email,
            "action": action,
            "ip_address": ip_address,
            "details": details or {},
            "created_at": datetime.now(timezone.utc).isoformat()
        }

       
        response = supabase_secondary.table('audit_logs').insert(audit_payload).execute()
        return response
        
    except Exception as e:
        print(f"CRITICAL ERROR: Failed to save audit log -> {str(e)}")
        return None