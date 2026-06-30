# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## What this is

A WordPress plugin for managing product warranty codes: admins bulk-import codes via CSV from the wp-admin dashboard; site visitors activate a code through a front-end shortcode form. Built on the [WordPress Plugin Boilerplate](https://github.com/DevinVinson/WordPress-Plugin-Boilerplate) (the loader/i18n/admin/public scaffolding comes from there).

There is **no build step, no package manager, and no test suite** — it is plain PHP 8.0+, vanilla CSS, and jQuery. "Running" it means dropping the plugin into a WordPress install.

## Layout

The git repo root contains the actual plugin in the `wp-warranty-manager/` subdirectory. That subdirectory is what gets deployed to `wp-content/plugins/wp-warranty-manager/`. Everything below is relative to it.

- `wp-warranty-manager.php` — plugin bootstrap: defines `WPWM_VERSION`/`WPWM_PLUGIN_PATH`/`WPWM_PLUGIN_URL`, registers activation/deactivation hooks, instantiates `WP_Warranty_Manager`.
- `includes/class-wpwm.php` — the core orchestrator. **This is where all hooks are wired up** (see below).
- `includes/class-wpwm-loader.php` — boilerplate hook registry; `add_action`/`add_filter` queue hooks, `run()` registers them with WP.
- `includes/class-wpwm-database.php` + `class-wpwm-activator.php` — table creation on activation via `dbDelta`.
- `includes/class-wpwm-ajax.php` — front-end warranty activation (the only AJAX endpoint).
- `includes/class-wpwm-csv-importer.php` — CSV bulk import.
- `includes/class-wpwm-shortcodes.php` — registers `[warranty_form]`.
- `admin/class-wpwm-admin.php` — admin menu, list/search/paginate, edit, delete. Renders via partials in `admin/partials/`.
- `public/class-wpwm-public.php` — enqueues front-end CSS/JS and `wp_localize_script`s the AJAX url + nonce as `wpWarranty`.
- `uninstall.php` — drops the table and deletes `wpwm_db_version` option.

## Architecture: how hooks are registered

Do **not** call `add_action`/`add_shortcode` directly inside feature classes. The single source of truth is `WP_Warranty_Manager::__construct()` in `includes/class-wpwm.php`, which calls a series of `define_*_hooks()` methods. Each method instantiates a component and queues its callbacks on the `$loader`. To add functionality:

1. Create/extend a class in `includes/` or `admin/`.
2. `require_once` it in `WP_Warranty_Manager::load_dependencies()`.
3. Add a `define_*_hooks()` method (or extend an existing one) that registers the callback via `$this->loader->add_action(...)`, and call it from the constructor.

`run()` flushes everything to WordPress at the end.

## Data model

Single table: `{$wpdb->prefix}warranty_codes`. The table suffix is defined as `WPWM_TABLE_WARRANTY_CODES = 'warranty_codes'` in the plugin bootstrap (`wp-warranty-manager.php`). Use this constant — do not hardcode the string in new code.

Columns: `id`, `warranty_code` (unique), `status`, `activated_at`, `expires_at`, `customer_ip`, `created_at`.

`status` only ever holds two values in code: `'inactive'` (default, set on import) and `'active'` (set on activation). There is no stored "expired" status — expiry is implied by `expires_at`.

## Activation flow

Front-end form (`[warranty_form]`) → jQuery in `public/js/wpwm-public.js` POSTs `action=wp_activate_warranty` to `admin-ajax.php` → `WPWM_Ajax::activate_warranty()`. It looks up the code, rejects if missing or already `active`, otherwise sets `status=active`, `activated_at=now`, and `expires_at=now+1 year`. The endpoint is registered for both `wp_ajax_` (logged-in) and `wp_ajax_nopriv_` (guests).

## Conventions

- **Prefixes:** classes `WPWM_*`, options/meta `wpwm_*`, hook/action names `wp_warranty_*` / `*_warranty_*`. Text domain is `wp-warranty-manager`.
- **Action/nonce names** (must match between the form and the handler):
  - AJAX: action `wp_activate_warranty`, nonce `wp_warranty_ajax_nonce`.
  - CSV import: `admin_post_import_warranty_csv`, nonce `import_warranty_csv_nonce`.
  - Edit: `admin_post_edit_warranty_code`, nonce `edit_warranty_code_nonce`.
  - Delete: `admin_post_delete_warranty_code`, nonce `delete_warranty_code_nonce`.
- Admin handlers gate on `current_user_can('manage_options')` + `check_admin_referer(...)`, then redirect (PRG pattern). AJAX uses `check_ajax_referer(...)`.
- Admin classes do no HTML; `WPWM_Admin::render()` `extract()`s a data array and `include`s a partial from `admin/partials/`.
- All SQL goes through `$wpdb->prepare` / `$wpdb->insert`/`update`/`delete`. Every PHP entry file guards with `if (!defined('ABSPATH')) exit;`.

## Localization / Persian calendar

This plugin targets a Persian (Iranian) audience. `to_jalali()` (duplicated in `WPWM_Ajax` and `WPWM_Admin`) converts dates to the Jalali calendar **if the WP-Parsidate plugin is active** (`function_exists('parsidate')`), and silently falls back to Gregorian `date()` otherwise. Translatable strings use `__()`/`esc_html_e()` etc.; the catalog is `languages/wp-warranty-manager.pot`.

## Unimplemented features — do not assume these exist

- CSV `product_name` and `warranty_period` columns: the importer reads the **first** CSV column only (`$data[0]`); everything else is ignored, and the table has no columns for them.
- CSV import does **not** skip a header row — a `warranty_code` header line is imported as a literal code.
- Warranty duration is **hardcoded to +1 year** in `WPWM_Ajax`; it is not configurable.
- A separate status-check / inquiry flow: only activation exists. Only `inactive`/`active` are stored.

When implementing any of these, update both the code and `README.md` to keep them in sync.

## Verifying changes

No automated tests exist. Run `bash scripts/setup-dev.sh` to check prerequisites and get environment-specific instructions. Then:

1. Install the plugin in a local WordPress (`wp-env`, Local, Docker, etc.) and activate it — this creates the DB table.
2. Add `[warranty_form]` to a page.
3. Import a CSV of codes from **Warranty Manager → Import CSV**.
4. Exercise activation, edit, and delete flows.
5. Lint PHP: `find wp-warranty-manager -name "*.php" -exec php -l {} \;`

GitHub Actions (`php-lint.yml`) runs PHP syntax checks on push and pull requests across PHP 8.0–8.3.

## Repository meta-structure

Files at the repo root (not deployed to WordPress):

| Path | Purpose |
|---|---|
| `README.md` | Public-facing project overview |
| `CHANGELOG.md` | Version history (Keep a Changelog format) |
| `CONTRIBUTING.md` | Contributor guide |
| `SECURITY.md` | Vulnerability reporting policy |
| `CODE_OF_CONDUCT.md` | Community standards |
| `.gitattributes` | Line endings + export-ignore rules |
| `scripts/setup-dev.sh` | Dev environment bootstrap |
| `.github/workflows/php-lint.yml` | CI: PHP syntax check |
| `.github/ISSUE_TEMPLATE/` | GitHub issue forms |
| `.github/pull_request_template.md` | PR checklist |

Only the `wp-warranty-manager/` subdirectory is deployed to WordPress.

## Claude Code setup

**Project-local** (lives in this repo, loaded automatically):
- `CLAUDE.md` — this file

**Global** (must be configured per developer machine, cannot live in the repo):
- Caveman skill (`/caveman`) — install via Claude Code global skills
- Frontend Design skill (`/frontend-design`) — install via Claude Code global skills
- The SessionStart hook that activates Caveman automatically is in `~/.claude/settings.json`

A new developer cloning this repo gets project-level CLAUDE.md automatically. They must install the global skills separately; `scripts/setup-dev.sh` prints a reminder.

## Skills

Two skills are installed globally:

**Caveman** (`/caveman`) — active automatically via SessionStart hook. Minimizes output tokens using compressed prose while preserving technical accuracy. Do not disable unless the user explicitly requests normal verbosity.

**Frontend Design** (`/frontend-design`) — invoke for any task involving UI/UX, HTML, CSS, JavaScript, or WordPress admin interface design (partials in `admin/partials/`, front-end shortcode form, public CSS/JS). Do not invoke for PHP logic, database, or hook-wiring tasks.

### Task pre-processing

Before making code changes on complex tasks (multi-file edits, new features, architectural changes), produce a concise execution plan: list the files to touch, the change in each, and any order dependency. Keep the plan to bullet points; skip it for single-file edits or trivial fixes.

### Context reuse

Reuse already-established context within a session — do not re-read files that were already read unless their content may have changed. Do not re-derive architecture or data model facts already confirmed earlier in the conversation.
