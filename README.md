# Warranty Code Manager

A WordPress plugin for managing and activating product warranty codes. Administrators bulk-import codes via CSV from the WordPress dashboard; customers activate a warranty through a front-end shortcode form.

[![PHP Lint](https://github.com/parsa-rajabi-nanami/WP-Warranty-manager/actions/workflows/php-lint.yml/badge.svg)](https://github.com/parsa-rajabi-nanami/WP-Warranty-manager/actions/workflows/php-lint.yml)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](LICENSE)
![WordPress 6.0+](https://img.shields.io/badge/WordPress-6.0%2B-blue)
![PHP 8.0+](https://img.shields.io/badge/PHP-8.0%2B-purple)

---

## Features

**Admin**
- Bulk-import warranty codes from a CSV file
- View, search, and paginate all warranty records
- Edit or delete individual warranty codes
- Activation timestamps and expiration dates visible per record

**Customer**
- Activate a warranty code via a front-end shortcode form
- Instant feedback for invalid or already-activated codes
- Jalali (Persian) calendar support when [WP-Parsidate](https://wordpress.org/plugins/wp-parsidate/) is active

---

## Requirements

| Requirement | Minimum |
|---|---|
| WordPress | 6.0 |
| PHP | 8.0 |
| MySQL / MariaDB | 5.7 / 10.3 |

---

## Installation

### From WordPress Admin
1. Download the latest plugin ZIP file from the repository releases.
2. In WordPress Admin, go to **Plugins → Add New Plugin**.
3. Click Upload Plugin.
4. Select the file and click Install Now.
5. After installation, click Activate Plugin.

### From source

1. Clone or download this repository.
2. Copy the `warranty-code-manager/` subdirectory into your WordPress install: **wp-content/plugins/warranty-code-manager/**
3. Activate the plugin from **WordPress Admin → Plugins**.
4. The database table is created automatically on activation.

---

## Usage

### 1. Import warranty codes

Go to **WordPress Admin → Warranty Code Manager**. The import form is on the main page.

Each row holds one warranty code in the **first column**. A first row containing the literal header `warranty_code` is optional — it is detected and skipped automatically. Any extra columns are ignored.

```csv
warranty_code
ABC123456
XYZ987654
TEST112233
```

Need a starting point? On the import screen, click **Download a sample CSV** to get a correctly formatted template, fill in your codes, and upload it back. Duplicate codes (within the file or already in the database) are skipped on import.

### 2. Place the activation form

Add the shortcode to any page or post:

```text
[warranty_form]
```

This renders the warranty activation form. When a customer submits a valid, unused code:
- Status is set to `active`
- Activation timestamp is recorded
- Expiration is set to **one year from activation**

### 3. Warranty statuses

| Status | Meaning |
|---|---|
| `inactive` | Code exists, not yet activated |
| `active` | Code has been activated |

Expiry is determined by the `expires_at` timestamp; there is no separate "expired" status stored in the database.

---

## Configuration

A few behaviours are controlled by constants defined in `warranty-code-manager/warranty-code-manager.php`:

| Constant | Default | Purpose |
|---|---|---|
| `WCMGR_RATE_LIMIT_MAX` | `10` | Max activation attempts allowed per IP within the window |
| `WCMGR_RATE_LIMIT_WINDOW` | `15 * MINUTE_IN_SECONDS` | Length of the rate-limit window |
| `WCMGR_CSV_MAX_SIZE` | `10 * MB_IN_BYTES` | Maximum accepted CSV upload size |
| `WCMGR_TABLE_WARRANTY_CODES` | `warranty_codes` | Warranty codes table suffix (appended to the WordPress table prefix) |

The activation period is currently fixed at **one year** from the activation date and is not configurable.

---

## Shortcodes

| Shortcode | Description |
|---|---|
| `[warranty_form]` | Renders the warranty activation form |

---

## Security

- Nonce verification on all form submissions and AJAX requests
- `current_user_can('manage_options')` check on all admin actions
- All SQL executed through `$wpdb->prepare()` / `$wpdb->insert()` / `$wpdb->update()` / `$wpdb->delete()`
- User input sanitised with WordPress sanitization functions
- Rate limiting on the AJAX activation endpoint (10 attempts per 15 minutes per IP)
- All PHP entry files guard against direct access with `if (!defined('ABSPATH')) exit;`

See [SECURITY.md](SECURITY.md) to report a vulnerability.

---

## Development

See [CONTRIBUTING.md](CONTRIBUTING.md) for the full guide. Quick start:

```bash
git clone git@github.com:parsa-rajabi-nanami/WP-Warranty-manager.git
cd Warranty-Code-Manager
```

---

## Changelog

See [CHANGELOG.md](CHANGELOG.md).

---

## License

[MIT](LICENSE) © 2026 Parsa Rajabi
