document.addEventListener('DOMContentLoaded', () => {
  const q = document.getElementById('accSearch');
  const role = document.getElementById('accRole');
  const rows = Array.from(document.querySelectorAll('.acc-row'));
  const empty = document.getElementById('accEmpty');
  function apply() {
    const s = (q?.value || '').toLowerCase().trim();
    const r = role?.value || '';
    let n = 0;
    rows.forEach((tr) => {
      const okS = !s || (tr.dataset.search || '').includes(s);
      const okR = !r || tr.dataset.role === r;
      const show = okS && okR;
      tr.style.display = show ? '' : 'none';
      if (show) n++;
    });
    if (empty) empty.classList.toggle('hidden', n > 0);
  }
  q?.addEventListener('input', apply);
  role?.addEventListener('change', apply);
  document.getElementById('genPass')?.addEventListener('click', () => {
    const chars = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789!@#$%';
    let p = '';
    for (let i = 0; i < 12; i++) p += chars[Math.floor(Math.random() * chars.length)];
    document.getElementById('f_pass').value = p;
  });
  document.getElementById('createAccountForm')?.addEventListener('submit', (e) => {
    e.preventDefault();
    const fn = document.getElementById('f_first').value.trim();
    const ln = document.getElementById('f_last').value.trim();
    const em = document.getElementById('f_email').value.trim();
    const rl = document.getElementById('f_role').value;
    const co = document.getElementById('f_company').value.trim() || 'Priority Handling';
    const pw = document.getElementById('f_pass').value;
    const msg = document.getElementById('formMsg');
    if (!fn || !ln || !em || pw.length < 8) {
      if (msg) { msg.textContent = 'Complete all fields. Password min 8 chars.'; msg.className = 'text-[11px] font-bold text-rose-600'; }
      return;
    }
    const body = document.getElementById('accBody');
    const tr = document.createElement('tr');
    tr.className = 'acc-row hover:bg-slate-50';
    tr.dataset.search = (fn + ' ' + ln + ' ' + em + ' demo').toLowerCase();
    tr.dataset.role = rl;
    const esc = (s) => s.replace(/&/g, '&amp;').replace(/</g, '&lt;');
    tr.innerHTML = '<td class="px-5 py-3 font-bold text-slate-800">' + esc(fn + ' ' + ln) + '<span class="block text-[10px] font-semibold text-slate-400">DEMO-NEW</span></td>'
      + '<td class="px-5 py-3 text-slate-500">' + esc(em) + '</td>'
      + '<td class="px-5 py-3"><span class="px-2 py-1 rounded-full bg-slate-100 border border-slate-200 text-[10px] font-bold">' + esc(rl) + '</span></td>'
      + '<td class="px-5 py-3 font-mono">0/5</td>'
      + '<td class="px-5 py-3"><span class="px-2 py-1 rounded-full text-[10px] font-bold border bg-emerald-100 text-emerald-700 border-emerald-200">active</span></td>'
      + '<td class="px-5 py-3 text-slate-400">just now</td>';
    body?.prepend(tr);
    rows.unshift(tr);
    apply();
    e.target.reset();
    if (msg) { msg.textContent = 'Demo account added to table only (no backend write).'; msg.className = 'text-[11px] font-bold text-emerald-600'; }
  });
});
