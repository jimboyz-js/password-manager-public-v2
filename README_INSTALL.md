# 🔐 Password Manager v2.2.2

A secure Password Manager web application built with HTML, CSS, JavaScript (frontend) and PHP, MySQL (backend).

This version introduces frontend encryption using the Web Crypto API and a guided web-based installer, eliminating the need for manual `.env` configuration.
Even if the database is compromised, stored credentials remain encrypted and unreadable without the client-side key.

## ✨ Key Features

### 🔑 User Authentication

- Secure login with username & password

- Two-Factor Authentication (2FA) via email verification

- Session-based authentication using PHP

### 🔐 Client-Side Encryption (NEW in v2.2.2)

- Credentials are encrypted in the browser using the Web Crypto API

- Encrypted data is stored directly in the database

- The backend never sees plaintext credentials

- Protects sensitive data even if the database is compromised

### 📂 Secure Credential Storage

- Encrypted credentials stored in MySQL

- Legacy backend encryption remains configurable (for compatibility)

- Encryption keys are never hardcoded

### 📧 Email Integration

- Sends 2FA verification codes via PHPMailer (SMTP)

- Supports Gmail App Passwords and other SMTP providers

### ⚙️ Built-in Web Installer (NEW in v2.2.2)

No more manual setup.

The installer guides you through:

1. Welcome

2. Server Requirements Check

3. SMTP Configuration (for 2FA)

4. Database Configuration

5. Admin Account Creation

6. Install & Finalize Setup

After installation, configuration files are generated automatically.

## 🛠️ Tech Stack

Frontend

- HTML

- CSS

- JavaScript

- Bootstrap

- Web Crypto API

Backend

- PHP

- MySQL

## 📖 Project Motivation & History

I created this project because I manage many online accounts (Gmail and other social platforms) and wanted a secure, personal password manager that I fully control.

### Version History

v1.0 — Java Mobile (AIDE App)

- No database

- Credentials hardcoded

- Intentionally confusing naming (no Java conventions)

v1.5 — Java Desktop (.jar, Console UI)

- Console login and credential viewing

- Limited to one device (my laptop)

v2.0 — PHP + MySQL (Basic HTML/CSS)

- Web-based login

- Credentials stored in MySQL

- Basic UI

v2.1 — PHP + MySQL + Improved UI

- HTML table for credentials

- Email-based 2FA via SMTP

- Improved frontend experience

v2.2.2 — Current Version

- Frontend encryption using Web Crypto API

- Database stores only encrypted data

- Web-based installer (no manual .env setup)

- Improved security flow and setup experience

### 🧭 Visual Roadmap

```bash
v1.0 ──▶ Java Mobile (AIDE)
         - No DB
         - Hardcoded credentials

v1.5 ──▶ Java Desktop (.jar)
         - Console UI
         - Single-device limitation

v2.0 ──▶ PHP + MySQL
         - Web login
         - Basic UI

v2.1 ──▶ PHP + MySQL + 2FA
         - Improved UI
         - SMTP-based email verification

v2.2.2 ─▶ PHP + MySQL + Web Crypto API
         - Frontend encryption
         - Secure DB storage
         - Web-based installer
```

## ⚙️ Installation & Setup (v2.2.2)

1. Clone the Repository

```bash
   git clone https://github.com/jimboyz-js/password-manager-v2.2.2.git

   cd password-manager-v2.2.2
```

Adjust accordingly to match the actual directory name.

2. Install PHP Dependencies
   Make sure Composer is installed:

```bash
composer install
```

3. Run the Application

Option A: Using PHP Built-in Server

```bash
php -S localhost:5000 -t public
```

Then open:

```
http://localhost:5000
```

Option B: Using XAMPP / WAMP / LAMP

- Place the project in htdocs or www

- Start Apache and MySQL

- Open in browser:

```
http://localhost/password-manager
```

### 🧙 Installer Workflow (NEW)

On first run, the installer will automatically start.

Installer Steps:

1. Welcome

2. Server Requirements Check

   - PHP version

   - Required extensions

3. SMTP Configuration

   - Email provider

   - App password (recommended)

4. Database Configuration

   - Host

   - Database name

   - Username

   - Password

5. Admin Account Setup

6. Install & Finalize

✅ Configuration files are generated automatically
✅ No manual `.env` file creation required

### 🔒 Security Notes

- User login passwords are hashed using password_hash()

- Sensitive credentials are encrypted on the client-side

- Backend never receives plaintext secrets

- Use HTTPS in production

- Use SMTP App Passwords, not your main email password

- Regularly rotate encryption keys and SMTP credentials

### 🚀 Planned Improvements

- Password generator

- Vault export/import (encrypted)

- Session/device management

- UI/UX enhancements

- Optional zero-knowledge mode

### 🌐 Hosting

This project is hosted online.

### 📜 License

This project is licensed under the MIT License.
You are free to use, modify, and distribute it with attribution.

### 👨‍💻 Author

Developed by `jimBoYz Ni ChOy`

📧 Contact: saronasda@gmail.com
