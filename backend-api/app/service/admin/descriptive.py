import pandas as pd
import numpy as np
from datetime import datetime
from app.supabase_config.supabase import supabase_secondary
from typing import Dict, Any

def get_descriptive_analytics() -> Dict[str, Any]:
    # 1. Fetch data from both 'tickets' and 'inquiries' tables
    res_tickets = supabase_secondary.table("tickets").select("*").execute()
    res_inquiries = supabase_secondary.table("inquiries").select("*").execute()

    tickets = res_tickets.data if res_tickets.data else []
    inquiries = res_inquiries.data if res_inquiries.data else []

    df_tickets = pd.DataFrame(tickets)
    df_inquiries = pd.DataFrame(inquiries)

    now = datetime.now()
    curr_year, curr_month = now.year, now.month
    
    # Standard Python datetime 
    prev_month = 12 if curr_month == 1 else curr_month - 1
    prev_year = curr_year - 1 if curr_month == 1 else curr_year

    def calc_pct(curr: float, prev: float) -> float:
        if prev == 0:
            return 100.0 if curr > 0 else 0.0
        return round(((curr - prev) / prev) * 100, 1)

    # -------------------------------------------------------------
    # METRICS FROM INQUIRIES TABLE
    # -------------------------------------------------------------
    quotes_curr, quotes_change = 0, 0.0
    inq_curr, inq_change = 0, 0.0
    open_curr, open_change = 0, 0.0
    won_cnt, lost_cnt, won_pct, lost_pct = 0, 0, 0.0, 0.0

    if not df_inquiries.empty:
        df_inquiries["created_at"] = pd.to_datetime(df_inquiries["created_at"], errors="coerce")
        df_inquiries["status"] = df_inquiries["status"].fillna("")

        inq_curr_df = df_inquiries[(df_inquiries["created_at"].dt.year == curr_year) & (df_inquiries["created_at"].dt.month == curr_month)]
        inq_prev_df = df_inquiries[(df_inquiries["created_at"].dt.year == prev_year) & (df_inquiries["created_at"].dt.month == prev_month)]

        # Quotes Issued
        quotes_curr = int((inq_curr_df["status"] == "quote_sent").sum())
        quotes_prev = int((inq_prev_df["status"] == "quote_sent").sum())
        quotes_change = calc_pct(quotes_curr, quotes_prev)

        # New Inquiries
        inq_curr = int((inq_curr_df["status"] == "new_inquiry").sum())
        inq_prev = int((inq_prev_df["status"] == "new_inquiry").sum())
        inq_change = calc_pct(inq_curr, inq_prev)

        # Open Deals
        open_statuses = ["new_inquiry", "qualifying", "negotiation", "quote_sent"]
        open_curr = int(inq_curr_df[inq_curr_df["status"].isin(open_statuses)]["id"].count())
        open_prev = int(inq_prev_df[inq_prev_df["status"].isin(open_statuses)]["id"].count())
        open_change = calc_pct(open_curr, open_prev)

        # Closed Won vs Lost Ratio
        won_cnt = int((df_inquiries["status"] == "closed_won").sum())
        lost_cnt = int((df_inquiries["status"] == "closed_lost").sum())
        tot = won_cnt + lost_cnt
        won_pct = round((won_cnt / tot * 100), 1) if tot > 0 else 0.0
        lost_pct = round((lost_cnt / tot * 100), 1) if tot > 0 else 0.0

    # -------------------------------------------------------------
    # METRICS FROM TICKETS TABLE (REVENUE)
    # -------------------------------------------------------------
    rev_curr, rev_change = 0.0, 0.0
    monthly_trend, weekly_trend = [], []
    sales_by_service = []
    top_cust = []

    if not df_tickets.empty:
        df_tickets["agreed_amount"] = pd.to_numeric(df_tickets["agreed_amount"], errors="coerce").fillna(0.0)
        df_tickets["created_at"] = pd.to_datetime(df_tickets["created_at"], errors="coerce")
        df_tickets["service_type"] = df_tickets["service_type"].fillna("Unspecified")
        df_tickets["company_name"] = df_tickets["company_name"].fillna("Unknown Customer")

        # Total Revenue MoM
        tix_curr_df = df_tickets[(df_tickets["created_at"].dt.year == curr_year) & (df_tickets["created_at"].dt.month == curr_month)]
        tix_prev_df = df_tickets[(df_tickets["created_at"].dt.year == prev_year) & (df_tickets["created_at"].dt.month == prev_month)]

        rev_curr = float(tix_curr_df["agreed_amount"].sum())
        rev_prev = float(tix_prev_df["agreed_amount"].sum())
        rev_change = calc_pct(rev_curr, rev_prev)

        # Revenue Trend (Monthly & Weekly)
        valid_tickets = df_tickets[df_tickets["created_at"].notna()].copy()
        
        if not valid_tickets.empty:
            # Grouping Month (MS) 
            df_m = valid_tickets.set_index("created_at").groupby(pd.Grouper(freq="MS"))["agreed_amount"].sum().reset_index()
            monthly_trend = [
                {"label": str(r["created_at"].strftime("%b")), "revenue": float(r["agreed_amount"])} 
                for _, r in df_m.iterrows()
            ]

            # Grouping Week (W-MON) 
            df_w = valid_tickets.set_index("created_at").groupby(pd.Grouper(freq="W-MON"))["agreed_amount"].sum().reset_index()
            weekly_trend = [
                {"label": "W" + str(r["created_at"].strftime("%U")), "revenue": float(r["agreed_amount"])} 
                for _, r in df_w.tail(8).iterrows()
            ]

        # Sales by Service Type
        service_grp = df_tickets.groupby("service_type")["agreed_amount"].sum().reset_index()
        sales_by_service = [
            {"service_type": str(r["service_type"]), "revenue": float(r["agreed_amount"])}
            for _, r in service_grp.iterrows()
        ]

        # Top Accounts by Revenue
        cust_df = df_tickets.groupby(["company_name", "service_type"])["agreed_amount"].sum().reset_index()
        cust_df = cust_df.sort_values(by="agreed_amount", ascending=False).head(5)
        max_r = float(cust_df["agreed_amount"].max()) if not cust_df.empty and cust_df["agreed_amount"].max() > 0 else 1.0

        top_cust = [
            {
                "company_name": str(r["company_name"]),
                "service_type": str(r["service_type"]),
                "revenue": float(r["agreed_amount"]),
                "bar_width": round((float(r["agreed_amount"]) / max_r) * 100, 1)
            }
            for _, r in cust_df.iterrows()
        ]

    return {
        "kpi_snapshot": {
            "total_revenue": {"value": rev_curr, "change_pct": rev_change},
            "quotes_issued": {"value": quotes_curr, "change_pct": quotes_change},
            "new_inquiries": {"value": inq_curr, "change_pct": inq_change},
            "open_deals": {"value": open_curr, "change_pct": open_change}
        },
        "revenue_trend": {"monthly": monthly_trend, "weekly": weekly_trend},
        "closed_won_vs_lost": {"won": won_cnt, "lost": lost_cnt, "won_pct": won_pct, "lost_pct": lost_pct},
        "sales_by_service_type": sales_by_service,
        "top_customers": top_cust
    }