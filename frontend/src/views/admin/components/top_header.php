<div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8 gap-4">

  <!-- Page Title / Dashboard Header -->
  <div>
    <h1 class="text-2xl font-bold text-navy-900 dark:text-slate-100 tracking-tight">Priority Handling</h1>
  </div>

  <!-- Right Actions: Global Search Bar & Notification Bell -->
  <div class="flex items-center gap-3">

    <!-- Search Bar -->
    <div class="crm-search w-full md:w-80">
      <i class="fa-solid fa-magnifying-glass crm-search-ico"></i>
      <input
        type="text"
        placeholder="Search leads, customer, quotes..."
        class="crm-input !pl-9 !py-2 !text-xs"
      />
    </div>

    <!-- Theme Toggle -->
    <button
      type="button"
      id="crmThemeToggle"
      class="crm-icon-btn shrink-0 border border-line bg-surface"
      aria-label="Toggle dark mode"
      title="Toggle dark mode"
    >
      <i class="fa-regular fa-moon text-sm crm-theme-ico"></i>
    </button>

    <!-- Notification Bell Button -->
    <button
      type="button"
      class="crm-icon-btn relative shrink-0 border border-line bg-surface"
      title="Notifications"
    >
      <i class="fa-regular fa-bell text-sm"></i>
      <!-- Red Badge Indicator -->
      <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-rose-500 rounded-full ring-2 ring-white dark:ring-navy-850"></span>
    </button>

  </div>

</div>

<script>
(function () {
  var btn = document.getElementById('crmThemeToggle');
  if (!btn) return;

  function paint() {
    var dark = document.documentElement.classList.contains('dark');
    btn.innerHTML = dark
      ? '<i class="fa-regular fa-sun text-sm"></i>'
      : '<i class="fa-regular fa-moon text-sm"></i>';
    btn.setAttribute('aria-pressed', dark ? 'true' : 'false');
  }

  btn.addEventListener('click', function () {
    var dark = !document.documentElement.classList.contains('dark');
    if (window.crmSetDarkMode) { window.crmSetDarkMode(dark); }
    paint();
  });

  paint();
})();
</script>