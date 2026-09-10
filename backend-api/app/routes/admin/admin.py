from fastapi import APIRouter, HTTPException, BackgroundTasks
from typing import Optional, List
import secrets
from app.supabase_config.supabase import supabase, supabase_secondary
from app.schemas.admin import (
    CloseWonTicketResponseSchema,
    CreateCustomerFromTicketSchema,
    CustomerUserResponse,
    AdminCreateCustomerSchema,
    CustomerStatusUpdateSchema,
    AdminDeleteCustomerSchema,
)
from app.service.email_service import send_customer_welcome_email

router = APIRouter(
    prefix="/api/v1/admin",
    tags=["Admin Management"]
)

# HELPER: Para sa Tier computation ng Customers table
def calculate_tier(bookings_count: int) -> str:
    if bookings_count >= 20:
        return "PLATINUM"
    elif bookings_count >= 10:
        return "GOLD"
    elif bookings_count >= 5:
        return "SILVER"
    return "BRONZE"


# 1. Kunin ang mga close won tickets na "for account" pa lang ang status
@router.get("/close-won-tickets", response_model=list[CloseWonTicketResponseSchema])
async def get_close_won_tickets():
    try:
        # Kukuha lang ng tickets 
        res = (
            supabase_secondary.table("tickets")
            .select("*")
            .eq("ticket_status", "for account")
            .execute()
        )
        tickets = res.data or []

        return tickets
    except Exception as e:
        raise HTTPException(status_code=500, detail=str(e))


# 2. Create customer portal from close won ticket
@router.post("/create-customer-from-ticket")
async def create_customer_from_ticket(
    payload: CreateCustomerFromTicketSchema, 
    background_tasks: BackgroundTasks
):
    try:
        new_user_id = None

        # --- DAGDAG: Check muna sa public.users kung registered na ---
        existing_user = (
            supabase_secondary.table("users")
            .select("id")
            .eq("email", payload.email)
            .execute()
        )

        if existing_user.data:
            # Gamitin ang umiiral na user ID kung gawan uli ng panibagong ticket ang customer
            new_user_id = existing_user.data[0]["id"]
        else:
            # 1. Create Auth account sa Secondary supabase (Orihinal mong code)
            try:
                auth_res = supabase_secondary.auth.admin.create_user({
                    "email": payload.email,
                    "password": payload.password,
                    "email_confirm": True,
                    "user_metadata": {
                        "first_name": payload.first_name,
                        "last_name": payload.last_name,
                        "role": "customer"
                    }
                })

                if not auth_res.user:
                    raise HTTPException(status_code=400, detail="Failed to create Auth account.")

                new_user_id = auth_res.user.id
            except Exception as auth_err:
                raise HTTPException(status_code=400, detail=str(auth_err))

            # 2. Mag-insert sa public.users table (Secondary DB) (Orihinal mong code)
            user_data = {
                "id": new_user_id,
                "email": payload.email,
                "first_name": payload.first_name,
                "last_name": payload.last_name,
                "company_name": payload.company_name,
                "phone_number": payload.phone_number,
                "role": "customer"
            }

            supabase_secondary.table("users").insert(user_data).execute()

        # --- DAGDAG: Insert o Update sa public.customers Table ---
        existing_cust = (
            supabase_secondary.table("customers")
            .select("*")
            .eq("email", payload.email)
            .execute()
        )

        full_contact_name = f"{payload.first_name} {payload.last_name}".strip()

        if existing_cust.data:
            # Kapag nagawan uli ng panibagong ticket, increment total_bookings at compute new tier
            c_rec = existing_cust.data[0]
            new_bookings = (c_rec.get("total_bookings") or 0) + 1
            new_tier = calculate_tier(new_bookings)

            supabase_secondary.table("customers").update({
                "company_name": payload.company_name or c_rec["company_name"],
                "contact_person": full_contact_name or c_rec["contact_person"],
                "phone_number": payload.phone_number or c_rec["phone_number"],
                "total_bookings": new_bookings,
                "tier": new_tier
            }).eq("id", c_rec["id"]).execute()
        else:
            # Kapag kauna-unahang beses gawan ng account
            new_cust_data = {
                "company_name": payload.company_name,
                "contact_person": full_contact_name,
                "email": payload.email,
                "phone_number": payload.phone_number,
                "total_bookings": 1,
                "tier": "BRONZE",
                "created_by_ticket_id": payload.ticket_id
            }
            supabase_secondary.table("customers").insert(new_cust_data).execute()

        # 3. I-update ang ticket: I-set ang customer_id AT baguhin ang ticket_status -> 'created' (Orihinal mong code)
        supabase_secondary.table("tickets").update({
            "customer_id": new_user_id,
            "ticket_status": "created"
        }).eq("id", payload.ticket_id).execute()

        # 4. Background task para sa welcome email (Orihinal mong code)
        background_tasks.add_task(
            send_customer_welcome_email,
            to_email=payload.email,
            first_name=payload.first_name,
            password=payload.password,
            customer_id=new_user_id,
            company_name=payload.company_name or "SwiftFreight Client"
        )

        return {
            "status": "success",
            "message": "Customer portal account created and notification email sent!",
            "user_id": new_user_id
        }

    except HTTPException as http_err:
        raise http_err
    except Exception as e:
        raise HTTPException(status_code=500, detail=str(e))


