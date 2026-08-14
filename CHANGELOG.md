# Changelog

All notable changes to Warranty Code Manager are documented in this file.

The format follows [Keep a Changelog](https://keepachangelog.com/en/1.1.0/).
This project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

---

## [1.0.0] - 2026-08-14

### Changed
- Renamed the plugin to **Warranty Code Manager** with the plugin slug and text domain `warranty-code-manager` to comply with WordPress.org trademark requirements
- Replaced the old PHP, asset, JavaScript, and translation prefixes with the unique `WCMGR_` / `wcmgr_` / `wcmgr-` prefixes
- Preserved the existing warranty database table, shortcode, admin routes, and admin-post actions; the pre-rename AJAX action remains available as a compatibility alias for cached pages

### Added
- Admin dashboard: list, search, paginate, edit, and delete warranty codes
- CSV bulk-import of warranty codes (first column, one code per row); an optional `warranty_code` header row is detected and skipped
- Downloadable sample CSV template from the admin import screen
- `[warranty_form]` shortcode for front-end warranty activation
- AJAX activation endpoint (`wcmgr_activate_warranty`) for logged-in and guest users
- Rate limiting on activation endpoint: 10 attempts per 15-minute window per IP
- Jalali (Persian) calendar display when WP-Parsidate plugin is active; falls back to Gregorian
- Database table `{prefix}warranty_codes` created on plugin activation via `dbDelta`
- Nonce verification, capability checks, and `$wpdb->prepare()` throughout
- Defined `WCMGR_TABLE_WARRANTY_CODES`, `WCMGR_RATE_LIMIT_MAX`, `WCMGR_RATE_LIMIT_WINDOW`, `WCMGR_CSV_MAX_SIZE` constants

### Fixed
- Hardened CSV upload field validation, temporary-file verification, read-error handling, and bulk-query preparation
- Sanitized read-only admin parameters, escaped pagination output, and prefixed partial-scope variables
- Prepared admin search/filter queries in one pass and documented intentional custom-table database operations

## [Unreleased]

### Changed

### Added

### Fixed

[Unreleased]: https://github.com/parsa-rajabi-nanami/WP-Warranty-manager/compare/v1.0.0...develop
[1.0.0]: https://github.com/parsa-rajabi-nanami/WP-Warranty-manager/releases/tag/v1.0.0