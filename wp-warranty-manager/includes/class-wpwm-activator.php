<?php

/**
 * Fired during plugin activation
 *
 * @link       https://github.com/parsa-rajabi-nanami/WP-Warranty-manager
 * @since      1.0.0
 *
 * @package    WP_Warranty_Manager
 * @subpackage WP_Warranty_Manager/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    WP_Warranty_Manager
 * @subpackage WP_Warranty_Manager/includes
 * @author     Parsa Rajabi
 */
if (!defined('ABSPATH')) {
	exit;
}

class WPWM_Activator
{

	/**
	 * Run all activation routines.
	 *
	 * Loads the database class and creates the required tables.
	 * Called by register_activation_hook() in the main plugin file.
	 *
	 * @since  1.0.0
	 * @return void
	 */
	public static function activate()
	{
		require_once WPWM_PLUGIN_PATH . 'includes/class-wpwm-database.php';

		$database = new WPWM_Database();
		$database->create_tables();
	}
}