# -----------------------------------------------------------------------------
# 2b. Create a customer portal account directly from the admin "Add New
#     Customer" form (frontend/src/views/admin/customers.php).
#
#     Unlike /create-customer-from-ticket this has NO ticket dependency: no
#     ticket_id, no ticket_status flip, and total_bookings starts at 0 since
#     the customer has not booked anything yet.
# -----------------------------------------------------------------------------

# Graceful degradation: the customer_type column only exists after
# backend-api/database/customer_type.sql has been run on the SECONDARY Supabase
# project. If it is missing, Postgres raises "column ... does not exist" and we
# retry the insert without that key so the flow keeps working.
def _is_missing_column_error(err: Exception, column: str) -> bool:
    """True kapag ang error ay 'dito ang columns ay wala pa sa table'."""
    msg = str(err).lower()
    if column.lower() not in msg:
        return False
    return (
        "does not exist" in msg
        or "could not find" in msg
        or "42703" in msg
        or "pgrst20" in msg
    )


def _has_unknown_optional_column(err: Exception) -> bool:
    """True kapag may optional na column (address/tax_id/website/contact_email) na wala pa."""
    msg = str(err)
    return any(_is_missing_column_error(err, col) for col in
               ("address", "tax_id", "website", "contact_email")) or (
        "column" in msg.lower() and "does not exist" in msg.lower()
    )


def _insert_customer_row(table_name: str, row: dict) -> dict:
    """Insert into the secondary DB, retrying without customer_type if needed."""
    try:
        res = supabase_secondary.table(table_name).insert(row).execute()
    except Exception as ins_err:
        if "customer_type" in row and _is_missing_column_error(ins_err, "customer_type"):
            print(f"DEBUG: {table_name}.customer_type column missing - "
                  f"run backend-api/database/customer_type.sql. Retrying without it.")
            fallback = {k: v for k, v in row.items() if k != "customer_type"}
            res = supabase_secondary.table(table_name).insert(fallback).execute()
        else:
            raise
    return (res.data or [{}])[0]


