from fastapi import APIRouter, Body, Header, HTTPException, Depends, Query
from typing import Optional, List, Dict, Any
from datetime import datetime, timedelta, timezone
from app.supabase_config.supabase import supabase, supabase_secondary

router = APIRouter(
    prefix="/api/v1/portal",
    tags=["Customer Portal"]
)

@router.get("/profile")
def get_profile(x_user_id: Optional[str] = Header(None, alias="x-user-id")):
    # 1. Tiyaking may pumasok na x-user-id header
    if not x_user_id or x_user_id.strip() == "":
        raise HTTPException(
            status_code=400, 
            detail="Header 'x-user-id' is missing or empty."
        )

    try:
        profile = None

        # 2. Priority 1: Query muna sa Secondary Supabase Customer
        try:
            sec_response = supabase_secondary.table("users").select("*").eq("id", x_user_id).execute()
            if sec_response.data and len(sec_response.data) > 0:
                profile = sec_response.data[0]
        except Exception as sec_err:
            print(f"[Secondary DB Check Error]: {str(sec_err)}")

        # 3. Priority 2: Fallback sa Primary Supabase Sale's / Admin
        if not profile:
            try:
                pri_response = supabase.table("profiles").select("*").eq("id", x_user_id).execute()
                if pri_response.data and len(pri_response.data) > 0:
                    profile = pri_response.data[0]
            except Exception as pri_err:
                print(f"[Primary DB Check Error]: {str(pri_err)}")

        # 4. Kapag parehong walang nahanap na record
        if not profile:
            raise HTTPException(
                status_code=404, 
                detail=f"Profile not found for user ID: {x_user_id}"
            )
        
        # 5. Helper field para sa full name
        first_name = profile.get("first_name", "") or ""
        last_name = profile.get("last_name", "") or ""
        profile["full_name"] = f"{first_name} {last_name}".strip()
        
        return profile

    except HTTPException as http_err:
        # I-pasa pabalik ang HTTP errors (400, 404)
        raise http_err
    except Exception as e:
        print(f"[Supabase Error]: {str(e)}")
        raise HTTPException(
            status_code=500, 
            detail=f"Database query failed: {str(e)}"
        )


# ---------------------------------------------------------------------------
# Customer identity resolution helpers
# ---------------------------------------------------------------------------

def _require_user_id(x_user_id: Optional[str]) -> str:
    if not x_user_id or x_user_id.strip() == "":
        raise HTTPException(
            status_code=400,
            detail="Header 'x-user-id' is missing or empty."
        )
    return x_user_id.strip()


def _resolve_customer(x_user_id: str) -> Optional[Dict[str, Any]]:
    """
    I-resolve ang logged-in user id papuntang customers row (Secondary DB).
    Strategy:
      1. Direct match: customers.id == user id
      2. Email match : users.email -> customers.email
    Return None kung walang mahanap (graceful, hindi 404).
    """
    # 1. Direct id match
    try:
        res = (
            supabase_secondary.table("customers")
            .select("*")
            .eq("id", x_user_id)
            .limit(1)
            .execute()
        )
        if res.data:
            return res.data[0]
    except Exception as err:
        print(f"[Resolve Customer - id Error]: {str(err)}")

    # 2. Email match via users table
    try:
        user_res = (
            supabase_secondary.table("users")
            .select("email")
            .eq("id", x_user_id)
            .limit(1)
            .execute()
        )
        email = None
        if user_res.data:
            email = (user_res.data[0].get("email") or "").strip()

        if email:
            cust_res = (
                supabase_secondary.table("customers")
                .select("*")
                .ilike("email", email)
                .limit(1)
                .execute()
            )
            if cust_res.data:
                return cust_res.data[0]
    except Exception as err:
        print(f"[Resolve Customer - email Error]: {str(err)}")

    return None


def _parse_dt(value: Any) -> Optional[datetime]:
    """ISO string -> aware UTC datetime (None kung invalid)."""
    if not value:
        return None
    try:
        dt = datetime.fromisoformat(str(value).replace("Z", "+00:00"))
    except (ValueError, TypeError):
        return None
    if dt.tzinfo is None:
        dt = dt.replace(tzinfo=timezone.utc)
    return dt


def _normalize_status(raw: Any) -> str:
    """Booking/ticket status values -> display label."""
    st = str(raw or "pending").strip().lower()
    mapping = {
        "pending": "Pending",
        "booking": "Pending",
        "quoted": "Quoted",
        "confirmed": "Confirmed",
        "cancelled": "Cancelled",
        "in_transit": "In Transit",
        "in transit": "In Transit",
        "transit": "In Transit",
        "customs": "Customs",
        "delivered": "Delivered",
        "delayed": "Delayed",
        "for account": "For Review",
        "created": "Open",
        "open": "Open",
        "in_progress": "In Progress",
        "resolved": "Resolved",
        "closed": "Closed",
    }
    return mapping.get(st, st.replace("_", " ").title())


