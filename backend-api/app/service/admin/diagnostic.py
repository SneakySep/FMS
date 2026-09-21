import re
import pandas as pd
import numpy as np
from datetime import datetime, timezone
from typing import Dict, Any

# Scikit-Learn Imports
from sklearn.linear_model import LogisticRegression
from sklearn.preprocessing import OneHotEncoder
from sklearn.feature_extraction.text import TfidfVectorizer

# Supabase Config
from app.supabase_config.supabase import supabase_secondary


def classify_lost_reason(text: str) -> str:
    """Ina-categorize ang free-form sales notes sa standard loss reasons gamit ang NLP rules."""
    if not text or pd.isna(text) or str(text).strip() == "":
        return "No Reason Stated"

    text_lower = str(text).lower()

    # Keyword Pattern Matching Rules
    patterns = {
        "High Price / Rates": [r"price", r"mahal", r"expensive", r"budget", r"rate", r"cost", r"high", r"quotation"],
        "Competitor Won": [r"competitor", r"ibang supplier", r"mas mura sa iba", r"another vendor", r"cheaper"],
        "Service / Route Unavailable": [r"no route", r"unserviceable", r"not available", r"walang biyahe", r"capacity", r"full"],
        "Client Unresponsive / Ghosted": [r"no response", r"ghosted", r"unreachable", r"di sumasagot", r"cancelled", r"no reply"],
        "Delay in Response": [r"matagal", r"late response", r"took too long", r"delayed"]
    }

    for category, keywords in patterns.items():
        if any(re.search(kw, text_lower) for kw in keywords):
            return category

    return "Other / Custom Reason"


