import pytest
from unittest.mock import patch, MagicMock
from fastapi import FastAPI
from fastapi.testclient import TestClient

from app.routes.admin.admin import router

app = FastAPI()
app.include_router(router)
client = TestClient(app)

# Helper para i-setup ang mock response ng Supabase table chain
def mock_supabase_table_chain(data=None):
    mock_execute = MagicMock()
    mock_execute.data = data if data is not None else []
    
    chain = MagicMock()
    chain.select.return_value = chain
    chain.eq.return_value = chain
    chain.insert.return_value = chain
    chain.update.return_value = chain
    chain.execute.return_value = mock_execute
    return chain


@patch("app.routes.admin.admin.send_customer_welcome_email")
@patch("app.routes.admin.admin.supabase_secondary")
def test_create_customer_from_ticket_success(mock_supabase, mock_send_email):
    # 1. Setup Table Chain Mocks
    mock_supabase.table.return_value = mock_supabase_table_chain(data=[])

    # 2. Setup Auth Admin Mock
    mock_user = MagicMock()
    mock_user.id = "mock-new-user-uuid-1234"
    mock_auth_res = MagicMock()
    mock_auth_res.user = mock_user
    mock_supabase.auth.admin.create_user.return_value = mock_auth_res

    payload = {
        "ticket_id": "123e4567-e89b-12d3-a456-426614174000",
        "email": "juan@abc.com",
        "password": "CustomerPassword123!",
        "first_name": "Juan",
        "last_name": "Dela Cruz",
        "company_name": "ABC Logistics",
        "phone_number": "09171234567",
    }

    response = client.post("/api/v1/admin/create-customer-from-ticket", json=payload)

    assert response.status_code == 200
    assert response.json()["status"] == "success"
    assert response.json()["user_id"] == "mock-new-user-uuid-1234"


@patch("app.routes.admin.admin.supabase_secondary")
def test_create_customer_from_ticket_auth_failed(mock_supabase):
    # 1. Setup Table Chain Mocks
    mock_supabase.table.return_value = mock_supabase_table_chain(data=[])

    # 2. Setup Auth failure (walang returned user)
    mock_auth_res = MagicMock()
    mock_auth_res.user = None
    mock_supabase.auth.admin.create_user.return_value = mock_auth_res

    payload = {
        "ticket_id": "123e4567-e89b-12d3-a456-426614174000",
        "email": "failed@abc.com",
        "password": "CustomerPassword123!",
        "first_name": "Juan",
        "last_name": "Dela Cruz",
    }

    response = client.post("/api/v1/admin/create-customer-from-ticket", json=payload)

    assert response.status_code == 400
    assert "Failed to create Auth account." in response.json()["detail"]


@patch("app.routes.admin.admin.supabase_secondary")
def test_create_customer_from_ticket_server_error(mock_supabase):
    # 1. Setup Table Chain Mocks
    mock_supabase.table.return_value = mock_supabase_table_chain(data=[])

    # 2. Setup Exception on Auth
    mock_supabase.auth.admin.create_user.side_effect = Exception("Unexpected Supabase Auth Error")

    payload = {
        "ticket_id": "123e4567-e89b-12d3-a456-426614174000",
        "email": "error@abc.com",
        "password": "CustomerPassword123!",
        "first_name": "Juan",
        "last_name": "Dela Cruz",
    }

    response = client.post("/api/v1/admin/create-customer-from-ticket", json=payload)

    assert response.status_code == 400
    assert "Unexpected Supabase Auth Error" in response.json()["detail"]


# -----------------------------------------------------------------------------
# POST /api/v1/admin/create-customer  (Admin "Add New Customer" - B2B / B2C)
# -----------------------------------------------------------------------------

def _customer_payload(**overrides):
    payload = {
        "account_type": "individual",
        "first_name": "Juan",
        "last_name": "Dela Cruz",
        "email": "juan.indiv@abc.com",
        "phone_number": "09171234567",
        "password": "CustomerPassword123!",
    }
    payload.update(overrides)
    return payload


@patch("app.routes.admin.admin.send_customer_welcome_email")
@patch("app.routes.admin.admin.supabase_secondary")
def test_create_customer_b2c_success(mock_supabase, mock_send_email):
    mock_supabase.table.return_value = mock_supabase_table_chain(data=[])

    mock_user = MagicMock()
    mock_user.id = "mock-b2c-user-uuid"
    mock_auth_res = MagicMock()
    mock_auth_res.user = mock_user
    mock_supabase.auth.admin.create_user.return_value = mock_auth_res

    response = client.post("/api/v1/admin/create-customer", json=_customer_payload())

    assert response.status_code == 201
    body = response.json()
    assert body["status"] == "success"
    assert body["user_id"] == "mock-b2c-user-uuid"
    assert body["customer_type"] == "individual"

    # Ang customers insert ay dapat may customer_type at total_bookings = 0
    inserts = [c.args[0] for c in mock_supabase.table.return_value.insert.call_args_list]
    cust_row = next(i for i in inserts if "total_bookings" in i)
    assert cust_row["customer_type"] == "individual"
    assert cust_row["total_bookings"] == 0
    assert cust_row["tier"] == "BRONZE"
    assert cust_row["company_name"] is None


