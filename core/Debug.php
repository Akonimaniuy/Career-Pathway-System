<?php
namespace core;

class Debug
{
    protected static $enabled = false;
    protected static $startTime;
    protected static $logs = [];
    protected static $queries = [];
    protected static $router = null;
    protected static $obStarted = false;

    public static function init()
    {
        self::$enabled = defined('APP_DEBUG') && APP_DEBUG === true;

        // optional IP filtering
        if (self::$enabled && defined('DEBUG_ALLOW_IPS') && DEBUG_ALLOW_IPS) {
            $allowed = array_map('trim', explode(',', DEBUG_ALLOW_IPS));
            $remote = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
            if (!in_array($remote, $allowed, true)) {
                self::$enabled = false;
            }
        }

        self::$startTime = microtime(true);

        // Ensure storage dir exists for logs
        $storage = APP_PATH . '/storage';
        if (!is_dir($storage)) {
            @mkdir($storage, 0755, true);
        }

        if (self::$enabled) {
            set_error_handler([self::class, 'errorHandler']);
            set_exception_handler([self::class, 'exceptionHandler']);
            register_shutdown_function([self::class, 'shutdown']);
        }
    }

    public static function startOutputBuffer()
    {
        if (!self::$enabled) {
            return;
        }
        if (!self::$obStarted) {
            ob_start();
            self::$obStarted = true;
        }
    }

    public static function shutdown()
    {
        if (!self::$enabled) {
            return;
        }

        if (self::$obStarted && ob_get_level() > 0) {
            $content = ob_get_clean();
            if (defined('DEBUG_SHOW_TOOLBAR') && DEBUG_SHOW_TOOLBAR && self::isHtmlResponse()) {
                $content = self::appendToolbar($content);
            }
            echo $content;
        }

        if (defined('DEBUG_LOG_PATH') && DEBUG_LOG_PATH) {
            $entry = sprintf(
                "[%s] PID:%s time:%.4f logs:%d queries:%d\n",
                date('Y-m-d H:i:s'),
                getmypid(),
                microtime(true) - self::$startTime,
                count(self::$logs),
                count(self::$queries)
            );
            $payload = $entry . self::formatLogsForFile() . "\n";
            @file_put_contents(DEBUG_LOG_PATH, $payload, FILE_APPEND | LOCK_EX);
        }
    }

    public static function setRouter($router)
    {
        self::$router = $router;
    }

    public static function log($message, $context = [])
    {
        if (!self::$enabled) {
            return;
        }
        $time = microtime(true);
        self::$logs[] = ['t' => $time, 'msg' => (string)$message, 'ctx' => $context];
        self::trimMemory();
    }

    public static function dump($var)
    {
        if (!self::$enabled) {
            return;
        }
        echo '<pre style="background:#f6f8fa;border:1px solid #ddd;padding:8px;margin:8px 0;font-size:13px;">';
        ob_start();
        var_dump($var);
        echo htmlspecialchars(ob_get_clean(), ENT_QUOTES, 'UTF-8');
        echo '</pre>';
    }

    public static function dd($var)
    {
        self::dump($var);
        exit;
    }

    public static function recordQuery(string $sql, array $params = [])
    {
        if (!self::$enabled) {
            return;
        }
        self::$queries[] = ['t' => microtime(true), 'sql' => $sql, 'params' => $params];
        self::trimMemory();
    }

    public static function getLogs() : array { return self::$logs; }
    public static function getQueries() : array { return self::$queries; }
    public static function getRoutes() : array {
        if (self::$router && method_exists(self::$router, 'getRoutes')) {
            return self::$router->getRoutes();
        }
        return [];
    }

    public static function errorHandler($errno, $errstr, $errfile, $errline)
    {
        if (!(error_reporting() & $errno)) {
            return false;
        }
        $message = sprintf("PHP Error: [%d] %s in %s on line %d", $errno, $errstr, $errfile, $errline);
        self::log($message);
        throw new \ErrorException($errstr, 0, $errno, $errfile, $errline);
    }

