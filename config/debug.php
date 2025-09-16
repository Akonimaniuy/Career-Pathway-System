<?php
// Debug configuration - development only
defined('APP_DEBUG') || define('APP_DEBUG', false);
defined('DEBUG_SHOW_TOOLBAR') || define('DEBUG_SHOW_TOOLBAR', false);
defined('DEBUG_LOG_PATH') || define('DEBUG_LOG_PATH', APP_PATH . '/storage/debug.log');
defined('DEBUG_MAX_IN_MEMORY') || define('DEBUG_MAX_IN_MEMORY', 200);

// Optional: show toolbar only on local IPs to avoid exposing on public dev servers
// define('DEBUG_ALLOW_IPS', '127.0.0.1,::1');