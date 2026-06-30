# Security Policy

## Supported versions

| Version | Supported |
|---|---|
| 1.0.x | Yes |

---

## Reporting a vulnerability

**Do not open a public GitHub issue for security vulnerabilities.**

Please report security issues by emailing **pursa218rajaby@gmail.com** with:

- A description of the vulnerability
- Steps to reproduce
- Potential impact
- WordPress and PHP versions affected
- Plugin version affected

You will receive acknowledgement within **48 hours** and a status update within **7 days**.

Once the vulnerability is confirmed, a patch will be prepared and a coordinated disclosure will follow. Credit will be given in the release notes unless you prefer to remain anonymous.

---

## Security measures in this plugin

- Nonce verification on all form submissions (`check_admin_referer`) and AJAX requests (`check_ajax_referer`)
- Capability check (`current_user_can('manage_options')`) on every admin action
- All database queries use `$wpdb->prepare()`, `$wpdb->insert()`, `$wpdb->update()`, or `$wpdb->delete()`
- User input is sanitised with WordPress sanitization functions before use
- Rate limiting on the AJAX activation endpoint (10 attempts / 15-minute window / IP)
- All PHP entry files guard against direct access with `if (!defined('ABSPATH')) exit;`
- File upload restricted to `.csv` MIME type with a configurable size cap (`WPWM_CSV_MAX_SIZE`, default 10 MB)
