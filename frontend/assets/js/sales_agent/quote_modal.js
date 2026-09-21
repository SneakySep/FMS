// Function para buksan ang Send Quotation Modal
function openSendQuoteModal(data) {
  if (data) {
    if (document.getElementById('quoteLeadId')) document.getElementById('quoteLeadId').value = data.id || '';
    if (document.getElementById('quoteCustomerEmail')) document.getElementById('quoteCustomerEmail').value = (data.email && data.email !== 'N/A') ? data.email : '';
    if (document.getElementById('quoteCustomerName')) document.getElementById('quoteCustomerName').value = (data.company_name && data.company_name !== 'N/A') ? data.company_name : (data.contact_person || '');
    if (document.getElementById('quoteRoute')) document.getElementById('quoteRoute').value = (data.route && data.route !== 'N/A') ? data.route : '';
    if (document.getElementById('quoteBaseAmount')) document.getElementById('quoteBaseAmount').value = data.amount || 0;
    if (document.getElementById('quoteService')) document.getElementById('quoteService').value = (data.service_type && data.service_type !== 'N/A') ? data.service_type : '';
  }

  const modal = document.getElementById('sendQuoteModal');
  if (modal) {
    modal.classList.remove('hidden');
    modal.style.display = 'flex'; // Pinipilit lumabas
  }
}

// Iisang linis na close function para i-clear pareho ang class at inline style
function closeSendQuoteModal() {
  const modal = document.getElementById('sendQuoteModal');
  if (modal) {
    modal.classList.add('hidden');
    modal.style.display = 'none'; // Ibinabalik sa none para tuluyang magsara
  }
}

// Shortcut function kapag ginamit sa drawer/view modal button
function openSendQuoteModalFromDrawer() {
  const leadId = document.getElementById('modalLeadId')?.value || '';
  const email = document.getElementById('modalEmail')?.innerText || '';
  const company = document.getElementById('modalCompany')?.innerText || '';
  const contact = document.getElementById('modalContact')?.innerText || '';
  const route = document.getElementById('modalRoute')?.innerText || '';
  const priceVal = document.getElementById('modalPriceInput')?.value || '0';
  const service = document.getElementById('modalService')?.innerText || '';

  openSendQuoteModal({
    id: leadId,
    email: email,
    company_name: company,
    contact_person: contact,
    route: route,
    amount: priceVal ? parseFloat(priceVal) : 0,
    service_type: service
  });
}

// ETO NA YUNG NA-UPDATE NA FETCH HANDLER
async function handleSendQuotationSubmit(e) {
  if (e && e.preventDefault) e.preventDefault();

  const leadId = document.getElementById('quoteLeadId').value;
  const email = document.getElementById('quoteCustomerEmail').value;
  const baseAmount = parseFloat(document.getElementById('quoteBaseAmount').value) || 0;
  const discount = parseFloat(document.getElementById('quoteDiscount').value) || 0;
  const validUntil = document.getElementById('quoteValidUntil').value;
  const remarks = document.getElementById('quoteRemarks').value;

  const payload = {
    customer_email: email,
    base_amount: baseAmount,
    discount_amount: discount,
    valid_until: validUntil ? new Date(validUntil).toISOString() : null,
    remarks: remarks
  };

  Swal.fire({
    title: 'Generating & Emailing PDF...',
    text: 'Please wait. This will generate the PDF, send the email, and update the lead status.',
    allowOutsideClick: false,
    didOpen: () => Swal.showLoading()
  });

  try {
    const response = await fetch(`${window.APP_CONFIG.API_BASE_URL}/api/v1/leads/${leadId}/send-quotation`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload)
    });

    if (response.ok) {
      const data = await response.json();
      
      await Swal.fire({
        icon: 'success',
        title: 'Success!',
        text: data.message || `Quotation successfully emailed to ${email}.`,
        confirmColor: '#4f46e5'
      });
      
      closeSendQuoteModal();
      if (typeof closeViewModal === 'function') closeViewModal();
      
      location.reload(); 
    } else {
      const err = await response.json();
      Swal.fire({ 
        icon: 'error', 
        title: 'Process Failed', 
        text: err.detail || 'Could not process quotation.',
        confirmColor: '#4f46e5'
      });
    }
  } catch (err) {
    console.error(err);
    Swal.fire({ 
      icon: 'error', 
      title: 'Server Error', 
      text: 'Cannot connect to backend server.',
      confirmColor: '#4f46e5'
    });
  }
}

