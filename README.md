# User Management - Testing Guide

## Overview

This guide provides detailed testing procedures for the User Management endpoints, with special focus on the **Deactivate/Activate** and **Reset 2FA** features.

## Important Distinction: Deactivate vs Reset 2FA

### ❌ Deactivate User
- **What it does**: Sets `is_active = false` in the user table
- **Effect**: User **cannot log in**
- **Data**: User data is **preserved**, not deleted
- **Reversible**: Yes - use **Activate** endpoint to enable again
- **Use case**: Temporarily disable account (suspension, leave, etc.)

```
User Account Status:
is_active = false ❌ Cannot login
is_active = true  ✅ Can login
```

### ❌ Reset 2FA
- **What it does**: Clears TOTP secret and backup codes
- **Effect**: User 2FA authentication is disabled
- **User can still log in**: Yes (if account is active)
- **Use case**: Account recovery, 2FA device reset, troubleshooting
- **Different from**: Deactivating the user account

```
2FA Status:
is_enabled = true  ✅ 2FA required on login
is_enabled = false ✅ 2FA not required, login normally
```

## Test Scenarios

### Scenario 1: Basic User List
**What to test**: Verify list-users endpoint returns is_active status

**Request:**
```bash
curl -X GET "http://localhost:7001/user-management/list-users?skip=0&take=50" \
  -H "Authorization: Bearer {access_token}"
```

**Expected Response:**
```json
{
  "data": [
    {
      "id": 1,
      "nama": "Admin User",
      "username": "admin",
      "role": "SUPERADMIN",
      "nomor_hp": "081234567890",
      "is_active": true,
      "created_at": "2026-05-13T10:00:00.000Z",
      ...
    }
  ],
  "total": 1,
  "page": 1,
  "limit": 50
}
```

**Verify:**
- ✅ `is_active` field is present
- ✅ Default value is `true` for active users
- ✅ All users visible in list

---

### Scenario 2: Deactivate User (Set to Inactive)
**What to test**: Deactivate a user account

**Test Steps:**

1. **Get User ID** - Note the user ID you want to deactivate (e.g., user ID = 2)

2. **Deactivate Request:**
```bash
curl -X POST "http://localhost:7001/user-management/deactivate" \
  -H "Authorization: Bearer {access_token}" \
  -H "Content-Type: application/json" \
  -d '{"userId": 2}'
```

3. **Expected Response:**
```json
{
  "message": "User deactivated successfully",
  "userId": 2,
  "is_active": false
}
```

4. **Verify in Database:**
```sql
SELECT id, username, is_active FROM user WHERE id = 2;
-- Should show: is_active = 0 (false)
```

5. **Try to Login** - Attempt to login with deactivated user's credentials
   - ❌ Login should **fail** or return unauthorized
   - User is blocked from login

**Success Criteria:**
- ✅ Endpoint returns `is_active: false`
- ✅ Database shows `is_active = 0`
- ✅ User cannot log in
- ✅ User data is NOT deleted

---

### Scenario 3: Activate User (Re-enable Account)
**What to test**: Reactivate a deactivated user

**Test Steps:**

1. **Use the user ID that was deactivated** (e.g., user ID = 2)

2. **Activate Request:**
```bash
curl -X POST "http://localhost:7001/user-management/activate" \
  -H "Authorization: Bearer {access_token}" \
  -H "Content-Type: application/json" \
  -d '{"userId": 2}'
```

3. **Expected Response:**
```json
{
  "message": "User activated successfully",
  "userId": 2,
  "is_active": true
}
```

4. **Verify in Database:**
```sql
SELECT id, username, is_active FROM user WHERE id = 2;
-- Should show: is_active = 1 (true)
```

5. **Try to Login Again** - Attempt to login with the reactivated user's credentials
   - ✅ Login should **succeed**
   - User has access again

**Success Criteria:**
- ✅ Endpoint returns `is_active: true`
- ✅ Database shows `is_active = 1`
- ✅ User can log in again
- ✅ User's password/data unchanged

---

### Scenario 4: Reset 2FA (Different from Deactivate)
**What to test**: Verify that reset 2FA is different from deactivate

**Prerequisites:**
- User should have 2FA enabled (is_enabled = true in user_two_factor table)

**Test Steps:**

1. **Get User ID with 2FA** (e.g., user ID = 1 who has 2FA setup)

2. **Check 2FA Status Before Reset:**
```sql
SELECT user_id, is_enabled, secret FROM user_two_factor WHERE user_id = '1';
-- Should show: is_enabled = 1, secret = (some value)
```

3. **Reset 2FA Request:**
```bash
curl -X POST "http://localhost:7001/user-management/reset-2fa" \
  -H "Authorization: Bearer {access_token}" \
  -H "Content-Type: application/json" \
  -d '{"userId": 1}'
```

4. **Expected Response:**
```json
{
  "message": "2FA reset successfully",
  "userId": 1,
  "twoFactorReset": true
}
```

5. **Verify in Database:**
```sql
SELECT user_id, is_enabled, secret FROM user_two_factor WHERE user_id = '1';
-- Should show: is_enabled = 0, secret = NULL
```

6. **Important: User Can Still Login**
   - ✅ User with reset 2FA can still log in
   - ✅ User account is **NOT deactivated** (is_active still = 1)
   - ✅ User only needs to re-setup 2FA if desired

**Success Criteria:**
- ✅ Endpoint returns `twoFactorReset: true`
- ✅ Database shows `is_enabled = 0`
- ✅ TOTP secret is cleared (NULL)
- ✅ User can still log in (account not deactivated)
- ✅ User account `is_active` remains `true`

