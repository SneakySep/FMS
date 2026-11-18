import base64, json
from supabase import create_client, Client
from app.config.config import settings
from upstash_redis import Redis
from google import genai


def jwt_role(key: str) -> str:
    """Basahin ang role claim ng Supabase key (anon o service_role)."""
    try:
        payload = key.split(".")[1]
        payload += "=" * (-len(payload) % 4)
        return json.loads(base64.urlsafe_b64decode(payload)).get("role", "unknown")
    except Exception:
        return "invalid"


print(f"DEBUG SECONDARY URL: {settings.SUPABASE_URL_2}")
print(f"DEBUG SECONDARY DB KEY ROLE: {jwt_role(settings.SUPABASE_SERVICE_KEY_2)}")  # service_role
print(f"DEBUG SECONDARY AUTH KEY ROLE: {jwt_role(settings.SUPABASE_KEY_2)}")  # anon

# Primary 
supabase: Client = create_client(settings.SUPABASE_URL, settings.SUPABASE_SERVICE_KEY)

# Secondary -> service_role
supabase_secondary: Client = create_client(settings.SUPABASE_URL_2, settings.SUPABASE_SERVICE_KEY_2)


# Secondary
def get_auth_client_secondary() -> Client:
    return create_client(settings.SUPABASE_URL_2, settings.SUPABASE_KEY_2)


redis_client = Redis(
    url=settings.UPSTASH_REDIS_REST_URL,
    token=settings.UPSTASH_REDIS_REST_TOKEN
)

gemini_client = genai.Client(api_key=settings.GEMINI_API_KEY)

LOGISTIC_API_KEY = settings.LOGISTIC_API_KEY