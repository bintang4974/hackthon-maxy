# Frontend Integration Guide - 2FA dengan Google Authenticator

## 📱 Implementasi Frontend untuk 2FA

### **Setup di HTML**

Pastikan load Google Authenticator library:

```html
<!DOCTYPE html>
<html>
<head>
  <script src="https://www.google.com/recaptcha/api.js"></script>
</head>
<body>
  <!-- Your HTML here -->
</body>
</html>
```

---

## 🔄 Flow Frontend

```
Login Form
    ↓
Enter Username, Password
    ↓
Get reCAPTCHA Token
    ↓
Submit Login Request
    ↓
Check if 2FA is Enabled
    ↓
If YES: Show OTP Input Screen
    ↓
Enter 6-digit OTP from Google Authenticator
    ↓
Verify OTP
    ↓
Success: Get JWT Token
```

---

## 📝 Step-by-Step Frontend Implementation

### **1. Login Form Component**

```html
<form id="loginForm">
  <input type="text" id="username" placeholder="Username" required>
  <input type="password" id="password" placeholder="Password" required>
  
  <div id="otpContainer" style="display:none;">
    <input type="text" id="otp" placeholder="Enter 6-digit OTP" maxlength="6" pattern="[0-9]{6}">
  </div>

  <button type="submit">Login</button>
</form>

<div id="qrContainer" style="display:none;">
  <p>Scan this QR code dengan Google Authenticator</p>
  <img id="qrCode" src="">
  <button id="confirmBtn">Confirm Setup 2FA</button>
</div>
```

---

### **2. Step 1: Login dengan reCAPTCHA**

```javascript
async function handleLogin(event) {
  event.preventDefault();
  
  const username = document.getElementById('username').value;
  const password = document.getElementById('password').value;
  
  // Generate reCAPTCHA token
  const recaptchaToken = await generateRecaptchaToken();
  
  try {
    const response = await fetch('/auth/login', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({
        username,
        password,
        recaptchaToken,
      }),
    });

    if (response.status === 401) {
      alert('Invalid username or password');
      return;
    }

    const data = await response.json();
    
    // Save temporary token untuk setup 2FA (jika diperlukan)
    localStorage.setItem('temp_token', data.access_token);
    
    // Check if 2FA is enabled
    const twoFAStatus = await check2FAStatus(data.access_token);
    
    if (!twoFAStatus.isEnabled) {
      // Offer to setup 2FA
      showSetup2FAOption();
    } else {
      // User sudah punya 2FA, minta OTP
      document.getElementById('otpContainer').style.display = 'block';
    }
  } catch (error) {
    console.error('Login error:', error);
    alert('Login failed');
  }
}

document.getElementById('loginForm').addEventListener('submit', handleLogin);
```

---

### **3. Generate reCAPTCHA Token**

```javascript
async function generateRecaptchaToken() {
  return new Promise((resolve) => {
    grecaptcha.ready(function() {
      grecaptcha.execute('YOUR_SITE_KEY_HERE', { action: 'login' }).then(function(token) {
        resolve(token);
      });
    });
  });
}
```

---

### **4. Check 2FA Status**

```javascript
async function check2FAStatus(token) {
  const response = await fetch('/auth/2fa/status', {
    method: 'GET',
    headers: {
      'Authorization': `Bearer ${token}`,
    },
  });

  if (response.ok) {
    return await response.json();
  }
  
  return { isEnabled: false };
}
```

---

### **5. Setup 2FA Flow**

