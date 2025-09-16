<?php
namespace core;

class Session
{
    public static function start()
    {
        if (session_status() === PHP_SESSION_NONE) {
            // Custom session name
            session_name(defined('AUTH_SESSION_NAME') ? AUTH_SESSION_NAME : 'app_sess');

            // Determine Secure flag
            $secure = defined('AUTH_COOKIE_SECURE') ? AUTH_COOKIE_SECURE : (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
            $samesite = defined('AUTH_COOKIE_SAMESITE') ? AUTH_COOKIE_SAMESITE : 'Lax';
            $lifetime = defined('AUTH_COOKIE_LIFETIME') ? AUTH_COOKIE_LIFETIME : 0;

            // Set cookie params with SameSite support for older PHP versions
            $cookieParams = session_get_cookie_params();
            $cookieParams['httponly'] = true;
            $cookieParams['secure'] = $secure;
            $cookieParams['lifetime'] = $lifetime;
            $cookieParams['samesite'] = $samesite;

            // For PHP 7.3+ we can pass options array
            if (PHP_VERSION_ID >= 70300) {
                session_set_cookie_params([
                    'lifetime' => $cookieParams['lifetime'],
                    'path' => $cookieParams['path'],
                    'domain' => $cookieParams['domain'],
                    'secure' => $cookieParams['secure'],
                    'httponly' => $cookieParams['httponly'],
                    'samesite' => $cookieParams['samesite']
                ]);
            } else {
                // Fallback: set cookie without samesite (we'll append it when sending headers)
                session_set_cookie_params($cookieParams['lifetime'], $cookieParams['path'] . '; samesite=' . $cookieParams['samesite'], $cookieParams['domain'], $cookieParams['secure'], $cookieParams['httponly']);
            }

            session_start();

            // Prevent session fixation
            if (!isset($_SESSION['_session_created'])) {
                session_regenerate_id(true);
                $_SESSION['_session_created'] = time();
            }
        }
    }

    public static function regenerate()
    {
        if (session_status() !== PHP_SESSION_NONE) {
            session_regenerate_id(true);
        }
    }

    public static function destroy()
    {
        if (session_status() !== PHP_SESSION_NONE) {
            $_SESSION = [];
            setcookie(session_name(), '', time() - 3600, '/');
            session_destroy();
        }
    }
}