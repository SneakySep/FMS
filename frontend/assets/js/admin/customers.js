// =================================================================================================
// Admin Customers – Add New Customer (B2B / B2C) + Details Modal
// =================================================================================================

// Resolve the API base URL from the PHP-injected config, falling back to localhost
var CUSTOMERS_API_BASE = (window.APP_CONFIG && window.APP_CONFIG.API_BASE_URL)
  ? window.APP_CONFIG.API_BASE_URL
  : 'http://127.0.0.1:8000';

// ---------------------------------------------------------------------------
// Details modal
// ---------------------------------------------------------------------------

function setText(id, value) {
  const el = document.getElementById(id);
  if (!el) return;
  const target = el.tagName === 'DIV' && el.querySelector('span') ? el.querySelector('span') : el;
  target.textContent = (value === null || value === undefined || value === '') ? '--' : value;
}

function openDetailsModal(data) {
  if (!data) return;
  setText('modal_first_name', data.first_name);
  setText('modal_last_name', data.last_name);
  setText('modal_email', data.email);
  setText('modal_company', data.company_name);
  setText('modal_phone', data.phone_number);
  setText('modal_created_at', data.created_at);

  const isB2B = data.account_type === 'B2B';
  const typeEl = document.getElementById('modal_account_type');
  if (typeEl) {
    typeEl.textContent = isB2B ? 'Business (B2B)' : 'Individual (B2C)';
    typeEl.className = 'text-xs font-bold p-2.5 rounded-xl border ' + (isB2B
      ? 'bg-sky-50 text-sky-600 border-sky-100'
      : 'bg-amber-50 text-amber-600 border-amber-100');
  }

  const bizBlock = document.getElementById('modal_business_details');
  if (bizBlock) {
    bizBlock.classList.toggle('hidden', !isB2B);
    if (isB2B) {
      setText('modal_tax_id', data.tax_id);
      setText('modal_website', data.website);
      setText('modal_address', data.address);
    }
  }

  document.getElementById('customerDetailsModal').classList.remove('hidden');
}

function closeDetailsModal() {
  document.getElementById('customerDetailsModal').classList.add('hidden');
}

// ---------------------------------------------------------------------------
// Add New Customer modal
// ---------------------------------------------------------------------------

function openAddCustomerModal() {
  document.getElementById('addCustomerForm').reset();
  clearAddCustomerErrors();
  document.getElementById('acm_error').classList.add('hidden');

  // Default to Individual (B2C)
  const indivRadio = document.querySelector('input[name="account_type"][value="individual"]');
  if (indivRadio) indivRadio.checked = true;
  onAccountTypeChange();

  generateCustomerPassword();
  document.getElementById('addCustomerModal').classList.remove('hidden');
}

function closeAddCustomerModal() {
  document.getElementById('addCustomerModal').classList.add('hidden');
}

function selectedAccountType() {
  const checked = document.querySelector('input[name="account_type"]:checked');
  return checked ? checked.value : 'individual';
}

function onAccountTypeChange() {
  const isB2B = selectedAccountType() === 'business';

  // B2B-only fields
  ['acm_company_group', 'acm_contact_person_group', 'acm_business_extras'].forEach(id => {
    const el = document.getElementById(id);
    if (el) el.classList.toggle('hidden', !isB2B);
  });

  // Address label flips between company address and billing address
  const label = document.getElementById('acm_address_label');
  if (label) label.textContent = isB2B ? 'Address' : 'Billing Address';
}

function generateCustomerPassword() {
  const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%';
  let password = '';
  for (let i = 0; i < 12; i++) {
    password += chars.charAt(Math.floor(Math.random() * chars.length));
  }
  document.getElementById('acm_password').value = password;
}

function clearAddCustomerErrors() {
  document.querySelectorAll('#addCustomerForm .acm-invalid').forEach(el => {
    el.classList.remove('acm-invalid');
  });
}

