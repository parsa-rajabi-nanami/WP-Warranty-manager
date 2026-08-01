<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @since      1.0.0
 *
 * @package    Warranty_Code_Manager
 * @subpackage Warranty_Code_Manager/admin
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
 * @package    Warranty_Code_Manager
 * @subpackage Warranty_Code_Manager/admin
 * @author     Parsa Rajabi
 */
class WCMGR_Admin {

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
     * Full name of the warranty codes table, including the WordPress database prefix.
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
        $this->table_name  = $wpdb->prefix . WCMGR_TABLE_WARRANTY_CODES;
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
            plugin_dir_url( __FILE__ ) . 'css/wcmgr-admin.css',
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
            plugin_dir_url( __FILE__ ) . 'js/wcmgr-admin.js',
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
            __( 'Warranty Code Manager', 'warranty-code-manager' ),
            __( 'Warranty Code Manager', 'warranty-code-manager' ),
            'manage_options',
            'warranty-manager',
            array( $this, 'admin_page' ),
            'dashicons-shield-alt',
            26
        );

        add_submenu_page(
            'options.php',
            __( 'Edit Warranty', 'warranty-code-manager' ),
            __( 'Edit Warranty', 'warranty-code-manager' ),
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

        // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only list filtering, no data modification.
        $search       = isset( $_GET['s'] )             ? sanitize_text_field( wp_unslash( $_GET['s'] ) )             : '';
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended
        $filter       = isset( $_GET['filter_status'] ) ? sanitize_text_field( wp_unslash( $_GET['filter_status'] ) ) : '';
        $per_page     = 50;
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended
        $current_page = isset( $_GET['paged'] ) ? max( 1, absint( $_GET['paged'] ) ) : 1;
        $offset       = ( $current_page - 1 ) * $per_page;

        $where       = 'WHERE 1=1';
        $query_args  = array();

        if ( $search ) {
            $where        .= ' AND warranty_code LIKE %s';
            $query_args[] = '%' . $wpdb->esc_like( $search ) . '%';
        }
        if ( in_array( $filter, array( 'active', 'inactive' ), true ) ) {
            $where        .= ' AND status = %s';
            $query_args[] = $filter;
        }

        // The table name and WHERE structure are trusted; all request-derived values are bound below.
        // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
        $count_query = "SELECT COUNT(*) FROM {$this->table_name} {$where}";
        if ( ! empty( $query_args ) ) {
            // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- Query structure is fixed above and values are passed separately.
            $count_query = $wpdb->prepare( $count_query, $query_args );
        }

        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter -- Custom-table query is prepared above when it contains request data.
        $total_items = (int) $wpdb->get_var( $count_query );
        $total_pages = (int) ceil( $total_items / $per_page );

        $results_args   = array_merge( $query_args, array( $per_page, $offset ) );
        // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Table name and WHERE structure are trusted; values are bound below.
        $results_query  = "SELECT * FROM {$this->table_name} {$where} ORDER BY id DESC LIMIT %d OFFSET %d";
        // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- Query structure is fixed above and values are passed separately.
        $results_query  = $wpdb->prepare( $results_query, $results_args );
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter -- Custom-table query is fully prepared above.
        $results        = $wpdb->get_results( $results_query );

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

        $this->render( 'wcmgr-admin-display', $view_data );
    }

    /**
     * Render the edit page for a single warranty record.
     *
     * @since   1.0.0
     * @return  void
     */
    public function edit_page() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( esc_html__( 'Unauthorized access.', 'warranty-code-manager' ) );
        }

        global $wpdb;

        // These query parameters select a read-only admin view and display its status notice.
        // phpcs:disable WordPress.Security.NonceVerification.Recommended
        $id      = isset( $_GET['id'] ) ? absint( wp_unslash( $_GET['id'] ) ) : 0;
        $updated = isset( $_GET['updated'] ) && '1' === sanitize_key( wp_unslash( $_GET['updated'] ) );
        // phpcs:enable WordPress.Security.NonceVerification.Recommended

        if ( ! $id ) {
            wp_safe_redirect( admin_url( 'admin.php?page=warranty-manager' ) );
            exit;
        }

        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
        $record = $wpdb->get_row( $wpdb->prepare(
            // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
            "SELECT * FROM {$this->table_name} WHERE id = %d",
            $id
        ) );

        if ( ! $record ) {
            wp_die( esc_html__( 'Warranty code not found.', 'warranty-code-manager' ) );
        }

        $view_data = array(
            'record'  => $record,
            'updated' => $updated,
        );

        $this->render( 'wcmgr-admin-edit-display', $view_data );
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
