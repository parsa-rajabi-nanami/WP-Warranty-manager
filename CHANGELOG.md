# Changelog

All notable changes to WP Warranty Manager are documented in this file.

The format follows [Keep a Changelog](https://keepachangelog.com/en/1.1.0/).
This project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

---

## [Unreleased]

---

## [1.0.0] - 2026-06-30

### Added
- Admin dashboard: list, search, paginate, edit, and delete warranty codes
- CSV bulk-import of warranty codes (first column, one code per row)
- `[warranty_form]` shortcode for front-end warranty activation
- AJAX activation endpoint (`wp_activate_warranty`) for logged-in and guest users
- Rate limiting on activation endpoint: 10 attempts per 15-minute window per IP
- Jalali (Persian) calendar display when WP-Parsidate plugin is active; falls back to Gregorian
- Database table `{prefix}warranty_codes` created on plugin activation via `dbDelta`
- Nonce verification, capability checks, and `$wpdb->prepare()` throughout
- Defined `WPWM_TABLE_WARRANTY_CODES`, `WPWM_RATE_LIMIT_MAX`, `WPWM_RATE_LIMIT_WINDOW`, `WPWM_CSV_MAX_SIZE` constants

[Unreleased]: https://github.com/parsa-rajabi-nanami/WP-Warranty-manager/compare/v1.0.0...HEAD
[1.0.0]: https://github.com/parsa-rajabi-nanami/WP-Warranty-manager/releases/tag/v1.0.0
