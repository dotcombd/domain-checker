<?php
if (!defined('ABSPATH')) exit;

add_action('admin_menu', function(){
    add_menu_page(
        'BD Domain Checker',
        'BD Domain Checker',
        'manage_options',
        'bd-domain-checker',
        'bddc_admin_settings',
        'dashicons-search',
        60
    );
});

function bddc_admin_settings(){
    if(isset($_POST['bddc_save'])){
        $exts = get_option('bdc_ext_prices', []);
        foreach($exts as $ext => $data){
            $exts[$ext]['price'] = intval($_POST['price'][$ext]);
            $exts[$ext]['url'] = esc_url_raw($_POST['url'][$ext]);
        }
        update_option('bdc_ext_prices',$exts);
        echo '<div class="updated"><p>✅ Saved!</p></div>';
    }
    $exts = get_option('bdc_ext_prices', bddc_default_prices());
    ?>
    <div class="wrap">
        <h1>BD Domain Checker Settings</h1>
        <form method="post">
            <table class="widefat">
                <thead><tr><th>Extension</th><th>Price</th><th>Buy URL</th></tr></thead>
                <tbody>
                <?php foreach($exts as $ext => $data){ ?>
                <tr>
                    <td><?php echo $ext; ?></td>
                    <td><input type="text" name="price[<?php echo $ext; ?>]" value="<?php echo $data['price']; ?>"></td>
                    <td><input type="text" name="url[<?php echo $ext; ?>]" value="<?php echo $data['url']; ?>" style="width:300px;"></td>
                </tr>
                <?php } ?>
                </tbody>
            </table>
            <?php submit_button('Save Settings','primary','bddc_save'); ?>
        </form>
    </div>
    <?php
}
