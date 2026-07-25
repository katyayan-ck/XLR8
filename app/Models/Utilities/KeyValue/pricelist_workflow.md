# **Vehicle Pricing & Quotation Engine – Detailed Workflow**

## Part 1: Layman Terms (For Management / Business Users)

### Objective
We want a system where:
- When a customer shows interest in a vehicle, the salesman can quickly generate an accurate **On-Road Price** with all charges and discounts.
- The system should automatically apply correct prices, offers, RTO, and Insurance based on rules.
- Prices can change over time (new price lists), and the system should remember old prices for records.
- Different types of discounts and add-ons (RSA, Shield, Accessories) should be easy to manage.

### How the System Will Work (Simple Flow)

**1. Vehicle Master Data**
- All vehicles (Bolero, XUV700, Thar, etc.) are already in the system with their basic details.
- We will add extra information like “Can this vehicle be sold as Taxi?”, “What is its Display Name?”, “Is it available for CSD channel?”, etc.

**2. Price List Upload (WEF Concept)**
- Every few weeks/months, the company receives a new price list from Mahindra.
- We will upload this new price list with a **“Valid From” date** (WEF date).
- From that date onwards, the new prices will automatically apply.
- Old prices are saved in history (for audit and old quotations).

**3. Add-ons & Discounts Management**
- RSA, Shield, and Dealer Charges are defined at **Model level** (e.g., all Scorpio-N variants).
- If any specific variant needs a different rate, we can define it at **Variant level** (it will override the model level).
- Same logic applies to Discounts (Cash Discount, Exchange Bonus, Corporate Discount, etc.).

**4. Quotation Creation**
When a salesman creates a quotation:
- He selects the vehicle.
- The system automatically shows:
  - Current Ex-Showroom Price
  - Default RSA (usually 1 year)
  - Default Shield
  - Default Dealer Charges
  - Default Insurance package
  - Applicable RTO
- The salesman can change Insurance company/package, add accessories, apply discounts, etc.
- On every change, the **On-Road Price** updates automatically.

**5. Import Process (Safe & Controlled)**
- We will not directly import everything in one go (to avoid mistakes).
- First, we upload PV + CV sheets → System updates vehicle master.
- Then the system generates an Excel with current data.
- User fills missing/new information and re-uploads.
- This reduces errors significantly.

---

## Part 2: Technical Workflow (For Developers)

### 1. Data Model Strategy

| Module              | Approach                                      | Override Logic          | History Required? |
|---------------------|-----------------------------------------------|-------------------------|-------------------|
| **Pricing**         | `xlr8_vehicle_pricing` + History              | Model → Variant         | Yes               |
| **Addons** (RSA, Shield, Dealer Charges) | `xlr8_vehicle_addons` + History     | Model → Variant         | Yes               |
| **Discounts**       | `xlr8_vehicle_discounts` + History            | Model → Variant         | Yes               |
| **RTO**             | Rule-based table (`xlr8_vehicle_rto_rules`)   | Combination based       | Yes (at quotation level) |
| **Accessories**     | Already exists (`Accessory` + `AccessoryScope`) | Segment → Model → Variant | Already handled |
| **Quotation**       | Snapshot + Calculation log                    | Dynamic                 | Yes               |

### 2. Core Workflow Components

#### A. Pricing with WEF (Most Critical)

**Rules:**
- One vehicle can have multiple pricing records.
- Only one record is **active** at any point in time (`is_active = true` + highest `wef_date` ≤ today).
- History is created **only when there is actual change** in any pricing value.
- Future WEF is allowed but system will warn if another future WEF already exists.

**Technical Flow:**
1. User uploads price list with WEF date.
2. System checks existing active pricing.
3. If new pricing has changes → Create history record of old pricing + update `expired_on`.
4. Activate new pricing record.

#### B. Add-on & Discount Override Logic

**Pattern (Same for RSA, Shield, Dealer Charges, Discounts):**

- First entry → `model_code` only (applies to all variants of that model)
- Later entries → `model_code` + `variant_code` (overrides only that variant)

**Example:**
- Scorpio-N (all variants) → RSA @ ₹1,550
- Scorpio-N AX7 → RSA @ ₹1,698 (this overrides only AX7)

#### C. Quotation Pricing Engine

**Recommended Architecture:**

Create a central service: **`QuotationPricingService`**

**Responsibilities:**
- Load current active pricing
- Apply model-level + variant-level addons/discounts
- Calculate RTO based on rules
- Calculate Insurance (Base + Selected Combo)
- Apply TCS if Invoice Value ≥ 10 Lakh
- Return full breakup + final On-Road Price

**Recalculation Strategy (Performance):**
- Do **not** recalculate everything on every keystroke.
- Use **debounced** API calls from frontend.
- On backend, only recalculate the changed component + dependent fields.
- Store final breakup in `quotation_pricing_snapshot` (JSON) at the time of submission.

#### D. Import Process (Hybrid)

**Recommended Flow:**

1. **Master Sync Phase**
   - Upload PV + CV sheets
   - System creates/updates `vehicle_model` + `vehicle_variant`

2. **Configuration Phase**
   - User provides one **WEF Date**
   - System exports current effective data (Pricing + Addons + Discounts)
   - New/incomplete vehicles appear with blank rows
   - User fills data in Excel or corrects via Grid (< 50 records)
   - Re-upload → System processes with validation

### 3. Key Use Cases to Handle

| Use Case | Description | Technical Handling |
|---------|-------------|-------------------|
| New Price List | New Ex-Showroom prices from Mahindra | WEF + History mechanism |
| Variant-level RSA | One variant has different RSA rate | Override row in `vehicle_addons` |
| Taxi Vehicle | Calculate RTO for both Private & Passenger permit | User selects permit during quotation |
| Old Stock Discount | Higher discount on old VIN vehicles | Old VIN / New VIN flag + Discount rules |
| CSD Channel | Different pricing for CSD vehicles | Use `csd_code` to identify + separate pricing |
| Accessories | User adds multiple accessories in quotation | Use existing `Accessory` + `AccessoryScope` logic |

