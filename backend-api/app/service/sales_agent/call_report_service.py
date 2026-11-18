import os
import joblib
import pandas as pd
import numpy as np
from app.supabase_config.supabase import supabase_secondary

class CallReportService:
    def __init__(self):
        self.supabase = supabase_secondary
        self.model_path = "app/ml_models/call_report_classifier.pkl"
        self.model = self._load_model()

    def _load_model(self):
        """I-load ang trained Scikit-Learn model mula sa .pkl file"""
        if os.path.exists(self.model_path):
            try:
                return joblib.load(self.model_path)
            except Exception as e:
                print(f"Error loading Scikit-Learn model: {e}")
        return None

    def predict_status_and_rating(self, remarks: str):
        """
        Gumagamit ng Pandas series at Scikit-Learn model para sa totoong hula
        batay sa remarks ng sales agent.
        """
        if self.model:
            try:
                # Gumamit ng Pandas Series para sa input ng model
                input_data = pd.Series([remarks])
                
                # Patakbuhin ang prediction
                prediction = self.model.predict(input_data)
                
                # Ang output ay array na may [status, rating]
                status = str(prediction[0][0])
                rating = str(prediction[0][1])
                
                # Kumuha ng confidence score kung kaya ng model (average probabilities)
                probabilities = self.model.predict_proba(input_data)
                confidence = float(np.mean([np.max(p) for p in probabilities]))
                
                return status, rating, round(confidence, 4)
            except Exception as e:
                print(f"Prediction error: {e}")
        
        # Fallback kung sakaling wala pang model file
        return "General Inquiry", "Neutral", 0.5000

    async def create_call_report(self, agent_id: str, data):
        # 1. Patakbuhin ang ML prediction gamit ang remarks
        status, rating, confidence = self.predict_status_and_rating(data.remarks)

        # 2. I-save sa Supabase database kasama ang ML outputs
        insert_data = {
            "agent_id": agent_id,
            "call_time": data.call_time,
            "client_name": data.client_name,
            "contact_person": data.contact_person,
            "contact_tel_no": data.contact_tel_no,
            "contact_email": data.contact_email,
            "remarks": data.remarks,
            "status": status,
            "rating": rating,
            "confidence_score": confidence,
            "is_overridden": False
        }

        response = self.supabase.table("daily_call_reports").insert(insert_data).execute()
        if not response.data:
            raise Exception("Failed to save call report to database.")

        return response.data[0]

    async def get_printable_reports(self, report_date: str = None):
        query = self.supabase.table("daily_call_reports").select("*")
        if report_date:
            query = query.eq("report_date", report_date)
        
        response = query.order("created_at", desc=True).execute()
        return response.data if response.data else []