@patch("app.routes.admin.admin.supabase_secondary")
def test_create_customer_b2b_requires_company_name(mock_supabase):
    mock_supabase.table.return_value = mock_supabase_table_chain(data=[])

    response = client.post(
        "/api/v1/admin/create-customer",
        json=_customer_payload(account_type="business", email="acme@abc.com"),
    )

    assert response.status_code == 422
    assert "Company name is required" in str(response.json()["detail"])


@patch("app.routes.admin.admin.supabase_secondary")
def test_create_customer_duplicate_email_conflict(mock_supabase):
    # public.users lookup returns an existing row -> dapat 409
    mock_supabase.table.return_value = mock_supabase_table_chain(
        data=[{"id": "already-exists-uuid"}]
    )

    response = client.post(
        "/api/v1/admin/create-customer",
        json=_customer_payload(email="taken@abc.com"),
    )

    assert response.status_code == 409
    assert "already exists" in response.json()["detail"]
    # Dapat hindi na nag Attempt ng auth account creation
    mock_supabase.auth.admin.create_user.assert_not_called()


@patch("app.routes.admin.admin.send_customer_welcome_email")
@patch("app.routes.admin.admin.supabase_secondary")
def test_create_customer_degrades_without_customer_type_column(mock_supabase, mock_send_email):
    """Dapat pa ring gumagana ang create kapag wala pa ang customer_type column."""
    calls = []

    class FakeRequest:
        def __init__(self, row):
            self._row = row

        def execute(self):
            if "customer_type" in self._row:
                raise Exception(
                    'Could not find the customer_type column of the table in the '
                    'schema cache. Try restarting PostgREST.'
                )
            calls.append(self._row)
            res = MagicMock()
            res.data = [self._row]
            return res

    class FakeChain:
        def select(self, *a, **k):
            return self

        def eq(self, *a, **k):
            return self

        def execute(self):
            # select lookup (duplicate-email check) -> always empty
            res = MagicMock()
            res.data = []
            return res

        def insert(self, row):
            return FakeRequest(row)

    mock_supabase.table.return_value = FakeChain()
    mock_user = MagicMock()
    mock_user.id = "mock-nocol-user-uuid"
    mock_auth_res = MagicMock()
    mock_auth_res.user = mock_user
    mock_supabase.auth.admin.create_user.return_value = mock_auth_res

    response = client.post(
        "/api/v1/admin/create-customer",
        json=_customer_payload(email="nocol@abc.com", address="BGC, Taguig"),
    )

    assert response.status_code == 201
    # Walang customer_type sa final insert rows
    assert all("customer_type" not in row for row in calls), calls
    assert calls  # may naganap na inserts


# -----------------------------------------------------------------------------
# Customer Management ACTIONS workflow
#   PATCH /api/v1/admin/customer-accounts/status
#   POST  /api/v1/admin/delete-customer
# -----------------------------------------------------------------------------

STATUS_UID = "11111111-aaaa-bbbb-cccc-222222222222"


class _FakeChain:
    """Isang table chain na nire-record ang bawat operation sa owner.ops."""

    def __init__(self, owner, table_name):
        self._owner = owner
        self._table = table_name

    def _push(self, op, payload=None):
        self._owner.ops.append(
            {"op": op, "table": self._table, "payload": payload, "filter": None}
        )
        return self

    def select(self, *_a, **_k):
        return self._push("select")

    def insert(self, row):
        return self._push("insert", row)

    def update(self, row):
        return self._push("update", row)

    def delete(self):
        return self._push("delete")

    def eq(self, col, val):
        self._owner.ops[-1]["filter"] = (col, val)
        return self

    def execute(self):
        rec = self._owner.ops[-1]
        if rec["op"] == "update" and self._table in self._owner.missing_status_tables:
            raise Exception(
                "Could not find the status column of the table in the schema cache. "
                "Try restarting PostgREST."
            )
        res = MagicMock()
        res.data = list(self._owner.rows_by_table.get(self._table, [])) if rec["op"] == "select" else []
        return res


