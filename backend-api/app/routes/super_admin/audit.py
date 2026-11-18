from fastapi import APIRouter, HTTPException, Query
from typing import Optional, List
from app.supabase_config.supabase import supabase_secondary

router = APIRouter(prefix="/api/v1/super_admin/audit", tags=["Audit Trail"])
