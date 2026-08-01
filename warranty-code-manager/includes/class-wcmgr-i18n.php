<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 *
 * @package    Warranty_Code_Manager
 * @subpackage Warranty_Code_Manager/includes
 */
if (!defined('ABSPATH')) {
	exit;
}

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Warranty_Code_Manager
 * @subpackage Warranty_Code_Manager/includes
 * @author     Parsa Rajabi
 */
class WCMGR_i18n
{
	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain()
	{
		// WordPress 4.6+ loads translations automatically for plugins on WordPress.org.
	}
}
