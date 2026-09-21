from app.supabase_config.supabase import supabase_secondary
from typing import Dict, Any

def get_kanban_board_data() -> Dict[str, Any]:
    res = supabase_secondary.table("inquiries").select("*").execute()
    inquiries = res.data if res.data else []

    columns_config = [
        {"title": "New Inquiry", "key": "new_inquiry"},
        {"title": "Qualifying", "key": "qualifying"},
        {"title": "Negotiation", "key": "negotiation"},
        {"title": "Quote Sent", "key": "quote_sent"},
        {"title": "Closed Won", "key": "closed_won"},
        {"title": "Closed Lost", "key": "closed_lost"},
    ]

    grouped_columns = {}

    for col in columns_config:
        status_key = col["key"]
        
        filtered_items = [
            {
                "id": str(item.get("id")),
                "inquiry_code": item.get("inquiry_code") or "N/A",
                "company_name": item.get("company_name") or "Unnamed Company",
                "contact_person": item.get("contact_person") or "No contact person",
                "email": item.get("email") or "No email provided",
                "phone_number": item.get("phone_number") or "No phone provided",
                "pickup_address": item.get("pickup_address") or "No pickup address set",
                "cargo_details": item.get("cargo_details") or item.get("initial_inquiry_text") or "No details provided",
                "service_type": item.get("service_type") or "General",
                "estimated_amount": float(item.get("estimated_amount")) if item.get("estimated_amount") is not None else 0.0,
                "created_at": item.get("created_at")
            }
            for item in inquiries if item.get("status") == status_key
        ]

        grouped_columns[status_key] = {
            "title": col["title"],
            "status_key": status_key,
            "count": len(filtered_items),
            "items": filtered_items
        }

    return {"columns": grouped_columns}


def update_card_status(card_id: str, new_status: str) -> bool:
    res = supabase_secondary.table("inquiries").update({"status": new_status}).eq("id", card_id).execute()
    return bool(res.data)