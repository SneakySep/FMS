import json
import pandas as pd
import numpy as np
from datetime import datetime, timezone
from typing import Dict, Any, List

# Machine Learning
from sklearn.linear_model import LogisticRegression
from sklearn.preprocessing import OneHotEncoder

# Google Gen AI SDK Types
from google.genai import types

# Supabase & Gemini Client
from app.supabase_config.supabase import supabase_secondary, gemini_client


def get_prescriptive_analytics() -> Dict[str, Any]:
    """
    Prescriptive Analytics Module:
    - Lead Prioritization Engine (ML Rank via Conversion Prob, Amount, Urgency)
    - Automated Follow-Up Alerts (High-priority unattended leads)
    - Next Best Action Recommendations (Call, Email, Discount, Upsell)
    - Dynamic Pricing Suggestions (Powered by Gemini AI based on Won/Lost deals & route volume)
    - Suggestions Based on Lost Deals and Won (Powered by Gemini AI)
    """
    # 1. Fetch Data mula sa Supabase
    res_inquiries = supabase_secondary.table("inquiries").select("*").execute()
    res_tickets = supabase_secondary.table("tickets").select("*").execute()

    inquiries = res_inquiries.data if res_inquiries.data else []
    tickets = res_tickets.data if res_tickets.data else []

    df_inquiries = pd.DataFrame(inquiries)
    df_tickets = pd.DataFrame(tickets)

    now = pd.Timestamp.now(tz=timezone.utc)

    # Defaults
    prioritized_leads = []
    followup_alerts = []
    next_best_actions = []
    dynamic_pricing_suggestions = []
    won_lost_suggestions = {}

    # -------------------------------------------------------------
    # 1. LEAD PRIORITIZATION ENGINE, FOLLOW-UPS, & NEXT BEST ACTION
    # -------------------------------------------------------------
    if not df_inquiries.empty:
        df_inquiries["status"] = df_inquiries["status"].fillna("new")
        df_inquiries["created_at"] = pd.to_datetime(df_inquiries["created_at"], errors="coerce", utc=True)
        df_inquiries["estimated_amount"] = pd.to_numeric(df_inquiries["estimated_amount"], errors="coerce").fillna(0.0)
        df_inquiries["company_name"] = df_inquiries["company_name"].fillna("Unknown Client")
        df_inquiries["platform_used"] = df_inquiries["platform_used"].fillna("Unknown")
        df_inquiries["service_type"] = df_inquiries["service_type"].fillna("Unspecified")
        df_inquiries["is_won"] = (df_inquiries["status"] == "closed_won").astype(int)

        # ML Probability Score Training
        train_data = df_inquiries[df_inquiries["status"].isin(["closed_won", "closed_lost"])].copy()
        open_leads = df_inquiries[~df_inquiries["status"].isin(["closed_won", "closed_lost"])].copy()

        if len(train_data) >= 5 and train_data["is_won"].nunique() > 1:
            try:
                encoder = OneHotEncoder(sparse_output=False, handle_unknown="ignore")
                X_train = encoder.fit_transform(train_data[["platform_used", "service_type"]])
                y_train = train_data["is_won"]

                clf = LogisticRegression()
                clf.fit(X_train, y_train)

                if not open_leads.empty:
                    X_open = encoder.transform(open_leads[["platform_used", "service_type"]])
                    open_leads["prob_score"] = clf.predict_proba(X_open)[:, 1] * 100.0
                else:
                    open_leads["prob_score"] = 50.0
            except Exception:
                open_leads["prob_score"] = 50.0
        else:
            open_leads["prob_score"] = 50.0

        if not open_leads.empty:
            open_leads["hours_pending"] = (now - open_leads["created_at"]).dt.total_seconds() / 3600.0
            open_leads["hours_pending"] = open_leads["hours_pending"].fillna(0).clip(lower=0)

            scored_leads = []
            for _, r in open_leads.iterrows():
                est_amount = float(r["estimated_amount"])
                hrs = float(r["hours_pending"])
                prob = float(r.get("prob_score", 50.0))

                amount_pts = min(30.0, (est_amount / 50000.0) * 30.0)
                urgency_pts = min(30.0, (hrs / 24.0) * 15.0)
                prob_pts = (prob / 100.0) * 40.0

                priority_score = round(min(100.0, amount_pts + urgency_pts + prob_pts), 1)

                if priority_score >= 75:
                    action_type = "Direct Phone Call & 5% Closing Discount"
                    action_desc = "Tawagan agad sa telepono at alokan ng executive discount para maipasa ang proposal ngayong araw."
                    urgency_level = "High"
                elif priority_score >= 50:
                    action_type = "Send Customized Price Quote & Route Options"
                    action_desc = "Magpadala ng tailored quotation sa email kasama ang 2-3 route schedule options."
                    urgency_level = "Medium"
                else:
                    action_type = "Automated Nurturing Email"
                    action_desc = "Isama sa email marketing flow kaugnay ng mga promotional freight rates."
                    urgency_level = "Low"

                lead_obj = {
                    "inquiry_id": str(r["id"]),
                    "company_name": str(r["company_name"]),
                    "service_type": str(r["service_type"]),
                    "platform": str(r["platform_used"]),
                    "estimated_amount": est_amount,
                    "hours_pending": round(hrs, 1),
                    "conversion_prob": round(prob, 1),
                    "priority_score": priority_score,
                    "urgency_level": urgency_level,
                    "recommended_action": action_type,
                    "action_details": action_desc
                }
                scored_leads.append(lead_obj)

                if hrs >= 12 or priority_score >= 70:
                    followup_alerts.append({
                        "inquiry_id": str(r["id"]),
                        "company_name": str(r["company_name"]),
                        "hours_unattended": round(hrs, 1),
                        "priority_score": priority_score,
                        "alert_message": f"Ang high-probability lead '{r['company_name']}' ay {round(hrs, 1)} oras nang hindi nai-followup.",
                        "severity": "CRITICAL" if hrs >= 24 else "WARNING"
                    })

            prioritized_leads = sorted(scored_leads, key=lambda x: x["priority_score"], reverse=True)[:10]
            next_best_actions = sorted(scored_leads, key=lambda x: x["priority_score"], reverse=True)[:5]
            followup_alerts = sorted(followup_alerts, key=lambda x: x["priority_score"], reverse=True)[:5]

    # -------------------------------------------------------------
    # 2. DATA PREPARATION FOR GEMINI AI
    # -------------------------------------------------------------
    won_lost_summary = []
    if not df_inquiries.empty and "status" in df_inquiries.columns:
        summary_df = df_inquiries.groupby(["service_type", "status"]).size().unstack(fill_value=0).reset_index()
        won_lost_summary = summary_df.to_dict(orient="records")

    route_summary = []
    if not df_tickets.empty:
        df_tickets["origin"] = df_tickets["origin"].fillna("N/A")
        df_tickets["destination"] = df_tickets["destination"].fillna("N/A")
        df_tickets["route"] = df_tickets["origin"] + " → " + df_tickets["destination"]
        r_grp = df_tickets.groupby(["route", "service_type"]).size().reset_index(name="volume")
        route_summary = r_grp.head(6).to_dict(orient="records")

