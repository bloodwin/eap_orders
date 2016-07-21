<?php
function eap_order_statuses(){
    $sts = array(
          1    => __( 'new', 'wp-recall' ),
          2    => __( 'confirmed', 'wp-recall' ),
          3    => __( 'sent', 'wp-recall' ),
          4    => __( 'cancelled', 'wp-recall' ),
          5    => __( 'in_hands_paid', 'wp-recall' ),
          6    => __( 'unconfirmed', 'wp-recall' ),
          7    => __( 'delayed', 'wp-recall' ),
          8    => __( 'refused', 'wp-recall' ),
          9    => __( 'delivered_unpaid', 'wp-recall' ),
          10   => __( 'ready_for_delivery', 'wp-recall' ),
          11   => __( 'problem', 'wp-recall' ),
          12   => __( 'delivered_paid', 'wp-recall' ),
          13   => __( 'pending', 'wp-recall' ),
          14   => __( 'specified', 'wp-recall' ),
          15   => __( 'in_hands_unpaid', 'wp-recall' ),
          16   => __( 'wanted', 'wp-recall' ),
          17   => __( 'absence', 'wp-recall' ),
          18   => __( 'returned', 'wp-recall' ),
          19   => __( 'notice1', 'wp-recall' ),
          20   => __( 'notice2', 'wp-recall' )
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
    return apply_filters('order_statuses',$sts);
}

//Устанавливаем перечень статусов
function eap_get_status_name_order($status_id){   
    $sts = eap_order_statuses();
    return $sts[$status_id];
}
function eap_get_status_id($status_name){   
    $sts = eap_order_statuses();
    $sid = array_search($status_name,$sts);
    return $sid;
}

//Отображение свойств заказа
function eap_order_ID(){
    global $eap_order;
    echo $eap_order->order_id;
}
function eap_order_date(){
    global $eap_order;
    echo $eap_order->created;
}
function eap_order_fio(){
    global $eap_order;
    echo $eap_order->fio;
}
function eap_order_address(){
    global $eap_order;
    echo $eap_order->address;
}
function eap_order_email(){
    global $eap_order;
    echo $eap_order->email;
}
function eap_order_phone(){
    global $eap_order;
    echo $eap_order->phone;
}
function eap_order_user_comment(){
    global $eap_order;
    echo $eap_order->author_comment;
}
function eap_number_products(){
    global $eap_order;
    echo $eap_order->items_count;
}
function eap_order_price(){
    global $eap_order;
    $price = apply_filters('order_price',$eap_order->order_price,$eap_order);
    echo $price;
}
function eap_order_status(){
    global $eap_order;
    echo eap_get_status_name_order($eap_order->order_status);
}
function eap_order_status_date(){
    global $eap_order;
    echo $eap_order->order_status_date;
}
function eap_order_basket_full(){
    global $eap_order;
    echo $eap_order->basket_full;
}

//Отображение свойств товара
function eap_product_ID(){
    global $eap_product;
    echo $eap_product->product_ID;
}
function eap_product_title(){
    global $eap_product;
    echo $eap_product->product_name;
}
function eap_product_price(){
    global $eap_product;
    echo $eap_product->product_price;
}
function eap_product_number(){
    global $eap_product;
    echo $eap_product->items_count;
}
function eap_product_summ(){
    global $eap_product;
    echo $eap_product->summ_price;
}
function eap_product_permalink(){
    global $eap_product;
    echo $eap_product->permalink;
}
//Получаем данные заказа
function eap_get_order($eap_order_id){
    global $wpdb,$eap_order,$eap_product;
    $query = "SELECT o.*, g.good_name, g.permalink, b.eap_cost, b.quantity, b.eap_good_id, CONCAT_WS(' - ', g.good_name, b.eap_cost, b.quantity, (b.eap_cost * b.quantity)) as good_data
              FROM ".EAP_PREF."orders as o, 
                   ".EAP_PREF."baskets as b,
                   ".EAP_PREF."goods as g
                   WHERE o.eap_order_id = b.eap_order_id and b.eap_good_id=g.eap_good_id";
    $eap_orderdata = $wpdb->get_results($query);
    if(!$eap_orderdata) return false;
    return eap_setup_orderdata($eap_orderdata);
}

//Получаем все заказы по указанным параметрам
function eap_get_orders($args){
    global $wpdb;
    $date = array();

    $where = '';
    
    $orders_history = "SELECT o.*, g.good_name, g.permalink, b.eap_cost, b.quantity, b.eap_good_id, CONCAT_WS(' - ', g.good_name, b.eap_cost, b.quantity, (b.eap_cost * b.quantity)) as good_data 
            FROM ".EAP_PREF."orders as o, 
                 ".EAP_PREF."baskets as b,
                 ".EAP_PREF."goods as g
            WHERE o.eap_order_id = b.eap_order_id and b.eap_good_id=g.eap_good_id";

    if(isset($args['count'])){
        $sql = "SELECT COUNT(DISTINCT(eap_order_id)) ";
    }else{
        $sql = "SELECT oh.*, group_concat(good_data SEPARATOR '<br/>\n') as basket_full";
    }

    if(isset($args['order_id'])) $wheres[] = "eap_order_id IN ('".$args['order_id']."')";
    if(isset($args['user_id'])) $wheres[] = "user_id='".$args['user_id']."'";
    if(isset($args['order_status'])) $wheres[] = "order_status='".$args['order_status']."'";
    if(isset($args['status_not_in'])) $wheres[] = "order_status NOT IN ('".$args['status_not_in']."')";
    if(isset($args['product_id'])) $wheres[] = "eap_good_id IN ('".$args['product_id']."')";
    if(isset($args['year'])) $date[] = $args['year'];
    if(isset($args['month'])) $date[] = $args['month'];

    if($date){
        $date = implode('-',$date);
        $wheres[] = "created LIKE '%$date%'";
    }

    if($wheres){
        /*if(isset($args['search'])&&$args['search']) $where = implode(' OR ',$wheres);
        else */
            $where = implode(' AND ',$wheres);
    }

    if($where) $orders_history .= " AND ".$where;
    
    if(!isset($args['count'])){
        $eap_orderby = (isset($args['orderby']))? "ORDER BY ".$args['orderby']:"ORDER BY ID";
        $eap_sql_order = (isset($args['order']))? $args['order']:"DESC";

        $sql .= " FROM ($orders_history) as oh GROUP BY oh.eap_order_id $eap_orderby $eap_sql_order";

        if(isset($args['per_page'])){

            $per_page = (isset($args['per_page']))? $args['per_page']: 30;
            $offset = (isset($args['offset']))? $args['offset']: 0;
            $sql .= " LIMIT $offset,$per_page";

        }
    }else{
        //если считаем
        $sql .= "FROM ($orders_history) as oh";
        return $wpdb->get_var($sql);
    }
    $ids = $wpdb->get_col($sql);
    
    if(!$ids) return false;
    
    $rdrs = $wpdb->get_results($sql);

    if(!$rdrs) return false;

    foreach($rdrs as $rd){
        $eap_orders[$rd->eap_order_id][] = $rd;
    }

    return $eap_orders;
}

//Удаляем заказ
//Пока не используем
function eap_delete_order($eap_order_id){
    global $wpdb;
    //do_action('eap_delete_order',$eap_order_id);
    //return $wpdb->query($wpdb->prepare("DELETE FROM ". RMAG_PREF ."orders_history WHERE order_id = '%d'",$eap_order_id));
    return false;
}

//Обновляем статус заказа
//:TODO E-Autopay API
function eap_update_status_order($eap_order_id,$status,$user_id=false){
    global $wpdb;
    $args = array('order_id' => $eap_order_id);
    if($user_id) $args['user_id'] = $user_id;
    do_action('eap_update_status_order',$eap_order_id,$status);
    return $wpdb->update( EAP_PREF ."orders", array( 'order_status' => $status), $args );
}

function eap_get_chart_orders($eap_orders){
    global $eap_order,$chartData,$chartArgs;

    if(!$eap_orders) return false;

    $chartArgs = array();
    $chartData = array(
        'title' => __('Finance','wp-recall'),
        'title-x' => __('Period of time','wp-recall'),
        'data'=>array(
            array('"'.__('Days/Months','wp-recall').'"', '"'.__('Payments (pcs.)','wp-recall').'"', '"'.__('Income (tsd.)','wp-recall').'"')
        )
    );

    foreach($eap_orders as $eap_order){
        //rcl_setup_orderdata($order);
        //rcl_setup_chartdata($eap_order->order_date,$eap_order->order_price);
        rcl_setup_chartdata($eap_order->created,$eap_order->order_status_date,$eap_order->order_price);
    }

    return rcl_get_chart($chartArgs);
}


//Формирование массива данных заказа
function eap_setup_orderdata($orderdata){
    global $eap_order,$eap_product;

/*
        o.order_status as order_status,
        o.status_date as status_date,
        o.confirmed as comfurmed,
        o.last_name as last_name,
        o.first_name as firts_name,
        o.otchestvo as otchestvo,
        o.zip_code as zip_code,
        o.country as country,
        o.state as state,
        o.city as city,
        o.address as address,
        o.email as email,
        o.phone as phone,
        o.created as created,
        o.currency as currency,
        o.amount as amount,
        o.delivery_cost as delivery_cost,
        o.notes as notes,
        o.logist_comment as logist_comment,
        o.author_comment as author_comment,
        bg.basket_full as basket_full
*/
    $eap_order = (object)array(
        'order_id'=>0,
        'order_price'=>0,
        'order_author'=>0,
        'order_status'=>0,
        'order_status_date'=>false,
        'items_count'=>0,
        'created'=>false,
        'confirmed'=>0,
        'fio'=>0,
        'address'=>0,
        'email'=>0,
        'phone'=>0,
        'delivery_cost'=>0,
        'logist_comment'=>0,
        'author_comment'=>0,
        'basket_full'=>false,
        'amount'=>0,
        'products'=>array()
    );

    $count = 0;

    foreach($orderdata as $data){ 
        eap_setup_productdata($data);
        
        if(!$eap_order->order_id) $eap_order->order_id = $data->eap_order_id;
        if(!$eap_order->order_author) $eap_order->order_author = $data->user_id;
        if(!$eap_order->order_status) $eap_order->order_status = eap_get_status_id($data->order_status);
        if(!$eap_order->order_status_date) $eap_order->order_status_date = $data->status_date;
        if(!$eap_order->created) $eap_order->created = $data->created;
        if(!$eap_order->confirmed) $eap_order->confirmed = $data->confirmed;
        if(!$eap_order->fio) $eap_order->fio = "{$data->last_name} {$data->first_name} {$data->otchestvo}";
        if(!$eap_order->address) $eap_order->address = "{$data->zip_code}<br\>\n".
                                                           "{$data->country}<br\>\n".
                                                           "{$data->state}<br\>\n".
                                                           "{$data->city}<br\>\n".
                                                           "{$data->address}";
        if(!$eap_order->email) $eap_order->email = $data->email;
        if(!$eap_order->phone) $eap_order->phone = $data->phone;
        if(!$eap_order->delivery_cost) $eap_order->delivery_cost = $data->delivery_cost;
        if(!$eap_order->logist_comment) $eap_order->logist_comment = $data->logist_comment;
        if(!$eap_order->author_comment) $eap_order->author_comment = $data->author_comment;
        if(!$eap_order->amount) $eap_order->amount = $data->amount;
        
        if(!$eap_order->basket_full) $eap_order->basket_full = $data->basket_full;

        $eap_order->order_price += $eap_product->summ_price;
        $eap_order->items_count += $eap_product->items_count;
        $eap_order->products[] = $eap_product;
        $count++;
    } 

    vardump($eap_order, "EAP_Order: ");
    return $eap_order;
}
function eap_setup_productdata($productdata){
    global $eap_product;

    $eap_product = (object)array(
        'product_id'=>0,
        'product_price'=>0,
        'summ_price'=>0,
        'items_count'=>0,
        'user_id'=>0,
        'order_id'=>0,
        'created'=>0,
        'order_status'=>0,
        'product_name'=>0,
        'permalink'=>false
    );
    
        $eap_product->product_id = $productdata->eap_good_id;
        $eap_product->product_price = $productdata->eap_cost;
        $eap_product->summ_price = $productdata->eap_cost*$productdata->quantity;
        $eap_product->items_count = $productdata->quantity;
        $eap_product->user_id = $productdata->user_id;
        $eap_product->order_id = $productdata->eap_order_id;
        $eap_product->created = $productdata->created;
        $eap_product->order_status = $productdata->order_status;
        $eap_product->product_name = $productdata->good_name;
        $eap_product->permalink = $productdata->permalink;


    return $eap_product;
}

