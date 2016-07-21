<?php

if( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

add_action('admin_init',array('Eap_Orders_History_Table','update_status_order'));

class Eap_Orders_History_Table extends WP_List_Table {
    
    var $per_page = 50;
    var $current_page = 1;
    var $total_items;
    var $offset = 0;
    var $sum = 0;
    
    function __construct(){
        global $status, $page;
        parent::__construct( array(
            'singular'  => __( 'order', 'wp-recall' ),
            'plural'    => __( 'orders', 'wp-recall' ),
            'ajax'      => false
        ) );
        
        $this->per_page = $this->get_items_per_page('eap_orders_per_page', 50);
        $this->current_page = $this->get_pagenum();
        $this->offset = ($this->current_page-1)*$this->per_page;

        add_action( 'admin_head', array( &$this, 'admin_header' ) );            
    }
    
    function admin_header() {
        $page = ( isset($_GET['page'] ) ) ? esc_attr( $_GET['page'] ) : false;
        if( 'manage-rmag' != $page )
        return;
        echo '<style type="text/css">';
        echo '.wp-list-table .column-order_id { width: 10%; }';
        echo '.wp-list-table .column-order_author { width: 25%; }';
        echo '.wp-list-table .column-items_count { width: 10%; }';
        echo '.wp-list-table .column-order_price { width: 10%;}';
        echo '.wp-list-table .column-order_status { width: 15%;}';
        echo '.wp-list-table .column-created_date { width: 15%;}';
        echo '.wp-list-table .column-status_date { width: 15%;}';
        echo '</style>';
    }

    function no_items() {
        _e( 'No orders found.', 'wp-recall' );
    }

    function column_default( $item, $column_name ) {
        switch( $column_name ) { 
            case 'order_id':
                return $item->order_id;
            case 'order_author':
                return $item->order_author.': '.get_the_author_meta('user_login',$item->order_author);
            case 'items_count':
                return $item->items_count;
            case 'order_price':
                return $item->order_price;
            case 'order_status':
                return $item->order_status;
            case 'created_date':
                return $item->created;
            case 'status_date':
                return $item->order_status_date;
            default:
                return print_r( $item, true ) ;
        }
    }

    function get_columns(){
        $columns = array(
            'cb'        => '<input type="checkbox" />',
            'order_id' => __( 'Order ID', 'wp-recall' ),
            'order_author' => __( 'Users', 'wp-recall' ),
            'items_count' => __( 'Number products', 'wp-recall' ),
            'order_price'    => __( 'Order sum', 'wp-recall' ),
            'order_status'    => __( 'Status', 'wp-recall' ),
            'created_date'      => __( 'Created', 'wp-recall' ),
            'status_date'      => __( 'Status changed', 'wp-recall' )
        );
         return $columns;
    }
    
    function column_order_id($item){
      $actions = array(
            'order-details'    => sprintf('<a href="?page=%s&action=%s&order=%s">'.__( 'Details', 'wp-recall' ).'</a>',$_REQUEST['page'],'order-details',$item->order_id),
        );
      return sprintf('%1$s %2$s', $item->order_id, $this->row_actions($actions) );
    }

    function column_order_status($item){
        $status = array(
              1 =>'new',
              2 =>'confirmed',
              3 =>'sent',
              4 =>'cancelled',
              5 =>'in_hands_paid',
              6 =>'unconfirmed',
              7 =>'delayed',
              8 =>'refused',
              9 =>'delivered_unpaid',
              10=>'ready_for_delivery',
              11=>'problem',
              12=>'delivered_paid',
              13=>'pending',
              14=>'specified',
              15=>'in_hands_unpaid',
              16=>'wanted',
              17=>'absence',
              18=>'returned',
              19=>'notice1',
              20=>'notice2'
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
"notice2"   Напоминание 2
*/
        $actions = array(
            'new'=> sprintf('<a href="?page=%s&action=%s&status=%s&order=%s">'.__( 'New', 'wp-recall' ).'</a>',$_REQUEST['page'],'update_status',1,$item->order_id),
            'confirmed'    => sprintf('<a href="?page=%s&action=%s&status=%s&order=%s">'.__( 'Confirmed', 'wp-recall' ).'</a>',$_REQUEST['page'],'update_status',2,$item->order_id),          
            'sent'    => sprintf('<a href="?page=%s&action=%s&status=%s&order=%s">'.__( 'Sent', 'wp-recall' ).'</a>',$_REQUEST['page'],'update_status',3,$item->order_id),
            'cancelled'=> sprintf('<a href="?page=%s&action=%s&status=%s&order=%s">'.__( 'Cancelled', 'wp-recall' ).'</a>',$_REQUEST['page'],'update_status',4,$item->order_id),
            'in_hands_paid'  => sprintf('<a href="?page=%s&action=%s&status=%s&order=%s">'.__( 'In_hands_paid', 'wp-recall' ).'</a>',$_REQUEST['page'],'update_status',5,$item->order_id),
            'unconfirmed'   => sprintf('<a href="?page=%s&action=%s&status=%s&order=%s">'.__( 'Unconfirmed', 'wp-recall' ).'</a>',$_REQUEST['page'],'update_status',6,$item->order_id),
            'delayed'=> sprintf('<a href="?page=%s&action=%s&status=%s&order=%s">'.__( 'Delayed', 'wp-recall' ).'</a>',$_REQUEST['page'],'update_status',7,$item->order_id),
            'refused'    => sprintf('<a href="?page=%s&action=%s&status=%s&order=%s">'.__( 'Refused', 'wp-recall' ).'</a>',$_REQUEST['page'],'update_status',8,$item->order_id),          
            'delivered_unpaid'    => sprintf('<a href="?page=%s&action=%s&status=%s&order=%s">'.__( 'Delivered_unpaid', 'wp-recall' ).'</a>',$_REQUEST['page'],'update_status',9,$item->order_id),
            'ready_for_delivery'=> sprintf('<a href="?page=%s&action=%s&status=%s&order=%s">'.__( 'Ready_for_delivery', 'wp-recall' ).'</a>',$_REQUEST['page'],'update_status',10,$item->order_id),
            'problem'  => sprintf('<a href="?page=%s&action=%s&status=%s&order=%s">'.__( 'Problem', 'wp-recall' ).'</a>',$_REQUEST['page'],'update_status',11,$item->order_id),
            'delivered_paid'=> sprintf('<a href="?page=%s&action=%s&status=%s&order=%s">'.__( 'Delivered_paid', 'wp-recall' ).'</a>',$_REQUEST['page'],'update_status',12,$item->order_id),
            'pending'    => sprintf('<a href="?page=%s&action=%s&status=%s&order=%s">'.__( 'Pending', 'wp-recall' ).'</a>',$_REQUEST['page'],'update_status',13,$item->order_id),          
            'specified'    => sprintf('<a href="?page=%s&action=%s&status=%s&order=%s">'.__( 'Specified', 'wp-recall' ).'</a>',$_REQUEST['page'],'update_status',14,$item->order_id),
            'in_hands_unpaid'=> sprintf('<a href="?page=%s&action=%s&status=%s&order=%s">'.__( 'In_hands_unpaid', 'wp-recall' ).'</a>',$_REQUEST['page'],'update_status',15,$item->order_id),
            'wanted'  => sprintf('<a href="?page=%s&action=%s&status=%s&order=%s">'.__( 'Wanted', 'wp-recall' ).'</a>',$_REQUEST['page'],'update_status',16,$item->order_id),
            'absence'=> sprintf('<a href="?page=%s&action=%s&status=%s&order=%s">'.__( 'Absence', 'wp-recall' ).'</a>',$_REQUEST['page'],'update_status',17,$item->order_id),
            'returned'    => sprintf('<a href="?page=%s&action=%s&status=%s&order=%s">'.__( 'Returned', 'wp-recall' ).'</a>',$_REQUEST['page'],'update_status',18,$item->order_id),          
            'notice1'    => sprintf('<a href="?page=%s&action=%s&status=%s&order=%s">'.__( 'Notice1', 'wp-recall' ).'</a>',$_REQUEST['page'],'update_status',19,$item->order_id),
            'notice2'=> sprintf('<a href="?page=%s&action=%s&status=%s&order=%s">'.__( 'Notice2', 'wp-recall' ).'</a>',$_REQUEST['page'],'update_status',20,$item->order_id),
            'delete'  => sprintf('<a href="?page=%s&action=%s&order=%s">'.__( 'Delete', 'wp-recall' ).'</a>',$_REQUEST['page'],'delete',$item->order_id),
          );

        unset($actions[$status[$item->order_status]]);
      
        return sprintf('%1$s %2$s', $item->order_status, $this->row_actions($actions) );
    }
    
    function column_order_author($item){
      $actions = array(
            'all-orders'    => sprintf('<a href="?page=%s&action=%s&user=%s">'.__( 'Get user orders', 'wp-recall' ).'</a>',$_REQUEST['page'],'all-orders',$item->order_author),
        );
      return sprintf('%1$s %2$s', $item->order_author.': '.get_the_author_meta('user_login',$item->order_author), $this->row_actions($actions) );
    }

    function get_bulk_actions() {
      $actions = eap_order_statuses();
      $actions['delete'] = __( 'Delete', 'wp-recall' );
      return $actions;
    }

    function column_cb($item) {
        return sprintf(
            '<input type="checkbox" name="orders[]" value="%s" />', $item->order_id
        );    
    }
    
    function months_dropdown( $post_type ) {
        global $wpdb,$wp_locale;

        $months = $wpdb->get_results("
                SELECT DISTINCT YEAR( created ) AS year, MONTH( created ) AS month
                FROM ".EAP_PREF ."orders
                ORDER BY created DESC
        ");

        $months = apply_filters( 'months_dropdown_results', $months, $post_type );
        
        $month_count = count( $months );
        if ( !$month_count || ( 1 == $month_count && 0 == $months[0]->month ) )
                return;
        
        $m = isset( $_GET['m'] ) ? $_GET['m'] : 0;
        $status = isset( $_GET['sts'] ) ? $_GET['sts'] : 0; ?>
        <label for="filter-by-status" class="screen-reader-text"><?php _e( 'Filter by date' ); ?></label>
        <?php $sts = eap_order_statuses(); ?>
        <select name="sts" id="filter-by-status">
            <option<?php selected( $status, 0 ); ?> value="0"><?php _e( 'All', 'wp-recall' ); ?></option>
            <?php foreach ( $sts as $id=>$name ) {
                    printf( "<option %s value='%s'>%s</option>\n",
                            selected( $id, $status, false ),
                            $id,
                            $name
                    );
            } ?>
        </select>
        <select name="m" id="filter-by-date">
            <option<?php selected( $m, 0 ); ?> value="0"><?php _e( 'All dates' ); ?></option>
            <?php foreach ( $months as $arc_row ) {
                    if ( 0 == $arc_row->year )
                            continue;
                    $month = zeroise( $arc_row->month, 2 );
                    $year = $arc_row->year;
                    printf( "<option %s value='%s'>%s</option>\n",
                            selected( $m, $year .'-'. $month, false ),
                            esc_attr( $arc_row->year .'-'. $month ),
                            /* translators: 1: month name, 2: 4-digit year */
                            sprintf( __( '%1$s %2$d' ), $wp_locale->get_month( $month ), $year )
                    );
            } ?>
        </select>
    <?php }
    
    static function update_status_order(){
        global $wpdb;
        
        $page = ( isset($_GET['page'] ) ) ? esc_attr( $_GET['page'] ) : false;
        if( 'manage-eap' != $page ) return;
        
        if(isset($_REQUEST['action'])){
            if(isset($_POST['action'])){
                if(!isset($_POST['orders'])) return;
                $action = $_POST['action'];
                foreach($_POST['orders'] as $order_id){
                    switch($action){
                        case 'delete': eap_delete_order($order_id); break;
                        default: eap_update_status_order($order_id,$action);
                    }
                }
                wp_redirect($_POST['_wp_http_referer']);exit;                
            }
            if(isset($_GET['action'])){
                switch($_GET['action']){
                    case 'update_status': return eap_update_status_order($_REQUEST['order'],$_REQUEST['status']);
                    case 'delete': return eap_delete_order($_REQUEST['order']);
                }
                
                return;
            }
            
    }
    }    

    function get_data(){
        
        global $eap_order,$eap_product,$wpdb;

        $args = array();

        if($_GET['m']){

            $args['year'] = substr($_GET['m'],0,4);
            $args['month'] = substr($_GET['m'],5,6);

            if($_GET['sts']) $args['order_status'] = intval($_GET['sts']);

        }else{
            if($_GET['sts']){
                $args['order_status'] = intval($_GET['sts']);
            }elseif($_GET['user']){
                $args['user_id'] = intval($_GET['user']);
            }else{
                $args['status_not_in'] = 21;
            }
    }
        
        if($_POST['s']){
            $args['order_id'] = intval($_POST['s']);
        }
        
        $args['per_page'] = $this->per_page;
        $args['offset'] = $this->offset;
        
        $orders = eap_get_orders($args);
        
        if(!$orders) return false;
        
        $args['count'] = 1;
        
        $this->total_items = eap_get_orders($args);
        
        foreach($orders as $order_id=>$orderfull){
            $orderdata = eap_get_order($order_id);
            vardump($orderdata, "ORDERDATA: ");

            $order = eap_setup_orderdata($orderdata);
            $items[$order_id] = $order;
        }

        return $items;
        
    }

    function prepare_items() {
        
        $data = $this->get_data();
        $this->_column_headers = $this->get_column_info();
        $this->set_pagination_args( array(
            'total_items' => $this->total_items,
            'per_page'    => $this->per_page
        ) );

        $this->items = $data;
        
    }
}

function eap_orders_page_options() {
    global $Eap_Orders;
    $option = 'per_page';
    $args = array(
        'label' => __( 'Orders', 'wp-recall' ),
        'default' => 50,
        'option' => 'eap_orders_per_page'
    );
    add_screen_option( $option, $args );
    $Eap_Orders = new Eap_Orders_History_Table();
}

function eap_admin_orders_page(){
    global $Eap_Orders;

  
  $Eap_Orders->prepare_items();

  echo '</pre><div class="wrap"><h2>'.__('Orders history','wp-recall').'</h2>';

  echo eap_get_chart_orders($Eap_Orders->items);
   ?>
    <form method="get"> 
    <input type="hidden" name="page" value="manage-eap">    
    <?php
    $Eap_Orders->months_dropdown('eap_orders'); 
    submit_button( __( 'Filter', 'wp-recall' ), 'button', '', false, array('id' => 'search-submit') ); ?>
    </form>
    <form method="post">
    <input type="hidden" name="page" value="manage-eap">    
    <?php
    $Eap_Orders->search_box( __( 'Search', 'wp-recall' ), 'search_id' );
    
    $Eap_Orders->display(); ?>
  </form>
</div>
<?php }

