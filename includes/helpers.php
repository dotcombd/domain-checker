<?php
if (!defined('ABSPATH')) exit;

/** ✅ Default Extensions & Price */
function bddc_default_prices(){
    return [
        '.com.bd' => ['price'=>800, 'url'=>'#'],
        '.edu.bd' => ['price'=>500, 'url'=>'#'],
        '.gov.bd' => ['price'=>0, 'url'=>'#'],
        '.net.bd' => ['price'=>700, 'url'=>'#'],
        '.org.bd' => ['price'=>600, 'url'=>'#'],
        '.ac.bd'  => ['price'=>550, 'url'=>'#'],
        '.mil.bd' => ['price'=>0, 'url'=>'#'],
        '.info.bd'=> ['price'=>750, 'url'=>'#'],
        '.বাংলা'  => ['price'=>900, 'url'=>'#']
    ];
}

/** ✅ Activate Default Options */
register_activation_hook(__FILE__, function(){
    if(!get_option('bdc_ext_prices')){
        update_option('bdc_ext_prices', bddc_default_prices());
    }
});
