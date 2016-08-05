<?php

class Eap_Product {
    
    private $product_id = '';
    private $product_name = '';
    private $permalink = '';
    
    public function __construct($id, $name, $permalink) {
        $this->product_id = $id;
        $this->product_name = $name;
        $this->permalink = $permalink;
    }
    
    public function getProductId() {
        return $this->product_id;
    }
    
    public function getProductName() {
        return $this->product_name;
    }
    
    public function getPermalink() {
        return $this->permalink;
    }
    
    
    /**
    *   Создание объекта на основе данных из БД
    *   
    *   Создает объект Eap_Product
    *   
    *   @param int $id ID продукта в БД
    *   @param PDO $db Объект PDO для доступа к БД. В контексте WordPress это переменная $wpdb
    *   @param string $prefix Префикс таблицы в БД
    *
    *   @return Eap_Product
    */
    
    public static function getInstance($id, $db, $prefix) {
        $query = "SELECT * FROM ".$prefix."products WHERE product_id=%d";
        $sql = $db->prepare($query, $id);
        $row = $db->get_row($sql);
        
        if (empty ($row)) { return null; }
        
        $product = new Eap_Product($row->product_id, $row->product_name, $row->permalink);
        return $product;
    }
}
