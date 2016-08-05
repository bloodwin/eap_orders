<?php

class Eap_Orders_History {
    
    private $order_id = 0;
    private $user_id = 0;
    
    public static function getHistoryByUserID($user_id, $db, $prefix) {
        $query = "SELECT DISTINCT(order_id) FROM ".$prefix."orders WHERE user_id=%d";
        $sql = $db->prepare($query, $user_id);
        $result = $db->get_results($sql);

        if (empty ($result)) { return null; }
        
        $orders_history = array();
        
        foreach ($result as $row) {
            $order = Eap_Order::getInstance($row->order_id, $db, $prefix);
            array_push($orders_history, $order);
        }
        
        return $orders_history;
    }
    
    public static function getFullHistory($db, $prefix) {
        $query = "SELECT DISTINCT(order_id) FROM ".$prefix."orders";
        $result = $db->query($query);
        
        if (empty ($result)) { return null; }
        
        $orders_history = array();
        
        foreach ($result as $row) {
            $order = Eap_Order::getInstance($row['order_id'], $db, $prefix);
            array_push($orders_history, $order);
        }
        
        return $orders_history;
    }
    
    public static function isExistsUserOrders ($user_id, $db, $prefix) {
        $query = "SELECT DISTINCT(order_id) FROM ".$prefix."orders WHERE user_id=$user_id";
        $result = $db->query($query);
        
        if (empty ($result)) { 
            return false;
        } else {
            return true;
        }
    }
}
