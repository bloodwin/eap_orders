<?php

class Eap_Product_Basket_String extends Eap_Product {
    
    private $order_id = 0; 
    private $product_price = 0;
    private $product_amount = 1;
    private $product_total = 0;
    
    public function __construct($product_id, $name, $permalink, $order_id, $product_price) {
        parent::__construct($product_id, $name, $permalink);
        $this->order_id = $order_id;
        $this->product_price = $product_price;
    }        
    
    public function setProductAmount($amount) {
        $this->product_amount = $amount;
        $this->product_total = $this->product_price * $this->product_amount;
    } 
    
    public function getProductAmount() {
        return $this->product_amount;
    }
    
    public function setProductPrice($price) {
        $this->product_price = $price;
        $this->product_total = $this->product_price * $this->product_amount;
    }
    
    public function getProductPrice() {
        return $this->product_price;
    }
    
    public function getProductTotalPrice() {
        return $this->product_total;    
    }
    
    public function getBasketLine() {
        $line  = $this->getProductName()." ";
        $line .= "$this->product_price ";
        $line .= "$this->product_amount ";
        $line .= "$this->product_total";
        $line .= "<br />\n";
        return $line;
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
        
        $basket = array();        
        
        $query = "SELECT * FROM ".$prefix."baskets WHERE order_id=%d";
        $sql = $db->prepare($query, $order_id);
        $result = $db->get_results($sql);
                
        if (empty ($result)) { return null; }
        
        foreach ($result as $row) {
            $product = Eap_Product::getInstance($row->product_id, $db, $prefix);
            /** @todo Обработка исключения если продукта нет в таблице */
            $basket_string = new Eap_Product_Basket_String ($product->getProductId(),
                                                            $product->getProductName(),
                                                            $product->getPermalink(),
                                                            $row->order_id,
                                                            $row->cost);
            $basket_string->setProductAmount($row->amount);
            array_push($basket, $basket_string);
        }        
        
        return $basket;
    }
}
