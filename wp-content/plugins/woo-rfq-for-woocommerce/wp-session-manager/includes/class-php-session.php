<?php
/*
 * Copyright (c) 2022. Neah Plugins All rights reserved.
 * Email: contact@neahplugins.com.
 */

/**
 * PHP session managment.
 *
 */


/**
 * WordPress Session class for managing user session data.
 *
 * @package WordPress
 * @since   3.7.0
 */

$session_type=get_option('settings_gpls_woo_rfq_cookie_or_phpsession',"rfq_cookie");


if($session_type !== "php_session"){
    return ;
}
#[\AllowDynamicProperties]
class RFQTK_PHP_Session extends RFQTK_Recursive_ArrayAccess

{
    /**
     * ID of the current session.
     *
     * @var string
     */
    public $session_id;

    /**
     * Unix timestamp when session expires.
     *
     * @var int
     */
    protected $expires;

    /**
     * Unix timestamp indicating when the expiration time needs to be reset.
     *
     * @var int
     */
    protected $exp_variant;

    /**
     * Singleton instance.
     *
     * @var bool|WP_Session
     */
    private static $instance = false;




    /**
     * Retrieve the current session instance.
     *
     * @param bool $session_id Session ID from which to populate data.
     *
     * @return bool|WP_Session
     */
    public static function get_instance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Default constructor.

     */
    protected function __construct()
    {


        $this->set_expiration();

        if (session_id() !=null) {

            $this->session_id = session_id();

            if(isset($_SESSION['expires'])) {

                $this->expires = sanitize_key( wp_unslash($_SESSION['expires']));
                $this->exp_variant = sanitize_url( wp_unslash($_SESSION['expires']));
            }
            // Update the session expiration if we're past the variant time
            if (time() > $this->exp_variant) {
                $this->np_update_expiration($this->session_id);
            }


        } else {


            if(!headers_sent()) {

             //   ini_set('session.use_cookies', 'true');
             //   ini_set('session.gc_maxlifetime', $this->expires);

                session_start();
                  
            }

            register_shutdown_function('session_write_close');
            $this->session_id=session_id();
        }


        $this->read_data($this->session_id);
    }


    /**
     * Set both the expiration time and the expiration variant.
     */
    protected function set_expiration()
    {
        $this->exp_variant = time() + (int)apply_filters('_rfqtk_wp_session_expiration_variant', RFQTK_WP_SESSION_EXPIRATION_VARIANT);

        $this->expires = time() + (int)apply_filters('_rfqtk_wp_session_expiration', RFQTK_WP_SESSION_EXPIRATION);
    }



    /**
     * Read data from a transient for the current session.
     *
     * Automatically resets the expiration time for the session transient to some time in the future.
     *
     * @return array
     */
    protected function read_data($id)
    {
        $this->container = $this->np_get_session($id, array());

        return $this->container;
    }

    /**
     * Write the data from the current session to the data storage system.
     */
    public function write_data()
    {

        if(is_array($this->container) && count($this->container) ==0 ){
            return true;
        }

        $option_key = $this->session_id;

        if ($this->dirty) {

            $this->np_add_session($this->session_id, $this->container);

        }
    }

    /**
     * Output the current container contents as a JSON-encoded string.
     *
     * @return string
     */
    public function json_out()
    {
        return wp_json_encode($this->container);
    }

    /**
     * Decodes a JSON string and, if the object is an array, overwrites the session container with its contents.
     *
     * @param string $data
     *
     * @return bool
     */
    public function json_in($data)
    {
        $array = json_decode($data);

        if (is_array($array)) {
            $this->container = $array;
            return true;
        }

        return false;
    }

    /**
     * Regenerate the current session's ID.
     *
     * @param bool $delete_old Flag whether or not to delete the old session data from the server.
     */
    public function regenerate_id($delete_old = false)
    {
        if ($delete_old) {
            $this->np_delete_session($this->session_id);
        }

        $this->session_id =  session_regenerate_id();

    }

    /**
     * Check if a session has been initialized.
     *
     * @return bool
     */
    public function session_started()
    {
        return self::$instance;
    }

    protected function is_valid_md5($md5 = '')
    {
        return preg_match('/^[a-f0-9]{32}$/', $md5);
    }

    /**
     * Return the read-only cache expiration value.
     *
     * @return int
     */
    public function cache_expiration()
    {
        return $this->expires;
    }

    /**
     * Flushes all session variables.
     */
    public function reset()
    {
        $this->container = array();
    }


    /**
     * Baosed on :
     * WordPress Option API
     * @package WordPress
     * @subpackage Option
     */

