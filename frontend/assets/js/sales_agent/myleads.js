const API_URL = window.APP_CONFIG.API_BASE_URL;

// Global reference to the currently opened lead for modal operations
var _currentLead = null;

document.addEventListener('DOMContentLoaded', () => {
  const statusSelect = document.getElementById('modalStatusSelect');
  if (statusSelect) {
    statusSelect.addEventListener('change', function () {
      togglePickupFields(this.value);
      syncStepper();
    });
  }

  const priceInput = document.getElementById('modalPriceInput');
  if (priceInput) {
    priceInput.addEventListener('input', function () {
      const val = parseFloat(this.value) || 0;
      updateValueChip(val);
    });
  }
});

const STAGE_ORDER = ['new_inquiry', 'qualifying', 'quote_sent', 'negotiation', 'closed_won', 'closed_lost'];

function normalizeStatus(st) {
  if (!st) return 'new_inquiry';
  let clean = String(st).toLowerCase().trim().replace(/[\s-]+/g, '_');
  if (clean === 'quote') clean = 'quote_sent';
  if (clean === 'qualify') clean = 'qualifying';
  if (clean === 'negotiate') clean = 'negotiation';
  if (clean === 'won') clean = 'closed_won';
  if (clean === 'lost') clean = 'closed_lost';
  return clean;
}

function stageOrder(stage) {
  const clean = normalizeStatus(stage);
  const idx = STAGE_ORDER.indexOf(clean);
  return idx !== -1 ? idx : 0;
}

function updateValueChip(amount) {
  const chip = document.getElementById('modalValueChip');
  if (chip) {
    const val = parseFloat(amount) || 0;
    chip.innerText = '₱' + val.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
  }
}

function syncStepper() {
  const select = document.getElementById('modalStatusSelect');
  if (!select) return;

  const current = normalizeStatus(select.value);
  const activeIdx = stageOrder(current);
  const steps = document.querySelectorAll('#viewModal .lm-step');

  steps.forEach(el => {
    const stageAttr = normalizeStatus(el.getAttribute('data-stage'));
    const idx = stageOrder(stageAttr);

    el.classList.remove('is-active', 'is-done');
    if (idx === activeIdx) {
      el.classList.add('is-active');
    } else if (idx < activeIdx) {
      el.classList.add('is-done');
    }
  });
}

function togglePickupFields(status) {
  const pickupSection = document.getElementById('pickupFieldsSection');
  if (!pickupSection) return;

  if (normalizeStatus(status) === 'closed_won') {
    pickupSection.classList.remove('hidden');
  } else {
    pickupSection.classList.add('hidden');
  }
}

function generateInitials(name) {
  if (!name || name === 'N/A') return '--';
  const cleanName = name.trim();
  const words = cleanName.split(/\s+/);
  if (words.length >= 2) {
    return (words[0][0] + words[1][0]).toUpperCase();
  }
  return cleanName.substring(0, 2).toUpperCase();
}

function restrictStatusDropdown(currentStatus) {
  const select = document.getElementById('modalStatusSelect');
  if (!select) return;

  const currentIdx = stageOrder(currentStatus);

  Array.from(select.options).forEach(option => {
    const optionIdx = stageOrder(option.value);
    if (optionIdx < currentIdx && normalizeStatus(option.value) !== 'closed_lost') {
      option.disabled = true;
      if (!option.innerText.includes('(Locked)')) {
        option.innerText = option.innerText + ' (Locked)';
      }
    } else {
      option.disabled = false;
      option.innerText = option.innerText.replace(' (Locked)', '');
    }
  });
}

