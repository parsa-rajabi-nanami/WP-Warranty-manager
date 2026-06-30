<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://github.com/parsa-rajabi-nanami/WP-Warranty-manager
 * @since             1.0.0
 * @package           WP_Warranty_Manager
 *
 * @wordpress-plugin
 * Plugin Name:       WP Warranty Manager
 * Plugin URI:        https://github.com/parsa-rajabi-nanami/WP-Warranty-manager
 * Description:       Professional warranty activation and management system with CSV import, shortcode and admin dashboard.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      8.0
 * Author:            Parsa Rajabi
 * Author URI:        https://github.com/parsa-rajabi-nanami/
 * License:           MIT
 * License URI:       https://opensource.org/licenses/MIT
 * Text Domain:       wp-warranty-manager
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
define('WPWM_VERSION', '1.0.0');
define('WPWM_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('WPWM_PLUGIN_URL', plugin_dir_url(__FILE__));

/** Rate limiting: max attempts before blocking, and window length in seconds. */
define('WPWM_RATE_LIMIT_MAX', 10);
define('WPWM_RATE_LIMIT_WINDOW', 15 * MINUTE_IN_SECONDS);

/** CSV import: maximum allowed file size in bytes (default 10 MB). */
define('WPWM_CSV_MAX_SIZE', 10 * MB_IN_BYTES);

/** The custom database table that stores all warranty codes (without WP prefix). */
define('WPWM_TABLE_WARRANTY_CODES', 'warranty_codes');

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-wpwm-activator.php
 */
function wpwm_activate()
{
	require_once plugin_dir_path(__FILE__) . 'includes/class-wpwm-activator.php';
	WPWM_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-wpwm-deactivator.php
 */
function wpwm_deactivate()
{
	require_once plugin_dir_path(__FILE__) . 'includes/class-wpwm-deactivator.php';
	WPWM_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'wpwm_activate');
register_deactivation_hook(__FILE__, 'wpwm_deactivate');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-wpwm.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function wpwm_run()
{
	$plugin = new WP_Warranty_Manager();
	$plugin->run();
}
wpwm_run();
