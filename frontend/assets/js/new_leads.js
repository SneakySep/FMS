
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
  
  // Disable button while processing
  submitBtn.disabled = true;
  submitBtn.innerHTML = `<i class="fa-solid fa-spinner animate-spin"></i> Saving...`;

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
    const response = await fetch('http://127.0.0.1:8000/api/v1/leads/leads', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(payload)
    });

    const result = await response.json();

    if (response.ok && result.status === 'success') {
      alert('Lead successfully added!');
      closeLeadModal();
      window.location.reload(); // Refresh para mag-update ang Pipeline Snapshot & Counts
    } else {
      alert('Error: ' + (result.detail || 'Failed to create lead.'));
    }
  } catch (err) {
    console.error('Fetch Error:', err);
    alert('Failed to connect to backend server.');
  } finally {
    submitBtn.disabled = false;
    submitBtn.innerHTML = `<span>Save Lead</span>`;
  }
}
