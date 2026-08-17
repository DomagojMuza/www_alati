<?php
//contributer: neahplugins
//contributer: ericmann from www.patreon.com


global $session_type;
$session_type=get_option('settings_gpls_woo_rfq_cookie_or_phpsession',"rfq_cookie");

if($session_type !== "php_session"){

    include ('wp-cookie-session.php');

}else{
    include ('php-cookie-session.php');
}


if(!function_exists('gpls_woo_get_session')){
    function gpls_woo_get_session()
    {

        if(is_admin()){

            return false;
        }

        global $session_type;

        if($session_type !== "php_session"){
            $wp_session = RFQTK_WP_Session::get_instance();
            return $wp_session;
        }else{
            $wp_session = RFQTK_PHP_Session::get_instance();
            return $wp_session;
        }

    }
}


