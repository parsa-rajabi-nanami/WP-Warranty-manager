<!--
Thank you for contributing to Warranty Code Manager.

Keep this pull request focused and replace the guidance comments with useful details.
Conditional checklist items may be checked when they do not apply to the change.
-->

## Summary

<!-- Explain the problem, why this change is needed, and the approach taken. -->

## Related issue

<!-- Use "Closes #123" when this PR should close an issue. Otherwise, write "None". -->

## Type of change

<!-- Select all that apply. -->

- [ ] Bug fix
- [ ] New feature or enhancement
- [ ] Refactor or maintenance
- [ ] Documentation
- [ ] Security hardening
- [ ] Breaking change

## What changed

<!-- List the key implementation or user-facing changes. -->

-

## Screenshots or recordings

<!-- Include before/after evidence for UI changes. Otherwise, write "Not applicable". -->

## Testing

### Environment

<!-- Include the versions relevant to this change. -->

- WordPress:
- PHP:
- Browser (if applicable):
- WP-Parsidate active (if applicable):

### Steps and results

<!-- Provide reproducible manual test steps and the observed results. -->

1.

### Additional checks

- [ ] I tested the affected flow as both an administrator and a front-end user, where applicable
- [ ] I tested relevant edge cases and failure paths

## Compatibility, data, and security

<!--
Describe any impact on public hooks, shortcodes, options, database schema/data,
permissions, nonces, validation, sanitization, escaping, rate limiting, or privacy.
Write "No impact" when none apply.
-->

## Contributor checklist

- [ ] I reviewed my own changes and kept the PR focused
- [ ] I preserved existing public APIs and backward compatibility, or documented the impact above
- [ ] New actions, filters, and shortcodes are registered through the loader in `includes/class-wcmgr.php`
- [ ] User input is validated and sanitized, output is escaped, and writes have nonce and capability checks
- [ ] Database queries use `$wpdb->prepare()` or WordPress CRUD helpers
- [ ] Translatable strings use the `warranty-code-manager` text domain
- [ ] I updated `CHANGELOG.md` under `[Unreleased]`, or the change does not require an entry
- [ ] I updated `README.md` and other documentation, or user-facing behavior did not change
- [ ] I added screenshots or recordings for UI changes, or no UI changed
- [ ] I did not commit secrets, logs, generated archives, or unrelated files

## Reviewer notes

<!-- Call out areas that deserve extra attention, known limitations, or follow-up work. -->
