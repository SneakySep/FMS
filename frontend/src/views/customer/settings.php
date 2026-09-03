<?php
$page_title = "Settings · Priority Handling Logistics";
$activePage = 'settings';
require_once '../../includes/header.php';
require_once '../../helpers/api_helper.php';
include_once '../../includes/sidebar.php';

// ---------------------------------------------------------------------------
// Hydrate the settings screen with the signed-in customer's real profile and
// saved preferences. Every call is defensive: when the FastAPI service is down
// the page still renders (JS then falls back to localStorage-only behaviour).
// ---------------------------------------------------------------------------
$profile_res = make_api_request('/api/v1/portal/profile', 'GET');
$raw_profile = $profile_res['data'] ?? [];
$profile     = is_array($raw_profile) ? ($raw_profile['data'] ?? $raw_profile) : [];

$settings_res = make_api_request('/api/v1/portal/settings', 'GET');
$raw_settings = $settings_res['data'] ?? [];
$settings     = is_array($raw_settings) ? ($raw_settings['data'] ?? $raw_settings) : [];

// Appearance values are read by the no-flash bootstrap in header.php, so they
// must be emitted as real data attributes on <html> as well as on this root.
$dark_mode    = !empty($settings['dark_mode']);
$accent_color = in_array($settings['accent_color'] ?? '', ['blue', 'violet', 'emerald', 'amber', 'rose'], true)
    ? $settings['accent_color'] : 'blue';
$density      = ($settings['density'] ?? 'comfortable') === 'compact' ? 'compact' : 'comfortable';

$attr = function ($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
};

