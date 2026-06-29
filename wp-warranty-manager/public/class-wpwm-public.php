<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://github.com/parsa-rajabi-nanami/WP-Warranty-manager
 * @since      1.0.0
 *
 * @package    WP_Warranty_Manager
 * @subpackage WP_Warranty_Manager/public
 */
if (!defined('ABSPATH')) {
	exit;
}

/**
 * The public-facing functionality of the plugin.
 *
 * @package    WP_Warranty_Manager
 * @subpackage WP_Warranty_Manager/public
 * @author     Parsa Rajabi
 */
class WPWM_Public
{

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct($plugin_name, $version)
	{

		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles()
	{

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in WPWM_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The WPWM_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/wpwm-public.css', array(), $this->version, 'all');
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts()
	{

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in WPWM_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The WPWM_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script(
			$this->plugin_name,
			plugin_dir_url(__FILE__) . 'js/wpwm-public.js',
			array('jquery'),
			$this->version,
			true
		);

		wp_localize_script($this->plugin_name, 'wpWarranty', array(
			'ajax_url'      => admin_url('admin-ajax.php'),
			'nonce'         => wp_create_nonce('wp_warranty_ajax_nonce'),
			'msg_checking'  => __('Checking...', 'wp-warranty-manager'),
			'msg_error'     => __('Server error occurred.', 'wp-warranty-manager'),
		));
	}
}