    public static function exceptionHandler($ex)
    {
        if (headers_sent() === false) {
            http_response_code(500);
        }
        echo '<!doctype html><html><head><meta charset="utf-8"><title>Exception</title>';
        echo '<style>body{font-family:Segoe UI,Arial;background:#fff;color:#222;padding:20px} .trace{background:#f6f8fa;border:1px solid #ddd;padding:12px;margin-top:12px;font-size:13px}</style>';
        echo '</head><body>';
        echo '<h1>Unhandled Exception</h1>';
        echo '<p><strong>' . htmlspecialchars(get_class($ex) . ': ' . $ex->getMessage(), ENT_QUOTES, 'UTF-8') . '</strong></p>';
        echo '<p>In ' . htmlspecialchars($ex->getFile() . ':' . $ex->getLine(), ENT_QUOTES, 'UTF-8') . '</p>';
        echo '<div class="trace"><pre>' . htmlspecialchars($ex->getTraceAsString(), ENT_QUOTES, 'UTF-8') . '</pre></div>';
        if (defined('DEBUG_SHOW_TOOLBAR') && DEBUG_SHOW_TOOLBAR) {
            echo '<hr>';
            echo self::renderToolbarHtml();
        }
        echo '</body></html>';
        self::log('Uncaught exception: ' . $ex->getMessage(), ['exception' => $ex]);
    }

    protected static function isHtmlResponse()
    {
        $headers = headers_list();
        foreach ($headers as $h) {
            if (stripos($h, 'Content-Type:') === 0 && stripos($h, 'html') === false) {
                return false;
            }
        }
        return true;
    }

    protected static function appendToolbar($content)
    {
        $toolbar = self::renderToolbarHtml();
        if (stripos($content, '</body>') !== false) {
            $content = str_ireplace('</body>', $toolbar . '</body>', $content);
        } else {
            $content .= $toolbar;
        }
        return $content;
    }

    protected static function renderToolbarHtml()
    {
        $logs = array_slice(array_reverse(self::$logs), 0, 50);
        $queries = array_slice(array_reverse(self::$queries), 0, 50);
        $routes = self::getRoutes();

        ob_start();
        ?>
        <style>
        #dbg-toolbar {position:fixed;left:10px;bottom:10px;z-index:99999;font-family:Segoe UI,Arial;background:rgba(0,0,0,0.75);color:#fff;border-radius:6px;padding:8px;min-width:420px;max-width:900px;box-shadow:0 8px 20px rgba(0,0,0,0.5)}
        #dbg-toolbar .dbg-title{font-weight:600;margin-bottom:6px}
        #dbg-toolbar .dbg-tabs{display:flex;gap:8px;margin-bottom:8px}
        #dbg-toolbar button{background:transparent;border:1px solid rgba(255,255,255,0.12);color:#fff;padding:4px 8px;border-radius:4px;cursor:pointer}
        #dbg-toolbar .dbg-content{background:#fff;color:#111;padding:8px;border-radius:4px;max-height:320px;overflow:auto}
        #dbg-toolbar pre{white-space:pre-wrap;word-break:break-word;font-size:12px}
        #dbg-toolbar .dbg-small{font-size:12px;color:rgba(255,255,255,0.85)}
        </style>
        <div id="dbg-toolbar" aria-hidden="false">
          <div class="dbg-title">Debug Toolbar <span class="dbg-small">[time: <?php echo round(microtime(true)-self::$startTime,4) ?>s]</span></div>
          <div class="dbg-tabs">
            <button data-tab="req">Request</button>
            <button data-tab="sess">Session</button>
            <button data-tab="logs">Logs (<?php echo count(self::$logs) ?>)</button>
            <button data-tab="qry">Queries (<?php echo count(self::$queries) ?>)</button>
            <button data-tab="routes">Routes (<?php echo count($routes) ?>)</button>
            <button data-tab="close" style="margin-left:auto">Close</button>
          </div>
          <div id="dbg-req" class="dbg-content" style="display:none">
            <pre><?php echo htmlspecialchars(self::formatRequest(), ENT_QUOTES, 'UTF-8'); ?></pre>
          </div>
          <div id="dbg-sess" class="dbg-content" style="display:none">
            <pre><?php echo htmlspecialchars(self::formatSession(), ENT_QUOTES, 'UTF-8'); ?></pre>
          </div>
          <div id="dbg-logs" class="dbg-content" style="display:none">
            <pre><?php foreach ($logs as $l) { echo date('H:i:s', $l['t']) . ' ' . htmlspecialchars($l['msg'], ENT_QUOTES, 'UTF-8') . "\n"; if (!empty($l['ctx'])) { echo "  " . htmlspecialchars(print_r($l['ctx'], true), ENT_QUOTES, 'UTF-8') . "\n"; } } ?></pre>
          </div>
          <div id="dbg-qry" class="dbg-content" style="display:none">
            <pre><?php foreach ($queries as $q) { echo date('H:i:s', $q['t']) . ' SQL: ' . htmlspecialchars($q['sql'], ENT_QUOTES, 'UTF-8') . "\n"; if (!empty($q['params'])) { echo "  Params: " . htmlspecialchars(print_r($q['params'], true), ENT_QUOTES, 'UTF-8') . "\n"; } echo "\n"; } ?></pre>
          </div>
          <div id="dbg-routes" class="dbg-content" style="display:none">
            <pre><?php foreach ($routes as $pattern => $params) { echo $pattern . ' => ' . htmlspecialchars(print_r($params, true), ENT_QUOTES, 'UTF-8') . "\n"; } ?></pre>
          </div>
        </div>
        <script>
        (function(){ var toolbar = document.getElementById('dbg-toolbar');
          if(!toolbar) return;
          function hideAll(){ ['req','sess','logs','qry','routes'].forEach(function(n){ var el=document.getElementById('dbg-'+n); if(el) el.style.display='none'; }) }
          toolbar.addEventListener('click', function(e){
            var t = e.target;
            if (t.tagName !== 'BUTTON') return;
            var tab = t.getAttribute('data-tab');
            if (tab === 'close') { toolbar.style.display='none'; return; }
            hideAll();
            var el = document.getElementById('dbg-'+tab);
            if (el) el.style.display = 'block';
          }, false);
        })();
        </script>
        <?php
        return ob_get_clean();
    }

