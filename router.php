<?php
/**
 * Router for PHP's built-in server (development only):
 *   php -S localhost:8000 router.php
 * Serves existing static files, sends everything else to the front controller.
 */

$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$file = __DIR__ . str_replace('/', DIRECTORY_SEPARATOR, $path);

if ($path !== '/' && is_file($file)) {
    return false;
}

require __DIR__ . '/index.php';
