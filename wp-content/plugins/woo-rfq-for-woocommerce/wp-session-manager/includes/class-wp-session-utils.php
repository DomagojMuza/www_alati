<?php

/**
 * Utility class for sesion utilities
 *
 * THIS CLASS SHOULD NEVER BE INSTANTIATED
 */

$session_type=get_option('settings_gpls_woo_rfq_cookie_or_phpsession',"rfq_cookie");
if($session_type === "php_session"){
    return ;
}

if(!class_exists('RFQTK_WP_Session_Utils')){
    #[\AllowDynamicProperties]
    class RFQTK_WP_Session_Utils
{
    /**
     * Count the total sessions in the database.
     *
     * @return int
     * @global wpdb $wpdb
     *
     */
    public static function count_sessions()
    {
        global $wpdb;

     //   $query = "SELECT distinct COUNT(*) FROM {$wpdb->base_prefix}nplugins1_sessions
//WHERE option_name LIKE '_rfqtk_wp_session_%' and misc_value <>'a:0:{}'";

        //db call ok; no-cache ok
        //custom table no wrappers or caching avaialable or needed
        // phpcs:disable WordPress.DB.DirectDatabaseQuery
        $sessions =  $wpdb->get_var($wpdb->query($wpdb->prepare("SELECT distinct COUNT(*) FROM {$wpdb->base_prefix}nplugins1_sessions 
        WHERE option_name LIKE %s and misc_value <> %s",'_rfqtk_wp_session_%','a:0:{}'))); //db call ok

// phpcs:enable  WordPress.DB.DirectDatabaseQuery

      //  $sessions = $wpdb->get_var($query);

        return absint($sessions);
    }

    /**
     * Create a new, random session in the database.
     *
     * @param null|string $date
     */
    public static function create_dummy_session($date = null)
    {
        // Generate our date
       /* if (null !== $date) {
            $time = strtotime($date);

            if (false === $time) {
                $date = null;
            } else {
                $expires = date('U', strtotime($date));
            }
        }

        // If null was passed, or if the string parsing failed, fall back on a default
        if (null === $date) {

            $expires = time() + (int)apply_filters('_rfqtk_wp_session_expiration', RFQTK_WP_SESSION_EXPIRATION);
        }

        $session_id = self::generate_id();*/

        // Store the session
       // RFQTK_WP_Session::get_instance()->np_add_session("_rfqtk_wp_session_{$session_id}", array(), '', 'no');

    }


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


            // Delete a batch of old sessions
            RFQTK_WP_Session_Utils::delete_all_sessions();
        }


    }

    /**
     * Delete old sessions from the database.
     *
     * @param int $limit Maximum number of sessions to delete.
     *
     * @return int Sessions deleted.
     * @global wpdb $wpdb
     *
     */
    public static function delete_old_sessions($limit = RFQTK_WP_SESSION_CLEAN_LIMIT)
    {


        if (defined('WP_INSTALLING')) {
            return 0;
        }

        if (defined('WP_SETUP_CONFIG')) {
            return 0;
        }

        if (defined('WP_INSTALLING')) {
            return 0;
        }


        global $wpdb;

        $limit = absint($limit);

        $limit = apply_filters('delete_old_sessions_filter', $limit);

        {

            //db call ok; no-cache ok
            //custom table no wrappers or caching avaialable or needed
            // phpcs:disable WordPress.DB.DirectDatabaseQuery
            $sessions = $wpdb->query($wpdb->prepare("delete FROM {$wpdb->base_prefix}npxyz2021_sessions
          WHERE  misc_value = %s and  option_value = %s or  expiration <= %s LIMIT %d  ",'rfq_session','a:0:{}',time(),$limit)); //db call ok
// phpcs:enable  WordPress.DB.DirectDatabaseQuery



            return 0;
        }


    }


    /**
     * Remove all sessions from the database, regardless of expiration.
     *
     * @return int Sessions deleted
     * @global wpdb $wpdb
     *
     */
    public static function delete_all_sessions()
    {


        if (defined('WP_INSTALLING')) {
            return 0;
        }

        global $wpdb;
        $limit = RFQTK_WP_SESSION_CLEAN_LIMIT;

        /*$count = $wpdb->query("DELETE FROM {$wpdb->base_prefix}npxyz2021_sessions
        WHERE misc_value='rfq_session' and  option_name LIKE '_rfqtk_wp_session_%'" . " LIMIT " . $limit . " ");*/


        //db call ok; no-cache ok
        //   WordPress.DB.DirectDatabaseQuery
        //custom table no wrappers or caching avaialable or needed
        // phpcs:disable WordPress.DB.DirectDatabaseQuery
        $count = $wpdb->query($wpdb->prepare("DELETE FROM {$wpdb->base_prefix}npxyz2021_sessions 
        WHERE misc_value=%s and  option_name LIKE %s LIMIT %s " ,'rfq_session','_rfqtk_wp_session_%',$limit)); //db call ok
// phpcs:enable  WordPress.DB.DirectDatabaseQuery

        return (int)($count);
    }

    /**
     * Generate a new, random session ID.
     *
     * @return string
     */
    public static function generate_id()
    {
        require_once(ABSPATH . 'wp-includes/class-phpass.php');
        $hash = new \PasswordHash(8, false);
//echo md5( $hash->get_random_bytes( 32 ) );
        return md5($hash->get_random_bytes(32));
    }

    public static function fix_old_sessions($limit = RFQTK_WP_SESSION_CLEAN_LIMIT)
    {


        if (defined('WP_INSTALLING')) {
            return 0;
        }

        global $wpdb;

       /* $sql = " delete FROM {$wpdb->base_prefix}options
        WHERE misc_value='rfq_session' and  option_name LIKE '_rfqtk_wp_session_%' LIMIT " . $limit . " ";*/


        //db call ok; no-cache ok
        //   WordPress.DB.DirectDatabaseQuery
        //custom table no wrappers or caching avaialable or needed
        // phpcs:disable WordPress.DB.DirectDatabaseQuery
        $sql = $wpdb->query($wpdb->prepare("DELETE FROM {$wpdb->base_prefix}npxyz2021_sessions 
        WHERE misc_value=%s and  option_name LIKE %s LIMIT %s " ,'rfq_session','_rfqtk_wp_session_%',$limit)); //db call ok
// phpcs:enable  WordPress.DB.DirectDatabaseQuery
       // $wpdb->query($sql);

    }


    public static function reset_sessions($limit = RFQTK_WP_SESSION_CLEAN_LIMIT)
    {


        if (defined('WP_INSTALLING')) {
            return 0;
        }

        global $wpdb;

        $sql = " truncate table {$wpdb->base_prefix}npxyz2021_sessions ";

        //db call ok; no-cache ok
        //   WordPress.DB.DirectDatabaseQuery
        //custom table no wrappers or caching avaialable or needed
        // phpcs:disable WordPress.DB.DirectDatabaseQuery
        $sql = $wpdb->query($wpdb->prepare("truncate table {$wpdb->base_prefix}%s " ,'npxyz2021_sessions')); //db call ok
// phpcs:enable  WordPress.DB.DirectDatabaseQuery

       // $wpdb->query($sql);

    }
}
}