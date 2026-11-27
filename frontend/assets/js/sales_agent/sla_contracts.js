document.addEventListener("DOMContentLoaded", function () {

    const lookupForm   = document.getElementById("slaLookupForm");
    const inquiryInput = document.getElementById("slaInquiryInput");
    const tableBody    = document.getElementById("slaTableBody");
    const tableWrap    = document.getElementById("slaTableWrap");
    const emptyState   = document.getElementById("slaEmptyState");

    // Kapag pinindot ang "Check", i-intercept ang submit at gamitin ang client-side fetch
    // papunta sa GET /api/sales/sla/check/{inquiry_id}
    if (lookupForm) {
        lookupForm.addEventListener("submit", function (event) {
            event.preventDefault();
            const inquiryId = (inquiryInput?.value || "").trim();
            if (!inquiryId) return;
            checkSla(inquiryId);
        });
    }

    async function checkSla(inquiryId) {
        setLoading(true);
        try {
            const res = await fetch(`${window.APP_CONFIG.API_BASE_URL}/api/sales/sla/check/${encodeURIComponent(inquiryId)}`, {
                method: "GET",
                headers: {
                    "Authorization": `Bearer ${window.APP_CONFIG.ACCESS_TOKEN}`,
                    "Content-Type": "application/json"
                }
            });

            const result = await res.json().catch(() => ({}));

            if (!res.ok) {
                throw new Error(result.detail || result.message || "Nabigo ang pag-check ng SLA.");
            }

            renderResult(inquiryId, result);
        } catch (err) {
            console.error("SLA check error:", err);
            showError(err.message);
        } finally {
            setLoading(false);
        }
    }

    function renderResult(inquiryId, result) {
        const exists   = !!result.exists;
        const contract = result.data || null;

        updateStats(exists, contract);

        if (!exists || !contract) {
            if (tableWrap) tableWrap.classList.add("hidden");
            if (tableBody) tableBody.innerHTML = "";
            showEmpty(inquiryId);
            return;
        }

        if (emptyState) emptyState.classList.add("hidden");
        if (tableWrap) tableWrap.classList.remove("hidden");
        if (tableBody) tableBody.innerHTML = createRowHTML(inquiryId, contract);
    }

    function createRowHTML(inquiryId, item) {
        const contractNo = item.contract_no || "—";
        const statusRaw  = item.status || "—";
        const created    = item.created_at
            ? new Date(item.created_at).toLocaleDateString("en-GB", { day: "numeric", month: "short", year: "numeric" })
            : "—";
        const pdfPath    = item.pdf_path || "";
        const pdfName    = pdfPath ? pdfPath.split("/").pop() : "";

        const pdfCell = pdfPath
            ? `<span class="inline-flex items-center gap-1.5 max-w-[220px] truncate" title="${escapeHtml(pdfPath)}">
                   <i class="fa-solid fa-file-pdf text-rose-500"></i>
                   <span class="truncate">${escapeHtml(pdfName)}</span>
               </span>`
            : `<span class="text-slate-400 italic">Not generated</span>`;

        const actionCell = pdfPath
            ? `<button type="button" data-copy-pdf="${escapeHtml(pdfPath)}" onclick="copySlaPdfPath(this)"
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-[11px] font-semibold transition">
                   <i class="fa-regular fa-copy"></i> Copy Path
               </button>`
            : `<span class="text-slate-300">—</span>`;

        return `
            <tr>
                <td class="py-3 px-4 font-semibold text-slate-800">${escapeHtml(contractNo)}</td>
                <td class="py-3 px-4 text-slate-600">${escapeHtml(inquiryId)}</td>
                <td class="py-3 px-4">
                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg border text-[11px] font-semibold capitalize ${statusBadgeClass(statusRaw)}">
                        ${escapeHtml(statusRaw)}
                    </span>
                </td>
                <td class="py-3 px-4 text-slate-600">${escapeHtml(created)}</td>
                <td class="py-3 px-4 text-slate-600">${pdfCell}</td>
                <td class="py-3 px-4 text-center">${actionCell}</td>
            </tr>`;
    }

    function updateStats(exists, contract) {
        const resultEl = document.getElementById("stat-result");
        const noEl     = document.getElementById("stat-contract-no");
        const statusEl = document.getElementById("stat-status");
        const pdfEl    = document.getElementById("stat-pdf");

        if (resultEl) {
            resultEl.textContent = exists ? "Found" : "Not Found";
            resultEl.className = `text-xl font-extrabold ${exists ? "text-emerald-600" : "text-rose-600"}`;
        }
        if (noEl) noEl.textContent = (contract && contract.contract_no) || "—";
        if (statusEl) statusEl.textContent = (contract && contract.status) || "—";
        if (pdfEl) {
            const hasPdf = !!(contract && contract.pdf_path);
            pdfEl.textContent = hasPdf ? "Yes" : "—";
            pdfEl.className = `text-xl font-extrabold ${hasPdf ? "text-emerald-600" : "text-slate-900"}`;
        }
    }

    function showEmpty(inquiryId) {
        if (!emptyState) return;
        emptyState.classList.remove("hidden");
        emptyState.innerHTML = `
            <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-500 flex items-center justify-center mx-auto mb-3">
                <i class="fa-solid fa-file-circle-xmark text-lg"></i>
            </div>
            <h4 class="text-sm font-bold text-slate-800">Walang SLA Contract na nahanap</h4>
            <p class="text-xs text-slate-500 mt-1">Walang existing na SLA contract para sa Inquiry ID <span class="font-semibold text-slate-700">${escapeHtml(inquiryId)}</span>.</p>`;
    }

    function showError(message) {
        if (emptyState) {
            emptyState.classList.remove("hidden");
            emptyState.innerHTML = `
                <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-500 flex items-center justify-center mx-auto mb-3">
                    <i class="fa-solid fa-triangle-exclamation text-lg"></i>
                </div>
                <h4 class="text-sm font-bold text-slate-800">Hindi makapag-check ng SLA</h4>
                <p class="text-xs text-slate-500 mt-1">${escapeHtml(message)}</p>`;
        }
        if (tableWrap) tableWrap.classList.add("hidden");
    }

    function setLoading(isLoading) {
        const btn = lookupForm?.querySelector("button[type='submit']");
        if (!btn) return;
        btn.disabled = isLoading;
        btn.classList.toggle("opacity-70", isLoading);
    }

    function statusBadgeClass(status) {
        switch (String(status).toLowerCase().trim()) {
            case "active":   return "bg-emerald-100 text-emerald-700 border-emerald-200";
            case "pending":  return "bg-amber-100 text-amber-700 border-amber-200";
            case "expired":  return "bg-rose-100 text-rose-700 border-rose-200";
            case "cancelled":
            case "canceled": return "bg-slate-100 text-slate-700 border-slate-200";
            default:         return "bg-blue-100 text-blue-700 border-blue-200";
        }
    }

    function escapeHtml(value) {
        const div = document.createElement("div");
        div.textContent = value ?? "";
        return div.innerHTML;
    }
});

// COPY PDF PATH — global para magamit ng inline onclick sa table rows
function copySlaPdfPath(button) {
    const path = button?.getAttribute("data-copy-pdf") || "";
    if (!path) return;

    navigator.clipboard.writeText(path).then(() => {
        const original = button.innerHTML;
        button.innerHTML = '<i class="fa-solid fa-check"></i> Copied';
        setTimeout(() => { button.innerHTML = original; }, 1500);
    }).catch((err) => {
        console.error("Copy error:", err);
        alert("Hindi ma-copy ang PDF path.");
    });
}