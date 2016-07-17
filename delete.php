<?php
global $wpdb;
define('EAP_PREF', $wpdb->prefix."eap_");
delete_option( 'primary-eap-options' );

$wpdb->query("DROP TABLE ".EAP_PREF ."orders");
$wpdb->query("DROP TABLE ".EAP_PREF ."baskets");
$wpdb->query("DROP TABLE ".EAP_PREF ."goods");

