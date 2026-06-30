<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://github.com/parsa-rajabi-nanami/WP-Warranty-manager
 * @since      1.0.0
 *
 * @package    WP_Warranty_Manager
 * @subpackage WP_Warranty_Manager/admin
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and hooks for the admin area.
 * Delegates all HTML rendering to partial view files.
 *
 * @since      1.0.0
 * @package    WP_Warranty_Manager
 * @subpackage WP_Warranty_Manager/admin
 * @author     Parsa Rajabi
 */
class WPWM_Admin {

    /**
     * The ID of this plugin.
     *
     * @since   1.0.0
     * @access  private
     * @var     string
     */
    private $plugin_name;

    /**
     * The current version of the plugin.
     *
     * @since   1.0.0
     * @access  private
     * @var     string
     */
    private $version;

    /**
     * Full name of the warranty codes table, including the WP prefix.
     *
     * @since   1.0.0
     * @access  private
     * @var     string
     */
    private $table_name;

    /**
     * Initialize the class and set its properties.
     *
     * @since  1.0.0
     * @param  string $plugin_name  The name of this plugin.
     * @param  string $version      The current version of this plugin.
     */
    public function __construct( $plugin_name, $version ) {
        global $wpdb;

        $this->plugin_name = $plugin_name;
        $this->version     = $version;
        $this->table_name  = $wpdb->prefix . WPWM_TABLE_WARRANTY_CODES;
    }

    /**
     * Admin page hook suffixes this plugin owns.
     *
     * Populated on the first call to enqueue_styles() / enqueue_scripts() and
     * used to guard against loading assets on unrelated admin pages.
     *
     * @since  1.0.0
     * @var    string[]
     */
    private static $plugin_hooks = array(
        'toplevel_page_warranty-manager',
        'warranty-manager_page_warranty-edit',
    );

    /**
     * Register the stylesheets for the admin area.
     *
     * Only loads on the plugin's own admin pages.
     *
     * @since   1.0.0
     * @param   string $hook  Current admin page hook suffix.
     * @return  void
     */
    public function enqueue_styles( $hook ) {
        if ( ! in_array( $hook, self::$plugin_hooks, true ) ) {
            return;
        }

        wp_enqueue_style(
            $this->plugin_name,
            plugin_dir_url( __FILE__ ) . 'css/wpwm-admin.css',
            array(),
            $this->version,
            'all'
        );
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * Only loads on the plugin's own admin pages.
     *
     * @since   1.0.0
     * @param   string $hook  Current admin page hook suffix.
     * @return  void
     */
    public function enqueue_scripts( $hook ) {
        if ( ! in_array( $hook, self::$plugin_hooks, true ) ) {
            return;
        }

        wp_enqueue_script(
            $this->plugin_name,
            plugin_dir_url( __FILE__ ) . 'js/wpwm-admin.js',
            array( 'jquery' ),
            $this->version,
            false
        );
    }

    /**
     * Register admin menu and submenu pages.
     *
     * @since   1.0.0
     * @return  void
     */
    public function admin_menu() {
        add_menu_page(
            __( 'Warranty Manager', 'wp-warranty-manager' ),
            __( 'Warranty Manager', 'wp-warranty-manager' ),
            'manage_options',
            'warranty-manager',
            array( $this, 'admin_page' ),
            'dashicons-shield-alt',
            26
        );

        add_submenu_page(
            'warranty-manager',
            __( 'Edit Warranty', 'wp-warranty-manager' ),
            '',
            'manage_options',
            'warranty-edit',
            array( $this, 'edit_page' )
        );
    }

    /**
     * Render the main admin page.
     *
     * Collects all required data and passes it to the partial view.
     *
     * @since   1.0.0
     * @return  void
     */
    public function admin_page() {
        global $wpdb;

        $search       = isset( $_GET['s'] )             ? sanitize_text_field( $_GET['s'] )             : '';
        $filter       = isset( $_GET['filter_status'] ) ? sanitize_text_field( $_GET['filter_status'] ) : '';
        $per_page     = 50;
        $current_page = isset( $_GET['paged'] ) ? max( 1, absint( $_GET['paged'] ) ) : 1;
        $offset       = ( $current_page - 1 ) * $per_page;

        $where = 'WHERE 1=1';
        if ( $search ) {
            $where .= $wpdb->prepare( ' AND warranty_code LIKE %s', '%' . $wpdb->esc_like( $search ) . '%' );
        }
        if ( in_array( $filter, array( 'active', 'inactive' ), true ) ) {
            $where .= $wpdb->prepare( ' AND status = %s', $filter );
        }

        $total_items = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$this->table_name} {$where}" );
        $total_pages = ceil( $total_items / $per_page );

        $results = $wpdb->get_results( $wpdb->prepare(
            "SELECT * FROM {$this->table_name} {$where} ORDER BY id DESC LIMIT %d OFFSET %d",
            $per_page,
            $offset
        ) );

        $base_url = admin_url( 'admin.php?page=warranty-manager' );
        if ( $search ) { $base_url .= '&s=' . urlencode( $search ); }
        if ( $filter )  { $base_url .= '&filter_status=' . urlencode( $filter ); }

        $view_data = array(
            'results'      => $results,
            'search'       => $search,
            'filter'       => $filter,
            'total_items'  => $total_items,
            'total_pages'  => $total_pages,
            'current_page' => $current_page,
            'per_page'     => $per_page,
            'base_url'     => $base_url,
            'table_name'   => $this->table_name,
        );

        $this->render( 'wpwm-admin-display', $view_data );
    }

    /**
     * Render the edit page for a single warranty record.
     *
     * @since   1.0.0
     * @return  void
     */
    public function edit_page() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( esc_html__( 'Unauthorized access.', 'wp-warranty-manager' ) );
        }

        global $wpdb;

        $id = isset( $_GET['id'] ) ? absint( $_GET['id'] ) : 0;

        if ( ! $id ) {
            wp_redirect( admin_url( 'admin.php?page=warranty-manager' ) );
            exit;
        }

        $record = $wpdb->get_row( $wpdb->prepare(
            "SELECT * FROM {$this->table_name} WHERE id = %d",
            $id
        ) );

        if ( ! $record ) {
            wp_die( esc_html__( 'Warranty code not found.', 'wp-warranty-manager' ) );
        }

        $view_data = array(
            'record'  => $record,
            'updated' => isset( $_GET['updated'] ) && '1' === $_GET['updated'],
        );

        $this->render( 'wpwm-admin-edit-display', $view_data );
    }

    /**
     * Load and render a partial view file.
     *
     * The partial receives data via the $view_data array. Access values
     * as $view_data['key'] — no extract(), no scope pollution.
     *
     * @since   1.0.0
     * @access  private
     * @param   string $partial    Partial file name without .php extension.
     * @param   array  $view_data  Data to pass to the partial.
     * @return  void
     */
    private function render( $partial, $view_data = array() ) {
        include plugin_dir_path( __FILE__ ) . 'partials/' . $partial . '.php';
    }
}