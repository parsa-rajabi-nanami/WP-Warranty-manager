<?php

/**
 * AJAX handler for warranty activation.
 *
 * @link       https://github.com/parsa-rajabi-nanami/WP-Warranty-manager
 * @since      1.0.0
 *
 * @package    WP_Warranty_Manager
 * @subpackage WP_Warranty_Manager/includes
 */
if (! defined('ABSPATH')) {
    exit;
}

/**
 * AJAX handler class.
 *
 * Handles all AJAX requests for warranty activation.
 * Hooks are registered via WPWM_Loader inside WP_Warranty_Manager::define_ajax_hooks().
 *
 * @since      1.0.0
 * @package    WP_Warranty_Manager
 * @subpackage WP_Warranty_Manager/includes
 * @author     Parsa Rajabi
 */
class WPWM_Ajax
{
    /**
     * Full name of the warranty codes table, including the WP prefix.
     *
     * @since  1.0.0
     * @var    string
     */
    private $table_name;

    /**
     * Initialize AJAX class and resolve the table name.
     *
     * @since  1.0.0
     */
    public function __construct()
    {
        global $wpdb;

        $this->table_name = $wpdb->prefix . 'warranty_codes';
    }

    /**
     * Handle warranty activation AJAX request.
     *
     * Validates the submitted warranty code, checks its current status,
     * activates it if eligible, and returns a JSON response.
     *
     * Mapped to wp_ajax_wp_activate_warranty and
     * wp_ajax_nopriv_wp_activate_warranty via the loader.
     *
     * @since  1.0.0
     * @return void
     */
    public function activate_warranty()
    {
        check_ajax_referer('wp_warranty_ajax_nonce', 'nonce');

        global $wpdb;

        $code = sanitize_text_field($_POST['warranty_code'] ?? '');

        if (empty($code)) {
            wp_send_json_error(array(
                'message' => __('Please enter a warranty code.', 'wp-warranty-manager'),
            ));
        }

        $record = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$this->table_name} WHERE warranty_code = %s",
            $code
        ));

        if (! $record) {
            wp_send_json_error(array(
                'message' => __('Warranty code not found.', 'wp-warranty-manager'),
            ));
        }

        if ('active' === $record->status) {
            wp_send_json_error(array(
                'message' => sprintf(
                    __('This warranty has already been activated.<br>Expiration date: %s', 'wp-warranty-manager'),
                    esc_html($this->to_jalali($record->expires_at, 'Y/m/d'))
                ),
            ));
        }

        $activated_at = current_time('mysql');
        $expires_at   = date('Y-m-d H:i:s', strtotime('+1 year', current_time('timestamp')));
        $ip           = sanitize_text_field($_SERVER['REMOTE_ADDR'] ?? '');

        $wpdb->update(
            $this->table_name,
            array(
                'status'       => 'active',
                'activated_at' => $activated_at,
                'expires_at'   => $expires_at,
                'customer_ip'  => $ip,
            ),
            array('id' => $record->id),
            array('%s', '%s', '%s', '%s'),
            array('%d')
        );

        wp_send_json_success(array(
            'message' => sprintf(
                __('Warranty activated successfully.<br>Expiration date: %s', 'wp-warranty-manager'),
                esc_html($this->to_jalali($expires_at, 'Y/m/d'))
            ),
        ));
    }
}
