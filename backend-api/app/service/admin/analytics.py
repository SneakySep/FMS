import pandas as pd
import numpy as np
from datetime import datetime, timedelta
from typing import Dict, Any, List

def get_admin_dashboard_kpis(inquiries_data: List[Dict[str, Any]], tickets_data: List[Dict[str, Any]] = None) -> Dict[str, Any]:
    
    df_inquiries = pd.DataFrame(inquiries_data) if inquiries_data else pd.DataFrame()
    now = datetime.now()

    # 1. NEW LEADS - WEEKLY BAR CHART DATA 
    days_labels = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri']
    weekly_leads_counts = {day: 0 for day in days_labels}

    if not df_inquiries.empty and 'created_at' in df_inquiries.columns:
        df_inquiries['created_at_dt'] = pd.to_datetime(df_inquiries['created_at'], errors='coerce')
        
        # FIX 
        if df_inquiries['created_at_dt'].dt.tz is not None:
            df_inquiries['created_at_dt'] = df_inquiries['created_at_dt'].dt.tz_localize(None)

        # Kunin ang start date nitong kasalukuyang linggo
        start_of_week = now - timedelta(days=now.weekday())
        start_of_week = start_of_week.replace(hour=0, minute=0, second=0, microsecond=0)
        
        recent_inquiries = df_inquiries[df_inquiries['created_at_dt'] >= start_of_week]

        for dt in recent_inquiries['created_at_dt']:
            if pd.notnull(dt):
                day_name = dt.strftime('%a')
                if day_name in weekly_leads_counts:
                    weekly_leads_counts[day_name] += 1

    # 2. SUCCESSFUL DEALS 
    successful_deals_rate = 0.0
    total_won_count = 0
    total_inquiries_count = len(df_inquiries)

    if not df_inquiries.empty and 'status' in df_inquiries.columns:
        df_inquiries['status_clean'] = df_inquiries['status'].astype(str).str.lower().str.strip()
        
        # Hanapin ang mga closed won o approved deals
        won_mask = df_inquiries['status_clean'].str.contains('won|closed_won|completed|approved', na=False)
        total_won_count = int(df_inquiries[won_mask].shape[0])

        if total_inquiries_count > 0:
            successful_deals_rate = float(np.round((total_won_count / total_inquiries_count) * 100, 1))

    # 3. FOLLOW-UPS NEEDED (Matagal ma-replyan na inquiries)
    followups_needed_count = 0

    if not df_inquiries.empty and 'created_at' in df_inquiries.columns:
        active_statuses = ['new', 'new_inquiry', 'pending', 'qualifying', 'quote_sent', 'negotiation']
        active_mask = df_inquiries['status_clean'].isin(active_statuses)
        
        time_col = 'updated_at' if 'updated_at' in df_inquiries.columns else 'created_at'
        df_inquiries['last_active'] = pd.to_datetime(df_inquiries[time_col], errors='coerce')

        # FIX 
        if df_inquiries['last_active'].dt.tz is not None:
            df_inquiries['last_active'] = df_inquiries['last_active'].dt.tz_localize(None)

        # Threshold: 2 araw o higit pa na walang activity
        threshold_time = datetime.now() - timedelta(days=2)

        needs_attention = df_inquiries[active_mask & (df_inquiries['last_active'] <= threshold_time)]
        followups_needed_count = int(needs_attention.shape[0])

    # 4. TOTAL PREPAYMENTS 
    prepayments_total = 0.0

    return {
        "status": "success",
        "data": {
            "new_leads_chart": {
                "labels": list(weekly_leads_counts.keys()),
                "counts": list(weekly_leads_counts.values())
            },
            "successful_deals": {
                "rate": successful_deals_rate,
                "total_won": total_won_count,
                "total_inquiries": total_inquiries_count
            },
            "followups_needed": followups_needed_count,
            "prepayments": {
                "amount": prepayments_total,
                "currency": "PHP",
                "status": "pending_integration"
            }
        }
    }