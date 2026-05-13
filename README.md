# User Management API Documentation

## Overview
The User Management Module provides comprehensive endpoints for managing users within the tenant system. This includes listing users, resetting passwords, editing user details, resetting 2FA, and deactivating users.

## Base URL
```
http://localhost:7001
```

## Authentication
All endpoints require JWT Bearer token authentication. Include the token in the Authorization header:
```
Authorization: Bearer {access_token}
```

## Endpoints

### 1. List Users
**Endpoint:** `GET /user-management/list-users`

**Description:** Get a paginated list of all users with their profile information.

**Query Parameters:**
- `skip` (optional): Number of records to skip (default: 0)
- `take` (optional): Number of records to take (default: 50)

**Response:**
```json
{
  "data": [
    {
      "id": 1,
      "nama": "Admin User",
      "username": "admin",
      "role": "SUPERADMIN",
      "nomor_hp": "081234567890",
      "email": "admin@example.com",
      "fullname": "Administrator",
      "nickname": null,
      "is_active": true,
      "expired_at": null,
      "fail_login": 0,
      "created_at": "2026-05-13T10:00:00.000Z",
      "updated_at": "2026-05-13T10:00:00.000Z"
    }
  ],
  "total": 1,
  "page": 1,
  "limit": 50
}
```

**Example Request (cURL):**
```bash
curl -X GET "http://localhost:7001/user-management/list-users?skip=0&take=50" \
  -H "Authorization: Bearer {access_token}"
```

**Example Request (Postman):**
```
Method: GET
URL: http://localhost:7001/user-management/list-users
Headers:
  - Authorization: Bearer {access_token}
Query Params:
  - skip: 0
  - take: 50
```

---

### 2. Search Users
**Endpoint:** `GET /user-management/search`

**Description:** Search users by name, username, or phone number.

**Query Parameters:**
- `term` (required): Search term (searches name, username, and phone)
- `skip` (optional): Number of records to skip (default: 0)
- `take` (optional): Number of records to take (default: 50)

**Response:**
```json
{
  "data": [
    {
      "id": 1,
      "nama": "Admin User",
      "username": "admin",
      "role": "SUPERADMIN",
      "nomor_hp": "081234567890",
      "email": "admin@example.com",
      "fullname": "Administrator",
      "nickname": null,
      "is_active": true,
      "expired_at": null,
      "fail_login": 0,
      "created_at": "2026-05-13T10:00:00.000Z",
      "updated_at": "2026-05-13T10:00:00.000Z"
    }
  ],
  "total": 1,
  "page": 1,
  "limit": 50
}
```

**Example Request (cURL):**
```bash
curl -X GET "http://localhost:7001/user-management/search?term=admin&skip=0&take=50" \
  -H "Authorization: Bearer {access_token}"
```

---

### 3. Reset Password
**Endpoint:** `POST /user-management/reset-password`

**Description:** Reset user password to default password: `I7lBLi'7x7s`

**Request Body:**
```json
{
  "userId": 1
}
```

**Response:**
```json
{
  "message": "Password reset successfully",
  "userId": 1,
  "newPassword": "I7lBLi'7x7s"
}
```

**Example Request (cURL):**
```bash
curl -X POST "http://localhost:7001/user-management/reset-password" \
  -H "Authorization: Bearer {access_token}" \
  -H "Content-Type: application/json" \
  -d '{"userId": 1}'
```

**Example Request (PowerShell):**
```powershell
$body = @{"userId" = 1} | ConvertTo-Json
Invoke-WebRequest -Uri "http://localhost:7001/user-management/reset-password" `
  -Method POST `
  -Headers @{"Authorization" = "Bearer {access_token}"; "Content-Type" = "application/json"} `
  -Body $body `
  -UseBasicParsing
```

---

### 4. Edit User
**Endpoint:** `PUT /user-management/:userId`

**Description:** Edit user information including name, email, phone, role, etc.

**Path Parameters:**
- `userId` (required): User ID to update

**Request Body:**
```json
{
  "name": "Updated Name",
  "email": "newemail@example.com",
  "fullname": "Updated Fullname",
  "nickname": "NewNickname",
  "nomor_hp": "085987654321",
  "role": "ADMIN"
}
```

**Note:** All fields in the request body are optional. Only include fields you want to update.

**Response:**
```json
{
  "id": 1,
  "nama": "Updated Name",
  "username": "admin",
  "role": "ADMIN",
  "nomor_hp": "085987654321",
  "email": "newemail@example.com",
  "fullname": "Updated Fullname",
  "nickname": "NewNickname",
  "is_active": true,
  "expired_at": null,
  "fail_login": 0,
  "created_at": "2026-05-13T10:00:00.000Z",
  "updated_at": "2026-05-13T11:30:00.000Z"
}
```

**Example Request (cURL):**
```bash
curl -X PUT "http://localhost:7001/user-management/1" \
  -H "Authorization: Bearer {access_token}" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Updated Name",
    "email": "newemail@example.com",
    "nomor_hp": "085987654321",
    "role": "ADMIN"
  }'
```

---

