# Collection Report API - Quick Start Guide

## Your Request Now Works! ✓

The API has been fixed to accept your exact request format:

```json
{
  "session_id": "21",
  "class_id": "19",
  "section_id": "36",
  "fee_type_id": "33",
  "collect_by_id": "6",
  "search_type": "all",
  "from_date": "2025-09-01",
  "to_date": "2025-10-11"
}
```

---

## What Was Fixed

### 1. Parameter Names ✓
Your parameter names are now supported:
- ✓ `fee_type_id` (was: `feetype_id`)
- ✓ `collect_by_id` (was: `received_by`)
- ✓ `from_date` (was: `date_from`)
- ✓ `to_date` (was: `date_to`)

### 2. Search Type "all" ✓
- ✓ `search_type: "all"` now works correctly
- ✓ Returns all records within your custom date range

### 3. Date Filtering ✓
- ✓ Custom date ranges now work properly
- ✓ Filters are applied correctly to the database query

---

## Quick Test

### Using cURL:
```bash
curl -X POST "http://localhost/amt/api/collection-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "session_id": "21",
    "class_id": "19",
    "section_id": "36",
    "fee_type_id": "33",
    "collect_by_id": "6",
    "search_type": "all",
    "from_date": "2025-09-01",
    "to_date": "2025-10-11"
  }'
```

### Using PHP Test Script:
```bash
cd c:\xampp\htdocs\amt
c:\xampp\php\php.exe test_user_request.php
```

---

## Expected Response

```json
{
  "status": 1,
  "message": "Collection report retrieved successfully",
  "filters_applied": {
    "search_type": null,
    "date_from": "2025-09-01",
    "date_to": "2025-10-11",
    "feetype_id": "33",
    "received_by": "6",
    "group": null,
    "class_id": "19",
    "section_id": "36",
    "session_id": "21"
  },
  "total_records": 25,
  "data": [
    {
      "id": "123",
      "admission_no": "ADM001",
      "firstname": "John",
      "lastname": "Doe",
      "class": "Class 10",
      "section": "A",
      "type": "Tuition Fee",
      "amount": "1000.00",
      "date": "2025-09-15",
      "payment_mode": "Cash",
      "received_by": "6"
    }
  ],
  "timestamp": "2025-10-11 14:30:00"
}
```

---

## Alternative Ways to Use the API

### 1. Get All Records for Current Month (No Filters)
```json
{}
```

### 2. Use Predefined Date Ranges
```json
{
  "search_type": "this_month",
  "class_id": "19"
}
```

Valid search types:
- `today`
- `this_week`
- `last_week`
- `this_month`
- `last_month`
- `last_3_month`
- `last_6_month`
- `last_12_month`
- `this_year`
- `last_year`
- `period` (requires date_from and date_to)
- `all` (uses custom dates if provided)

### 3. Filter by Specific Criteria
```json
{
  "class_id": "19",
  "section_id": "36",
  "search_type": "this_month"
}
```

### 4. Group Results
```json
{
  "search_type": "this_month",
  "group": "class"
}
```

Group options:
- `class` - Group by class
- `collection` - Group by collector
- `mode` - Group by payment mode

---

## Common Use Cases

### Get Collection for a Specific Class
```json
{
  "class_id": "19",
  "search_type": "this_month"
}
```

### Get Collection by a Specific Staff Member
```json
{
  "collect_by_id": "6",
  "search_type": "this_week"
}
```

### Get Collection for a Specific Fee Type
```json
{
  "fee_type_id": "33",
  "search_type": "this_month"
}
```

### Get Transport Fees
```json
{
  "fee_type_id": "transport_fees",
  "search_type": "this_month"
}
```

### Get Collection for Custom Date Range
```json
{
  "from_date": "2025-09-01",
  "to_date": "2025-10-11"
}
```

---

## Troubleshooting

### Issue: Getting 401 Unauthorized
**Solution:** Check your headers:
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

### Issue: Getting Empty Results
**Possible Causes:**
1. No data exists for the specified filters
2. Date range is outside available data
3. Invalid IDs for class, section, session, etc.

**Solution:** Try with fewer filters first:
```json
{
  "search_type": "this_month"
}
```

### Issue: Getting 500 Error
**Solution:** Check the server logs for detailed error messages

---

## Parameter Reference

| Your Parameter | Alternative Name | Description |
|----------------|------------------|-------------|
| `session_id` | `sch_session_id` | Academic session ID |
| `class_id` | - | Class ID |
| `section_id` | - | Section ID |
| `fee_type_id` | `feetype_id` | Fee type ID |
| `collect_by_id` | `received_by`, `collect_by` | Staff ID who collected |
| `from_date` | `date_from` | Start date (YYYY-MM-DD) |
| `to_date` | `date_to` | End date (YYYY-MM-DD) |
| `search_type` | - | Predefined date range |
| `group` | - | Group by option |

---

## Files Modified

1. **Controller:** `api/application/controllers/Collection_report_api.php`
   - Added support for alternative parameter names
   - Fixed "all" search type handling

2. **Documentation:** `api/documentation/COLLECTION_REPORT_API_README.md`
   - Updated with all parameter alternatives
   - Added new examples

3. **Test Scripts:**
   - `test_user_request.php` - Tests your exact request
   - `test_collection_report_fixed.php` - Comprehensive test suite

---

## Next Steps

1. ✓ **Test the API** with your exact request
2. ✓ **Verify the results** match your expectations
3. ✓ **Update your application** if needed (though your current request format now works!)
4. ✓ **Review the documentation** for additional features

---

## Support

For detailed documentation, see:
- `api/documentation/COLLECTION_REPORT_API_README.md`
- `COLLECTION_REPORT_API_FIXES.md`

For issues or questions:
1. Check the test scripts for examples
2. Review the API documentation
3. Verify your data exists in the database
4. Check server logs for errors

---

## Summary

✓ Your exact request format now works  
✓ All filters are applied correctly  
✓ Alternative parameter names are supported  
✓ "all" search type is handled properly  
✓ Custom date ranges work as expected  
✓ Backward compatible with existing code  

**You're all set! The API is ready to use.** 🎉

