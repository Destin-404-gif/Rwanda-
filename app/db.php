<?php
/** PDO connection singleton (prepared statements only). */

function db(): PDO {
    static $pdo = null;
    if ($pdo instanceof PDO) return $pdo;
    $host = (string) cfg('DB_HOST', '127.0.0.1');
    $port = (string) cfg('DB_PORT', '3306');
    $name = (string) cfg('DB_NAME', '');
    $user = (string) cfg('DB_USER', 'root');
    $pass = (string) cfg('DB_PASS', '');

    $dsn = "mysql:host={$host};port={$port};dbname={$name};charset=utf8mb4";
    try {
        $pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
            PDO::ATTR_STRINGIFY_FETCHES  => false,
        ]);
    } catch (PDOException $e) {
        error_log('[DB] ' . $e->getMessage());
        http_response_code(500);
        if (cfg('APP_ENV') === 'local') {
            exit('Database connection failed: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES));
        }
        exit('Service temporarily unavailable.');
    }
    return $pdo;
}

/** Convenience: fetch one row. */
function db_one(string $sql, array $params = []): ?array {
    $st = db()->prepare($sql);
    $st->execute($params);
    $row = $st->fetch();
    return $row === false ? null : $row;
}

/** Convenience: fetch all rows. */
function db_all(string $sql, array $params = []): array {
    $st = db()->prepare($sql);
    $st->execute($params);
    return $st->fetchAll();
}

/** Convenience: scalar value. */
function db_val(string $sql, array $params = []) {
    $st = db()->prepare($sql);
    $st->execute($params);
    return $st->fetchColumn();
}

/** Convenience: INSERT returning last insert id. */
function db_insert(string $sql, array $params = []): int {
    $st = db()->prepare($sql);
    $st->execute($params);
    return (int) db()->lastInsertId();
}
