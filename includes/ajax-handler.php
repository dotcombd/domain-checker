<?php
if (!defined('ABSPATH')) exit;

add_action('wp_ajax_bddc_check_domain', 'bddc_check_domain');
add_action('wp_ajax_nopriv_bddc_check_domain', 'bddc_check_domain');

function bddc_check_domain(){
    check_ajax_referer('bddc_nonce', 'security');

    $domain = sanitize_text_field($_POST['domain'] ?? '');
    if(!$domain){
        wp_send_json_error(['message' => '❌ No domain received']);
    }

    // === আগে DNS Lookup ছিল ===
    $available = bddc_dns_check($domain);

    $msg = $available 
        ? "✅ {$domain} is Available" 
        : "❌ {$domain} is Taken";

    wp_send_json_success(['message'=>$msg]);
}
