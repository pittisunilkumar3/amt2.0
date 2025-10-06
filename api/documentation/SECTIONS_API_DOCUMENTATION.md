# Sections API Documentation

## Overview

The Sections API provides comprehensive CRUD (Create, Read, Update, Delete) functionality for managing school section records. This API allows you to manage the section system used for organizing students into different sections within classes.

---

## Base URL
```
http://{domain}/api/
```

## Important URL Structure

**For Sections APIs, use the controller/method pattern:**
- List sections: `http://{domain}/api/sections/list`
- Get single section: `http://{domain}/api/sections/get/{id}`
- Create section: `http://{domain}/api/sections/create`
- Update section: `http://{domain}/api/sections/update/{id}`
- Delete section: `http://{domain}/api/sections/delete/{id}`

**Examples:**
- List all: `http://localhost/amt/api/sections/list`
- Get section: `http://localhost/amt/api/sections/get/5`
- Create: `http://localhost/amt/api/sections/create`
- Update: `http://localhost/amt/api/sections/update/5`
- Delete: `http://localhost/amt/api/sections/delete/5`

## Authentication Headers

All API requests require the following headers:
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

---

## Endpoints

### 1. List Sections

**Endpoint:** `POST /sections/list`
**Full URL:** `http://localhost/amt/api/sections/list`

**Description:** Retrieve a list of all section records.

#### Request Headers
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

#### Request Body
```json
{}
```

#### Success Response (HTTP 200)
```json
{
  "status": 1,
  "message": "Sections retrieved successfully",
  "total_records": 4,
  "data": [
    {
      "id": 1,
      "section": "A",
      "is_active": "yes",
      "created_at": "2024-01-15 10:30:00",
      "updated_at": "2024-01-15 10:30:00"
    },
    {
      "id": 2,
      "section": "B",
      "is_active": "yes",
      "created_at": "2024-01-16 11:45:00",
      "updated_at": "2024-01-16 11:45:00"
    },
    {
      "id": 3,
      "section": "C",
      "is_active": "yes",
      "created_at": "2024-01-17 09:15:00",
      "updated_at": "2024-01-17 09:15:00"
    }
  ]
}
```

---

### 2. Get Single Section

**Endpoint:** `POST /sections/get/{id}`
**Full URL:** `http://localhost/amt/api/sections/get/5`

**Description:** Retrieve detailed information for a specific section record.

#### Request Headers
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

#### URL Parameters
- `id` (required): Section record ID

#### Request Body
```json
{}
```

#### Success Response (HTTP 200)
```json
{
  "status": 1,
  "message": "Section record retrieved successfully",
  "data": {
    "id": 5,
    "section": "A",
    "is_active": "yes",
    "created_at": "2024-01-15 10:30:00",
    "updated_at": "2024-01-15 10:30:00"
  }
}
```

---

### 3. Create Section

**Endpoint:** `POST /sections/create`
**Full URL:** `http://localhost/amt/api/sections/create`

**Description:** Create a new section record.

#### Request Headers
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

#### Request Body
```json
{
  "section": "D",
  "is_active": "yes"
}
```

#### Request Parameters
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| section | string | Yes | The section name (cannot be empty) |
| is_active | string | No | Active status ("yes" or "no"), defaults to "yes" |

#### Success Response (HTTP 201)
```json
{
  "status": 1,
  "message": "Section created successfully",
  "data": {
    "id": 6,
    "section": "D",
    "is_active": "yes",
    "created_at": "2024-01-20 14:30:00"
  }
}
```

---

### 4. Update Section

**Endpoint:** `POST /sections/update/{id}`
**Full URL:** `http://localhost/amt/api/sections/update/5`

**Description:** Update an existing section record.

#### Request Headers
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

#### URL Parameters
- `id` (required): Section record ID to update

#### Request Body
```json
{
  "section": "Updated Section A",
  "is_active": "yes"
}
```

#### Request Parameters
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| section | string | Yes | The updated section name (cannot be empty) |
| is_active | string | No | Active status ("yes" or "no") |

#### Success Response (HTTP 200)
```json
{
  "status": 1,
  "message": "Section updated successfully",
  "data": {
    "id": 5,
    "section": "Updated Section A",
    "is_active": "yes",
    "updated_at": "2024-01-20 15:45:00"
  }
}
```

---

