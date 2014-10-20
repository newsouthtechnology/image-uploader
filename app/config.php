<?php

/**
 * Returns the curent project's base url
 * @return string
 */
function getBaseUrl(){
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    $domainName = $_SERVER['HTTP_HOST'];

    return rtrim($protocol.$domainName, '/');
}

define('ABS_PATH', dirname(dirname(__FILE__)).'/');
define('APP_PATH', ABS_PATH.'app/');
define('UPLOADS_PATH', ABS_PATH.'uploads/');
define('BASE_URL', getBaseUrl().'/');
define('APP_URL', BASE_URL.'app/');