@router.post("/create-customer", status_code=201)
async def create_customer(
    payload: AdminCreateCustomerSchema,
    background_tasks: BackgroundTasks
):
    email = str(payload.email).lower().strip()

    try:
        # --- 1. Bawal ang duplicated na email -------------------------------
        existing_user = (
            supabase_secondary.table("users")
            .select("id")
            .eq("email", email)
            .execute()
        )
        if existing_user.data:
            raise HTTPException(
                status_code=409,
                detail="A customer account with this email already exists."
            )

        # --- 2. Gumawa ng Auth account sa Secondary Supabase ----------------
        try:
            auth_res = supabase_secondary.auth.admin.create_user({
                "email": email,
                "password": payload.password,
                "email_confirm": True,
                "user_metadata": {
                    "first_name": payload.first_name,
                    "last_name": payload.last_name,
                    "role": "customer",
                    "customer_type": payload.account_type
                }
            })
        except Exception as auth_err:
            print(f"DEBUG: create-customer auth failed: {auth_err}")
            raise HTTPException(
                status_code=409,
                detail="Could not create the login account - the email may already be registered."
            )

        if not auth_res.user:
            raise HTTPException(status_code=400, detail="Failed to create Auth account.")

        new_user_id = auth_res.user.id
        contact_person = (payload.contact_person or "").strip() or \
            f"{payload.first_name} {payload.last_name}".strip()

        # --- 3. I-insert ang row sa public.users ----------------------------
        _insert_customer_row("users", {
            "id": new_user_id,
            "email": email,
            "first_name": payload.first_name,
            "last_name": payload.last_name,
            "company_name": payload.company_name,
            "phone_number": payload.phone_number,
            "customer_type": payload.account_type,
            "role": "customer"
        })

        # --- 4. I-insert ang row sa public.customers ------------------------
        customers_row = {
            "email": email,
            "company_name": payload.company_name,
            "contact_person": contact_person,
            "contact_email": email,
            "phone_number": payload.phone_number,
            "address": payload.address,
            "tax_id": payload.tax_id,
            "website": payload.website,
            "customer_type": payload.account_type,
            "total_bookings": 0,
            "tier": calculate_tier(0)
        }

        try:
            _insert_customer_row("customers", customers_row)
        except Exception as cust_err:
            # Ang optional na B2B columns ay pwedeng wala pa - i-strip at
            # subukan ulit ang insert gamit ang core columns lamang.
            if _has_unknown_optional_column(cust_err):
                print(f"DEBUG: customers optional column missing, inserting minimal row: {cust_err}")
                minimal = {
                    "email": email,
                    "company_name": payload.company_name,
                    "contact_person": contact_person,
                    "phone_number": payload.phone_number,
                    "total_bookings": 0,
                    "tier": calculate_tier(0)
                }
                _insert_customer_row("customers", {**minimal, "customer_type": payload.account_type})
            else:
                raise

        # --- 5. Welcome email (hindi ma-fail ang request kung mag-fail ang SMTP)
        background_tasks.add_task(
            send_customer_welcome_email,
            to_email=email,
            first_name=payload.first_name,
            password=payload.password,
            customer_id=new_user_id,
            company_name=payload.company_name or "SwiftFreight Client"
        )

        return {
            "status": "success",
            "message": "Customer account created successfully.",
            "user_id": new_user_id,
            "full_name": f"{payload.first_name} {payload.last_name}".strip(),
            "customer_type": payload.account_type
        }

    except HTTPException as http_err:
        raise http_err
    except Exception as e:
        print(f"DEBUG: create-customer failed: {e}")
        raise HTTPException(status_code=500, detail=str(e))


@router.get("/customer-accounts", response_model=dict)
async def get_customer_accounts():
    try:
        # Kukunin ang lahat ng rows sa public.users table (Orihinal mong code)
        res = supabase_secondary.table("users").select("*").execute()
        return {
            "status": "success", 
            "data": res.data or []
        }
    except Exception as e:
        raise HTTPException(status_code=500, detail=str(e))


# -----------------------------------------------------------------------------
# 2c. Customer Management ACTIONS - status change + privacy-gated hard delete
#
#     UI: frontend/src/views/admin/customers.php  (Actions menu, Manage
#         Customer modal, Delete Customer modal) + assets/js/admin/customers.js
#
#     Status model (backend-api/database/customer_status.sql):
#         Active      -> normal portal access
#         Inactive    -> parked; data kept, sign-in still works, no auth ban
#         Deactivated -> permanent auth ban (portal lock-out); data kept
#
#     Delete model (two-step, deliberate):
#         Hard deletion is only allowed while the row is 'Deactivated', and it
#         requires an explicit Privacy Policy acknowledgement + audit reason.
# -----------------------------------------------------------------------------

