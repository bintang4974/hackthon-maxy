# 2FA Conditional Login Testing Guide

## Overview
The login endpoint now returns 2FA status information to guide the frontend on what action to take next:
- **If 2FA not setup**: Returns QR code, secret, and backup codes
- **If 2FA setup but not verified**: Returns message to verify OTP
- **If 2FA fully enabled**: Returns normal JWT token

---

## Test Scenario 1: First Time Login (2FA Not Setup)

### Request
```bash
curl -X POST http://localhost:3000/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "username": "admin",
    "password": "admin123",
    "recaptchaToken": "test-token-dev"
  }'
```

### Expected Response (Status: 200)
```json
{
  "access_token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
  "two_factor_status": {
    "is_enabled": false,
    "requires_setup": true,
    "message": "Please setup 2FA",
    "qrCode": "data:image/png;base64,...",
    "secret": "JBSWY3DPEBLW64TMMQ...",
    "backup_codes": ["ABC123", "DEF456", ...],
    "next_action": "/auth/2fa/setup"
  }
}
```

**Frontend Action**: Show QR code and ask user to scan with Google Authenticator/Authy

---

## Test Scenario 2: Setup 2FA with OTP Verification

### Step 1: Setup 2FA (Get QR Code)
```bash
curl -X POST http://localhost:3000/auth/2fa/setup \
  -H "Authorization: Bearer {access_token}" \
  -H "Content-Type: application/json"
```

### Response
```json
{
  "message": "Scan this QR code with Google Authenticator or Authy app",
  "qrCode": "data:image/png;base64,...",
  "secret": "JBSWY3DPEBLW64TMMQ...",
  "backup_codes": ["ABC123", "DEF456", ...],
  "instructions": "Verify OTP to enable 2FA. Save backup codes in a secure place."
}
```

### Step 2: Check 2FA Status Before Verification
```bash
curl -X GET http://localhost:3000/auth/2fa/check-status \
  -H "Authorization: Bearer {access_token}"
```

### Response
```json
{
  "status": "verify_needed",
  "is_enabled": false,
  "message": "2FA setup in progress. Please verify OTP to complete setup.",
  "next_action": "POST /auth/2fa/verify",
  "requires_action": true
}
```

**Frontend Action**: Show OTP input field and ask user to enter code from authenticator

### Step 3: Verify OTP Code
```bash
curl -X POST http://localhost:3000/auth/2fa/verify \
  -H "Authorization: Bearer {access_token}" \
  -H "Content-Type: application/json" \
  -d '{
    "otp": "123456"
  }'
```

### Response
```json
{
  "message": "2FA enabled successfully",
  "is_enabled": true,
  "backup_codes": ["ABC123", "DEF456", ...]
}
```

**Frontend Action**: Show success message and save backup codes

---

## Test Scenario 3: Login with 2FA Already Enabled

### Request
```bash
curl -X POST http://localhost:3000/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "username": "admin",
    "password": "admin123",
    "recaptchaToken": "test-token-dev"
  }'
```

### Expected Response (Status: 200)
```json
{
  "access_token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
  "two_factor_status": {
    "is_enabled": true,
    "requires_setup": false,
    "message": "Please verify your 2FA OTP code",
    "next_action": "/auth/2fa/verify"
  }
}
```

**Frontend Action**: Redirect to OTP verification screen

---

## Test Scenario 4: Check 2FA Status After Full Setup

### Request
```bash
curl -X GET http://localhost:3000/auth/2fa/check-status \
  -H "Authorization: Bearer {access_token}"
```

### Response
```json
{
  "status": "already_enabled",
  "is_enabled": true,
  "message": "2FA is already enabled and active.",
  "enabled_at": "2026-05-12T05:35:00.000Z",
  "device_name": "Unknown Device",
  "next_action": "No action needed",
  "requires_action": false
}
```

---

## Test Scenario 5: Disable 2FA

### Request
```bash
curl -X POST http://localhost:3000/auth/2fa/disable \
  -H "Authorization: Bearer {access_token}" \
  -H "Content-Type: application/json" \
  -d '{
    "otp": "123456"
  }'
```

### Response
```json
{
  "message": "2FA disabled successfully",
  "is_enabled": false
}
```

---

## Test Scenario 6: Generate New Backup Codes

### Request
```bash
curl -X POST http://localhost:3000/auth/2fa/backup-codes \
  -H "Authorization: Bearer {access_token}" \
  -H "Content-Type: application/json" \
  -d '{
    "otp": "123456"
  }'
```

### Response
```json
{
  "message": "New backup codes generated",
  "backup_codes": ["XYZ123", "UVW456", ...]
}
```

---

## Postman Setup Instructions

### 1. Create Variables in Postman
- Variable: `base_url` = `http://localhost:3000`
- Variable: `access_token` = (populated after login)
- Variable: `otp_code` = (populate with 6-digit code from authenticator)

### 2. Create Request Collection

#### Request 1: Login - First Time
- **Method**: POST
- **URL**: `{{base_url}}/auth/login`
- **Body**:
```json
{
  "username": "admin",
  "password": "admin123",
  "recaptchaToken": "test-token-dev"
}
```
- **Tests**: 
```javascript
var jsonData = pm.response.json();
pm.environment.set("access_token", jsonData.access_token);
pm.test("Login returns 2FA status", function() {
    pm.expect(jsonData.two_factor_status).to.exist;
});
```

#### Request 2: Check 2FA Status
- **Method**: GET
- **URL**: `{{base_url}}/auth/2fa/check-status`
- **Headers**: `Authorization: Bearer {{access_token}}`

#### Request 3: Setup 2FA
- **Method**: POST
- **URL**: `{{base_url}}/auth/2fa/setup`
- **Headers**: `Authorization: Bearer {{access_token}}`

#### Request 4: Verify OTP
- **Method**: POST
- **URL**: `{{base_url}}/auth/2fa/verify`
- **Headers**: `Authorization: Bearer {{access_token}}`
- **Body**:
```json
{
  "otp": "{{otp_code}}"
}
```

---

## Notes for Testing

1. **Getting OTP Codes**: Use Google Authenticator or Authy app
   - Scan the QR code provided in setup response
   - App will generate 6-digit codes that refresh every 30 seconds

2. **Using Backup Codes**: If you lose access to authenticator
   - Keep backup codes in a safe place
   - Can be used instead of OTP to disable 2FA

3. **Testing Without App**: 
   - Scan QR code in browser using a QR code scanner
   - Manually create a TOTP secret using the `secret` value
   - Use online TOTP generators for testing (speakeasy lib uses standard TOTP)

4. **Database State**:
   - Each user has exactly one `user_two_factor` record
   - Fields are stored in snake_case: `user_id`, `is_enabled`, `secret`, `backup_codes`
   - `enabled_at` field records when 2FA was activated

---

## Success Criteria

✅ Login without 2FA shows "requires_setup: true" with QR code
✅ Setup generates valid OTP secret and backup codes
✅ Check status shows "verify_needed" after setup but before verification
✅ Verify with correct OTP enables 2FA ("is_enabled: true")
✅ Check status shows "already_enabled" after full setup
✅ Login with 2FA enabled returns "requires_setup: false"
✅ Can disable 2FA with correct OTP
✅ Can generate new backup codes

