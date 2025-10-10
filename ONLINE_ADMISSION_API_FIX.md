# Online Admission API Fix

## Date: 2025-10-10

## Issue Description

**Error Message:**
```
Type: Error
Message: Call to undefined method Onlinestudent_model::get()
Filename: C:\xampp\htdocs\amt\api\application\controllers\Online_admission_api.php
Line Number: 462
```

**Problem:**
The Online Admission API's filter endpoint (`/api/online-admission/filter`) was calling `$this->onlinestudent_model->get(null, null)` on line 462, but the API's `Onlinestudent_model` didn't have a `get()` method defined.

**Root Cause:**
The `api/application/models/Onlinestudent_model.php` was created specifically for the Online Admission Fee Report API and only contained methods related to fee collection reports. It didn't have the general-purpose `get()` method that exists in the main application's `application/models/Onlinestudent_model.php`.

---

## Solution Applied

### Added `get()` Method to API Model

**File Modified:** `api/application/models/Onlinestudent_model.php`

**Method Added:**
```php
/**
 * Get online admission records
 * Returns online admission records with related data (class, section, category, etc.)
 *
 * @param int $id Optional - specific admission ID to retrieve
 * @param array $carray Optional - array of class IDs to filter by
 * @return array Single record (if $id provided) or array of records
 */
public function get($id = null, $carray = null)
{
    $this->db->select('online_admissions.vehroute_id,vehicle_routes.route_id,vehicle_routes.vehicle_id,transport_route.route_title,vehicles.vehicle_no,hostel_rooms.room_no,vehicles.driver_name,vehicles.driver_contact,hostel.id as `hostel_id`,hostel.hostel_name,room_types.id as `room_type_id`,room_types.room_type ,online_admissions.hostel_room_id,class_sections.id as class_section_id,classes.id AS `class_id`,classes.class,sections.id AS `section_id`,sections.section,online_admissions.id,online_admissions.admission_no , online_admissions.roll_no,online_admissions.admission_date,online_admissions.firstname,online_admissions.middlename, online_admissions.lastname,online_admissions.image,    online_admissions.mobileno, online_admissions.email ,online_admissions.state ,   online_admissions.city , online_admissions.pincode , online_admissions.note, online_admissions.religion, online_admissions.cast, school_houses.house_name,   online_admissions.dob ,online_admissions.current_address, online_admissions.previous_school,
        online_admissions.guardian_is,
        online_admissions.permanent_address,IFNULL(online_admissions.category_id, 0) as `category_id`,IFNULL(categories.category, "") as `category`,online_admissions.adhar_no,online_admissions.samagra_id,online_admissions.bank_account_no,online_admissions.bank_name, online_admissions.ifsc_code , online_admissions.guardian_name , online_admissions.father_pic ,online_admissions.height ,online_admissions.weight,online_admissions.measurement_date, online_admissions.mother_pic , online_admissions.guardian_pic , online_admissions.guardian_relation,online_admissions.guardian_phone,online_admissions.guardian_address,online_admissions.is_enroll ,online_admissions.created_at,online_admissions.document ,online_admissions.updated_at,online_admissions.father_name,online_admissions.father_phone,online_admissions.blood_group,online_admissions.school_house_id,online_admissions.father_occupation,online_admissions.mother_name,online_admissions.mother_phone,online_admissions.mother_occupation,online_admissions.guardian_occupation,online_admissions.gender,online_admissions.guardian_is,online_admissions.rte,online_admissions.guardian_email,online_admissions.paid_status,online_admissions.form_status,online_admissions.reference_no,online_admissions.class_section_id')->from('online_admissions');

    $this->db->join('class_sections', 'class_sections.id = online_admissions.class_section_id', 'left');
    $this->db->join('classes', 'class_sections.class_id = classes.id', 'left');
    $this->db->join('sections', 'sections.id = class_sections.section_id', 'left');
    $this->db->join('hostel_rooms', 'hostel_rooms.id = online_admissions.hostel_room_id', 'left');
    $this->db->join('hostel', 'hostel.id = hostel_rooms.hostel_id', 'left');
    $this->db->join('room_types', 'room_types.id = hostel_rooms.room_type_id', 'left');
    $this->db->join('categories', 'online_admissions.category_id = categories.id', 'left');
    $this->db->join('vehicle_routes', 'vehicle_routes.id = online_admissions.vehroute_id', 'left');
    $this->db->join('transport_route', 'vehicle_routes.route_id = transport_route.id', 'left');
    $this->db->join('vehicles', 'vehicles.id = vehicle_routes.vehicle_id', 'left');
    $this->db->join('school_houses', 'school_houses.id = online_admissions.school_house_id', 'left');

    if ($carray != null) {
        $this->db->where_in('classes.id', $carray);
    }

    if ($id != null) {
        $this->db->where('online_admissions.id', $id);
    } else {
        $this->db->order_by('online_admissions.id', 'desc');
    }
    
    $query = $this->db->get();
    
    if ($id != null) {
        return $query->row_array();
    } else {
        return $query->result_array();
    }
}
```

