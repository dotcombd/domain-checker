<?php
/**
 * Plugin Name: BD Domain Checker
 * Description: Simple .bd domain availability checker (DNS Lookup Version)
 * Version: 1.0
 * Author: DOT.COM.BD
 */

// ✅ AJAX হ্যান্ডলার
add_action('wp_ajax_bd_domain_checker', 'bd_domain_checker_callback');
add_action('wp_ajax_nopriv_bd_domain_checker', 'bd_domain_checker_callback');

function bd_domain_checker_callback() {
    check_ajax_referer('bd_checker_nonce', 'security');

    if (!isset($_POST['domain']) || empty($_POST['domain'])) {
        wp_send_json_error(['message' => '❌ Please enter a domain']);
    }

    $domain = sanitize_text_field($_POST['domain']);
    $clean_domain = preg_replace('/^https?:\/\//', '', $domain);
    $clean_domain = preg_replace('/^www\./', '', $clean_domain);

    // ✅ মেইন ডোমেইন DNS Lookup
    $main_available = bd_check_dns_availability($clean_domain);

    $main_result = $main_available
        ? "🎉 Congratulations! ✅ <strong>{$domain}</strong> is Available for Registration."
        : "❌ Domain <strong>{$domain}</strong> is NOT Available";

    // ✅ অন্যান্য এক্সটেনশন
    $extensions = [
        '.com.bd','.edu.bd','.gov.bd','.net.bd',
        '.org.bd','.ac.bd','.mil.bd','.info.bd','.বাংলা'
    ];
    $current_ext = substr($domain, strpos($domain, '.'));
    $other_exts = array_diff($extensions, [$current_ext]);
    $base_name = preg_replace('/\..*$/', '', $domain);

    $other_results = "<div class='other-ext-results'><h4>Here are some other options:</h4><ul>";
    foreach ($other_exts as $ext) {
        $alt_domain = $base_name . $ext;
        $alt_available = bd_check_dns_availability($alt_domain);
        $status = $alt_available ? "✅ Available" : "❌ Taken";
        $other_results .= "<li><strong>{$alt_domain}</strong> → {$status}</li>";
    }
    $other_results .= "</ul></div>";

    wp_send_json_success(['message' => $main_result . $other_results]);
}

// ✅ DNS Lookup ফাংশন (আগের মতোই)
function bd_check_dns_availability($domain) {
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

    return !$has_records; // রেকর্ড না থাকলে Available
}

// ✅ Shortcode → [bd_domain_checker]
function bd_domain_checker_shortcode() {
    ob_start(); ?>
    <div class="bd-domain-checker-wrapper">
        <h2 class="bd-domain-title">BD Domain Checker</h2>

        <div class="bd-domain-form">
            <input type="text" id="bd-domain-input" placeholder="Enter domain name">
            <select id="bd-domain-ext">
                <option value=".com.bd">.com.bd</option>
                <option value=".edu.bd">.edu.bd</option>
                <option value=".gov.bd">.gov.bd</option>
                <option value=".net.bd">.net.bd</option>
                <option value=".org.bd">.org.bd</option>
                <option value=".ac.bd">.ac.bd</option>
                <option value=".mil.bd">.mil.bd</option>
                <option value=".info.bd">.info.bd</option>
                <option value=".বাংলা">.বাংলা</option>
            </select>
            <button id="bd-domain-submit">Search</button>
        </div>

        <div id="bd-domain-result"></div>

        <p class="bd-domain-welcome">
            Welcome to the World of <span class="bangla">.বাংলা</span> & <span class="bd">.bd</span> Domain Service
        </p>
    </div>
    <?php return ob_get_clean();
}
add_shortcode('bd_domain_checker', 'bd_domain_checker_shortcode');

// ✅ CSS + JS আগের মতোই লোড হবে
function bd_checker_assets() {
    wp_enqueue_style('bd-checker-style', plugin_dir_url(__FILE__).'style.css');
    wp_enqueue_script('bd-checker-js', plugin_dir_url(__FILE__).'checker.js', ['jquery'], '1.1', true);
    wp_localize_script('bd-checker-js', 'bdAjax', [
        'ajaxurl' => admin_url('admin-ajax.php'),
        'nonce'   => wp_create_nonce('bd_checker_nonce')
    ]);
}
add_action('wp_enqueue_scripts', 'bd_checker_assets');
