import smtplib
from email.mime.text import MIMEText
from email.mime.multipart import MIMEMultipart
from datetime import datetime, timezone
from app.supabase_config.supabase import supabase_secondary
from app.config.config import settings

class CampaignService:
    def __init__(self):
        self.supabase = supabase_secondary

    def send_broadcast_email(self, to_email: str, title: str, description: str, image_url: str):
        sender_email = settings.SMTP_SENDER
        sender_password = settings.SMTP_PASSWORD

        msg = MIMEMultipart()
        msg['From'] = f"Priority <{sender_email}>"
        msg['To'] = to_email
        msg['Subject'] = f"Priority Campaign: {title}"

        logo_url = "https://ikptlfzgqotfnneqkgae.supabase.co/storage/v1/object/public/assets/logo1.jpg"

        # HTML Email template para sa campaign
        image_html = f'<div style="text-align: center; margin: 20px 0;"><img src="{image_url}" alt="Campaign Image" style="max-width: 100%; height: auto; border-radius: 8px;" /></div>' if image_url else ''

        html_content = f"""
        <html>
            <body style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #ffffff; color: #333333; padding: 40px 20px; margin: 0;">
                <div style="max-width: 560px; margin: 0 auto; background: #ffffff; padding: 40px; border-radius: 8px; border: 1px solid #e1e1e1;">
                    <div style="text-align: center; margin-bottom: 20px;">
                        <img src="{logo_url}" alt="Logo" style="max-width: 120px; display: block; margin: 0 auto;" />
                    </div>
                    
                    <h2 style="font-size: 22px; font-weight: 600; color: #111111; text-align: center; margin-bottom: 20px;">
                        {title}
                    </h2>
                    
                    {image_html}
                    
                    <p style="font-size: 14px; line-height: 1.6; color: #444444; white-space: pre-line;">
                        {description}
                    </p>
                    
                    <hr style="border: 0; border-top: 1px solid #eeeeee; margin: 30px 0;">
                    
                    <p style="font-size: 11px; color: #888888; text-align: center; margin: 0;">
                        Priority Handling Logistics Inc. • Enterprise Campaigns & Announcements
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
            print(f"SMTP Campaign Email Error ({to_email}): {str(e)}")
            return False

    async def create_and_broadcast_campaign(self, agent_id: str, data):
        # 1. I-save ang campaign sa Supabase database
        insert_data = {
            "title": data.title,
            "description": data.description,
            "image_url": data.image_url,
            "target_audience": data.target_audience,
            "status": "published",
            "created_by": agent_id,
            "is_permanent": data.is_permanent,
            "expires_at": data.expires_at.isoformat() if data.expires_at and not data.is_permanent else None
        }

        campaign_res = self.supabase.table("campaigns").insert(insert_data).execute()
        if not campaign_res.data:
            raise Exception("Failed to save campaign to database.")

        created_campaign = campaign_res.data[0]

        # 2. Kunin ang listahan ng mga customer email galing sa database para sa email broadcast
        customers_res = self.supabase.table("customer_profiles").select("email").execute()
        customers = customers_res.data if customers_res.data else []

        # 3. I-broadcast ang email sa lahat ng nakuha nating customer
        success_count = 0
        for cust in customers:
            email = cust.get("email")
            if email:
                sent = self.send_broadcast_email(
                    to_email=email,
                    title=data.title,
                    description=data.description,
                    image_url=data.image_url
                )
                if sent:
                    success_count += 1

        return {
            "campaign": created_campaign,
            "emails_sent": success_count
        }