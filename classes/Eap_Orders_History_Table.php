<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Eap_Orders_History_Table extends WP_List_Table {
    
    var $per_page = 50;
    var $current_page = 1;
    var $total_items;
    var $offset = 0;
    var $sum = 0;
    
    function __construct(){
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
        if( 'manage-rmag' != $page ) { return; }
        echo '<style type="text/css">';
        echo '.wp-list-table .column-order_id { width: 10%; }';
        echo '.wp-list-table .column-user_id { width: 25%; }';
        echo '.wp-list-table .column-total_amount { width: 10%; }';
        echo '.wp-list-table .column-total_price { width: 10%;}';
        echo '.wp-list-table .column-status { width: 15%;}';
        echo '.wp-list-table .column-created { width: 15%;}';
        echo '.wp-list-table .column-status_date { width: 15%;}';
        echo '</style>';
    }

    function no_items() {
        _e( 'No orders found.', 'wp-recall' );
    }

    function column_default(Eap_Order $item, $column_name ) {
        switch( $column_name ) { 
            case 'order_id':
                return $item->getOrderId();
            case 'user_id':
                return $item->getUserId().': '.get_the_author_meta('user_login',$item->getUserId());
            case 'total_amount':
                return $item->getTotalAmount();
            case 'total_price':
                return $item->getTotalPrice();
            case 'status':
                return $item->getStatus();
            case 'created':
                return $item->getCreated();
            case 'status_date':
                return $item->getStatusDate();
            default:
                return print_r( $item, true ) ;
        }
    }

    function get_columns(){
        $columns = array(
            'cb'            => '<input type="checkbox" />',
            'order_id'      => __('Order ID', 'wp-recall'),
            'user_id'       => __('Users', 'wp-recall'),
            'total_amount'  => __('Number products', 'wp-recall'),
            'total_price'   => __('Order sum', 'wp-recall'),
            'status'        => __('Status', 'wp-recall'),
            'created'       => __('Created', 'wp-recall'),
            'status_date'   => __('Status changed', 'wp-recall')
        );
        return $columns;
    }
    
    function column_order_id(Eap_Order $item){
      $actions = array(
            'order-details' => sprintf('<a href="?page=%s&action=%s&order=%s">' . __('Details', 'wp-recall') . '</a>', $_REQUEST['page'], 
                                        'order-details', $item->getOrderId()),
        );
      return sprintf('%1$s %2$s', $item->getOrderId(), $this->row_actions($actions) );
    }
    
    function column_status(Eap_Order $item){
        $status = Eap_Order_Statuses::getInstance();
        $actions = array(
            'new'=> sprintf('<a href="?page=%s&action=%s&status=%s&order=%s">'.__( 'New', 'wp-recall' ).'</a>',$_REQUEST['page'],'update_status',1,$item->getOrderId()),
            'confirmed'    => sprintf('<a href="?page=%s&action=%s&status=%s&order=%s">'.__( 'Confirmed', 'wp-recall' ).'</a>',$_REQUEST['page'],'update_status',2,$item->getOrderId()),          
            'sent'    => sprintf('<a href="?page=%s&action=%s&status=%s&order=%s">'.__( 'Sent', 'wp-recall' ).'</a>',$_REQUEST['page'],'update_status',3,$item->getOrderId()),
            'cancelled'=> sprintf('<a href="?page=%s&action=%s&status=%s&order=%s">'.__( 'Cancelled', 'wp-recall' ).'</a>',$_REQUEST['page'],'update_status',4,$item->getOrderId()),
            'in_hands_paid'  => sprintf('<a href="?page=%s&action=%s&status=%s&order=%s">'.__( 'In_hands_paid', 'wp-recall' ).'</a>',$_REQUEST['page'],'update_status',5,$item->getOrderId()),
            'unconfirmed'   => sprintf('<a href="?page=%s&action=%s&status=%s&order=%s">'.__( 'Unconfirmed', 'wp-recall' ).'</a>',$_REQUEST['page'],'update_status',6,$item->getOrderId()),
            'delayed'=> sprintf('<a href="?page=%s&action=%s&status=%s&order=%s">'.__( 'Delayed', 'wp-recall' ).'</a>',$_REQUEST['page'],'update_status',7,$item->getOrderId()),
            'refused'    => sprintf('<a href="?page=%s&action=%s&status=%s&order=%s">'.__( 'Refused', 'wp-recall' ).'</a>',$_REQUEST['page'],'update_status',8,$item->getOrderId()),          
            'delivered_unpaid'    => sprintf('<a href="?page=%s&action=%s&status=%s&order=%s">'.__( 'Delivered_unpaid', 'wp-recall' ).'</a>',$_REQUEST['page'],'update_status',9,$item->getOrderId()),
            'ready_for_delivery'=> sprintf('<a href="?page=%s&action=%s&status=%s&order=%s">'.__( 'Ready_for_delivery', 'wp-recall' ).'</a>',$_REQUEST['page'],'update_status',10,$item->getOrderId()),
            'problem'  => sprintf('<a href="?page=%s&action=%s&status=%s&order=%s">'.__( 'Problem', 'wp-recall' ).'</a>',$_REQUEST['page'],'update_status',11,$item->getOrderId()),
            'delivered_paid'=> sprintf('<a href="?page=%s&action=%s&status=%s&order=%s">'.__( 'Delivered_paid', 'wp-recall' ).'</a>',$_REQUEST['page'],'update_status',12,$item->getOrderId()),
            'pending'    => sprintf('<a href="?page=%s&action=%s&status=%s&order=%s">'.__( 'Pending', 'wp-recall' ).'</a>',$_REQUEST['page'],'update_status',13,$item->getOrderId()),          
            'specified'    => sprintf('<a href="?page=%s&action=%s&status=%s&order=%s">'.__( 'Specified', 'wp-recall' ).'</a>',$_REQUEST['page'],'update_status',14,$item->getOrderId()),
            'in_hands_unpaid'=> sprintf('<a href="?page=%s&action=%s&status=%s&order=%s">'.__( 'In_hands_unpaid', 'wp-recall' ).'</a>',$_REQUEST['page'],'update_status',15,$item->getOrderId()),
            'wanted'  => sprintf('<a href="?page=%s&action=%s&status=%s&order=%s">'.__( 'Wanted', 'wp-recall' ).'</a>',$_REQUEST['page'],'update_status',16,$item->getOrderId()),
            'absence'=> sprintf('<a href="?page=%s&action=%s&status=%s&order=%s">'.__( 'Absence', 'wp-recall' ).'</a>',$_REQUEST['page'],'update_status',17,$item->getOrderId()),
            'returned'    => sprintf('<a href="?page=%s&action=%s&status=%s&order=%s">'.__( 'Returned', 'wp-recall' ).'</a>',$_REQUEST['page'],'update_status',18,$item->getOrderId()),          
            'notice1'    => sprintf('<a href="?page=%s&action=%s&status=%s&order=%s">'.__( 'Notice1', 'wp-recall' ).'</a>',$_REQUEST['page'],'update_status',19,$item->getOrderId()),
            'notice2'=> sprintf('<a href="?page=%s&action=%s&status=%s&order=%s">'.__( 'Notice2', 'wp-recall' ).'</a>',$_REQUEST['page'],'update_status',20,$item->getOrderId()),
            'delete'  => sprintf('<a href="?page=%s&action=%s&order=%s">'.__( 'Delete', 'wp-recall' ).'</a>',$_REQUEST['page'],'delete',$item->getOrderId()),
          );

        unset($actions[$status[$item->getStatus()]]);
      
        return sprintf('%1$s %2$s', $item->getStatus(), $this->row_actions($actions) );
    }
    
    function column_user_id(Eap_Order $item){
      $actions = array(
            'all-orders'    => sprintf('<a href="?page=%s&action=%s&user=%s">'.__( 'Get user orders', 'wp-recall' ).'</a>',$_REQUEST['page'],
                                        'all-orders',$item->getUserId()),
        );
      return sprintf('%1$s %2$s', $item->getUserId().': '.get_the_author_meta('user_login',$item->getUserId()), $this->row_actions($actions) );
    }

    function get_bulk_actions() {
      $actions = Eap_Orders_Statuses::getInstance();
      $actions['delete'] = __( 'Delete', 'wp-recall' );
      return $actions;
    }

    function column_cb(Eap_Order $item) {
        return sprintf(
            '<input type="checkbox" name="orders[]" value="%s" />', $item->getOrderId()
        );    
    }
    
    function months_dropdown( $post_type ) {
        global $wpdb,$wp_locale;

        $month = $wpdb->get_results("
                SELECT DISTINCT YEAR( created ) AS year, MONTH( created ) AS month
                FROM ". EAP_PREF ."orders
                ORDER BY created DESC
        ");

        $months = apply_filters( 'months_dropdown_results', $month, $post_type );
        
        $month_count = count( $months );
        if (!$month_count || ( 1 == $month_count && 0 == $months[0]->month )) {
            return;
        }

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
                        if ( 0 == $arc_row->year) {
                            continue;
                        }
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
    
    function get_data(){
        
        global $wpdb;

        $args = array();

        if($_GET['m']){

            $args['year'] = substr($_GET['m'],0,4);
            $args['month'] = substr($_GET['m'],5,6);

            if($_GET['sts']) $args['order_status'] = intval($_GET['sts']);

        }else{
            if($_GET['sts']){
                $args['status'] = intval($_GET['sts']);
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
        
        $orders = Eap_Orders_History::getHistoryByArgs(&$wpdb, EAP_PREF, $args);
        
        if(!$orders) { return false; }
        
        $args['count'] = 1;
        
        $this->total_items = count($orders);
        
        foreach($orders as $order){
            $order_id = $order->getOrderId();
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

