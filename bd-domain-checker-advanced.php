<?php
/**
 * Plugin Name: BD Domain Checker
 * Description: Multi-extension BD domain checker with DNS Lookup.
 * Version: 1.0
 * Author: DOT.COM.BD
 */

if (!defined('ABSPATH')) exit;

// ✅ CSS + JS লোড
add_action('wp_enqueue_scripts', function(){
    wp_enqueue_style('bdc-style', plugin_dir_url(__FILE__).'style.css');
    wp_enqueue_script('bdc-js', plugin_dir_url(__FILE__).'checker.js', ['jquery'], false, true);
    wp_localize_script('bdc-js', 'bdAjax', [
        'ajaxurl' => admin_url('admin-ajax.php'),
        'nonce'   => wp_create_nonce('bdc_nonce')
    ]);
});

// ✅ শর্টকোড ফর্ম
add_shortcode('bd_domain_checker', function(){
    ob_start(); ?>
    
    <div class="bdc-wrapper">
        <h2 class="bdc-title">BD Domain Availability Checker</h2>

        <div class="bdc-form">
            <input type="text" id="bd-domain-input" placeholder="Enter domain name (without extension)">
            <button id="bd-domain-submit">Search</button>
        </div>

        <div id="bd-domain-result"></div>
    </div>

    <?php return ob_get_clean();
});

// ✅ AJAX Hook
add_action('wp_ajax_bdc_check_domain', 'bdc_check_domain');
add_action('wp_ajax_nopriv_bdc_check_domain', 'bdc_check_domain');

function bdc_check_domain(){
    check_ajax_referer('bdc_nonce', 'security');

    $name = sanitize_text_field($_POST['name'] ?? '');
    if(!$name){
        wp_send_json_error(['message' => '❌ Please enter a domain name']);
    }

    // সব এক্সটেনশন লিস্ট
    $extensions = [
        '.com.bd', '.net.bd', '.org.bd',
        '.edu.bd', '.gov.bd', '.ac.bd',
        '.mil.bd', '.info.bd', '.বাংলা'
    ];

    $results = [];
    foreach($extensions as $ext){
        $domain = $name.$ext;

        // ✅ DNS Lookup
        $has_records = false;
        if (function_exists('dns_get_record')) {
            $records = @dns_get_record($domain, DNS_A + DNS_AAAA + DNS_CNAME + DNS_MX);
            if ($records && count($records) > 0) {
                $has_records = true;
            }
        }
        if (!$has_records && function_exists('checkdnsrr')) {
            if (checkdnsrr($domain, "A") || checkdnsrr($domain, "MX")) {
                $has_records = true;
            }
        }

        // ✅ Available = true যদি কোনো রেকর্ড নাই
        $results[] = [
            'domain' => $domain,
            'status' => !$has_records 
                ? "✅ {$domain} is Available" 
                : "❌ {$domain} is Already Registered"
        ];
    }

    wp_send_json_success(['results' => $results]);
}
