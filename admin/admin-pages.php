<?php
include_once 'orders-history.php';

function wp_eap_orders_options_panel(){
    add_menu_page('EAP Orders', 'EAP Orders', 'manage_options', 'manage-eap', 'eap_manage_orders');
    $hook = add_submenu_page( 'manage-eap', __('Orders','wp-recall'), __('Orders','wp-recall'), 'manage_options', 'manage-eap', 'eap_manage_orders');
    add_action( "load-$hook", 'eap_orders_page_options' );
    add_submenu_page( 'manage-eap', __('Export/Import','wp-recall'), __('Export/Import','wp-recall'), 'manage_options', 'manage-eap-price', 'eap_export');
    add_submenu_page( 'manage-eap', __('Store settings','wp-recall'), __('Store settings','wp-recall'), 'manage_options', 'manage-eap-options', 'eap_global_options');
}
add_action('admin_menu', 'wp_eap_orders_options_panel',20);

add_filter('admin_options_eap','eap_primary_options',5);

function eap_primary_options($content){
    global $rcl_options;
    $rcl_options = get_option('primary-eap-options');
    
    include_once RCL_PATH.'functions/rcl_options.php';

    $opt = new Rcl_Options(rcl_key_addon(pathinfo(__FILE__)));
    $content .= $opt->options(
                __('Settings','wp-recall').' WP-RECALL-EAP',array(
                $opt->option_block(
                    array(
                        $opt->title(__('General settings','wp-recall')),
                        $opt->label(__('Email Notification','wp-recall')),
                        $opt->option('email',array('name'=>'admin_email_magazin_recall')),
                        $opt->notice(__('If email is not specified, a notification will be sent to all users of the website with the rights of the "Administrator"','wp-recall')),
                    )
                ),
                $opt->option_block(
                    array(
                        $opt->title(__('Temp settings area','wp-recall')),
                        $opt->label(__('Temp settings','wp-recall')),
                        $opt->option('select',array(
                            'name'=>'temp_setting',
                            'default'=>1,
                            'options'=>array(__('Disabled','wp-recall'),__('Included','wp-recall'))
                        )),
                        $opt->notice(__('Temporary area for setting','wp-recall'))
                    )
                ),
               $opt->option('select',array(
                            'name'=>'primary_cur',
                            'options'=>rcl_get_currency()
                       )),
                       $opt->label(__('Secondary currency','wp-recall')),
                       $opt->option('select',array(
                                    'name'=>'multi_cur',
                                    'parent'=>true,
                                    'options'=>array(__('Disabled','wp-recall'),__('Included','wp-recall'))
                                    )
                                   ),
                       $opt->child(
                                 array(
                                       'name'=>'multi_cur',
                                        'value'=>1
                                 ),
                                array(
                                      $opt->label(__('Select a currency','wp-recall')),
                                      $opt->option('select',array(
                                                   'name'=>'secondary_cur',
                                                   'options'=>rcl_get_currency()
                                      )),
                                      $opt->label(__('Course','wp-recall')),
                                      $opt->option('text',array('name'=>'curse_currency')),
                                      $opt->notice(__('Enter the secondary currency exchange rate in relation to the principal. For example: 1.3','wp-recall'))
                                )       
                            )
                )
        );
    return $content;
}

function eap_manage_orders(){
    global $wpdb;

    $n=0;

    if(isset($_GET['action'])&&$_GET['action']=='order-details'){
    
        echo '<h2>'.__('EAP order management','wp-recall').'</h2>
            <div style="width:1050px">';

    $order = Eap_Order::getInstance($_GET['order'], $wpdb, EAP_PREF);
    vardump($order, "ORDER:");

    if($_POST['submit_message']){
        if($_POST['email_author']) $email_author = sanitize_email($_POST['email_author']);
        else $email_author = 'noreply@'.$_SERVER['HTTP_HOST'];
        $user_email = get_the_author_meta('user_email',intval($_POST['address_message']));
        $result_mess = rcl_mail($user_email, sanitize_text_field($_POST['title_message']), force_balance_tags($_POST['text_message']));
    }

    $header_tb = array(
        '№ п/п',
        __('Name product','wp-recall'),
        __('Price','wp-recall'),
        __('Amount','wp-recall'),
        __('Sum','wp-recall'),
        __('Status','wp-recall'),
    );

    echo '<h3>'.__('ID order','wp-recall').': '.$_GET['order'].'</h3>'
                . '<table class="widefat">'
                . '<tr>';

    foreach($header_tb as $h){
        echo '<th>'.$h.'</th>';
    }

    echo '</tr>';

    foreach($order->getBasket() as $product){
        $n++;
        $user_login = get_the_author_meta('user_login',$order->getUserId());
        echo '<tr>'
            . '<td>'.$n.'</td>'
            . '<td>'.$product->getProductName().'</td>'
            . '<td>'.$product->getProductPrice().'</td>'
            . '<td>'.$product->getProductAmount().'</td>'
            . '<td>'.$product->getProductTotalPrice().'</td>'
            . '<td>'.$order->getStatus().'</td>'
        . '</tr>';

    }
    echo '<tr>
                    <td colspan="4">'.__('Sum order','wp-recall').'</td>
                    <td colspan="2">'.$order->getTotalPrice().'</td>
        </tr>
    </table>';

    $get_fields = get_option( 'rcl_profile_fields' );

    $cf = new Rcl_Custom_Fields();

    foreach((array)$get_fields as $custom_field){
            $meta = get_the_author_meta($custom_field['slug'],$order->getUserId());
            $show_custom_field .= $cf->get_field_value($custom_field,$meta);
    }

    #$details_order = eap_get_order_details($order->order_id);

    echo '<form><input type="button" value="'.__('Ago','wp-recall').'" onClick="history.back()"></form>'
                . '<div style="text-align:right;">'
                    . '<a href="'.admin_url('admin.php?page=manage-eap').'">'.__('Show all orders','wp-recall').'</a>
                </div>
    <h3>'.__('All orders user','wp-recall').': <a href="'.admin_url('admin.php?page=manage-eap&user='.$order->getUserId()).'">'.$user_login.'</a></h3>
    <h3>'.__('Information about the user','wp-recall').':</h3>'
                . '<p><b>'.__('Name','wp-recall').'</b>: '.get_the_author_meta('display_name',$order->getUserId()).'</p>'
                . '<p><b>'.__('Email','wp-recall').'</b>: '.get_the_author_meta('user_email',$order->getUserId()).'</p>'.$show_custom_field;
    #if($details_order) echo '<h3>'.__('Order details','wp-recall').':</h3>'.$details_order;

    echo '</div>';//конец блока заказов

    }else{

        eap_admin_orders_page();

    }

}

function eap_export(){
#TODO: Export\Import CSV.
    global $wpdb;
}
