<?php

$session_type=get_option('settings_gpls_woo_rfq_cookie_or_phpsession',"rfq_cookie");
if($session_type !== "php_session"){
    return ;
}

    /**
     * WordPress session managment.
     *
     * Standardizes WordPress session data and uses either database transients or in-memory caching
     * for storing user session information.
     *
     * @package WordPress
     * @subpackage Session
     * @since   3.7.0
     */

    /**
     * Return the current cache expire setting.
     *
     * @return int
     */
    function RFQTK_php_session_cache_expire()
    {
        $wp_session = RFQTK_PHP_Session::get_instance();

        return $wp_session->cache_expiration();
    }

    /**
     * Alias of wp_session_write_close()
     */
    function RFQTK_php_session_commit()
    {
        RFQTK_php_session_write_close();
    }

    /**
     * Load a JSON-encoded string into the current session.
     *
     * @param string $data
     */
    function RFQTK_php_session_decode($data)
    {
        $wp_session = RFQTK_PHP_Session::get_instance();

        return $wp_session->json_in($data);
    }

    /**
     * Encode the current session's data as a JSON string.
     *
     * @return string
     */
    function RFQTK_php_session_encode()
    {
        $wp_session = RFQTK_PHP_Session::get_instance();

        return $wp_session->json_out();
    }

    /**
     * Regenerate the session ID.
     *
     * @param bool $delete_old_session
     *
     * @return bool
     */
    function RFQTK_php_session_regenerate_id($delete_old_session = false)
    {
        $wp_session = RFQTK_PHP_Session::get_instance();

        $wp_session->regenerate_id($delete_old_session);

        return true;
    }

    /**
     * Start new or resume existing session.
     *
     * Resumes an existing session based on a value sent by the _wp_session cookie.
     *
     * @return bool
     */
    function RFQTK_php_session_start()
    {
        $wp_session = RFQTK_PHP_Session::get_instance();
        do_action('php_session_start');

        return $wp_session->session_started();
    }

    if (!defined('WP_CLI') || false === WP_CLI) {
        add_action('plugins_loaded', 'RFQTK_php_session_start');
    }

    /**
     * Return the current session status.
     *
     * @return int
     */
    function RFQTK_php_session_status()
    {
        $wp_session = RFQTK_PHP_Session::get_instance();

        if ($wp_session->session_started()) {
            return PHP_SESSION_ACTIVE;
        }

        return PHP_SESSION_NONE;
    }

    /**
     * Unset all session variables.
     */
    function RFQTK_php_session_unset()
    {
        $wp_session = RFQTK_PHP_Session::get_instance();

        $wp_session->reset();
    }

    /**
     * Write session data and end session
     */
    function RFQTK_php_session_write_close()
    {
        $wp_session = RFQTK_PHP_Session::get_instance();

        $wp_session->write_data();
        do_action('wp_session_commit');
    }

    if (!defined('WP_CLI') || false === WP_CLI) {
        add_action('shutdown', 'RFQTK_php_session_write_close');
    }

    /**
     * Clean up expired sessions by removing data and their expiration entries from
     * the WordPress options table.
     *
     * This method should never be called directly and should instead be triggered as part
     * of a scheduled task or cron job.
     */

function RFQTK_php_session_reset()
{
    if (defined('WP_SETUP_CONFIG')) {
        return;
    }

    if (!defined('WP_INSTALLING')) {
        /**
         * Determine the size of each batch for deletion.
         *
         * @param int
         */


        if (defined('WP_INSTALLING')) {
            return 0;
        }

        global $wpdb;
        $limit = RFQTK_WP_SESSION_CLEAN_LIMIT;

        $limit = absint($limit);

        $limit = apply_filters('delete_old_sessions_filter', $limit);

        //custom table no wrappers or caching avaialable or needed
        // phpcs:disable WordPress.DB.DirectDatabaseQuery
        $count = $wpdb->query($wpdb->prepare("delete FROM {$wpdb->base_prefix}npxyz2021_sessions
          WHERE  misc_value = %s and  option_value = %s or  expiration <= %s LIMIT %d ",'phpsid','a:0:{}',time(),$limit)); //db call ok
// phpcs:enable  WordPress.DB.DirectDatabaseQuery
        return (int) ( $count );
    }


}



function RFQTK_php_session_cleanup_all()
{
    if (defined('WP_SETUP_CONFIG')) {
        return 0;
    }

    if (!defined('WP_INSTALLING')) {
        /**
         * Determine the size of each batch for deletion.
         *
         * @param int
         */


        if (defined('WP_INSTALLING')) {
            return 0;
        }

        if (defined('WP_SETUP_CONFIG')) {
            return 0;
        }

        if (defined('WP_INSTALLING')) {
            return 0;
        }

        $limit = RFQTK_WP_SESSION_CLEAN_LIMIT;

        global $wpdb;

        $limit = absint( $limit );

        $limit = apply_filters('delete_old_sessions_filter',$limit);

        {

          //  $sql = " delete FROM {$wpdb->base_prefix}npxyz2021_sessions
        //  WHERE misc_value='phpsid' and option_value = 'a:0:{}' or  expiration <= ". time() ." LIMIT " . $limit . " ";

            //db call ok; no-cache ok
            //   WordPress.DB.DirectDatabaseQuery
            //custom table no wrappers or caching avaialable or needed
            // phpcs:disable WordPress.DB.DirectDatabaseQuery
            $count = $wpdb->query($wpdb->prepare("delete FROM {$wpdb->base_prefix}npxyz2021_sessions
          WHERE  misc_value = %s and  option_value = %s or  expiration <= %s LIMIT %d ",'phpsid','a:0:{}',time(),$limit)); //db call ok
// phpcs:enable  WordPress.DB.DirectDatabaseQuery


            //  $wpdb->query($sql);

            //  $this->slide_expiration=true;

            return $count;
        }

    }

    do_action('RFQTK_php_session_cleanup');
}


add_action('RFQTK_php_session_daily_garbage_collection', 'RFQTK_php_session_cleanup_all');


function RFQTK_php_session_daily_garbage_collection()
{



    if (!wp_next_scheduled('RFQTK_php_session_daily_garbage_collection')) {
        wp_schedule_event(time(), 'daily', 'RFQTK_php_session_daily_garbage_collection');
    }


}

add_action('wp_loaded', 'RFQTK_php_session_daily_garbage_collection');
add_action('wp', 'RFQTK_php_session_daily_garbage_collection');


