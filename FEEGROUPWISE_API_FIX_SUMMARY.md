# Fee Group-wise Collection Report API - Bug Fix Summary

## Date: 2024-10-09

## Issues Reported

### Issue 1: Incorrect Calculation - Negative Values
**Problem**: The report was displaying negative values in the collection amounts.

**Root Cause**: 
The model was calculating `total_amount` by summing `fee_groups_feetype.amount` (base fee type amounts) instead of `student_fees_master.amount` (actual assigned amounts per student).

In the original code:
```sql
SELECT
    fg.id as fee_group_id,
    fg.name as fee_group_name,
    SUM(fgf.amount) as total_amount,  -- ❌ WRONG: This sums fee type base amounts
    COUNT(DISTINCT sfm.student_session_id) as total_students
FROM fee_groups fg
INNER JOIN fee_groups_feetype fgf ON fgf.fee_groups_id = fg.id
```

This caused incorrect calculations because:
- `fee_groups_feetype.amount` is the base amount for a fee type (e.g., Tuition Fee = 5000)
- `student_fees_master.amount` is the actual amount assigned to each student (which may vary)
- When these were mismatched, it could result in negative balance amounts

**Fix Applied**:
Updated SQL queries in `application/models/Feegroupwise_model.php`:
- Changed `SUM(fgf.amount)` to `SUM(sfm.amount)` for regular fees
- Changed `SUM(fgfa.amount)` to `SUM(sfma.amount)` for additional fees
- Added `COALESCE()` to handle NULL values properly
- Improved JOIN conditions to ensure correct data relationships

### Issue 2: Missing Data - "Detailed Fee Collection Records" Not Displaying
**Problem**: The "Detailed Fee Collection Records" section was not appearing in the API response.

**Root Cause**: 
The API endpoint `http://localhost/amt/financereports/feegroupwise_collection` didn't exist as an API endpoint. It was only available as a web view endpoint in the main application.

**Fix Applied**:
Created a new API controller following the school management system API pattern:
- File: `api/application/controllers/Feegroupwise_collection_report_api.php`
- Endpoint: `/api/feegroupwise-collection-report/filter`
- Returns both summary data (`grid_data`) and detailed student-level records (`detailed_data`)

---

## Files Modified

### 1. application/models/Feegroupwise_model.php
**Changes Made**:
- **Line 52-120**: Fixed `getRegularFeesCollection()` method
  - Changed from `SUM(fgf.amount)` to `COALESCE(SUM(sfm.amount), 0)`
  - Improved JOIN logic to use `student_fees_master.amount`
  - Added proper NULL handling with COALESCE
  - Fixed WHERE conditions for class/section filtering

- **Line 122-189**: Fixed `getAdditionalFeesCollection()` method
  - Changed from `SUM(fgfa.amount)` to `COALESCE(SUM(sfma.amount), 0)`
  - Improved JOIN logic to use `student_fees_masteradding.amount`
  - Added proper NULL handling with COALESCE
  - Fixed WHERE conditions for class/section filtering

**Impact**: 
- ✅ Eliminates negative values in calculations
- ✅ Ensures accurate total amounts based on actual student assignments
- ✅ Properly handles cases with no student assignments

---

## Files Created

### 1. api/application/controllers/Feegroupwise_collection_report_api.php
**Purpose**: New API controller for fee group-wise collection reports

**Endpoints**:
1. **POST** `/api/feegroupwise-collection-report/filter`
   - Returns fee group-wise collection data with filters
   - Includes both summary (`grid_data`) and detailed records (`detailed_data`)
   - Supports filtering by session, class, section, fee groups, and date range
   - Handles empty parameters gracefully (returns all records)

2. **POST** `/api/feegroupwise-collection-report/list`
   - Returns filter options (fee groups, classes, sessions)
   - Used to populate filter dropdowns in client applications

**Features**:
- ✅ Proper authentication using `check_auth_client()`
- ✅ Follows school management system API pattern
- ✅ Returns formatted JSON responses
- ✅ Includes comprehensive error handling
- ✅ Formats all amounts to 2 decimal places
- ✅ Calculates summary statistics
- ✅ Returns both grid data and detailed student records

### 2. api/documentation/FEEGROUPWISE_COLLECTION_REPORT_API.md
**Purpose**: Complete API documentation

**Contents**:
- API overview and base URL
- Authentication requirements
- Detailed endpoint documentation
- Request/response examples
- Field descriptions
- Usage examples with cURL
- Bug fix explanations

### 3. test_feegroupwise_api.php
**Purpose**: Test script to verify the fixes

**Features**:
- Tests all API endpoints
- Validates no negative values exist
- Confirms detailed records are present
- Checks calculation accuracy
- Provides detailed test results