function openViewModal(lead) {
  console.log("OPENING MODAL WITH DATA:", lead);
  _currentLead = lead;
  document.getElementById('modalLeadId').value = lead.id || lead.lead_id || '';
  const company = lead.company_name || lead.company || 'N/A';
  document.getElementById('modalCompany').innerText = company;
  document.getElementById('modalCode').innerText = lead.inquiry_code || lead.code || 'INQ-' + (lead.id || '');
  const avatarEl = document.getElementById('modalAvatar');
  if (avatarEl) {
    avatarEl.innerText = generateInitials(company);
  }
  const origin = lead.origin || 'N/A';
  const destination = lead.destination || 'N/A';
  const route = origin + ' → ' + destination;
  document.getElementById('modalRoute').innerText = route;
  document.getElementById('modalService').innerText = lead.service_type || 'General Freight';
  document.getElementById('modalCargo').value = lead.cargo_details || lead.initial_inquiry_text || '';
  const email = lead.email || 'N/A';
  const phone = lead.phone_number || lead.phone || 'N/A';
  const contactEmail = document.getElementById('modalEmail');
  const contactEmailBtn = document.getElementById('contactModalEmailBtn');
  const contactEmailText = document.getElementById('contactModalEmailText');
  if (contactEmail) {
    contactEmail.innerText = email;
    contactEmailBtn.href = (email !== 'N/A') ? 'mailto:' + email : '#';
  }
  if (contactEmailText) {
    contactEmailText.innerText = email;
    contactEmailBtn.href = (email !== 'N/A') ? 'mailto:' + email : '#';
  }
  const contactPhone = document.getElementById('modalPhone');
  const contactPhoneBtn = document.getElementById('contactModalPhoneBtn');
  const contactPhoneText = document.getElementById('contactModalPhoneText');
  if (contactPhone) {
    contactPhone.innerText = phone;
    contactPhoneBtn.href = (phone !== 'N/A') ? 'tel:' + phone : '#';
  }
  if (contactPhoneText) {
    contactPhoneText.innerText = phone;
    contactPhoneBtn.href = (phone !== 'N/A') ? 'tel:' + phone : '#';
  }
  const cargoElem = document.getElementById('modalCargo');
  if (cargoElem) {
    cargoElem.value = lead.cargo_details || lead.initial_inquiry_text || '';
  }
  const rawPrice = parseFloat(lead.estimated_amount ?? lead.estimated_price ?? lead.agreed_price ?? 0);
  const priceInput = document.getElementById('modalPriceInput');
  if (priceInput) {
    priceInput.value = rawPrice > 0 ? rawPrice : '';
  }
  updateValueChip(rawPrice);
  const currentStatus = normalizeStatus(lead.status);
  const statusSelect = document.getElementById('modalStatusSelect');
  if (statusSelect) {
    statusSelect.value = currentStatus;
  }
  restrictStatusDropdown(currentStatus);
  togglePickupFields(currentStatus);
  syncStepper();
  const modal = document.getElementById('viewModal');
  modal.classList.remove('hidden');
  modal.classList.add('flex');
}

function closeViewModal() {
  const modal = document.getElementById('viewModal');
  modal.classList.add('hidden');
  modal.classList.remove('flex');
}

async function handleStatusUpdate(e) {
  e.preventDefault();
  const leadId = document.getElementById('modalLeadId').value;
  const newStatus = document.getElementById('modalStatusSelect').value;
  const priceVal = document.getElementById('modalPriceInput').value;
  const cargoDetails = document.getElementById('modalCargo')?.value.trim();
  const pickupAddress = document.getElementById('modalPickupAddress')?.value.trim();
  const pickupDateTime = document.getElementById('modalPickupDateTime')?.value;
  if (newStatus === 'closed_won') {
    if (!pickupAddress || !pickupDateTime) {
      if (typeof SwiftAlert !== 'undefined') {
        SwiftAlert.fire({
          icon: 'warning',
          title: 'Missing Pickup Details',
          text: 'Please provide both Pickup Address and Pickup Date & Time before closing as WON.'
        });
      } else {
        alert('Please provide both Pickup Address and Pickup Date & Time before closing as WON.');
      }
      return;
    }
  }
  const payload = {
    status: newStatus,
    estimated_amount: priceVal ? parseFloat(priceVal) : 0,
    estimated_price: priceVal ? parseFloat(priceVal) : 0,
    cargo_details: cargoDetails || null,
    pickup_address: pickupAddress || null,
    pickup_datetime: pickupDateTime ? new Date(pickupDateTime).toISOString() : null
  };
  try {
    const response = await fetch(API_URL + '/api/v1/leads/' + leadId + '/status', {
      method: 'PATCH',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload)
    });
    if (response.ok) {
      closeViewModal();
      location.reload();
    } else {
      const errData = await response.json();
      alert('Update Failed: ' + (errData.detail || 'Could not update lead record.'));
    }
  } catch (err) {
    console.error(err);
    alert('Cannot connect to FastAPI server. Make sure Uvicorn is running!');
  }
}

function openLeadModal() {
  console.log('openLeadModal called');
  try {
    var modal = document.getElementById('leadModal');
    if (modal) {
      modal.classList.remove('hidden');
      modal.classList.add('flex');
      console.log('Lead modal opened');
    } else {
      console.warn('Lead modal element not found');
      alert('Error: Lead modal not found');
    }
  } catch (err) {
    console.error('Error in openLeadModal:', err);
    alert('Error: ' + err.message);
  }
}

function handlePdfQuoteClick(event, lead) {
  event.preventDefault();
  event.stopPropagation();
  console.log('handlePdfQuoteClick called with lead:', lead);
  try {
    var leadData = lead || _currentLead || {};
    console.log('Lead data to pass to openQuoteModal:', leadData);
    openQuoteModal(leadData);
  } catch (err) {
    console.error('Error in handlePdfQuoteClick:', err);
    alert('Error: ' + err.message);
  }
}