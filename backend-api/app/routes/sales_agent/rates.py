from fastapi import APIRouter, HTTPException, Query
from typing import Optional, List
from pydantic import BaseModel
import json
from pathlib import Path

router = APIRouter(
    prefix="/api/v1/sales-agent/rates",
    tags=["Sales Agent - Rate Search"]
)

# Path papunta sa app/data/rate.json
DATA_FILE_PATH = Path(__file__).resolve().parent.parent.parent / "data" / "rate.json"


def load_rates_data() -> list:
    """Helper function para basahin ang rate.json file"""
    if not DATA_FILE_PATH.exists():
        raise HTTPException(status_code=500, detail="rate.json file not found in app/data/")
    try:
        with open(DATA_FILE_PATH, "r", encoding="utf-8") as f:
            return json.load(f)
    except Exception as e:
        raise HTTPException(status_code=500, detail=f"Error reading rate.json: {str(e)}")


@router.get("")  # Inalis ang slash para maiwasan ang 307 Redirect
def search_rates(
    mode: Optional[str] = Query(None, description="AIR, SEA, o LAND"),
    type: Optional[str] = Query(None, description="Local o International"),
    delivery_option: Optional[str] = Query(None, description="Door-to-Door o Port-to-Port"),
    transit_time: Optional[str] = Query(None, description="Filtering (e.g. Same Day)"),
    origin: Optional[str] = Query(None, description="Filter sa origin (case-insensitive substring)"),
    destination: Optional[str] = Query(None, description="Filter sa destination (case-insensitive substring)")
):
    rates = load_rates_data()
    filtered_rates = []

    for item in rates:
        if mode and item.get("mode", "").strip().upper() != mode.strip().upper():
            continue
        if type and item.get("type", "").strip().lower() != type.strip().lower():
            continue
        if delivery_option and item.get("delivery_option", "").strip().lower() != delivery_option.strip().lower():
            continue
        if transit_time and transit_time.strip().lower() not in item.get("transit_time", "").strip().lower():
            continue
        if origin and origin.strip().lower() not in item.get("origin", "").strip().lower():
            continue
        if destination and destination.strip().lower() not in item.get("destination", "").strip().lower():
            continue

        filtered_rates.append(item)

    return {
        "status": "success",
        "count": len(filtered_rates),
        "data": filtered_rates
    }


class RateCalculationRequest(BaseModel):
    rate_id: str
    weight_kg: Optional[float] = 0.0
    cbm: Optional[float] = 0.0


@router.post("/calculate")
def calculate_quotation(payload: RateCalculationRequest):
    """
    Kukuha ng rate ID at ikokompyut ang kabuuang quotation breakdown.
    """
    rates = load_rates_data()
    rate = next((r for r in rates if r["id"] == payload.rate_id), None)

    if not rate:
        raise HTTPException(status_code=404, detail=f"Rate ID '{payload.rate_id}' not found")

    # Base freight computation
    base_freight_cost = 0.0
    if "base_rate_per_kg" in rate:
        weight = max(payload.weight_kg or 0, rate.get("min_weight_kg", 1))
        base_freight_cost = weight * rate["base_rate_per_kg"]
    elif "base_rate_per_cbm" in rate:
        volume = max(payload.cbm or 0, rate.get("min_cbm", 1))
        base_freight_cost = volume * rate["base_rate_per_cbm"]
    elif "base_rate_flat" in rate:
        base_freight_cost = float(rate["base_rate_flat"])

    # Additional fees setup (Kapag Port-to-Port, dapat 0 ang pickup at delivery)
    pickup_fee = rate.get("trucking_pickup_fee", 0.0) if rate.get("delivery_option") == "Door-to-Door" else 0.0
    delivery_fee = rate.get("trucking_delivery_fee", 0.0) if rate.get("delivery_option") == "Door-to-Door" else 0.0
    doc_fee = rate.get("documentation_fee", 0.0)
    handling_fee = rate.get("handling_fee", 0.0)

    total_amount = base_freight_cost + pickup_fee + delivery_fee + doc_fee + handling_fee

    return {
        "status": "success",
        "rate_id": rate["id"],
        "summary": {
            "mode": rate["mode"],
            "route": f"{rate['origin']} ➔ {rate['destination']}",
            "delivery_option": rate["delivery_option"],
            "transit_time": rate["transit_time"],
            "carrier": rate["carrier"]
        },
        "breakdown": {
            "base_freight": round(base_freight_cost, 2),
            "trucking_pickup": round(pickup_fee, 2),
            "trucking_delivery": round(delivery_fee, 2),
            "documentation_fee": round(doc_fee, 2),
            "handling_fee": round(handling_fee, 2)
        },
        "total_quotation": round(total_amount, 2),
        "currency": rate.get("currency", "PHP"),
        "valid_until": rate.get("valid_until")
    }