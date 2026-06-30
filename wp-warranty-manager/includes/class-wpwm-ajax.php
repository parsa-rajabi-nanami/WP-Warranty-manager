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

        $this->table_name = $wpdb->prefix . WPWM_TABLE_WARRANTY_CODES;
    }

/**
     * Return the client IP address.
     *
     * Uses REMOTE_ADDR only. X-Forwarded-For is intentionally ignored
     * because it can be forged; this value is used only for rate-limiting
     * and informational storage, not for security decisions.
     *
     * @since  1.0.0
     * @return string
     */
    private function get_client_ip()
    {
        // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash
        return sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ?? '' ) );
    }

    /**
     * Check and increment the activation rate-limit counter for an IP.
     *
     * Allows at most WPWM_RATE_LIMIT_MAX attempts per WPWM_RATE_LIMIT_WINDOW
     * seconds per IP address. Uses a WordPress transient as the counter.
     *
     * @since  1.0.0
     * @return bool  True when the request is within the allowed limit, false when blocked.
     */
    private function check_rate_limit()
    {
        $ip           = $this->get_client_ip();
        $transient    = 'wpwm_rl_' . md5( $ip );
        $attempts     = (int) get_transient( $transient );

        if ( $attempts >= WPWM_RATE_LIMIT_MAX ) {
            return false;
        }

        if ( $attempts === 0 ) {
            set_transient( $transient, 1, WPWM_RATE_LIMIT_WINDOW );
        } else {
            // Preserve the original expiry by just incrementing the stored value.
            set_transient( $transient, $attempts + 1, WPWM_RATE_LIMIT_WINDOW );
        }

        return true;
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

        if ( ! $this->check_rate_limit() ) {
            wp_send_json_error( array(
                'message' => __( 'Too many attempts. Please try again later.', 'wp-warranty-manager' ),
            ), 429 );
        }

        global $wpdb;

        // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash
        $code = sanitize_text_field( wp_unslash( $_POST['warranty_code'] ?? '' ) );

        if (empty($code)) {
            wp_send_json_error(array(
                'message' => __('Please enter a warranty code.', 'wp-warranty-manager'),
            ));
        }

        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
        $record = $wpdb->get_row( $wpdb->prepare(
            // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
            "SELECT * FROM {$this->table_name} WHERE warranty_code = %s",
            $code
        ) );

        if (! $record) {
            wp_send_json_error(array(
                'message' => __('Warranty code not found.', 'wp-warranty-manager'),
            ));
        }

        if ('active' === $record->status) {
            wp_send_json_error(array(
                'message' => sprintf(
                    /* translators: %s: expiration date */
                    __('This warranty has already been activated.<br>Expiration date: %s', 'wp-warranty-manager'),
                    esc_html(WPWM_Date_Helper::format($record->expires_at, 'Y/m/d'))
                ),
            ));
        }

        $activated_at = current_time('mysql');
        $expires_at   = wp_date('Y-m-d H:i:s', strtotime('+1 year', current_time('timestamp')));
        $ip           = $this->get_client_ip();

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
                /* translators: %s: expiration date */
                __('Warranty activated successfully.<br>Expiration date: %s', 'wp-warranty-manager'),
                esc_html(WPWM_Date_Helper::format($expires_at, 'Y/m/d'))
            ),
        ));
    }
}
