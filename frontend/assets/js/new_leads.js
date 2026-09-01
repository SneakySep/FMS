
// =================================================================================================
// New Leads - Create & Submit
// =================================================================================================

// Resolve the API base URL from the PHP-injected config, falling back to localhost
var LEADS_API_BASE = (window.ADMIN_DASHBOARD_DATA && window.ADMIN_DASHBOARD_DATA.api_base_url)
  ? window.ADMIN_DASHBOARD_DATA.api_base_url
  : 'http://127.0.0.1:8000';

function openLeadModal() {
  document.getElementById('newLeadModal').classList.remove('hidden');
}

function closeLeadModal() {
  document.getElementById('newLeadModal').classList.add('hidden');
  document.getElementById('createLeadForm').reset();
}

async function submitNewLead(event) {
  event.preventDefault();

  const form = event.target;
  const submitBtn = document.getElementById('submitLeadBtn');
  
  submitBtn.disabled = true;
  submitBtn.innerHTML = '<i class="fa-solid fa-spinner animate-spin"></i> Saving...';

  const payload = {
    company_name: form.company_name.value.trim(),
    contact_person: form.contact_person.value.trim(),
    email: form.email.value.trim(),
    phone_number: form.phone_number.value.trim(),
    service_type: form.service_type.value,
    origin: form.origin.value.trim(),
    destination: form.destination.value.trim()
  };

  try {
    const response = await fetch(LEADS_API_BASE + '/api/v1/leads/leads', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload)
    });

    const result = await response.json();

    if (response.ok && result.status === 'success') {
      alert('Lead successfully added!');
      closeLeadModal();
      window.location.reload();
    } else {
      alert('Error: ' + (result.detail || 'Failed to create lead.'));
    }
  } catch (err) {
    console.error('Fetch Error:', err);
    alert('Failed to connect to backend server.');
  } finally {
    submitBtn.disabled = false;
    submitBtn.innerHTML = '<span>Save Lead</span>';
  }
}