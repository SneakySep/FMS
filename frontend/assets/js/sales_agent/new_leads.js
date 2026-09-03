// =================================================================================================
// New Leads - Create & Submit
// =================================================================================================

// Resolve the API base URL from the PHP-injected config, falling back to localhost
var LEADS_API_BASE = (window.APP_CONFIG && window.APP_CONFIG.API_BASE_URL)
  ? window.APP_CONFIG.API_BASE_URL
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

    if (response.ok && (result.status === 'success' || response.status === 200 || response.status === 201)) {
      closeLeadModal();
      
      // Success Toast Notification
      showToast('Lead successfully added!', 'success');

      // Continuous flow delay para makita ang toast bago mag-reload
      setTimeout(() => {
        window.location.reload();
      }, 1200);

    } else {
      // 3. Error SweetAlert Modal
      showAlert('Failed to Add Lead', result.detail || 'Failed to create lead.', 'error');
    }
  } catch (err) {
    console.error('Fetch Error:', err);
    // 4. Connection Failure Alert
    showAlert('Connection Error', 'Failed to connect to backend server.', 'error');
  } finally {
    submitBtn.disabled = false;
    submitBtn.innerHTML = '<span>Save Lead</span>';
  }
}