    protected static function formatRequest()
    {
        $out = [];
        $out[] = 'URI: ' . ($_SERVER['REQUEST_URI'] ?? '');
        $out[] = 'Method: ' . ($_SERVER['REQUEST_METHOD'] ?? 'GET');
        $out[] = 'Query: ' . (isset($_GET) ? http_build_query($_GET) : '');
        $out[] = 'Post keys: ' . implode(', ', array_keys($_POST ?? []));
        $out[] = 'Cookies: ' . json_encode($_COOKIE ?? [], JSON_PRETTY_PRINT);
        $out[] = 'Server: ' . json_encode(array_intersect_key($_SERVER, array_flip(['HTTP_HOST','SERVER_NAME','REMOTE_ADDR','REQUEST_URI','REQUEST_METHOD','HTTP_USER_AGENT','SERVER_SOFTWARE'])), JSON_PRETTY_PRINT);
        return implode("\n", $out);
    }

    protected static function formatSession()
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return print_r($_SESSION, true);
        }
        return 'No active session';
    }

    protected static function formatLogsForFile()
    {
        $s = '';
        foreach (self::$logs as $l) {
            $s .= '[' . date('Y-m-d H:i:s', $l['t']) . '] ' . $l['msg'] . ' ' . trim(print_r($l['ctx'], true)) . "\n";
        }
        foreach (self::$queries as $q) {
            $s .= '[' . date('Y-m-d H:i:s', $q['t']) . '] SQL: ' . $q['sql'] . ' PARAMS: ' . json_encode($q['params']) . "\n";
        }
        return $s;
    }

    protected static function trimMemory()
    {
        $max = defined('DEBUG_MAX_IN_MEMORY') ? DEBUG_MAX_IN_MEMORY : 200;
        if (count(self::$logs) > $max) {
            self::$logs = array_slice(self::$logs, -$max);
        }
        if (count(self::$queries) > $max) {
            self::$queries = array_slice(self::$queries, -$max);
        }
    }
}