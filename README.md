
# Shobee

![Laravel](https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)
![Node.js](https://img.shields.io/badge/Node.js-339933?style=for-the-badge&logo=node.js&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![Stripe](https://img.shields.io/badge/Stripe-008CDD?style=for-the-badge&logo=stripe&logoColor=white)

---

## ℹ️ Information
This project is built using **Windows Subsystem for Linux (WSL)** with the **Ubuntu distribution**.  
Follow the installation and configuration steps below to set up the development environment.

---

## 📑 Table of Contents
- [Installation Guide](#installation-guide)
  1. [WSL Setup](#1-wsl-setup)
  2. [PHP & Composer](#2-php--composer)
  3. [Node.js](#3-nodejs)
  4. [Composer Dependencies](#4-composer-dependencies)
  5. [Environment File](#5-environment-file)
  6. [App Key](#6-app-key)
  7. [Database Setup](#7-database-setup)
  8. [Mailpit](#8-mailpit)
  9. [Stripe](#9-stripe)
- [Running the Project](#running-the-project)

---

## Installation Guide

### 1. WSL Setup
If you are on Windows, install WSL with Ubuntu:  
👉 [WSL Installation Guide](https://learn.microsoft.com/en-us/windows/wsl/install)

---

### 2. PHP & Composer
Install PHP, Composer, and the Laravel installer:
```bash
/bin/bash -c "$(curl -fsSL https://php.new/install/linux/8.4)"
```

---

### 3. Node.js

* If Node.js is already installed, refresh your shell:

```bash
hash -r
```

* If not, install **NVM** and Node.js:

```bash
curl -fsSL https://raw.githubusercontent.com/nvm-sh/nvm/v0.39.7/install.sh | bash
source ~/.bashrc

nvm install 22
nvm use 22
```

* Install project dependencies:

```bash
npm install
npm run build
```

---

### 4. Composer Dependencies

```bash
composer install
```

---

### 5. Environment File

Copy the example environment file:

```bash
cp .env.example .env
```

---

### 6. App Key

Generate a new application key:

```bash
php artisan key:generate
```

---

### 7. Database Setup

Install and configure MySQL:

```bash
sudo apt install mysql-server
sudo systemctl status mysql
```

If running, log in:

```bash
sudo mysql
```

Set a password:

```sql
ALTER USER 'root'@'localhost' IDENTIFIED BY 'new_password';
FLUSH PRIVILEGES;
exit;
```

Update `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=root
DB_PASSWORD=new_password
```

Run migrations with seeders:

```bash
php artisan migrate --seed
```

---

### 8. Mailpit

Install Mailpit for email notifications (password reset, order confirmation, etc.):

```bash
sudo sh < <(curl -sL https://raw.githubusercontent.com/axllent/mailpit/develop/install.sh)
```

---

### 9. Stripe

#### 9.1 Install Stripe CLI

1. Add GPG key:

```bash
curl -s https://packages.stripe.dev/api/security/keypair/stripe-cli-gpg/public | gpg --dearmor | sudo tee /usr/share/keyrings/stripe.gpg
```

2. Add repository:

```bash
echo "deb [signed-by=/usr/share/keyrings/stripe.gpg] https://packages.stripe.dev/stripe-cli-debian-local stable main" | sudo tee -a /etc/apt/sources.list.d/stripe.list
```

3. Update package list:

```bash
sudo apt update
```

4. Install Stripe CLI:

```bash
sudo apt install stripe
```

#### 9.2 Configure Stripe

1. Register an account at [Stripe](https://stripe.com).
2. Create a **sandbox project**.
3. Copy API keys into `.env`:

```env
STRIPE_KEY=<Publishable key>
STRIPE_SECRET=<Secret key>
```

#### 9.3 Stripe CLI Login

```bash
stripe login
```

Press **Enter** to authenticate via browser.

Example output:

```output
Your pairing code is: enjoy-enough-outwit-win
Press Enter to open the browser or visit https://dashboard.stripe.com/stripecli/confirm_auth
```

---

## Running the Project

Run the following commands in separate terminals:

1. Start Laravel:

```bash
composer run dev
```

2. Start Mailpit:

```bash
mailpit
```

3. Start Stripe webhook listener:

```bash
stripe listen --forward-to http://localhost:8000/stripe/webhook
```

Update `.env` with the new webhook secret:

```env
STRIPE_WEBHOOK_SECRET=<WEBHOOK_SECRET>
```

---

✅ You are now ready to run **Shobee** locally!