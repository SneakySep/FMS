from app.supabase_config.supabase import supabase_secondary
from datetime import datetime, timezone
import csv
import io

async def get_locked_accounts():
    """
    Kinukuha ang mga listahan ng user na naka-lock out o may multiple failed login attempts.
    """
    try:
        res = (
            supabase_secondary.table('login_restrictions')
            .select('*')
            .gt('failed_attempts', 0)
            .order('updated_at', desc=True)
            .execute()
        )
        return res.data if res and res.data else []
    except Exception as e:
        print(f"Error fetching locked accounts: {str(e)}")
        return []

async def get_suspicious_activities(limit: int = 50):
    """
    Kinukuha ang mga Suspicious action mula sa audit_logs 
    (like LOGIN_FAILED, ACCOUNT_LOCKED, o OTP_VERIFICATION_FAILED).
    """
    try:
        # Pwede i-filter ang mga actions 
        suspicious_actions = [
            "LOGIN_FAILED", 
            "ACCOUNT_LOCKED", 
            "OTP_VERIFICATION_FAILED", 
            "LOGIN_BLOCKED_LOCKED_OUT"
        ]
        
        res = (
            supabase_secondary.table('audit_logs')
            .select('*')
            .in_('action', suspicious_actions)
            .order('created_at', desc=True)
            .limit(limit)
            .execute()
        )
        return res.data if res and res.data else []
    except Exception as e:
        print(f"Error fetching suspicious activities: {str(e)}")
        return []

async def get_filtered_audit_logs(user_email: str = None, action: str = None, start_date: str = None, end_date: str = None, limit: int = 100):
    """
    Kumuha ng audit logs na may filtering base sa User, Action, at Date.
    """
    try:
        query = supabase_secondary.table('audit_logs').select('*')
        
        if user_email:
            query = query.ilike('user_email', f"%{user_email}%")
        if action:
            query = query.eq('action', action)
        if start_date:
            query = query.gte('created_at', start_date)
        if end_date:
            query = query.lte('created_at', end_date)
            
        res = query.order('created_at', desc=True).limit(limit).execute()
        return res.data if res and res.data else []
    except Exception as e:
        print(f"Error filtering audit logs: {str(e)}")
        return []

async def export_audit_logs_csv():
    """
    Ginagawang CSV format ang buong audit logs 
    """
    try:
        res = supabase_secondary.table('audit_logs').select('*').order('created_at', desc=True).limit(1000).execute()
        logs = res.data if res and res.data else []
        
        output = io.StringIO()
        writer = csv.writer(output)
        
        # CSV Header
        writer.writerow(["ID", "User Email", "Action", "IP Address", "Details", "Created At"])
        
        # CSV Rows
        for log in logs:
            writer.writerow([
                log.get("id"),
                log.get("user_email"),
                log.get("action"),
                log.get("ip_address"),
                str(log.get("details")),
                log.get("created_at")
            ])
            
        return output.getvalue()
    except Exception as e:
        print(f"Error exporting audit logs: {str(e)}")
        return ""