### 5. Delete Section

**Endpoint:** `POST /sections/delete/{id}`
**Full URL:** `http://localhost/amt/api/sections/delete/5`

**Description:** Delete an existing section record.

#### Request Headers
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

#### URL Parameters
- `id` (required): Section record ID to delete

#### Request Body
```json
{}
```

#### Success Response (HTTP 200)
```json
{
  "status": 1,
  "message": "Section deleted successfully",
  "data": {
    "id": 5,
    "section": "Section A"
  }
}
```

---

## Error Responses

### 400 Bad Request
```json
{
  "status": 0,
  "message": "Invalid or missing section ID",
  "data": null
}
```

```json
{
  "status": 0,
  "message": "Section name is required and cannot be empty",
  "data": null
}
```

### 401 Unauthorized
```json
{
  "status": 0,
  "message": "Unauthorized access. Invalid headers.",
  "data": null
}
```

### 404 Not Found
```json
{
  "status": 0,
  "message": "Section record not found",
  "data": null
}
```

### 405 Method Not Allowed
```json
{
  "status": 0,
  "message": "Method not allowed. Use POST method.",
  "data": null
}
```

### 500 Internal Server Error
```json
{
  "status": 0,
  "message": "Internal server error occurred",
  "data": null
}
```

---

## Usage Examples

### Example 1: Get All Sections
```bash
curl -X POST "http://localhost/amt/api/sections/list" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

### Example 2: Create New Section
```bash
curl -X POST "http://localhost/amt/api/sections/create" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "section": "E",
    "is_active": "yes"
  }'
```

### Example 3: Update Section
```bash
curl -X POST "http://localhost/amt/api/sections/update/3" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "section": "Updated Section C",
    "is_active": "yes"
  }'
```

### Example 4: Delete Section
```bash
curl -X POST "http://localhost/amt/api/sections/delete/3" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

### Example 5: Get Specific Section
```bash
curl -X POST "http://localhost/amt/api/sections/get/2" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

---

## Database Tables Used

- `sections` - Main section records table

### Table Structure
```sql
CREATE TABLE `sections` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `section` varchar(60) DEFAULT NULL,
  `is_active` varchar(255) DEFAULT 'no',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_at` date DEFAULT NULL,
  PRIMARY KEY (`id`)
);
```

---

## Validation Rules

1. **Section Field:**
   - Required for create and update operations
   - Cannot be empty or contain only whitespace
   - String type
   - Maximum length: 60 characters

2. **Is Active Field:**
   - Optional for all operations
   - Must be "yes" or "no"
   - Defaults to "yes" for new records

3. **ID Parameter:**
   - Must be a positive integer
   - Required for get, update, and delete operations
   - Must exist in the database for update and delete operations

---

## Notes

1. All endpoints require POST method
2. Authentication headers are mandatory for all requests
3. Section name field is trimmed of leading/trailing whitespace
4. All responses include status, message, and data fields
5. Error responses follow consistent format
6. Successful creation returns HTTP 201, others return HTTP 200
7. Database transactions are used for data integrity
8. Audit logging is implemented for all operations
9. The `is_active` field defaults to "yes" for new sections

---

## Common Use Cases

1. **List all sections** - For populating dropdown menus and section selection interfaces
2. **Create section** - Adding new sections to the school system
3. **Update section** - Modifying section names or active status
4. **Delete section** - Removing sections that are no longer needed
5. **Get specific section** - Retrieving details for editing forms or display

---

## Section System Integration

The Sections API integrates with:
- **Classes Management** - Sections are linked to classes via class_sections table
- **Student Management** - Students are assigned to sections within classes
- **Teacher Management** - Teachers are assigned to class-section combinations
- **Timetable** - Sections are used in scheduling
- **Examinations** - Sections are used for exam organization
- **Reports** - Section-wise reports and statistics

---

## Best Practices

1. **Standard Names** - Use standard section names (A, B, C, etc.)
2. **Consistent Naming** - Maintain consistent naming conventions across the school
3. **Active Status** - Use the is_active field to temporarily disable sections
4. **Validation** - Always validate section assignments before deletion
5. **Backup** - Maintain backups before bulk operations
6. **Dependencies** - Check for dependent records (students, class_sections) before deletion

---

## Support

For API support and questions, contact the development team or refer to the main project documentation.
