-- =============================================================================
-- customer_status  (SECONDARY Supabase project - Customer Portal DB)
-- -----------------------------------------------------------------------------
-- Adds the account lifecycle status used by the admin Customer Management
-- Actions workflow:
--   frontend/src/views/admin/customers.php   (Actions menu + Manage Customer modal)
--   PATCH  /api/v1/admin/customer-accounts/status   (apply a status change)
--   POST   /api/v1/admin/delete-customer            (permanent, privacy-gated erase)
--
-- Values and what they mean:
--   'Active'       -> normal portal access (default for every existing row)
--   'Inactive'     -> parked account; data is kept and sign-in still works,
--                     useful for seasonal / on-hold clients
--   'Deactivated'  -> locked out. The backend additionally applies a permanent
--                     ban on the Supabase Auth user, so the customer cannot
--                     sign in to the portal. Re-activating removes the ban.
--
-- DELETE RULE (two-step): a public.users row may only be erased permanently
-- while it is 'Deactivated'. That is what makes the destructive action
-- deliberate - an admin has to deactivate first, and only then can the
-- Privacy-Policy-acknowledged hard delete be issued.
--
-- Run this in the SQL editor of the CUSTOMER (secondary) Supabase project.
-- Idempotent: safe to re-run.
--
-- NOTE: until this migration is applied the FastAPI endpoints keep working -
-- they detect the missing column, skip the status write and log a
-- "DEBUG: ... status column missing" line. The list still renders a badge
-- because the view falls back to 'Active', and the backend REFUSES hard
-- deletion while the status cannot be read (fail-safe direction).
-- =============================================================================

-- -----------------------------------------------------------------------------
-- 1. public.users  (the auth-mirrored profile row the admin list reads)
-- -----------------------------------------------------------------------------
alter table public.users
    drop constraint if exists users_status_check;

alter table public.users
    add column if not exists status text not null default 'Active';

alter table public.users
    add constraint users_status_check
    check (status in ('Active', 'Inactive', 'Deactivated'));

comment on column public.users.status is
    'Admin-managed account status: Active, Inactive (parked) or Deactivated (auth user banned, hard delete allowed).';

-- -----------------------------------------------------------------------------
-- 2. public.customers  (mirrored so the CRM record agrees with the login row)
-- -----------------------------------------------------------------------------
alter table public.customers
    drop constraint if exists customers_status_check;

alter table public.customers
    add column if not exists status text not null default 'Active';

alter table public.customers
    add constraint customers_status_check
    check (status in ('Active', 'Inactive', 'Deactivated'));

comment on column public.customers.status is
    'Mirror of public.users.status kept in sync by PATCH /api/v1/admin/customer-accounts/status.';

-- -----------------------------------------------------------------------------
-- 3. Backfill: everything provisioned before this migration was live.
-- -----------------------------------------------------------------------------
update public.users
set    status = 'Active'
where  status is null
   or  status not in ('Active', 'Inactive', 'Deactivated');

update public.customers
set    status = 'Active'
where  status is null
   or  status not in ('Active', 'Inactive', 'Deactivated');

-- -----------------------------------------------------------------------------
-- 4. Index for the admin list status filter.
-- -----------------------------------------------------------------------------
create index if not exists users_status_idx
    on public.users (status);

create index if not exists customers_status_idx
    on public.customers (status);