function showAddCustomerError(message) {
  const box = document.getElementById('acm_error');
  box.textContent = message;
  box.classList.remove('hidden');
}

function markFieldInvalid(fieldId) {
  const el = document.getElementById(fieldId);
  if (el) el.classList.add('acm-invalid');
}


// Map a FastAPI 422 detail array to inline field errors + a summary message.
function handleValidationErrors(detail) {
  if (!Array.isArray(detail)) {
    showAddCustomerError(typeof detail === 'string' ? detail : 'Validation failed.');
    return;
  }

  const fieldMap = {
    company_name: 'acm_company_name',
    first_name: 'acm_first_name',
    last_name: 'acm_last_name',
    email: 'acm_email',
    phone_number: 'acm_phone',
    password: 'acm_password',
    contact_person: 'acm_contact_person',
    address: 'acm_address',
    tax_id: 'acm_tax_id',
    website: 'acm_website'
  };

  const messages = [];
  detail.forEach(err => {
    const loc = Array.isArray(err.loc) ? err.loc : [];
    const name = loc[loc.length - 1];
    if (fieldMap[name]) markFieldInvalid(fieldMap[name]);
    messages.push(`${name}: ${err.msg}`);
  });
  showAddCustomerError(messages.join(' | '));
}

function validateAddCustomerForm(payload) {
  clearAddCustomerErrors();
  const errors = [];

  if (!payload.first_name) { errors.push('First name is required.'); markFieldInvalid('acm_first_name'); }
  if (!payload.last_name) { errors.push('Last name is required.'); markFieldInvalid('acm_last_name'); }
  if (!payload.email) { errors.push('Email is required.'); markFieldInvalid('acm_email'); }
  if (!payload.phone_number) {
    errors.push('Phone number is required.');
    markFieldInvalid('acm_phone');
  } else if (!/^\+?[0-9\s-]{7,15}$/.test(payload.phone_number)) {
    errors.push('Phone number must be 7-15 digits.');
    markFieldInvalid('acm_phone');
  }
  if (!payload.password || payload.password.length < 8) {
    errors.push('Password must be at least 8 characters.');
    markFieldInvalid('acm_password');
  }
  if (payload.account_type === 'business' && !payload.company_name) {
    errors.push('Company name is required for a Business account.');
    markFieldInvalid('acm_company_name');
  }

  return errors;
}

async function submitAddCustomer(e) {
  e.preventDefault();

  const payload = {
    account_type: selectedAccountType(),
    company_name: document.getElementById('acm_company_name').value.trim(),
    first_name: document.getElementById('acm_first_name').value.trim(),
    last_name: document.getElementById('acm_last_name').value.trim(),
    email: document.getElementById('acm_email').value.trim(),
    phone_number: document.getElementById('acm_phone').value.trim(),
    password: document.getElementById('acm_password').value,
    contact_person: document.getElementById('acm_contact_person').value.trim(),
    address: document.getElementById('acm_address').value.trim(),
    tax_id: document.getElementById('acm_tax_id').value.trim(),
    website: document.getElementById('acm_website').value.trim()
  };

  // Mirror backend rules client-side for instant feedback
  const errors = validateAddCustomerForm(payload);
  const box = document.getElementById('acm_error');
  if (errors.length) {
    showAddCustomerError(errors.join(' '));
    return false;
  }
  box.classList.add('hidden');

  // B2C: never send the company fields
  if (payload.account_type === 'individual') {
    payload.company_name = '';
    payload.contact_person = '';
    payload.tax_id = '';
    payload.website = '';
  }

  const submitBtn = document.getElementById('acmSubmitBtn');
  submitBtn.disabled = true;
  submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> <span>Creating...</span>';

  try {
    const response = await fetch(CUSTOMERS_API_BASE + '/api/v1/admin/create-customer', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload)
    });

    const result = await response.json();

    if (response.ok) {
      closeAddCustomerModal();
      if (typeof showAlert === 'function') {
        showAlert('Customer created!', `${result.full_name || 'The new account'} can now sign in with the emailed credentials.`);
      }
      window.location.reload(); // Refresh table & sidebar count
    } else if (response.status === 422) {
      handleValidationErrors(result.detail);
    } else {
      showAddCustomerError(result.detail || 'Failed to create the customer account.');
    }
  } catch (err) {
    showAddCustomerError('Server connection failed: ' + err.message);
  } finally {
    submitBtn.disabled = false;
    submitBtn.innerHTML = '<i class="fa-solid fa-user-plus"></i> <span>Create Customer</span>';
  }

  return false;
}

