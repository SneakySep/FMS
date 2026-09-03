-- =============================================================================
-- customer_settings  (SECONDARY Supabase project - Customer Portal DB)
-- -----------------------------------------------------------------------------
-- Per-customer portal preferences for
--   frontend/src/views/customer/settings.php
-- Backed by the endpoints in backend-api/app/routes/portal.py:
--   GET  /api/v1/portal/settings
--   PUT  /api/v1/portal/settings
--   PUT  /api/v1/portal/profile   (billing_address / default_warehouse land here)
--
-- Run this in the SQL editor of the CUSTOMER (secondary) Supabase project.
-- Idempotent: safe to re-run.
-- =============================================================================

create table if not exists public.customer_settings (
    customer_id         uuid primary key references public.customers(id) on delete cascade,

    -- Appearance ---------------------------------------------------------------
    dark_mode           boolean     not null default false,
    accent_color        text        not null default 'blue'
                            check (accent_color in ('blue','violet','emerald','amber','rose')),
    density             text        not null default 'comfortable'
                            check (density in ('comfortable','compact')),

    -- Notifications ------------------------------------------------------------
    notif_sound         text        not null default 'notification-1.mp3',
    sound_enabled       boolean     not null default true,
    notify_shipment     boolean     not null default true,
    notify_sla          boolean     not null default true,
    notify_invoice      boolean     not null default true,

    -- Security -----------------------------------------------------------------
    two_factor_enabled  boolean     not null default false,

    -- Billing / logistics preferences -----------------------------------------
    billing_address     text        not null default '',
    default_warehouse   text        not null default 'Caloocan Hub',

    created_at          timestamptz not null default now(),
    updated_at          timestamptz not null default now()
);

comment on table public.customer_settings is
    'Customer portal preferences: appearance, notifications, 2FA flag, billing address, default warehouse.';

-- Keep updated_at fresh on every write.
create or replace function public.touch_customer_settings_updated_at()
returns trigger
language plpgsql
as $$
begin
    new.updated_at := now();
    return new;
end;
$$;

drop trigger if exists trg_customer_settings_updated_at on public.customer_settings;

create trigger trg_customer_settings_updated_at
    before update on public.customer_settings
    for each row
    execute function public.touch_customer_settings_updated_at();

-- -----------------------------------------------------------------------------
-- Row Level Security
-- The FastAPI service-role client bypasses RLS, but policies are still added so
-- a future anon/authenticated client cannot read or write other customers'
-- preferences.
-- -----------------------------------------------------------------------------
alter table public.customer_settings enable row level security;

drop policy if exists "customer_settings_owner_all" on public.customer_settings;

create policy "customer_settings_owner_all"
    on public.customer_settings
    for all
    using (
        auth.uid() is not null
        and customer_id in (
            select id from public.customers
            where lower(email) = lower(coalesce(auth.jwt() ->> 'email', ''))
        )
    )
    with check (
        auth.uid() is not null
        and customer_id in (
            select id from public.customers
            where lower(email) = lower(coalesce(auth.jwt() ->> 'email', ''))
        )
    );
