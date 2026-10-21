<?php
/**
 * Shared helper functions.
 */

function cfg(string $key, $default = null) {
    global $CONFIG;
    return $CONFIG[$key] ?? $default;
}

/** Base path of the app ('' at domain root, '/Rwanda' in local sub-folder). */
function base_path(): string {
    static $base = null;
    if ($base !== null) return $base;
    $configured = cfg('BASE_PATH');
    if ($configured !== null && $configured !== '') {
        return $base = rtrim($configured, '/');
    }
    $siteUrl = (string) cfg('SITE_URL', '');
    if ($siteUrl !== '') {
        $path = parse_url($siteUrl, PHP_URL_PATH) ?: '';
        return $base = rtrim($path, '/');
    }
    $script = str_replace('\\', '/', (string) ($_SERVER['SCRIPT_NAME'] ?? ''));
    $dir = dirname($script);
    if ($dir === '/' || $dir === '.' || $dir === '\\') $dir = '';
    return $base = rtrim($dir, '/');
}

/** Build an app URL: url('/tours') => http://localhost/Rwanda/tours */
function url(string $path = ''): string {
    $site = rtrim((string) cfg('SITE_URL', ''), '/');
    if ($site !== '' && preg_match('#^https?://#i', $site)) {
        return $site . base_path() . '/' . ltrim($path, '/');
    }
    $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    return ($https ? 'https://' : 'http://') . $host . base_path() . '/' . ltrim($path, '/');
}

/** Absolute URL for a local asset: asset('assets/img/x.jpg') */
function asset(string $path): string {
    $path = ltrim($path, '/');
    if (preg_match('#^(https?:)?//#i', $path) || str_starts_with($path, 'data:')) return $path;
    $site = rtrim((string) cfg('SITE_URL', ''), '/');
    if ($site !== '' && preg_match('#^https?://#i', $site)) return $site . base_path() . '/' . $path;
    $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    return ($https ? 'https://' : 'http://') . $host . base_path() . '/' . $path;
}

function e($value): string {
    return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES, 'UTF-8');
}

function is_post(): bool {
    return ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';
}

function post(string $key, $default = '') {
    return isset($_POST[$key]) ? (is_string($_POST[$key]) ? trim($_POST[$key]) : $_POST[$key]) : $default;
}

function input(string $key, $default = '') {
    $v = $_GET[$key] ?? $default;
    return is_string($v) ? trim($v) : $v;
}

function redirect(string $path): never {
    header('Location: ' . (preg_match('#^https?://#i', $path) ? $path : url($path)));
    exit;
}

function json_response($data, int $status = 200): never {
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    exit;
}

// ---- Flash messages ----------------------------------------------------
function flash_set(string $type, string $message): void {
    $_SESSION['flash'][] = ['type' => $type, 'message' => $message];
}

function flash_pull(): array {
    $all = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $all;
}

// ---- CSRF --------------------------------------------------------------
function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field(): string {
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

function csrf_header_check(): void {
    $sent = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? ($_POST['csrf_token'] ?? '');
    if (!$sent || !hash_equals(csrf_token(), (string) $sent)) {
        json_response(['ok' => false, 'error' => 'Invalid or missing CSRF token. Refresh and try again.'], 419);
    }
}

function csrf_verify(): void {
    $sent = (string) ($_POST['csrf_token'] ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? ''));
    if (!$sent || !hash_equals(csrf_token(), $sent)) {
        http_response_code(419);
        exit('Your session expired. Please go back, refresh the page and try again.');
    }
}

// ---- Formatting --------------------------------------------------------
function money($amount, string $currency = 'RWF'): string {
    $amount = (float) $amount;
    $cur = strtoupper($currency);
    $num = number_format($amount, $cur === 'RWF' ? 0 : 2);
    return $cur === 'USD' ? '$' . $num . ' USD' : $num . ' RWF';
}

function slugify(string $text): string {
    $text = strtolower(trim($text));
    $text = preg_replace('/[^a-z0-9]+/', '-', $text) ?? '';
    return trim($text, '-') ?: 'item';
}

function excerpt(string $text, int $len = 140): string {
    $text = trim(preg_replace('/\s+/', ' ', strip_tags($text)) ?? '');
    if (mb_strlen($text) <= $len) return $text;
    return rtrim(mb_substr($text, 0, $len)) . '…';
}

function time_ago(string $datetime): string {
    $diff = time() - strtotime($datetime);
    if ($diff < 60) return 'just now';
    if ($diff < 3600) return floor($diff / 60) . 'm ago';
    if ($diff < 86400) return floor($diff / 3600) . 'h ago';
    if ($diff < 604800) return floor($diff / 86400) . 'd ago';
    return date('d M Y', strtotime($datetime));
}

function initials(string $name): string {
    $parts = preg_split('/\s+/', trim($name)) ?: [];
    $i = strtoupper(mb_substr($parts[0] ?? '?', 0, 1));
    if (count($parts) > 1) $i .= strtoupper(mb_substr(end($parts), 0, 1));
    return $i;
}

function client_ip(): string {
    $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    return substr($ip, 0, 45);
}

// ---- Settings ----------------------------------------------------------
function settings(): array {
    static $cache = null;
    if ($cache !== null) return $cache;
    try {
        $rows = db()->query('SELECT key_name, value FROM settings')->fetchAll(PDO::FETCH_KEY_PAIR);
    } catch (Throwable $e) {
        $rows = [];
    }
    return $cache = $rows;
}

function setting(string $key, string $default = ''): string {
    return settings()[$key] ?? $default;
}

// ---- View rendering ----------------------------------------------------
function render(string $template, array $vars = [], string $layout = 'site'): void {
    extract($vars, EXTR_SKIP);
    ob_start();
    include VIEWS . '/' . $template . '.php';
    $content = ob_get_clean();
    include VIEWS . '/layouts/' . $layout . '.php';
}

function partial(string $template, array $vars = []): void {
    extract($vars, EXTR_SKIP);
    include VIEWS . '/partials/' . $template . '.php';
}

/** JSON columns returned as strings -> decode to arrays. */
function jarr($value): array {
    if (is_array($value)) return $value;
    if (!$value) return [];
    $decoded = json_decode((string) $value, true);
    return is_array($decoded) ? $decoded : [];
}

/** Turn a DB image path/URL into a usable src. */
function img_url(?string $path): string {
    if (!$path) return asset('assets/img/hero-kivu.jpg');
    if (preg_match('#^(https?:)?//#i', $path) || str_starts_with($path, 'data:')) return $path;
    return asset($path);
}

function nav_active(string $section, string $current): string {
    return $section === $current ? ' is-active' : '';
}

/** Is this item in the logged-in user's wishlist? */
function is_favorited(string $type, int $id): bool {
    $user = auth_user();
    if (!$user || $id <= 0) return false;
    try {
        return (bool) db_val(
            'SELECT COUNT(*) FROM wishlists WHERE user_id = ? AND target_type = ? AND target_id = ?',
            [(int) $user['id'], $type, $id]
        );
    } catch (Throwable $e) {
        return false;
    }
}
