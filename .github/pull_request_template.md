## Summary

<!-- What does this PR do? Why? One paragraph or a short bullet list. -->

## Type of change

- [ ] Bug fix
- [ ] New feature
- [ ] Refactor / cleanup
- [ ] Documentation
- [ ] Chore / dependency update

## Related issues

<!-- Closes #NNN -->

## Checklist

- [ ] PHP syntax passes: `find wp-warranty-manager -name "*.php" -exec php -l {} \;`
- [ ] New hooks wired through `class-wpwm.php` loader, not added directly in feature classes
- [ ] New SQL uses `$wpdb->prepare()` / insert / update / delete
- [ ] New admin actions include `current_user_can('manage_options')` + `check_admin_referer()`
- [ ] CHANGELOG.md updated under `[Unreleased]`
- [ ] README.md updated if user-facing behaviour changed
- [ ] No commented-out code

## Testing notes

<!-- How did you test this? List the manual steps or describe the scenario. -->