// =============================================================================
// Actions menu + Manage Customer (status) + Delete Customer
//   PATCH /api/v1/admin/customer-accounts/status
//   POST  /api/v1/admin/delete-customer
// =============================================================================

var CUSTOMER_STATUSES = ['Active', 'Inactive', 'Deactivated'];
var manageTarget = null;   // { user_id, email, name, status, statusToApply }
var deleteTarget = null;   // { user_id, email, name }

function closeAllCustomerMenus() {
  document.querySelectorAll('.cstm-menu').forEach(function (m) {
    m.classList.add('hidden');
    m.style.position = '';
    m.style.top = '';
    m.style.left = '';
  });
}

// Basahin ang row payload mula sa data-customer attribute ng wrapper.
// Mas safe kaysa i-inline ang JSON sa bawat onclick handler.
function customerRowData(el) {
  const holder = el && el.closest ? el.closest('[data-customer]') : null;
  if (!holder) return null;
  try {
    return JSON.parse(holder.getAttribute('data-customer'));
  } catch (err) {
    return null;
  }
}

function toggleCustomerActionMenu(event, menuId) {
  event.stopPropagation();
  const menu = document.getElementById(menuId);
  if (!menu) return;

  const wasOpen = !menu.classList.contains('hidden');
  closeAllCustomerMenus();
  if (wasOpen) return;

  // Ang dropdown ay nasa loob ng overflow-x-auto container, kaya inilalagay
  // natin ito bilang fixed para hindi ma-clip ng talahanayan.
  menu.style.position = 'fixed';
  menu.classList.remove('hidden');

  const rect = menu.getBoundingClientRect();
  const trigger = event.currentTarget.getBoundingClientRect();
  let left = trigger.right - rect.width;
  let top = trigger.bottom + 4;

  left = Math.max(8, Math.min(left, window.innerWidth - rect.width - 8));
  if (top + rect.height > window.innerHeight - 8) {
    top = Math.max(8, trigger.top - rect.height - 4);
  }
  menu.style.top = top + 'px';
  menu.style.left = left + 'px';
}

document.addEventListener('click', closeAllCustomerMenus);
window.addEventListener('scroll', closeAllCustomerMenus, true);
window.addEventListener('resize', closeAllCustomerMenus);

// Kung may session token kahit saan, ipadala natin para handa na kapag
// nilagyan na ng auth protection ang mga admin endpoint.
function buildCustomerHeaders() {
  const headers = { 'Content-Type': 'application/json' };
  const token = window.SUPABASE_ACCESS_TOKEN
    || localStorage.getItem('supabase.auth.token')
    || sessionStorage.getItem('access_token');
  if (token) headers['Authorization'] = 'Bearer ' + token;
  return headers;
}

// I-convert ang FastAPI detail (string o 422 array) sa isang readable line.
function readCustomerApiError(result, fallback) {
  const detail = result && result.detail;
  if (typeof detail === 'string') return detail;
  if (Array.isArray(detail)) {
    return detail.map(function (e) {
      const loc = Array.isArray(e.loc) ? e.loc[e.loc.length - 1] : '';
      return loc ? loc + ': ' + e.msg : e.msg;
    }).join(' | ');
  }
  return fallback;
}

