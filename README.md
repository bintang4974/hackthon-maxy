# Account & Profile Module - Implementation Summary

## 📦 Module Overview

Saya telah membuat dua modul baru untuk aplikasi NestJS Anda:

### 1. **Profile Module** (`src/profile/`)
Mengelola informasi profile lengkap user dengan fields:
- Name
- NIK (Nomor Identitas Kependudukan)
- Direktorat
- Divisi
- Departemen
- Email Corporate
- Email Non Corporate
- Nomor HP

### 2. **Account Module** (`src/account/`)
Mengelola informasi account & security settings user:
- Account info (username, role, 2FA status)
- Account security (password changes, 2FA history)
- Change password functionality
- Account search & listing

---

## 🗂️ File Structure

### Profile Module
```
src/profile/
├── profile.module.ts           # Module definition
├── profile.controller.ts       # HTTP endpoints
├── profile.service.ts          # Business logic
└── dto/
    └── profile.dto.ts          # Data Transfer Objects
```

### Account Module
```
src/account/
├── account.module.ts           # Module definition
├── account.controller.ts       # HTTP endpoints
├── account.service.ts          # Business logic
└── dto/
    └── account.dto.ts          # Data Transfer Objects
```

### Database
```
src/database/
├── entities/
│   └── user_profile.entity.ts   # Database entity
└── migrations/
    └── CreateUserProfileTable.ts # Migration file
```

---

## 🔌 API Endpoints

### Account Module (`/account`)
```
GET    /account/me                    # Get own account info
GET    /account/security              # Get account security info
POST   /account/change-password       # Change password
GET    /account/list/all              # Get all accounts (with pagination)
GET    /account/search/find           # Search accounts by username/name
```

### Profile Module (`/profile`)
```
POST   /profile                       # Create profile for logged-in user
GET    /profile/me                    # Get own profile
GET    /profile/:userId               # Get other user profile
PUT    /profile                       # Update own profile
PUT    /profile/:userId               # Update other user profile
DELETE /profile                       # Delete own profile
DELETE /profile/:userId               # Delete other user profile
GET    /profile/list/all              # Get all profiles (with pagination)
GET    /profile/search/find           # Search profiles
```

---

## 🔐 Authentication & Authorization

Semua endpoints dilindungi dengan `JwtAuthGuard`:
- Memerlukan Bearer token di header `Authorization`
- User hanya bisa mengakses endpoint sesuai role mereka

```javascript
@UseGuards(JwtAuthGuard)
async getMyProfile(@Request() req): Promise<ProfileResponseDto> {
  return this.profileService.getProfileByUserId(req.user.id);
}
```

---

## 💾 Database Schema

### user_profile Table
```sql
CREATE TABLE `user_profile` (
  `id` varchar(36) PRIMARY KEY,                    -- UUID
  `userId` varchar(255) UNIQUE NOT NULL,          -- Link to user
  `name` varchar(255) NULL,                       -- Full name
  `nik` varchar(20) NULL,                         -- National ID
  `direktorat` varchar(255) NULL,                 -- Directorate
  `divisi` varchar(255) NULL,                     -- Division
  `departemen` varchar(255) NULL,                 -- Department
  `emailCorporate` varchar(255) NULL,             -- Corporate email
  `emailNonCorporate` varchar(255) NULL,          -- Personal email
  `nomorHp` varchar(20) NULL,                     -- Phone number
  `createdAt` datetime DEFAULT CURRENT_TIMESTAMP,
  `updatedAt` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `IDX_user_profile_userId` (`userId`)
);
```

---

## 🚀 Integration Points

### 1. Connected to Auth Module
- Account endpoints show 2FA status
- Both modules work together for authentication

### 2. Connected to User Module
- Profile links to user via userId
- Account module uses User entity for account info

### 3. Connected to 2FA System
- Account security endpoint shows 2FA enabled status
- Shows when 2FA was enabled

---

## 📋 Key Features

### Profile Service
✅ CRUD operations (Create, Read, Update, Delete)
✅ Search by name, NIK, email
✅ Pagination support
✅ Validation for duplicate profiles per user
✅ Partial updates supported

### Account Service
✅ Get account information
✅ Get security information
✅ Change password with validation
✅ Search accounts
✅ Listing with pagination
✅ Password strength validation (min 8 chars)
✅ Old password verification
✅ Prevent using same password

---

## 🔄 Request/Response Flow

### Create Profile Flow
```
1. User sends POST /profile dengan profile data
2. Controller receives request dengan JWT token
3. JwtAuthGuard validates token
4. ProfileService checks if profile already exists
5. Create new profile record di database
6. Return created profile dengan ID
```

### Change Password Flow
```
1. User sends POST /account/change-password dengan old & new password
2. Controller verifies JWT token
3. AccountService loads user dari database
4. Verify old password dengan bcrypt
5. Hash new password
6. Update user password di database
7. Return success message
```

---

## 📊 Database Relationship

```
User Entity (user table)
    ↓
    └── Has One ──→ UserProfile Entity
    └── Has One ──→ UserTwoFactor Entity
```

---

## 🛡️ Security Considerations

1. **Password Hashing** - Passwords di-hash menggunakan bcryptjs
2. **JWT Protection** - Semua endpoint memerlukan valid JWT token
3. **Validation** - Input data di-validate menggunakan class-validator
4. **Unique Constraints** - Setiap user hanya bisa punya satu profile
5. **Error Messages** - Error messages tidak mengungkap detail database

---

## 🧪 Testing

Gunakan file **ACCOUNT_PROFILE_TESTING_GUIDE.md** untuk:
- Contoh request/response
- Step-by-step testing procedures
- Error scenarios
- Postman collection examples

---

## 🔧 Modules Registered

Update ke **app.module.ts**:
```typescript
imports: [
  // ... existing modules
  ProfileModule,
  AccountModule,
]
```

---

## 📝 Migration Info

Migration file: `CreateUserProfileTable1684000000001`
Status: ✅ Executed successfully
Created table: `user_profile`

---

## 🎯 Next Steps

1. **Test di Postman** - Gunakan ACCOUNT_PROFILE_TESTING_GUIDE.md
2. **Integrate ke Frontend** - Implement UI untuk profile management
3. **Add Validation Rules** - Tambahkan business logic sesuai kebutuhan
4. **Add Logging** - Track account & profile changes
5. **Add Audit Trail** - Catat siapa yang mengubah apa dan kapan

---

## 📞 Support

Untuk pertanyaan atau modifikasi:
- Lihat kode di `src/profile/` dan `src/account/`
- Modifikasi DTOs untuk requirements tambahan
- Extend services untuk business logic lebih kompleks

---

Generated: May 11, 2026
Application: onx-tenant v2.0.0
Status: ✅ Ready for Testing
