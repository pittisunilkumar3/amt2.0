# Teacher Authentication API - Troubleshooting Guide

## Database Connection Error Fix

The error you encountered was a database connection issue. Here are the fixes applied and steps to resolve it:

## Issues Fixed

### 1. Database Configuration
**Fixed:** Typo in `api/application/config/database.php`
- Changed `'prodcution'` to `'production'` on line 18

### 2. JWT Library Loading Error
**Fixed:** "Unable to load the requested class: Jwt_lib"
- Fixed case sensitivity issue (jwt_lib → JWT_lib)
- Added file existence check before loading
- Made JWT library completely optional
- Added proper error handling for JWT operations

### 3. Model Dependencies
**Fixed:** Simplified model loading and added error handling
- Removed complex dependency chains
- Added safe defaults for settings
- Made JWT token generation optional
- Fixed library loading with try-catch blocks

### 4. Controller Initialization
**Fixed:** Improved controller constructor
- Added explicit database loading
- Simplified dependency loading
- Added error handling
- Removed problematic model dependencies

## Testing Steps

### Step 1: Test Database Connection
Visit these URLs to test your database connection:

```
http://your-domain.com/api/test-db
http://your-domain.com/api/test-db/staff
http://your-domain.com/api/test-db/settings
http://your-domain.com/api/test-db/auth-tables
```

**Expected Response:**
```json
{
    "status": 1,
    "message": "Database connection successful",
    "database": "your_database_name"
}
```

### Step 2: Test Teacher Auth Controller
Visit this URL to test the controller:

```
http://your-domain.com/api/teacher/test
```

**Expected Response:**
```json
{
    "status": 1,
    "message": "Teacher Auth Controller is working",
    "timestamp": "2024-01-01 12:00:00",
    "database_connected": true
}
```

### Step 3: Test Simple Teacher Login (No JWT)
Use this endpoint to test basic login without JWT:

```
POST http://your-domain.com/api/teacher/simple-login
```

**Headers:**
```
Client-Service: smartschool
Auth-Key: schoolAdmin@
Content-Type: application/json
```

**Body:**
```json
{
    "email": "teacher@school.com",
    "password": "teacher123"
}
```

### Step 4: Test Full Teacher Login (With JWT)
Once simple login works, test the full login:

```
POST http://your-domain.com/api/teacher/login
```

**Headers:**
```
Client-Service: smartschool
Auth-Key: schoolAdmin@
Content-Type: application/json
```

**Body:**
```json
{
    "email": "teacher@school.com",
    "password": "teacher123"
}
```

## Database Requirements

### Required Tables
Make sure these tables exist in your database:

1. **staff** - Teacher information
2. **users_authentication** - Authentication tokens
3. **sch_settings** - System settings
4. **roles** - User roles
5. **staff_roles** - Role assignments

### Sample Teacher Record
Insert a test teacher record:

```sql
INSERT INTO staff (
    employee_id, name, surname, email, password, 
    contact_no, designation, department, is_active,
    date_of_joining, gender, lang_id, currency_id
) VALUES (
    'TEACH001', 'Test', 'Teacher', 'teacher@school.com', 'teacher123',
    '1234567890', 1, 1, 1,
    '2020-01-15', 'Male', 1, 0
);
```

## Common Issues and Solutions

### Issue 1: "Unable to connect to database"
**Cause:** Database credentials or configuration issue
**Solution:**
1. Check database credentials in `api/application/config/database.php`
2. Verify database server is running
3. Test connection using phpMyAdmin or similar tool
4. Check if database name exists

### Issue 2: "Table doesn't exist"
**Cause:** Missing database tables
**Solution:**
1. Import the complete database schema
2. Run the migration script: `api/database_updates/teacher_auth_migration.sql`
3. Verify all required tables exist

### Issue 3: "Class not found"
**Cause:** Missing model or library files
**Solution:**
1. Verify all files are uploaded correctly
2. Check file permissions (755 for directories, 644 for files)
3. Clear any caches

### Issue 4: "Invalid Email or Password"
**Cause:** No teacher record or wrong credentials
**Solution:**
1. Insert a test teacher record (see sample above)
2. Verify email and password match database
3. Check if teacher is active (`is_active = 1`)

### Issue 5: "JWT Token Error"
**Cause:** JWT library issues
**Solution:**
1. JWT is now optional - login will work without it
2. Check if `api/application/libraries/JWT_lib.php` exists
3. Verify PHP version supports required functions

## File Checklist

Ensure these files exist and have correct permissions:

### Controllers
- ✅ `api/application/controllers/Teacher_auth.php`
- ✅ `api/application/controllers/Teacher_webservice.php`
- ✅ `api/application/controllers/Test_db.php`

### Models
- ✅ `api/application/models/Teacher_auth_model.php`
- ✅ `api/application/models/Teacher_permission_model.php`
- ✅ `api/application/models/Staff_model.php`
- ✅ `api/application/models/Setting_model.php`

### Libraries
- ✅ `api/application/libraries/JWT_lib.php`
- ✅ `api/application/libraries/Teacher_middleware.php`

### Helpers
- ✅ `api/application/helpers/teacher_auth_helper.php`
- ✅ `api/application/helpers/json_output_helper.php`

### Configuration
- ✅ `api/application/config/database.php`
- ✅ `api/application/config/routes.php`

## Testing Checklist

### Basic Tests
- [ ] Database connection test passes
- [ ] Teacher auth controller test passes
- [ ] Staff table accessible
- [ ] Settings table accessible

### Authentication Tests
- [ ] Teacher login with valid credentials
- [ ] Teacher login with invalid credentials
- [ ] Token generation works
- [ ] Profile retrieval works

### API Tests
- [ ] All endpoints return proper JSON
- [ ] Error handling works correctly
- [ ] Authentication middleware works
- [ ] Permission system functions

## Production Deployment

### Security Updates Needed
1. **Change JWT Secret Key** in `JWT_lib.php`
2. **Implement Password Hashing** (bcrypt/Argon2)
3. **Enable HTTPS** for all API calls
4. **Update Database Credentials** for production
5. **Set Proper File Permissions**

### Performance Optimizations
1. **Add Database Indexes** for frequently queried fields
2. **Enable Query Caching** in database config
3. **Implement Redis/Memcached** for session storage
4. **Optimize Database Queries**

## Support Information

### Log Files to Check
- PHP error logs
- Apache/Nginx error logs
- CodeIgniter logs (if enabled)

### Debug Mode
To enable debug mode, set in `api/index.php`:
```php
define('ENVIRONMENT', 'development');
```

### Contact Information
If issues persist:
1. Check the error logs for specific error messages
2. Test each component individually using the test endpoints
3. Verify database schema matches requirements
4. Ensure all files have correct permissions

## Success Indicators

The system is working correctly when:
- ✅ Database connection test passes
- ✅ Teacher login returns authentication tokens
- ✅ Profile retrieval works with tokens
- ✅ Menu and permission endpoints function
- ✅ No PHP errors in logs
- ✅ All API endpoints return proper JSON responses

Follow this guide step by step, and the Teacher Authentication API should work correctly.
