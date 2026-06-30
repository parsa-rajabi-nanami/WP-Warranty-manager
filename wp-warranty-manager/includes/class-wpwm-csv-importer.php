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

        $this->table_name = $wpdb->prefix . WPWM_TABLE_WARRANTY_CODES;
    }

    /**
     * Validate the uploaded CSV file before processing.
     *
     * Checks presence, upload errors, file size, and MIME type / extension.
     * Returns a translated error string on failure, or an empty string on success.
     *
     * @since  1.0.0
     * @return string  Error message, or '' when valid.
     */
    private function validate_upload() {
        if ( empty( $_FILES['csv_file']['tmp_name'] ) || ! isset( $_FILES['csv_file']['error'] ) ) {
            return __( 'No file was uploaded.', 'wp-warranty-manager' );
        }

        if ( UPLOAD_ERR_OK !== (int) $_FILES['csv_file']['error'] ) {
            return __( 'File upload error. Please try again.', 'wp-warranty-manager' );
        }

        if ( (int) $_FILES['csv_file']['size'] > WPWM_CSV_MAX_SIZE ) {
            return sprintf(
                /* translators: %d: max allowed megabytes */
                __( 'File exceeds the maximum allowed size of %d MB.', 'wp-warranty-manager' ),
                WPWM_CSV_MAX_SIZE / MB_IN_BYTES
            );
        }

        // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- tmp_name is server-generated, not user-controlled.
        $file_info = wp_check_filetype_and_ext(
            // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
            $_FILES['csv_file']['tmp_name'],
            sanitize_file_name( wp_unslash( $_FILES['csv_file']['name'] ) ),
            array( 'csv' => 'text/csv' )
        );

        $allowed_ext   = array( 'csv' );
        $allowed_types = array( 'text/csv', 'text/plain', 'application/csv', 'application/vnd.ms-excel' );
        $ext           = strtolower( pathinfo( sanitize_file_name( $_FILES['csv_file']['name'] ), PATHINFO_EXTENSION ) );

        if ( ! in_array( $ext, $allowed_ext, true ) ) {
            return __( 'Only .csv files are allowed.', 'wp-warranty-manager' );
        }

        // wp_check_filetype_and_ext returns false ext/type when the real MIME doesn't match.
        // Fall back to checking the reported MIME type from the browser as a secondary gate.
        $mime = $file_info['type'] ?: sanitize_text_field( $_FILES['csv_file']['type'] );
        if ( ! in_array( $mime, $allowed_types, true ) ) {
            return __( 'Invalid file type. Please upload a valid CSV file.', 'wp-warranty-manager' );
        }

        return '';
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

        $error = $this->validate_upload();
        if ( $error ) {
            wp_safe_redirect( add_query_arg(
                array( 'page' => 'warranty-manager', 'import_error' => urlencode( $error ) ),
                admin_url( 'admin.php' )
            ) );
            exit;
        }

        global $wpdb;

        // Normalize line endings: \r\n (Windows) and \r (old Mac) → \n
        $raw   = file_get_contents( $_FILES['csv_file']['tmp_name'] );
        $raw   = str_replace( array( "\r\n", "\r" ), "\n", $raw );
        $lines = array_values( array_filter( array_map( 'trim', explode( "\n", $raw ) ) ) );

        // Skip a header row when the first non-empty cell is the literal column name.
        if ( ! empty( $lines ) ) {
            $first_cell = strtolower( trim( str_getcsv( $lines[0], ',' )[0] ?? '' ) );
            if ( 'warranty_code' === $first_cell ) {
                array_shift( $lines );
            }
        }

        // Parse and sanitize all codes first, discarding blanks and duplicates.
        $codes = array();
        foreach ( $lines as $line ) {
            $code = sanitize_text_field( trim( str_getcsv( $line, ',' )[0] ?? '' ) );
            if ( '' !== $code ) {
                $codes[ $code ] = true; // key-dedup within the file itself
            }
        }
        $codes = array_keys( $codes );

        $imported = 0;

        if ( ! empty( $codes ) ) {
            // Bulk insert in chunks of 200 rows per query to avoid huge SQL strings.
            $chunks = array_chunk( $codes, 200 );

            $wpdb->query( 'START TRANSACTION' );

            foreach ( $chunks as $chunk ) {
                $placeholders = implode( ', ', array_fill( 0, count( $chunk ), "(%s, 'inactive')" ) );
                // phpcs:ignore WordPress.DB.PreparedSQLNotPrepared
                $rows_affected = $wpdb->query(
                    $wpdb->prepare(
                        // INSERT IGNORE silently skips rows that violate the UNIQUE KEY on warranty_code.
                        "INSERT IGNORE INTO {$this->table_name} (warranty_code, status) VALUES {$placeholders}",
                        $chunk
                    )
                );

                if ( false === $rows_affected ) {
                    $wpdb->query( 'ROLLBACK' );
                    wp_safe_redirect( add_query_arg(
                        array( 'page' => 'warranty-manager', 'import_error' => urlencode( __( 'Database error during import. No codes were saved.', 'wp-warranty-manager' ) ) ),
                        admin_url( 'admin.php' )
                    ) );
                    exit;
                }

                $imported += (int) $rows_affected;
            }

            $wpdb->query( 'COMMIT' );
        }

        wp_safe_redirect( add_query_arg(
            array( 'page' => 'warranty-manager', 'imported' => $imported ),
            admin_url( 'admin.php' )
        ) );
        exit;
    }

    /**
     * Stream a sample CSV template for download.
     *
     * Serves a small example file in the exact format the importer expects:
     * a single warranty_code column, one code per row. The first row is the
     * literal `warranty_code` header, which import() detects and skips.
     *
     * Called via admin-post.php on the download_warranty_sample action.
     *
     * @since  1.0.0
     * @return void
     */
    public function download_sample() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( esc_html__( 'Unauthorized access.', 'wp-warranty-manager' ) );
        }

        check_admin_referer( 'download_warranty_sample_nonce' );

        nocache_headers();
        header( 'Content-Type: text/csv; charset=utf-8' );
        header( 'Content-Disposition: attachment; filename=sample-warranty-codes.csv' );

        // Plain literal output: the header row matches the column import() recognizes and skips.
        echo "warranty_code\r\nABC123456\r\nXYZ987654\r\nTEST112233\r\n";
        exit;
    }
}