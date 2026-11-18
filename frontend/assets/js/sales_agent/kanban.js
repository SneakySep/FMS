document.addEventListener("DOMContentLoaded", function () {

    // Fetch Kanban Board Data
    fetchKanbanData();
    initDragAndDrop();

    async function fetchKanbanData() {
        try {
            
            const res = await fetch(`${window.APP_CONFIG.API_BASE_URL}/api/sales/kanban`, {
                method: "GET",
                headers: {
                    "Authorization": `Bearer ${window.APP_CONFIG.ACCESS_TOKEN}`,
                    "Content-Type": "application/json"
                }
            });
            if (!res.ok) throw new Error("Failed to fetch kanban data");
            
            const result = await res.json();
            console.log("Kanban API Response:", result); 

            
            const columnsData = result.data || result;
            if (columnsData) {
                renderBoard(columnsData);
            }
        } catch (err) {
            console.error("Kanban fetch error:", err);
        }
    }

    function renderBoard(columnsData) {
        const statuses = ["new_inquiry", "qualifying", "negotiation", "closed_won", "closed_lost"];

        statuses.forEach(statusKey => {
            // Kunin nang diretso ang array mula sa key (hal. columnsData['new_inquiry'])
            const items = columnsData[statusKey] || [];

            const countEl = document.getElementById(`count-${statusKey}`);
            if (countEl) countEl.innerText = items.length;

            const container = document.getElementById(`col-${statusKey}`);
            if (container) {
                if (Array.isArray(items) && items.length > 0) {
                    container.innerHTML = items.map(item => createCardHTML(item)).join("");
                } else {
                    container.innerHTML = `<div class="text-center py-6 text-xs text-slate-400">Wala pang data</div>`;
                }
            }
        });
    }

    function initDragAndDrop() {
        const dropzones = document.querySelectorAll(".kanban-dropzone");

        dropzones.forEach(zone => {
            new Sortable(zone, {
                group: "sales-kanban",
                animation: 200,
                ghostClass: "opacity-40",
                dragClass: "rotate-1",
                onEnd: async function (evt) {
                    const itemEl = evt.item;
                    const newZone = evt.to;
                    const oldZone = evt.from;

                    const inquiryId = itemEl.getAttribute("data-id");
                    const newStatus = newZone.id.replace("col-", "") || newZone.getAttribute("data-status");
                    const oldStatus = oldZone.id.replace("col-", "") || oldZone.getAttribute("data-status");

                    console.log("Dragging ID:", inquiryId, "From:", oldStatus, "To:", newStatus);

                    if (newStatus === oldStatus) return;

                    updateColumnCounts();

                    try {
                        const patchUrl = `${window.APP_CONFIG.API_BASE_URL}/api/sales/kanban/${inquiryId}/status`;
                        console.log("Fetching URL:", patchUrl);

                        const patchRes = await fetch(patchUrl, {
                            method: "PATCH",
                            headers: {
                                "Content-Type": "application/json",
                                "Authorization": `Bearer ${window.APP_CONFIG.ACCESS_TOKEN}`
                            },
                            body: JSON.stringify({ status: newStatus })
                        });

                        // Kunin ang response text para makita kung anong eksaktong sagot ng backend
                        const responseText = await patchRes.text();
                        console.log("Raw Server Response Status:", patchRes.status);
                        console.log("Raw Server Response Text:", responseText);

                        let result;
                        try {
                            result = JSON.parse(responseText);
                        } catch (e) {
                            throw new Error("Hindi balidong JSON ang ibinalik ng server: " + responseText);
                        }

                        if (!patchRes.ok || (result.success === false)) {
                            throw new Error(result.detail || result.message || "Nabigo ang pag-update ng status.");
                        }

                        console.log("Matagumpay na na-update ang status sa database!");
                    } catch (err) {
                        console.error("DETAILED PATCH ERROR:", err);
                        alert("Error: " + err.message); // Ipapakita na nito ang eksaktong error message
                        oldZone.appendChild(itemEl);
                        updateColumnCounts();
                    }
                }
            });
        });
    }

    function updateColumnCounts() {
        const statuses = ["new_inquiry", "qualifying", "negotiation", "closed_won", "closed_lost"];
        statuses.forEach(statusKey => {
            const container = document.getElementById(`col-${statusKey}`);
            const countEl = document.getElementById(`count-${statusKey}`);
            if (container && countEl) {
                countEl.innerText = container.querySelectorAll(".kanban-card").length;
            }
        });
    }
});

