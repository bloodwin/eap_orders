<?php

if (!session_id()) { session_start(); }

require_once("functions.php");

if(is_admin()):
    require_once("admin-pages.php");
else:
    add_action('rcl_enqueue_scripts','rcl_eap_scripts',10);
endif;

function rcl_eap_scripts(){
         rcl_enqueue_style('eap_orders', rcl_addon_url('style.css', __FILE__));
         rcl_enqueue_script('eap_orders', rcl_addon_url('js/scripts.js', __FILE__) );
}

function eap_global_unit(){
    if(defined('EAP_PREF')) { return false; }
    global $wpdb,$eap_options,$user_ID;

    if(!isset($_SESSION['return_'.$user_ID])) {
            $_SESSION['return_'.$user_ID] = (isset($_SERVER['HTTP_REFERER']))? $_SERVER['HTTP_REFERER']: '/';
    }
    
    $eap_options = get_option('primary-eap-options');
    define('EAP_PREF', $wpdb->prefix."eap_");
}

add_action('init','eap_global_unit',10);

add_action('init','eap_tab_orders');

function eap_tab_orders(){
    rcl_tab('eap_orders','eap_orders_func','Заказы',array('public'=>0,'ajax-load'=>true,'class'=>'fa-shopping-cart','order'=>30,'path'=>__FILE__));
}

function eap_orders_func($author_lk){
    global $wpdb,$user_ID,$rcl_options;

    if($user_ID!=$author_lk) { return false; }

    $block = apply_filters('content_order_tab','');

    if(isset($_GET['order-id'])){

        $eap_order = Eap_Order::getInstance($_GET['order-id'], $wpdb,  EAP_PREF);

        if ($eap_order->order_author != $user_ID) { return false; }

        $eap_order_id = $eap_order->getOrderId();

        $block .= '<a class="recall-button view-orders" href="'.rcl_format_url(get_author_posts_url($author_lk),'eap_orders').'">Смотреть все заказы</a>';

        $block .= '<h3>Заказ №'.$eap_order_id.'</h3>';

        $block .= '<div class="redirectform"></div>';

        $block .= rcl_get_include_template('order.php',__FILE__);
        
    }else{

        $test = Eap_Orders_History::isExistsUserOrders($user_ID, $wpdb, EAP_PREF);

        if(!$test) $block .= '<p>У вас пока не оформлено ни одного заказа.</p>';
        else $block .= rcl_get_include_template('orders-history.php',__FILE__);

    }

    return $block;
}

