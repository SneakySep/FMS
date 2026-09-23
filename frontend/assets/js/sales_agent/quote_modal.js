/* ======================================================================
   SEND PDF QUOTATION MODAL - Quote Modal Functions
   Separated from PHP for early loading and global availability
   ====================================================================== */

// API_URL is declared in myleads.js - reuse it from there
var _quoteLead = null;

function openQuoteModal(lead) {
  console.log('openQuoteModal called with lead:', lead);
  _quoteLead = lead;

  // FIRST: Get and show the modal immediately
  var modal = document.getElementById('quoteModal');
  if (!modal) {
    console.error('ERROR: Modal element with id quoteModal not found!');
    alert('Error: Quote modal not found in page');
    return;
  }

  console.log('Modal found, showing now...');
  modal.classList.remove('hidden');
  modal.classList.add('flex');
  modal.style.display = 'flex';

  // SECOND: Prefill form fields (non-critical, won't break modal display)
  try {
    if (lead && typeof lead === 'object') {
      document.getElementById('quoteLeadId').value = lead.id || lead.lead_id || '';
      var inquiryCode = lead.inquiry_code || lead.code || ('INQ-' + String(lead.id || '').substring(0, 8));
      document.getElementById('quoteInquiryCode').value = inquiryCode;
      document.getElementById('quoteInquiryBadge').innerText = inquiryCode;

      document.getElementById('quoteEmail').value = lead.email || '';
      document.getElementById('quoteCompany').value = lead.company_name || lead.company || '';
      document.getElementById('quoteRoute').value = (lead.origin && lead.destination) ? (lead.origin + ' -> ' + lead.destination) : '';

      // Prefill Base Freight from the lead's estimated price
      var est = parseFloat(lead.estimated_amount ?? lead.estimated_price ?? 0);
      document.getElementById('qBaseFreight').value = est > 0 ? est : '';

      // Default validity: +14 days (local date, avoids UTC day-shift)
      var d = new Date();
      d.setDate(d.getDate() + 14);
      var isoDate = d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0') + '-' + String(d.getDate()).padStart(2, '0');
      document.getElementById('quoteValidity').value = isoDate;

      // Reset other charges
      ['qDiscount', 'qPickupTrucking', 'qDeliveryTrucking', 'qDocFee', 'qHandlingFee', 'qVat'].forEach(function (id) {
        var el = document.getElementById(id);
        if (el) el.value = '';
      });
      var termsEl = document.getElementById('quoteTerms');
      if (termsEl) termsEl.value = '';

      recalcQuoteTotals();
      console.log('Quote modal prefilled with lead data');
    } else {
      console.warn('Lead data is null or invalid, showing empty form');
    }
  } catch (err) {
    console.error('Error prefilling quote form:', err);
    // Modal is already shown, so user can still use it
  }

  console.log('Modal display complete. Current classes:', modal.className);
}

function closeQuoteModal() {
  console.log('closeQuoteModal called');
  var modal = document.getElementById('quoteModal');
  if (modal) {
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    modal.style.display = 'none';
    console.log('Modal closed');
  }
}

function _qval(id) {
  var el = document.getElementById(id);
  return el ? (parseFloat(el.value) || 0) : 0;
}