    public function np_add_session($option, $value)
    {

        if(is_admin()){
            // return false;
        }

        global $wpdb;

        $serialized_value = maybe_serialize($value);

        //custom table no wrappers or caching avaialable or needed

        // phpcs:disable WordPress.DB.DirectDatabaseQuery
        $result = $wpdb->query($wpdb->prepare(// phpcs:ignore WordPress.DB.DirectDatabaseQuery
            "INSERT INTO 
        {$wpdb->base_prefix}npxyz2021_sessions (`option_name`, `option_value`,`expiration`,`misc_value`) VALUES (%s, %s,%s,%s)
         ON DUPLICATE KEY UPDATE `option_name` = 
        VALUES(`option_name`), `option_value` = VALUES(`option_value`),`expiration` = VALUES(`expiration`),
        `misc_value`=VALUES(`misc_value`) ", $option, $serialized_value, $this->expires, 'phpsid')); //db call ok; no-cache ok
        // phpcs:enable  WordPress.DB.DirectDatabaseQuery

        if (!$result) {
            return false;
        }
    }

    public function np_update_expiration($option)
    {

        if(is_admin()){
            //return false;
        }

        global $wpdb;

        $_SESSION['timeout_idle'] = time() + $this->expires;

        //custom table no wrappers or caching avaialable or needed

        // phpcs:disable WordPress.DB.DirectDatabaseQuery
        $result = $wpdb->query($wpdb->prepare("UPDATE {$wpdb->base_prefix}npxyz2021_sessions set `expiration`= %s,`updated`= now() where
         `option_name` = %s ",$this->expires,$option)); //db call ok
// phpcs:enable  WordPress.DB.DirectDatabaseQuery
        if (!$result) {
            return false;
        }
    }


    public function np_get_session($option, $default = false)
    {

        global $wpdb;

        if (empty($option)) {
            return false;
        }

        //custom table no wrappers or caching avaialable or needed

        //   WordPress.DB.DirectDatabaseQuery
        // phpcs:disable WordPress.DB.DirectDatabaseQuery
        $session_value = $wpdb->get_var($wpdb->prepare("SELECT option_value
        FROM {$wpdb->base_prefix}npxyz2021_sessions 
        WHERE option_name = %s  LIMIT %d",$option,1)); //db call ok
        // phpcs:enable  WordPress.DB.DirectDatabaseQuery

        if (!empty($session_value)) {
            $value = $session_value;

        } else {
            return $default;
        }

        return maybe_unserialize($value);

    }

    public function np_delete_session($option)
    {
        global $wpdb;

       // $sql = " delete FROM {$wpdb->base_prefix}npxyz2021_sessions WHERE  option_name= '{$option}' ";

        //custom table no wrappers or caching avaialable or needed
        //   WordPress.DB.DirectDatabaseQuery
        // phpcs:disable WordPress.DB.DirectDatabaseQuery
        $result = $wpdb->query($wpdb->prepare("delete FROM {$wpdb->base_prefix}npxyz2021_sessions where `option_name` = %s ",$option)); //db call ok
// phpcs:enable  WordPress.DB.DirectDatabaseQuery



        return $result;

    }


    /**
     * @return bool
     */
    public function close()
    {

        return true;
        // global $wpdb;
        // $wpdb->close();

    }

    /**
     * @param $id
     * @return bool
     */
    public function destroy($id)
    {

        $this->np_delete_session($id);
        return true;
    }

    /**
     * @param $max_lifetime
     * @return int|false
     */
    public function gc($max_lifetime)
    {
        $limit = RFQTK_WP_SESSION_CLEAN_LIMIT;

        global $wpdb;

        $limit = absint( $limit );

        $limit = apply_filters('delete_old_sessions_filter',$limit);

        {
           // $sql = " delete FROM {$wpdb->base_prefix}npxyz2021_sessions
         // WHERE misc_value='phpsid' and option_value = 'a:0:{}' or  expiration <= ". time() ." LIMIT " . $limit . " ";

            //custom table no wrappers or caching avaialable or needed
            // phpcs:disable WordPress.DB.DirectDatabaseQuery
            $result = $wpdb->query($wpdb->prepare("delete FROM {$wpdb->base_prefix}npxyz2021_sessions
          WHERE  misc_value = %s and  option_value = %s or  expiration <= %s LIMIT %d  ",'phpsid','a:0:{}',time(),$limit)); //db call ok
// phpcs:enable  WordPress.DB.DirectDatabaseQuery
            return $result;
        }

    }

    /**
     * @param $path
     * @param $name
     * @return bool
     */
    public function open($path, $name)
    {
        return true;
        // global $wpdb;
        //  $wpdb->db_connect();
    }

    /**
     * @param $id
     * @return string|false
     */
    public function read($id)
    {
        // return  $this->read_data($id);
        return maybe_serialize($this->read_data($id));
    }

    /**
     * @param $id
     * @param $data
     * @return bool
     */
    public function write($id, $data)
    {

        $this->json_in($data);
        $this->write_data();

        return true;
    }
}