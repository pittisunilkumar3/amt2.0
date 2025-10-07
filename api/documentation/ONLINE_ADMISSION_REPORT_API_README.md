# Online Admission Report API - README

## 📋 Overview

The **Online Admission Report API** provides flexible endpoints for retrieving online admission data with advanced filtering capabilities. It returns comprehensive information about online admissions including student details, admission status, payment status, and enrollment status.

### Key Highlights
- ✅ **Comprehensive data** - Returns all online admission fields
- ✅ **Flexible filtering** - Filter by class, section, admission status, and date range
- ✅ **Graceful null/empty handling** - Returns all records when no filters provided
- ✅ **Multi-select support** - Filter by multiple classes, sections, or statuses
- ✅ **Date range filtering** - Filter by admission date range
- ✅ **Payment information** - Includes paid amount calculations
- ✅ **RESTful design** - Follows REST API best practices

---

## 🚀 Quick Start

### 1. Basic Request (All Online Admissions)
```bash
curl -X POST "http://localhost/amt/api/online-admission-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

### 2. Filter by Class
```bash
curl -X POST "http://localhost/amt/api/online-admission-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{"class_id": 1}'
```

### 3. Filter by Admission Status
```bash
curl -X POST "http://localhost/amt/api/online-admission-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{"admission_status": 1}'
```

### 4. Filter by Date Range
```bash
curl -X POST "http://localhost/amt/api/online-admission-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{"from_date": "2025-01-01", "to_date": "2025-12-31"}'
```

---

## ✨ Features

### 1. Comprehensive Online Admission Data
Each record includes:
- **Reference Information**: reference_no, admission_no
- **Personal Information**: firstname, middlename, lastname, mobileno, email, dob, gender
- **Status Information**: form_status, paid_status, is_enroll
- **Class Information**: class_id, class, section_id, section
- **Student Information**: student_id, admission_date
- **Payment Information**: paid_amount (calculated from online_admission_payment table)
- **Timestamps**: created_at

### 2. Flexible Filtering
Filter by:
- **Class** (single or multiple)
- **Section** (single or multiple)
- **Admission Status** (0=pending, 1=admitted)
- **Date Range** (from_date, to_date)

### 3. Admission Status Values
- **0** - Pending (not yet admitted)
- **1** - Admitted (enrolled)

### 4. Form Status Values
- **0** - Not submitted
- **1** - Submitted

### 5. Payment Status Values
- **0** - Unpaid
- **1** - Paid
- **2** - Processing

---

## 📚 API Endpoints

### 1. Filter Online Admission Report
**URL:** `POST /api/online-admission-report/filter`

**Parameters (all optional):**
- `class_id` - integer or array
- `section_id` - integer or array
- `admission_status` - integer or array (0 or 1)
- `from_date` - string (YYYY-MM-DD format)
- `to_date` - string (YYYY-MM-DD format)

### 2. List All Online Admissions
**URL:** `POST /api/online-admission-report/list`

**Parameters:** None (returns all online admissions)

---

## 🧪 Testing

### Interactive HTML Tester
Open `online_admission_report_api_test.html` in your browser for an interactive testing interface.

### Manual Testing with cURL

**Test 1: All Online Admissions**
```bash
curl -X POST "http://localhost/amt/api/online-admission-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

**Test 2: Pending Admissions**
```bash
curl -X POST "http://localhost/amt/api/online-admission-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{"admission_status": 0}'
```

**Test 3: Admitted Students**
```bash
curl -X POST "http://localhost/amt/api/online-admission-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{"admission_status": 1}'
```

**Test 4: Date Range with Class Filter**
```bash
curl -X POST "http://localhost/amt/api/online-admission-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{"class_id": 1, "from_date": "2025-01-01", "to_date": "2025-12-31"}'
```

---

## 💡 Code Examples

### JavaScript
```javascript
async function getOnlineAdmissionReport(filters = {}) {
  const response = await fetch('http://localhost/amt/api/online-admission-report/filter', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'Client-Service': 'smartschool',
      'Auth-Key': 'schoolAdmin@'
    },
    body: JSON.stringify(filters)
  });
  return await response.json();
}

// Usage
const data = await getOnlineAdmissionReport({ 
  class_id: 1, 
  admission_status: 1 
});
```

### PHP
```php
function getOnlineAdmissionReport($filters = []) {
    $ch = curl_init('http://localhost/amt/api/online-admission-report/filter');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($filters));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Client-Service: smartschool',
        'Auth-Key: schoolAdmin@'
    ]);
    $response = curl_exec($ch);
    curl_close($ch);
    return json_decode($response, true);
}

// Usage
$data = getOnlineAdmissionReport([
    'class_id' => 1,
    'admission_status' => 1
]);
```

### Python
```python
import requests
import json

def get_online_admission_report(filters={}):
    url = 'http://localhost/amt/api/online-admission-report/filter'
    headers = {
        'Content-Type': 'application/json',
        'Client-Service': 'smartschool',
        'Auth-Key': 'schoolAdmin@'
    }
    response = requests.post(url, headers=headers, json=filters)
    return response.json()

# Usage
data = get_online_admission_report({
    'class_id': 1,
    'admission_status': 1
})
```

---

## 🔧 Troubleshooting

### Common Issues

#### 1. 401 Unauthorized Error
**Solution:** Ensure you're sending the correct headers:
```
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

#### 2. 400 Bad Request
**Solution:** Use POST method, not GET

#### 3. Empty Response
**Solution:**
- Check if online_admissions table has data
- Verify class_section_id is properly set
- Review application logs

#### 4. Incorrect Date Format
**Solution:** Use YYYY-MM-DD format for dates (e.g., "2025-01-01")

---

## 📊 Response Example

```json
{
  "status": 1,
  "message": "Online admission report retrieved successfully",
  "filters_applied": {
    "class_id": [1],
    "section_id": null,
    "admission_status": [1],
    "from_date": null,
    "to_date": null
  },
  "total_records": 25,
  "data": [
    {
      "id": "1",
      "reference_no": "REF001",
      "admission_no": "ADM2025001",
      "firstname": "John",
      "middlename": "M",
      "lastname": "Doe",
      "mobileno": "1234567890",
      "email": "john@example.com",
      "dob": "2010-05-15",
      "gender": "Male",
      "form_status": "1",
      "paid_status": "1",
      "is_enroll": "1",
      "created_at": "2025-01-15 10:30:00",
      "class_id": "1",
      "class": "Class 1",
      "section_id": "1",
      "section": "A",
      "student_id": "123",
      "admission_date": "2025-01-20",
      "paid_amount": "5000.00"
    }
  ],
  "timestamp": "2025-10-07 10:30:45"
}
```

---

## 📝 Version History

| Version | Date | Changes |
|---------|------|---------|
| 1.0.0 | 2025-10-07 | Initial release |

---

## 🎓 Related APIs

- **Student Report API**
- **Student Profile Report API**
- **Admission Report API**
- **Boys Girls Ratio Report API**

---

## ✅ Quick Checklist

Before using the API, ensure:
- [ ] Server is running
- [ ] Database is accessible
- [ ] Authentication headers are correct
- [ ] Using POST method
- [ ] Request body is valid JSON
- [ ] Routes are configured
- [ ] Models are loaded correctly
- [ ] online_admissions table has data

---

## 🎉 Success!

You're now ready to use the Online Admission Report API!

Happy coding! 🚀