// CARD HTML TEMPLATE
function createCardHTML(item) {
    const contactName = item.contact_person || item.client_name || "Unknown Contact";
    const companyName = item.company_name || item.company || "Client Inquiry";
    const cargoDetails = item.cargo_details || item.description || item.service_type || "No cargo details provided.";
    const pickupAddress = item.pickup_address || item.origin || "N/A";
    const email = item.email || "N/A";
    const phone = item.phone_number || item.phone || "N/A";
    const amount = item.estimated_amount ? Number(item.estimated_amount).toLocaleString() : "0";

    const initial = contactName.charAt(0).toUpperCase();
    const formattedDate = item.created_at
        ? new Date(item.created_at).toLocaleDateString("en-GB", { day: "numeric", month: "short" })
        : "No date";

    return `
    <div id="card-${item.id}" data-id="${item.id}" class="kanban-card bg-white text-[#1A1A1A] p-5 rounded-2xl shadow-sm border border-[#E5E5DF] hover:shadow-md transition-all duration-200 cursor-grab active:cursor-grabbing flex flex-col justify-between gap-4">
      
      <div class="flex items-start justify-between gap-2">
        <h5 id="title-${item.id}" class="font-semibold text-base tracking-tight text-[#1A1A1A] leading-snug">${companyName}</h5>
        <button type="button" onclick="toggleCardDetails('${item.id}')" id="btn-${item.id}" class="text-[#666666] hover:text-[#1A1A1A] p-1 rounded-lg hover:bg-[#F0F0EC] transition-colors flex-shrink-0">
          <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
            <path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/>
          </svg>
        </button>
      </div>

      <p id="desc-${item.id}" class="text-xs text-[#666666] leading-relaxed font-normal line-clamp-2">
        ${cargoDetails}
      </p>

      <div id="details-${item.id}" class="hidden flex-col gap-2.5 pt-3 border-t border-gray-700 text-xs text-gray-300">
        <div class="flex items-start gap-2">
          <svg class="w-4 h-4 text-gray-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
          </svg>
          <span class="break-words">${pickupAddress}</span>
        </div>

        <div class="flex items-center gap-2">
          <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
          </svg>
          <span class="truncate">${email}</span>
        </div>

        <div class="flex items-center gap-2">
          <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
          </svg>
          <span>${phone}</span>
        </div>
      </div>

      <div class="flex items-center gap-3 pt-1">
        <div id="avatar-${item.id}" class="w-8 h-8 rounded-full bg-[#1A1A1A] text-white flex items-center justify-center font-medium text-xs flex-shrink-0 transition-colors">
          ${initial}
        </div>
        <div class="flex flex-col">
          <span id="label-${item.id}" class="text-[10px] text-[#888888] uppercase tracking-wider font-semibold">Contact Person</span>
          <span id="person-${item.id}" class="text-xs font-semibold text-[#1A1A1A]">${contactName}</span>
        </div>
      </div>

      <div id="footer-${item.id}" class="flex items-center justify-between border-t border-[#F0F0EC] pt-3 text-xs text-[#666666]">
        <div id="date-badge-${item.id}" class="flex items-center gap-1.5 border border-[#E5E5DF] px-2.5 py-1 rounded-lg bg-white">
          <svg class="w-3.5 h-3.5 text-[#666666]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
          </svg>
          <span class="text-xs font-medium">${formattedDate}</span>
        </div>

        <span id="amount-${item.id}" class="text-xs font-semibold text-emerald-600">
          ₱${amount}
        </span>
      </div>

    </div>
    `;
}

// TOGGLE DETAILS
function toggleCardDetails(id) {
    const card = document.getElementById(`card-${id}`);
    const details = document.getElementById(`details-${id}`);
    const title = document.getElementById(`title-${id}`);
    const desc = document.getElementById(`desc-${id}`);
    const avatar = document.getElementById(`avatar-${id}`);
    const person = document.getElementById(`person-${id}`);
    const label = document.getElementById(`label-${id}`);
    const footer = document.getElementById(`footer-${id}`);
    const dateBadge = document.getElementById(`date-badge-${id}`);
    const btn = document.getElementById(`btn-${id}`);

    if (!card || !details) return;

    const isExpanded = !details.classList.contains("hidden");

    if (isExpanded) {
        details.classList.add("hidden");
        details.classList.remove("flex");

        card.className = "kanban-card bg-white text-[#1A1A1A] p-5 rounded-2xl shadow-sm border border-[#E5E5DF] hover:shadow-md transition-all duration-200 cursor-grab active:cursor-grabbing flex flex-col justify-between gap-4";
        title.className = "font-semibold text-base tracking-tight text-[#1A1A1A] leading-snug";
        desc.className = "text-xs text-[#666666] leading-relaxed font-normal line-clamp-2";
        avatar.className = "w-8 h-8 rounded-full bg-[#1A1A1A] text-white flex items-center justify-center font-medium text-xs flex-shrink-0 transition-colors";
        person.className = "text-xs font-semibold text-[#1A1A1A]";
        label.className = "text-[10px] text-[#888888] uppercase tracking-wider font-semibold";
        footer.className = "flex items-center justify-between border-t border-[#F0F0EC] pt-3 text-xs text-[#666666]";
        dateBadge.className = "flex items-center gap-1.5 border border-[#E5E5DF] px-2.5 py-1 rounded-lg bg-white";
        btn.className = "text-[#666666] hover:text-[#1A1A1A] p-1 rounded-lg hover:bg-[#F0F0EC] transition-colors flex-shrink-0";
    } else {
        details.classList.remove("hidden");
        details.classList.add("flex");

        card.className = "kanban-card bg-[#1A1A1A] text-white p-5 rounded-2xl shadow-lg border border-[#2A2A2A] transition-all duration-200 cursor-grab active:cursor-grabbing flex flex-col justify-between gap-4";
        title.className = "font-semibold text-base tracking-tight text-white leading-snug";
        desc.className = "text-xs text-gray-300 leading-relaxed font-normal";
        avatar.className = "w-8 h-8 rounded-full bg-[#2A2A2A] border border-[#3E3E3E] text-white flex items-center justify-center font-medium text-xs flex-shrink-0 transition-colors";
        person.className = "text-xs font-semibold text-gray-200";
        label.className = "text-[10px] text-gray-400 uppercase tracking-wider font-semibold";
        footer.className = "flex items-center justify-between border-t border-[#2A2A2A] pt-3 text-xs text-gray-400";
        dateBadge.className = "flex items-center gap-1.5 border border-[#333333] px-2.5 py-1 rounded-lg bg-[#222222]";
        btn.className = "text-gray-400 hover:text-white p-1 rounded-lg hover:bg-[#2A2A2A] transition-colors flex-shrink-0";
    }
}