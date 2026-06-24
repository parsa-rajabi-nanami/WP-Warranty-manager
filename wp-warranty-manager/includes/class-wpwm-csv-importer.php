<?php

/**
 * CSV import functionality.
 *
 * Handles importing warranty codes from uploaded CSV files.
 *
 * @link       https://github.com/parsa-rajabi-nanami/WP-Warranty-manager
 * @since      1.0.0
 *
 * @package    WP_Warranty_Manager
 * @subpackage WP_Warranty_Manager/includes
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * CSV importer class.
 *
 * Responsible for importing warranty codes from uploaded CSV files.
 * Handles line ending normalization for Windows/Mac/Linux CSV exports.
 *
 * @since      1.0.0
 * @package    WP_Warranty_Manager
 * @subpackage WP_Warranty_Manager/includes
 * @author     Parsa Rajabi
 */
class WPWM_CSV_Importer {

    /**
     * Full name of the warranty codes table, including the WP prefix.
     *
     * @since  1.0.0
     * @var    string
     */
    private $table_name;

    /**
     * Initialize the importer and resolve the table name.
     *
     * @since  1.0.0
     */
    public function __construct() {
        global $wpdb;

        $this->table_name = $wpdb->prefix . 'warranty_codes';
    }

    /**
     * Import warranty codes from an uploaded CSV file.
     *
     * Reads the uploaded file, normalizes line endings, and inserts
     * each unique warranty code into the database as inactive.
     * Skips codes that already exist in the table.
     *
     * Called via admin-post.php on the import_warranty_csv action.
     *
     * @since  1.0.0
     * @return void
     */
    public function import() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( esc_html__( 'Unauthorized access.', 'wp-warranty-manager' ) );
        }

        check_admin_referer( 'import_warranty_csv_nonce' );

        if ( empty( $_FILES['csv_file']['tmp_name'] ) ) {
            wp_redirect( admin_url( 'admin.php?page=warranty-manager' ) );
            exit;
        }

        global $wpdb;

        // Normalize line endings: \r\n (Windows) and \r (old Mac) → \n
        $raw   = file_get_contents( $_FILES['csv_file']['tmp_name'] );
        $raw   = str_replace( array( "\r\n", "\r" ), "\n", $raw );
        $lines = array_filter( array_map( 'trim', explode( "\n", $raw ) ) );

        foreach ( $lines as $line ) {
            $data = str_getcsv( $line, ',' );
            $code = sanitize_text_field( trim( $data[0] ?? '' ) );

            if ( empty( $code ) ) {
                continue;
            }

            $exists = $wpdb->get_var( $wpdb->prepare(
                "SELECT id FROM {$this->table_name} WHERE warranty_code = %s",
                $code
            ) );

            if ( ! $exists ) {
                $wpdb->insert(
                    $this->table_name,
                    array(
                        'warranty_code' => $code,
                        'status'        => 'inactive',
                    ),
                    array( '%s', '%s' )
                );
            }
        }

        wp_redirect( admin_url( 'admin.php?page=warranty-manager' ) );
        exit;
    }
}