<?php
/**
 * WordPress session managment.
 *
 * Standardizes WordPress session data using database-backed options for storage.
 * for storing user session information.
 *
 * @package WordPress
 * @subpackage Session
 * @since   3.7.0
 */


/**
 * WordPress Session class for managing user session data.
 *
 * @package WordPress
 * @since   3.7.0
 */

$session_type=get_option('settings_gpls_woo_rfq_cookie_or_phpsession',"rfq_cookie");


if($session_type === "php_session"){
    return ;
}

#[\AllowDynamicProperties]
final class RFQTK_WP_Session extends RFQTK_Recursive_ArrayAccess
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
     * Will rebuild the session collection from the given session ID if it exists. Otherwise, will
     * create a new session with that ID.
     *
     * @param $session_id
     * @uses apply_filters Calls `wp_session_expiration` to determine how long until sessions expire.
     */
    protected function __construct()
    {



        // error_reporting(0);
//change list

        if (isset($_COOKIE[RFQTK_WP_SESSION_COOKIE])) {

            //  WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
            $cookie = sanitize_text_field(wp_unslash($_COOKIE[RFQTK_WP_SESSION_COOKIE]));
            $cookie_crumbs = explode('||', $cookie);


            if ($this->is_valid_md5($cookie_crumbs[0])) {

                $this->session_id = $cookie_crumbs[0];
                // echo $this->session_id;

            } else {

                $this->regenerate_id(true);
            }

            $this->expires = absint($cookie_crumbs[1]);
            $this->exp_variant = absint($cookie_crumbs[2]);

            // Update the session expiration if we're past the variant time
            if (time() > $this->exp_variant) {

                $this->set_expiration();

                $this->np_update_expiration("_rfqtk_wp_session_{$this->session_id}");

            }
        } else {

            $this->session_id = RFQTK_WP_Session_Utils::generate_id();
            $this->set_cookie();
            $this->set_expiration();
        }


        $this->read_data();
    }


    /**
     * Set both the expiration time and the expiration variant.
     * @uses apply_filters Calls `wp_session_expiration_variant` to get the max update window for session data.
     * @uses apply_filters Calls `wp_session_expiration` to get the standard expiration time for sessions.
     */
    protected function set_expiration()
    {
        $this->exp_variant = time() + (int)apply_filters('_rfqtk_wp_session_expiration_variant', RFQTK_WP_SESSION_EXPIRATION_VARIANT);
        $this->expires = time() + (int)apply_filters('_rfqtk_wp_session_expiration', RFQTK_WP_SESSION_EXPIRATION);

    }

    /**
     * Set the session cookie
     * @uses apply_filters Calls `wp_session_cookie_secure` to set the $secure parameter of setcookie()
     * @uses apply_filters Calls `wp_session_cookie_httponly` to set the $httponly parameter of setcookie()
     */
    protected function set_cookie()
    {
        if (!headers_sent()) {
            $secure = apply_filters('_rfqtk_wp_session_cookie_secure', true);
            $httponly = apply_filters('_rfqtk_wp_session_cookie_httponly', true);

            if (!defined("COOKIEPATH")) {
                define("COOKIEPATH", "");
            }
            if (!defined("COOKIE_DOMAIN")) {
                define('COOKIE_DOMAIN', "");
            }

            if(!$this->expires) {
                $this->set_expiration();
            }
            setcookie(RFQTK_WP_SESSION_COOKIE, $this->session_id . '||' . $this->expires . '||' . $this->exp_variant, $this->expires, COOKIEPATH, COOKIE_DOMAIN, $secure, $httponly);

        }
    }

    /**
     * Read data from a transient for the current session.
     *
     * Automatically resets the expiration time for the session transient to some time in the future.
     *
     * @return array
     */
    protected function read_data()
    {
        $session_type=get_option('settings_gpls_woo_rfq_cookie_or_phpsession',"rfq_cookie");
        if($session_type === "php_session"){
           return array();
        }

        $this->container = $this->np_get_session("_rfqtk_wp_session_{$this->session_id}", array());

        return $this->container;
    }

    /**
     * Write the data from the current session to the data storage system.
     */
    public function write_data()
    {

        $session_type=get_option('settings_gpls_woo_rfq_cookie_or_phpsession',"rfq_cookie");
        if($session_type === "php_session"){

            return false;
        }

        if(is_array($this->container) && count($this->container) ==0 ){

            return false;
        }

        $option_key = "_rfqtk_wp_session_{$this->session_id}";

        if ($this->dirty)
        {

            $this->np_add_session("_rfqtk_wp_session_{$this->session_id}", $this->container);

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
        $array = wp_json_decode($data);

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
        $session_type=get_option('settings_gpls_woo_rfq_cookie_or_phpsession',"rfq_cookie");
        if($session_type === "php_session"){
            return ;
        }

        if ($delete_old) {
            $this->np_delete_session("_rfqtk_wp_session_{$this->session_id}");
        }

        $this->session_id = RFQTK_WP_Session_Utils::generate_id();
        // echo $this->session_id;

        $this->set_cookie();
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

        //$serialized_value = maybe_serialize( $value );
        $serialized_value = maybe_serialize($value);
        $serialized_container = maybe_serialize($this->container);

        //$result = $wpdb->query($wpdb->prepare("INSERT INTO {$wpdb->base_prefix}npxyz2021_sessions (`option_name`, `option_value`,`expiration`,`misc_value`) VALUES (%s, %s,%s,%s)" , $option, $serialized_value, $this->expires, $serialized_container));
        //db call ok; no-cache ok
        //   WordPress.DB.DirectDatabaseQuery
        //custom table no wrappers or caching avaialable or needed
        // phpcs:disable WordPress.DB.DirectDatabaseQuery
        $result = $wpdb->query($wpdb->prepare("
        INSERT INTO {$wpdb->base_prefix}npxyz2021_sessions
         (`option_name`, `option_value`,`expiration`,`misc_value`) 
         VALUES (%s, %s,%s,%s) ON DUPLICATE KEY UPDATE `option_name` = VALUES(`option_name`),
          `option_value` = VALUES(`option_value`),
          `expiration` = VALUES(`expiration`),`misc_value` = 'rfq_session' ", $option, $serialized_value, $this->expires, $serialized_container));//db call ok; no-cache ok
// phpcs:enable  WordPress.DB.DirectDatabaseQuery


        if (!$result) {
            return false;
        }
    }

    public function np_update_expiration($option)
    {
        if(is_admin()){
           // return false;
        }

        global $wpdb;

        /*if($this->np_get_session($option)==false){
            return false;
        }*/

        //db call ok; no-cache ok
        //   WordPress.DB.DirectDatabaseQuery
        //custom table no wrappers or caching avaialable or needed
        // phpcs:disable WordPress.DB.DirectDatabaseQuery
        $result = $wpdb->query($wpdb->prepare("UPDATE {$wpdb->base_prefix}npxyz2021_sessions set `expiration`= %s,
        `updated`= now() where `option_name` = %s ",$this->expires,$option)); //db call ok; no-cache ok
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
        //db call ok; no-cache ok
        //   WordPress.DB.DirectDatabaseQuery
        //custom table no wrappers or caching avaialable or needed
        // phpcs:disable WordPress.DB.DirectDatabaseQuery
        $session_value = $wpdb->get_var($wpdb->prepare("SELECT option_value
        FROM {$wpdb->base_prefix}npxyz2021_sessions 
        WHERE option_name = %s  LIMIT %d",$option,1)); //db call ok


       // $session_value = ($wpdb->get_var("SELECT option_value FROM {$wpdb->base_prefix}npxyz2021_sessions WHERE option_name = '{$option}' LIMIT 1"));


        //custom table no wrappers or caching avaialable or needed
        //db call ok; no-cache ok
        //   WordPress.DB.DirectDatabaseQuery
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
        // phpcs:disable WordPress.DB.DirectDatabaseQuery
        $result = $wpdb->query($wpdb->prepare(
            "delete FROM {$wpdb->base_prefix}npxyz2021_sessions where `option_name` = %s ",$option));   //db call ok; no-cache ok
// phpcs:enable  WordPress.DB.DirectDatabaseQuery

     //   $wpdb->query($sql);
        // we only care that we attempted the delete.
        return true;

    }




}