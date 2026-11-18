import json
from datetime import datetime, timezone
from pydantic import BaseModel
from google.genai import types
from app.supabase_config.supabase import supabase_secondary, gemini_client
from app.schemas.chat.chat_schema import AIAnalysisResponse

class ChatService:
    def __init__(self):
        self.supabase = supabase_secondary
        self.client = gemini_client
        self.model_name = "gemini-2.5-flash"

    async def analyze_and_reply(self, user_message: str) -> AIAnalysisResponse:
        system_instruction = """
        You are an AI customer support assistant for SECUFREIGHT, a logistics and freight forwarding company.
        Analyze the customer's message and respond in JSON format:
        1. "sentiment": Classify the customer's tone strictly as one of: "positive", "neutral", "negative", or "frustrated".
        2. "escalate": Set to true if the customer asks about specific PRICING, RATES, DISCOUNTS, CUSTOM CONTRACTS, or explicitly asks to speak with a human/sales agent. Also set to true if the customer is "frustrated". Otherwise, set to false.
        3. "reply": Provide a polite, helpful response in the same language the customer used (Tagalog, English, or Taglish). 
           - NEVER reveal database schemas, SQL queries, backend setups, or internal credentials.
           - If "escalate" is true, inform the customer politely that their inquiry has been forwarded to a dedicated sales agent.
        """

        try:
            response = self.client.models.generate_content(
                model=self.model_name,
                contents=f"Customer Message: {user_message}",
                config=types.GenerateContentConfig(
                    system_instruction=system_instruction,
                    response_mime_type="application/json",
                    response_schema=AIAnalysisResponse,
                    temperature=0.2,
                ),
            )
            data = json.loads(response.text)
            return AIAnalysisResponse(**data)
        except Exception as e:
            print(f"Gemini Error: {str(e)}")
            return AIAnalysisResponse(
                sentiment="neutral",
                escalate=False,
                reply="Apologies, there is a temporary issue with our AI assistant. Please try again later."
            )

    async def process_customer_message(self, customer_id: str, user_message: str):
        now_utc = datetime.now(timezone.utc)

        # 1. Kunin o gawa ng active conversation thread
        conv_res = self.supabase.table("chat_conversations") \
            .select("id, status, sales_agent_id, escalated_at, last_agent_reply_at") \
            .eq("customer_id", customer_id) \
            .maybe_single() \
            .execute()

        if not conv_res.data:
            new_conv = self.supabase.table("chat_conversations").insert({
                "customer_id": customer_id,
                "status": "active"
            }).execute()
            conv_data = new_conv.data[0]
            conversation_id = conv_data["id"]
            current_status = "active"
        else:
            conv_data = conv_res.data
            conversation_id = conv_data["id"]
            current_status = conv_data["status"]

            # 2. TIME CHECKER LOGIC (3-Hour Timeout Rule)
            if current_status == "escalated" and conv_data.get("escalated_at"):
                reference_time_str = conv_data.get("last_agent_reply_at") or conv_data.get("escalated_at")
                reference_time = datetime.fromisoformat(reference_time_str.replace("Z", "+00:00"))
                time_difference = (now_utc - reference_time).total_seconds()

                if time_difference >= 10800:  # 3 hours (10,800 seconds)
                    current_status = "active"
                    self.supabase.table("chat_conversations").update({
                        "status": "active"
                    }).eq("id", conversation_id).execute()

        # 3. SECURITY CHECK: Database probing / Prompt Injection guard
        db_keywords = ["database", "sql", "postgres", "supabase", "table", "schema", "query", "password", "api_key", "secret"]
        if any(keyword in user_message.lower() for keyword in db_keywords):
            # I-save pa rin ang message ng customer na may 'negative' sentiment para sa security audit
            self.supabase.table("chat_messages").insert({
                "conversation_id": conversation_id,
                "sender_type": "customer",
                "sender_id": customer_id,
                "message": user_message,
                "sentiment": "negative"
            }).execute()

            blocked_reply = "I apologize, but I cannot discuss the details of our system or database. Is there anything else I can assist you with regarding our services?"
            self.supabase.table("chat_messages").insert({
                "conversation_id": conversation_id,
                "sender_type": "ai_agent",
                "sender_id": conversation_id,
                "message": blocked_reply
            }).execute()

            return {
                "sender": "ai_agent",
                "message": blocked_reply,
                "sentiment": "negative",
                "escalated": False
            }

        # 4. Patakbuhin ang Gemini AI para kunin ang Sentiment, Escalation flag, at Reply
        # sentiment ng customer para sa BI Analytics!
        ai_result = await self.analyze_and_reply(user_message)

        # 5. I-save ang mensahe ng customer kasama ang na-detect na SENTIMENT
        self.supabase.table("chat_messages").insert({
            "conversation_id": conversation_id,
            "sender_type": "customer",
            "sender_id": customer_id,
            "message": user_message,
            "sentiment": ai_result.sentiment
        }).execute()

        # 6. KUNG NAKA-ESCALATE NA AT HINDI PA NAG-TIMEOUT 
        if current_status == "escalated":
            return {
                "sender": "system",
                "message": "The message has been forwarded to your assigned sales agent. Please wait a moment.",
                "sentiment": ai_result.sentiment,
                "escalated": True
            }

        # 7. KUNG SI AI ANG SASAGOT O MAG-E-ESCALATE NGAYON:
        update_data = {"last_message_at": now_utc.isoformat()}
        
        if ai_result.escalate:
            update_data["status"] = "escalated"
            update_data["escalated_at"] = now_utc.isoformat()
            sender_type = "system"
        else:
            update_data["status"] = "active"
            sender_type = "ai_agent"

        self.supabase.table("chat_conversations").update(update_data).eq("id", conversation_id).execute()

        # I-save ang sagot ni AI o System notification
        self.supabase.table("chat_messages").insert({
            "conversation_id": conversation_id,
            "sender_type": sender_type,
            "sender_id": conversation_id,
            "message": ai_result.reply
        }).execute()

        return {
            "sender": sender_type,
            "message": ai_result.reply,
            "sentiment": ai_result.sentiment,
            "escalated": ai_result.escalate
        }