---

## What This Method Does

The `get()` method retrieves online admission records from the database with comprehensive related data:

### Parameters:
- `$id` (optional): Specific admission ID to retrieve. If provided, returns a single record.
- `$carray` (optional): Array of class IDs to filter results by.

### Returns:
- If `$id` is provided: Single record as associative array
- If `$id` is null: Array of all records (or filtered by class IDs if `$carray` provided)

### Joined Tables:
The method performs LEFT JOINs with multiple tables to provide complete admission information:
- `class_sections` - Class and section assignment
- `classes` - Class details
- `sections` - Section details
- `hostel_rooms` - Hostel room assignment
- `hostel` - Hostel details
- `room_types` - Room type information
- `categories` - Student category
- `vehicle_routes` - Transport route assignment
- `transport_route` - Route details
- `vehicles` - Vehicle information
- `school_houses` - School house assignment

### Fields Retrieved:
The method retrieves comprehensive student information including:
- Personal details (name, DOB, gender, contact info)
- Academic details (class, section, admission number)
- Guardian information (father, mother, guardian details)
- Address information (current, permanent)
- Transport details (route, vehicle)
- Hostel details (hostel, room)
- Status fields (enrollment status, form status, payment status)
- Documents and additional information

---

## API Endpoint Usage

### Filter Endpoint
**URL:** `POST /api/online-admission/filter`

**Headers:**
```
Client-Service: smartschool
Auth-Key: schoolAdmin@
Content-Type: application/json
```

**Request Body (all parameters optional):**
```json
{
  "class_id": 1,
  "section_id": 2,
  "category_id": 3,
  "gender": "Male",
  "is_enroll": "1",
  "form_status": "approved",
  "paid_status": "paid",
  "date_from": "2024-01-01",
  "date_to": "2024-12-31",
  "search": "John"
}
```

**Example cURL Command:**
```bash
curl -X POST "http://localhost/amt/api/online-admission/filter" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -H "Content-Type: application/json" \
  -d '{}'
```

**Response:**
```json
{
  "status": 1,
  "message": "Online admissions retrieved successfully",
  "data": [
    {
      "id": "1",
      "reference_no": "REF001",
      "admission_no": "ADM001",
      "firstname": "John",
      "lastname": "Doe",
      "class": "Class 1",
      "section": "A",
      "gender": "Male",
      "is_enroll": "1",
      "form_status": "approved",
      "paid_status": "paid",
      ...
    }
  ],
  "total_records": 1,
  "filtered_records": 1
}
```

---

## Testing

A test script has been created: `test_online_admission_api.php`

### Run Tests:

**Command Line:**
```bash
php test_online_admission_api.php
```

**Browser:**
```
http://localhost/amt/test_online_admission_api.php
```

### Test Cases Included:
1. ✅ Filter with empty request body (returns all records)
2. ✅ Filter by gender
3. ✅ Filter by enrollment status
4. ✅ Filter by date range
5. ✅ List endpoint (get filter options)

---

## Files Modified

1. ✅ `api/application/models/Onlinestudent_model.php` - Added `get()` method

---

## Files Created

1. ✅ `test_online_admission_api.php` - Test script for API verification
2. ✅ `ONLINE_ADMISSION_API_FIX.md` - This documentation file

---

## Impact

### Before Fix:
- ❌ API endpoint returned error: "Call to undefined method Onlinestudent_model::get()"
- ❌ Unable to retrieve online admission records via API
- ❌ Filter functionality was broken

### After Fix:
- ✅ API endpoint works correctly
- ✅ Can retrieve all online admission records
- ✅ Can filter by class, section, gender, enrollment status, etc.
- ✅ Returns comprehensive student information with related data

---

## Notes

- The `get()` method was copied from the main application's `Onlinestudent_model` to maintain consistency
- All existing functionality in the API model (fee collection reports) remains unchanged
- The method uses LEFT JOINs to ensure records are returned even if related data is missing
- The method supports both single record retrieval (by ID) and bulk retrieval with optional filtering

---

## Related Documentation

- `api/documentation/ONLINE_ADMISSION_API_DOCUMENTATION.md` - Full API documentation
- `application/models/Onlinestudent_model.php` - Main application model (source of the method)

