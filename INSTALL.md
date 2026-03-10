# ⚙️ Password Manager v2.2.2 — Installation Guide

This version includes a **built-in web installer**.  
No `.env` file creation or manual configuration is required.

## ✅ Requirements

<!--- PHP 8.0+-->

- PHP 7.4.0+
- MySQL 5.7+ / MariaDB
- Composer
- Apache / Nginx or PHP Built-in Server
- Enabled PHP extensions:
  - openssl
  <!--- pdo_mysql-->
  - mysqli
  - mbstring
  - json

## 📥 Installation Steps

### 1. Clone the Repository

```bash
git clone https://github.com/jimboyz-js/password-manager-v2.2.2.git
cd password-manager-v2.2.2
```

### 2. Install Dependencies

```bash
composer install
```

### 3. ▶️ Running the App

Option A: PHP Built-in Server

```bash
php -S localhost:5000 -t public
```

Option B: XAMPP / WAMP / LAMP

- Place project in htdocs or www

- Start Apache and MySQL

- Open in browser:

```bash
http://localhost/password-manager
```

Adjust accordingly to match the actual project name.

### 4. 🧙 Installer Walkthrough

When you open the app for the first time, the installer starts automatically.

Installer Steps:

1. Welcome

2. Server Requirements Check

3. SMTP Configuration

   - Email address

   - SMTP host & port

   - App password (recommended)

4. Database Configuration

   - Host

   - Database name

   - Username

   - Password

5. Admin Account Setup

6. Install & Finish

<!--✔ Configuration files generated automatically-->

✔ Configuration `.env` file generated automatically

✔ Installer locks after completion

### 5. 🔒 Post-Installation Notes

- Delete or restrict access to the installer directory (if applicable)

- Always use HTTPS in production

- Use SMTP App Passwords, not real email passwords

- Regularly rotate credentials

#### ❗ Troubleshooting

---

- Ensure PHP extensions are enabled

- Check file permissions for config generation

- Verify SMTP credentials if emails fail
