# Payroll Report API Documentation

## Overview
The Payroll Report API provides endpoints to retrieve staff payroll information with flexible filtering options. This API allows you to fetch payroll data by month, year, role, or date range.

## Base URL
```
http://localhost/amt/api
```

## Authentication
All API requests require authentication headers:

| Header | Value | Required |
|--------|-------|----------|
| `Client-Service` | `smartschool` | Yes |
| `Auth-Key` | `schoolAdmin@` | Yes |
| `Content-Type` | `application/json` | Yes |

## Endpoints

### 1. Filter Payroll Report
Retrieve payroll report data with optional filters.

**Endpoint:** `POST /api/payroll-report/filter`

**Request Body Parameters (All Optional):**

| Parameter | Type | Description | Example |
|-----------|------|-------------|---------|
| `month` | string | Month name | `"January"` |
| `year` | integer | Year | `2025` |
| `role` | string | Staff role name | `"Teacher"` |
| `from_date` | string | Start date (YYYY-MM-DD) | `"2025-01-01"` |
| `to_date` | string | End date (YYYY-MM-DD) | `"2025-12-31"` |

**Important:** 
- Empty request body `{}` returns **ALL payroll data** for the current year
- All parameters are optional
- Date range (`from_date` and `to_date`) takes precedence over month/year filters

**Response Format:**
```json
{
    "status": 1,
    "message": "Payroll report retrieved successfully",
    "filters_applied": {
        "month": "January",
        "year": 2025,
        "role": "Teacher",
        "from_date": null,
        "to_date": null
    },
    "total_records": 45,
    "data": [
        {
            "id": "123",
            "staff_id": "45",
            "employee_id": "EMP001",
            "name": "John",
            "surname": "Doe",
            "user_type": "Teacher",
            "designation": "Senior Teacher",
            "department": "Mathematics",
            "month": "01",
            "year": "2025",
            "basic": "50000.00",
            "total_allowance": "10000.00",
            "total_deduction": "5000.00",
            "net_salary": "55000.00",
            "tax": "2000.00",
            "gross_salary": "60000.00",
            "payment_mode": "Bank Transfer",
            "payment_date": "2025-01-31",
            "status": "paid",
            "remark": "Regular payment"
        }
    ],
    "timestamp": "2025-10-07 22:30:00"
}
```

**Response Fields:**

| Field | Type | Description |
|-------|------|-------------|
| `status` | integer | 1 for success, 0 for error |
| `message` | string | Response message |
| `filters_applied` | object | Applied filter parameters |
| `total_records` | integer | Number of records returned |
| `data` | array | Array of payroll records |
| `timestamp` | string | Response timestamp |

**Payroll Record Fields:**

| Field | Description |
|-------|-------------|
| `id` | Payslip ID |
| `staff_id` | Staff member ID |
| `employee_id` | Employee ID |
| `name` | Staff first name |
| `surname` | Staff last name |
| `user_type` | Staff role (Teacher, Admin, etc.) |
| `designation` | Job designation |
| `department` | Department name |
| `month` | Payment month (01-12) |
| `year` | Payment year |
| `basic` | Basic salary |
| `total_allowance` | Total allowances |
| `total_deduction` | Total deductions |
| `net_salary` | Net salary (after deductions) |
| `tax` | Tax amount |
| `gross_salary` | Gross salary (before deductions) |
| `payment_mode` | Payment method |
| `payment_date` | Date of payment |
| `status` | Payment status (paid, generated, etc.) |
| `remark` | Additional remarks |

### 2. List Payroll Filter Options
Retrieve available filter options (years, roles, months).

**Endpoint:** `POST /api/payroll-report/list`

**Request Body:** Empty `{}`

**Response Format:**
```json
{
    "status": 1,
    "message": "Payroll filter options retrieved successfully",
    "total_years": 3,
    "years": [
        {"year": "2025"},
        {"year": "2024"},
        {"year": "2023"}
    ],
    "total_roles": 5,
    "roles": [
        {"id": "1", "name": "Teacher"},
        {"id": "2", "name": "Admin"},
        {"id": "3", "name": "Accountant"}
    ],
    "months": [
        "January", "February", "March", "April", "May", "June",
        "July", "August", "September", "October", "November", "December"
    ],
    "note": "Use the filter endpoint with month, year, role, or date range to get payroll report",
    "timestamp": "2025-10-07 22:30:00"
}
```

## Usage Examples

### Example 1: Get All Payroll Data (Empty Request)
```bash
curl -X POST "http://localhost/amt/api/payroll-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

**Returns:** All payroll records for the current year

### Example 2: Filter by Month and Year
```bash
curl -X POST "http://localhost/amt/api/payroll-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "month": "January",
    "year": 2025
  }'
```

### Example 3: Filter by Role
```bash
curl -X POST "http://localhost/amt/api/payroll-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "role": "Teacher",
    "year": 2025
  }'
```

### Example 4: Filter by Date Range
```bash
curl -X POST "http://localhost/amt/api/payroll-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "from_date": "2025-01-01",
    "to_date": "2025-03-31"
  }'
```

### Example 5: Get Filter Options
```bash
curl -X POST "http://localhost/amt/api/payroll-report/list" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