// ---------------------------------------------------------------------------
// Manage Customer modal - status change requires an explicit "Apply Changes"
// ---------------------------------------------------------------------------

function openManageCustomerModal(data) {
  if (!data || !data.id) {
    if (typeof showAlert === 'function') {
      showAlert('Missing account id', 'This row has no id yet - refresh the page and try again.', 'warning');
    }
    return;
  }

  manageTarget = {
    user_id: data.id,
    email: (data.email || '').toLowerCase(),
    name: [data.first_name, data.last_name].filter(Boolean).join(' ') || 'Customer',
    status: CUSTOMER_STATUSES.indexOf(data.status) > -1 ? data.status : 'Active'
  };

  document.getElementById('mcm_user_id').value = manageTarget.user_id;
  document.getElementById('mcm_name').textContent = manageTarget.name;
  document.getElementById('mcm_subtitle').textContent = manageTarget.email || 'Customer account';
  document.getElementById('mcm_current_note').innerHTML =
    'Current status: <span class="font-bold text-slate-700">' + manageTarget.status + '</span>';
  document.getElementById('mcm_error').classList.add('hidden');

  selectCustomerStatus(manageTarget.status);
  resetApplyBtn();
  document.getElementById('manageCustomerModal').classList.remove('hidden');
}

function closeManageCustomerModal() {
  document.getElementById('manageCustomerModal').classList.add('hidden');
}

function selectCustomerStatus(status) {
  if (!manageTarget) return;
  document.querySelectorAll('#mcm_choices .mcm-choice').forEach(function (choice) {
    const active = choice.getAttribute('data-status') === status;
    choice.classList.toggle('border-indigo-500', active);
    choice.classList.toggle('bg-indigo-50', active);
    choice.classList.toggle('border-slate-200', !active);

    const radio = choice.querySelector('.mcm-radio');
    if (radio) {
      radio.classList.toggle('border-indigo-500', active);
      radio.classList.toggle('border-slate-300', !active);
      radio.innerHTML = active ? '<span class="w-2 h-2 rounded-full bg-indigo-500"></span>' : '';
    }
  });
  manageTarget.statusToApply = status;
}

function resetApplyBtn() {
  const btn = document.getElementById('mcmApplyBtn');
  btn.disabled = false;
  btn.innerHTML = '<i class="fa-solid fa-check"></i> <span>Apply Changes</span>';
}

function showManageError(message) {
  const box = document.getElementById('mcm_error');
  box.textContent = message;
  box.classList.remove('hidden');
}

// I-render ang bagong status sa row nang hindi nagre-reload, para manatili
// ang toast at ang posisyon ng admin sa listahan.
var STATUS_BADGE_CLASS = {
  'Active':      'bg-emerald-50 text-emerald-600 border-emerald-100',
  'Inactive':    'bg-slate-100 text-slate-500 border-slate-200',
  'Deactivated': 'bg-rose-50 text-rose-600 border-rose-100'
};

function setRowStatus(userId, status) {
  const badge = document.querySelector('[data-status-badge="' + userId + '"]');
  if (badge) {
    badge.className = 'px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider border '
      + (STATUS_BADGE_CLASS[status] || STATUS_BADGE_CLASS['Active']);
    badge.textContent = status;
  }

  const wrapper = document.getElementById('cstm-actions-' + userId);
  if (!wrapper) return;

  // I-update ang nakatagong payload para tamang-gating ang Delete button.
  try {
    const data = JSON.parse(wrapper.getAttribute('data-customer') || '{}');
    data.status = status;
    wrapper.setAttribute('data-customer', JSON.stringify(data));
  } catch (err) { /* panatilihin ang dating payload */ }

  const deactivated = status === 'Deactivated';
  const deleteOn = wrapper.querySelector('[data-delete-option="deactivated"]');
  const deleteOff = wrapper.querySelector('[data-delete-option="locked"]');
  if (deleteOn) deleteOn.hidden = !deactivated;
  if (deleteOff) deleteOff.hidden = deactivated;

  // I-grey-out ang quick-status option na tumpak ang kasalukuyang status.
  wrapper.querySelectorAll('[data-quick-status]').forEach(function (btn) {
    const isCurrent = btn.getAttribute('data-quick-status') === status;
    btn.classList.toggle('opacity-40', isCurrent);
    btn.classList.toggle('pointer-events-none', isCurrent);
  });
}

