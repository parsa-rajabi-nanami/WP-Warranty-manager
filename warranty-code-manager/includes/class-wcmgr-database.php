<?php

/**
 * Database management functionality.
 *
 * Handles creation and updates of the plugin database tables.
 *
 * @since      1.0.0
 *
 * @package    Warranty_Code_Manager
 * @subpackage Warranty_Code_Manager/includes
 */

/**
 * Database handler class.
 *
 * Responsible for creating and maintaining the database tables
 * required by the Warranty Code Manager plugin.
 *
 * @since      1.0.0
 * @package    Warranty_Code_Manager
 * @subpackage Warranty_Code_Manager/includes
 * @author     Parsa Rajabi
 */
if (!defined('ABSPATH')) {
    exit;
}

class WCMGR_Database
{
    /**
     * Warranty table name.
     *
     * @var string
     */
    private $table_name;

    /**
     * Initialize database class.
     */
    public function __construct()
    {
        global $wpdb;

        $this->table_name = $wpdb->prefix . WCMGR_TABLE_WARRANTY_CODES;
    }

    /**
     * Create plugin database tables.
     *
     * Runs during plugin activation and plugin upgrades.
     *
     * @return void
     */
    public function create_tables()
    {
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE {$this->table_name} (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            warranty_code VARCHAR(100) NOT NULL,
            status VARCHAR(20) NOT NULL DEFAULT 'inactive',
            activated_at DATETIME NULL,
            expires_at DATETIME NULL,
            customer_ip VARCHAR(45) NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY warranty_code (warranty_code),
            KEY status (status)
        ) $charset_collate;";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        dbDelta($sql);

        update_option(
            'wcmgr_db_version',
            '1.0.0'
        );
    }
}
