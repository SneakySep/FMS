// Contact options modal (Email / Call) - independent from the lead drawer's openViewModal
function openContactModal(lead) {
  const modal = document.getElementById('contactOptionsModal');
  if (!modal) return;

  // 1. Lead identity
  document.getElementById('contactModalCompany').innerText = lead.company_name || 'Individual Client';
  document.getElementById('contactModalPerson').innerText = lead.contact_person || 'No contact person';

  // 2. Email link setup
  const emailBtn = document.getElementById('contactModalEmailBtn');
  const emailText = document.getElementById('contactModalEmailText');
  if (emailBtn && emailText) {
    if (lead.email) {
      emailBtn.href = `https://mail.google.com/mail/?view=cm&fs=1&to=${encodeURIComponent(lead.email)}`;
      emailText.innerText = lead.email;
      emailBtn.classList.remove('opacity-50', 'pointer-events-none');
    } else {
      emailBtn.href = '#';
      emailText.innerText = 'No email provided';
      emailBtn.classList.add('opacity-50', 'pointer-events-none');
    }
  }

  // 3. Phone link setup
  const phoneBtn = document.getElementById('contactModalPhoneBtn');
  const phoneText = document.getElementById('contactModalPhoneText');
  if (phoneBtn && phoneText) {
    if (lead.phone_number) {
      phoneBtn.href = `tel:${lead.phone_number}`;
      phoneText.innerText = lead.phone_number;
      phoneBtn.classList.remove('opacity-50', 'pointer-events-none');
    } else {
      phoneBtn.href = '#';
      phoneText.innerText = 'No phone provided';
      phoneBtn.classList.add('opacity-50', 'pointer-events-none');
    }
  }

  // 4. Show modal
  modal.classList.remove('hidden');
  modal.classList.add('flex');
}

function closeContactModal() {
  const modal = document.getElementById('contactOptionsModal');
  if (!modal) return;
  modal.classList.add('hidden');
  modal.classList.remove('flex');
}