---

### Scenario 5: Difference Between Deactivate and Reset 2FA
**What to test**: Demonstrate the clear difference between these two operations

| Feature | Deactivate User | Reset 2FA |
|---------|-----------------|-----------|
| **Field Changed** | `user.is_active` | `user_two_factor.is_enabled` |
| **Can Login After?** | ❌ NO | ✅ YES (without 2FA) |
| **User Data Deleted?** | ❌ NO | ❌ NO |
| **Reversible?** | ✅ YES (Activate) | ✅ YES (Setup 2FA again) |
| **Effect** | Account suspended | 2FA disabled |
| **Use Case** | Lock account | Recover device loss |

**Test:**

1. Deactivate a user with 2FA
2. Reset 2FA for a different active user
3. Verify:
   - Deactivated user cannot login AT ALL
   - Reset 2FA user can login without 2FA

---

### Scenario 6: Edit User (Verify is_active Status Preserved)
**What to test**: Verify that editing a user preserves their is_active status

**Test Steps:**

1. **Deactivate a user first**
```bash
curl -X POST "http://localhost:7001/user-management/deactivate" \
  -H "Authorization: Bearer {access_token}" \
  -H "Content-Type: application/json" \
  -d '{"userId": 3}'
```

2. **Edit that user's name:**
```bash
curl -X PUT "http://localhost:7001/user-management/3" \
  -H "Authorization: Bearer {access_token}" \
  -H "Content-Type: application/json" \
  -d '{"name": "Updated Name"}'
```

3. **Verify Response Contains is_active=false:**
```json
{
  "id": 3,
  "nama": "Updated Name",
  "username": "someuser",
  "is_active": false,
  ...
}
```

4. **Verify Database:**
```sql
SELECT id, name, is_active FROM user WHERE id = 3;
-- Should show: name = "Updated Name", is_active = 0
```

**Success Criteria:**
- ✅ Deactivated status is preserved
- ✅ User remains inactive after editing
- ✅ Response shows `is_active: false`

---

## Database Verification

### Check is_active Column Exists
```sql
DESCRIBE user;
-- Look for: is_active tinyint(1) NO MUL 1
```

### Check Index Created
```sql
SHOW INDEX FROM user;
-- Look for: IDX_user_is_active on is_active column
```

### Query Active vs Inactive Users
```sql
-- Active users
SELECT id, username, is_active FROM user WHERE is_active = 1;

-- Inactive users
SELECT id, username, is_active FROM user WHERE is_active = 0;

-- Count
SELECT COUNT(*) as active_count FROM user WHERE is_active = 1;
SELECT COUNT(*) as inactive_count FROM user WHERE is_active = 0;
```

### Check 2FA Status
```sql
-- Users with 2FA enabled
SELECT u.id, u.username, utf.is_enabled 
FROM user u 
LEFT JOIN user_two_factor utf ON u.id = utf.user_id 
WHERE utf.is_enabled = 1;

-- Users with 2FA disabled
SELECT u.id, u.username, utf.is_enabled 
FROM user u 
LEFT JOIN user_two_factor utf ON u.id = utf.user_id 
WHERE utf.is_enabled = 0 OR utf.is_enabled IS NULL;
```

---

## Postman Collection Testing

### Import and Setup
1. Open Postman
2. Import `USER_MANAGEMENT_POSTMAN.json`
3. Set `base_url` variable: `http://localhost:7001`
4. Set `access_token` variable with your JWT token

### Test Sequence
1. **1. List Users** - Verify is_active field present
2. **2. Search Users** - Verify is_active in results
3. **5. Deactivate User** - Set user to inactive
4. **6. Activate User** - Re-enable user
5. **3. Reset Password** - Change user password
6. **4. Edit User** - Modify user info
7. **7. Reset 2FA** - Clear 2FA settings

---

## Common Issues & Troubleshooting

### Issue: Deactivate endpoint not working
**Check:**
- ✅ is_active column exists in user table
- ✅ Migration was recorded in migrations table
- ✅ User ID is correct
- ✅ JWT token is valid

### Issue: Cannot see is_active in response
**Check:**
- ✅ User entity has is_active field
- ✅ User management service is using user.is_active (not hardcoded)
- ✅ App was restarted after code changes

### Issue: Deactivated user can still login
**Check:**
- ✅ Authentication controller is checking is_active field
- ✅ Login validation includes is_active check
- ✅ Database shows is_active = 0

### Issue: Reset 2FA not clearing secret
**Check:**
- ✅ user_two_factor table exists
- ✅ User has a record in user_two_factor table
- ✅ is_enabled is set to false after reset
- ✅ secret field is NULL after reset

---

## Frontend Integration Notes

### Display User Status
```javascript
// In user list table
<span className={user.is_active ? 'badge-success' : 'badge-danger'}>
  {user.is_active ? 'Active' : 'Inactive'}
</span>
```

### Action Buttons Logic
```javascript
// Show different buttons based on status
if (user.is_active) {
  // Show: Edit, Deactivate, Reset Password, Reset 2FA
} else {
  // Show: Edit, Activate, Delete (maybe)
}
```

### Deactivate Confirmation
```javascript
if (confirm(`Deactivate user ${user.nama}? They will not be able to login.`)) {
  // Call deactivate endpoint
}
```

---

## Summary

✅ **is_active field** is now available in user table
✅ **Deactivate endpoint** prevents user from logging in
✅ **Activate endpoint** re-enables user login
✅ **Reset 2FA endpoint** is separate from deactivate
✅ User data is preserved in all operations
✅ All changes are reversible
