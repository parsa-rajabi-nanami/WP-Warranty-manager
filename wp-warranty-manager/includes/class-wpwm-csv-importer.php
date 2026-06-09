<?php

/**
 * CSV import functionality.
 *
 * Handles importing warranty codes from CSV files.
 *
 * @link       https://github.com/parsa-rajabi-nanami/WP-Warranty-manager
 * @since      1.0.0
 *
 * @package    WP_Warranty_Manager
 * @subpackage WP_Warranty_Manager/includes
 */

/**
 * CSV importer class.
 *
 * Responsible for importing warranty codes from uploaded CSV files.
 *
 * @since      1.0.0
 * @package    WP_Warranty_Manager
 * @subpackage WP_Warranty_Manager/includes
 * @author     Parsa Rajabi
 */

if (!defined('ABSPATH')) {
    exit;
}

class WPWM_CSV_Importer
{
    /**
     * Warranty table name.
     *
     * @var string
     */
    private $table_name;

    /**
     * Initialize importer.
     */
    public function __construct()
    {
        global $wpdb;

        $this->table_name = $wpdb->prefix . 'warranty_codes';
    }

    /**
     * Import warranty codes from CSV file.
     *
     * Runs from the admin import form submission.
     *
     * @return void
     */
    public function import()
    {
        if (!current_user_can('manage_options')) {
            wp_die(
                esc_html__(
                    'Unauthorized access.',
                    'wp-warranty-manager'
                )
            );
        }

        check_admin_referer(
            'import_warranty_csv_nonce'
        );

        if (
            empty($_FILES['csv_file']) ||
            empty($_FILES['csv_file']['tmp_name'])
        ) {
            wp_redirect(
                admin_url(
                    'admin.php?page=warranty-manager'
                )
            );
            exit;
        }

        global $wpdb;

        $file = fopen(
            $_FILES['csv_file']['tmp_name'],
            'r'
        );

        if (!$file) {
            wp_redirect(
                admin_url(
                    'admin.php?page=warranty-manager'
                )
            );
            exit;
        }

        while (
            ($data = fgetcsv($file, 1000, ',')) !== false
        ) {
            $code = sanitize_text_field(
                $data[0] ?? ''
            );

            if (empty($code)) {
                continue;
            }

            $exists = $wpdb->get_var(
                $wpdb->prepare(
                    "SELECT id
                    FROM {$this->table_name}
                    WHERE warranty_code = %s",
                    $code
                )
            );

            if (!$exists) {
                $wpdb->insert(
                    $this->table_name,
                    [
                        'warranty_code' => $code,
                        'status' => 'inactive',
                    ],
                    [
                        '%s',
                        '%s',
                    ]
                );
            }
        }

        fclose($file);

        wp_redirect(
            admin_url(
                'admin.php?page=warranty-manager'
            )
        );

        exit;
    }
}