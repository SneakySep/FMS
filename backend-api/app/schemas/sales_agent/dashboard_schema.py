from pydantic import BaseModel, Field
from typing import Dict, Any

class SalesKPIResponseSchema(BaseModel):
    success: bool = True
    message: str = "Dashboard KPIs fetched and analyzed successfully."
    data: Dict[str, Any]