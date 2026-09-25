document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('.counter').forEach((el) => {
    const target = parseInt(el.dataset.target || '0', 10);
    const t0 = performance.now(), dur = 900;
    function tick(t) {
      const p = Math.min(1, (t - t0) / dur);
      el.textContent = Math.round(target * (1 - Math.pow(1 - p, 3)));
      if (p < 1) requestAnimationFrame(tick);
    }
    requestAnimationFrame(tick);
  });
  try {
    const labels = window.SUPERADMIN_ROLE_LABELS || [];
    const series = window.SUPERADMIN_ROLE_DATA || [];
    if (window.ApexCharts && document.querySelector('#roleChart') && series.some((v) => v > 0)) {
      new ApexCharts(document.querySelector('#roleChart'), {
        chart: { type: 'donut', height: 220 },
        labels: labels, series: series,
        legend: { position: 'bottom', fontSize: '11px' },
        colors: ['#7c3aed', '#2563eb', '#059669', '#f59e0b'],
        dataLabels: { enabled: true }
      }).render();
    } else if (document.querySelector('#roleChart')) {
      document.querySelector('#roleChart').innerHTML = '<p class="text-xs text-slate-400">Chart unavailable offline.</p>';g
    }
  } catch (e) { console.warn(e); }
});
