document.addEventListener('DOMContentLoaded', () => {
  const q = document.getElementById('auditSearch');
  const sev = document.getElementById('auditSev');
  const cat = document.getElementById('auditCat');
  const rows = Array.from(document.querySelectorAll('.audit-row'));
  const empty = document.getElementById('auditEmpty');
  const KEY = 'superadmin_audit_read_v1';
  const read = JSON.parse(localStorage.getItem(KEY) || '[]');
  rows.forEach((tr) => {
    if (read.includes(tr.dataset.id)) {
      const s = tr.querySelector('.audit-state');
      if (s) { s.textContent = 'READ'; s.className = 'audit-state px-2 py-1 rounded-full text-[10px] font-bold border bg-emerald-100 text-emerald-700 border-emerald-200'; }
    }
  });
  function apply() {
    const s = (q?.value || '').toLowerCase().trim();
    const fS = sev?.value || '';
    const fC = cat?.value || '';
    let n = 0;
    rows.forEach((tr) => {
      const ok = (!s || (tr.dataset.search || '').includes(s)) && (!fS || tr.dataset.sev === fS) && (!fC || tr.dataset.cat === fC);
      tr.style.display = ok ? '' : 'none';
      if (ok) n++;
    });
    if (empty) empty.classList.toggle('hidden', n > 0);
  }
  q?.addEventListener('input', apply);
  sev?.addEventListener('change', apply);
  cat?.addEventListener('change', apply);
  document.getElementById('auditRead')?.addEventListener('click', () => {
    const ids = rows.map((tr) => tr.dataset.id);
    localStorage.setItem(KEY, JSON.stringify(ids));
    rows.forEach((tr) => {
      const s = tr.querySelector('.audit-state');
      if (s) { s.textContent = 'READ'; s.className = 'audit-state px-2 py-1 rounded-full text-[10px] font-bold border bg-emerald-100 text-emerald-700 border-emerald-200'; }
    });
  });
  document.getElementById('auditExport')?.addEventListener('click', () => {
    const vis = rows.filter((tr) => tr.style.display !== 'none');
    const head = 'id,timestamp,actor,action,resource,severity,category,ip,details,state';
    const esc = (v) => '"' + String(v ?? '').replace(/"/g, '""') + '"';
    const lines = [head];
    vis.forEach((tr) => {
      const tds = tr.querySelectorAll('td');
      lines.push([tr.dataset.id, tds[0]?.innerText, tds[1]?.innerText.replace(/\n/g, ' '), tds[2]?.innerText, tds[3]?.innerText, tr.dataset.sev, tr.dataset.cat, tds[5]?.innerText, tds[6]?.innerText.replace(/\n/g, ' '), tr.querySelector('.audit-state')?.innerText].map(esc).join(','));
    });
    const blob = new Blob([lines.join('\n')], { type: 'text/csv' });
    const a = document.createElement('a');
    a.href = URL.createObjectURL(blob);
    a.download = 'audit_trail_masked.csv';
    a.click();
    URL.revokeObjectURL(a.href);
  });
});