def _fetch_customer_bookings(customer_id: str) -> List[Dict[str, Any]]:
    res = (
        supabase_secondary.table("bookings")
        .select("*")
        .eq("customer_id", customer_id)
        .order("created_at", desc=True)
        .limit(200)
        .execute()
    )
    return res.data or []


# ---------------------------------------------------------------------------
# GET /api/v1/portal/shipments - Customer shipment manifest
# ---------------------------------------------------------------------------

@router.get("/shipments")
def get_customer_shipments(
    status_filter: Optional[str] = Query(None, alias="status"),
    search: Optional[str] = Query(None),
    limit: int = Query(25, ge=1, le=100),
    x_user_id: Optional[str] = Header(None, alias="x-user-id"),
):
    user_id = _require_user_id(x_user_id)

    try:
        customer = _resolve_customer(user_id)
        if not customer:
            return {"status": "success", "data": [], "meta": {"total": 0, "customer": None}}

        rows = _fetch_customer_bookings(str(customer.get("id")))

        shipments: List[Dict[str, Any]] = []
        for b in rows:
            pickup_dt = _parse_dt(b.get("pickup_datetime"))
            created_dt = _parse_dt(b.get("created_at")) or datetime.now(timezone.utc)
            label = _normalize_status(b.get("booking_status"))

            shipments.append({
                "id": b.get("id"),
                "booking_code": b.get("booking_code") or f"BK-{str(b.get('id'))[:8].upper()}",
                "service_type": b.get("service_type") or "Freight Shipping",
                "origin": b.get("origin") or "N/A",
                "destination": b.get("destination") or "N/A",
                "status": label,
                "status_raw": str(b.get("booking_status") or "pending").strip().lower(),
                "cargo_details": b.get("cargo_details"),
                "pickup_datetime": pickup_dt.isoformat() if pickup_dt else None,
                "eta": pickup_dt.isoformat() if pickup_dt else None,
                "amount": b.get("agreed_amount") or 0,
                "created_at": created_dt.isoformat(),
            })

        # Optional server-side filters (client din nagfi-filter for instant UX)
        if status_filter and status_filter.strip().lower() not in ("all", ""):
            wanted = _normalize_status(status_filter)
            shipments = [s for s in shipments if s["status"] == wanted]

        if search:
            q = search.strip().lower()
            shipments = [
                s for s in shipments
                if q in " ".join([
                    str(s["booking_code"]), str(s["service_type"]),
                    str(s["origin"]), str(s["destination"]),
                ]).lower()
            ]

        shipments.sort(key=lambda s: s["created_at"], reverse=True)

        return {
            "status": "success",
            "data": shipments[:limit],
            "meta": {
                "total": len(shipments),
                "customer": {
                    "id": customer.get("id"),
                    "company_name": customer.get("company_name"),
                    "tier": customer.get("tier"),
                },
            },
        }

    except HTTPException as http_err:
        raise http_err
    except Exception as e:
        print(f"[Portal Shipments Error]: {str(e)}")
        raise HTTPException(status_code=500, detail=f"Failed to load shipments: {str(e)}")


# ---------------------------------------------------------------------------
# Customer portal settings / profile / password
# ---------------------------------------------------------------------------

# Whitelist of appearance + notification preferences the portal may persist.
# Anything else in the incoming payload is ignored so a rogue client cannot
# stuff arbitrary columns into customer_settings.
SETTINGS_COLUMNS = (
    "dark_mode",
    "accent_color",
    "density",
    "notif_sound",
    "sound_enabled",
    "notify_shipment",
    "notify_sla",
    "notify_invoice",
    "two_factor_enabled",
    "billing_address",
    "default_warehouse",
)

# Sensible defaults returned when a customer has never saved settings yet.
SETTINGS_DEFAULTS: Dict[str, Any] = {
    "dark_mode": False,
    "accent_color": "blue",
    "density": "comfortable",
    "notif_sound": "notification-1.mp3",
    "sound_enabled": True,
    "notify_shipment": True,
    "notify_sla": True,
    "notify_invoice": True,
    "two_factor_enabled": False,
    "billing_address": "",
    "default_warehouse": "Caloocan Hub",
}

ACCENT_CHOICES = ("blue", "violet", "emerald", "amber", "rose")
DENSITY_CHOICES = ("comfortable", "compact")


