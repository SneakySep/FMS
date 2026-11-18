from pydantic import BaseModel, Field
from typing import Optional

class ShipmentCreateSchema(BaseModel):
    inquiry_id: str = Field(..., description="UUID ng existing inquiry na naging closed_won")
    consignment_note_number: str = Field(..., description="Natatanging tracking / consignment number")
    shipper_reference: Optional[str] = None
    
    # Consignee (Receiver) Details
    consignee_company_name: str
    consignee_contact_person: Optional[str] = Field(None, description="Name ng consignee")
    consignee_address: str
    consignee_city: Optional[str] = None
    consignee_state: Optional[str] = None
    consignee_country: Optional[str] = None
    consignee_zip_code: Optional[str] = None
    consignee_telephone: Optional[str] = None
    
    # Package & Cargo Details
    package_type: Optional[str] = Field("PARCEL", description="DOX o PARCEL")
    parcel_size: Optional[str] = Field(None, description="Small, Medium, Large")
    declared_value: Optional[float] = 0.0
    declared_currency: Optional[str] = "PHP"
    dimensions_cms: Optional[str] = Field(None, description="Sukkat sa cm (Hal. 10x20x30)")
    quantity: Optional[int] = 1
    chargeable_weight: Optional[float] = Field(None, description="Timbang sa Klos o Grams")
    
    # Service & Delivery Options
    transport_mode: Optional[str] = Field("AIR", description="AIR, LAND, o SEA")
    delivery_speed: Optional[str] = Field("NEXT_DAY", description="SAME_DAY, NEXT_DAY, 3_5_DAYS")
    handling_terms: Optional[str] = Field(None, description="DDP, NON_DDP, ADD, ED, CI")
    notes: Optional[str] = None