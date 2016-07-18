<?php
global $wpdb;

if(!defined('EAP_PREF')) define('EAP_PREF', $wpdb->prefix."eap_");

require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    
$collate = '';

if ( $wpdb->has_cap( 'collation' ) ) {
    if ( ! empty( $wpdb->charset ) ) {
        $collate .= "DEFAULT CHARACTER SET $wpdb->charset";
    }
    if ( ! empty( $wpdb->collate ) ) {
        $collate .= " COLLATE $wpdb->collate";
    }
}

$table = EAP_PREF ."orders";
$sql = "CREATE TABLE IF NOT EXISTS ". $table . " (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `user_id` int(11) NOT NULL,
        `eap_order_id` int(11) DEFAULT NULL,
        `order_status` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
        `status_date` datetime DEFAULT NULL COMMENT 'last change date',
        `confirmed` enum('0','1') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
        `first_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
        `last_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
        `otchestvo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
        `zip_code` varchar(7) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
        `country` varchar(90) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
        `state` varchar(90) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
        `city` varchar(90) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
        `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
        `email` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
        `phone` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
        `created` datetime DEFAULT NULL,
        `currency` varchar(6) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
        `amount` decimal(7,2) DEFAULT NULL,
        `delivery_cost` decimal(7,2) DEFAULT NULL,
        `notes` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
        `logist_comment` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
        `author_comment` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
        PRIMARY KEY (`id`),
        UNIQUE KEY `id_UNIQUE` (`id`),
        KEY `fk_orders_users1_idx` (`user_id`),
        KEY `eap_order_id_idx` (`eap_order_id`)
    ) $collate;";

dbDelta( $sql );

$table = EAP_PREF ."baskets";
$sql = "CREATE TABLE IF NOT EXISTS ". $table . " (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `eap_order_id` int(11) NOT NULL,
        `eap_good_id` int(11) NOT NULL,
        `eap_cost` decimal(7,2) DEFAULT NULL,
        `quantity` int(11) NOT NULL,
        PRIMARY KEY (`id`),
        UNIQUE KEY `id_UNIQUE` (`id`),
        KEY `eap_order_id_idx` (`eap_order_id`)
      ) $collate;";

dbDelta( $sql );

$table = EAP_PREF ."goods";
$sql = "CREATE TABLE IF NOT EXISTS ". $table . " (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `eap_good_id` int(11) NOT NULL,
        `good_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
        `permalink` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
        PRIMARY KEY (`id`),
        UNIQUE KEY `id_UNIQUE` (`id`),
        KEY `eap_good_id_idx` (`eap_good_id`)
      ) $collate;";

dbDelta( $sql );


$eap_options = get_option('primary-eap-options');

if(!isset($eap_options['primary_cur'])) $eap_options['primary_cur']='RUB';

update_option('primary-eap-options',$eap_options);

