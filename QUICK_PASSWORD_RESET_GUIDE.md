# Quick Password Reset Guide

## For Superadmin Account

### Current Working Credentials

```
Email: amaravatijuniorcollege@gmail.com
Password: 2017@amaravathi
Login URL: http://localhost/amt/site/login
```

---

## Method 1: Using the Fix Script (Recommended)

### Step 1: Edit the Script
Open `check_and_fix_admin_login.php` and modify these lines:

```php
// Admin credentials to check/fix
$admin_email = 'amaravatijuniorcollege@gmail.com';  // Change if needed
$admin_password = 'your_new_password_here';          // Change to new password
```

### Step 2: Run the Script
```bash
C:\xampp\php\php.exe check_and_fix_admin_login.php
```

### Step 3: Login
Use the new password to login at: http://localhost/amt/site/login

---

## Method 2: Direct Database Update

### Step 1: Generate Password Hash

Create a file `generate_hash.php`:
```php
<?php
$password = 'your_new_password';
$hash = password_hash($password, PASSWORD_DEFAULT);
echo "Password: $password\n";
echo "Hash: $hash\n";
?>
```

Run it:
```bash
C:\xampp\php\php.exe generate_hash.php
```

### Step 2: Update Database

Open phpMyAdmin or MySQL command line:
```sql
UPDATE staff 
SET password = 'paste_hash_here' 
WHERE email = 'amaravatijuniorcollege@gmail.com';
```

---

## Method 3: Using Forgot Password Feature

1. Go to: http://localhost/amt/site/login
2. Click "Forgot Password"
3. Enter email: amaravatijuniorcollege@gmail.com
4. Check your email for reset link
5. Follow the link and set new password

**Note:** This requires email to be configured in the system.

---

## Troubleshooting

### Issue: "Account is disabled"
**Solution:**
```sql
UPDATE staff 
SET is_active = 1 
WHERE email = 'amaravatijuniorcollege@gmail.com';
```

### Issue: "No roles assigned"
**Solution:**
```sql
INSERT INTO staff_roles (staff_id, role_id) 
VALUES (1, 7);  -- 7 is Super Admin role
```

### Issue: "User not found"
**Solution:** Check available users:
```sql
SELECT id, name, surname, email, is_active 
FROM staff 
ORDER BY id;
```

---

## Database Information

**Database:** amt
**Host:** localhost
**User:** root
**Password:** (empty)

**Key Tables:**
- `staff` - User accounts
- `staff_roles` - User role assignments
- `roles` - Available roles

**Super Admin:**
- User ID: 1
- Role ID: 7
- Email: amaravatijuniorcollege@gmail.com

---

## Quick Commands

### Check User Status
```sql
SELECT s.id, s.name, s.surname, s.email, s.is_active, 
       sr.role_id, r.name as role_name
FROM staff s
LEFT JOIN staff_roles sr ON s.id = sr.staff_id
LEFT JOIN roles r ON sr.role_id = r.id
WHERE s.email = 'amaravatijuniorcollege@gmail.com';
```

### Reset Password (SQL)
```sql
-- Replace 'NEW_HASH_HERE' with actual hash from password_hash()
UPDATE staff 
SET password = 'NEW_HASH_HERE' 
WHERE email = 'amaravatijuniorcollege@gmail.com';
```

### Enable Account
```sql
UPDATE staff 
SET is_active = 1 
WHERE email = 'amaravatijuniorcollege@gmail.com';
```

---

## Important Notes

1. **Password Hashing:** Always use `password_hash()` - never store plain text passwords
2. **Hash Format:** Bcrypt hashes are 60 characters long and start with `$2y$10$`
3. **Verification:** Use `password_verify()` to check passwords
4. **Security:** Keep the fix script secure - it contains database credentials

---

## Contact Information

If you need further assistance:
1. Check `ADMIN_LOGIN_FIX_REPORT.md` for detailed information
2. Review the authentication code in:
   - `application/controllers/Site.php`
   - `application/models/Staff_model.php`
   - `application/libraries/Enc_lib.php`

