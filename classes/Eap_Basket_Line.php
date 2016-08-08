<?php

class Eap_Basket_Line extends Eap_Product {
    
    private $order_id = 0; 
    private $product_price = 0;
    private $product_amount = 1;
    private $product_total = 0;
    
    public function __construct(Eap_Product $product, $order_id, $product_price) {
        parent::__construct($product->getProductId(), $product->getProductName(), $product->getPermalink());
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
    
    public function getBasketLineString() {
        $line =  "$this->getProductName() ";
        $line .= "$this->product_price ";
        $line .= "$this->product_amount ";
        $line .= "$this->product_total";
        $line .= "<br />\n";
        return $line;
    }

}