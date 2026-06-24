<?php

/**
 * Shortcode functionality of the plugin.
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
 * Shortcode handler class.
 *
 * Registers and renders the [warranty_form] shortcode.
 *
 * @since      1.0.0
 * @package    WP_Warranty_Manager
 * @subpackage WP_Warranty_Manager/includes
 * @author     Parsa Rajabi
 */
class WPWM_Shortcodes {

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
     * Usage: [warranty_form]
     *
     * @since  1.0.0
     * @return string  HTML output of the form.
     */
    public function render_warranty_form() {
        ob_start();
        ?>
        <div class="wp-warranty-box">
            <form id="wp-warranty-form">
                <input
                    type="text"
                    name="warranty_code"
                    placeholder="<?php esc_attr_e( 'Enter your warranty code', 'wp-warranty-manager' ); ?>"
                    required
                >
                <button type="submit">
                    <?php esc_html_e( 'Activate Warranty', 'wp-warranty-manager' ); ?>
                </button>
            </form>
            <div class="wp-warranty-message"></div>
        </div>
        <?php
        return ob_get_clean();
    }
}