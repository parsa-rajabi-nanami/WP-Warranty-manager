# Repository Guidelines

## Working Agreement

Read this before every task; it is the persistent source of truth after direct user instructions. Record only durable decisions, conventions, and workflows. Remove obsolete, conflicting, or duplicate guidance; never record temporary task state.

Before editing, read relevant files fully and trace the affected flow. Make the smallest scoped change. Preserve behavior, public APIs, hooks, and file locations unless explicitly required. Avoid unrelated refactors and unapproved dependencies; clarify ambiguities affecting behavior or compatibility.

## Project Structure & Module Organization

The plugin lives in `wp-warranty-manager/`; `wp-warranty-manager.php` is its bootstrap. Services, database access, AJAX, shortcodes, imports, and the hook loader belong in `includes/`. Dashboard code lives in `admin/`; front-end code and assets in `public/`; translations in `languages/`.

Register actions, filters, and shortcodes through the loader methods in `includes/class-wpwm.php`. Feature classes should implement callbacks, not call `add_action()`, `add_filter()`, or `add_shortcode()` directly.

## Build, Test, and Development Commands

There is no build step. Develop on WordPress 6.0+ and PHP 8.0+ by placing `wp-warranty-manager/` in `wp-content/plugins/` and activating it.

```bash
find wp-warranty-manager -name "*.php" -exec php -l {} \;
```

Run this before every push; GitHub Actions repeats it on PHP 8.0 through 8.3.

## Coding Style & Naming Conventions

Follow WPCS and WordPress security practices; use PSR-12 only when compatible. Preserve LF endings and use tabs for PHP indentation. Prefix classes with `WPWM_`, functions/options with `wpwm_`, and public hooks with `wp_warranty_`; use text domain `wp-warranty-manager`. Prefer focused methods, early returns, strict comparisons, core APIs, and PHPDoc for public methods. Use `WP_Error` for suitable expected failures; never silently ignore errors.

Validate and sanitize request data; escape at output. Require nonces and capabilities for writes. Keep admin HTML in partials. Use `$wpdb->prepare()` or CRUD helpers for SQL, avoiding repeated queries and queries inside loops.

## Testing Guidelines

There is no automated test suite or coverage threshold. Besides linting, test activation/deactivation, CSV import edge cases, admin search/edit/delete, and `[warranty_form]` as guest and authenticated users. Document environment and steps in the pull request.

## Commit & Pull Request Guidelines

Use Conventional Commits, for example `feat: add warranty export`, and branches such as `fix/short-description`. Complete `.github/pull_request_template.md`: summarize intent, link issues, provide testing notes, and add UI screenshots. Update `CHANGELOG.md` under `[Unreleased]` and `README.md` for behavior changes.

## Security & Configuration

Never commit secrets, `wp-config.php`, uploads, logs, or archives. Guard PHP entry files against direct access and preserve public AJAX nonce and rate-limit checks.
