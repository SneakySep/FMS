import secrets
import string
import smtplib
from email.mime.text import MIMEText
from email.mime.multipart import MIMEMultipart
from app.config.config import settings

def generate_strong_password(length: int = 12) -> str:
    """Gumawa ng random at malakas na password na may kasamang letters, digits, at symbols."""
    alphabet = string.ascii_letters + string.digits + "!@#$%^&*"
    while True:
        password = "".join(secrets.choice(alphabet) for _ in range(length))
        if (any(c.islower() for c in password)
                and any(c.isupper() for c in password)
                and sum(c.isdigit() for c in password) >= 2
                and any(c in "!@#$%^&*" for c in password)):
            return password

def send_customer_credentials_email(to_email: str, company_name: str, customer_id: str, temp_password: str):
    sender_email = settings.SMTP_SENDER
    sender_password = settings.SMTP_PASSWORD

    # 1. Set up ang email Header
    msg = MIMEMultipart()
    msg['From'] = f"Priority Handling <{sender_email}>"
    msg['To'] = to_email
    msg['Subject'] = f"Welcome to Priority Handling - Your Customer Portal Credentials"

    logo_url = "https://ikptlfzgqotfnneqkgae.supabase.co/storage/v1/object/public/assets/logo1.jpg"

    # HTML Body clean look na may White & Blue Theme
    html_content = f"""
    <html>
        <body style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #f7f9fc; color: #333333; padding: 40px 20px; margin: 0;">
            <div style="max-width: 560px; margin: 0 auto; background: #ffffff; padding: 40px; border-radius: 8px; border: 1px solid #e1e1e1; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">

            <!-- LOGO HERE -->
                <div style="text-align: center; margin-bottom: 20px;">
                    <img src="{logo_url}" alt="Priority Logo" style="max-width: 120px; height: auto; display: block; margin: 0 auto;" />
                </div>
                
                <!-- Title / Header -->
                <h2 style="font-size: 22px; font-weight: 600; color: #1a365d; text-align: center; margin-bottom: 10px; margin-top: 0;">
                    Your Customer Portal Account
                </h2>
                <p style="font-size: 14px; color: #4a5568; text-align: center; margin-top: 0; margin-bottom: 30px;">
                    <b>{company_name}</b>
                </p>
                
                <!-- Body Text -->
                <p style="font-size: 14px; line-height: 1.6; color: #444444; margin-bottom: 10px;">
                    Hello,
                </p>
                <p style="font-size: 14px; line-height: 1.6; color: #444444; margin-top: 0; margin-bottom: 25px;">
                    Your account for the Priority Handling Customer Portal has been successfully created. You can now track your shipments and manage inquiries using your credentials below:
                </p>
                
                <!-- Credentials Box (Blue Theme) -->
                <div style="background-color: #ebf8ff; border: 1px solid #bee3f8; border-radius: 6px; padding: 20px; margin: 25px 0;">
                    <p style="font-size: 14px; color: #2b6cb0; margin: 0 0 10px 0;">
                        <strong>Customer ID:</strong> <span style="font-family: monospace; font-size: 15px; color: #1a202c; background: #fff; padding: 2px 6px; border-radius: 4px; border: 1px solid #cbd5e0;">{customer_id}</span>
                    </p>
                    <p style="font-size: 14px; color: #2b6cb0; margin: 0 0 10px 0;">
                        <strong>Portal Email:</strong> <span style="color: #1a202c;">{to_email}</span>
                    </p>
                    <p style="font-size: 14px; color: #2b6cb0; margin: 0;">
                        <strong>Temporary Password:</strong> <span style="font-family: monospace; font-size: 15px; color: #2b6cb0; background: #fff; padding: 2px 6px; border-radius: 4px; border: 1px solid #cbd5e0; font-weight: bold;">{temp_password}</span>
                    </p>
                </div>
                
                <!-- Notice Text -->
                <p style="font-size: 13px; line-height: 1.5; color: #666666; margin-top: 25px; margin-bottom: 30px;">
                    For security purposes, we strongly advise you to log in and change your password immediately.
                </p>
                
                <!-- Footer Divider -->
                <hr style="border: 0; border-top: 1px solid #eeeeee; margin-bottom: 20px;">
                
                <!-- Footer Text -->
                <p style="font-size: 11px; color: #888888; text-align: center; margin: 0; letter-spacing: 0.5px;">
                    Priority Handling Logistics Inc. • Secure Customer Portal Service
                </p>
            </div>
        </body>
    </html>
    """

    msg.attach(MIMEText(html_content, 'html', 'utf-8'))

    try:
        
        with smtplib.SMTP_SSL("smtp.gmail.com", 465) as server:
            server.login(sender_email, sender_password)
            server.sendmail(sender_email, to_email, msg.as_string())

        return True
    except Exception as e:
        print(f"SMTP Email Error: {str(e)}")
        return False