```javascript
async function setup2FA() {
  const token = localStorage.getItem('temp_token');
  
  try {
    const response = await fetch('/auth/2fa/setup', {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${token}`,
      },
    });

    const data = await response.json();
    
    // Show QR code
    document.getElementById('qrCode').src = data.qrCode;
    document.getElementById('qrContainer').style.display = 'block';
    
    // Save secret & backup codes untuk display
    localStorage.setItem('2fa_secret', data.secret);
    localStorage.setItem('2fa_backup_codes', JSON.stringify(data.backupCodes));
    
    // Show backup codes
    console.log('Backup Codes:', data.backupCodes);
    
  } catch (error) {
    console.error('Setup 2FA error:', error);
    alert('Failed to setup 2FA');
  }
}
```

---

### **6. Verify 2FA Setup**

```javascript
async function verify2FASetup() {
  const token = localStorage.getItem('temp_token');
  const otp = document.getElementById('otp').value;
  const userId = 'user-id-anda'; // Get from user object
  
  try {
    const response = await fetch('/auth/2fa/verify', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${token}`,
      },
      body: JSON.stringify({
        userId,
        otp,
      }),
    });

    const data = await response.json();
    
    if (response.ok) {
      alert('2FA enabled successfully!');
      alert('Backup codes:\n' + data.backupCodes.join('\n'));
      // Redirect to dashboard
      window.location.href = '/dashboard';
    } else {
      alert('Invalid OTP');
    }
  } catch (error) {
    console.error('Verify 2FA error:', error);
  }
}
```

---

### **7. Login dengan OTP (Setelah 2FA Enabled)**

```javascript
async function loginWith2FA(event) {
  event.preventDefault();
  
  const username = document.getElementById('username').value;
  const password = document.getElementById('password').value;
  const otp = document.getElementById('otp').value;
  const recaptchaToken = await generateRecaptchaToken();
  
  try {
    const response = await fetch('/auth/login', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({
        username,
        password,
        recaptchaToken,
        twoFactorCode: otp, // OTP dari Google Authenticator
      }),
    });

    const data = await response.json();
    
    if (response.ok) {
      // Save JWT token
      localStorage.setItem('auth_token', data.access_token);
      // Redirect to dashboard
      window.location.href = '/dashboard';
    } else {
      alert('Invalid OTP or credentials');
    }
  } catch (error) {
    console.error('Login error:', error);
  }
}
```

---

## 🎨 UI Components Recommendations

### **Login Form:**
```
┌─────────────────────────┐
│  Login                  │
├─────────────────────────┤
│ Username: [_________]   │
│ Password: [_________]   │
│ [    Login Button    ]  │
└─────────────────────────┘
```

### **2FA Setup Form:**
```
┌─────────────────────────┐
│  Setup 2FA              │
├─────────────────────────┤
│ Scan QR Code:           │
│  [  QR Code Image  ]    │
│                         │
│ Or enter secret:        │
│ JBSWY3DPEBLW64TMMQ     │
│                         │
│ [  Setup Button  ]      │
└─────────────────────────┘
```

### **Verify OTP Form:**
```
┌─────────────────────────┐
│  Enter OTP              │
├─────────────────────────┤
│ From your app:          │
│ [__ __ __ __ __ __]     │
│                         │
│ [ Verify ]  [ Cancel ]  │
└─────────────────────────┘
```

---

## 🔐 Security Best Practices

1. **Always use HTTPS** untuk komunikasi
2. **Never store secret** di local storage - generate baru setiap setup
3. **Clear tokens** dari localStorage setelah logout
4. **Validate OTP** client-side sebelum submit (6 digits)
5. **Show backup codes** hanya sekali saat setup

---

## 🧪 Testing Checklist

- [ ] Login flow berjalan normal
- [ ] reCAPTCHA token berhasil digenerate
- [ ] 2FA setup menampilkan QR code
- [ ] Bisa scan QR code dengan Google Authenticator
- [ ] OTP verification bekerja
- [ ] Backup codes bisa di-copy dan disimpan
- [ ] Login dengan OTP berjalan lancar
- [ ] Disable 2FA berjalan
- [ ] Token JWT disimpan dengan aman

---

## 📚 Useful Libraries

- **qrcode.react** - React component untuk display QR code
- **react-otp-input** - Component untuk OTP input
- **speakeasy** (jika perlu generate token di client)

---

## 🚀 Next Steps

1. Implement form validation
2. Add loading states
3. Add error handling
4. Implement remember device feature
5. Add backup codes display