async function applyCustomerChanges() {
  if (!manageTarget || !manageTarget.user_id) return;

  const status = manageTarget.statusToApply || manageTarget.status;
  if (status === manageTarget.status) {
    showManageError('Choose a different status before applying changes.');
    return;
  }

  const btn = document.getElementById('mcmApplyBtn');
  btn.disabled = true;
  btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> <span>Applying...</span>';
  document.getElementById('mcm_error').classList.add('hidden');

  try {
    const response = await fetch(CUSTOMERS_API_BASE + '/api/v1/admin/customer-accounts/status', {
      method: 'PATCH',
      headers: buildCustomerHeaders(),
      body: JSON.stringify({ user_id: manageTarget.user_id, status: status })
    });
    const result = await response.json();

    if (response.ok) {
      closeManageCustomerModal();
      setRowStatus(manageTarget.user_id, status);
      if (typeof showAlert === 'function') {
        showAlert('Status updated', manageTarget.name + ' is now ' + status + '.');
      }
    } else {
      showManageError(readCustomerApiError(result, 'Could not change the status.'));
      resetApplyBtn();
    }
  } catch (err) {
    showManageError('Server connection failed: ' + err.message);
    resetApplyBtn();
  }
}

// ---------------------------------------------------------------------------
// Quick status change from the dropdown (same endpoint, no modal step)
// ---------------------------------------------------------------------------

async function applyQuickStatus(data, status) {
  closeAllCustomerMenus();
  if (!data || !data.id) {
    if (typeof showAlert === 'function') {
      showAlert('Missing account id', 'This row has no id yet - refresh the page and try again.', 'warning');
    }
    return;
  }

  const label = [data.first_name, data.last_name].filter(Boolean).join(' ') || 'Customer';

  try {
    const response = await fetch(CUSTOMERS_API_BASE + '/api/v1/admin/customer-accounts/status', {
      method: 'PATCH',
      headers: buildCustomerHeaders(),
      body: JSON.stringify({ user_id: data.id, status: status })
    });
    const result = await response.json();

    if (response.ok) {
      setRowStatus(data.id, status);
      if (typeof showAlert === 'function') {
        showAlert('Status updated', label + ' is now ' + status + '.');
      }
    } else {
      if (typeof showAlert === 'function') {
        showAlert('Could not update status', readCustomerApiError(result, 'Please try again.'), 'warning');
      }
    }
  } catch (err) {
    if (typeof showAlert === 'function') showAlert('Server error', err.message, 'warning');
  }
}

// ---------------------------------------------------------------------------
// Delete Customer modal - privacy-gated, only for Deactivated accounts
// ---------------------------------------------------------------------------

function openDeleteCustomerModal(data) {
  if (!data || !data.id) {
    if (typeof showAlert === 'function') {
      showAlert('Missing account id', 'This row has no id yet - refresh the page and try again.', 'warning');
    }
    return;
  }

  deleteTarget = {
    user_id: data.id,
    email: (data.email || '').toLowerCase(),
    name: [data.first_name, data.last_name].filter(Boolean).join(' ') || 'Customer'
  };

  document.getElementById('dcm_user_id').value = deleteTarget.user_id;
  document.getElementById('dcm_email').value = deleteTarget.email;
  document.getElementById('dcm_name').textContent = deleteTarget.name;
  document.getElementById('dcm_email_label').textContent = deleteTarget.email || 'No email on file';
  document.getElementById('dcm_error').classList.add('hidden');

  document.getElementById('dcm_ack').checked = false;
  document.getElementById('dcm_reason').value = '';
  document.getElementById('dcm_confirm').value = '';
  resetDeleteBtn();
  validateDeleteForm();

  document.getElementById('deleteCustomerModal').classList.remove('hidden');
}

