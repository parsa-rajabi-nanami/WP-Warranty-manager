<?php

/**
 * Fired during plugin deactivation
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
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Warranty_Code_Manager
 * @subpackage Warranty_Code_Manager/includes
 * @author     Parsa Rajabi
 */
class WCMGR_Deactivator
{

	/**
	 * Run all deactivation routines.
	 *
	 * Called by register_deactivation_hook() in the main plugin file.
	 *
	 * @since  1.0.0
	 * @return void
	 */
	public static function deactivate()
	{
		// No action needed on deactivation.
	}
}
