# Warranty Management System for WordPress

A complete WordPress plugin for managing, activating, and verifying product warranty codes.

## Overview

Warranty Management System is a WordPress plugin designed to simplify the management of product warranty codes. Administrators can import warranty codes in bulk through CSV files, while customers can activate their warranties or check warranty status directly from the website.

The plugin provides a centralized system for tracking warranty activations, expiration dates, and customer information.

---

## Features

### Admin Features

* Import unlimited warranty codes using CSV files.
* Bulk management of warranty codes.
* View all warranty records and activation history.
* Search and filter warranty codes.
* Monitor active, inactive, and expired warranties.
* Set warranty duration and validity periods.
* Export warranty data when needed.
* Secure management dashboard within WordPress Admin.

### Customer Features

* Activate warranty by entering a valid warranty code.
* Check warranty status instantly.
* View activation date and warranty expiration date.
* Receive validation messages for invalid, expired, or already activated codes.
* Mobile-friendly warranty activation and inquiry forms.

---

## How It Works

### 1. Import Warranty Codes

Administrators can upload a CSV file containing warranty codes from the WordPress dashboard.

Example CSV format:

| warranty_code |
| ------------- |
| ABC123456     |
| XYZ987654     |
| TEST112233    |

### 2. Warranty Activation

Customers enter their warranty code through the activation form.

The system will:

* Validate the code.
* Check whether the code exists.
* Verify that it has not already been activated.
* Register the activation date.
* Calculate the expiration date based on the configured warranty period.

### 3. Warranty Verification

Customers can enter their warranty code at any time to view:

* Warranty status
* Activation date
* Expiration date
* Remaining validity period

---

## Installation

### Automatic Installation

1. Upload the plugin through the WordPress Plugins screen.
2. Activate the plugin.
3. Navigate to the Warranty Management menu in the WordPress dashboard.
4. Configure plugin settings.
5. Import warranty codes using a CSV file.

### Manual Installation

1. Upload the plugin files to:

`/wp-content/plugins/wp-warranty-manager/`

2. Activate the plugin through the WordPress Plugins menu.
3. Configure warranty settings.
4. Import warranty codes and start using the system.

---

## Shortcodes

### Combined Form

```text
[warranty_form]
```

Displays activation and inquiry functionality in a single interface.

---

## CSV Import Requirements

The CSV file must contain at least one column:

```csv
warranty_code
ABC123456
XYZ987654
TEST112233
```

Optional fields:

```csv
warranty_code,product_name,warranty_period
ABC123456,Product A,12
XYZ987654,Product B,24
```

Where:

* `warranty_code` = Unique warranty code
* `product_name` = Product name
* `warranty_period` = Warranty duration in months

---

## Warranty Statuses

| Status    | Description                            |
| --------- | -------------------------------------- |
| Available | Code exists and has not been activated |
| Activated | Warranty is active                     |
| Expired   | Warranty period has ended              |
| Invalid   | Warranty code does not exist           |

---

## Security Features

* WordPress nonce verification.
* Sanitization and validation of user inputs.
* Protected CSV upload process.
* Role and capability checks for administrators.
* SQL injection protection using WordPress standards.

---

## Technical Requirements

* WordPress 6.0 or higher
* PHP 8.0 or higher
* MySQL 5.7+ / MariaDB equivalent

---

## License

MIT License

Copyright (c) 2026 parsa rajabi

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.

---

## Support

For support, bug reports, feature requests, or documentation updates, please submit an issue through the project's GitHub repository and include a valid issue ID for tracking.
