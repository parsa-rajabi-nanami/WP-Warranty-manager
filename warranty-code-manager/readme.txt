=== Warranty Code Manager ===
Contributors: parsa-rajabi-nanami
Tags: warranty, warranty-code, serial-number, product-registration, csv-import
Requires at least: 6.0
Requires PHP: 8.0
Tested up to: 7.0
Stable tag: 1.0.0
License: MIT
License URI: https://opensource.org/licenses/MIT

A WordPress plugin for bulk-importing warranty codes via CSV and letting customers activate them through a front-end form.

== Description ==

Warranty Code Manager lets administrators bulk-import warranty codes via CSV and lets customers activate a code through a front-end shortcode form.

It tracks, per code:

* Activation status (inactive / active)
* Activation date
* Expiration date (one year from activation)
* The activating customer's IP address

=== Key Features ===

* CSV-based bulk import of warranty codes, with a downloadable sample template
* Front-end customer activation via the [warranty_form] shortcode
* Admin dashboard to list, search, filter, edit, and delete codes
* Jalali (Persian) calendar display when the WP-Parsidate plugin is active
* Rate-limited activation endpoint with nonce and capability checks

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/warranty-code-manager/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress (this creates the warranty codes database table)
3. Open the **Warranty Code Manager** menu in the admin dashboard and upload a CSV of warranty codes (use the **Download a sample CSV** link for a correctly formatted template)
4. Add the `[warranty_form]` shortcode to any page so customers can activate their codes

== Frequently Asked Questions ==

= How do I import warranty codes? =
Use the CSV upload feature on the Warranty Code Manager screen. Put one code per row in the first column; an optional `warranty_code` header row is detected and skipped. A starter template is available via the "Download a sample CSV" link.

= What happens when a customer activates a code? =
The code is marked active, the activation time is recorded, and the expiration date is set to one year from activation. Re-activating an already-active code is rejected.

= Does it support multiple products or custom warranty durations? =
Not yet. Each row stores a single warranty code, and the warranty period is fixed at one year from activation. These are possible future enhancements.

== Screenshots ==

1. Admin warranty management table (list, search, filter)
2. Admin CSV import screen
3. Front-end warranty activation form

== Changelog ==

= 1.0.0 =
* CSV bulk import of warranty codes (with downloadable sample template)
* Front-end warranty activation via the [warranty_form] shortcode
* Admin dashboard: list, search, filter, edit, and delete codes
* Rate-limited activation endpoint
* Jalali (Persian) calendar support via WP-Parsidate

== Upgrade Notice ==

= 1.0.0 =
First public release of Warranty Code Manager.

== License ==

This plugin is licensed under the MIT License.

Copyright (c) 2026 parsa rajabi

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software...
