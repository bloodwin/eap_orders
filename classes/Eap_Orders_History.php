<?php

class Eap_Orders_History {
    
    private $order_id = 0;
    private $user_id = 0;
    
    public static function getHistoryByUserID($user_id, $db, $prefix) {
        $query = "SELECT DISTINCT(order_id) FROM ".$prefix."orders WHERE user_id=?";
        $stmt = $db->prepare($query);
        $result = $stmt->execute( array($user_id) );
        
        if (empty ($result)) { return null; }
        
        $orders_history = array();
        
        foreach ($result->fetch() as $row) {
            $order = Eap_Order::getInstance($row['order_id'], $db, $prefix);
            array_push($orders_history, $order);
        }
        
        return $orders_history;
    }
    
    public static function getFullHistory($db, $prefix) {
        $query = "SELECT DISTINCT(order_id) FROM ".$prefix."orders";
        $stmt = $db->prepare($query);
        $result = $stmt->execute();
        
        if (empty ($result)) { return null; }
        
        $orders_history = array();
        
        foreach ($result->fetch() as $row) {
            $order = Eap_Order::getInstance($row['order_id'], $db, $prefix);
            array_push($orders_history, $order);
        }
        
        return $orders_history;
    }
    
    public static function isExistsUserOrders ($user_id, $db, $prefix) {
        $query = "SELECT DISTINCT(order_id) FROM ".$prefix."orders WHERE user_id=?";
        $stmt = $db->prepare($query);
        $result = $stmt->execute( array($user_id) );
        
        if (empty ($result)) { 
            return false;
        } else {
            return true;
        }
    }
}