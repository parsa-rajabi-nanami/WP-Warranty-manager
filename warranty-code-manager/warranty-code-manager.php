<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @since             1.0.0
 * @package           Warranty_Code_Manager
 *
 * @wordpress-plugin
 * Plugin Name:       Warranty Code Manager
 * Description:       Professional warranty activation and management system with CSV import, shortcode and admin dashboard.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      8.0
 * Author:            Parsa Rajabi
 * Author URI:        https://github.com/parsa-rajabi-nanami/
 * License:           MIT
 * License URI:       https://opensource.org/licenses/MIT
 * Text Domain:       warranty-code-manager
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (! defined('WPINC')) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 */
define('WCMGR_VERSION', '1.0.0');
define('WCMGR_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('WCMGR_PLUGIN_URL', plugin_dir_url(__FILE__));

/** Rate limiting: max attempts before blocking, and window length in seconds. */
define('WCMGR_RATE_LIMIT_MAX', 10);
define('WCMGR_RATE_LIMIT_WINDOW', 15 * MINUTE_IN_SECONDS);

/** CSV import: maximum allowed file size in bytes (default 10 MB). */
define('WCMGR_CSV_MAX_SIZE', 10 * MB_IN_BYTES);

/** The custom database table that stores all warranty codes (without the WordPress database prefix). */
define('WCMGR_TABLE_WARRANTY_CODES', 'warranty_codes');

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-wcmgr-activator.php
 */
function wcmgr_activate()
{
	require_once plugin_dir_path(__FILE__) . 'includes/class-wcmgr-activator.php';
	WCMGR_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-wcmgr-deactivator.php
 */
function wcmgr_deactivate()
{
	require_once plugin_dir_path(__FILE__) . 'includes/class-wcmgr-deactivator.php';
	WCMGR_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'wcmgr_activate');
register_deactivation_hook(__FILE__, 'wcmgr_deactivate');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-wcmgr.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function wcmgr_run()
{
	$plugin = new WCMGR_Plugin();
	$plugin->run();
}
wcmgr_run();
