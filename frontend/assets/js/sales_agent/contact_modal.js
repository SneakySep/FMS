/* Contact Modal Helpers - Email & Phone Button Setup */
// NOTE: openViewModal() is now defined in myleads.js to ensure full functionality
// This file only handles contact-specific UI updates

function updateContactButtons(lead) {
  // 1. EMAIL LINK SETUP
  const emailBtn = document.getElementById("contactModalEmailBtn");
  const emailText = document.getElementById("contactModalEmailText");
  
  if (lead.email) {
    emailBtn.href = `mailto:${encodeURIComponent(lead.email)}`;
    emailText.innerText = lead.email;
    emailBtn.classList.remove("opacity-50", "pointer-events-none");
  } else {
    emailBtn.href = "#";
    emailText.innerText = "No email provided";
    emailBtn.classList.add("opacity-50", "pointer-events-none");
  }

  // 2. PHONE LINK SETUP
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
}