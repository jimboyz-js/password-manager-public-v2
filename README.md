# 🔐 Password Manager v2.2.2

A secure **Password Manager web application** built with **HTML, CSS, JavaScript (frontend)** and **PHP, MySQL (backend)**.

Version **2.2.2** introduces **client-side encryption using the Web Crypto API** and a **web-based installer**, removing the need for manual `.env` configuration.  
Even if the database is compromised, stored credentials remain encrypted and unreadable.

## ✨ Features

### 🔑 Authentication

- Username & password login
- Email-based **Two-Factor Authentication (2FA)**
- Secure PHP session handling

### 🔐 Client-Side Encryption (NEW)

- Credentials are encrypted **in the browser** using the **Web Crypto API**
- Plaintext credentials are **never sent to the server**
- Database stores **only encrypted data** except date and other non-sensitive info.
- Protects user data even during database compromise

### 📂 Secure Storage

- Encrypted credentials stored in MySQL
<!-- - _Optional_ backend encryption layer for compatibility-->
- No hardcoded secrets

### 📧 Email (SMTP)

- 2FA verification via PHPMailer
- Supports Gmail App Passwords and custom SMTP providers

### ⚙️ Web-Based Installer (NEW)

No manual configuration required.

Installer steps:

1. Welcome
2. Server Requirements Check
3. SMTP Configuration
4. Database Configuration
5. Admin Account Creation
6. Install & Finalize

## 🛠️ Tech Stack

**Frontend**

- HTML, CSS, JavaScript
- Bootstrap
- Web Crypto API

**Backend**

- PHP
- MySQL

## 🔐 Security Model (Zero-Knowledge Inspired)

This application follows a **zero-knowledge–inspired design**:

- Sensitive credentials are encrypted **before leaving the browser**
- The server **never receives plaintext secrets**
- Encryption keys are derived and handled client-side
- The backend acts only as a **secure storage and transport layer**
- A database breach does **not** reveal usable credentials

> While not a formal zero-knowledge proof system, the architecture ensures the server cannot decrypt stored secrets.

## 🧪 Threat Model

### ✅ Defended Against

- **Database compromise** → Encrypted data only
- **Server-side data leaks** → No plaintext stored (sensitive)
- **Insider access to DB** → Encryption prevents disclosure
- **Credential reuse exposure** → Passwords never stored in plaintext

### ⚠️ Assumptions & Limitations

- Client device is trusted at time of use
- HTTPS is required to prevent MITM attacks
- XSS vulnerabilities can compromise encryption keys
- User must protect their email account (2FA dependency)

## 📖 Project History

- **v1.0** — Java Mobile (AIDE), hardcoded credentials
- **v1.5** — Java Desktop (.jar), console-based
- **v2.0** — PHP + MySQL, basic web UI
- **v2.1** — Improved UI + Email 2FA
- **v2.2.2** — Client-side encryption + Installer (Current)

## 🚀 Future Improvements

- Password generator
- Vault export/import (encrypted)
- Multi-device session control
- UI/UX enhancements
- Stronger zero-knowledge guarantees

## 🌐 Hosting

This project is hosted online.

## 📜 License

MIT License — free to use, modify, and distribute with attribution.

## 👨‍💻 Author

Developed by **jimBoYz Ni ChOy**  
📧 Contact: saronasda@gmail.com
