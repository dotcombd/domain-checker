<?php
if (!defined('ABSPATH')) exit;

/** ✅ AJAX Handler */
add_action('wp_ajax_bddc_check','bddc_ajax_check');
add_action('wp_ajax_nopriv_bddc_check','bddc_ajax_check');

function bddc_ajax_check(){
    $name = sanitize_text_field($_POST['domain']); // user input
    $exts = get_option('bdc_ext_prices', bddc_default_prices());

    echo '<div class="multi-results">';
    foreach($exts as $ext => $data){
        $full = $name.$ext;

        // ✅ REAL WHOIS CHECK
        $available = bddc_real_check($full);

        if($available){
            echo '<div class="result-card available">
                    <div class="result-left">
                        <span class="domain-name">🎉 Congratulations! <strong>'.$full.'</strong> is Available for Registration</span>
                        <span class="domain-price">💰 ৳'.$data['price'].'/year</span>
                    </div>
                    <div class="result-right">
                        <a href="'.$data['url'].'" class="buy-btn">Buy Now</a>
                    </div>
                  </div>';
        } else {
            echo '<div class="result-card taken">
                    <span class="domain-name">❌ '.$full.' is already taken</span>
                  </div>';
        }
    }
    echo '</div>';
    wp_die();
}
