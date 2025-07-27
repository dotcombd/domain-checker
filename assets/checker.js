<?php
if (!defined('ABSPATH')) exit;

add_action('wp_ajax_bdc_check_domain', 'bdc_check_domain');
add_action('wp_ajax_nopriv_bdc_check_domain', 'bdc_check_domain');

function bdc_check_domain() {
    check_ajax_referer('bdc_nonce', 'security');

    $name = sanitize_text_field($_POST['name'] ?? '');
    if (!$name) {
        wp_send_json_error(['message' => '❌ Please enter a domain name']);
    }

    $extensions = [
        '.com.bd', '.net.bd', '.org.bd',
        '.edu.bd', '.gov.bd', '.ac.bd',
        '.mil.bd', '.info.bd', '.বাংলা'
    ];

    $results = [];
    foreach ($extensions as $ext) {
        $domain = $name . $ext;

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

        $results[] = [
            'domain' => $domain,
            'status' => !$has_records
                ? "✅ {$domain} is Available"
                : "❌ {$domain} is Already Registered"
        ];
    }

    wp_send_json_success(['results' => $results]);
}
