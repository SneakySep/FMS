-- =============================================================================
-- customer_type  (SECONDARY Supabase project - Customer Portal DB)
-- -----------------------------------------------------------------------------
-- Adds the B2B / B2C segment to the two tables the admin "Add New Customer"
-- flow writes to:
--   frontend/src/views/admin/customers.php   (Add New Customer modal)
--   POST /api/v1/admin/create-customer       (backend-api/app/routes/admin/admin.py)
--
-- Values:
--   'business'   -> Business to Business (company account, company_name required)
--   'individual' -> Customer to Business (walk-in / personal account)
--
-- Run this in the SQL editor of the CUSTOMER (secondary) Supabase project.
-- Idempotent: safe to re-run.
--
-- NOTE: until this migration is applied the FastAPI endpoints still work -
-- they detect the missing column, retry the insert without it and log a
-- "DEBUG: ... customer_type column missing" line. Existing rows keep their
-- segment unknown and the UI falls back to deriving it from company_name.
-- =============================================================================

-- -----------------------------------------------------------------------------
-- 1. public.customers
-- -----------------------------------------------------------------------------
alter table public.customers
    drop constraint if exists customers_customer_type_check;

alter table public.customers
    add column if not exists customer_type text not null default 'individual';

alter table public.customers
    add constraint customers_customer_type_check
    check (customer_type in ('business', 'individual'));

comment on column public.customers.customer_type is
    'Account segment: business = Business to Business (B2B), individual = Customer to Business (B2C).';

-- -----------------------------------------------------------------------------
-- 2. public.users
-- -----------------------------------------------------------------------------
alter table public.users
    drop constraint if exists users_customer_type_check;

alter table public.users
    add column if not exists customer_type text not null default 'individual';

alter table public.users
    add constraint users_customer_type_check
    check (customer_type in ('business', 'individual'));

comment on column public.users.customer_type is
    'Account segment mirrored from public.customers so the admin accounts list can badge B2B/B2C without a join.';

-- -----------------------------------------------------------------------------
-- 3. Backfill: any pre-existing row that carries a company name was provisioned
--    as a business account (including every row created through the
--    "create-customer-from-ticket" flow).
-- -----------------------------------------------------------------------------
update public.customers
set    customer_type = 'business'
where  company_name is not null
  and  trim(company_name) <> ''
  and  customer_type <> 'business';

update public.users
set    customer_type = 'business'
where  company_name is not null
  and  trim(company_name) <> ''
  and  customer_type <> 'business';

-- -----------------------------------------------------------------------------
-- 4. Optional segment attributes collected by the B2B branch of the form.
--    These are additive and nullable: the backend only writes them when the
--    columns are present, so leaving them out does not break anything.
-- -----------------------------------------------------------------------------
alter table public.customers
    add column if not exists contact_email text,
    add column if not exists address text,
    add column if not exists tax_id text,
    add column if not exists website text;

comment on column public.customers.tax_id is
    'TIN / DTI-SEC business registration number (B2B accounts only, optional).';

-- -----------------------------------------------------------------------------
-- 5. Index for the admin list filter (Business / Individual).
-- -----------------------------------------------------------------------------
create index if not exists customers_customer_type_idx
    on public.customers (customer_type);

create index if not exists users_customer_type_idx
    on public.users (customer_type);
