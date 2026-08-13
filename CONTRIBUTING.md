# Contributing to Warranty Code Manager

Thank you for taking the time to contribute.

---

## Table of contents

- [Code of Conduct](#code-of-conduct)
- [How to report a bug](#how-to-report-a-bug)
- [How to request a feature](#how-to-request-a-feature)
- [Development setup](#development-setup)
- [Making changes](#making-changes)
- [Pull request checklist](#pull-request-checklist)
- [Coding standards](#coding-standards)

---

## Code of Conduct

This project follows the [Contributor Covenant Code of Conduct](CODE_OF_CONDUCT.md). By participating you agree to abide by its terms.

---

## How to report a bug

1. Search [existing issues](https://github.com/parsa-rajabi-nanami/WP-Warranty-manager/issues) first.
2. If none match, open a **Bug Report** using the issue template.
3. Include: WordPress version, PHP version, plugin version, steps to reproduce, expected vs actual behaviour, and any relevant error output.

---

## How to request a feature

Open a **Feature Request** using the issue template and describe the use case clearly.

---

## Development setup

### Prerequisites

| Tool | Version |
|---|---|
| PHP | 8.0+ |
| WordPress | 6.0+ |
| Node.js | 18+ (for `wp-env` only) |

### Quick start

```bash
git clone git@github.com:parsa-rajabi-nanami/WP-Warranty-manager.git
cd Warranty-Code-Manager
```

The setup script checks prerequisites and prints next steps for your chosen local environment (wp-env, Local, or Docker).

### Manual setup

1. Copy `warranty-code-manager/` into `wp-content/plugins/` of a local WordPress install.
2. Activate the plugin from the WordPress admin.
3. The `{prefix}warranty_codes` database table is created automatically.

---

## Making changes

### Architecture rules

- **Do not** call `add_action` / `add_filter` / `add_shortcode` directly inside feature classes.
- All hooks are registered in `includes/class-wcmgr.php` → `define_*_hooks()` methods via the loader.
- To add a new hook: create or extend a class, `require_once` it in `load_dependencies()`, queue it through the loader.
- Admin classes render no HTML directly — they `extract()` a data array and `include` a partial from `admin/partials/`.
- All SQL must go through `$wpdb->prepare()`, `$wpdb->insert()`, `$wpdb->update()`, or `$wpdb->delete()`.

### Branch naming

```
feat/short-description
fix/short-description
docs/short-description
chore/short-description
```

### Commit messages

Use [Conventional Commits](https://www.conventionalcommits.org/):

```
feat: add warranty code export
fix: prevent duplicate activation on concurrent requests
docs: update CSV import instructions
```

---

## Pull request checklist

- [ ] All new hooks wired through `class-wcmgr.php`, not added directly in feature classes
- [ ] New SQL uses `$wpdb->prepare()` / insert / update / delete
- [ ] New admin actions include `current_user_can('manage_options')` + `check_admin_referer()`
- [ ] CHANGELOG.md updated under `[Unreleased]`
- [ ] README.md updated if the change affects user-facing behaviour
- [ ] No commented-out code committed

---

## Coding standards

This plugin follows [WordPress PHP Coding Standards](https://developer.wordpress.org/coding-standards/wordpress-coding-standards/php/).

Key conventions used in this codebase:

| Convention | Example |
|---|---|
| Class prefix | `WCMGR_*` |
| Option / meta prefix | `wcmgr_*` |
| Hook / action names | `wcmgr_*` |
| Text domain | `warranty-code-manager` |
| ABSPATH guard | Every PHP entry file |