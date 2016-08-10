<?php

class Eap_Basket {
    
    private $order_id = 0; 
    private $basket = array(); /** Array of Eap_Basket_Line */
    private $total_count = 0;
    
    public function __construct($order_id) {
        $this->order_id = $order_id;
        $this->basket = array();
    }        
    
    public function getTotalCount() {
        return $this->total_count;        
    }
    
    public function getBasket() {
        return $this->basket;
    }

    public function addToBasket (Eap_Basket_Line $line) {
        array_push($this->basket, $line);
    }
    
    public function setTotalCount ($count) {
        $this->total_count = $count;
    }
    
    public function getBasketString() {
        if (!isset($this->basket)) { return null; }
        $basket = '';
        foreach ($this->basket as $line) {
            $basket .= $line->getBasketLineString();
        }
        return $basket;
    }

    /**
    *   Получение данных полной корзины заказа из БД
    *   
    *   Возвращает массив объектов типа Eap_Product_Basket_String 
    *   которые относятся к данному заказу
    *   
    *   @param int $order_id ID продукта в БД
    *   @param PDO $db Объект PDO для доступа к БД. В контексте WordPress это переменная $wpdb
    *   @param string $prefix Префикс таблицы в БД
    *
    *   @return array
    */
    
    public static function getBasketFull($order_id, $db, $prefix) {
        
        $basket = new Eap_Basket($order_id);     
        
        $query = "SELECT * FROM ".$prefix."baskets WHERE order_id=%d";
        $sql = $db->prepare($query, $order_id);
        $result = $db->get_results($sql);
                
        if (empty ($result)) { return null; }
        
        $total_count = 0;
        
        foreach ($result as $row) {
            $product = Eap_Product::getInstance($row->product_id, $db, $prefix);
            /** @todo Обработка исключения если продукта нет в таблице */
            $basket_line = new Eap_Basket_Line ($product, $row->order_id, $row->cost);
            $basket_line->setProductAmount($row->amount);
            $total_count += $row->amount;
            $basket->addToBasket($basket_line);
        }        
        
        $basket->setTotalCount($total_count);
        
        return $basket;
    }
}
