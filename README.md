<div align="center">

# 🌐 SitePulse

### Self-Hosted Website Uptime, SSL Certificate & Domain Expiration Monitoring System

[![Laravel](https://img.shields.io/badge/Laravel-13.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2%20|%208.4-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-7952B3?style=for-the-badge&logo=bootstrap&logoColor=white)](https://getbootstrap.com)
[![License: MIT](https://img.shields.io/badge/License-MIT-emerald?style=for-the-badge)](LICENSE)

<p align="center">
  A clean, lightweight, and reliable monitoring dashboard designed to eliminate deceptive browser-cached checks by dispatching zero-cache HTTP probes, direct OpenSSL socket handshakes, and registrar expiration queries directly from your server.
</p>

[Key Features](#-key-features) •
[Architecture](#-how-it-works-anti-cache-engine) •
[Installation](#-installation--setup) •
[CLI & Automation](#-cron--background-automation) •
[Author](#-author--attribution)

</div>

---

## 🚀 Key Features

* **Anti-Cache Live Uptime Probing:**
  * Bypasses client-side browser caches and intermediate CDN/proxy caches using strict zero-cache headers (`Cache-Control: no-cache, no-store, must-revalidate`, `Pragma: no-cache`) and dynamic timestamp query-busters.
  * Measures round-trip server latency in milliseconds (`ms`).
  * Enforces stable HTTP 1.1 protocol with automatic response compression (`gzip`, `deflate`, `br`) to conserve server bandwidth.
  * Resilient against unannounced TLS socket closures with OpenSSL 3.0+ compatibility and automatic HEAD-request fallback.

* **Live SSL Certificate Inspection:**
  * Direct encrypted TLS socket inspection via port 443 (`stream_socket_client` and `openssl_x509_parse`).
  * Extracts authoritative certificate attributes: **Issuer Name** (e.g., Let's Encrypt, Cloudflare, DigiCert) and exact **Expiration Timestamps**.
  * Calculates real-time countdown days and displays intuitive health badges:
    * `Valid` (More than 14 days remaining)
    * `Expiring Soon` (14 days or less remaining)
    * `Expired` (Certificate has lapsed)
    * `Invalid SSL` (Handshake failure or hostname mismatch)

* **Domain & Hosting Renewal Tracking:**
  * Records domain registrars, hosting providers, server IPs, and scheduled renewal deadlines.
  * Authoritative RDAP lookup integration to query domain registry expiration dates.
  * Dynamic remaining-days countdown badges: Safe (`> 30d`), Warning (`≤ 30d`), and Critical (`≤ 7d / Expired`).

* **Interactive Mini-Browser Live Previews:**
  * Displays a compact, sandboxed mini-browser window frame (`220px × 145px`) with Mac-style controls directly inside dashboard table cells for quick visual site verification.

* **Diagnostic Details & Historical Logs:**
  * Comprehensive single-site view (`/sites/{id}`) containing diagnostic cards, client information, and full inspection history logs (HTTP status codes, latency in ms, error traces, and timestamps).

* **On-Demand Single & Bulk Checks:**
  * Trigger real-time AJAX single-site diagnostic checks without refreshing the page.
  * "Check All Sites" bulk action for batch re-checking with visual progress feedback.

* **Interactive Filtering & Search:**
  * Instantly filter registered websites by status (`All`, `Online`, `Down`, `SSL Alert`) or search by site name, client name, or URL.

* **Built-in Documentation (`/guide`):**
  * Integrated user documentation explaining website onboarding, status codes, anti-cache architecture, and server cron setup.

---

## 🛠️ How It Works: Anti-Cache Engine

Traditional browser testing can deliver misleading results due to local browser caching, service workers, or ISP-level proxy nodes—often serving stale cached pages even when the origin web server is returning 5xx errors or down.

SitePulse executes all probes server-to-server:

```
[SitePulse Server] ──( cURL + Zero-Cache Headers )──> [Origin Web Server]
[SitePulse Server] ──( OpenSSL Socket Handshake )───> [Port 443 / Peer Cert]
[SitePulse Server] ──( Authoritative RDAP Query )───> [Domain Registry]
```

1. **HTTP Uptime Probe:** Sends real-time requests with cache-busting headers to verify genuine HTTP response codes (200, 301, 302, 401, 403, 500, etc.).
2. **OpenSSL Socket Stream:** Establishes a TLS stream client to parse the peer X.509 certificate directly, extracting live validity dates regardless of what the web application renders.
3. **Registry Sync:** Queries RDAP registry servers to confirm official domain expiration dates.

---

## 💻 Tech Stack

* **Framework:** [Laravel 13.x](https://laravel.com)
* **Backend Language:** PHP 8.3+ / PHP 8.4
* **Database:** MySQL / MariaDB (via Eloquent ORM)
* **Frontend:** Laravel Blade, [Bootstrap 5.3.3](https://getbootstrap.com), [Bootstrap Icons 1.11.3](https://icons.getbootstrap.com)
* **Networking / Security:** cURL, OpenSSL Extension, RDAP Client

---

## 📦 Installation & Setup

### 1. Prerequisites
Ensure you have the following installed on your machine or server:
* PHP >= 8.3 with `curl`, `openssl`, `pdo_mysql`, `mbstring`, and `json` extensions.
* Composer
* MySQL or MariaDB

### 2. Clone the Repository
```bash
git clone https://github.com/AyodhyaSankalpa/Site-Monitoring-System.git
cd Site-Monitoring-System
```

### 3. Install Dependencies
```bash
composer install
```

### 4. Configure Environment
Copy the example environment configuration:
```bash
cp .env.example .env
```

Generate your application key:
```bash
php artisan key:generate
```

Open `.env` and set your database connection details:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=site_monitoring_db
DB_USERNAME=root
DB_PASSWORD=your_password
```

### 5. Run Database Migrations
Create the `sites` and `site_checks` tables:
```bash
php artisan migrate
```

### 6. Start the Development Server
```bash
php artisan serve
```

Visit `http://127.0.0.1:8000` in your browser.

---

## ⏰ Cron & Background Automation

SitePulse includes a console command to inspect all active websites in batch:

```bash
php artisan sites:check
```

### Production Crontab Setup
To automate monitoring checks every 15 minutes on a production Linux server (VPS, Ubuntu, cPanel):

```bash
crontab -e
```

Add the following cron expression (replace `/path/to/project` with your absolute project path):

```cron
*/15 * * * * cd /path/to/project && php artisan sites:check >> /dev/null 2>&1
```

---

## 📂 Project Structure Overview

```
Site-Monitoring-System/
├── app/
│   ├── Console/Commands/
│   │   └── CheckSitesCommand.php       # CLI command: php artisan sites:check
│   ├── Http/Controllers/
│   │   └── SiteController.php          # CRUD, filtering, single & bulk checks
│   ├── Models/
│   │   ├── Site.php                    # Site entity with expiry countdown helpers
│   │   └── SiteCheck.php               # Historical inspection logs
│   └── Services/
│       └── SiteCheckerService.php      # cURL anti-cache probe, OpenSSL socket & RDAP
├── database/migrations/
│   ├── 2026_10_15_000001_create_sites_table.php
│   └── 2026_10_15_000002_create_site_checks_table.php
├── resources/views/
│   ├── layouts/
│   │   └── app.blade.php               # Layout with SVG favicon, responsive footer & navbar
│   ├── sites/
│   │   ├── index.blade.php             # Dashboard with stats, filters, mini-browser previews
│   │   ├── create.blade.php            # Register new site form
│   │   ├── edit.blade.php              # Edit site & renewal info
│   │   └── show.blade.php              # Diagnostic view & historical logs table
│   └── guide.blade.php                 # In-app user documentation & status manual
└── routes/
    └── web.php                         # Resource routes, monitoring actions & guide
```

---

## 👨‍💻 Author & Attribution

* **Developer:** [Ayodhya Sankalpa](https://github.com/AyodhyaSankalpa)
* **GitHub Repository:** [Site-Monitoring-System](https://github.com/AyodhyaSankalpa/Site-Monitoring-System)

If you find this project useful, feel free to **star ⭐ the repository**!

---

## 📄 License

This project is open-source software licensed under the [MIT License](LICENSE).