def get_diagnostic_analytics() -> Dict[str, Any]:
    """Main Diagnostic Analytics Function."""
    # 1. Fetch Data mula sa Supabase
    res_tickets = supabase_secondary.table("tickets").select("*").execute()
    res_inquiries = supabase_secondary.table("inquiries").select("*").execute()

    tickets = res_tickets.data if res_tickets.data else []
    inquiries = res_inquiries.data if res_inquiries.data else []

    df_tickets = pd.DataFrame(tickets)
    df_inquiries = pd.DataFrame(inquiries)

    # Initial Structure ng Response
    lead_source_analysis = []
    deal_aging = {"avg_days_to_close": 0.0, "aging_brackets": {"0-7_days": 0, "8-14_days": 0, "15-30_days": 0, "30+_days": 0}}
    lost_deal_analysis = {
        "total_lost": 0,
        "reasons_breakdown": [],
        "top_keywords": [],
        "ai_summary": "Walang naitalang closed lost deals."
    }
    win_rate_route = []
    customer_segments = []

    # -------------------------------------------------------------
    # A. INQUIRIES ANALYTICS (Platform Effectiveness, Deal Aging, NLP Lost Notes)
    # -------------------------------------------------------------
    if not df_inquiries.empty:
        # 1. PLATFORM EFFECTIVENESS
        df_inquiries["platform_used"] = df_inquiries["platform_used"].fillna("Unknown")
        df_inquiries["status"] = df_inquiries["status"].fillna("")
        df_inquiries["is_won"] = (df_inquiries["status"] == "closed_won").astype(int)

        source_grp = df_inquiries.groupby("platform_used").agg(
            total_leads=("id", "count"),
            won_leads=("is_won", "sum")
        ).reset_index()

        source_grp["win_rate"] = np.where(
            source_grp["total_leads"] > 0,
            np.round((source_grp["won_leads"] / source_grp["total_leads"]) * 100, 1),
            0.0
        )

        # Scikit-Learn Machine Learning: Logistic Regression
        if len(df_inquiries) >= 5 and df_inquiries["is_won"].nunique() > 1:
            try:
                X_raw = df_inquiries[["platform_used"]]
                y = df_inquiries["is_won"]

                encoder = OneHotEncoder(sparse_output=False, handle_unknown='ignore')
                X_encoded = encoder.fit_transform(X_raw)

                model = LogisticRegression()
                model.fit(X_encoded, y)

                unique_sources = pd.DataFrame({"platform_used": source_grp["platform_used"]})
                unique_encoded = encoder.transform(unique_sources)
                probs = model.predict_proba(unique_encoded)[:, 1]

                source_grp["ml_quality_score"] = np.round(probs * 100, 1)
            except Exception:
                source_grp["ml_quality_score"] = source_grp["win_rate"]
        else:
            source_grp["ml_quality_score"] = source_grp["win_rate"]

        lead_source_analysis = [
            {
                "source": str(r["platform_used"]),
                "total_leads": int(r["total_leads"]),
                "won_leads": int(r["won_leads"]),
                "win_rate": float(r["win_rate"]),
                "quality_score": float(r["ml_quality_score"])
            }
            for _, r in source_grp.iterrows()
        ]

        # 2. DEAL AGING REPORT (Fixed: No updated_at in schema)
        df_inquiries["created_at"] = pd.to_datetime(df_inquiries["created_at"], errors="coerce", utc=True)
        
        # Gagamit ng pickup_datetime kung mayroon, else kasalukuyang oras bilang end point
        if "pickup_datetime" in df_inquiries.columns:
            df_inquiries["end_time"] = pd.to_datetime(df_inquiries["pickup_datetime"], errors="coerce", utc=True)
            df_inquiries["end_time"] = df_inquiries["end_time"].fillna(pd.Timestamp.now(tz=timezone.utc))
        else:
            df_inquiries["end_time"] = pd.Timestamp.now(tz=timezone.utc)

        closed_deals = df_inquiries[df_inquiries["status"].isin(["closed_won", "closed_loss"])].copy()

        if not closed_deals.empty:
            closed_deals["days_to_close"] = (closed_deals["end_time"] - closed_deals["created_at"]).dt.days
            closed_deals["days_to_close"] = closed_deals["days_to_close"].fillna(0).clip(lower=0)

            avg_days = float(closed_deals["days_to_close"].mean())

            deal_aging = {
                "avg_days_to_close": round(avg_days, 1),
                "aging_brackets": {
                    "0-7_days": int((closed_deals["days_to_close"] <= 7).sum()),
                    "8-14_days": int(((closed_deals["days_to_close"] > 7) & (closed_deals["days_to_close"] <= 14)).sum()),
                    "15-30_days": int(((closed_deals["days_to_close"] > 14) & (closed_deals["days_to_close"] <= 30)).sum()),
                    "30+_days": int((closed_deals["days_to_close"] > 30).sum())
                }
            }

        # 3. NLP LOST DEAL REASONS (Parsing Freeform Sales Notes)
        lost_deals = df_inquiries[df_inquiries["status"] == "closed_lost"].copy()

        if not lost_deals.empty:
            lost_deals["notes_text"] = lost_deals["notes"].fillna("")
            lost_deals["categorized_reason"] = lost_deals["notes_text"].apply(classify_lost_reason)

            tot_lost = len(lost_deals)
            reason_grp = lost_deals.groupby("categorized_reason").size().reset_index(name="count")
            reason_grp["percentage"] = np.round((reason_grp["count"] / tot_lost) * 100, 1)

            reasons_breakdown = [
                {
                    "reason": str(r["categorized_reason"]),
                    "count": int(r["count"]),
                    "percentage": float(r["percentage"])
                }
                for _, r in reason_grp.sort_values(by="count", ascending=False).iterrows()
            ]

            # TF-IDF Feature Extraction
            top_keywords = []
            valid_notes = lost_deals[lost_deals["notes_text"].str.strip() != ""]["notes_text"].tolist()

            if len(valid_notes) >= 2:
                try:
                    vectorizer = TfidfVectorizer(stop_words='english', max_features=5, ngram_range=(1, 2))
                    tfidf_matrix = vectorizer.fit_transform(valid_notes)
                    feature_names = vectorizer.get_feature_names_out()
                    scores = tfidf_matrix.sum(axis=0).A1

                    keyword_data = sorted(zip(feature_names, scores), key=lambda x: x[1], reverse=True)
                    top_keywords = [str(kw[0]) for kw in keyword_data]
                except Exception:
                    top_keywords = []

            # AI Dynamic Summary
            top_reason = reasons_breakdown[0] if reasons_breakdown else None
            if top_reason and top_reason["reason"] != "No Reason Stated":
                ai_summary = f"Ang pangunahing dahilan ng pagkatalo ng deals ay '{top_reason['reason']}' na kumakatawan sa {top_reason['percentage']}% ng mga nawalang inquiries."
            else:
                ai_summary = "Karamihan sa mga lost deals ay walang nakalagay na detalyadong dahilan sa sales notes."

            lost_deal_analysis = {
                "total_lost": int(tot_lost),
                "reasons_breakdown": reasons_breakdown,
                "top_keywords": top_keywords,
                "ai_summary": str(ai_summary)
            }

    # -------------------------------------------------------------
    # B. TICKETS ANALYTICS (Route Performance & Customer Segment)
    # -------------------------------------------------------------
    if not df_tickets.empty:
        df_tickets["service_type"] = df_tickets["service_type"].fillna("Unspecified")
        df_tickets["origin"] = df_tickets["origin"].fillna("N/A")
        df_tickets["destination"] = df_tickets["destination"].fillna("N/A")
        df_tickets["route"] = df_tickets["origin"] + " → " + df_tickets["destination"]
        df_tickets["agreed_amount"] = pd.to_numeric(df_tickets["agreed_amount"], errors="coerce").fillna(0.0)
        df_tickets["company_name"] = df_tickets["company_name"].fillna("Unknown Customer")

        # Route Performance
        route_grp = df_tickets.groupby(["service_type", "route"]).agg(
            total_booked=("id", "count"),
            total_revenue=("agreed_amount", "sum")
        ).reset_index()

        win_rate_route = [
            {
                "service_type": str(r["service_type"]),
                "route": str(r["route"]),
                "total_booked": int(r["total_booked"]),
                "revenue": float(r["total_revenue"])
            }
            for _, r in route_grp.iterrows()
        ]

        # Customer Segment Performance
        cust_grp = df_tickets.groupby(["company_name", "service_type", "route"]).agg(
            total_orders=("id", "count"),
            total_revenue=("agreed_amount", "sum")
        ).reset_index()

        customer_segments = [
            {
                "company_name": str(r["company_name"]),
                "service_type": str(r["service_type"]),
                "route": str(r["route"]),
                "total_orders": int(r["total_orders"]),
                "revenue": float(r["total_revenue"])
            }
            for _, r in cust_grp.iterrows()
        ]

    # -------------------------------------------------------------
    # FINAL JSON RETURN STRUCT
    # -------------------------------------------------------------
    return {
        "lead_source_effectiveness": lead_source_analysis,
        "deal_aging_report": deal_aging,
        "lost_deal_analysis": lost_deal_analysis,
        "win_rate_by_route": win_rate_route,
        "customer_segment_performance": customer_segments
    }