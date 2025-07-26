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

    // ✅ Try WHOIS first
    $whois_status = bddc_whois_check($domain);

    if($whois_status === 'available'){
        $msg = "🎉 Congratulations! ✅ {$domain} is Available for Registration.";
    } elseif($whois_status === 'taken'){
        $msg = "❌ Sorry, {$domain} is Already Taken.";
    } else {
        // WHOIS failed → fallback to DNS lookup
        $dns_available = bddc_dns_lookup($domain);
        $msg = $dns_available 
            ? "🎉 (Fallback) ✅ {$domain} seems Available."
            : "❌ (Fallback) {$domain} is Taken.";
    }

    wp_send_json_success(['message'=>$msg]);
}

/** ✅ WHOIS Query */
function bddc_whois_check($domain){
    $whois_server = "whois.btcl.net.bd";
    $port = 43;

    $fp = @fsockopen($whois_server, $port, $errno, $errstr, 10);
    if(!$fp){
        return 'error'; // WHOIS unavailable
    }

    fwrite($fp, $domain."\r\n");
    $response = '';
    while(!feof($fp)){
        $response .= fgets($fp, 128);
    }
    fclose($fp);

    // Debug: log response (optional)
    // file_put_contents(__DIR__.'/whois_log.txt', $response);

    if(stripos($response, 'No entries') !== false || stripos($response, 'Not Found') !== false){
        return 'available';
    }
    if(trim($response) !== ''){
        return 'taken';
    }
    return 'error'; // No response
}

/** ✅ DNS Lookup fallback */
function bddc_dns_lookup($domain){
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
    return !$has_records; // No records = available
}