def _settings_payload(row: Optional[Dict[str, Any]]) -> Dict[str, Any]:
    """Merge a stored customer_settings row over the defaults."""
    merged = dict(SETTINGS_DEFAULTS)
    if row:
        for key in SETTINGS_COLUMNS:
            if row.get(key) is not None:
                merged[key] = row[key]
    return merged


def _sanitize_settings(payload: Dict[str, Any]) -> Dict[str, Any]:
    """Coerce an incoming settings payload into safe, typed column values."""
    clean: Dict[str, Any] = {}

    if "dark_mode" in payload:
        clean["dark_mode"] = bool(payload["dark_mode"])
    if "sound_enabled" in payload:
        clean["sound_enabled"] = bool(payload["sound_enabled"])
    if "notify_shipment" in payload:
        clean["notify_shipment"] = bool(payload["notify_shipment"])
    if "notify_sla" in payload:
        clean["notify_sla"] = bool(payload["notify_sla"])
    if "notify_invoice" in payload:
        clean["notify_invoice"] = bool(payload["notify_invoice"])
    if "two_factor_enabled" in payload:
        clean["two_factor_enabled"] = bool(payload["two_factor_enabled"])

    if "accent_color" in payload:
        accent = str(payload["accent_color"] or "").strip().lower()
        if accent not in ACCENT_CHOICES:
            raise HTTPException(
                status_code=422,
                detail=f"accent_color must be one of: {', '.join(ACCENT_CHOICES)}",
            )
        clean["accent_color"] = accent

    if "density" in payload:
        density = str(payload["density"] or "").strip().lower()
        if density not in DENSITY_CHOICES:
            raise HTTPException(
                status_code=422,
                detail=f"density must be one of: {', '.join(DENSITY_CHOICES)}",
            )
        clean["density"] = density

    # Free-text fields: cap the length, never trust the client.
    for field, limit in (("notif_sound", 120), ("billing_address", 300), ("default_warehouse", 120)):
        if field in payload:
            clean[field] = str(payload[field] or "").strip()[:limit]

    return clean


@router.get("/settings")
def get_portal_settings(x_user_id: Optional[str] = Header(None, alias="x-user-id")):
    """
    Return the saved portal preferences for the logged-in customer.
    Falls back to defaults (status "default") when nothing has been stored yet
    or when the customer_settings table is not yet provisioned.
    """
    user_id = _require_user_id(x_user_id)

    try:
        customer = _resolve_customer(user_id)
        if not customer:
            raise HTTPException(
                status_code=404,
                detail=f"No customer record found for user ID: {user_id}",
            )

        customer_id = str(customer.get("id"))
        res = (
            supabase_secondary.table("customer_settings")
            .select("*")
            .eq("customer_id", customer_id)
            .limit(1)
            .execute()
        )
        row = res.data[0] if res.data else None

        return {
            "status": "success",
            "data": _settings_payload(row),
            "meta": {
                "customer_id": customer_id,
                "saved": bool(row),
            },
        }

    except HTTPException as http_err:
        raise http_err
    except Exception as e:
        # Missing table / column -> serve defaults instead of a hard 500 so the
        # portal stays usable before the migration has been applied.
        print(f"[Portal Settings Read Error]: {str(e)}")
        return {
            "status": "default",
            "data": dict(SETTINGS_DEFAULTS),
            "meta": {"customer_id": None, "saved": False},
        }


@router.put("/settings")
def update_portal_settings(
    payload: Dict[str, Any] = Body(...),
    x_user_id: Optional[str] = Header(None, alias="x-user-id"),
):
    """Upsert the portal preferences for the authenticated customer."""
    user_id = _require_user_id(x_user_id)

    if not isinstance(payload, dict) or not payload:
        raise HTTPException(status_code=422, detail="Settings payload must be a non-empty object.")

    clean = _sanitize_settings(payload)
    if not clean:
        raise HTTPException(status_code=422, detail="No recognised settings fields were supplied.")

    try:
        customer = _resolve_customer(user_id)
        if not customer:
            raise HTTPException(
                status_code=404,
                detail=f"No customer record found for user ID: {user_id}",
            )

        customer_id = str(customer.get("id"))
        # customer_id always comes from the authenticated identity, never from
        # the request body, so one customer cannot write another's row.
        clean["customer_id"] = customer_id

        res = (
            supabase_secondary.table("customer_settings")
            .upsert(clean, on_conflict="customer_id")
            .execute()
        )
        saved = res.data[0] if res.data else None

        return {
            "status": "success",
            "data": _settings_payload(saved),
            "meta": {"customer_id": customer_id, "saved": True},
        }

    except HTTPException as http_err:
        raise http_err
    except Exception as e:
        print(f"[Portal Settings Write Error]: {str(e)}")
        raise HTTPException(
            status_code=500,
            detail=f"Failed to save settings: {str(e)}",
        )


