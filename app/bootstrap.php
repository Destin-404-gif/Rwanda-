<?php
/**
 * Rwanda Explorer - application bootstrap.
 * Loads config, error handling, session, core libraries.
 */
declare(strict_types=1);

define('APP_ROOT', dirname(__DIR__));
define('VIEWS', APP_ROOT . '/views');

$configFile = APP_ROOT . '/config/config.php';
if (!is_file($configFile)) {
    http_response_code(500);
    exit('Missing configuration: copy config/config.example.php to config/config.php and fill in the values.');
}
$CONFIG = require $configFile;

$env = $CONFIG['APP_ENV'] ?? 'production';
$isLocal = $env === 'local';

date_default_timezone_set('Africa/Kigali');
mb_internal_encoding('UTF-8');

ini_set('display_errors', $isLocal ? '1' : '0');
ini_set('display_startup_errors', $isLocal ? '1' : '0');
ini_set('log_errors', '1');
@mkdir(APP_ROOT . '/logs', 0775, true);
ini_set('error_log', APP_ROOT . '/logs/error.log');

set_error_handler(function (int $no, string $str, string $file = '', int $line = 0) {
    if (!(error_reporting() & $no)) return false;
    error_log("[PHP]$str in $file on line $line");
    if (($CONFIG['APP_ENV'] ?? 'production') === 'local') {
        echo '<pre style="color:#ff6b6b;background:#111;padding:12px;font-size:13px">'
            . htmlspecialchars("$str\n$file:$line", ENT_QUOTES) . '</pre>';
    }
    return true;
});

register_shutdown_function(function () {
    $err = error_get_last();
    if ($err && in_array($err['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR], true)) {
        error_log('[FATAL] ' . $err['message'] . ' in ' . $err['file'] . ' on line ' . $err['line']);
        if (headers_sent() === false) {
            http_response_code(500);
            echo 'A server error occurred. Please try again later.';
        }
    }
});

// ---- Session (secure cookies, regenerated on login) -------------------
$secure = !empty($CONFIG['SESSION_SECURE']) && (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
session_name($CONFIG['SESSION_NAME'] ?? 'rwanda_explorer_session');
session_set_cookie_params([
    'lifetime' => 0,
    'path'     => '/',
    'domain'   => '',
    'secure'   => $secure,
    'httponly' => true,
    'samesite' => 'Lax',
]);
ini_set('session.use_strict_mode', '1');
session_start();

// ---- Core libraries ---------------------------------------------------
require APP_ROOT . '/app/icons.php';
require APP_ROOT . '/app/helpers.php';
require APP_ROOT . '/app/db.php';
require APP_ROOT . '/app/auth.php';
