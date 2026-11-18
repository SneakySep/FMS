from fastapi import APIRouter, HTTPException, status, Header, Depends
from app.supabase_config.supabase import supabase_secondary, LOGISTIC_API_KEY
from typing import Optional

logistic_api_router = APIRouter(
    prefix="/api/logistic",
    tags=["Logistic System Integration"]
)

async def verify_logistic_api_key(x_api_key: Optional[str] = Header(None)):
    if not x_api_key or x_api_key != LOGISTIC_API_KEY:
        raise HTTPException(
            status_code=status.HTTP_401_UNAUTHORIZED,
            detail="Invalid or missing Logistic API Key."
        )
    return x_api_key

@logistic_api_router.get("/closed-won-shipments", status_code=status.HTTP_200_OK)
async def get_closed_won_for_logistics(api_key: str = Depends(verify_logistic_api_key)):
    try:
        # 1. Kunin ang lahat ng inquiries na closed_won
        inquiries_res = (
            supabase_secondary.table("inquiries")
            .select("*")
            .eq("status", "closed_won")
            .execute()
        )
        closed_inquiries = inquiries_res.data if inquiries_res and inquiries_res.data else []
        
        logistic_payload = []
        for inquiry in closed_inquiries:
            inquiry_id = inquiry.get("id")
            
            # 2. Kunin ang shipment data kung meron man
            shipment_res = (
                supabase_secondary.table("shipments")
                .select("*")
                .eq("inquiry_id", inquiry_id)
                .maybe_single()
                .execute()
            )
            shipment_data = shipment_res.data if shipment_res and shipment_res.data else {}
            
            # 3. Kunin ang SLA Contract details kung ang contract_type ay 'sla'
            sla_contract_data = None
            contract_type = inquiry.get("contract_type", "standard_tc")
            
            if contract_type == "sla":
                sla_res = (
                    supabase_secondary.table("sla_contracts")
                    .select("*")
                    .eq("inquiry_id", inquiry_id)
                    .eq("status", "active") 
                    .maybe_single()
                    .execute()
                )
                sla_contract_data = sla_res.data if sla_res and sla_res.data else None

            # 4. Buuin ang combined record para sa logistics payload
            combined_record = {
                "inquiry_code": inquiry.get("inquiry_code"),
                "company_name": inquiry.get("company_name"),
                "contact_person": inquiry.get("contact_person"),
                "phone_number": inquiry.get("phone_number"),
                "email": inquiry.get("email"),
                "cargo_details": inquiry.get("cargo_details"),
                "pickup_address": inquiry.get("pickup_address"),
                "pickup_datetime": inquiry.get("pickup_datetime"),
                
                # Consignee Details
                "consignment_note_number": shipment_data.get("consignment_note_number"),
                "consignee_company_name": shipment_data.get("consignee_company_name"),
                "consignee_contact_person": shipment_data.get("consignee_contact_person"),
                "consignee_address": shipment_data.get("consignee_address"),
                "consignee_telephone": shipment_data.get("consignee_telephone"),
                "package_type": shipment_data.get("package_type"),
                "parcel_size": shipment_data.get("parcel_size"),
                "dimensions_cms": shipment_data.get("dimensions_cms"),
                "chargeable_weight": shipment_data.get("chargeable_weight"),
                "transport_mode": shipment_data.get("transport_mode"),
                "delivery_speed": shipment_data.get("delivery_speed"),
                "handling_terms": shipment_data.get("handling_terms"),
                "notes": shipment_data.get("notes"),

                # 5. SLA Contract Information Integration
                "contract_type": contract_type,
                "sla_reason": inquiry.get("sla_reason"),
                "has_sla": bool(sla_contract_data),
                "sla_details": {
                    "contract_no": sla_contract_data.get("contract_no") if sla_contract_data else None,
                    "transit_days": sla_contract_data.get("transit_days") if sla_contract_data else None,
                    "pickup_window_hours": sla_contract_data.get("pickup_window_hours") if sla_contract_data else None,
                    "ontime_target_pct": sla_contract_data.get("ontime_target_pct") if sla_contract_data else None,
                    "tracking_interval_hours": sla_contract_data.get("tracking_interval_hours") if sla_contract_data else None,
                    "claims_response_hours": sla_contract_data.get("claims_response_hours") if sla_contract_data else None,
                    "late_credit_pct_per_day": sla_contract_data.get("late_credit_pct_per_day") if sla_contract_data else None,
                    "credit_cap_pct": sla_contract_data.get("credit_cap_pct") if sla_contract_data else None,
                    "liability_limit_pct": sla_contract_data.get("liability_limit_pct") if sla_contract_data else None,
                    "rate": sla_contract_data.get("rate") if sla_contract_data else None,
                    "currency": sla_contract_data.get("currency") if sla_contract_data else None,
                    "pdf_path": sla_contract_data.get("pdf_path") if sla_contract_data else None
                } if sla_contract_data else None
            }
            
            logistic_payload.append(combined_record)
            
        return {
            "success": True,
            "count": len(logistic_payload),
            "data": logistic_payload
        }
        
    except Exception as e:
        raise HTTPException(
            status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
            detail=f"Failed to fetch data for logistics: {str(e)}"
        )