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

    // ✅ Real WHOIS Query
    $available = bddc_whois_check($domain);

    $msg = $available 
        ? "🎉 Congratulations! ✅ {$domain} is Available for Registration."
        : "❌ Sorry, {$domain} is Already Taken.";

    wp_send_json_success(['message'=>$msg]);
}

/** ✅ WHOIS Query Function */
function bddc_whois_check($domain){
    $whois_server = "whois.btcl.net.bd";
    $port = 43;

    $fp = @fsockopen($whois_server, $port, $errno, $errstr, 10);
    if(!$fp){
        return false; // fallback: assume not available
    }

    fwrite($fp, $domain."\r\n");
    $response = '';
    while(!feof($fp)){
        $response .= fgets($fp, 128);
    }
    fclose($fp);

    // যদি "No entries found" বা ফাঁকা আসে = Available
    if(stripos($response, 'No entries') !== false || empty(trim($response))){
        return true;
    }
    return false; // otherwise Taken
}