### 5. Deactivate User
**Endpoint:** `POST /user-management/deactivate`

**Description:** Deactivate a user account.

**Request Body:**
```json
{
  "userId": 1
}
```

**Response:**
```json
{
  "message": "User deactivated successfully",
  "userId": 1,
  "is_active": false
}
```

**Example Request (cURL):**
```bash
curl -X POST "http://localhost:7001/user-management/deactivate" \
  -H "Authorization: Bearer {access_token}" \
  -H "Content-Type: application/json" \
  -d '{"userId": 1}'
```

---

### 6. Reset 2FA
**Endpoint:** `POST /user-management/reset-2fa`

**Description:** Reset/disable 2FA for a user. This clears the TOTP secret and backup codes.

**Request Body:**
```json
{
  "userId": 1
}
```

**Response:**
```json
{
  "message": "2FA reset successfully",
  "userId": 1,
  "twoFactorReset": true
}
```

**Example Request (cURL):**
```bash
curl -X POST "http://localhost:7001/user-management/reset-2fa" \
  -H "Authorization: Bearer {access_token}" \
  -H "Content-Type: application/json" \
  -d '{"userId": 1}'
```

---

## Error Responses

### 400 Bad Request
```json
{
  "message": "userId is required",
  "error": "Bad Request",
  "statusCode": 400
}
```

### 404 Not Found
```json
{
  "message": "User not found",
  "error": "Not Found",
  "statusCode": 404
}
```

### 401 Unauthorized
```json
{
  "message": "Unauthorized",
  "error": "Unauthorized",
  "statusCode": 401
}
```

---

## Available User Roles

The system supports the following roles:
- `SUPERADMIN`: Full system access
- `ADMIN`: Administrative access
- `USER`: Standard user access
- `GUEST`: Limited access

---

## User Status Fields

The user list response includes the following status fields:

- **is_active**: User account status (boolean)
- **fail_login**: Number of failed login attempts (integer)
- **expired_at**: Account expiration date (timestamp or null)

---

## Database Fields

The User Management system uses the following database entities:

### User Table
- `id`: Primary key (integer)
- `name`: User name (varchar)
- `username`: Username for login (varchar)
- `password`: Hashed password (varchar)
- `role`: User role enum
- `create_at`: Creation timestamp
- `update_at`: Update timestamp

### UserProfile Table
- `user_id`: Foreign key to User (varchar)
- `name`: Full name (varchar)
- `email_corporate`: Corporate email (varchar)
- `nomor_hp`: Phone number (varchar)
- `nik`: National ID (varchar)
- `direktorat`: Directorate (varchar)
- `divisi`: Division (varchar)
- `departemen`: Department (varchar)
- `created_at`: Creation timestamp
- `updated_at`: Update timestamp

### UserTwoFactor Table
- `user_id`: Foreign key to User (varchar)
- `secret`: TOTP secret (varchar, nullable)
- `backup_codes`: Backup codes (text array, nullable)
- `is_enabled`: 2FA enabled status (boolean)
- `device_name`: Device name (varchar, nullable)
- `created_at`: Creation timestamp
- `updated_at`: Update timestamp
- `enabled_at`: When 2FA was enabled (timestamp, nullable)

---

## Implementation Notes

1. **Default Password**: Reset password always uses `I7lBLi'7x7s`
2. **Search**: Search functionality is case-insensitive
3. **Pagination**: Default limit is 50, maximum recommended is 100
4. **2FA Reset**: Resets both TOTP secret and backup codes
5. **Field Updates**: Edit user endpoint updates both User and UserProfile tables
6. **Phone Number**: Stored in user_profile as `nomor_hp`

---

## Integration with Frontend

For the frontend UI displaying user management table with columns: Nama, Role, Nomor HP, Username, Action

### Example Table Display Code
```javascript
// Fetch users
async function loadUsers() {
  const token = localStorage.getItem('access_token');
  const response = await fetch('http://localhost:7001/user-management/list-users?take=50', {
    headers: {
      'Authorization': `Bearer ${token}`
    }
  });
  const data = await response.json();
  return data.data; // Array of users
}

// Reset password action
async function resetUserPassword(userId) {
  const token = localStorage.getItem('access_token');
  const response = await fetch('http://localhost:7001/user-management/reset-password', {
    method: 'POST',
    headers: {
      'Authorization': `Bearer ${token}`,
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({ userId })
  });
  const data = await response.json();
  console.log(`New password: ${data.newPassword}`); // Show to admin
  return data;
}

// Reset 2FA action
async function reset2FA(userId) {
  const token = localStorage.getItem('access_token');
  const response = await fetch('http://localhost:7001/user-management/reset-2fa', {
    method: 'POST',
    headers: {
      'Authorization': `Bearer ${token}`,
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({ userId })
  });
  return await response.json();
}
```

---

## Changelog

### Version 1.0.0 (2026-05-13)
- Initial release
- Endpoints: List users, search, reset password, edit user, deactivate, reset 2FA
- Default password support with `I7lBLi'7x7s`
- Integration with UserProfile and UserTwoFactor entities