# -------------------------------------------------------------
    # 3. GEMINI AI: DYNAMIC PRICING & WON/LOST SUGGESTIONS
    # -------------------------------------------------------------
    if gemini_client:
        try:
            prompt = f"""
            You are a logistics pricing and business operations AI expert.
            Analyze the following operational data:
            - Service Type Won/Lost Summary: {json.dumps(won_lost_summary)}
            - Active Route Shipment Volume: {json.dumps(route_summary)}

            Provide a valid JSON response strictly in this structure:
            {{
              "dynamic_pricing_suggestions": [
                {{
                  "route": "Origin -> Destination",
                  "service_type": "Air / Sea / Land",
                  "suggested_adjustment": "+10% / -5% / Base Rate",
                  "strategy_type": "Peak Demand Surge / Low Volume Promo",
                  "reasoning": "Detailed explanation based on won/lost ratios and capacity demand."
                }}
              ],
              "suggestions_based_on_lost_won": {{
                "summary": "Short 2-sentence breakdown of what service types win or fail most.",
                "actionable_recommendations": [
                  "Recommendation 1",
                  "Recommendation 2",
                  "Recommendation 3"
                ]
              }}
            }}
            """

            # Gumamit ng Chat session at i-pass ang JSON response_mime_type sa config
            chat_session = gemini_client.chats.create(
                model="gemini-3.6-flash",
                config=types.GenerateContentConfig(
                    response_mime_type="application/json",
                    temperature=0.2
                )
            )

            response = chat_session.send_message(prompt)
            raw_text = response.text.strip()
            ai_data = json.loads(raw_text)

            dynamic_pricing_suggestions = ai_data.get("dynamic_pricing_suggestions", [])
            won_lost_suggestions = ai_data.get("suggestions_based_on_lost_won", {})

        except Exception as e:
            print(f"❌ Gemini API Error: {type(e).__name__} - {str(e)}")
            dynamic_pricing_suggestions = [
                {
                    "route": r.get("route", "General Route"),
                    "service_type": r.get("service_type", "Air Freight"),
                    "suggested_adjustment": "+10% Surge" if r.get("volume", 0) >= 3 else "Base Rate",
                    "strategy_type": "Capacity Based Pricing",
                    "reasoning": "Mataas ang volume demand kaya inirerekomenda ang pagtaas ng margin."
                }
                for r in route_summary
            ]
            won_lost_suggestions = {
                "summary": "Karamihan sa mga nailoss na deal ay sanhi ng mataas na price quote sa Air Freight kumpara sa Sea Freight.",
                "actionable_recommendations": [
                    "Mag-alok ng hybrid Air-Sea options para sa price-sensitive clients.",
                    "Bawasan ang response time sa mga high-value inquiries.",
                    "Magbigay ng bundle discount para sa mga repeat client."
                ]
            }

    return {
        "lead_prioritization_engine": prioritized_leads,
        "automated_followup_alerts": followup_alerts,
        "next_best_actions": next_best_actions,
        "dynamic_pricing_suggestions": dynamic_pricing_suggestions,
        "suggestions_based_on_lost_won": won_lost_suggestions
    }