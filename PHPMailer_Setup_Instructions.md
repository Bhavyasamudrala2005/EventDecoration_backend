# PHPMailer Setup Instructions for Email OTP

## Step 1: Download PHPMailer

1. Go to: https://github.com/PHPMailer/PHPMailer/releases
2. Download the latest release (ZIP file)
3. Extract and copy the `src` folder to: `C:\xampp\htdocs\eventease\PHPMailer\src\`

Your folder structure should look like:
```
C:\xampp\htdocs\eventease\
├── PHPMailer\
│   └── src\
│       ├── Exception.php
│       ├── PHPMailer.php
│       └── SMTP.php
├── forgot_password.php
└── ... other files
```

## Step 2: Create Gmail App Password

1. Go to https://myaccount.google.com/security
2. Enable 2-Step Verification (if not already enabled)
3. Go to https://myaccount.google.com/apppasswords
4. Select "Mail" and "Windows Computer"
5. Click "Generate"
6. **Copy the 16-character password** (e.g., "abcd efgh ijkl mnop")

## Step 3: Update forgot_password.php

Edit `C:\xampp\htdocs\eventease\forgot_password.php` and update these lines:

```php
$mail->Username = 'YOUR_GMAIL@gmail.com';     // Your Gmail address
$mail->Password = 'xxxx xxxx xxxx xxxx';       // Your 16-char App Password
$mail->setFrom('YOUR_GMAIL@gmail.com', 'EventEase');
```

## Step 4: Test

1. Restart Apache in XAMPP
2. Try sending OTP from the app
3. Check your email inbox!
