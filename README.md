# Testing Guide - Account & Profile Module

Panduan lengkap untuk testing API module Account dan Profile di Postman.

---

## 📋 Prerequisites

- Postman installed
- Application running on `http://localhost:7001`
- Default user credentials:
  - **Username:** omnix
  - **Password:** admin123

---

## 🔐 Step 1: Login & Get JWT Token

### Endpoint
```
POST http://localhost:7001/auth/login
Content-Type: application/json
```

### Request Body
```json
{
  "username": "omnix",
  "password": "admin123",
  "recaptchaToken": "test-token-dev"
}
```

### Response
```json
{
  "access_token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c2VybmFtZSI6Im9tbml4IiwiaWQiOjEsImlhdCI6MTY4NDA2NzUwMCwiZXhwIjoxNjg0MTUzOTAwfQ.xyz..."
}
```

**💡 Simpan token ini untuk semua request berikutnya!**

---

## 👤 ACCOUNT Module Testing

### 1. Get My Account Info
```
GET http://localhost:7001/account/me
Authorization: Bearer <YOUR_JWT_TOKEN>
Content-Type: application/json
```

**Response:**
```json
{
  "id": 1,
  "username": "omnix",
  "name": "Omnix Admin",
  "role": "ADMIN",
  "twoFactorEnabled": false,
  "createdAt": "2025-11-15T10:30:00.000Z"
}
```

---

### 2. Get Account Security Info
```
GET http://localhost:7001/account/security
Authorization: Bearer <YOUR_JWT_TOKEN>
Content-Type: application/json
```

**Response:**
```json
{
  "id": 1,
  "username": "omnix",
  "name": "Omnix Admin",
  "role": "ADMIN",
  "lastPasswordChange": "2025-11-15T10:30:00.000Z",
  "twoFactorEnabled": false,
  "twoFactorEnabledAt": null
}
```

---

### 3. Change Password
```
POST http://localhost:7001/account/change-password
Authorization: Bearer <YOUR_JWT_TOKEN>
Content-Type: application/json
```

**Request Body:**
```json
{
  "oldPassword": "admin123",
  "newPassword": "newPassword123",
  "confirmPassword": "newPassword123"
}
```

**Response (Success):**
```json
{
  "message": "Password changed successfully"
}
```

**Response (Error - Old password incorrect):**
```json
{
  "statusCode": 401,
  "message": "Old password is incorrect"
}
```

**Response (Error - Password too short):**
```json
{
  "statusCode": 400,
  "message": "New password must be at least 8 characters long"
}
```

---

### 4. Get All Accounts (Admin)
```
GET http://localhost:7001/account/list/all?skip=0&take=50
Authorization: Bearer <YOUR_JWT_TOKEN>
Content-Type: application/json
```

**Response:**
```json
{
  "data": [
    {
      "id": 1,
      "username": "omnix",
      "name": "Omnix Admin",
      "role": "ADMIN",
      "twoFactorEnabled": false,
      "createdAt": "2025-11-15T10:30:00.000Z"
    },
    {
      "id": 2,
      "username": "user1",
      "name": "User One",
      "role": "REQUESTER",
      "twoFactorEnabled": true,
      "createdAt": "2025-11-15T11:00:00.000Z"
    }
  ],
  "total": 2
}
```

---

### 5. Search Accounts
```
GET http://localhost:7001/account/search/find?term=omnix&skip=0&take=50
Authorization: Bearer <YOUR_JWT_TOKEN>
Content-Type: application/json
```

**Response:**
```json
{
  "data": [
    {
      "id": 1,
      "username": "omnix",
      "name": "Omnix Admin",
      "role": "ADMIN",
      "twoFactorEnabled": false,
      "createdAt": "2025-11-15T10:30:00.000Z"
    }
  ],
  "total": 1
}
```

---

## 📝 PROFILE Module Testing

### 1. Create User Profile
```
POST http://localhost:7001/profile
Authorization: Bearer <YOUR_JWT_TOKEN>
Content-Type: application/json
```

**Request Body (All Optional):**
```json
{
  "name": "John Doe",
  "nik": "1234567890123456",
  "direktorat": "IT & Digital",
  "divisi": "Backend Engineering",
  "departemen": "API Development",
  "emailCorporate": "john.doe@company.com",
  "emailNonCorporate": "john.doe@gmail.com",
  "nomorHp": "081234567890"
}
```

**Response:**
```json
{
  "id": "550e8400-e29b-41d4-a716-446655440000",
  "userId": "1",
  "name": "John Doe",
  "nik": "1234567890123456",
  "direktorat": "IT & Digital",
  "divisi": "Backend Engineering",
  "departemen": "API Development",
  "emailCorporate": "john.doe@company.com",
  "emailNonCorporate": "john.doe@gmail.com",
  "nomorHp": "081234567890",
  "createdAt": "2025-11-15T15:30:00.000Z",
  "updatedAt": "2025-11-15T15:30:00.000Z"
}
```

---

### 2. Get My Profile
```
GET http://localhost:7001/profile/me
Authorization: Bearer <YOUR_JWT_TOKEN>
Content-Type: application/json
```

**Response:**
```json
{
  "id": "550e8400-e29b-41d4-a716-446655440000",
  "userId": "1",
  "name": "John Doe",
  "nik": "1234567890123456",
  "direktorat": "IT & Digital",
  "divisi": "Backend Engineering",
  "departemen": "API Development",
  "emailCorporate": "john.doe@company.com",
  "emailNonCorporate": "john.doe@gmail.com",
  "nomorHp": "081234567890",
  "createdAt": "2025-11-15T15:30:00.000Z",
  "updatedAt": "2025-11-15T15:30:00.000Z"
}
```