@router.put("/profile")
def update_portal_profile(
    payload: Dict[str, Any] = Body(...),
    x_user_id: Optional[str] = Header(None, alias="x-user-id"),
):
    """
    Update the customer's own profile fields (company name, contact email,
    contact number). Billing address + default warehouse are preference
    fields and live in customer_settings, so they are routed there in the
    same call.
    """
    user_id = _require_user_id(x_user_id)

    if not isinstance(payload, dict) or not payload:
        raise HTTPException(status_code=422, detail="Profile payload must be a non-empty object.")

    # Only these customer columns may be written from the portal.
    allowed_text_fields = {
        "company_name": 160,
        "email": 160,
        "phone_number": 40,
        "contact_person": 120,
    }

    customer_update: Dict[str, Any] = {}
    for field, limit in allowed_text_fields.items():
        if field in payload:
            value = str(payload[field] or "").strip()
            if not value:
                raise HTTPException(status_code=422, detail=f"'{field}' cannot be empty.")
            customer_update[field] = value[:limit]

    settings_update = _sanitize_settings(
        {k: v for k, v in payload.items() if k in SETTINGS_COLUMNS}
    )

    if not customer_update and not settings_update:
        raise HTTPException(status_code=422, detail="No recognised profile fields were supplied.")

    try:
        customer = _resolve_customer(user_id)
        if not customer:
            raise HTTPException(
                status_code=404,
                detail=f"No customer record found for user ID: {user_id}",
            )

        customer_id = str(customer.get("id"))
        updated = dict(customer)

        if customer_update:
            res = (
                supabase_secondary.table("customers")
                .update(customer_update)
                .eq("id", customer_id)
                .execute()
            )
            if res.data:
                updated = {**customer, **res.data[0]}

        if settings_update:
            settings_update["customer_id"] = customer_id
            try:
                supabase_secondary.table("customer_settings").upsert(
                    settings_update, on_conflict="customer_id"
                ).execute()
            except Exception as set_err:
                # Preferences are secondary to the profile write; log and carry on.
                print(f"[Portal Profile Write - Settings Skip]: {str(set_err)}")

        return {
            "status": "success",
            "data": {
                "customer_id": customer_id,
                "company_name": updated.get("company_name"),
                "email": updated.get("email"),
                "phone_number": updated.get("phone_number"),
                "contact_person": updated.get("contact_person"),
                "tier": updated.get("tier"),
                "status": updated.get("status"),
                "full_name": updated.get("contact_person") or "",
            },
        }

    except HTTPException as http_err:
        raise http_err
    except Exception as e:
        print(f"[Portal Profile Write Error]: {str(e)}")
        raise HTTPException(
            status_code=500,
            detail=f"Failed to update profile: {str(e)}",
        )


@router.put("/password")
def update_portal_password(
    payload: Dict[str, Any] = Body(...),
    x_user_id: Optional[str] = Header(None, alias="x-user-id"),
):
    """
    Change the signed-in customer's password through the Supabase Auth admin
    API. Customer auth lives in the Secondary Supabase project, and that
    client is built with the service-role key, so admin updates are allowed.
    """
    user_id = _require_user_id(x_user_id)

    if not isinstance(payload, dict):
        raise HTTPException(status_code=422, detail="Password payload must be an object.")

    new_password = str(payload.get("new_password") or "")
    confirm = str(payload.get("confirm_password") or "")

    if new_password != confirm:
        raise HTTPException(status_code=422, detail="New password and confirmation do not match.")
    if len(new_password) < 8:
        raise HTTPException(status_code=422, detail="Password must be at least 8 characters long.")
    if not any(c.isdigit() for c in new_password):
        raise HTTPException(status_code=422, detail="Password must contain at least one number.")
    if not any(c.isalpha() for c in new_password):
        raise HTTPException(status_code=422, detail="Password must contain at least one letter.")

    try:
        res = supabase_secondary.auth.admin.update_user_by_id(
            user_id,
            {"password": new_password},
        )
    except Exception as e:
        print(f"[Portal Password Change Error]: {str(e)}")
        raise HTTPException(
            status_code=500,
            detail=f"Failed to change password: {str(e)}",
        )

    email = getattr(getattr(res, "user", None), "email", "") or ""

    return {
        "status": "success",
        "message": "Password updated.",
        "data": {"user_id": user_id, "email": email},
    }
