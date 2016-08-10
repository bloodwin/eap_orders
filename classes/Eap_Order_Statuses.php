<?php

/* 
 * Статический класс для описания статусов заказов E-AutoPay
 * 
 * 
 */

class Eap_Order_Statuses {
    
    static private $statuses = array();
    
    private function __construct() {
    }

    static private function fillStatuses () {
        self::$statuses = array (
            1 => __( 'new', 'wp-recall' ),
            2 => __( 'confirmed', 'wp-recall' ),
            3 => __( 'sent', 'wp-recall' ),
            4 => __( 'cancelled', 'wp-recall' ),
            5 => __( 'in_hands_paid', 'wp-recall' ),
            6 => __( 'unconfirmed', 'wp-recall' ),
            7 => __( 'delayed', 'wp-recall' ),
            8 => __( 'refused', 'wp-recall' ),
            9 => __( 'delivered_unpaid', 'wp-recall' ),
            10=> __( 'ready_for_delivery', 'wp-recall' ),
            11=> __( 'problem', 'wp-recall' ),
            12=> __( 'delivered_paid', 'wp-recall' ),
            13=> __( 'pending', 'wp-recall' ),
            14=> __( 'specified', 'wp-recall' ),
            15=> __( 'in_hands_unpaid', 'wp-recall' ),
            16=> __( 'wanted', 'wp-recall' ),
            17=> __( 'absence', 'wp-recall' ),
            18=> __( 'returned', 'wp-recall' ),
            19=> __( 'notice1', 'wp-recall' ),
            20=> __( 'notice2', 'wp-recall' )
        );
     
        /*
        "new"   Новый
        "confirmed" Подтвержден оператором
        "sent"  Отправлен клиенту
        "cancelled" Возврат
        "in_hands_paid" Вручен и оплачен
        "unconfirmed"   Неподтвержден
        "delayed"   Отсрочен
        "refused"   Отказ
        "delivered_unpaid"  Доставлен к месту получения
        "ready_for_delivery"    Подготовлен к отправке
        "problem"   Проблемный
        "delivered_paid"    Вручен и оплачен
        "pending"   Ожидает подтверждения (Call-центр)
        "specified" Уточненный
        "in_hands_unpaid"   Вручен
        "wanted"    В розыске
        "absence"   Отсутствие продукта
        "returned"  Получен возврат
        "notice1"   Напоминание 1
        "notice2"   Напоминание 1
        */
    } 
    
    static public function getInstance() {
        self::fillStatuses();
        return self::$statuses;
    }

    static public function getStatusName($status_id) {
        self::fillStatuses();
        return self::$statuses[$status_id];
    }
    
    static public function getStatusId($status_name) {
        self::fillStatuses();
        $sid = array_search($status_name,self::$statuses);
        return $sid;
    }
}