---

## Testing Instructions

### Step 1: Run the Test Script
```bash
php test_feegroupwise_api.php
```

This will:
- Test the filter endpoint with various parameters
- Validate that no negative values appear
- Confirm detailed records are returned
- Check calculation accuracy

### Step 2: Manual API Testing with cURL

**Test 1: Get All Records (Empty Filter)**
```bash
curl -X POST http://localhost/amt/api/feegroupwise-collection-report/filter \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -H "Content-Type: application/json" \
  -d '{}'
```

**Test 2: Filter by Date Range**
```bash
curl -X POST http://localhost/amt/api/feegroupwise-collection-report/filter \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -H "Content-Type: application/json" \
  -d '{
    "from_date": "2024-01-01",
    "to_date": "2024-12-31"
  }'
```

**Test 3: Get Filter Options**
```bash
curl -X POST http://localhost/amt/api/feegroupwise-collection-report/list \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -H "Content-Type: application/json" \
  -d '{}'
```

### Step 3: Verify Results

**Check for Issue 1 (Negative Values)**:
- Look at `grid_data` array in the response
- Verify all `total_amount`, `amount_collected`, and `balance_amount` are positive
- Check `summary` object for positive values
- Verify `balance_amount = total_amount - amount_collected`

**Check for Issue 2 (Detailed Records)**:
- Look for `detailed_data` array in the response
- Verify it contains student-level records
- Each record should have:
  - Student information (name, admission_no, etc.)
  - Fee group information
  - Amount details (total, collected, balance)
  - Payment status (Paid/Partial/Pending)

---

## Expected Response Structure

```json
{
  "status": 1,
  "message": "Fee group-wise collection report retrieved successfully",
  "filters_applied": { ... },
  "summary": {
    "total_fee_groups": 5,
    "total_amount": "500000.00",      // ✅ Should be positive
    "total_collected": "350000.00",   // ✅ Should be positive
    "total_balance": "150000.00",     // ✅ Should be positive
    "collection_percentage": 70.00
  },
  "grid_data": [
    {
      "fee_group_id": "1",
      "fee_group_name": "Tuition Fee",
      "total_amount": "200000.00",      // ✅ Should be positive
      "amount_collected": "150000.00",  // ✅ Should be positive
      "balance_amount": "50000.00",     // ✅ Should be positive
      "total_students": 50,
      "collection_percentage": 75.00
    }
  ],
  "detailed_data": [                    // ✅ Should be present
    {
      "student_id": "123",
      "admission_no": "STU001",
      "student_name": "John Doe",
      "father_name": "Robert Doe",
      "class_name": "Class 1",
      "section_name": "A",
      "fee_group_name": "Tuition Fee",
      "total_amount": "5000.00",        // ✅ Should be positive
      "amount_collected": "3000.00",    // ✅ Should be positive
      "balance_amount": "2000.00",      // ✅ Should be positive
      "collection_percentage": 60.00,
      "payment_status": "Partial"
    }
  ],
  "total_fee_groups": 5,
  "total_detailed_records": 150,
  "timestamp": "2024-10-09 12:00:00"
}
```

---

## Validation Checklist

- [ ] No negative values in `total_amount` fields
- [ ] No negative values in `amount_collected` fields
- [ ] No negative values in `balance_amount` fields
- [ ] `balance_amount = total_amount - amount_collected` for all records
- [ ] `detailed_data` array is present in response
- [ ] `detailed_data` contains student-level records
- [ ] All amounts are formatted to 2 decimal places
- [ ] Summary calculations are accurate
- [ ] API returns 200 status code for valid requests
- [ ] API returns proper error messages for invalid requests

---

## Additional Notes

1. **Empty Parameter Handling**: The API gracefully handles empty or missing parameters by returning all available records, following the pattern of treating empty filters the same as list endpoints.

2. **Date Filtering**: When `from_date` and `to_date` are provided, the API filters payment records based on the payment date stored in the `amount_detail` JSON field.

3. **Fee Types**: The API handles both regular fees (from `fee_groups` table) and additional fees (from `fee_groupsadding` table), combining them in the response.

4. **Payment Status Logic**:
   - `Paid`: balance_amount = 0 and amount_collected > 0
   - `Partial`: balance_amount > 0 and amount_collected > 0
   - `Pending`: amount_collected = 0

---

## Support

For any issues or questions, refer to:
- API Documentation: `api/documentation/FEEGROUPWISE_COLLECTION_REPORT_API.md`
- Test Script: `test_feegroupwise_api.php`
- Model File: `application/models/Feegroupwise_model.php`
- Controller File: `api/application/controllers/Feegroupwise_collection_report_api.php`

