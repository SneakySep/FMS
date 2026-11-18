from app.supabase_config.supabase import supabase_secondary
from app.service.audit_service import log_audit
from app.service.notification_service import NotificationService  # <-- I-import ang NotificationService
from datetime import datetime, timezone

async def create_manual_inquiry(payload_dict: dict, current_user, client_ip: str):
    try:
        # 1. Kunin ang details ng nag-a-add na sales agent mula sa employees table gamit ang email ng current user
        emp_res = (
            supabase_secondary.table('employees')
            .select('employee_id, first_name, last_name, email')
            .eq('email', current_user.email)
            .maybe_single()
            .execute()
        )
        
        emp_data = emp_res.data if emp_res and emp_res.data else None
        
        agent_id = emp_data.get("employee_id") if emp_data else "EMP-UNKNOWN"
        agent_name = f"{emp_data.get('first_name', '')} {emp_data.get('last_name', '')}".strip() if emp_data else "Sales Agent"
        agent_email = current_user.email

        # 2. I-set ang default values 
        payload_dict["status"] = "new_inquiry"
        payload_dict["platform_used"] = "manual_entry"
        payload_dict["assigned_agent_id"] = agent_id
        payload_dict["assigned_agent_name"] = agent_name
        payload_dict["assigned_agent_email"] = agent_email
        payload_dict["created_at"] = datetime.now(timezone.utc).isoformat()

        # Kung may pickup_datetime, gawing string kung hindi pa naka-string
        if payload_dict.get("pickup_datetime"):
            if isinstance(payload_dict["pickup_datetime"], datetime):
                payload_dict["pickup_datetime"] = payload_dict["pickup_datetime"].isoformat()

        # 3. I-insert sa inquiries table
        insert_res = (
            supabase_secondary.table('inquiries')
            .insert(payload_dict)
            .execute()
        )

        inserted_data = insert_res.data[0] if insert_res and insert_res.data else None

        if not inserted_data:
            raise Exception("Failed to insert manual inquiry into database.")

        # 4. Mag-log ng audit para kay sales agent
        await log_audit(
            user_email=agent_email,
            action="MANUAL_INQUIRY_CREATED",
            ip_address=client_ip,
            details={
                "inquiry_code": inserted_data.get("inquiry_code"),
                "company_name": inserted_data.get("company_name"),
                "assigned_agent": agent_id
            }
        )

        # =========================================================================
        # 5. REAL-TIME NOTIFICATION SA MGA MANAGER AT SUPER ADMIN
        # =========================================================================
        try:
            notif_service = NotificationService()
            company_name = inserted_data.get("company_name", "Unknown Company")
            inquiry_code = inserted_data.get("inquiry_code", "")

            # Magpapadala tayo para sa manager 
            await notif_service.create_notification({
                "target_role": "manager",
                "title": "New Manual Inquiry / Lead",
                "message": f"Sales agent: {agent_name} added a new inquiry for {company_name} (Code: {inquiry_code}).",
                "type": "new_inquiry",
                "reference_id": inserted_data.get("id")
            })
        except Exception as notif_error:
            # Hindi natin hahayaang masira ang pag-create ng inquiry kapag nagka-issue ang notification
            print(f"Warning: Failed to dispatch manager notification: {str(notif_error)}")

        return inserted_data

    except Exception as e:
        print(f"Error in create_manual_inquiry: {str(e)}")
        raise e