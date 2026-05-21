# Forgot Password Feature Documentation

## Overview
Forgot Password adalah fitur untuk user yang ingin mengganti password dari halaman login tanpa perlu login terlebih dahulu. Validasi dilakukan dengan NIK, email, dan reCAPTCHA, lalu user dapat memasukkan password baru dan login kembali.

## Endpoint Details

### URL
```http
POST /auth/forgotPassword
```

### Base URL
- **Development (localhost):** `http://localhost:7001`
- **Production (ngrok tunnel):** `https://subacetabular-jodee-literally.ngrok-free.dev`

## Request Body

### Required Fields
```json
{
  "nik": "string (required)",
  "email": "string (required)",
  "recaptchaToken": "string (required)",
  "password": "string (required)",
  "retypepassword": "string (required)"
}
```

### Field Descriptions
| Field | Type | Description | Example |
|---|---|---|---|
| `nik` | string | Nomor Induk Karyawan yang harus cocok dengan data `user_profile.nik` | `1234567890123456` |
| `email` | string | Email yang harus cocok dengan `user_profile.email_corporate` atau `user_profile.email_non_corporate` | `agnes@company.com` |
| `recaptchaToken` | string | Token reCAPTCHA untuk verifikasi | `test-token-dev` (development) |
| `password` | string | Password baru yang akan disimpan | `NewPass123!` |
| `retypepassword` | string | Konfirmasi password baru | `NewPass123!` |

## Validation Rules

### 1. reCAPTCHA Verification
- Token harus valid dan terverifikasi
- Dalam development mode, token `test-token-dev` bisa dipakai untuk testing
- Production menggunakan mekanisme reCAPTCHA yang aktif di backend

### 2. Identity Validation
- NIK harus cocok dengan data di tabel `user_profile`
- Email harus cocok dengan salah satu field berikut:
  - `user_profile.email_corporate`
  - `user_profile.email_non_corporate`
- Jika NIK dan email tidak cocok, request akan ditolak

### 3. Password Validation
- `password` dan `retypepassword` harus sama
- Password harus mengikuti aturan yang sama dengan endpoint change password:
  - 8 sampai 24 karakter
  - minimal 1 huruf besar
  - minimal 1 huruf kecil
  - minimal 1 angka
  - minimal 1 karakter spesial

## Response Examples

### Success Response
```json
{
  "message": "Password berhasil diubah, silakan login kembali",
  "success": true
}
```

**HTTP Status:** `200` atau `201`

### Error Responses

#### NIK dan Email Tidak Cocok
```json
{
  "message": "NIK dan email tidak sesuai",
  "error": "Bad Request",
  "statusCode": 400
}
```

#### Password Tidak Sama
```json
{
  "message": "Passwords do not match",
  "error": "Bad Request",
  "statusCode": 400
}
```

#### User Tidak Ditemukan
```json
{
  "message": "User not found",
  "error": "Bad Request",
  "statusCode": 400
}
```

#### reCAPTCHA Gagal
```json
{
  "message": "reCAPTCHA verification failed. Please try again.",
  "error": "Bad Request",
  "statusCode": 400
}
```

## Postman Testing Guide

### 1. Import Collection dan Environment
1. Import `POSTMAN_COLLECTION.json`
2. Import `POSTMAN_ENVIRONMENT.json`
3. Pilih environment **ONX Tenancy - Local**

### 2. Buka Request Forgot Password
1. Masuk ke folder **Auth & Users**
2. Pilih request **Forgot Password**
3. Pastikan method adalah `POST`
4. Pastikan URL mengarah ke `{{base_url}}/auth/forgotPassword`

### 3. Isi Body Request
Gunakan contoh body berikut:
```json
{
  "nik": "1234567890",
  "email": "user@company.com",
  "recaptchaToken": "test-token-dev",
  "password": "NewPass123!",
  "retypepassword": "NewPass123!"
}
```

### 4. Kirim Request
1. Klik **Send**
2. Jika data cocok, response akan mengembalikan `success: true`
3. Setelah itu, login ulang menggunakan password baru di endpoint **Login**

### 5. Tips Testing
- Endpoint ini tidak membutuhkan Bearer token karena digunakan dari halaman login
- Pastikan NIK dan email diambil dari data profile user yang benar
- Gunakan `test-token-dev` hanya untuk environment development

## Frontend Consumption Notes

### Flow yang Disarankan
1. User klik tombol **Forgot Password** di halaman login
2. User diarahkan ke halaman forgot password
3. User mengisi NIK dan email
4. User menyelesaikan reCAPTCHA
5. User mengisi password baru dan konfirmasi password
6. Frontend memanggil `POST /auth/forgotPassword`
7. Jika berhasil, tampilkan pesan sukses dan arahkan user ke halaman login

### Handling Success
- Tampilkan notifikasi bahwa password berhasil diubah
- Arahkan user kembali ke halaman login
- Jangan simpan password baru di frontend setelah request selesai

### Handling Error
- Tampilkan pesan error validasi sesuai response backend
- Jika NIK atau email tidak cocok, minta user cek ulang data profile
- Jika reCAPTCHA gagal, minta user mengulang verifikasi

## Sample cURL
```bash
curl -X POST "http://localhost:7001/auth/forgotPassword" \
  -H "Content-Type: application/json" \
  -d '{
    "nik": "1234567890",
    "email": "user@company.com",
    "recaptchaToken": "test-token-dev",
    "password": "NewPass123!",
    "retypepassword": "NewPass123!"
  }'
```
