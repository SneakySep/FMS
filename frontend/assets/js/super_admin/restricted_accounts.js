document.addEventListener('DOMContentLoaded', () => {
  const q = document.getElementById('resSearch');
  const st = document.getElementById('resStatus');
  const rows = Array.from(document.querySelectorAll('.res-row'));
  const empty = document.getElementById('resEmpty');
  const KEY = 'superadmin_restrict_overrides_v1';
  const overrides = JSON.parse(localStorage.getItem(KEY) || '{}');
  function paint(tr) {
    const id = tr.dataset.id;
    if (overrides[id]) {
      const pill = tr.querySelector('.res-pill');
      const btn = tr.querySelector('.res-toggle');
      if (pill) {
        pill.textContent = overrides[id];
        pill.className = 'res-pill px-2 py-1 rounded-full text-[10px] font-bold border ' + (overrides[id] === 'restricted' ? 'bg-rose-100 text-rose-700 border-rose-200' : 'bg-emerald-100 text-emerald-700 border-emerald-200');
      }
      if (btn) btn.textContent = overrides[id] === 'restricted' ? 'Unrestrict' : 'Restrict';
      tr.dataset.status = overrides[id];
    }
  }
  rows.forEach(paint);
  function apply() {
    const s = (q?.value || '').toLowerCase().trim();
    const f = st?.value || '';
    let n = 0;
    rows.forEach((tr) => {
      const ok = (!s || (tr.dataset.search || '').includes(s)) && (!f || tr.dataset.status === f);
      tr.style.display = ok ? '' : 'none';
      if (ok) n++;
    });
    if (empty) empty.classList.toggle('hidden', n > 0);
  }
  q?.addEventListener('input', apply);
  st?.addEventListener('change', apply);
  document.querySelectorAll('.res-toggle').forEach((btn) => {
    btn.addEventListener('click', () => {
      const tr = btn.closest('.res-row');
      const id = tr.dataset.id;
      const cur = tr.dataset.status === 'restricted' ? 'restricted' : 'active';
      const next = cur === 'restricted' ? 'active' : 'restricted';
      if (!confirm('Demo-only: set ' + id + ' to ' + next + '?')) return;
      overrides[id] = next;
      localStorage.setItem(KEY, JSON.stringify(overrides));
      paint(tr); apply();
    });
  });
});
