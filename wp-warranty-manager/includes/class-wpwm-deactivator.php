<?php

/**
 * Fired during plugin deactivation
 *
 * @link       https://github.com/parsa-rajabi-nanami/WP-Warranty-manager
 * @since      1.0.0
 *
 * @package    WP_Warranty_Manager
 * @subpackage WP_Warranty_Manager/includes
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
 * @package    WP_Warranty_Manager
 * @subpackage WP_Warranty_Manager/includes
 * @author     Parsa Rajabi
 */
class WPWM_Deactivator
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
