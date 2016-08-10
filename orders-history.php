<?php

if( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

add_action('admin_init',array('Eap_Orders_History_Table','update_status_order'));


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

