<?php
/**
 * Plugin Name: BD Domain Checker
 * Description: BD Domain Availability Checker with modular structure.
 * Version: 1.0
 * Author: DOT.COM.BD
 */

if (!defined('ABSPATH')) exit;

// Enqueue CSS & JS assets
function bdc_enqueue_assets() {
    wp_enqueue_style('bdc-style', plugin_dir_url(__FILE__) . 'assets/css/style.css');
    wp_enqueue_script('bdc-script', plugin_dir_url(__FILE__) . 'assets/js/script.js', ['jquery'], false, true);

    wp_localize_script('bdc-script', 'bdAjax', [
        'ajaxurl' => admin_url('admin-ajax.php'),
        'nonce'   => wp_create_nonce('bdc_nonce')
    ]);
}
add_action('wp_enqueue_scripts', 'bdc_enqueue_assets');

// Include domain checker logic
require_once plugin_dir_path(__FILE__) . 'includes/checker.php';

// Register shortcode for form display
function bdc_form_shortcode() {
    return file_get_contents(plugin_dir_path(__FILE__) . 'templates/form-layout.php');
}
add_shortcode('bd_domain_checker', 'bdc_form_shortcode');
