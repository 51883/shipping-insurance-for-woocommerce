<?php
/**
 * Plugin Name: Shipping Insurance for WooCommerce
 * Plugin URI: https://www.wpcocktail.com/e-commerce-shipping-insurance
 * Description: Adds a shipping insurance option to the WooCommerce checkout page.
 * Version: 1.0.0
 * Author: WPCocktail
 * Author URI: https://www.wpcocktail.com/
 * License:  GPLv3 or later
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: shipping-insurance-for-woocommerce
 * Domain Path: /languages
 * Tested up to: 6.1
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Prefix all functions with "esi_" to make them unique
// Add shipping insurance field to the checkout page
function esi_add_shipping_insurance_field( $checkout ) {
    // Sanitize and validate options
    $default_state = get_option( 'woocommerce_shipping_insurance_default_state', 'yes' ) === 'yes' ? 1 : 0;
    $label_text = sanitize_text_field( get_option( 'woocommerce_shipping_insurance_label_text', 'Yes, I would like to protect my package with shipping insurance' ) );

    woocommerce_form_field( 'shipping_insurance', array(
        'type'    => 'checkbox',
        'class'   => array( 'form-row-wide' ),
        'label'   => esc_html( $label_text ),
        'default' => $default_state,
    ), 1 );
}
add_action( 'woocommerce_after_order_notes', 'esi_add_shipping_insurance_field' );

// Add shipping insurance fee to the cart based on the admin setting
function esi_add_shipping_insurance_fee( $cart ) {
    // Sanitize input data
    if ( isset( $_POST['post_data'] ) ) {
        parse_str( sanitize_text_field( $_POST['post_data'] ), $post_data );
    } else {
        $post_data = filter_input_array( INPUT_POST, FILTER_SANITIZE_STRING );
    }

    // Validate input data
    if ( isset( $post_data['shipping_insurance'] ) && $post_data['shipping_insurance'] == '1' ) {
        $shipping_insurance_fee = floatval( get_option( 'woocommerce_shipping_insurance_price', 2.99 ) );
        $cart->add_fee( __( 'Shipping Insurance', 'woocommerce' ), $shipping_insurance_fee );
    }
}
add_action( 'woocommerce_cart_calculate_fees', 'esi_add_shipping_insurance_fee', 10, 1 );

// Add shipping insurance price, default state, and label text settings to the WooCommerce settings page
function esi_add_shipping_insurance_settings( $settings ) {
    $updated_settings = array();
    foreach ( $settings as $section ) {
        if ( isset( $section['id'] ) && 'shipping_options' === $section['id'] && isset( $section['type'] ) && 'sectionend' === $section['type'] ) {
            $updated_settings[] = array(
                'title' => __( 'Shipping Insurance Price', 'woocommerce' ),
                'desc'  => __( 'Enter the shipping insurance price (USD).', 'woocommerce' ),
                'id'    => 'woocommerce_shipping_insurance_price',
                'default' => '2.99',
                'type'  => 'text',
                'desc_tip' => true,
            );

            $updated_settings[] = array(
                'title'   => __( 'Shipping Insurance Default State', 'woocommerce' ),
                'desc' => __( 'Enable this to make the shipping insurance checkbox checked by default on the checkout page.', 'woocommerce' ),
                'id' => 'woocommerce_shipping_insurance_default_state',
                'default' => 'yes',
                'type' => 'checkbox',
                );

            $updated_settings[] = array(
                'title'   => __( 'Shipping Insurance Label Text', 'woocommerce' ),
                'desc'    => __( 'Enter the label text for the shipping insurance checkbox on the checkout page.', 'woocommerce' ),
                'id'      => 'woocommerce_shipping_insurance_label_text',
                'default' => 'Yes, I would like to protect my package with shipping insurance',
                'type'    => 'text',
                'desc_tip' => true,
            );
        }
        $updated_settings[] = $section;
    }
    return $updated_settings;
}
add_filter( 'woocommerce_shipping_settings', 'esi_add_shipping_insurance_settings' );

// Ensure the cart is updated when shipping insurance is toggled
function esi_enqueue_shipping_insurance_scripts() {
    if ( is_checkout() ) {
        wp_enqueue_script( 'shipping-insurance', plugins_url( '/js/shipping-insurance.js', FILE ), array( 'jquery', 'wc-checkout' ), '1.0.0', true );
    }
}
add_action( 'wp_enqueue_scripts', 'esi_enqueue_shipping_insurance_scripts' );


// Add shipping insurance price, default state, and label text settings to the WooCommerce settings page
function add_shipping_insurance_settings( $settings ) {
    $updated_settings = array();
    foreach ( $settings as $section ) {
        if ( isset( $section['id'] ) && 'shipping_options' === $section['id'] && isset( $section['type'] ) && 'sectionend' === $section['type'] ) {
            $updated_settings[] = array(
                'title' => __( 'Shipping Insurance Price', 'woocommerce' ),
                'desc'  => __( 'Enter the shipping insurance price (USD).', 'woocommerce' ),
                'id'    => 'woocommerce_shipping_insurance_price',
                'default' => '2.99',
                'type'  => 'text',
                'desc_tip' => true,
            );

            $updated_settings[] = array(
                'title'   => __( 'Shipping Insurance Default State', 'woocommerce' ),
                'desc'    => __( 'Enable this to make the shipping insurance checkbox checked by default on the checkout page.', 'woocommerce' ),
                'id'      => 'woocommerce_shipping_insurance_default_state',
                'default' => 'yes',
                'type'    => 'checkbox',
            );

            $updated_settings[] = array(
                'title'   => __( 'Shipping Insurance Label Text', 'woocommerce' ),
                            'desc'    => __( 'Enter the label text for the shipping insurance checkbox on the checkout page.', 'woocommerce' ),
            'id'      => 'woocommerce_shipping_insurance_label_text',
            'default' => 'Yes, I would like to protect my package with shipping insurance',
            'type'    => 'text',
            'desc_tip' => true,
        );
    }
    $updated_settings[] = $section;
}
return $updated_settings;
}
add_filter( 'woocommerce_shipping_settings', 'add_shipping_insurance_settings' );

// Ensure the cart is updated when shipping insurance is toggled
function enqueue_shipping_insurance_scripts() {
    if ( is_checkout() ) {
        wp_enqueue_script( 'shipping-insurance', plugins_url( '/js/shipping-insurance.js', __FILE__ ), array( 'jquery', 'wc-checkout' ), '1.0.0', true );
    }
}
add_action( 'wp_enqueue_scripts', 'enqueue_shipping_insurance_scripts' );
