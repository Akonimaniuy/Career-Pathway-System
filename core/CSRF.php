<?php
namespace core;

class CSRF
{
    const FIELD = '_csrf';

    public static function token()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            Session::start();
        }
        if (empty($_SESSION['_csrf_token'])) {
            $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
            $_SESSION['_csrf_time'] = time();
        }
        return $_SESSION['_csrf_token'];
    }

    public static function inputField()
    {
        $token = self::token();
        return '<input type="hidden" name="' . self::FIELD . '" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
    }

    public static function validate($token, $maxAge = 3600)
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            Session::start();
        }
        if (empty($_SESSION['_csrf_token'])) {
            return false;
        }
        $valid = hash_equals($_SESSION['_csrf_token'], (string)$token);
        if ($valid && isset($_SESSION['_csrf_time']) && (time() - $_SESSION['_csrf_time'] <= $maxAge)) {
            return true;
        }
        return false;
    }
}