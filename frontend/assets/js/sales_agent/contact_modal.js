function openViewModal(lead) {
  // 1. POPULATE LEAD DETAILS
  document.getElementById("modalCompany").innerText = lead.company_name || "Individual Client";
  document.getElementById("modalCode").innerText = lead.inquiry_code || "INQ-CODE";
  document.getElementById("modalContact").innerText = lead.contact_person || "No contact person";
  document.getElementById("modalEmail").innerText = lead.email || "N/A";
  document.getElementById("modalPhone").innerText = lead.phone_number || "N/A";
  document.getElementById("modalPlatform").innerText = lead.platform_used || "N/A";
  document.getElementById("modalService").innerText = lead.service_type || "N/A";
  document.getElementById("modalRoute").innerText = (lead.origin || "N/A") + " ➔ " + (lead.destination || "N/A");
  
  // Populate live status badge sa header
  const status = lead.status || "new_inquiry";
  const badge = document.getElementById("modalStatusBadge");
  if (badge) {
    badge.innerText = status.replace(/_/g, " ");
    badge.classList.remove("hidden");
  }

  const cargoInput = document.getElementById("modalCargo");
  if (cargoInput) {
    cargoInput.value = lead.cargo_details || lead.initial_inquiry_text || "";
  }

  // 2. GMAIL / EMAIL LINK SETUP
  const emailBtn = document.getElementById("contactModalEmailBtn");
  const emailText = document.getElementById("contactModalEmailText");
  
  if (lead.email) {
    emailBtn.href = `https://mail.google.com/mail/?view=cm&fs=1&to=${encodeURIComponent(lead.email)}`;
    emailText.innerText = lead.email;
    emailBtn.classList.remove("opacity-50", "pointer-events-none");
  } else {
    emailBtn.href = "#";
    emailText.innerText = "No email provided";
    emailBtn.classList.add("opacity-50", "pointer-events-none");
  }

  // 3. PHONE LINK SETUP
  const phoneBtn = document.getElementById("contactModalPhoneBtn");
  const phoneText = document.getElementById("contactModalPhoneText");
  
  if (lead.phone_number) {
    phoneBtn.href = `tel:${lead.phone_number}`;
    phoneText.innerText = lead.phone_number;
    phoneBtn.classList.remove("opacity-50", "pointer-events-none");
  } else {
    phoneBtn.href = "#";
    phoneText.innerText = "No phone provided";
    phoneBtn.classList.add("opacity-50", "pointer-events-none");
  }

  // 4. POPULATE FORM FIELDS
  document.getElementById("modalLeadId").value = lead.id || "";
  document.getElementById("modalStatusSelect").value = status;
  document.getElementById("modalPriceInput").value = lead.agreed_price || lead.estimated_amount || "";

  // Pickup fields (kung mayroon na)
  const pickupAddr = document.getElementById("modalPickupAddress");
  const pickupDt = document.getElementById("modalPickupDateTime");
  if (pickupAddr) pickupAddr.value = lead.pickup_address || "";
  if (pickupDt) pickupDt.value = lead.pickup_datetime ? lead.pickup_datetime.slice(0, 16) : "";

  // Tiyakin kung dapat bang nakatago o nakalitaw ang Pickup fields
  if (typeof togglePickupFields === "function") togglePickupFields(status);

  // 5. SHOW MODAL
  const modal = document.getElementById("viewModal");
  modal.classList.remove("hidden");
  modal.classList.add("flex");
}

function closeViewModal() {
  const modal = document.getElementById("viewModal");
  modal.classList.add("hidden");
  modal.classList.remove("flex");
}