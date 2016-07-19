<?php

class Eap_Order{
    
    public $user_id = 0;
    public $userdata = array();
    public $orders_page = 0;
    public $is_error = 0;
    public $check_amount;
    public $order_id;
    public $amount = array('success','error');
    
    function __construct(){
        global $eap_options,$user_ID;
        $this->user_id = $user_ID;
        $this->check_amount = (isset($eap_options['products_warehouse_recall']))? $eap_options['products_warehouse_recall']: 0;
    }
    
    function error($code,$error){
        $this->is_error = $code;
        $wp_errors = new WP_Error();
        $wp_errors->add( $code, $error );
        return $wp_errors;
    }
    
    function next_order_id(){
        global $wpdb;

        $pay_max = $wpdb->get_var("SELECT MAX(order_id) FROM ".EAP_PREF ."orders");

        if($pay_max) $order_id = $pay_max+1;
        else $order_id = rand(0,100);

        return $order_id;
    }
    

    function get_order_details($order_id){
        
    }

    function get_ip(){
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip=$_SERVER['HTTP_CLIENT_IP'];
        }elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
            $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
        }else{
            $ip=$_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }

    function send_mail($order_details){
        global $eap_options,$rcl_options,$eap_order;
        
        $table_order = rcl_get_include_template('order.php',__FILE__);

        $subject = __('Order data','wp-recall').' №'.$this->order_id;

        $textmail = '
        <p>'.__('This user has formed a purchase','wp-recall').' "'.get_bloginfo('name').'".</p>
        <h3>'.__('Information about the customer','wp-recall').':</h3>
        <p><b>'.__('Name','wp-recall').'</b>: '.get_the_author_meta('display_name',$this->user_id).'</p>
        <p><b>'.__('Email','wp-recall').'</b>: '.get_the_author_meta('user_email',$this->user_id).'</p>
        <h3>'.__('The data obtained at registration','wp-recall').':</h3>
        '.$order_details.'
        <p>'.sprintf(__('Order №%d received the status of "%s"','wp-recall'),$this->order_id,eap_get_status_name_order(1)).'.</p>
        <h3>'.__('Order details','wp-recall').':</h3>
        '.$table_order.'
        <p>'.__('Link to control order','wp-recall').':</p>
        <p>'.admin_url('admin.php?page=manage-eap&order-id='.$this->order_id).'</p>';

        $admin_email = $eap_options['admin_email_magazin_recall'];
        if($admin_email){
                rcl_mail($admin_email, $subject, $textmail);
        }else{
            $users = get_users( array('role' => 'administrator') );
            foreach((array)$users as $userdata){
                $email = $userdata->user_email;
                rcl_mail($email, $subject, $textmail);
            }
        }

        $email = get_the_author_meta('user_email',$this->user_id);

        $textmail = '';

        $textmail .= '
        <p>'.__('You have formed a purchase','wp-recall').' "'.get_bloginfo('name').'".</p>
        <h3>'.__('Order details','wp-recall').'</h3>
        <p>'.sprintf(__('Order №%d received the status of "%s"','wp-recall'),$this->order_id,eap_get_status_name_orderp(1)).'.</p>
        '.$table_order;

        $link = rcl_format_url(get_author_posts_url($this->user_id),'orders');
        $textmail .= '<p>'.__('Link to control order','wp-recall').': <a href="'.$link.'">'.$link.'</a></p>';

        $mail = array(
            'email'=>$email,
            'user_id'=>$this->user_id,
            'content'=>$textmail,
            'subject'=>$subject
        );

        $maildata = apply_filters('mail_insert_order_rcl',$mail,$this->order_id);

        rcl_mail($maildata['email'], $maildata['subject'], $maildata['content']);
    }
}
