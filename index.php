<?php

if (!session_id()) { session_start(); }

require_once("functions.php");
#require_once("functions/ajax-func.php");

if(is_admin()):
    require_once("admin-pages.php");
else:
#    require_once("functions/shortcodes.php");
    add_action('rcl_enqueue_scripts','rcl_eap_scripts',10);
endif;

function rcl_eap_scripts(){
         rcl_enqueue_style('eap_orders', rcl_addon_url('style.css', __FILE__));
         rcl_enqueue_script('eap_orders', rcl_addon_url('js/scripts.js', __FILE__) );
}
function eap_global_unit(){
    if(defined('EAP_PREF')) return false;
    global $wpdb,$eap_options,$user_ID;

    if(!isset($_SESSION['return_'.$user_ID]))
            $_SESSION['return_'.$user_ID] = (isset($_SERVER['HTTP_REFERER']))? $_SERVER['HTTP_REFERER']: '/';

    $rmag_options = get_option('primary-eap-options');
    define('EAP_PREF', $wpdb->prefix."eap_");
}
add_action('init','eap_global_unit',10);

add_action('init','eap_tab_orders');

function eap_tab_orders(){
    rcl_tab('eap_orders','eap_orders_func','Заказы',array('public'=>0,'ajax-load'=>true,'class'=>'fa-shopping-cart','order'=>30,'path'=>__FILE__));
}

function eap_orders_func($author_lk){
    global $wpdb,$user_ID,$rcl_options,$eap_order;

    if($user_ID!=$author_lk) return false;

        $block = apply_filters('content_order_tab','');

    if(isset($_GET['order-id'])){

                $eap_order = eap_get_order($_GET['order-id']);

                if($eap_order->order_author!=$user_ID) return false;

                $status = $eap_order->order_status;
                $eap_order_id = $eap_order->order_id;
                $price = $eap_order->order_price;

                $block .= '<a class="recall-button view-orders" href="'.rcl_format_url(get_author_posts_url($author_lk),'eap_orders').'">Смотреть все заказы</a>';

                $block .= '<h3>Заказ №'.$eap_order_id.'</h3>';

                $postdata = rcl_encode_post(array(
                    'callback'=>'rcl_trash_order',
                    'order_id'=>$eap_order_id
                ));

                $block .= '<div class="redirectform"></div>';

        $block .= rcl_get_include_template('order.php',__FILE__);

    }else{

        global $eap_orders;

        #$eap_orders = eap_get_orders(array('user_id'=>$user_ID,'status_not_in'=>6));
        $eap_orders = eap_get_orders(array('user_id'=>$user_ID));

        if(!$eap_orders) $block .= '<p>У вас пока не оформлено ни одного заказа.</p>';
        else $block .= rcl_get_include_template('orders-history.php',__FILE__);

    }

    return $block;
}

