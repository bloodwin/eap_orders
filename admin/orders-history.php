<?php

add_action('admin_init',array('Eap_Orders_History_Table','update_order_status'));

function eap_orders_page_options() {
    $option = 'per_page';
    $args = array(
        'label' => __( 'Orders', 'wp-recall' ),
        'default' => 50,
        'option' => 'eap_orders_per_page'
    );
    add_screen_option( $option, $args );
}

function eap_admin_orders_page(){
    $Eap_Orders_Global = new Eap_Orders_History_Table();
    $Eap_Orders_Global->prepare_items();

    echo '</pre><div class="wrap"><h2>' . __('Orders history', 'wp-recall') . '</h2>';
?>
    <form method="get"> 
        <input type="hidden" name="page" value="manage-eap">    
<?php
    $Eap_Orders_Global->months_dropdown('eap_orders');
    submit_button(__('Filter', 'wp-recall'), 'button', '', false, array('id' => 'search-submit'));
?>
    </form>
    <form method="post">
        <input type="hidden" name="page" value="manage-eap">    
<?php
    $Eap_Orders_Global->search_box(__('Search', 'wp-recall'), 'search_id');

    $Eap_Orders_Global->display();
?>
    </form>
</div>
<?php }

