from unittest.mock import MagicMock, patch

import pytest

import sys

sys.modules["weasyprint"] = MagicMock()


@pytest.mark.asyncio
@patch("app.service.sintement.gemini_client")
async def test_sentiment_analysis_logic(mock_gemini_client):
    from app.service.sintement import analyze_message_and_extracted_details

    mock_response = MagicMock()
    mock_response.text = """{
        "reply": "Kailan po ang target date ng delivery?",
        "sentiment": "neutral",
        "updated_details": {"origin": "Manila", "destination": "Cebu"},
        "is_complete": false,
        "force_handoff": false
    }"""

    # generate_content is synchronous
    mock_gemini_client.models.generate_content.return_value = mock_response

    result = await analyze_message_and_extracted_details("Manila to Cebu", {})

    assert result["sentiment"] == "neutral"
    assert result["updated_details"]["origin"] == "Manila"
    assert result["is_complete"] is False