# 🧪 Testing 2FA di Postman - Step by Step

## ⚠️ IMPORTANT: Install Dependencies Terlebih Dahulu!

Sebelum testing, pastikan install library yang diperlukan:

```bash
npm install speakeasy qrcode
npm install --save-dev @types/speakeasy
```

Tunggu sampai selesai. Ini REQUIRED untuk 2FA bekerja!

---

## 🚀 Step 1: Start Server

```bash
npm run start:dev
```

Tunggu sampai muncul output:
```
[Nest] 12345 - 05/11/2026, 10:30:02 AM     LOG [InstanceLoader] AuthModule dependencies initialized
[Nest] 12345 - 05/11/2026, 10:30:02 AM     LOG Nest application successfully started on port 7001
```

---

## 📝 Step 2: Testing Endpoints di Postman

### **Endpoint 1: Get Mock reCAPTCHA Token**

**Info:**
- Endpoint ini generate mock token untuk testing tanpa perlu real reCAPTCHA

**Request:**
```
POST http://localhost:7001/auth/recaptcha/mock-token
Content-Type: application/json
```

**Body:** (kosong atau biarkan saja)

**Expected Response:**
```json
{
  "mockToken": "test-token-dev",
  "message": "Use this token for testing in Postman",
  "expiresIn": "Never (for testing)"
}
```

✅ **Status: 200 OK**

---

### **Endpoint 2: Login dengan reCAPTCHA**

**Info:**
- Login biasa, gunakan mock token dari Endpoint 1
- Jika berhasil, dapat JWT token untuk step berikutnya

**Request:**
```
POST http://localhost:7001/auth/login
Content-Type: application/json
```

**Body:**
```json
{
  "username": "omnix",
  "password": "admin123",
  "recaptchaToken": "test-token-dev"
}
```

**Expected Response:**
```json
{
  "access_token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpZCI6MSwiaWF0IjoxNzE1NDAxNDAwLCJleHAiOjE3MTU0ODc4MDB9.xxxxx"
}
```

✅ **Status: 200 OK**

**💾 SAVE TOKEN INI!** - Copy `access_token` dan simpan untuk endpoint berikutnya

---

### **Endpoint 3: Setup 2FA (Generate QR Code)**

**Info:**
- Setup 2FA awal
- Generate secret dan QR code untuk Google Authenticator
- Response berisi backup codes

**Request:**
```
POST http://localhost:7001/auth/2fa/setup
Content-Type: application/json
Authorization: Bearer <PASTE_JWT_TOKEN_DARI_ENDPOINT_2>
```

**Body:** (kosong, biarkan blank)

**Headers:**
```
Authorization: Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpZCI6MSwiaWF0IjoxNzE1NDAxNDAwLCJleHAiOjE3MTU0ODc4MDB9.xxxxx
```

**Expected Response:**
```json
{
  "message": "Scan this QR code with Google Authenticator or Authy app",
  "qrCode": "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAWgAAAFoCAYAAAB65JSMAA...",
  "secret": "JBSWY3DPEBLW64TMMQ",
  "backupCodes": [
    "A1B2C3D4",
    "E5F6G7H8",
    "I9J0K1L2",
    "M3N4O5P6",
    "Q7R8S9T0",
    "U1V2W3X4",
    "Y5Z6A7B8",
    "C9D0E1F2",
    "G3H4I5J6",
    "K7L8M9N0"
  ],
  "instructions": "Verify OTP to enable 2FA. Save backup codes in a secure place."
}
```

✅ **Status: 200 OK**

**💾 SIMPAN:**
- Secret: `JBSWY3DPEBLW64TMMQ`
- Backup Codes (simpan di tempat aman!)

---

### **Step Penting: Scan QR Code dengan Google Authenticator**

1. **Download app:**
   - iOS: App Store → cari "Google Authenticator"
   - Android: Play Store → cari "Google Authenticator"

2. **Buka app Google Authenticator**

3. **Tap tombol "+" (tambah akun baru)**

4. **Pilih "Scan a QR code"**

5. **Copy base64 QR code dari response** atau generate manual:
   - Jika ingin generate QR code di browser, gunakan online tool:
   - https://www.the-qrcode-generator.com/
   - Paste secret: `JBSWY3DPEBLW64TMMQ`

6. **Scan dengan Authenticator app**

7. **App akan show 6-digit code yang berubah setiap 30 detik**

   Contoh: `123456` (berubah setiap 30 detik)

---

### **Endpoint 4: Verify 2FA (Enable 2FA dengan OTP)**

**Info:**
- Verify OTP untuk enable 2FA
- Ambil 6-digit code dari Google Authenticator

**Request:**
```
POST http://localhost:7001/auth/2fa/verify
Content-Type: application/json
Authorization: Bearer <JWT_TOKEN>
```

**Body:**
```json
{
  "userId": "1",
  "otp": "123456"
}
```

⚠️ **PENTING:** Gunakan OTP yang muncul di Google Authenticator app saat itu juga (valid 30 detik)

**Expected Response (Jika Benar):**
```json
{
  "message": "2FA enabled successfully",
  "isEnabled": true,
  "backupCodes": [
    "A1B2C3D4",
    "E5F6G7H8",
    "I9J0K1L2",
    "M3N4O5P6",
    "Q7R8S9T0",
    "U1V2W3X4",
    "Y5Z6A7B8",
    "C9D0E1F2",
    "G3H4I5J6",
    "K7L8M9N0"
  ]
}
```

✅ **Status: 200 OK**

