<?php

/**
 * Admin write-action handlers (edit and delete).
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
 * Handles admin-post.php write actions for warranty records.
 *
 * Owns the edit and delete mutation paths. Registered separately from
 * WPWM_Admin (which owns read-only rendering) so each class has a single
 * responsibility.
 *
 * @since      1.0.0
 * @package    WP_Warranty_Manager
 * @subpackage WP_Warranty_Manager/admin
 * @author     Parsa Rajabi
 */
class WPWM_Admin_Actions {

    /**
     * Full name of the warranty codes table, including the WP prefix.
     *
     * @since  1.0.0
     * @var    string
     */
    private $table_name;

    /**
     * Initialize the class.
     *
     * @since  1.0.0
     */
    public function __construct() {
        global $wpdb;

        $this->table_name = $wpdb->prefix . WPWM_TABLE_WARRANTY_CODES;
    }

    /**
     * Process edit form submission for a single warranty record.
     *
     * Called via admin-post.php on the edit_warranty_code action.
     *
     * @since  1.0.0
     * @return void
     */
    public function edit_code() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( esc_html__( 'Unauthorized access.', 'wp-warranty-manager' ) );
        }

        check_admin_referer( 'edit_warranty_code_nonce' );

        global $wpdb;

        $id            = absint( $_POST['id'] );
        $warranty_code = sanitize_text_field( $_POST['warranty_code'] );
        $status        = in_array( $_POST['status'], array( 'active', 'inactive' ), true ) ? $_POST['status'] : 'inactive';
        $activated_at  = ! empty( $_POST['activated_at'] ) ? sanitize_text_field( str_replace( 'T', ' ', $_POST['activated_at'] ) ) . ':00' : null;
        $expires_at    = ! empty( $_POST['expires_at'] )   ? sanitize_text_field( str_replace( 'T', ' ', $_POST['expires_at'] ) )   . ':00' : null;
        $customer_ip   = sanitize_text_field( $_POST['customer_ip'] );

        if ( empty( $warranty_code ) || ! $id ) {
            wp_redirect( admin_url( 'admin.php?page=warranty-manager&error=invalid' ) );
            exit;
        }

        $wpdb->update(
            $this->table_name,
            array(
                'warranty_code' => $warranty_code,
                'status'        => $status,
                'activated_at'  => $activated_at,
                'expires_at'    => $expires_at,
                'customer_ip'   => $customer_ip,
            ),
            array( 'id' => $id ),
            array( '%s', '%s', '%s', '%s', '%s' ),
            array( '%d' )
        );

        wp_redirect( admin_url( 'admin.php?page=warranty-edit&id=' . $id . '&updated=1' ) );
        exit;
    }

    /**
     * Process delete form submission for a single warranty record.
     *
     * Called via admin-post.php on the delete_warranty_code action.
     *
     * @since  1.0.0
     * @return void
     */
    public function delete_code() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( esc_html__( 'Unauthorized access.', 'wp-warranty-manager' ) );
        }

        check_admin_referer( 'delete_warranty_code_nonce' );

        global $wpdb;

        $id = absint( $_POST['id'] );

        if ( ! $id ) {
            wp_redirect( admin_url( 'admin.php?page=warranty-manager' ) );
            exit;
        }

        $wpdb->delete(
            $this->table_name,
            array( 'id' => $id ),
            array( '%d' )
        );

        wp_redirect( admin_url( 'admin.php?page=warranty-manager&deleted=1' ) );
        exit;
    }
}
