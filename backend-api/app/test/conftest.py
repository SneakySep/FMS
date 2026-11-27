import os


def pytest_configure(config):
    """Force-load dummy environment variables before pytest collects modules."""
    os.environ.setdefault("SUPABASE_URL", "https://mock.supabase.co")
    os.environ.setdefault("SUPABASE_KEY", "mock-supabase-key")
    os.environ.setdefault("SUPABASE_SERVICE_KEY", "mock-service-key")
    os.environ.setdefault("SUPABASE_JWT_SECRET", "mock-jwt-secret")
    os.environ.setdefault("UPSTASH_REDIS_REST_URL", "https://mock-redis.upstash.io")
    os.environ.setdefault("UPSTASH_REDIS_REST_TOKEN", "mock-redis-token")
    os.environ.setdefault("SMTP_SENDER", "test@example.com")
    os.environ.setdefault("SMTP_PASSWORD", "mock-smtp-password")
    os.environ.setdefault("SUPABASE_URL_2", "https://mock-supabase-url-2.supabase.co")
    os.environ.setdefault("SUPABASE_KEY_2", "mock-key-2")
    os.environ.setdefault("SUPABASE_SERVICE_KEY_2", "mock-service-key-2")
    os.environ.setdefault("GEMINI_API_KEY", "mock-gemini-key")
    os.environ.setdefault("MONGODB_URL", "mongodb://mock-mongo:27017")
    os.environ.setdefault("LOGISTIC_API_KEY", "mock-logistic-key")