# Ang status column ay nag-exist lamang pagkatapos i-run ang
# backend-api/database/customer_status.sql sa SECONDARY Supabase project.
# Kapag wala pa, i-skip natin ang status write (at i-block ang hard delete)
# sa halip na bumagsak ang buong flow - parehong approach ng customer_type.
STATUS_COLUMN_AVAILABLE = True


def _resolve_customer_row(user_id: str) -> dict:
    """Kunin ang public.users row ng customer; 404 kung hindi umiiral."""
    res = (
        supabase_secondary.table("users")
        .select("*")
        .eq("id", user_id)
        .execute()
    )
    rows = res.data or []
    if not rows:
        raise HTTPException(
            status_code=404,
            detail="Customer account not found. Refresh the list and try again."
        )
    return rows[0]


def _update_status_column(table_name: str, column: str, value: str, status: str) -> bool:
    """
    I-update ang status ng isang row.

    Bumabalik ng True kung nasulat ang status, False kung wala pa ang status
    column (graceful degradation). Iba pang error ay ipapasa.
    """
    try:
        supabase_secondary.table(table_name).update({"status": status}) \
            .eq(column, value).execute()
        return True
    except Exception as upd_err:
        if _is_missing_column_error(upd_err, "status"):
            print(f"DEBUG: {table_name}.status column missing - "
                  f"run backend-api/database/customer_status.sql. Skipping status write.")
            return False
        raise


def _apply_auth_ban(user_id: str, status: str) -> str:
    """
    I-align ang Supabase Auth access sa bagong status.

    Deactivated -> permanent ban; Active/Inactive -> i-unban. Hindi pinapalbag
    ang request kung mag-fail ang auth call - ang data-layer status ang source
    of truth, at nag-log lang ng DEBUG line.
    """
    ban_duration = "permanent" if status == "Deactivated" else "none"
    try:
        supabase_secondary.auth.admin.update_user_by_id(
            user_id, {"ban_duration": ban_duration}
        )
        return "ok"
    except Exception as ban_err:
        print(f"DEBUG: auth ban update failed for {user_id} ({ban_duration}): {ban_err}")
        return "failed"


@router.patch("/customer-accounts/status")
async def update_customer_status(payload: CustomerStatusUpdateSchema):
    try:
        row = _resolve_customer_row(payload.user_id)
        email = str(row.get("email") or "").lower().strip()

        # --- 1. I-update ang users row (ang binabasa ng admin list) ----------
        users_updated = _update_status_column("users", "id", payload.user_id, payload.status)

        # --- 2. I-mirror sa customers row kung may email ---------------------
        customers_updated = False
        if email:
            customers_updated = _update_status_column("customers", "email", email, payload.status)

        # --- 3. I-align ang portal access (Deactivated = lock-out) -----------
        auth_ban = _apply_auth_ban(payload.user_id, payload.status)

        return {
            "status": "success",
            "message": f"Customer marked as {payload.status}.",
            "user_id": payload.user_id,
            "email": email,
            "new_status": payload.status,
            "updated": {
                "users": users_updated,
                "customers": customers_updated
            },
            "auth_ban": auth_ban
        }

    except HTTPException as http_err:
        raise http_err
    except Exception as e:
        print(f"DEBUG: customer status update failed: {e}")
        raise HTTPException(status_code=500, detail=str(e))


