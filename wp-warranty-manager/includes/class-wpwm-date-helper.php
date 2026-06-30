<?php

/**
 * Date formatting utility.
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
 * Shared date helper.
 *
 * Converts Gregorian datetime strings to Jalali (Persian) calendar when the
 * WP-Parsidate plugin is active, and falls back to Gregorian wp_date() otherwise.
 *
 * @since  1.0.0
 */
class WPWM_Date_Helper {

    /**
     * Format a MySQL datetime string.
     *
     * Returns '—' for empty/null values. Delegates to parsidate() when
     * available; falls back to wp_date() which respects the WP site timezone.
     *
     * @since  1.0.0
     * @param  string $datetime  MySQL datetime string (e.g. '2024-01-15 10:30:00').
     * @param  string $format    Date/time format string (default 'Y/m/d H:i').
     * @return string            Formatted date string, or '—' when input is empty.
     */
    public static function format( $datetime, $format = 'Y/m/d H:i' ) {
        if ( empty( $datetime ) ) {
            return '—';
        }

        $timestamp = strtotime( $datetime );

        if ( function_exists( 'parsidate' ) ) {
            return parsidate( $format, $timestamp );
        }

        return wp_date( $format, $timestamp );
    }
}