### Example 6: Filter by Year Only
```bash
curl -X POST "http://localhost/amt/api/payroll-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "year": 2024
  }'
```

## Code Examples

### JavaScript (Fetch API)
```javascript
// Get all payroll data
async function getAllPayrollData() {
    const response = await fetch('http://localhost/amt/api/payroll-report/filter', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Client-Service': 'smartschool',
            'Auth-Key': 'schoolAdmin@'
        },
        body: JSON.stringify({})
    });
    
    const data = await response.json();
    console.log('Payroll Data:', data);
    return data;
}

// Filter by month and year
async function getPayrollByMonth(month, year) {
    const response = await fetch('http://localhost/amt/api/payroll-report/filter', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Client-Service': 'smartschool',
            'Auth-Key': 'schoolAdmin@'
        },
        body: JSON.stringify({
            month: month,
            year: year
        })
    });
    
    const data = await response.json();
    return data;
}

// Usage
getAllPayrollData();
getPayrollByMonth('January', 2025);
```

### PHP (cURL)
```php
<?php
// Get all payroll data
function getAllPayrollData() {
    $url = 'http://localhost/amt/api/payroll-report/filter';
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Client-Service: smartschool',
        'Auth-Key: schoolAdmin@'
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([]));
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    return json_decode($response, true);
}

// Filter by role and year
function getPayrollByRole($role, $year) {
    $url = 'http://localhost/amt/api/payroll-report/filter';
    
    $data = [
        'role' => $role,
        'year' => $year
    ];
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Client-Service: smartschool',
        'Auth-Key: schoolAdmin@'
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    return json_decode($response, true);
}

// Usage
$allData = getAllPayrollData();
$teacherData = getPayrollByRole('Teacher', 2025);
?>
```

### Python (Requests)
```python
import requests
import json

# API configuration
BASE_URL = 'http://localhost/amt/api'
HEADERS = {
    'Content-Type': 'application/json',
    'Client-Service': 'smartschool',
    'Auth-Key': 'schoolAdmin@'
}

# Get all payroll data
def get_all_payroll_data():
    url = f'{BASE_URL}/payroll-report/filter'
    response = requests.post(url, headers=HEADERS, json={})
    return response.json()

# Filter by month and year
def get_payroll_by_month(month, year):
    url = f'{BASE_URL}/payroll-report/filter'
    data = {
        'month': month,
        'year': year
    }
    response = requests.post(url, headers=HEADERS, json=data)
    return response.json()

# Filter by date range
def get_payroll_by_date_range(from_date, to_date):
    url = f'{BASE_URL}/payroll-report/filter'
    data = {
        'from_date': from_date,
        'to_date': to_date
    }
    response = requests.post(url, headers=HEADERS, json=data)
    return response.json()

# Get filter options
def get_filter_options():
    url = f'{BASE_URL}/payroll-report/list'
    response = requests.post(url, headers=HEADERS, json={})
    return response.json()

# Usage
if __name__ == '__main__':
    # Get all data
    all_data = get_all_payroll_data()
    print(f"Total records: {all_data['total_records']}")

    # Filter by month
    january_data = get_payroll_by_month('January', 2025)
    print(f"January records: {january_data['total_records']}")

    # Get filter options
    options = get_filter_options()
    print(f"Available years: {options['years']}")
```

## Error Handling

### Error Response Format
```json
{
    "status": 0,
    "message": "Error description",
    "error": "Detailed error message"
}
```

### Common Error Codes

| HTTP Status | Description |
|-------------|-------------|
| 400 | Bad Request - Invalid request method |
| 401 | Unauthorized - Invalid or missing authentication |
| 500 | Internal Server Error - Server-side error |

### Error Examples

**Unauthorized Access:**
```json
{
    "status": 0,
    "message": "Unauthorized access"
}
```

**Bad Request:**
```json
{
    "status": 0,
    "message": "Bad request. Only POST method allowed."
}
```

## Notes

1. **Empty Request Behavior:** Sending an empty request body `{}` to the filter endpoint returns all payroll data for the current year, not an error.

2. **Date Range Priority:** If both date range (`from_date`, `to_date`) and month/year filters are provided, the date range takes precedence.

3. **Default Year:** If no year is specified, the current year is used by default.

4. **Role Filter:** The role parameter accepts the role name (e.g., "Teacher", "Admin") as a string.

5. **Month Format:** Month names should be provided in full English format (e.g., "January", "February").

6. **Payment Status:** The API returns only paid payroll records by default.

7. **Data Consistency:** All monetary values are returned as strings with decimal precision.

## Best Practices

1. **Always include authentication headers** in every request
2. **Use date range filters** for custom reporting periods
3. **Cache filter options** from the list endpoint to avoid repeated calls
4. **Handle empty data arrays** gracefully in your application
5. **Validate date formats** before sending requests
6. **Check the status field** in responses before processing data
7. **Log errors** for debugging and monitoring

## Support

For issues or questions regarding this API, please contact the system administrator or refer to the main API documentation.

---

**Version:** 1.0
**Last Updated:** October 7, 2025
**API Endpoint:** `/api/payroll-report`