---

### 3. Get Other User Profile
```
GET http://localhost:7001/profile/2
Authorization: Bearer <YOUR_JWT_TOKEN>
Content-Type: application/json
```

(Replace `2` with actual userId)

---

### 4. Update My Profile
```
PUT http://localhost:7001/profile
Authorization: Bearer <YOUR_JWT_TOKEN>
Content-Type: application/json
```

**Request Body (Update specific fields):**
```json
{
  "name": "John Updated",
  "direktorat": "IT & Digital Updated",
  "nomorHp": "081234567899"
}
```

**Response:**
```json
{
  "id": "550e8400-e29b-41d4-a716-446655440000",
  "userId": "1",
  "name": "John Updated",
  "nik": "1234567890123456",
  "direktorat": "IT & Digital Updated",
  "divisi": "Backend Engineering",
  "departemen": "API Development",
  "emailCorporate": "john.doe@company.com",
  "emailNonCorporate": "john.doe@gmail.com",
  "nomorHp": "081234567899",
  "createdAt": "2025-11-15T15:30:00.000Z",
  "updatedAt": "2025-11-15T15:35:00.000Z"
}
```

---

### 5. Update Other User Profile
```
PUT http://localhost:7001/profile/2
Authorization: Bearer <YOUR_JWT_TOKEN>
Content-Type: application/json
```

(Replace `2` with actual userId)

---

### 6. Delete My Profile
```
DELETE http://localhost:7001/profile
Authorization: Bearer <YOUR_JWT_TOKEN>
Content-Type: application/json
```

**Response:**
```json
{
  "message": "Profile deleted successfully"
}
```

---

### 7. Delete Other User Profile
```
DELETE http://localhost:7001/profile/2
Authorization: Bearer <YOUR_JWT_TOKEN>
Content-Type: application/json
```

(Replace `2` with actual userId)

---

### 8. Get All Profiles
```
GET http://localhost:7001/profile/list/all?skip=0&take=50
Authorization: Bearer <YOUR_JWT_TOKEN>
Content-Type: application/json
```

**Response:**
```json
{
  "data": [
    {
      "id": "550e8400-e29b-41d4-a716-446655440000",
      "userId": "1",
      "name": "John Doe",
      "nik": "1234567890123456",
      "direktorat": "IT & Digital",
      "divisi": "Backend Engineering",
      "departemen": "API Development",
      "emailCorporate": "john.doe@company.com",
      "emailNonCorporate": "john.doe@gmail.com",
      "nomorHp": "081234567890",
      "createdAt": "2025-11-15T15:30:00.000Z",
      "updatedAt": "2025-11-15T15:30:00.000Z"
    }
  ],
  "total": 1
}
```

---

### 9. Search Profiles
```
GET http://localhost:7001/profile/search/find?term=john&skip=0&take=50
Authorization: Bearer <YOUR_JWT_TOKEN>
Content-Type: application/json
```

**Cari berdasarkan:**
- Name
- NIK
- Email Corporate
- User ID

**Response:**
```json
{
  "data": [
    {
      "id": "550e8400-e29b-41d4-a716-446655440000",
      "userId": "1",
      "name": "John Doe",
      "nik": "1234567890123456",
      "direktorat": "IT & Digital",
      "divisi": "Backend Engineering",
      "departemen": "API Development",
      "emailCorporate": "john.doe@company.com",
      "emailNonCorporate": "john.doe@gmail.com",
      "nomorHp": "081234567890",
      "createdAt": "2025-11-15T15:30:00.000Z",
      "updatedAt": "2025-11-15T15:30:00.000Z"
    }
  ],
  "total": 1
}
```

---

## ✅ Testing Checklist

### Account Module
- [ ] Login mendapat JWT token
- [ ] Get account info berhasil
- [ ] Get security info berhasil
- [ ] Change password berhasil (dengan password lama yang benar)
- [ ] Get all accounts berhasil
- [ ] Search accounts berhasil

### Profile Module
- [ ] Create profile berhasil
- [ ] Get my profile berhasil
- [ ] Get other user profile berhasil
- [ ] Update profile berhasil
- [ ] Delete profile berhasil
- [ ] Get all profiles dengan pagination berhasil
- [ ] Search profiles berhasil

---

## 🚨 Error Handling

### 400 Bad Request
```json
{
  "statusCode": 400,
  "message": "Error message here",
  "error": "Bad Request"
}
```

### 401 Unauthorized
```json
{
  "statusCode": 401,
  "message": "Unauthorized"
}
```

### 404 Not Found
```json
{
  "statusCode": 404,
  "message": "Resource not found"
}
```

---

## 📌 Tips

1. **Simpan JWT Token** - Gunakan environment variable di Postman untuk menyimpan token
2. **Pagination** - Gunakan `skip` dan `take` untuk mengontrol pagination
3. **Search** - Parameter `term` bersifat case-insensitive
4. **Validasi Email** - Email harus format yang valid
5. **Required Fields** - Semua fields profile bersifat optional untuk create/update

---

## 🔗 Related Endpoints

- **Auth:** `/auth/login`, `/auth/2fa/setup`, `/auth/2fa/verify`
- **Users:** `/users/*` (dari users module)
- **Account:** `/account/*` (user account management)
- **Profile:** `/profile/*` (user profile information)

---

Generated: May 11, 2026
Application Version: 2.0.0
