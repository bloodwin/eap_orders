<?php

class Eap_Orders_History {
    
    private $order_id = 0;
    private $user_id = 0;
    private $total_items = 0; /** Количество заказов */
    
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
        $result = $db->get_results($query);
        
        if (empty ($result)) { return null; }
        
        $orders_history = array();
        
        foreach ($result as $row) {
            $order = Eap_Order::getInstance($row->order_id, $db, $prefix);
            array_push($orders_history, $order);
        }
        
        return $orders_history;
    }
    
    public static function isExistsUserOrders ($user_id, $db, $prefix) {
        $query = "SELECT DISTINCT(order_id) FROM ".$prefix."orders WHERE user_id=%d";
        $sql = $db->prepare($query, $user_id);
        $result = $db->get_row($sql);
        
        if (empty ($result)) { 
            return false;
        } else {
            return true;
        }
    }
    
    public static function getHistoryByArgs ($db, $prefix, array $args) {
        $date = array();
        $where = '';

        $sql = "SELECT * FROM ".$prefix."orders ";

        if (isset($args['order_id'])) { $wheres[] = "order_id IN ('" . $args['order_id'] . "')"; }
        if (isset($args['user_id'])) { $wheres[] = "user_id='" . $args['user_id'] . "'"; }
        if (isset($args['status'])) { $wheres[] = "status='" . $args['status'] . "'"; }
        if (isset($args['status_not_in'])) { $wheres[] = "status NOT IN ('" . $args['status_not_in'] . "')"; }
        if (isset($args['year'])) { $date[] = $args['year']; }
        if (isset($args['month'])) { $date[] = $args['month']; }

        if($date){
            $date = implode('-',$date);
            $wheres[] = "created LIKE '%$date%'";
        }

        if($wheres){ $where = implode(' AND ',$wheres); }
        if($where) { $sql .= ' WHERE '.$where; }

        $orderby = (isset($args['orderby']))? " ORDER BY ".$args['orderby']:" ORDER BY ID";
        $sql_order = (isset($args['order']))? $args['order']:" DESC";

        $sql .= $orderby.$sql_order;

        if(isset($args['per_page'])){
            $per_page = (isset($args['per_page']))? $args['per_page']: 30;
            $offset = (isset($args['offset']))? $args['offset']: 0;
            $sql .= " LIMIT $offset,$per_page";
        }

        $ids = $db->get_col($sql);

        if (!$ids) { return FALSE; }

        $result = $db->get_results($sql);

        if (!$result) { return false; }

        $orders_history = array();

        foreach($result as $row){
            $order = Eap_Order::getInstance($row->order_id, $db, $prefix);
            array_push($orders_history, $order);
        }

        return $orders_history;
     }
}
