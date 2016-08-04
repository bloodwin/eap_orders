<?php

class Eap_Order {
    
    private $user_id = 0;
    private $is_error = 0;
    private $order_id = 0; /** Получаем от E-AutoPay */
    private $id = 0; /** ID в нашей таблице заказов */
    private $status = '';
    private $status_date = '';
    private $confurmed = 0;
    private $userdata; /** Объект Eap_User */
    private $created = null;
    private $currency;
    private $total_price = 0;
    private $delivery_cost = 0;
    private $notes = '';
    private $logist_comment = '';
    private $author_comment = '';
    
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
       
        $query = "SELECT * FROM ".$prefix."orders WHERE order_id=?";
        $stmt = $db->prepare($query);
        $result = $stmt->execute( array($odrer_id) );
                
        if (empty ($result)) { return null; }
        
        $row = $result->fetch();)
        $basket = Eap_Eap_Product_Basket_String::getBasketFull($order_id, $db, $prefix);
        /** @todo Обработка исключения если корзина пустая */
        $order = new Eap_Order($row->user_id);
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
        $order->userdata = new Eap_User($row->user_id, $row->first_name,
                                        $row->last_name, $row->otchestvo,
                                        $row->zip_code, $row->country,
                                        $row->state, $row->city,
                                        $row->address, $row->email, $row->phone0);        
        
        return $order;
    }

}