function closeDeleteCustomerModal() {
  document.getElementById('deleteCustomerModal').classList.add('hidden');
}

function resetDeleteBtn() {
  const btn = document.getElementById('dcmDeleteBtn');
  btn.dataset.busy = '0';
  btn.disabled = true;
  btn.innerHTML = '<i class="fa-solid fa-trash-can"></i> <span>Permanently Delete</span>';
}

function showDeleteError(message) {
  const box = document.getElementById('dcm_error');
  box.textContent = message;
  box.classList.remove('hidden');
}

// Tatlong gate bago ma-unlock ang destructive button: acknowledgement,
// isang reason na hindi bababa sa 10 characters, at ang typed na email.
function validateDeleteForm() {
  const ack = document.getElementById('dcm_ack').checked;
  const reason = document.getElementById('dcm_reason').value.trim();
  const typed = document.getElementById('dcm_confirm').value.trim().toLowerCase();
  const btn = document.getElementById('dcmDeleteBtn');

  if (btn.dataset.busy === '1') return false;

  const emailOk = !!deleteTarget && !!deleteTarget.email && typed === deleteTarget.email;
  const ok = ack && reason.length >= 10 && emailOk;
  btn.disabled = !ok;

  if (!ack) {
    btn.title = 'Acknowledge the Privacy Policy first';
  } else if (reason.length < 10) {
    btn.title = 'Enter a deletion reason of at least 10 characters';
  } else if (!emailOk) {
    btn.title = 'Type the customer email exactly to confirm';
  } else {
    btn.title = 'Permanently delete this account';
  }
  return ok;
}

async function confirmDeleteCustomer() {
  if (!deleteTarget || !validateDeleteForm()) return;

  const btn = document.getElementById('dcmDeleteBtn');
  btn.dataset.busy = '1';
  btn.disabled = true;
  btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> <span>Deleting...</span>';
  document.getElementById('dcm_error').classList.add('hidden');

  try {
    const response = await fetch(CUSTOMERS_API_BASE + '/api/v1/admin/delete-customer', {
      method: 'POST',
      headers: buildCustomerHeaders(),
      body: JSON.stringify({
        user_id: deleteTarget.user_id,
        privacy_ack: document.getElementById('dcm_ack').checked,
        deletion_reason: document.getElementById('dcm_reason').value.trim()
      })
    });
    const result = await response.json();

    if (response.ok) {
      closeDeleteCustomerModal();
      if (typeof showAlert === 'function') {
        showAlert('Account deleted', deleteTarget.name + ' and its CRM records were permanently erased.');
      }
      window.location.reload();
    } else {
      showDeleteError(readCustomerApiError(result, 'Could not delete the account.'));
      resetDeleteBtn();
      validateDeleteForm();
    }
  } catch (err) {
    showDeleteError('Server connection failed: ' + err.message);
    resetDeleteBtn();
    validateDeleteForm();
  }
}


// Close modals when clicking outside
['addCustomerModal', 'customerDetailsModal', 'manageCustomerModal', 'deleteCustomerModal'].forEach(id => {
  const el = document.getElementById(id);
  if (el) {
    el.addEventListener('click', function (e) {
      if (e.target === this) this.classList.add('hidden');
    });
  }
});

// Simple search functionality
document.getElementById('customerSearch').addEventListener('input', function (e) {
  const searchTerm = e.target.value.toLowerCase();
  const rows = document.querySelectorAll('#customerTableBody tr');
  rows.forEach(row => {
    const text = row.innerText.toLowerCase();
    row.style.display = text.includes(searchTerm) ? '' : 'none';
  });
});