@router.post("/delete-customer")
async def delete_customer(payload: AdminDeleteCustomerSchema):
    try:
        row = _resolve_customer_row(payload.user_id)
        email = str(row.get("email") or "").lower().strip()
        current_status = (row.get("status") or "").strip()

        # --- 1. Two-step guardrail: dapat naka-Deactivate muna ---------------
        # Kung wala pa ang status column, hindi natin mapatutunayan na
        # deactivated ang account - kaya i-block (fail-safe direction).
        if not current_status:
            raise HTTPException(
                status_code=409,
                detail=(
                    "Account status is unavailable (run backend-api/database/"
                    "customer_status.sql). Deactivate the account before deleting it."
                )
            )
        if current_status != "Deactivated":
            raise HTTPException(
                status_code=409,
                detail=(
                    "Deactivate the account first. Permanent deletion is only "
                    f"allowed on a deactivated account (current status: {current_status})."
                )
            )

        # --- 2. Tanggalin ang portal settings (cascade-safe, hindi fatal) ---
        settings_deleted = False
        try:
            supabase_secondary.table("customer_settings").delete() \
                .eq("customer_id", payload.user_id).execute()
            settings_deleted = True
        except Exception as set_err:
            print(f"DEBUG: customer_settings cleanup skipped for {payload.user_id}: {set_err}")

        # --- 3. Tanggalin ang CRM customers row -----------------------------
        customers_deleted = False
        if email:
            try:
                supabase_secondary.table("customers").delete().eq("email", email).execute()
                customers_deleted = True
            except Exception as cust_err:
                print(f"DEBUG: customers delete failed for {email}: {cust_err}")
                raise HTTPException(
                    status_code=500,
                    detail="Could not erase the customer record. Please retry."
                )

        # --- 4. Tanggalin ang public.users row ------------------------------
        try:
            supabase_secondary.table("users").delete().eq("id", payload.user_id).execute()
        except Exception as user_err:
            print(f"DEBUG: users delete failed for {payload.user_id}: {user_err}")
            raise HTTPException(
                status_code=500,
                detail="Could not erase the account row. Please retry."
            )

        # --- 5. Tanggalin ang Auth user (hindi fatal sa data delete) --------
        auth_deleted = True
        try:
            supabase_secondary.auth.admin.delete_user(payload.user_id)
        except Exception as auth_err:
            auth_deleted = False
            print(f"DEBUG: auth delete failed for {payload.user_id}: {auth_err}")

        # --- 6. Audit trail para sa Privacy Policy / RA 10173 --------------
        print(
            f"DEBUG: PRIVACY-DELETE uid={payload.user_id} email={email or 'n/a'} "
            f"reason={payload.deletion_reason!r} auth_deleted={auth_deleted} "
            f"customers_row={customers_deleted} settings_row={settings_deleted}"
        )

        return {
            "status": "success",
            "message": "Customer account and its records were permanently deleted.",
            "user_id": payload.user_id,
            "email": email,
            "deleted": {
                "auth_user": auth_deleted,
                "users_row": True,
                "customers_row": customers_deleted,
                "settings_row": settings_deleted
            }
        }

    except HTTPException as http_err:
        raise http_err
    except Exception as e:
        print(f"DEBUG: delete-customer failed: {e}")
        raise HTTPException(status_code=500, detail=str(e))


# 3. Kunin ang lahat ng Sales Agents mula sa profiles table (Primary DB)
@router.get("/agents")
async def get_agents():
    try:
        # Kukunin ang lahat ng users na may role = 'sales_agent' sa profiles table
        res = supabase.table("profiles").select("*").eq("role", "sales_agent").execute()
        agents = res.data or []

        # Normalize ang data para consistent ang format sa frontend
        normalized = []
        for agent in agents:
            full_name = f"{agent.get('first_name', '')} {agent.get('last_name', '')}".strip()
            normalized.append({
                "id": agent.get("id", ""),
                "name": full_name or agent.get("full_name", "Unknown"),
                "first_name": agent.get("first_name", ""),
                "last_name": agent.get("last_name", ""),
                "email": agent.get("email", ""),
                "status": agent.get("status", "Active"),
                "sales": float(agent.get("total_sales", 0) or 0),
            })

        return {
            "status": "success",
            "data": normalized
        }
    except Exception as e:
        raise HTTPException(status_code=500, detail=str(e))