<?php
/**
 * JSON APIs: public search + customer helpers + admin dashboard endpoints.
 * Every admin API runs through require_admin(true) -> 401/403 JSON.
 */

function api_is_fetch(): bool {
    return ($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'fetch'
        || ($_SERVER['HTTP_ACCEPT'] ?? '') === 'application/json'
        || str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json');
}

// ---------------------------------------------------------------- public
function api_search(): void {
    $q = (string) input('q');
    if (mb_strlen($q) < 2) json_response(['ok' => true, 'results' => [], 'total' => 0]);
    $like = '%' . $q . '%';
    $results = [];
    try {
        $results['destinations'] = db_all(
            "SELECT slug, name, region AS meta, hero_image AS image FROM destinations
             WHERE status='published' AND (name LIKE ? OR region LIKE ?) LIMIT 6", [$like, $like]);
        $results['tours'] = db_all(
            "SELECT slug, title AS name, category AS meta, cover_image AS image FROM tours
             WHERE status='published' AND (title LIKE ? OR category LIKE ?) LIMIT 6", [$like, $like]);
        $results['hotels'] = db_all(
            "SELECT slug, name, city AS meta, cover_image AS image FROM hotels
             WHERE status='approved' AND (name LIKE ? OR city LIKE ?) LIMIT 6", [$like, $like]);
    } catch (Throwable $e) {
        error_log('[api-search] ' . $e->getMessage());
    }
    $total = array_sum(array_map('count', $results));
    json_response(['ok' => true, 'results' => $results, 'total' => $total, 'q' => $q]);
}

// ------------------------------------------------------------- customer
function api_favorites_toggle(): void {
    csrf_header_check();
    $user = require_customer_api();

    $type = (string) post('target_type');
    $id = (int) post('target_id');
    if (!in_array($type, ['tour', 'hotel', 'destination'], true) || $id <= 0) {
        json_response(['ok' => false, 'error' => 'Invalid item.'], 400);
    }
    try {
        $existing = db_one(
            'SELECT id FROM wishlists WHERE user_id = ? AND target_type = ? AND target_id = ?',
            [(int) $user['id'], $type, $id]
        );
        if ($existing) {
            db()->prepare('DELETE FROM wishlists WHERE id = ?')->execute([(int) $existing['id']]);
            $active = false;
        } else {
            db()->prepare('INSERT INTO wishlists (user_id, target_type, target_id) VALUES (?, ?, ?)')
                ->execute([(int) $user['id'], $type, $id]);
            $active = true;
        }
    } catch (Throwable $e) {
        error_log('[fav] ' . $e->getMessage());
        json_response(['ok' => false, 'error' => 'Could not update favourites.'], 500);
    }

    // Classic form POST (favourites tab) -> go back to the page.
    if (!api_is_fetch()) {
        flash_set('success', $active ? 'Saved to favourites.' : 'Removed from favourites.');
        redirect('/account?tab=favorites');
    }
    json_response(['ok' => true, 'active' => $active, 'type' => $type, 'id' => $id]);
}

function api_account_bookings(): void {
    $user = require_customer_api();
    try {
        $rows = db_all(
            "SELECT b.id, b.reference, b.booking_type, b.status, b.payment_status, b.total, b.currency,
                    b.travel_date, b.check_in, b.check_out, b.guests, b.created_at,
                    t.title AS tour_title, h.name AS hotel_name
             FROM bookings b
             LEFT JOIN tours t ON t.id = b.tour_id
             LEFT JOIN hotels h ON h.id = b.hotel_id
             WHERE b.user_id = ? ORDER BY b.created_at DESC LIMIT 50",
            [(int) $user['id']]
        );
    } catch (Throwable $e) {
        error_log('[api-bookings] ' . $e->getMessage());
        json_response(['ok' => false, 'error' => 'Could not load bookings.'], 500);
    }
    json_response(['ok' => true, 'bookings' => $rows]);
}

function api_notifications(): void {
    $user = auth_user();
    if (!$user) json_response(['ok' => false, 'error' => 'Authentication required.'], 401);
    try {
        if ($user['role'] === 'admin') {
            $rows = db_all('SELECT id, type, title, message, is_read, created_at
                            FROM notifications ORDER BY created_at DESC LIMIT 30');
        } else {
            $rows = db_all('SELECT id, type, title, message, is_read, created_at
                            FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT 30',
                [(int) $user['id']]);
        }
        $unread = (int) db_val(
            'SELECT COUNT(*) FROM notifications WHERE is_read = 0 AND (user_id IS NULL OR user_id = ?)',
            [(int) $user['id']]
        );
    } catch (Throwable $e) {
        error_log('[api-notifications] ' . $e->getMessage());
        json_response(['ok' => false, 'error' => 'Could not load notifications.'], 500);
    }
    json_response(['ok' => true, 'notifications' => $rows, 'unread' => $unread]);
}

// ----------------------------------------------------------------- admin
function api_admin_stats(): void {
    require_admin(true);
    $monthStart = date('Y-m-01');
    $chart = db_all(
        "SELECT DATE_FORMAT(created_at, '%Y-%m') AS ym, COUNT(*) AS cnt, COALESCE(SUM(total),0) AS amt
         FROM bookings WHERE created_at >= (CURDATE() - INTERVAL 6 MONTH)
         GROUP BY ym ORDER BY ym ASC"
    );
    $byStatus = db_all('SELECT status, COUNT(*) AS cnt FROM bookings GROUP BY status');
    json_response([
        'ok' => true,
        'stats' => [
            'revenue' => (float) db_val("SELECT COALESCE(SUM(total),0) FROM bookings WHERE status IN ('confirmed','completed')"),
            'revenue_month' => (float) db_val("SELECT COALESCE(SUM(total),0) FROM bookings WHERE status IN ('confirmed','completed') AND created_at >= ?", [$monthStart]),
            'bookings' => (int) db_val('SELECT COUNT(*) FROM bookings'),
            'pending' => (int) db_val("SELECT COUNT(*) FROM bookings WHERE status='pending'"),
            'customers' => (int) db_val("SELECT COUNT(*) FROM users WHERE role='customer'"),
            'unread' => (int) db_val('SELECT COUNT(*) FROM notifications WHERE is_read = 0'),
        ],
        'chart' => $chart,
        'byStatus' => $byStatus,
    ]);
}

function api_admin_bookings(): void {
    require_admin(true);
    $limit = max(1, min(100, (int) input('limit', 25)));
    $status = (string) input('status');
    $sql = "SELECT b.id, b.reference, b.booking_type, b.status, b.payment_status, b.total, b.currency,
                   b.created_at, b.customer_name, b.customer_email,
                   t.title AS tour_title, h.name AS hotel_name
            FROM bookings b
            LEFT JOIN tours t ON t.id = b.tour_id
            LEFT JOIN hotels h ON h.id = b.hotel_id";
    $params = [];
    if ($status !== '' && in_array($status, ['pending', 'confirmed', 'completed', 'cancelled', 'refunded'], true)) {
        $sql .= ' WHERE b.status = ?';
        $params[] = $status;
    }
    $sql .= ' ORDER BY b.created_at DESC LIMIT ' . $limit;
    try {
        $rows = db_all($sql, $params);
    } catch (Throwable $e) {
        error_log('[api-admin-bookings] ' . $e->getMessage());
        json_response(['ok' => false, 'error' => 'Could not load bookings.'], 500);
    }
    json_response(['ok' => true, 'bookings' => $rows]);
}

function api_admin_list(string $resource): void {
    require_admin(true);
    $cfg = admin_resource_config($resource);
    if (!$cfg) json_response(['ok' => false, 'error' => 'Unknown resource.'], 404);

    $limit = max(1, min(100, (int) input('limit', 50)));
    try {
        $rows = db_all('SELECT * FROM ' . $cfg['table'] . ' ORDER BY ' . $cfg['order'] . ' LIMIT ' . $limit);
    } catch (Throwable $e) {
        error_log('[api-admin-list] ' . $e->getMessage());
        json_response(['ok' => false, 'error' => 'Could not load ' . $cfg['label'] . '.'], 500);
    }
    json_response(['ok' => true, 'resource' => $resource, 'label' => $cfg['label'], 'count' => count($rows), 'rows' => $rows]);
}

function api_admin_delete(string $resource, string $id): void {
    require_admin(true);
    csrf_header_check();
    $cfg = admin_resource_config($resource);
    if (!$cfg) json_response(['ok' => false, 'error' => 'Unknown resource.'], 404);

    try {
        $row = db_one('SELECT * FROM ' . $cfg['table'] . ' WHERE id = ?', [(int) $id]);
        if (!$row) json_response(['ok' => false, 'error' => 'Record not found.'], 404);
        db()->prepare('DELETE FROM ' . $cfg['table'] . ' WHERE id = ?')->execute([(int) $id]);
        if ($cfg['image_col'] ?? null) admin_delete_file((string) ($row[$cfg['image_col']] ?? ''));
    } catch (PDOException $e) {
        if ((string) $e->getCode() === '23000') {
            json_response(['ok' => false, 'error' => 'Cannot delete: other records reference this item.'], 409);
        }
        error_log('[api-admin-delete] ' . $e->getMessage());
        json_response(['ok' => false, 'error' => 'Delete failed.'], 500);
    }
    json_response(['ok' => true, 'deleted' => (int) $id]);
}
