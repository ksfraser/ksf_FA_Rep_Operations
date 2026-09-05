# Functional Requirements - ksf_FA_Rep_Operations

## FR-OPS-001-001: Inventory History Report
**BABOK Related**: BR-OPS-001

Tracks inventory movements and adjustments over time.

### Parameters
- Location
- Category
- Date From
- Date To
- Comments

### Output
PDF/Excel report with inventory transactions by location/category.

---

## FR-OPS-001-002: Pick List Report
**BABOK Related**: BR-OPS-001

Generates pick lists for order fulfillment.

### Parameters
- Location
- Customer
- Item Selection
- Comments

### Output
PDF/Excel pick list organized by location.

---

## FR-OPS-001-003: Price Changes Since Report
**BABOK Related**: BR-OPS-001

Shows items with price changes since a specified date.

### Parameters
- Date
- Category
- Location
- Comments

### Output
PDF/Excel report of price changes with old/new prices.

---

## FR-OPS-001-004: Bank Daily Sweep Report
**BABOK Related**: BR-OPS-001

Shows daily bank account activity and sweep transactions.

### Parameters
- Date
- Bank Account
- Comments

### Output
PDF/Excel report with daily bank activity.

---

## FR-OPS-001-005: Tax Details (Cash Basis) Report
**BABOK Related**: BR-OPS-001

Tax report using cash basis accounting.

### Parameters
- Date From
- Date To
- Tax Type
- Summary Only
- Comments

### Output
PDF/Excel tax details report with optional summary view.
