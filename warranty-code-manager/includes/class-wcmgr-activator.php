<?php

/**
 * Fired during plugin activation
 *
 * @since      1.0.0
 *
 * @package    Warranty_Code_Manager
 * @subpackage Warranty_Code_Manager/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Warranty_Code_Manager
 * @subpackage Warranty_Code_Manager/includes
 * @author     Parsa Rajabi
 */
if (!defined('ABSPATH')) {
	exit;
}

class WCMGR_Activator
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
		require_once WCMGR_PLUGIN_PATH . 'includes/class-wcmgr-database.php';

		$database = new WCMGR_Database();
		$database->create_tables();
	}
}
