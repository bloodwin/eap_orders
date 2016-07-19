<?php
if (is_admin()):
    add_action('admin_head','eap_admin_scripts');
endif;

function eap_admin_scripts(){
#    wp_enqueue_script( 'jquery' );
#    wp_enqueue_script( 'eap_admin_scripts', rcl_addon_url('js/admin.js', __FILE__) );
}