**Expected Response (Jika Salah):**
```json
{
  "message": "Invalid OTP code",
  "error": "Bad Request",
  "statusCode": 400
}
```

❌ **Status: 400 Bad Request**

---

### **Endpoint 5: Check 2FA Status**

**Info:**
- Check apakah 2FA sudah enabled atau belum

**Request:**
```
GET http://localhost:7001/auth/2fa/status
Authorization: Bearer <JWT_TOKEN>
```

**Expected Response:**
```json
{
  "isEnabled": true,
  "enabledAt": "2026-05-11T10:30:00.000Z",
  "deviceName": null
}
```

✅ **Status: 200 OK**

---

### **Endpoint 6: Get Backup Codes (Generate New)**

**Info:**
- Generate backup codes baru
- Require OTP verification

**Request:**
```
POST http://localhost:7001/auth/2fa/backup-codes
Content-Type: application/json
Authorization: Bearer <JWT_TOKEN>
```

**Body:**
```json
{
  "otp": "123456"
}
```

**Expected Response:**
```json
{
  "message": "New backup codes generated",
  "backupCodes": [
    "NEW1A2B3",
    "NEW2C4D5",
    "NEW3E6F7",
    ...
  ]
}
```

✅ **Status: 200 OK**

---

### **Endpoint 7: Disable 2FA**

**Info:**
- Disable 2FA dengan verify OTP terlebih dahulu

**Request:**
```
POST http://localhost:7001/auth/2fa/disable
Content-Type: application/json
Authorization: Bearer <JWT_TOKEN>
```

**Body:**
```json
{
  "otp": "123456"
}
```

**Expected Response:**
```json
{
  "message": "2FA disabled successfully",
  "isEnabled": false
}
```

✅ **Status: 200 OK**

---

## 📊 Complete Testing Checklist

| # | Endpoint | Method | Status | Notes |
|---|----------|--------|--------|-------|
| 1 | `/auth/recaptcha/mock-token` | POST | ✅ | Get mock token |
| 2 | `/auth/login` | POST | ✅ | Login dengan reCAPTCHA |
| 3 | `/auth/2fa/setup` | POST | ✅ | Generate QR code & secret |
| 4 | *Scan QR di Authenticator* | - | ✅ | Manual step |
| 5 | `/auth/2fa/verify` | POST | ✅ | Enable 2FA dengan OTP |
| 6 | `/auth/2fa/status` | GET | ✅ | Check status |
| 7 | `/auth/2fa/backup-codes` | POST | ✅ | Generate new backup codes |
| 8 | `/auth/2fa/disable` | POST | ✅ | Disable 2FA |

---

## 🔴 Troubleshooting

### **Error: "Cannot find module 'speakeasy'"**
```bash
npm install speakeasy qrcode
npm run build
npm run start:dev
```

### **Error: "Invalid OTP code"**
- Pastikan OTP dari Google Authenticator valid (tidak expired)
- OTP hanya valid 30 detik
- Coba gunakan code yang baru muncul di app

### **Error: "No 2FA setup found"**
- Setup 2FA dulu dengan `/auth/2fa/setup`
- Pastikan sudah scan QR code

### **Error: "2FA is not enabled"**
- Endpoint ini hanya bisa digunakan setelah 2FA enabled
- Verify OTP dulu dengan `/auth/2fa/verify`

### **Error: "socket_hang_up"**
- Server belum jalan
- Jalankan `npm run start:dev`

---

## 💡 Tips Postman

### **1. Save JWT Token sebagai Variable**

Di Response dari `/auth/login`:

```javascript
// Tests tab
var jsonData = pm.response.json();
pm.environment.set("jwt_token", jsonData.access_token);
```

Kemudian di Authorization header, gunakan:
```
Bearer {{jwt_token}}
```

### **2. Format QR Code dari Response**

Jika ingin lihat QR code yang di-generate:

```javascript
// Copy response qrCode field
// Paste di browser: data:image/png;base64,iVBOR...
// Atau gunakan online viewer: https://www.qr-code-generator.com/
```

### **3. Test Multiple Times**

Setiap setup 2FA generate secret baru, jadi bisa test berkali-kali tanpa conflict.

---

## 🎯 Expected Full Flow

```
1. POST /auth/recaptcha/mock-token
   Response: { mockToken: "test-token-dev" }
   
2. POST /auth/login
   Body: { username, password, recaptchaToken }
   Response: { access_token: "JWT_TOKEN" }
   
3. POST /auth/2fa/setup
   Headers: Authorization Bearer JWT_TOKEN
   Response: { qrCode, secret, backupCodes }
   
4. [MANUAL] Scan QR code dengan Google Authenticator
   Google Authenticator shows: 6-digit OTP
   
5. POST /auth/2fa/verify
   Body: { userId, otp: "123456" }
   Response: { isEnabled: true, backupCodes }
   
6. GET /auth/2fa/status
   Response: { isEnabled: true }
   
7. POST /auth/2fa/backup-codes
   Body: { otp: "123456" }
   Response: { newBackupCodes }
   
8. POST /auth/2fa/disable
   Body: { otp: "123456" }
   Response: { isEnabled: false }
```

---

## ✅ Ready to Test?

1. ✅ Install dependencies: `npm install speakeasy qrcode`
2. ✅ Start server: `npm run start:dev`
3. ✅ Open Postman
4. ✅ Follow step-by-step di atas
5. ✅ Scan QR code dengan Google Authenticator
6. ✅ Test setiap endpoint

Siap? Let's go! 🚀
