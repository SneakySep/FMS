import pandas as pd
import numpy as np
from datetime import datetime, timedelta, timezone
from sklearn.linear_model import LogisticRegression
from app.supabase_config.supabase import supabase_secondary

class SalesDashboardService:
    def __init__(self):
        self.supabase = supabase_secondary

    async def get_sales_kpis(self, agent_id: str = None):
        """
        Kumukuha ng inquiries data, pinoproseso gamit ang Pandas & NumPy para sa KPIs,
        at gumagamit ng Scikit-Learn para sa predictive conversion scoring.
        """
        try:
            # 1. Kunin ang lahat ng inquiries mula sa database
            query = self.supabase.table("inquiries").select("*")
            if agent_id:
                # Kung gusto nating i-filter para sa specific na agent lang
                query = query.eq("assigned_agent_id", agent_id)
                
            response = query.execute()
            raw_data = response.data if response.data else []

            if not raw_data:
                return {
                    "total_leads_current_month": 0,
                    "total_leads_last_month": 0,
                    "lead_growth_percentage": 0.0,
                    "active_leads": 0,
                    "qualifying_opportunities": 0,
                    "total_closed_won": 0,
                    "total_closed_lost": 0,
                    "conversion_health_score": 0.0
                }

            # 2. I-convert ang raw data sa Pandas DataFrame
            df = pd.DataFrame(raw_data)

            # Siguraduhing may created_at column at i-convert sa datetime
            if "created_at" in df.columns:
                df["created_at"] = pd.to_datetime(df["created_at"], errors="coerce")
            else:
                df["created_at"] = datetime.now(timezone.utc)

            # 3. Kunin ang petsa ngayon at ang date ranges para sa Current Month vs Last Month
            now = datetime.now(timezone.utc)
            current_year, current_month = now.year, now.month
            
            # Petsa ng nakaraang buwan
            first_day_current_month = pd.Timestamp(year=current_year, month=current_month, day=1, tz="UTC")
            
            if current_month == 1:
                first_day_last_month = pd.Timestamp(year=current_year - 1, month=12, day=1, tz="UTC")
            else:
                first_day_last_month = pd.Timestamp(year=current_year, month=current_month - 1, day=1, tz="UTC")

            # 4. Pag-compute gamit ang Pandas at NumPy masks
            # Total leads ngayong buwan
            current_month_mask = df["created_at"] >= first_day_current_month
            total_leads_current = int(np.sum(current_month_mask))

            # Total leads noong nakaraang buwan
            last_month_mask = (df["created_at"] >= first_day_last_month) & (df["created_at"] < first_day_current_month)
            total_leads_last = int(np.sum(last_month_mask))

            # Lead Growth Percentage (NumPy safe division)
            if total_leads_last > 0:
                lead_growth = float(((total_leads_current - total_leads_last) / total_leads_last) * 100)
            else:
                lead_growth = 100.0 if total_leads_current > 0 else 0.0

            # Active Leads (Halimbawa: status na hindi pa closed)
            active_mask = ~df["status"].isin(["closed_won", "closed_lost"])
            active_leads_count = int(np.sum(active_mask))

            # Opportunities sa status na 'qualifying'
            qualifying_mask = df["status"] == "qualifying"
            qualifying_count = int(np.sum(qualifying_mask))

            # Total Closed-Won vs Total Closed-Lost
            closed_won_count = int(np.sum(df["status"] == "closed_won"))
            closed_lost_count = int(np.sum(df["status"] == "closed_lost"))

            # 5. Scikit-Learn Integration: Predictive Conversion Health Score
            # Gagawa tayo ng simpleng model training o scoring gamit ang Scikit-Learn batay sa historical data
            conversion_score = 50.0
            if len(df) >= 5:
                try:
                    # Feature engineering para sa ML: Gamitin ang estimated_amount o kaya'y pagkakaroon ng email/phone
                    df["has_contact"] = df["email"].notnull() & df["phone_number"].notnull()
                    df["amount_filled"] = df["estimated_amount"].fillna(0) > 0
                    
                    # Target variable: 1 kung closed_won, 0 kung iba
                    df["is_won"] = (df["status"] == "closed_won").astype(int)
                    
                    X = df[["has_contact", "amount_filled"]].astype(int)
                    y = df["is_won"]
                    
                    # Kung may variation na ang data (may at least isang 0 at 1 sa y)
                    if y.nunique() > 1:
                        model = LogisticRegression()
                        model.fit(X, y)
                        
                        # Hulaan ang pangkalahatang win probability ng mga active leads
                        probabilities = model.predict_proba(X)[:, 1]
                        conversion_score = float(np.mean(probabilities) * 100)
                except Exception as ml_err:
                    print(f"ML Scoring fallback triggered: {ml_err}")
                    conversion_score = float((closed_won_count / (closed_won_count + closed_lost_count + 1)) * 100)

            return {
                "total_leads_current_month": total_leads_current,
                "total_leads_last_month": total_leads_last,
                "lead_growth_percentage": round(lead_growth, 2),
                "active_leads": active_leads_count,
                "qualifying_opportunities": qualifying_count,
                "total_closed_won": closed_won_count,
                "total_closed_lost": closed_lost_count,
                "predicted_conversion_health_score": round(conversion_score, 2)
            }

        except Exception as e:
            raise Exception(f"Dashboard Service Error: {str(e)}")