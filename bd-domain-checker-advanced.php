<?php
/**
 * Plugin Name: BD Domain Checker Advanced
 * Description: Advanced BD Domain Checker with multi-extension, Dashboard price settings & official responsive design.
 * Version: 1.0
 * Author: DOT.COM.BD
 */

if (!defined('ABSPATH')) exit;

/** ✅ Define Paths */
define('BDDC_PATH', plugin_dir_path(__FILE__));
define('BDDC_URL', plugin_dir_url(__FILE__));

/** ✅ Include Files */
require_once BDDC_PATH.'includes/helpers.php';
require_once BDDC_PATH.'includes/ajax-handler.php';
require_once BDDC_PATH.'includes/admin-page.php';

/** ✅ Enqueue Scripts */
add_action('wp_enqueue_scripts', function(){
    wp_enqueue_style('bdc-style', BDDC_URL.'assets/style.css');
    wp_enqueue_script('bdc-js', BDDC_URL.'assets/checker.js',['jquery'],false,true);
    wp_localize_script('bdc-js','bdChecker',['ajax_url'=>admin_url('admin-ajax.php')]);
});

/** ✅ Shortcode */
add_shortcode('bd_domain_checker', function(){
    ob_start();
    include BDDC_PATH.'templates/form-template.php';
    return ob_get_clean();
});
