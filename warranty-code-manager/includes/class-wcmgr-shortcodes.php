<?php

/**
 * Shortcode functionality of the plugin.
 *
 * @since      1.0.0
 *
 * @package    Warranty_Code_Manager
 * @subpackage Warranty_Code_Manager/includes
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Shortcode handler class.
 *
 * Registers and renders the [warranty_form] shortcode.
 *
 * @since      1.0.0
 * @package    Warranty_Code_Manager
 * @subpackage Warranty_Code_Manager/includes
 * @author     Parsa Rajabi
 */
class WCMGR_Shortcodes {

    /**
     * Register plugin shortcodes with WordPress.
     *
     * Called on the 'init' hook via the loader.
     *
     * @since  1.0.0
     * @return void
     */
    public function register_shortcode() {
        add_shortcode( 'warranty_form', array( $this, 'render_warranty_form' ) );
    }

    /**
     * Render the warranty activation form.
     *
     * Enqueues front-end assets on demand so they are only loaded on
     * pages that actually contain the [warranty_form] shortcode.
     *
     * Usage: [warranty_form]
     *
     * @since  1.0.0
     * @return string  HTML output of the form.
     */
    public function render_warranty_form() {
        wp_enqueue_style(
            'warranty-code-manager',
            WCMGR_PLUGIN_URL . 'public/css/wcmgr-public.css',
            array(),
            WCMGR_VERSION
        );

        wp_enqueue_script(
            'warranty-code-manager',
            WCMGR_PLUGIN_URL . 'public/js/wcmgr-public.js',
            array( 'jquery' ),
            WCMGR_VERSION,
            true
        );

        wp_localize_script( 'warranty-code-manager', 'warrantyCodeManager', array(
            'ajax_url'     => admin_url( 'admin-ajax.php' ),
            'nonce'        => wp_create_nonce( 'wcmgr_warranty_ajax_nonce' ),
            'msg_checking' => __( 'Checking...', 'warranty-code-manager' ),
            'msg_error'    => __( 'Server error occurred.', 'warranty-code-manager' ),
        ) );

        ob_start();
        ?>
        <div class="wcmgr-warranty-box">
            <form id="wcmgr-warranty-form">
                <input
                    type="text"
                    name="warranty_code"
                    placeholder="<?php esc_attr_e( 'Enter your warranty code', 'warranty-code-manager' ); ?>"
                    required
                >
                <button type="submit">
                    <?php esc_html_e( 'Activate Warranty', 'warranty-code-manager' ); ?>
                </button>
            </form>
            <div class="wcmgr-warranty-message"></div>
        </div>
        <?php
        return ob_get_clean();
    }
}