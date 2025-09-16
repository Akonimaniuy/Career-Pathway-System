<?php
// Authentication configuration

// Password hashing algorithm: will use Argon2id if available, otherwise PASSWORD_DEFAULT
if (defined('PASSWORD_ARGON2ID')) {
    define('AUTH_PWD_ALGO', PASSWORD_ARGON2ID);
    define('AUTH_PWD_OPTIONS', ['memory_cost' => 1<<17, 'time_cost' => 4, 'threads' => 2]); // tune per host
} else {
    define('AUTH_PWD_ALGO', PASSWORD_DEFAULT);
    define('AUTH_PWD_OPTIONS', ['cost' => 12]);
}

// Session / cookie settings
define('AUTH_SESSION_NAME', 'cps_sess');
define('AUTH_COOKIE_SECURE', isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
define('AUTH_COOKIE_SAMESITE', 'Strict'); // Strict prevents third-party sending
define('AUTH_COOKIE_LIFETIME', 0); // 0 = session cookie

// Remember-me settings
define('AUTH_REMEMBER_COOKIE', 'cps_remember');
define('AUTH_REMEMBER_EXPIRE', 60 * 60 * 24 * 30); // 30 days
define('AUTH_REMEMBER_TOKEN_BYTES', 32);

// Login throttle / lockout
define('AUTH_MAX_ATTEMPTS', 5);
define('AUTH_LOCKOUT_MINUTES', 15);

// Misc
define('AUTH_USER_TABLE', 'users');
define('AUTH_TOKENS_TABLE', 'auth_tokens'); // stores hashed remember tokens