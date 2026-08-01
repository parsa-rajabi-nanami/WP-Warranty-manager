<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
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
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Warranty_Code_Manager
 * @subpackage Warranty_Code_Manager/includes
 * @author     Parsa Rajabi
 */

class WCMGR_Plugin
{

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      WCMGR_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct()
	{
		if (defined('WCMGR_VERSION')) {
			$this->version = WCMGR_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'warranty-code-manager';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
		$this->define_ajax_hooks();
		$this->define_shortcode_hooks();
		$this->define_csv_hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - WCMGR_Loader. Orchestrates the hooks of the plugin.
	 * - WCMGR_i18n. Defines internationalization functionality.
	 * - WCMGR_Admin. Defines all hooks for the admin area.
	 * - WCMGR_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies()
	{

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-wcmgr-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-wcmgr-i18n.php';

		/**
		 * The class responsible for the admin menu, asset enqueueing, and read-only rendering.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-wcmgr-admin.php';

		/**
		 * The class responsible for admin write actions (edit, delete).
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-wcmgr-admin-actions.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-wcmgr-public.php';

		/**
		 * The class responsible for handling all AJAX requests.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-wcmgr-ajax.php';

		/**
		 * The class responsible for registering and rendering plugin shortcodes.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-wcmgr-shortcodes.php';

		/**
		 * The class responsible for importing warranty codes from CSV files.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-wcmgr-csv-importer.php';

		/**
		 * Shared date formatting utility (Jalali / Gregorian).
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-wcmgr-date-helper.php';

		$this->loader = new WCMGR_Loader();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the WCMGR_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale()
	{

		$plugin_i18n = new WCMGR_i18n();

		$this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
	}

	/**
	 * Register all of the hooks related to the admin area functionality of the plugin.
	 *
	 * WCMGR_Admin handles read-only concerns: menu registration, asset enqueueing, and rendering.
	 * WCMGR_Admin_Actions handles write concerns: edit and delete form submissions.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks()
	{
		$plugin_admin   = new WCMGR_Admin($this->get_plugin_name(), $this->get_version());
		$admin_actions  = new WCMGR_Admin_Actions();

		$this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
		$this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');
		$this->loader->add_action('admin_menu', $plugin_admin, 'admin_menu');

		$this->loader->add_action('admin_post_edit_warranty_code',   $admin_actions, 'edit_code');
		$this->loader->add_action('admin_post_delete_warranty_code', $admin_actions, 'delete_code');
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * Assets (CSS/JS) are enqueued on demand inside WCMGR_Shortcodes::render_warranty_form()
	 * so they are only loaded on pages that contain the [warranty_form] shortcode.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks()
	{
		// Intentionally empty: front-end assets are enqueued conditionally by the shortcode renderer.
	}

	/**
	 * Register all AJAX hooks for warranty activation.
	 *
	 * Covers both logged-in (wp_ajax) and guest (wp_ajax_nopriv) requests.
	 *
	 * @since   1.0.0
	 * @access  private
	 */
	private function define_ajax_hooks()
	{
		$plugin_ajax = new WCMGR_Ajax();

		$this->loader->add_action(
			'wp_ajax_wcmgr_activate_warranty',
			$plugin_ajax,
			'activate_warranty'
		);

		$this->loader->add_action(
			'wp_ajax_nopriv_wcmgr_activate_warranty',
			$plugin_ajax,
			'activate_warranty'
		);

		// Backward compatibility for cached pages using the pre-rename AJAX action.
		$this->loader->add_action(
			'wp_ajax_wp_activate_warranty',
			$plugin_ajax,
			'activate_warranty_legacy'
		);

		$this->loader->add_action(
			'wp_ajax_nopriv_wp_activate_warranty',
			$plugin_ajax,
			'activate_warranty_legacy'
		);
	}

	/**
	 * Register shortcode hooks with WordPress.
	 *
	 * Registers the [warranty_form] shortcode via the init hook.
	 *
	 * @since   1.0.0
	 * @access  private
	 */
	private function define_shortcode_hooks()
	{
		$plugin_shortcode = new WCMGR_Shortcodes();

		$this->loader->add_action(
			'init',
			$plugin_shortcode,
			'register_shortcode'
		);
	}

	/**
	 * Register CSV import hooks.
	 *
	 * Handles the admin-post action for CSV file uploads.
	 *
	 * @since   1.0.0
	 * @access  private
	 */
	private function define_csv_hooks()
	{
		$plugin_csv = new WCMGR_CSV_Importer();

		$this->loader->add_action(
			'admin_post_import_warranty_csv',
			$plugin_csv,
			'import'
		);

		$this->loader->add_action(
			'admin_post_download_warranty_sample',
			$plugin_csv,
			'download_sample'
		);
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run()
	{
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name()
	{
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    WCMGR_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader()
	{
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version()
	{
		return $this->version;
	}
}
