
import smtplib
from email.mime.text import MIMEText
from email.mime.multipart import MIMEMultipart
from app.config.config import settings

def send_sla_contract_email(to_email: str, contract_no: str, company_name: str, download_url: str):
    sender_email = settings.SMTP_SENDER
    sender_password = settings.SMTP_PASSWORD

    # 1. Set up ang email Header
    msg = MIMEMultipart()
    msg['From'] = f"Priority <{sender_email}>"
    msg['To'] = to_email
    msg['Subject'] = f"Your Service Level Agreement (SLA) Contract - {contract_no}"

    logo_url = "https://ikptlfzgqotfnneqkgae.supabase.co/storage/v1/object/public/assets/logo1.jpg"

    # HTML Body clean look para sa SLA
    html_content = f"""
    <html>
        <body style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #ffffff; color: #333333; padding: 40px 20px; margin: 0;">
            <div style="max-width: 560px; margin: 0 auto; background: #ffffff; padding: 40px; border-radius: 8px; border: 1px solid #e1e1e1; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">

            <!-- LOGO HERE -->
                <div style="text-align: center; margin-bottom: 20px;">
                    <img src="{logo_url}" alt="Priority Logo" style="max-width: 120px; height: auto; display: block; margin: 0 auto;" />
                </div>
                
                <!-- Title / Header -->
                <h2 style="font-size: 22px; font-weight: 600; color: #111111; text-align: center; margin-bottom: 30px; margin-top: 0;">
                    Service Level Agreement (SLA) Ready
                </h2>
                
                <!-- Body Text -->
                <p style="font-size: 14px; line-height: 1.6; color: #444444; margin-bottom: 10px;">
                    Hello <strong>{company_name}</strong>,
                </p>
                <p style="font-size: 14px; line-height: 1.6; color: #444444; margin-top: 0; margin-bottom: 30px;">
                    Your Service Level Agreement (SLA) contract has been successfully generated for your closed-won inquiry. You can securely view and download your contract copy using the button below.
                </p>
                
                <!-- Contract No Box -->
                <div style="text-align: center; margin: 25px 0;">
                    <span style="font-size: 18px; font-weight: bold; letter-spacing: 1px; color: #0f172a; background-color: #f1f5f9; padding: 10px 25px; border-radius: 4px; border: 1px solid #cbd5e1; display: inline-block;">
                        Contract No: {contract_no}
                    </span>
                </div>

                <!-- Download Button -->
                <div style="text-align: center; margin: 35px 0;">
                    <a href="{download_url}" target="_blank" style="background-color: #0f172a; color: #ffffff; padding: 14px 30px; border-radius: 6px; text-decoration: none; font-size: 14px; font-weight: bold; display: inline-block; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                        Download SLA Contract (PDF)
                    </a>
                </div>
                
                <!-- Notice Text -->
                <p style="font-size: 13px; line-height: 1.5; color: #666666; margin-top: 30px; margin-bottom: 30px;">
                    Note: This download link is secure and valid for 7 days. If you have any questions regarding your terms, please contact your account manager.
                </p>
                
                <!-- Footer Divider -->
                <hr style="border: 0; border-top: 1px solid #eeeeee; margin-bottom: 20px;">
                
                <!-- Footer Text -->
                <p style="font-size: 11px; color: #888888; text-align: center; margin: 0; letter-spacing: 0.5px;">
                    Priority Handling Logistics Inc. • Enterprise Business Control System
                </p>
            </div>
        </body>
    </html>
    """

    msg.attach(MIMEText(html_content, 'html', 'utf-8'))

    try:
        # Connect sa SMTP server ng Google PORT 465
        with smtplib.SMTP_SSL("smtp.gmail.com", 465) as server:
            server.login(sender_email, sender_password)
            server.sendmail(sender_email, to_email, msg.as_string())

        return True
    except Exception as e:
        print(f"SMTP SLA Email Error: {str(e)}")
        return False