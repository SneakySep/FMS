import uuid
from typing import List, Dict, Any, Optional
from datetime import datetime, timedelta, timezone
from fastapi import UploadFile
from app.supabase_config.supabase import supabase, supabase_secondary

# Fallback author kapag walang nahanap na profile sa created_by
DEFAULT_AUTHOR = {"name": "Account Team", "role": "Priority Handling"}

# Kung mas maliit sa 48 oras ang natitirang panahon -> "Ending Soon"
ENDING_SOON_WINDOW = timedelta(hours=48)


class CampaignService:

    @staticmethod
    async def create_campaign(
        title: str,
        description: Optional[str],
        is_permanent: bool,
        start_date: Optional[str],
        end_date: Optional[str],
        image_file: UploadFile,
        agent_id: Optional[str] = None
    ) -> Dict[str, Any]:
        try:
            # 1. Upload Poster Image sa Supabase Storage
            file_ext = image_file.filename.split(".")[-1]
            file_name = f"{uuid.uuid4()}.{file_ext}"
            file_content = await image_file.read()

            upload_response = supabase_secondary.storage.from_("campaign-posters").upload(
                file_name,
                file_content,
                file_options={"content-type": image_file.content_type}
            )

            # Kunin ang Public Image URL
            image_url = supabase_secondary.storage.from_("campaign-posters").get_public_url(file_name)

            # 2. Prepare Database Record
            payload = {
                "title": title,
                "description": description,
                "image_url": image_url,
                "is_permanent": is_permanent,
                "created_by": agent_id,
                "is_active": True
            }

            if not is_permanent:
                payload["start_date"] = start_date if start_date else datetime.now(timezone.utc).isoformat()
                payload["end_date"] = end_date
            else:
                payload["start_date"] = datetime.now(timezone.utc).isoformat()
                payload["end_date"] = None

            # 3. Save Record to Database
            db_response = supabase_secondary.table("campaign_posts").insert(payload).execute()

            return {"status": "success", "data": db_response.data}

        except Exception as e:
            raise Exception(f"Failed to create campaign post: {str(e)}")

    @staticmethod
    def _parse_dt(value: Optional[str]) -> Optional[datetime]:
        """I-convert ang ISO string papuntang aware datetime (UTC kung walang zone)."""
        if not value:
            return None
        try:
            dt = datetime.fromisoformat(str(value).replace("Z", "+00:00"))
        except (ValueError, TypeError):
            return None
        if dt.tzinfo is None:
            dt = dt.replace(tzinfo=timezone.utc)
        return dt

    @staticmethod
    def _resolve_authors(created_by_values: List[Any]) -> Dict[str, Dict[str, str]]:
        """
        I-resolve ang campaign_posts.created_by papuntang author name/role.
        Priority 1: profiles (Primary DB - sales agents/admin)
        Priority 2: users (Secondary DB - customer-side accounts)
        Return: { user_id: {"name": str, "role": str} }
        """
        ids = [str(v).strip() for v in created_by_values if v and str(v).strip()]
        ids = list(dict.fromkeys(ids))  # unique, preserve order
        if not ids:
            return {}

        authors: Dict[str, Dict[str, str]] = {}

        # Priority 1: Primary DB profiles table
        try:
            res = (
                supabase.table("profiles")
                .select("id, first_name, last_name, full_name, role")
                .in_("id", ids)
                .execute()
            )
            for row in res.data or []:
                name = (row.get("full_name") or "").strip()
                if not name:
                    name = f"{row.get('first_name') or ''} {row.get('last_name') or ''}".strip()
                if not name:
                    continue
                authors[str(row.get("id"))] = {
                    "name": name,
                    "role": (row.get("role") or "Sales Agent").replace("_", " ").title(),
                }
        except Exception as err:
            print(f"[Campaign Author Lookup - profiles Error]: {str(err)}")

        # Priority 2: Secondary DB users table (para sa mga hindi nahanap)
        missing = [i for i in ids if i not in authors]
        if missing:
            try:
                res = (
                    supabase_secondary.table("users")
                    .select("id, first_name, last_name, full_name, role")
                    .in_("id", missing)
                    .execute()
                )
                for row in res.data or []:
                    name = (row.get("full_name") or "").strip()
                    if not name:
                        name = f"{row.get('first_name') or ''} {row.get('last_name') or ''}".strip()
                    if not name:
                        continue
                    authors[str(row.get("id"))] = {
                        "name": name,
                        "role": (row.get("role") or "Account Team").replace("_", " ").title(),
                    }
            except Exception as err:
                print(f"[Campaign Author Lookup - users Error]: {str(err)}")

        return authors

    @staticmethod
    def get_active_campaigns_for_customer() -> List[Dict[str, Any]]:
        try:
            now = datetime.now(timezone.utc)

            # Query: Lahat ng is_active = True, bago muna ang pinaka-new
            response = (
                supabase_secondary.table("campaign_posts")
                .select("*")
                .eq("is_active", True)
                .order("created_at", desc=True)
                .execute()
            )

            all_campaigns = response.data or []

            # I-resolve ang author names sa isang batch query lang
            authors = CampaignService._resolve_authors(
                [item.get("created_by") for item in all_campaigns]
            )

            active_campaigns = []
            for item in all_campaigns:
                is_permanent = bool(item.get("is_permanent"))
                end_dt = None if is_permanent else CampaignService._parse_dt(item.get("end_date"))

                # Auto-filter out ng mga lumagpas na sa expiration time
                if not is_permanent:
                    if not end_dt or end_dt <= now:
                        continue

                created_dt = CampaignService._parse_dt(item.get("created_at"))

                remaining = None
                if end_dt:
                    remaining = max(0, int((end_dt - now).total_seconds()))

                author = authors.get(str(item.get("created_by") or ""), DEFAULT_AUTHOR)

                # Additive fields - hindi sinasira ang existing consumers
                item["author_name"] = author["name"]
                item["author_role"] = author["role"]
                item["expires_at"] = None if is_permanent else (end_dt.isoformat() if end_dt else None)
                item["remaining_seconds"] = remaining
                item["is_ending_soon"] = bool(
                    remaining is not None and remaining <= ENDING_SOON_WINDOW.total_seconds()
                )
                item["created_at_iso"] = created_dt.isoformat() if created_dt else item.get("created_at")

                active_campaigns.append(item)

            return active_campaigns

        except Exception as e:
            raise Exception(f"Failed to fetch customer campaigns: {str(e)}")