class _FakeSecondary:
    """Minimal na Supabase client na nagre-record ng table ops para sa assertions."""

    def __init__(self, rows_by_table=None, missing_status_tables=()):
        self.ops = []
        self.rows_by_table = rows_by_table or {}
        self.missing_status_tables = set(missing_status_tables)
        self.auth = MagicMock()

    def table(self, name):
        return _FakeChain(self, name)


def _wire(mock_supabase, fake):
    """Ilagay ang _FakeSecondary sa likod ng patched supabase_secondary MagicMock."""
    mock_supabase.table.side_effect = fake.table
    mock_supabase.auth = fake.auth
    return fake


def _ops_of(fake, op=None, table=None):
    return [
        o for o in fake.ops
        if (op is None or o["op"] == op) and (table is None or o["table"] == table)
    ]


def _user_row(**overrides):
    row = {
        "id": STATUS_UID,
        "email": "Juan@Abc.com",
        "first_name": "Juan",
        "last_name": "Dela Cruz",
        "company_name": "ABC Logistics",
        "status": "Active",
    }
    row.update(overrides)
    return row


@patch("app.routes.admin.admin.supabase_secondary")
def test_update_customer_status_active_updates_both_tables(mock_supabase):
    fake = _wire(mock_supabase, _FakeSecondary({"users": [_user_row()]}))

    response = client.patch(
        "/api/v1/admin/customer-accounts/status",
        json={"user_id": STATUS_UID, "status": "Active"},
    )

    assert response.status_code == 200
    body = response.json()
    assert body["status"] == "success"
    assert body["new_status"] == "Active"
    assert body["updated"] == {"users": True, "customers": True}
    assert body["email"] == "juan@abc.com"

    updates = _ops_of(fake, "update")
    assert [u["table"] for u in updates] == ["users", "customers"]
    assert all(u["payload"] == {"status": "Active"} for u in updates)
    # users is keyed by id, customers by email (both lower-cased)
    assert updates[0]["filter"] == ("id", STATUS_UID)
    assert updates[1]["filter"] == ("email", "juan@abc.com")
    # Active must not leave the auth user banned
    fake.auth.admin.update_user_by_id.assert_called_once_with(
        STATUS_UID, {"ban_duration": "none"}
    )


@patch("app.routes.admin.admin.supabase_secondary")
def test_update_customer_status_inactive_does_not_ban_auth(mock_supabase):
    fake = _wire(mock_supabase, _FakeSecondary({"users": [_user_row()]}))

    response = client.patch(
        "/api/v1/admin/customer-accounts/status",
        json={"user_id": STATUS_UID, "status": "Inactive"},
    )

    assert response.status_code == 200
    assert response.json()["auth_ban"] == "ok"
    fake.auth.admin.update_user_by_id.assert_called_once_with(
        STATUS_UID, {"ban_duration": "none"}
    )
    assert _ops_of(fake, "update")[0]["payload"] == {"status": "Inactive"}


@patch("app.routes.admin.admin.supabase_secondary")
def test_update_customer_status_deactivated_bans_auth(mock_supabase):
    fake = _wire(mock_supabase, _FakeSecondary({"users": [_user_row(status="Active")]}))

    response = client.patch(
        "/api/v1/admin/customer-accounts/status",
        json={"user_id": STATUS_UID, "status": "Deactivated"},
    )

    assert response.status_code == 200
    assert response.json()["new_status"] == "Deactivated"
    # Deactivated = permanent lock-out of the portal login
    fake.auth.admin.update_user_by_id.assert_called_once_with(
        STATUS_UID, {"ban_duration": "permanent"}
    )


@patch("app.routes.admin.admin.supabase_secondary")
def test_update_customer_status_rejects_unknown_value(mock_supabase):
    _wire(mock_supabase, _FakeSecondary({"users": [_user_row()]}))

    response = client.patch(
        "/api/v1/admin/customer-accounts/status",
        json={"user_id": STATUS_UID, "status": "Terminated"},
    )

    assert response.status_code == 422


@patch("app.routes.admin.admin.supabase_secondary")
def test_update_customer_status_unknown_user_is_404(mock_supabase):
    _wire(mock_supabase, _FakeSecondary({"users": []}))

    response = client.patch(
        "/api/v1/admin/customer-accounts/status",
        json={"user_id": "does-not-exist", "status": "Inactive"},
    )

    assert response.status_code == 404
    assert "not found" in response.json()["detail"].lower()


@patch("app.routes.admin.admin.supabase_secondary")
def test_update_customer_status_degrades_without_status_column(mock_supabase):
    """Dapat pa ring gumagana ang status change kapag wala pa ang status column."""
    fake = _wire(
        mock_supabase,
        _FakeSecondary({"users": [_user_row()]}, missing_status_tables={"users", "customers"}),
    )

    response = client.patch(
        "/api/v1/admin/customer-accounts/status",
        json={"user_id": STATUS_UID, "status": "Deactivated"},
    )

    assert response.status_code == 200
    body = response.json()
    # Na-attempt ang update pero nag-degrade nang marahan
    assert body["updated"] == {"users": False, "customers": False}
    assert body["new_status"] == "Deactivated"
    # Ang auth ban ay dapat tumatakbo pa rin
    fake.auth.admin.update_user_by_id.assert_called_once_with(
        STATUS_UID, {"ban_duration": "permanent"}
    )

