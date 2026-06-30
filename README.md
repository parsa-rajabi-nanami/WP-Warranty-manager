# WP Warranty Manager

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

### From source

1. Clone or download this repository.
2. Copy the `wp-warranty-manager/` subdirectory into your WordPress install:
   ```
   wp-content/plugins/wp-warranty-manager/
   ```
3. Activate the plugin from **WordPress Admin → Plugins**.
4. The database table is created automatically on activation.

### Using WP-CLI

```bash
wp plugin install https://github.com/parsa-rajabi-nanami/WP-Warranty-manager/archive/refs/heads/main.zip --activate
```

---

## Usage

### 1. Import warranty codes

Go to **WordPress Admin → Warranty Manager → Import CSV**.

CSV format — one warranty code per row, first column only:

```csv
warranty_code
ABC123456
XYZ987654
TEST112233
```

> **Note:** The importer reads the first column of every row including any header row. If your CSV has a header, import it and then delete the header row from the admin list.

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
git clone https://github.com/parsa-rajabi-nanami/WP-Warranty-manager.git
cd WP-Warranty-manager
bash scripts/setup-dev.sh
```

### Linting

```bash
# PHP syntax check
find wp-warranty-manager -name "*.php" -exec php -l {} \;
```

---

## Changelog

See [CHANGELOG.md](CHANGELOG.md).

---

## License

[MIT](LICENSE) © 2026 Parsa Rajabi
