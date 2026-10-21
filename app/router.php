<?php
/**
 * Tiny front controller / router.
 * Pattern placeholders: /destinations/{slug}
 */

class Router {
    private array $routes = [];

    public function add(string $method, string $pattern, callable|string $handler): void {
        $regex = preg_replace('#\{([a-zA-Z_]+)\}#', '(?P<$1>[^/]+)', $pattern);
        $this->routes[] = [strtoupper($method), '#^' . $regex . '$#', $handler, $pattern];
    }

    public function get(string $p, callable|string $h): void { $this->add('GET', $p, $h); }
    public function post(string $p, callable|string $h): void { $this->add('POST', $p, $h); }

    public function dispatch(string $path, string $method): void {
        $method = strtoupper($method);
        $allowed = [];
        foreach ($this->routes as [$m, $regex, $handler, $pattern]) {
            if ($m !== $method) continue;
            if (preg_match($regex, $path, $matches)) {
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                $params = array_map('urldecode', $params);
                call_user_func_array($handler, $params);
                return;
            }
            $allowed[] = $m;
        }
        if ($allowed) {
            http_response_code(405);
            header('Allow: ' . implode(', ', $allowed));
            if (str_starts_with($path, '/api/')) json_response(['ok' => false, 'error' => 'Method not allowed'], 405);
            page_404();
            return;
        }
        page_404();
    }
}

/** Normalise the current request path (front controller aware). */
function request_path(): string {
    $uri = $_SERVER['REQUEST_URI'] ?? '/';
    $path = parse_url($uri, PHP_URL_PATH) ?: '/';
    $base = base_path();
    if ($base !== '' && str_starts_with($path, $base)) {
        $path = substr($path, strlen($base));
    }
    $path = '/' . ltrim($path, '/');
    // strip index.php
    if (str_starts_with($path, '/index.php')) {
        $path = substr($path, strlen('/index.php')) ?: '/';
    }
    $path = rtrim($path, '/') ?: '/';
    return $path;
}
