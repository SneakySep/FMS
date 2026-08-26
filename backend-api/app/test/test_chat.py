import pytest
from unittest.mock import AsyncMock, MagicMock, patch
from fastapi import FastAPI
from fastapi.testclient import TestClient
from app.routes.customers.chat import router

app = FastAPI()
app.include_router(router)
client = TestClient(app)

TEST_CUSTOMER_ID = "cust-12345"
VALID_CHAT_PAYLOAD = {
    "customer_id": TEST_CUSTOMER_ID,
    "message": "Kumusta, anong status ng booking ko?"
}


# Async Iterator Helper para sa Motor MongoDB cursor mocking
class AsyncCursorMock:
    def __init__(self, documents):
        self.documents = documents

    def __aiter__(self):
        self._iter = iter(self.documents)
        return self

    async def __anext__(self):
        try:
            return next(self._iter)
        except StopIteration:
            raise StopAsyncIteration


# ===================================================
# 1. POST /api/v1/chat TESTS
# ===================================================

@patch("app.routes.customers.chat.process_customer_chat", new_callable=AsyncMock)
def test_chat_endpoint_success(mock_process_chat):
    mock_process_chat.return_value = "Ang inyong booking ay confirmed na."

    response = client.post("/api/v1/chat", json=VALID_CHAT_PAYLOAD)

    assert response.status_code == 200
    assert response.json()["status"] == "success"
    assert response.json()["reply"] == "Ang inyong booking ay confirmed na."
    mock_process_chat.assert_called_once_with(
        customer_id=TEST_CUSTOMER_ID,
        user_message="Kumusta, anong status ng booking ko?"
    )


@patch("app.routes.customers.chat.process_customer_chat", new_callable=AsyncMock)
def test_chat_endpoint_service_exception(mock_process_chat):
    mock_process_chat.side_effect = Exception("AI Service connection timeout")

    response = client.post("/api/v1/chat", json=VALID_CHAT_PAYLOAD)

    assert response.status_code == 500
    assert response.json()["detail"] == "AI Service connection timeout"


# ===================================================
# 2. GET /api/v1/chat/active-conversations TESTS
# ===================================================

@patch("app.routes.customers.chat.chat_collection.find")
def test_get_active_conversations_success(mock_find):
    fake_docs = [
        {
            "customer_id": "cust-001",
            "history": [{"parts": [{"text": "Hello AI!"}]}],
            "status": "ai_active"
        },
        {
            "customer_id": "cust-002",
            "history": [],
            "status": "human_agent"
        }
    ]
    mock_find.return_value = AsyncCursorMock(fake_docs)

    response = client.get("/api/v1/chat/active-conversations")

    assert response.status_code == 200
    data = response.json()
    assert len(data) == 2
    assert data[0]["customer_id"] == "cust-001"
    assert data[0]["last_message"] == "Hello AI!"
    assert data[1]["last_message"] == "No messages"


@patch("app.routes.customers.chat.chat_collection.find")
def test_get_active_conversations_exception(mock_find):
    mock_find.side_effect = Exception("MongoDB query error")

    response = client.get("/api/v1/chat/active-conversations")

    assert response.status_code == 500
    assert response.json()["detail"] == "MongoDB query error"


# ===================================================
# 3. GET /api/v1/chat/history/{customer_id} TESTS
# ===================================================

@patch("app.routes.customers.chat.chat_collection.find_one", new_callable=AsyncMock)
def test_get_chat_history_found(mock_find_one):
    mock_find_one.return_value = {
        "customer_id": TEST_CUSTOMER_ID,
        "history": [{"role": "user", "parts": [{"text": "Hi"}]}]
    }

    response = client.get(f"/api/v1/chat/history/{TEST_CUSTOMER_ID}")

    assert response.status_code == 200
    assert response.json()["customer_id"] == TEST_CUSTOMER_ID
    assert len(response.json()["history"]) == 1


@patch("app.routes.customers.chat.chat_collection.find_one", new_callable=AsyncMock)
def test_get_chat_history_not_found(mock_find_one):
    mock_find_one.return_value = None

    response = client.get(f"/api/v1/chat/history/{TEST_CUSTOMER_ID}")

    assert response.status_code == 200
    assert response.json() == {"history": []}


@patch("app.routes.customers.chat.chat_collection.find_one", new_callable=AsyncMock)
def test_get_chat_history_exception(mock_find_one):
    mock_find_one.side_effect = Exception("Database error")

    response = client.get(f"/api/v1/chat/history/{TEST_CUSTOMER_ID}")

    assert response.status_code == 500
    assert response.json()["detail"] == "Database error"


# ===================================================
# 4. POST /api/v1/chat/handover/{customer_id} TESTS
# ===================================================

@patch("app.routes.customers.chat.chat_collection.update_one", new_callable=AsyncMock)
def test_handover_to_ai_success(mock_update_one):
    mock_update_one.return_value = None

    response = client.post(f"/api/v1/chat/handover/{TEST_CUSTOMER_ID}")

    assert response.status_code == 200
    assert response.json()["status"] == "success"
    assert response.json()["message"] == "Handed back to AI successfully."
    mock_update_one.assert_called_once_with(
        {"customer_id": TEST_CUSTOMER_ID},
        {"$set": {"status": "ai_active"}}
    )


@patch("app.routes.customers.chat.chat_collection.update_one", new_callable=AsyncMock)
def test_handover_to_ai_exception(mock_update_one):
    mock_update_one.side_effect = Exception("Update failed")

    response = client.post(f"/api/v1/chat/handover/{TEST_CUSTOMER_ID}")

    assert response.status_code == 500
    assert response.json()["detail"] == "Update failed"


# ===================================================
# 5. DELETE /api/v1/chat/history/{customer_id} TESTS
# ===================================================

@patch("app.routes.customers.chat.chat_collection.delete_one", new_callable=AsyncMock)
def test_clear_chat_history_success(mock_delete_one):
    mock_delete_one.return_value = None

    response = client.delete(f"/api/v1/chat/history/{TEST_CUSTOMER_ID}")

    assert response.status_code == 200
    assert response.json()["status"] == "success"
    assert response.json()["message"] == "Chat history cleared successfully."
    mock_delete_one.assert_called_once_with({"customer_id": TEST_CUSTOMER_ID})


@patch("app.routes.customers.chat.chat_collection.delete_one", new_callable=AsyncMock)
def test_clear_chat_history_exception(mock_delete_one):
    mock_delete_one.side_effect = Exception("Delete failed")

    response = client.delete(f"/api/v1/chat/history/{TEST_CUSTOMER_ID}")

    assert response.status_code == 500
    assert response.json()["detail"] == "Delete failed"