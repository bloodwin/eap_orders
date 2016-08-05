<?php

class Eap_Order {
    
    private $user_id = 0;
    private $is_error = 0;
    private $order_id = 0; /** Получаем от E-AutoPay */
    private $id = 0; /** ID в нашей таблице заказов */
    private $status = '';
    private $status_date = '';
    private $confurmed = 0;
    public  $userdata; /** Объект Eap_User */
    private $created = null;
    private $currency;
    private $total_price = 0;
    private $delivery_cost = 0;
    private $notes = '';
    private $logist_comment = '';
    private $author_comment = '';
    private $basket = array();
    private $total_amount = 0;
    
    public function __construct($user_id){
        $this->user_id = $user_id;
    }
    
    public function error($code,$error){
        $this->is_error = $code;
        $wp_errors = new WP_Error();
        $wp_errors->add( $code, $error );
        return $wp_errors;
    }
    
 
    /**
    *   Создание объекта заказа из БД
    *   
    *   Возвращает объект Eap_Order
    *   
    *   @param int $order_id ID продукта в БД
    *   @param PDO $db Объект PDO для доступа к БД. В контексте WordPress это переменная $wpdb
    *   @param string $prefix Префикс таблицы в БД
    *
    *   @return Eap_Order
    */
    
    public static function getInstance($order_id, $db, $prefix) {
       
        $query = "SELECT * FROM ".$prefix."orders WHERE order_id=%d";
        $sql = $db->prepare($query, $order_id);
        $result = $db->get_row($sql);
                
        if (empty ($result)) { return null; }
        
        $row = $result;
        $basket = Eap_Product_Basket_String::getBasketFull($order_id, $db, $prefix);
        /** @todo Обработка исключения если корзина пустая */
        $order = new Eap_Order($row->user_id);
        $order->total_amount = array_shift($basket);
        $order->order_id = $row->order_id;
        $order->id = $row->id;
        $order->status = $row->status;
        $order->status_date = $row->status_date;
        $order->confurmed = $row->confurmed;
        $order->created = $row->created;
        $order->currency = $row->currency;
        $order->total_price = $row->total_price;
        $order->delivery_cost = $row->delivery_cost;
        $order->notes = $row->notes;
        $order->logist_comment = $row->logist_comment;
        $order->author_comment = $row->author_comment;
        $order->userdata = Eap_User::initUser($row);        
        $order->basket = $basket;
        
        return $order;
    }

    function getUserId() {
        return $this->user_id;
    }

    function getOrderId() {
        return $this->order_id;
    }

    function getStatus() {
        return $this->status;
    }

    function getStatusDate() {
        return $this->status_date;
    }

    function getConfurmed() {
        return $this->confurmed;
    }

    function getUserdata() {
        return $this->userdata;
    }

    function getCreated() {
        return $this->created;
    }

    function getCurrency() {
        return $this->currency;
    }

    function getTotalPrice() {
        return $this->total_price;
    }

    function getDeliveryCost() {
        return $this->delivery_cost;
    }

    function getNotes() {
        return $this->notes;
    }

    function getLogistComment() {
        return $this->logist_comment;
    }

    function getAuthorComment() {
        return $this->author_comment;
    }
    
    function getTotalAmount() {
        return $this->total_amount;
    }
    
    function getBasket() {
        return $this->basket;
    }
    
    function getBasketString() {
        if (!isset($this->basket)) { return null; }
        $basket = '';
        foreach ($this->basket as $line) {
            $basket .= $line->getBasketLine();
        }
        return $basket;
    }
    
    function setUserId($user_id) {
        $this->user_id = $user_id;
    }

    function setOrderId($order_id) {
        $this->order_id = $order_id;
    }

    function setStatus($status) {
        $this->status = $status;
    }

    function setStatusDate($status_date) {
        $this->status_date = $status_date;
    }

    function setConfurmed($confurmed) {
        $this->confurmed = $confurmed;
    }

    function setUserdata(Eap_User $userdata) {
        $this->userdata = $userdata;
    }

    function setCreated($created) {
        $this->created = $created;
    }

    function setCurrency($currency) {
        $this->currency = $currency;
    }

    function setTotalPrice($total_price) {
        $this->total_price = $total_price;
    }

    function setDeliveryCost($delivery_cost) {
        $this->delivery_cost = $delivery_cost;
    }

    function setNotes($notes) {
        $this->notes = $notes;
    }

    function setLogistComment($logist_comment) {
        $this->logist_comment = $logist_comment;
    }

    function setAuthorComment($author_comment) {
        $this->author_comment = $author_comment;
    }
    
    function setTotalAmount($amount) {
        $this->total_amount = $amount;
    }

    function setBasket(array $basket) {
        /** @todo Проверить, что $basket является массивом объектов типа Eap_Product_Basket_String */
        $this->basket = $basket;
    }


    
}
