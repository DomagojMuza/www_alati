<?php

add_filter('rest_endpoints', function($endpoints) {
    unset($endpoints['/batch/v1']);
    return $endpoints;
});

define('TELEGRAM_TOKEN', '8884057077:AAHDtOYC51dTnDPAExUdBqjpxTzr6iOP9Uw');
define('TELEGRAM_CHAT_ID', '-5510511700');


function send_telegram($message) {
    $token = TELEGRAM_TOKEN;
    $chat_id = TELEGRAM_CHAT_ID;
    $url = "https://api.telegram.org/bot{$token}/sendMessage";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
        'chat_id' => $chat_id,
        'text' => $message,
        'parse_mode' => 'HTML'
    ]));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    curl_exec($ch);
    curl_close($ch);
}
 
add_action('wp_login', 'log_user_login', 10, 2);
function log_user_login($user_login, $user) {
    $ip = $_SERVER['REMOTE_ADDR'];
    $time = date('Y-m-d H:i:s');
    $roles = implode(',', $user->roles);
    $domain = $_SERVER['HTTP_HOST'];
    $log = "[$time] LOGIN user=$user_login role=$roles ip=$ip domain=$domain ";
    file_put_contents('/var/log/wp-logins.log', $log, FILE_APPEND);
    send_telegram("Login Domain: $domain User: $user_login Role: $roles IP: $ip Time: $time");
}
 
add_action('wp_login_failed', 'log_user_login_failed');
function log_user_login_failed($user_login) {
    $ip = $_SERVER['REMOTE_ADDR'];
    $time = date('Y-m-d H:i:s');
    $domain = $_SERVER['HTTP_HOST'];
    $log = "[$time] FAILED user=$user_login ip=$ip domain=$domain ";
    file_put_contents('/var/log/wp-logins.log', $log, FILE_APPEND);
    send_telegram("FAILED Login Domain: $domain User: $user_login IP: $ip Time: $time");
}
