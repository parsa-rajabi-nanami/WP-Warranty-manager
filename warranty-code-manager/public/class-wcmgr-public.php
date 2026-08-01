<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @since      1.0.0
 *
 * @package    Warranty_Code_Manager
 * @subpackage Warranty_Code_Manager/public
 */
if (!defined('ABSPATH')) {
	exit;
}

/**
 * The public-facing functionality of the plugin.
 *
 * @package    Warranty_Code_Manager
 * @subpackage Warranty_Code_Manager/public
 * @author     Parsa Rajabi
 */
class WCMGR_Public
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
		 * defined in WCMGR_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The WCMGR_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/wcmgr-public.css', array(), $this->version, 'all');
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
		 * defined in WCMGR_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The WCMGR_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script(
			$this->plugin_name,
			plugin_dir_url(__FILE__) . 'js/wcmgr-public.js',
			array('jquery'),
			$this->version,
			true
		);

		wp_localize_script($this->plugin_name, 'warrantyCodeManager', array(
			'ajax_url'      => admin_url('admin-ajax.php'),
			'nonce'         => wp_create_nonce('wcmgr_warranty_ajax_nonce'),
			'msg_checking'  => __('Checking...', 'warranty-code-manager'),
			'msg_error'     => __('Server error occurred.', 'warranty-code-manager'),
		));
	}
}