@patch("app.routes.admin.admin.supabase_secondary")
def test_delete_customer_blocked_until_deactivated(mock_supabase):
    fake = _wire(mock_supabase, _FakeSecondary({"users": [_user_row(status="Active")]}))

    response = client.post(
        "/api/v1/admin/delete-customer",
        json={
            "user_id": STATUS_UID,
            "privacy_ack": True,
            "deletion_reason": "Customer requested erasure under RA 10173.",
        },
    )

    assert response.status_code == 409
    assert "Deactivate the account first" in response.json()["detail"]
    # WALANG dapat matanggal na row
    assert _ops_of(fake, "delete") == []
    fake.auth.admin.delete_user.assert_not_called()


@patch("app.routes.admin.admin.supabase_secondary")
def test_delete_customer_blocked_when_status_column_missing(mock_supabase):
    """Fail-safe: kung hindi mabasa ang status, bawal ang permanent delete."""
    fake = _wire(mock_supabase, _FakeSecondary({"users": [_user_row(status=None)]}))

    response = client.post(
        "/api/v1/admin/delete-customer",
        json={
            "user_id": STATUS_UID,
            "privacy_ack": True,
            "deletion_reason": "Customer requested erasure under RA 10173.",
        },
    )

    assert response.status_code == 409
    assert "customer_status.sql" in response.json()["detail"]
    assert _ops_of(fake, "delete") == []


@patch("app.routes.admin.admin.supabase_secondary")
def test_delete_customer_requires_privacy_ack(mock_supabase):
    fake = _wire(mock_supabase, _FakeSecondary({"users": [_user_row(status="Deactivated")]}))

    response = client.post(
        "/api/v1/admin/delete-customer",
        json={
            "user_id": STATUS_UID,
            "privacy_ack": False,
            "deletion_reason": "Customer requested erasure under RA 10173.",
        },
    )

    assert response.status_code == 422
    assert "Privacy Policy" in str(response.json()["detail"])
    assert _ops_of(fake, "delete") == []


@patch("app.routes.admin.admin.supabase_secondary")
def test_delete_customer_requires_meaningful_reason(mock_supabase):
    _wire(mock_supabase, _FakeSecondary({"users": [_user_row(status="Deactivated")]}))

    response = client.post(
        "/api/v1/admin/delete-customer",
        json={"user_id": STATUS_UID, "privacy_ack": True, "deletion_reason": "x"},
    )

    assert response.status_code == 422
    assert "deletion_reason" in str(response.json()["detail"])


@patch("app.routes.admin.admin.supabase_secondary")
def test_delete_customer_success_removes_all_records(mock_supabase):
    fake = _wire(
        mock_supabase,
        _FakeSecondary({
            "users": [_user_row(status="Deactivated")],
            "customers": [{"id": "cust-uuid", "email": "juan@abc.com"}],
        }),
    )

    response = client.post(
        "/api/v1/admin/delete-customer",
        json={
            "user_id": STATUS_UID,
            "privacy_ack": True,
            "deletion_reason": "Customer requested erasure under RA 10173.",
        },
    )

    assert response.status_code == 200
    body = response.json()
    assert body["status"] == "success"
    assert body["deleted"]["users_row"] is True
    assert body["deleted"]["customers_row"] is True
    assert body["deleted"]["auth_user"] is True

    # FK-safe order: settings -> customers -> users
    deletes = [(d["table"], d["filter"]) for d in _ops_of(fake, "delete")]
    assert [d[0] for d in deletes] == ["customer_settings", "customers", "users"]
    assert deletes[0][1] == ("customer_id", STATUS_UID)
    assert deletes[1][1] == ("email", "juan@abc.com")
    assert deletes[2][1] == ("id", STATUS_UID)

    fake.auth.admin.delete_user.assert_called_once_with(STATUS_UID)


@patch("app.routes.admin.admin.supabase_secondary")
def test_delete_customer_unknown_user_is_404(mock_supabase):
    _wire(mock_supabase, _FakeSecondary({"users": []}))

    response = client.post(
        "/api/v1/admin/delete-customer",
        json={
            "user_id": "ghost-uuid",
            "privacy_ack": True,
            "deletion_reason": "Customer requested erasure under RA 10173.",
        },
    )

    assert response.status_code == 404

