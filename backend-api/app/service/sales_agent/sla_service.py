import os
from datetime import datetime
from decimal import Decimal
from weasyprint import HTML
from app.schemas.sales_agent.sla_schema import SLACreateRequest
from app.supabase_config.supabase import supabase_secondary
from fastapi import HTTPException
import httpx
from app.middleware.sla_contract import send_sla_contract_email

class SLAService:
    def __init__(self):
        self.supabase = supabase_secondary

    def generate_sla_pdf(self, contract_data: dict) -> str:
        html_content = f"""
        <!DOCTYPE html>
        <html>
        <head>
            <style>
                body {{ font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; color: #333; margin: 40px; }}
                .header {{ border-bottom: 2px solid #0f172a; padding-bottom: 10px; margin-bottom: 20px; }}
                .title {{ font-size: 24px; font-weight: bold; color: #0f172a; }}
                .meta {{ font-size: 12px; color: #64748b; }}
                .section {{ margin-top: 20px; font-size: 14px; line-height: 1.5; }}
                table {{ width: 100%; border-collapse: collapse; margin-top: 15px; }}
                th, td {{ border: 1px solid #cbd5e1; padding: 8px; text-align: left; font-size: 12px; }}
                th {{ background-color: #f1f5f9; }}
            </style>
        </head>
        <body>
            <div class="header">
                <div class="title">SECUFREIGHT SERVICE LEVEL AGREEMENT</div>
                <div class="meta">Contract No: {contract_data.get('contract_no')} | Date: {datetime.now().strftime('%Y-%m-%d')}</div>
            </div>
            <div class="section">
                <p><strong>Customer ID:</strong> {contract_data.get('customer_id')}</p>
                <p><strong>Transport Mode:</strong> {contract_data.get('mode', '').upper()} ({contract_data.get('origin')} to {contract_data.get('destination')})</p>
                <p><strong>Validity:</strong> {contract_data.get('start_date')} up to {contract_data.get('end_date')}</p>
            </div>
            <div class="section">
                <h3>Agreed Performance Metrics & Terms</h3>
                <table>
                    <tr><th>Parameter</th><th>Agreed Value</th></tr>
                    <tr><td>Transit Days</td><td>{contract_data.get('transit_days')} Days</td></tr>
                    <tr><td>On-Time Target</td><td>{contract_data.get('ontime_target_pct')}%</td></tr>
                    <tr><td>Claims Response</td><td>{contract_data.get('claims_response_hours')} Hours</td></tr>
                    <tr><td>Agreed Rate</td><td>{contract_data.get('currency', 'PHP')} {contract_data.get('rate')} ({contract_data.get('rate_basis')})</td></tr>
                </table>
            </div>
        </body>
        </html>
        """
        
        filename = f"sla_{contract_data.get('contract_no')}.pdf"
        local_path = f"/tmp/{filename}"
        HTML(string=html_content).write_pdf(local_path)
        
        return local_path

    async def send_sla_email_to_customer(self, customer_id: str, contract_no: str, storage_path: str):
        customer_res = self.supabase.table("customer_profiles") \
            .select("email, company_name") \
            .eq("id", customer_id) \
            .maybe_single() \
            .execute()
            
        if not customer_res.data or not customer_res.data.get("email"):
            return

        customer_email = customer_res.data.get("email")
        company_name = customer_res.data.get("company_name", "Valued Customer")

        # Generate Signed URL
        signed_url_res = self.supabase.storage.from_("sla-documents").create_signed_url(
            path=storage_path,
            expires_in=604800
        )
        download_url = signed_url_res.get("signedURL") if isinstance(signed_url_res, dict) else signed_url_res

        # Tawagin ang helper para magpadala ng magandang HTML email
        send_sla_contract_email(to_email=customer_email, contract_no=contract_no, company_name=company_name, download_url=download_url)

    async def create_contract_and_pdf(self, payload: SLACreateRequest, employee_id: str):
        # 1. Check existing SLA para sa inquiry
        existing_contract = self.supabase.table("sla_contracts") \
            .select("id, contract_no") \
            .eq("inquiry_id", payload.inquiry_id) \
            .execute()
            
        if existing_contract.data and len(existing_contract.data) > 0:
            raise Exception(f"SLA Contract already exists for this inquiry (Contract No: {existing_contract.data[0]['contract_no']})")

        # 2. Insert sa database
        insert_data = payload.dict(exclude_unset=True)
        insert_data["created_by"] = employee_id
        
        response = self.supabase.table("sla_contracts").insert(insert_data).execute()
        if not response.data:
            raise Exception("Failed to insert SLA contract to database.")
            
        created_contract = response.data[0]
        contract_no = created_contract["contract_no"]
        contract_id = created_contract["id"]
        customer_id = created_contract["customer_id"]

        # 3. Generate PDF
        pdf_local_path = self.generate_sla_pdf(created_contract)

        # 4. Upload sa Private Storage Bucket
        storage_path = f"contracts/{datetime.now().strftime('%Y')}/{contract_no}.pdf"
        with open(pdf_local_path, "rb") as f:
            self.supabase.storage.from_("sla-documents").upload(
                path=storage_path,
                file=f,
                file_options={"content-type": "application/pdf", "upsert": "true"}
            )

        # 5. Update row with pdf_path
        self.supabase.table("sla_contracts").update({
            "pdf_path": storage_path,
            "pdf_generated_at": datetime.now().isoformat()
        }).eq("id", contract_id).execute()

        if os.path.exists(pdf_local_path):
            os.remove(pdf_local_path)

        # 6. AUTOMATIC SENDING NG EMAIL SA CUSTOMER
        await self.send_sla_email_to_customer(customer_id, contract_no, storage_path)

        return {"contract_id": contract_id, "contract_no": contract_no, "pdf_path": storage_path}