function _qfmt(n) {
  return '₱' + (n || 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

function recalcQuoteTotals() {
  var base = _qval('qBaseFreight');
  var disc = _qval('qDiscount');
  var other = _qval('qPickupTrucking') + _qval('qDeliveryTrucking') + _qval('qDocFee') + _qval('qHandlingFee');
  var vat = _qval('qVat');
  var total = Math.max(0, base + other - disc + vat);

  document.getElementById('qsBase').innerText = _qfmt(base);
  document.getElementById('qsOther').innerText = _qfmt(other);
  document.getElementById('qsDiscount').innerText = '-' + _qfmt(disc);
  document.getElementById('qsVat').innerText = _qfmt(vat);
  document.getElementById('qsTotal').innerText = _qfmt(total);
}

function esc(s) {
  return String(s || '')
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#39;');
}


function buildQuotePdfHtml(data) {
  return ''
    + '<!DOCTYPE html><html><head><meta charset="UTF-8">'
    + '<style>'
    + '  body { font-family: Arial, sans-serif; color: #333; background: white; }'
    + '  .container { max-width: 800px; margin: 40px auto; padding: 0 20px; }'
    + '  .header { text-align: center; border-bottom: 3px solid #4f46e5; padding-bottom: 20px; margin-bottom: 30px; }'
    + '  .logo { font-size: 28px; font-weight: bold; color: #4f46e5; }'
    + '  .subtitle { font-size: 12px; color: #666; margin-top: 5px; }'
    + '  .invoice-info { display: flex; justify-content: space-between; margin-bottom: 30px; font-size: 13px; }'
    + '  .invoice-left { flex: 1; }'
    + '  .invoice-right { text-align: right; }'
    + '  .label { font-weight: bold; color: #4f46e5; margin-bottom: 5px; }'
    + '  .value { margin-bottom: 8px; }'
    + '  table { width: 100%; border-collapse: collapse; margin: 30px 0; font-size: 13px; }'
    + '  th { background: #f3f4f6; padding: 12px; text-align: left; font-weight: bold; color: #1f2937; border-bottom: 2px solid #d1d5db; }'
    + '  td { padding: 10px 12px; border-bottom: 1px solid #e5e7eb; }'
    + '  tr:last-child td { border-bottom: 2px solid #d1d5db; }'
    + '  .summary { display: flex; justify-content: flex-end; margin: 30px 0; }'
    + '  .summary-table { width: 300px; }'
    + '  .summary-row { display: flex; justify-content: space-between; padding: 8px 0; }'
    + '  .summary-total { font-weight: bold; color: #4f46e5; font-size: 16px; padding: 12px 0; border-top: 2px solid #d1d5db; }'
    + '  .note { background: #f0f4ff; padding: 15px; margin: 30px 0; font-size: 12px; color: #4338ca; border-left: 4px solid #4f46e5; }'
    + '  .brandfoot { text-align: center; margin-top: 40px; padding-top: 20px; border-top: 1px solid #e5e7eb; font-size: 11px; color: #999; }'
    + '</style>'
    + '</head><body>'
    + '<div class="container">'
    + '  <div class="header">'
    + '    <div class="logo">PRIORITY HANDLING LOGISTICS INC.</div>'
    + '    <div class="subtitle">Official Freight Quotation</div>'
    + '  </div>'
    + '  <div class="invoice-info">'
    + '    <div class="invoice-left">'
    + '      <div class="label">Bill To:</div>'
    + '      <div class="value"><b>' + esc(data.company) + '</b></div>'
    + '      <div class="value">' + esc(data.email) + '</div>'
    + '    </div>'
    + '    <div class="invoice-right">'
    + '      <div class="value"><b>Inquiry Code:</b> ' + esc(data.inquiryCode) + '</div>'
    + '      <div class="value"><b>Issued Date:</b> ' + esc(data.issuedDate) + '</div>'
    + '      <div class="value"><b>Valid Until:</b> ' + esc(data.validityLabel) + '</div>'
    + '    </div>'
    + '  </div>'
    + '  <table>'
    + '    <tr><th>Description</th><th style="text-align: right;">Amount</th></tr>'
    + '    <tr><td>Base Freight (' + esc(data.route) + ' | ' + esc(data.service) + ')</td><td style="text-align: right;">' + data.fmt(data.base) + '</td></tr>'
    + (data.pickup > 0 ? '    <tr><td>Pickup Trucking</td><td style="text-align: right;">' + data.fmt(data.pickup) + '</td></tr>' : '')
    + (data.delivery > 0 ? '    <tr><td>Delivery Trucking</td><td style="text-align: right;">' + data.fmt(data.delivery) + '</td></tr>' : '')
    + (data.docs > 0 ? '    <tr><td>Documentation Fee</td><td style="text-align: right;">' + data.fmt(data.docs) + '</td></tr>' : '')
    + (data.handling > 0 ? '    <tr><td>Handling Fee</td><td style="text-align: right;">' + data.fmt(data.handling) + '</td></tr>' : '')
    + (data.discount > 0 ? '    <tr><td>Discount</td><td style="text-align: right;">-' + data.fmt(data.discount) + '</td></tr>' : '')
    + (data.vat > 0 ? '    <tr><td>VAT (12%)</td><td style="text-align: right;">' + data.fmt(data.vat) + '</td></tr>' : '')
    + '  </table>'
    + '  <div class="summary">'
    + '    <div class="summary-table">'
    + '      <div class="summary-row"><span>Subtotal:</span><span>' + data.fmt(data.base + data.pickup + data.delivery + data.docs + data.handling - data.discount) + '</span></div>'
    + (data.vat > 0 ? '      <div class="summary-row"><span>VAT:</span><span>' + data.fmt(data.vat) + '</span></div>' : '')
    + '      <div class="summary-total"><span>TOTAL:</span> <span>' + data.fmt(data.total) + '</span></div>'
    + '    </div>'
    + '  </div>'
    + (data.terms ? '  <div class="note"><b>Terms & Remarks:</b><br />' + esc(data.terms) + '</div>' : '')
    + '    <div class="note">This quotation is valid until <b>' + esc(data.validityLabel) + '</b>. Rates are subject to change based on fuel surcharge and market conditions. Please attach this PDF when replying to your sales representative.</div>'
    + '  </div>'
    + '  <div class="brandfoot">PRIORITY HANDLING LOGISTICS INC. • OFFICIAL FREIGHT QUOTATION • ' + esc(data.inquiryCode) + '-Q</div>'
    + '</div></body></html>';
}

async function sendQuotePdf() {
  var leadId = document.getElementById('quoteLeadId').value;
  var inquiryCode = document.getElementById('quoteInquiryCode').value;
  var company = document.getElementById('quoteCompany').value.trim();
  var email = document.getElementById('quoteEmail').value.trim();
  var route = document.getElementById('quoteRoute').value.trim();
  var service = (_quoteLead && (_quoteLead.service_type || _quoteLead.service)) || '';

  var base = _qval('qBaseFreight');
  var disc = _qval('qDiscount');
  var pickup = _qval('qPickupTrucking');
  var delivery = _qval('qDeliveryTrucking');
  var docs = _qval('qDocFee');
  var handling = _qval('qHandlingFee');
  var vat = _qval('qVat');
  var total = Math.max(0, base + pickup + delivery + docs + handling - disc + vat);

  var validity = document.getElementById('quoteValidity').value;
  var validityLabel = validity
    ? new Date(validity + 'T00:00:00').toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' })
    : 'N/A';
  var terms = document.getElementById('quoteTerms').value.trim();

  // --- Validation ---
  if (!email || !/^\S+@\S+\.\S+$/.test(email)) {
    if (typeof SwiftAlert !== 'undefined') {
      SwiftAlert.fire({ icon: 'warning', title: 'Invalid Email', text: 'Please enter a valid customer email address.' });
    } else { alert('Please enter a valid customer email address.'); }
    return;
  }
  if (base <= 0) {
    if (typeof SwiftAlert !== 'undefined') {
      SwiftAlert.fire({ icon: 'warning', title: 'Missing Charges', text: 'Base Freight is required to generate the quotation.' });
    } else { alert('Base Freight is required to generate the quotation.'); }
    return;
  }

  var issuedDate = new Date().toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' });

  // --- 1. Print-ready quotation window (Save as PDF) ---
  var pdfWindow = window.open('', '_blank', 'width=920,height=1040');
  if (!pdfWindow) {
    if (typeof SwiftAlert !== 'undefined') {
      SwiftAlert.fire({ icon: 'error', title: 'Popup Blocked', text: 'Please allow popups to generate the PDF quotation.' });
    } else { alert('Please allow popups to generate the PDF quotation.'); }
    return;
  }
  pdfWindow.document.open();
  pdfWindow.document.write(buildQuotePdfHtml({
    inquiryCode: inquiryCode, company: company || 'Customer', email: email,
    route: route, service: service, issuedDate: issuedDate, validityLabel: validityLabel,
    base: base, pickup: pickup, delivery: delivery, docs: docs, handling: handling,
    vat: vat, discount: disc, total: total, terms: terms, fmt: _qfmt
  }));
  pdfWindow.document.close();

  // --- 2. Prefilled mailto draft ---
  var subject = 'Freight Quotation ' + inquiryCode + ' – ' + (company || 'Priority Handling');
  var body = 'Dear ' + (company || 'Customer') + ',\n\n'
    + 'Thank you for your inquiry with Priority Handling Logistics Inc.\n\n'
    + 'ROUTE: ' + (route || 'N/A') + '\n'
    + 'SERVICE: ' + (service || 'General Freight') + '\n'
    + 'TOTAL QUOTATION: ' + _qfmt(total) + '\n'
    + 'VALID UNTIL: ' + validityLabel + '\n'
    + (terms ? '\nTERMS & REMARKS:\n' + terms + '\n' : '')
    + '\nPlease find attached the complete PDF quotation. Do not hesitate to reach out for any clarifications.\n\n'
    + 'Best regards,\nSales Team\nPriority Handling Logistics Inc.';
  window.location.href = 'mailto:' + email + '?subject=' + encodeURIComponent(subject) + '&body=' + encodeURIComponent(body);

  // --- 3. Mark lead as quote_sent (only if it is still before that stage) ---
  if (_quoteLead && leadId) {
    var order = ['new_inquiry', 'qualifying', 'quote_sent', 'negotiation', 'closed_won', 'closed_lost'];
    var cur = (typeof normalizeStatus === 'function') ? normalizeStatus(_quoteLead.status) : (_quoteLead.status || 'new_inquiry');
    if (order.indexOf(cur) !== -1 && order.indexOf(cur) < order.indexOf('quote_sent')) {
      try {
        await fetch(API_URL + '/api/v1/leads/' + leadId + '/status', {
          method: 'PATCH',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ status: 'quote_sent' })
        });
      } catch (err) {
        console.error('Failed to update lead status to quote_sent:', err);
      }
    }
  }

  closeQuoteModal();
  if (typeof SwiftAlert !== 'undefined') {
    SwiftAlert.fire({
      icon: 'success',
      title: 'Quotation Ready',
      text: 'PDF opened in a new tab and a mail draft was started for ' + email + '.'
    });
  }
}

// Wire up live total recalculation for quote modal form fields
document.addEventListener('DOMContentLoaded', function () {
  ['qBaseFreight', 'qDiscount', 'qPickupTrucking', 'qDeliveryTrucking', 'qDocFee', 'qHandlingFee', 'qVat'].forEach(function (id) {
    var el = document.getElementById(id);
    if (el) el.addEventListener('input', recalcQuoteTotals);
  });
});

