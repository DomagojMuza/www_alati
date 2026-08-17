<?php
//contributer: neahplugins
//contributer: ericmann from www.patreon.com
//**
$session_type=get_option('settings_gpls_woo_rfq_cookie_or_phpsession',"rfq_cookie");
if($session_type === "php_session"){
    return ;
}

$cookie_prepend=get_option('settings_gpls_woo_rfq_cookie_prepend',"");

$cookie_days_keep= (get_option('settings_gpls_woo_rfq_cookie_days_keep',30));
if(!is_int($cookie_days_keep)){
    $cookie_days_keep=30;
}

$records_clean_limit=get_option('settings_gpls_woo_rfq_records_clean_limit',10000);


$cookie_append=get_option('settings_gpls_woo_rfq_cookie',false);

if ($cookie_append == false) {

    require_once( ABSPATH . 'wp-includes/class-phpass.php' );
    $hash = new \PasswordHash( 8, false );
    $cookie_append = md5( $hash->get_random_bytes( 1 ) );
    update_option('settings_gpls_woo_rfq_cookie',$cookie_append);
}

if (!defined('RFQTK_WP_SESSION_COOKIE')) {

    define('RFQTK_WP_SESSION_COOKIE', $cookie_prepend.'rfqtk_wp_session_'.$cookie_append);
}

if (!defined('RFQTK_WP_SESSION_DAYS_KEEP')) {

    DEFINE('RFQTK_WP_SESSION_DAYS_KEEP', is_int($cookie_days_keep)?$cookie_days_keep : 30);

}


if (!defined('RFQTK_WP_SESSION_EXPIRATION_VARIANT')) {
    DEFINE('RFQTK_WP_SESSION_EXPIRATION_VARIANT', RFQTK_WP_SESSION_DAYS_KEEP * 60 * 60 * 25);

   // DEFINE('RFQTK_WP_SESSION_EXPIRATION_VARIANT', 60 );
}

if (!defined('RFQTK_WP_SESSION_EXPIRATION')) {

    DEFINE('RFQTK_WP_SESSION_EXPIRATION', (RFQTK_WP_SESSION_DAYS_KEEP) * 60 * 60 * 24);
   // DEFINE('RFQTK_WP_SESSION_EXPIRATION', 61);
}
if (!defined('RFQTK_WP_SESSION_CLEAN_LIMIT')) {
    DEFINE('RFQTK_WP_SESSION_CLEAN_LIMIT', $records_clean_limit);//clean up this many records during each garbage collection
}

if (!class_exists('RFQTK_Recursive_ArrayAccess')) {
    include 'includes/class-recursive-arrayaccess.php';
}

if (!class_exists('RFQTK_WP_Session_Utils')) {
    include 'includes/class-wp-session-utils.php';
}

// Include WP_CLI routines early
if (defined('RFQTK_WP_CLI') && defined('WP_CLI')) {
    include 'includes/wp-cli.php';
}

// Only include the functionality if it's not pre-defined.
if (!class_exists('RFQTK_WP_Session')) {
    include 'includes/class-wp-session.php';
    include 'includes/wp-session.php';
}



if(!function_exists('gpls_woo_rfq_cart_tran_key')) {

    function gpls_woo_rfq_cart_tran_key()
    {

        $wp_session = RFQTK_WP_Session::get_instance();

        $tran_key = apply_filters('set_gpls_rfq_cart_tran_key', $wp_session->session_id);

        return $wp_session->session_id;

    }
}


if(!function_exists('gpls_woo_rfq_get_item')) {

    function gpls_woo_rfq_get_item($key)
    {

        $wp_session = RFQTK_WP_Session::get_instance();

        $key = sanitize_key($key);

        return isset($wp_session[$key]) ? maybe_unserialize($wp_session[$key]) : false;

    }
}


if(!function_exists('gpls_woo_rfq_cart_set')) {

    function gpls_woo_rfq_cart_set($key, $value)
    {

        $wp_session = RFQTK_WP_Session::get_instance();

        $key = sanitize_key($key);

        if (is_array($value)) {

            $wp_session->write_data();
            $wp_session[$key] = serialize($value);
        } else {

            $wp_session[$key] = $value;
        }

        $wp_session->write_data();

        return isset($wp_session[$key]) ? maybe_unserialize($wp_session[$key]) : false;
    }
}

if(!function_exists('gpls_woo_rfq_cart_delete')) {

    function gpls_woo_rfq_cart_delete($key)
    {
        $wp_session = RFQTK_WP_Session::get_instance();

        $key = sanitize_key($key);

        unset($wp_session[$key]);

        $wp_session->write_data();

        return $wp_session[$key];
    }
}
