import smtplib
from email.mime.text import MIMEText
from email.mime.multipart import MIMEMultipart
from email.mime.application import MIMEApplication
from app.config.config import settings

def send_quotation_email(to_email: str, company_name: str, pdf_bytes: bytes, filename: str = "Quotation.pdf") -> bool:
    sender_email = settings.SMTP_SENDER
    sender_password = settings.SMTP_PASSWORD

    # 1. Setup email headers
    msg = MIMEMultipart()
    msg['From'] = f"Priority <{sender_email}>"
    msg['To'] = to_email
    msg['Subject'] = f"Official Quotation - Priority Handling Logistics Inc."

    logo_url = "https://ueexljyfzygzhgjluqhm.supabase.co/storage/v1/object/public/assets/logo1.jpg"

    # 2. HTML Body
    html_content = f"""
    <html>
        <body style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #ffffff; color: #333333; padding: 40px 20px; margin: 0;">
            <div style="max-width: 560px; margin: 0 auto; background: #ffffff; padding: 40px; border-radius: 8px; border: 1px solid #e1e1e1; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">

                <!-- LOGO -->
                <div style="text-align: center; margin-bottom: 20px;">
                    <img src="{logo_url}" alt="Priority Logistics" style="max-width: 120px; height: auto; display: block; margin: 0 auto;" />
                </div>
                
                <!-- Title -->
                <h2 style="font-size: 20px; font-weight: 600; color: #111111; text-align: center; margin-bottom: 25px; margin-top: 0;">
                    Official Freight Quotation
                </h2>
                
                <!-- Body -->
                <p style="font-size: 14px; line-height: 1.6; color: #444444; margin-bottom: 10px;">
                    Dear <strong>{company_name}</strong>,
                </p>
                <p style="font-size: 14px; line-height: 1.6; color: #444444; margin-top: 0; margin-bottom: 20px;">
                    Thank you for reaching out to <strong>Priority Handling Logistics Inc.</strong> Attached to this email is your official price quotation based on your service request.
                </p>
                
                <div style="background-color: #f9fafb; padding: 15px; border-radius: 6px; border-left: 4px solid #4f46e5; margin-bottom: 25px;">
                    <p style="font-size: 13px; color: #555555; margin: 0; line-height: 1.5;">
                        📌 Please review the attached PDF file for details including breakdown, validity, and instructions on how to proceed.
                    </p>
                </div>

                <p style="font-size: 13px; line-height: 1.5; color: #666666; margin-bottom: 30px;">
                    If you have questions or want to adjust the service details, reply directly to this email or contact your assigned agent.
                </p>
                
                <hr style="border: 0; border-top: 1px solid #eeeeee; margin-bottom: 20px;">
                
                <p style="font-size: 11px; color: #888888; text-align: center; margin: 0; letter-spacing: 0.5px;">
                    Priority Handling Logistics Inc. • Express & Freight Solutions
                </p>
            </div>
        </body>
    </html>
    """

    msg.attach(MIMEText(html_content, 'html', 'utf-8'))

    # 3. Attach PDF Byte Data
    pdf_attachment = MIMEApplication(pdf_bytes, _subtype="pdf")
    pdf_attachment.add_header('Content-Disposition', 'attachment', filename=filename)
    msg.attach(pdf_attachment)

    # 4. Send via SMTP
    try:
        with smtplib.SMTP_SSL("smtp.gmail.com", 465) as server:
            server.login(sender_email, sender_password)
            server.sendmail(sender_email, to_email, msg.as_string())
        return True
    except Exception as e:
        print(f"SMTP Quotation Email Error: {str(e)}")
        return False