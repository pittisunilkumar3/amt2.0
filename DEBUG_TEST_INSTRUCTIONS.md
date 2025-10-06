# Fee Payment Debug Test Instructions

## Test the Zero Amount Issue Fix

### Step 1: Make a Test Payment
1. Go to the fee collection page in your application
2. Select a student who has advance payment balance
3. Try to collect a fee (any small amount like 1.00)
4. Complete the payment

### Step 2: Check Debug Logs
After making the payment, check these log files:

**Primary Debug Log:**
```
c:\xampp\htdocs\amt\application\logs\fee_debug.log
```

**Alternative Log Locations:**
```
c:\xampp\htdocs\amt\application\logs\log-{date}.php
c:\xampp\htdocs\amt\error_log
c:\xampp\php\logs\php_error_log
```

### Step 3: Check Database Results
Run this script to see the latest payment:
```
c:\xampp\htdocs\amt\debug_receipt_54.php
```

### What to Look For:

**In Debug Logs:**
- "Raw input amount"
- "Final amount after conversion"
- "Available advance balance"
- "Advance to apply"
- "Remaining amount after advance"
- "JSON array amount set to"

**In Database:**
- Check if `amount` field is still 0
- Check if `advance_applied` shows the correct value
- Check if `original_amount` shows the correct value

### Expected Results:
- If advance balance = 1 and input amount = "1.00"
- After currency conversion, amount should be much larger (e.g., 1000)
- Advance applied should be 1 (from advance balance)
- Remaining amount should be large positive number (e.g., 999)
- Database should store positive amount, not zero

### If Still Getting Zero:
1. Check the debug logs to see exact values
2. Verify currency conversion is working properly
3. Check if advance balance format matches expected format

## Quick Database Check Command:
```sql
SELECT id, amount_detail, created_at 
FROM student_fees_deposite 
WHERE created_at >= CURDATE() 
ORDER BY id DESC LIMIT 3;
```
