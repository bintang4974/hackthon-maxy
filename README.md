# 2FA dengan Google Authenticator - Documentation

## 📋 Integrasi 2FA (Two-Factor Authentication) dengan Google Authenticator

Sistem 2FA sudah terintegrasi dengan backend NestJS Anda. Berikut adalah panduan lengkap untuk setup dan testing.

---

## 🚀 Instalasi Dependencies

Jalankan perintah ini untuk install library yang diperlukan:

```bash
npm install speakeasy qrcode
npm install --save-dev @types/speakeasy
```

---

## 📊 Flow 2FA

```
1. User login dengan username, password, dan reCAPTCHA
2. System check apakah 2FA enabled
3. Jika enabled:
   - User harus provide OTP dari Google Authenticator
   - Verify OTP
   - Return JWT token
4. Jika belum enabled:
   - Return temporary token (untuk setup 2FA)
```

---

## 🔧 Testing di Postman

### **Step 1: Login (Get Temporary Token)**

```http
POST http://localhost:7001/auth/login
Content-Type: application/json

{
  "username": "omnix",
  "password": "admin123",
  "recaptchaToken": "test-token-dev"
}
```

Response:
```json
{
  "access_token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9..."
}
```

---

### **Step 2: Setup 2FA (Generate QR Code)**

Gunakan JWT token dari Step 1 di Authorization header.

```http
POST http://localhost:7001/auth/2fa/setup
Authorization: Bearer <JWT_TOKEN_DARI_STEP_1>
Content-Type: application/json
```

**Body:** (kosong atau `{}`)

Response:
```json
{
  "message": "Scan this QR code with Google Authenticator or Authy app",
  "qrCode": "data:image/png;base64,iVBORw0KGgoAAAANSUhE...",
  "secret": "JBSWY3DPEBLW64TMMQ",
  "backupCodes": [
    "A1B2C3D4",
    "E5F6G7H8",
    "I9J0K1L2",
    ...
  ],
  "instructions": "Verify OTP to enable 2FA. Save backup codes in a secure place."
}
```

---

### **Step 3: Scan QR Code di Google Authenticator**

1. **Download Google Authenticator** (iOS/Android)
2. **Buka app Google Authenticator**
3. **Tap "+" untuk tambah account**
4. **Pilih "Scan a QR code"**
5. **Scan QR code dari response Step 2**
6. **App akan show 6-digit code yang berubah setiap 30 detik**

---

### **Step 4: Verify 2FA (Enable 2FA)**

Ambil 6-digit code dari Google Authenticator dan verify:

```http
POST http://localhost:7001/auth/2fa/verify
Authorization: Bearer <JWT_TOKEN_DARI_STEP_1>
Content-Type: application/json

{
  "userId": "user-id-anda",
  "otp": "123456"
}
```

Response (Success):
```json
{
  "message": "2FA enabled successfully",
  "isEnabled": true,
  "backupCodes": [
    "A1B2C3D4",
    "E5F6G7H8",
    ...
  ]
}
```

---

### **Step 5: Check 2FA Status**

```http
GET http://localhost:7001/auth/2fa/status
Authorization: Bearer <JWT_TOKEN_DARI_STEP_1>
```

Response:
```json
{
  "isEnabled": true,
  "enabledAt": "2026-05-11T10:30:00.000Z",
  "deviceName": null
}
```

---

### **Step 6: Get New Backup Codes**

Jika ingin regenerate backup codes:

```http
POST http://localhost:7001/auth/2fa/backup-codes
Authorization: Bearer <JWT_TOKEN_DARI_STEP_1>
Content-Type: application/json

{
  "otp": "123456"
}
```

Response:
```json
{
  "message": "New backup codes generated",
  "backupCodes": [
    "A1B2C3D4",
    "E5F6G7H8",
    ...
  ]
}
```

---

### **Step 7: Disable 2FA**

```http
POST http://localhost:7001/auth/2fa/disable
Authorization: Bearer <JWT_TOKEN_DARI_STEP_1>
Content-Type: application/json

{
  "otp": "123456"
}
```

Response:
```json
{
  "message": "2FA disabled successfully",
  "isEnabled": false
}
```

---

## 📱 Menggunakan Backup Codes

Jika user tidak punya akses ke Google Authenticator (loss phone, etc):

1. Pada login screen, user bisa gunakan **backup code** sebagai pengganti OTP
2. Backup code format: `A1B2C3D4` (8 karakter)
3. Setelah digunakan, backup code itu invalid

---

## 🔐 Security Features

- ✓ OTP valid hanya 30 detik (Time-based OTP)
- ✓ Backup codes untuk emergency access
- ✓ Require OTP verification untuk disable 2FA
- ✓ Time window ±2 untuk account clock skew

---

## 📚 Library yang Digunakan

- **speakeasy** - Generate & verify TOTP tokens
- **qrcode** - Generate QR code untuk scanning

---

## 🛠️ Next Steps

1. **Update login endpoint** untuk require OTP jika 2FA enabled
2. **Update frontend** untuk show 2FA setup flow
3. **Add migration** untuk create `user_two_factor` table
4. **Test dengan real 2FA flow**