$acct = [
    'customer_id'  => $profile['customer_id'] ?? ($profile['id'] ?? ''),
    'company_name' => $profile['company_name'] ?? '',
    'email'        => $profile['email'] ?? ($_SESSION['email'] ?? ''),
    'phone_number' => $profile['phone_number'] ?? '',
    'tier'         => $profile['tier'] ?? ($profile['status'] ?? ''),
    'status'       => $profile['status'] ?? '',
    'created_at'   => $profile['created_at'] ?? '',
];
?>

    <!-- MAIN CONTENT AREA -->
    <!--
      The <main> element doubles as the hydration root for
      assets/js/customer/customer_settings.js: the real profile + saved
      preferences are rendered here as data attributes so the page paints with
      correct values before the JS fetch round-trip completes.
    -->
    <main id="customerSettingsRoot" class="flex-1 flex flex-col min-w-0"
          data-customer-user-id="<?= $attr($_SESSION['user_id'] ?? '') ?>"
          data-account-id="<?= $attr($acct['customer_id']) ?>"
          data-company="<?= $attr($acct['company_name']) ?>"
          data-email="<?= $attr($acct['email']) ?>"
          data-phone="<?= $attr($acct['phone_number']) ?>"
          data-tier="<?= $attr($acct['tier']) ?>"
          data-status="<?= $attr($acct['status']) ?>"
          data-created-at="<?= $attr($acct['created_at']) ?>"
          data-dark-mode="<?= $dark_mode ? '1' : '0' ?>"
          data-accent-color="<?= $attr($accent_color) ?>"
          data-density="<?= $attr($density) ?>"
          data-notif-sound="<?= $attr($settings['notif_sound'] ?? 'notification-1.mp3') ?>"
          data-sound-enabled="<?= isset($settings['sound_enabled']) ? (empty($settings['sound_enabled']) ? '0' : '1') : '1' ?>"
          data-notify-shipment="<?= isset($settings['notify_shipment']) ? (empty($settings['notify_shipment']) ? '0' : '1') : '1' ?>"
          data-notify-sla="<?= isset($settings['notify_sla']) ? (empty($settings['notify_sla']) ? '0' : '1') : '1' ?>"
          data-notify-invoice="<?= isset($settings['notify_invoice']) ? (empty($settings['notify_invoice']) ? '0' : '1') : '1' ?>"
          data-two-factor="<?= !empty($settings['two_factor_enabled']) ? '1' : '0' ?>"
          data-billing-address="<?= $attr($settings['billing_address'] ?? '') ?>"
          data-default-warehouse="<?= $attr($settings['default_warehouse'] ?? 'Caloocan Hub') ?>">


        <!-- TOP HEADER BAR (matches the rest of the portal) -->
        <header class="bg-white dark:bg-[#0e1b33] border-b border-slate-200 dark:border-slate-800 px-8 py-4 flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-black italic text-slate-900 dark:text-white tracking-tight">Settings</h2>
                <p class="text-[11px] text-slate-400 dark:text-slate-500 font-medium mt-0.5">Manage your account, preferences & security</p>
            </div>

            <div class="flex-1 max-w-md mx-8">
                <div class="relative">
                    <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    <input type="text" placeholder="Track, a waybill, invoice, or document..." class="w-full bg-slate-50 dark:bg-slate-900 dark:border-slate-700 border border-slate-200 rounded-xl pl-9 pr-4 py-2 text-xs text-slate-800 dark:text-slate-200 placeholder-slate-400 focus:outline-none focus:border-brand-blue focus:bg-white dark:focus:bg-slate-900 transition-all">
                </div>
            </div>

            <div class="flex items-center gap-3">
                <button class="relative w-9 h-9 bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 rounded-xl flex items-center justify-center hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors">
                    <i class="fa-solid fa-bell text-xs"></i>
                    <span class="absolute top-2 right-2 w-2 h-2 bg-red-500 rounded-full"></span>
                </button>
                <button onclick="toggleChat()" class="bg-blue-50 dark:bg-blue-500/10 hover:bg-blue-100 dark:hover:bg-blue-500/20 text-brand-blue font-semibold text-xs px-4 py-2 rounded-xl transition-colors flex items-center gap-2 border border-blue-100 dark:border-blue-500/20">
                    Help Desk <i class="fa-solid fa-headset text-xs"></i>
                </button>
                <button onclick="alert('Opening Freight Booking Form...')" class="bg-brand-blue hover:bg-brand-darkblue text-white font-semibold text-xs px-4 py-2 rounded-xl transition-all shadow-md shadow-blue-500/20 flex items-center gap-2">
                    + Book Shipment
                </button>
            </div>
        </header>


        <!-- SETTINGS DASHBOARD BODY -->
        <div class="p-6 lg:p-8 2xl:px-10 w-full pb-36">

            <!-- INTRO BANNER -->
            <section class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-[#0b1528] via-[#0e1b33] to-[#103a8a] p-6 mb-8 shadow-lg">
                <div class="relative z-10 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-2 text-[11px] font-semibold uppercase tracking-wider text-blue-200 mb-2">
                            <i class="fa-solid fa-gear text-brand-blue"></i> Account Centre
                        </div>
                        <h3 class="text-xl font-black italic text-white tracking-tight">Robles Cargo Corp. · Acct #8841</h3>
                        <p class="text-sm text-blue-100/80 mt-1">Keep your profile, notifications and security up to date. Changes are staged until you apply them.</p>
                    </div>
                    <div class="flex items-center gap-3 shrink-0">
                        <span class="inline-flex items-center gap-1.5 text-[11px] font-semibold uppercase tracking-wider text-emerald-300 bg-emerald-500/15 border border-emerald-400/30 px-3 py-1.5 rounded-full">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span> Active
                        </span>
                    </div>
                </div>
                <i class="fa-solid fa-sliders absolute -right-6 -bottom-10 text-[160px] text-white/5 pointer-events-none select-none"></i>
            </section>

            <!-- LAYOUT: LEFT NAV + RIGHT PANELS -->
            <div class="grid grid-cols-1 lg:grid-cols-[260px_1fr] gap-8">

                <!-- LEFT: CATEGORY NAV -->
                <aside class="lg:sticky lg:top-8 self-start space-y-3">
                    <div class="bg-white dark:bg-[#112240] border border-slate-200 dark:border-slate-700/60 rounded-2xl p-3 shadow-sm">
                        <nav id="settingsNav" class="space-y-1">
                            <button onclick="switchSettingsTab('overview')" data-tab="overview"  class="settings-tab w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800/60 transition-colors">
                                <i class="fa-solid fa-gauge-high w-5 text-center text-slate-400"></i> Overview
                            </button>
                            <button onclick="switchSettingsTab('profile')" data-tab="profile"    class="settings-tab w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800/60 transition-colors">
                                <i class="fa-solid fa-user w-5 text-center text-slate-400"></i> Profile & Account
                            </button>
                            <button onclick="switchSettingsTab('appearance')" data-tab="appearance" class="settings-tab w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800/60 transition-colors">
                                <i class="fa-solid fa-palette w-5 text-center text-slate-400"></i> Appearance
                            </button>
                            <button onclick="switchSettingsTab('notify')" data-tab="notify"    class="settings-tab w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800/60 transition-colors">
                                <i class="fa-solid fa-bell w-5 text-center text-slate-400"></i> Notifications
                            </button>
                            <button onclick="switchSettingsTab('security')" data-tab="security"  class="settings-tab w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800/60 transition-colors">
                                <i class="fa-solid fa-shield-halved w-5 text-center text-slate-400"></i> Security & Privacy
                            </button>
                            <button onclick="switchSettingsTab('billing')" data-tab="billing"   class="settings-tab w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800/60 transition-colors">
                                <i class="fa-solid fa-receipt w-5 text-center text-slate-400"></i> Billing & Plan
                            </button>
                        </nav>
                    </div>

                    <!-- MINI ACCOUNT CARD -->
                    <div class="bg-white dark:bg-[#112240] border border-slate-200 dark:border-slate-700/60 rounded-2xl p-4 shadow-sm">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-brand-blue/10 text-brand-blue flex items-center justify-center font-black shrink-0" id="miniAccountInitials"><?= $attr(strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $acct['company_name'] ?: 'AC'), 0, 2))) ?></div>
                            <div class="min-w-0">
                                <p class="text-xs font-bold text-slate-800 dark:text-white truncate" id="miniAccountName"><?= $attr($acct['company_name'] ?: 'Your company') ?></p>
                                <p class="text-[11px] text-slate-400 truncate" id="miniAccountEmail"><?= $attr($acct['email'] ?: '—') ?></p>
                            </div>
                        </div>
                        <div class="mt-3 pt-3 border-t border-slate-100 dark:border-slate-700/60 flex items-center justify-between text-[11px]">
                            <span class="text-slate-400">Plan</span>
                            <span class="font-semibold text-brand-blue" id="miniAccountPlan"><?= $attr($acct['tier'] ?: 'Standard') ?></span>
                        </div>
                    </div>
                </aside>

                <!-- RIGHT: PANELS -->
                <div class="space-y-8">


                    <!-- ============ PANEL: OVERVIEW ============ -->
                    <section data-panel="overview" class="settings-panel space-y-6">
                        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                            <div class="bg-white dark:bg-[#112240] border border-slate-200 dark:border-slate-700/60 rounded-2xl p-5 shadow-sm">
                                <div class="flex items-center justify-between">
                                    <span class="text-[11px] font-semibold text-slate-500 dark:text-slate-400">Account status</span>
                                    <div class="p-1.5 rounded-lg bg-emerald-50 text-emerald-600"><i class="fa-solid fa-circle-check text-xs"></i></div>
                                </div>
                                <p class="text-lg font-extrabold text-slate-900 dark:text-white mt-3" id="overviewAccountStatus"><?= $attr($acct['status'] ?: 'Active') ?></p>
                                <p class="text-[11px] text-slate-400 mt-1">Verified · <span id="overviewPlan"><?= $attr($acct['tier'] ?: 'Standard') ?></span></p>
                            </div>
                            <div class="bg-white dark:bg-[#112240] border border-slate-200 dark:border-slate-700/60 rounded-2xl p-5 shadow-sm">
                                <div class="flex items-center justify-between">
                                    <span class="text-[11px] font-semibold text-slate-500 dark:text-slate-400">Open tickets</span>
                                    <div class="p-1.5 rounded-lg bg-amber-50 text-amber-600"><i class="fa-solid fa-ticket text-xs"></i></div>
                                </div>
                                <p class="text-lg font-extrabold text-slate-900 dark:text-white mt-3">2</p>
                                <p class="text-[11px] text-emerald-600 font-semibold mt-1">1 awaiting you</p>
                            </div>
                            <div class="bg-white dark:bg-[#112240] border border-slate-200 dark:border-slate-700/60 rounded-2xl p-5 shadow-sm">
                                <div class="flex items-center justify-between">
                                    <span class="text-[11px] font-semibold text-slate-500 dark:text-slate-400">On-time SLA</span>
                                    <div class="p-1.5 rounded-lg bg-blue-50 text-brand-blue"><i class="fa-solid fa-bolt text-xs"></i></div>
                                </div>
                                <p class="text-lg font-extrabold text-slate-900 dark:text-white mt-3">94%</p>
                                <div class="mt-2 w-full bg-slate-100 dark:bg-slate-800 h-1.5 rounded-full overflow-hidden">
                                    <div class="bg-emerald-500 h-full w-[94%] rounded-full"></div>
                                </div>
                            </div>
                            <div class="bg-white dark:bg-[#112240] border border-slate-200 dark:border-slate-700/60 rounded-2xl p-5 shadow-sm">
                                <div class="flex items-center justify-between">
                                    <span class="text-[11px] font-semibold text-slate-500 dark:text-slate-400">2FA</span>
                                    <div class="p-1.5 rounded-lg bg-rose-50 text-rose-600"><i class="fa-solid fa-lock text-xs"></i></div>
                                </div>
                                <p class="text-lg font-extrabold text-slate-900 dark:text-white mt-3" id="overviewTwoFactor">Off</p>
                                <p class="text-[11px] text-rose-500 font-semibold mt-1" id="overviewTwoFactorHint">Recommended on</p>
                            </div>
                        </div>


                        <div class="bg-white dark:bg-[#112240] border border-slate-200 dark:border-slate-700/60 rounded-2xl p-6 shadow-sm">
                            <h3 class="text-base font-extrabold text-slate-900 dark:text-white">Quick settings</h3>
                            <p class="text-xs text-slate-400 dark:text-slate-500 mt-0.5">Jump straight to a category.</p>
                            <div class="mt-4 grid grid-cols-2 sm:grid-cols-3 gap-3">
                                <button onclick="switchSettingsTab('profile')"    class="flex items-center gap-3 p-3 rounded-xl border border-slate-200 dark:border-slate-700/60 hover:border-brand-blue hover:bg-slate-50 dark:hover:bg-slate-800/60 transition-colors text-left">
                                    <i class="fa-solid fa-user text-brand-blue"></i><span class="text-xs font-semibold text-slate-700 dark:text-slate-200">Profile</span>
                                </button>
                                <button onclick="switchSettingsTab('appearance')" class="flex items-center gap-3 p-3 rounded-xl border border-slate-200 dark:border-slate-700/60 hover:border-brand-blue hover:bg-slate-50 dark:hover:bg-slate-800/60 transition-colors text-left">
                                    <i class="fa-solid fa-palette text-brand-blue"></i><span class="text-xs font-semibold text-slate-700 dark:text-slate-200">Appearance</span>
                                </button>
                                <button onclick="switchSettingsTab('notify')"    class="flex items-center gap-3 p-3 rounded-xl border border-slate-200 dark:border-slate-700/60 hover:border-brand-blue hover:bg-slate-50 dark:hover:bg-slate-800/60 transition-colors text-left">
                                    <i class="fa-solid fa-bell text-brand-blue"></i><span class="text-xs font-semibold text-slate-700 dark:text-slate-200">Notifications</span>
                                </button>
                                <button onclick="switchSettingsTab('security')"  class="flex items-center gap-3 p-3 rounded-xl border border-slate-200 dark:border-slate-700/60 hover:border-brand-blue hover:bg-slate-50 dark:hover:bg-slate-800/60 transition-colors text-left">
                                    <i class="fa-solid fa-shield-halved text-brand-blue"></i><span class="text-xs font-semibold text-slate-700 dark:text-slate-200">Security</span>
                                </button>
                                <button onclick="switchSettingsTab('billing')"   class="flex items-center gap-3 p-3 rounded-xl border border-slate-200 dark:border-slate-700/60 hover:border-brand-blue hover:bg-slate-50 dark:hover:bg-slate-800/60 transition-colors text-left">
                                    <i class="fa-solid fa-receipt text-brand-blue"></i><span class="text-xs font-semibold text-slate-700 dark:text-slate-200">Billing</span>
                                </button>
                                <a href="/tracking" class="flex items-center gap-3 p-3 rounded-xl border border-slate-200 dark:border-slate-700/60 hover:border-brand-blue hover:bg-slate-50 dark:hover:bg-slate-800/60 transition-colors text-left">
                                    <i class="fa-solid fa-location-crosshairs text-brand-blue"></i><span class="text-xs font-semibold text-slate-700 dark:text-slate-200">Track shipment</span>
                                </a>
                            </div>
                        </div>
                    </section>


                    <!-- ============ PANEL: PROFILE ============ -->
                    <section data-panel="profile" class="settings-panel hidden space-y-6">
                        <div class="bg-white dark:bg-[#112240] border border-slate-200 dark:border-slate-700/60 rounded-2xl p-6 sm:p-8 shadow-sm space-y-6">
                            <div>
                                <h3 class="text-base font-extrabold text-slate-900 dark:text-white">Account Details</h3>
                                <p class="text-xs text-slate-400 dark:text-slate-500">
                                    <span id="profileCompanyName"><?= $attr($acct['company_name'] ?: 'Your company') ?></span>
                                    · Acct #<span id="profileAccountId"><?= $attr($acct['customer_id'] ?: '—') ?></span>
                                </p>
                            </div>

                            <form onsubmit="stageAccountDetails(event)" class="space-y-5">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1">Company name</label>
                                        <input type="text" id="settingCompany" value="<?= $attr($acct['company_name']) ?>" required class="w-full bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200 rounded-xl px-4 py-2.5 text-xs focus:bg-white dark:focus:bg-slate-900 focus:border-brand-blue focus:outline-none transition-colors">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1">Contact email</label>
                                        <input type="email" id="settingEmail" value="<?= $attr($acct['email']) ?>" required class="w-full bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200 rounded-xl px-4 py-2.5 text-xs focus:bg-white dark:focus:bg-slate-900 focus:border-brand-blue focus:outline-none transition-colors">
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1">Contact number</label>
                                        <input type="tel" id="settingPhone" value="<?= $attr($acct['phone_number']) ?>" required class="w-full bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200 rounded-xl px-4 py-2.5 text-xs focus:bg-white dark:focus:bg-slate-900 focus:border-brand-blue focus:outline-none transition-colors">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1">Default warehouse</label>
                                        <select id="settingWarehouse" class="w-full bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200 rounded-xl px-4 py-2.5 text-xs focus:bg-white dark:focus:bg-slate-900 focus:border-brand-blue focus:outline-none transition-colors cursor-pointer">
                                            <?php
                                            $warehouses = ['Caloocan Hub', 'Manila South Harbor Hub', 'Cebu Logistics Center', 'Davao Regional Hub'];
                                            $savedWarehouse = $settings['default_warehouse'] ?? 'Caloocan Hub';
                                            if (!in_array($savedWarehouse, $warehouses, true)) {
                                                // Keep unknown saved values visible so they are not silently reset.
                                                $warehouses[] = $savedWarehouse;
                                            }
                                            foreach ($warehouses as $wh): ?>
                                                <option value="<?= $attr($wh) ?>" <?= $wh === $savedWarehouse ? 'selected' : '' ?>><?= $attr($wh) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1">Billing address</label>
                                    <input type="text" id="settingAddress" value="<?= $attr($settings['billing_address'] ?? '') ?>" required class="w-full bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200 rounded-xl px-4 py-2.5 text-xs focus:bg-white dark:focus:bg-slate-900 focus:border-brand-blue focus:outline-none transition-colors">
                                </div>

                                <div class="flex justify-end gap-3 pt-3">
                                    <button type="button" onclick="location.reload()" class="bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 dark:text-slate-200 font-semibold text-xs px-6 py-2.5 rounded-xl transition-colors">Cancel</button>
                                    <button type="submit" id="saveAccountBtn" class="bg-brand-blue hover:bg-brand-darkblue text-white font-semibold text-xs px-6 py-2.5 rounded-xl transition-colors shadow-md shadow-blue-500/20">Save changes</button>
                                </div>
                            </form>
                        </div>
                    </section>


                    <!-- ============ PANEL: APPEARANCE ============ -->
                    <section data-panel="appearance" class="settings-panel hidden space-y-6">
                        <div class="bg-white dark:bg-[#112240] border border-slate-200 dark:border-slate-700/60 rounded-2xl p-6 sm:p-8 shadow-sm space-y-6">
                            <div>
                                <h3 class="text-base font-extrabold text-slate-900 dark:text-white">Appearance</h3>
                                <p class="text-xs text-slate-400 dark:text-slate-500">Theme and density apply live; other changes stage until you <span class="font-semibold text-brand-blue dark:text-slate-300">Apply Changes</span>.</p>
                            </div>

                            <div class="flex items-center justify-between gap-4">
                                <div>
                                    <h4 class="font-extrabold text-slate-900 dark:text-white">Dark mode</h4>
                                    <p class="text-[11px] text-slate-400 dark:text-slate-500">Previewed instantly across the portal</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" id="appearanceDarkToggle" onchange="stageAppearanceDark(this.checked)" class="sr-only peer">
                                    <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-brand-blue dark:bg-slate-600"></div>
                                </label>
                            </div>

                            <div class="py-4 border-t border-slate-100 dark:border-slate-700/60">
                                <h4 class="font-extrabold text-slate-900 dark:text-white mb-1">Accent colour</h4>
                                <p class="text-[11px] text-slate-400 dark:text-slate-500 mb-3">Used for highlights and active states</p>
                                <div class="flex items-center gap-3" id="accentSwatches">
                                    <button type="button" onclick="setAccent('#0066ff','blue')"   data-accent="blue"   class="w-8 h-8 rounded-full bg-[#0066ff] ring-2 ring-offset-2 ring-transparent dark:ring-offset-[#112240] transition"></button>
                                    <button type="button" onclick="setAccent('#7c3aed','violet')" data-accent="violet" class="w-8 h-8 rounded-full bg-[#7c3aed] ring-2 ring-offset-2 ring-transparent dark:ring-offset-[#112240] transition"></button>
                                    <button type="button" onclick="setAccent('#059669','emerald')" data-accent="emerald" class="w-8 h-8 rounded-full bg-[#059669] ring-2 ring-offset-2 ring-transparent dark:ring-offset-[#112240] transition"></button>
                                    <button type="button" onclick="setAccent('#d97706','amber')" data-accent="amber" class="w-8 h-8 rounded-full bg-[#d97706] ring-2 ring-offset-2 ring-transparent dark:ring-offset-[#112240] transition"></button>
                                    <button type="button" onclick="setAccent('#dc2626','rose')" data-accent="rose" class="w-8 h-8 rounded-full bg-[#dc2626] ring-2 ring-offset-2 ring-transparent dark:ring-offset-[#112240] transition"></button>
                                </div>
                            </div>

                            <div class="py-4 border-t border-slate-100 dark:border-slate-700/60 flex items-center justify-between gap-4">
                                <div>
                                    <h4 class="font-extrabold text-slate-900 dark:text-white">Compact density</h4>
                                    <p class="text-[11px] text-slate-400 dark:text-slate-500">Tighter spacing for information-dense screens</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" id="densityToggle" onchange="setDensity(this.checked)" class="sr-only peer">
                                    <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-brand-blue dark:bg-slate-600"></div>
                                </label>
                            </div>
                        </div>
                    </section>


                    <!-- ============ PANEL: NOTIFICATIONS ============ -->
                    <section data-panel="notify" class="settings-panel hidden space-y-6">
                        <div class="bg-white dark:bg-[#112240] border border-slate-200 dark:border-slate-700/60 rounded-2xl p-6 sm:p-8 shadow-sm space-y-6">
                            <div>
                                <h3 class="text-base font-extrabold text-slate-900 dark:text-white">Notification Preferences</h3>
                                <p class="text-xs text-slate-400 dark:text-slate-500">Routed via the Notification Hub</p>
                            </div>

                            <div class="divide-y divide-slate-100 dark:divide-slate-700/60 text-xs">
                                <div class="py-4 flex items-center justify-between gap-4">
                                    <div>
                                        <h4 class="font-extrabold text-slate-900 dark:text-white">Shipment status updates</h4>
                                        <p class="text-[11px] text-slate-400 dark:text-slate-500">Email + SMS when a waybill changes status</p>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" checked onchange="stageNotification(this.checked,'shipment')" data-notif-channel="shipment" class="sr-only peer">
                                        <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-brand-blue dark:bg-slate-600"></div>
                                    </label>
                                </div>

                                <div class="py-4 flex items-center justify-between gap-4">
                                    <div>
                                        <h4 class="font-extrabold text-slate-900 dark:text-white">SLA breach alerts</h4>
                                        <p class="text-[11px] text-slate-400 dark:text-slate-500">Immediate notice when a commitment is at risk</p>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" checked onchange="stageNotification(this.checked,'sla')" data-notif-channel="sla" class="sr-only peer">
                                        <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-brand-blue dark:bg-slate-600"></div>
                                    </label>
                                </div>

                                <div class="py-4 flex items-center justify-between gap-4">
                                    <div>
                                        <h4 class="font-extrabold text-slate-900 dark:text-white">Invoice reminders</h4>
                                        <p class="text-[11px] text-slate-400 dark:text-slate-500">3 days before an invoice is due</p>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" checked onchange="stageNotification(this.checked,'invoice')" data-notif-channel="invoice" class="sr-only peer">
                                        <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-brand-blue dark:bg-slate-600"></div>
                                    </label>
                                </div>
                            </div>
                        </div>


                        <div class="bg-white dark:bg-[#112240] border border-slate-200 dark:border-slate-700/60 rounded-2xl p-6 sm:p-8 shadow-sm space-y-6">
                            <div>
                                <h3 class="text-base font-extrabold text-slate-900 dark:text-white">Notification Sound</h3>
                                <p class="text-xs text-slate-400 dark:text-slate-500">Played for portal notifications and chat replies</p>
                            </div>
                            <div class="flex items-center justify-between gap-4">
                                <div>
                                    <h4 class="font-extrabold text-slate-900 dark:text-white">Play sounds</h4>
                                    <p class="text-[11px] text-slate-400 dark:text-slate-500">Off mutes portal notifications and chat replies</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer shrink-0">
                                    <input type="checkbox" checked onchange="stageSoundEnabled(this.checked)" id="soundEnabledToggle" class="sr-only peer">
                                    <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-brand-blue dark:bg-slate-600"></div>
                                </label>
                            </div>
                            <div class="flex items-center gap-2">
                                <select id="notifSoundSelect" onchange="stageNotificationSound(this.value)" class="bg-slate-50 dark:bg-slate-900 dark:border-slate-700 text-slate-700 dark:text-slate-200 border border-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-brand-blue cursor-pointer">
                                    <option value="notification-1.mp3">Notification 1</option>
                                    <option value="notification-2.mp3">Notification 2</option>
                                    <option value="notification-3.mp3">Notification 3</option>
                                    <option value="notification-4.mp3">Notification 4</option>
                                </select>
                                <button type="button" onclick="previewNotificationSound()" title="Preview sound" class="bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 dark:text-slate-300 w-8 h-8 rounded-lg flex items-center justify-center transition-colors">
                                    <i class="fa-solid fa-volume-high text-xs"></i>
                                </button>
                            </div>
                        </div>
                    </section>


                    <!-- ============ PANEL: SECURITY ============ -->
                    <section data-panel="security" class="settings-panel hidden space-y-6">
                        <div class="bg-white dark:bg-[#112240] border border-slate-200 dark:border-slate-700/60 rounded-2xl p-6 sm:p-8 shadow-sm space-y-6">
                            <div>
                                <h3 class="text-base font-extrabold text-slate-900 dark:text-white">Sign-in & Security</h3>
                                <p class="text-xs text-slate-400 dark:text-slate-500">Protect your account with a strong password and 2FA</p>
                            </div>

                            <form onsubmit="stageSecurity(event)" class="space-y-5">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1">New password</label>
                                        <input type="password" id="inputNewPassword" autocomplete="new-password" placeholder="••••••••" class="w-full bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200 rounded-xl px-4 py-2.5 text-xs focus:bg-white dark:focus:bg-slate-900 focus:border-brand-blue focus:outline-none transition-colors">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1">Confirm password</label>
                                        <input type="password" id="inputConfirmPassword" autocomplete="new-password" placeholder="••••••••" class="w-full bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200 rounded-xl px-4 py-2.5 text-xs focus:bg-white dark:focus:bg-slate-900 focus:border-brand-blue focus:outline-none transition-colors">
                                    </div>
                                </div>
                                <div class="flex justify-end gap-3 pt-1">
                                    <button type="submit" id="savePasswordBtn" class="bg-brand-blue hover:bg-brand-darkblue text-white font-semibold text-xs px-6 py-2.5 rounded-xl transition-colors shadow-md shadow-blue-500/20">Update password</button>
                                </div>
                            </form>

                            <div class="py-4 border-t border-slate-100 dark:border-slate-700/60 flex items-center justify-between gap-4">
                                <div>
                                    <h4 class="font-extrabold text-slate-900 dark:text-white">Two-factor authentication</h4>
                                    <p class="text-[11px] text-slate-400 dark:text-slate-500">Require a one-time code at sign-in · <span id="twoFactorStatus" class="font-semibold text-slate-400">Disabled</span></p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" id="twoFactorToggle" onchange="toggleTwoFactor(this.checked)" class="sr-only peer">
                                    <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-brand-blue dark:bg-slate-600"></div>
                                </label>
                            </div>
                        </div>


                        <div class="bg-white dark:bg-[#112240] border border-slate-200 dark:border-slate-700/60 rounded-2xl p-6 sm:p-8 shadow-sm space-y-4">
                            <div class="flex items-center justify-between">
                                <h3 class="text-base font-extrabold text-slate-900 dark:text-white">Active sessions</h3>
                                <button type="button" onclick="endAllSessions()" class="text-[11px] font-semibold text-rose-600 hover:text-rose-700 transition-colors">End all other sessions</button>
                            </div>
                            <div class="divide-y divide-slate-100 dark:divide-slate-700/60 text-xs" id="sessionList">
                                <div class="py-3 flex items-center justify-between gap-4" data-current-session>
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-lg bg-blue-50 dark:bg-blue-500/10 text-brand-blue flex items-center justify-center"><i class="fa-solid fa-desktop text-sm"></i></div>
                                        <div>
                                            <p class="font-extrabold text-slate-900 dark:text-white">Windows · Chrome</p>
                                            <p class="text-[11px] text-slate-400">Manila, PH · Current session</p>
                                        </div>
                                    </div>
                                    <span class="text-[11px] font-semibold text-emerald-600">This device</span>
                                </div>
                                <div class="py-3 flex items-center justify-between gap-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-500 flex items-center justify-center"><i class="fa-solid fa-mobile-screen text-sm"></i></div>
                                        <div>
                                            <p class="font-extrabold text-slate-900 dark:text-white">iOS · SwiftFreight App</p>
                                            <p class="text-[11px] text-slate-400">Cebu, PH · 2 days ago</p>
                                        </div>
                                    </div>
                                    <button type="button" onclick="endSession(this)" class="text-[11px] font-semibold text-slate-400 hover:text-rose-600 transition-colors">Revoke</button>
                                </div>
                            </div>
                        </div>
                    </section>


                    <!-- ============ PANEL: BILLING ============ -->
                    <section data-panel="billing" class="settings-panel hidden space-y-6">
                        <div class="bg-white dark:bg-[#112240] border border-slate-200 dark:border-slate-700/60 rounded-2xl p-6 sm:p-8 shadow-sm">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 rounded-2xl bg-brand-blue/10 text-brand-blue flex items-center justify-center text-lg"><i class="fa-solid fa-crown"></i></div>
                                    <div>
                                        <h3 class="text-base font-extrabold text-slate-900 dark:text-white">Enterprise Plan</h3>
                                        <p class="text-xs text-slate-400 dark:text-slate-500">Unlimited shipments · Dedicated account manager</p>
                                    </div>
                                </div>
                                <span class="inline-flex items-center gap-1.5 text-[11px] font-semibold uppercase tracking-wider text-emerald-600 bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/30 px-3 py-1.5 rounded-full">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Active
                                </span>
                            </div>
                            <div class="mt-6 grid grid-cols-1 sm:grid-cols-3 gap-4">
                                <div class="rounded-xl bg-slate-50 dark:bg-slate-900/60 p-4">
                                    <p class="text-[11px] font-semibold text-slate-500 dark:text-slate-400">Paid this quarter</p>
                                    <p class="text-xl font-extrabold text-slate-900 dark:text-white mt-1">₱284,000</p>
                                </div>
                                <div class="rounded-xl bg-slate-50 dark:bg-slate-900/60 p-4">
                                    <p class="text-[11px] font-semibold text-slate-500 dark:text-slate-400">Next invoice</p>
                                    <p class="text-xl font-extrabold text-slate-900 dark:text-white mt-1">Sep 1, 2026</p>
                                </div>
                                <div class="rounded-xl bg-slate-50 dark:bg-slate-900/60 p-4">
                                    <p class="text-[11px] font-semibold text-slate-500 dark:text-slate-400">Payment method</p>
                                    <p class="text-xl font-extrabold text-slate-900 dark:text-white mt-1 flex items-center gap-2"><i class="fa-brands fa-cc-visa text-blue-600"></i> •• 4242</p>
                                </div>
                            </div>
                            <div class="mt-6 flex flex-wrap gap-3">
                                <button onclick="stageBillingAction('invoices')" class="bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 dark:text-slate-200 font-semibold text-xs px-5 py-2.5 rounded-xl transition-colors">View invoices</button>
                                <button onclick="stageBillingAction('upgrade-plan')" class="bg-brand-blue hover:bg-brand-darkblue text-white font-semibold text-xs px-5 py-2.5 rounded-xl transition-colors shadow-md shadow-blue-500/20">Manage plan</button>
                            </div>
                        </div>

                        <div class="bg-white dark:bg-[#112240] border border-rose-200 dark:border-rose-500/30 rounded-2xl p-6 sm:p-8 shadow-sm">
                            <h3 class="text-base font-extrabold text-rose-600 dark:text-rose-400">Danger zone</h3>
                            <p class="text-xs text-slate-400 dark:text-slate-500 mt-0.5">These actions are irreversible. Proceed with caution.</p>
                            <div class="mt-4 flex flex-wrap gap-3">
                                <button onclick="exportAccountData()" class="border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:border-slate-300 dark:hover:border-slate-600 font-semibold text-xs px-5 py-2.5 rounded-xl transition-colors">Download my data</button>
                                <button onclick="if(confirm('Close your account? This cannot be undone.')) alert('Account closure requested.')" class="border border-rose-200 dark:border-rose-500/40 text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-500/10 font-semibold text-xs px-5 py-2.5 rounded-xl transition-colors">Close account</button>
                            </div>
                        </div>
                    </section>


                </div>
            </div>
        </div>

        <!-- STICKY APPLY BAR -->
        <div id="applyBar" class="fixed bottom-0 left-64 right-0 z-30 hidden bg-white/95 dark:bg-[#0e1b33]/95 backdrop-blur border-t border-slate-200 dark:border-slate-800 px-8 py-4 flex items-center justify-between shadow-[0_-4px_20px_rgba(0,0,0,0.08)]">
            <span id="applyHint" class="text-xs font-semibold text-slate-500 dark:text-slate-400">You have unsaved changes.</span>
            <div class="flex items-center gap-3">
                <button type="button" onclick="discardSettings()" class="bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 dark:text-slate-200 font-semibold text-xs px-6 py-2.5 rounded-xl transition-colors">Discard</button>
                <button type="button" onclick="applySettings()" class="bg-brand-blue hover:bg-brand-darkblue text-white font-semibold text-xs px-6 py-2.5 rounded-xl transition-colors shadow-md shadow-blue-500/20 flex items-center gap-2">
                    <i class="fa-solid fa-check text-xs"></i> Apply Changes
                </button>
            </div>
        </div>

    </main>

    <?php include_once '../../components/chat_widget.php'; ?>

    <!-- Scripts -->
    <script src="../../../assets/js/customer/customer_dashboard.js"></script>
    <script src="../../../assets/js/customer/customer_settings.js"></script>

<!-- FOOTER INCLUDE -->
<?php include_once '../../includes/footer.php'; ?>

