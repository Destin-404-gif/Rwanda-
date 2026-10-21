<?php
/**
 * Authentication: sessions, login rate limiting, registration, guards.
 * One users table, role column: 'customer' | 'admin'.
 */

function auth_user(): ?array {
    static $user = false;
    if ($user !== false) return $user;
    $id = $_SESSION['user_id'] ?? null;
    if (!$id) return $user = null;
    $row = db_one('SELECT id, name, email, phone, role, avatar, status, loyalty_points, created_at FROM users WHERE id = ?', [$id]);
    if (!$row || $row['status'] === 'suspended') {
        unset($_SESSION['user_id'], $_SESSION['user_role']);
        return $user = null;
    }
    return $user = $row;
}

function is_logged_in(): bool { return auth_user() !== null; }
function is_admin(): bool { $u = auth_user(); return $u !== null && $u['role'] === 'admin'; }
function is_customer(): bool { $u = auth_user(); return $u !== null && $u['role'] === 'customer'; }

function login_user(array $user): void {
    session_regenerate_id(true);
    $_SESSION['user_id'] = (int) $user['id'];
    $_SESSION['user_role'] = $user['role'];
    $_SESSION['login_time'] = time();
    try {
        db()->prepare('UPDATE users SET last_login_at = NOW() WHERE id = ?')->execute([(int) $user['id']]);
        db()->prepare('DELETE FROM login_attempts WHERE ip = ? AND email = ?')->execute([client_ip(), $user['email']]);
    } catch (Throwable $e) {
        error_log('[auth] ' . $e->getMessage());
    }
}

function logout_user(): void {
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
    }
    session_destroy();
}

// ---- Rate limiting -----------------------------------------------------
function login_throttle(string $email): ?string {
    $ip = client_ip();
    try {
        $recent = db_val(
            'SELECT COUNT(*) FROM login_attempts WHERE ip = ? AND email = ? AND successful = 0 AND attempted_at > (NOW() - INTERVAL 15 MINUTE)',
            [$ip, $email]
        );
        if ((int) $recent >= 5) {
            return 'Too many failed attempts. Please wait 15 minutes before trying again.';
        }
        $blocked = db_val(
            'SELECT COUNT(*) FROM login_attempts WHERE ip = ? AND successful = 0 AND attempted_at > (NOW() - INTERVAL 15 MINUTE)',
            [$ip]
        );
        if ((int) $blocked >= 20) {
            return 'Too many failed attempts from this device. Please wait 15 minutes.';
        }
    } catch (Throwable $e) {
        error_log('[auth] throttle: ' . $e->getMessage());
    }
    return null;
}

function log_attempt(string $email, bool $success): void {
    try {
        db()->prepare('INSERT INTO login_attempts (ip, email, successful) VALUES (?, ?, ?)')
            ->execute([client_ip(), $email, $success ? 1 : 0]);
    } catch (Throwable $e) {
        error_log('[auth] ' . $e->getMessage());
    }
}

/**
 * Attempt login. $role restricts which accounts may sign in here:
 * 'customer' (public site) or 'admin' (dashboard).
 * Returns [user|null, error|null].
 */
function attempt_login(string $email, string $password, string $role): array {
    $email = mb_strtolower(trim($email));
    if ($email === '' || $password === '') {
        return [null, 'Please enter your email and password.'];
    }
    if ($throttled = login_throttle($email)) {
        return [null, $throttled];
    }

    $user = db_one('SELECT * FROM users WHERE email = ? LIMIT 1', [$email]);
    if (!$user || !password_verify($password, $user['password_hash'])) {
        log_attempt($email, false);
        return [null, 'Invalid email or password.'];
    }
    if ($user['status'] !== 'active') {
        log_attempt($email, false);
        return [null, $user['status'] === 'pending'
            ? 'Your account is waiting for approval by an existing admin.'
            : 'This account is not active. Please contact support.'];
    }
    // Role separation: customer login rejects admins and vice versa.
    if ($user['role'] !== $role) {
        log_attempt($email, false);
        return [null, $role === 'admin'
            ? 'This is a customer account. Sign in at the customer login instead.'
            : 'This is an administrator account. Please use the admin login.'];
    }

    // Rehash if the algorithm cost changed.
    if (password_needs_rehash($user['password_hash'], PASSWORD_BCRYPT, ['cost' => (int) cfg('BCRYPT_COST', 10)])) {
        try {
            db()->prepare('UPDATE users SET password_hash = ? WHERE id = ?')
                ->execute([password_hash($password, PASSWORD_BCRYPT, ['cost' => (int) cfg('BCRYPT_COST', 10)]), $user['id']]);
        } catch (Throwable $e) { /* non-fatal */ }
    }

    log_attempt($email, true);
    login_user($user);
    return [$user, null];
}

// ---- Registration ------------------------------------------------------
function password_errors(string $password): array {
    $errors = [];
    if (mb_strlen($password) < 8) $errors[] = 'Password must be at least 8 characters.';
    if (!preg_match('/[A-Za-z]/', $password) || !preg_match('/[0-9]/', $password)) {
        $errors[] = 'Password must contain letters and numbers.';
    }
    return $errors;
}

/**
 * @return array{0:?array,1:?string} [user|null, error|null]
 */
function register_account(array $data, string $role): array {
    $name = trim($data['name'] ?? '');
    $email = mb_strtolower(trim($data['email'] ?? ''));
    $phone = trim($data['phone'] ?? '');
    $password = (string) ($data['password'] ?? '');
    $confirm = (string) ($data['password_confirm'] ?? '');

    if ($name === '' || mb_strlen($name) < 2) return [null, 'Please enter your full name.'];
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) return [null, 'Please enter a valid email address.'];
    if ($phone !== '' && !preg_match('/^[0-9+\-\s()]{7,20}$/', $phone)) return [null, 'Please enter a valid phone number.'];
    if ($errs = password_errors($password)) return [null, implode(' ', $errs)];
    if ($password !== $confirm) return [null, 'Passwords do not match.'];

    try {
        $exists = db_one('SELECT id FROM users WHERE email = ?', [$email]);
        if ($exists) return [null, 'An account with this email already exists.'];

        // Admin accounts: the very first admin self-registers as active;
        // every later one is stored as 'pending' until an existing admin approves it.
        $status = 'active';
        $notify = false;
        if ($role === 'admin') {
            $adminCount = (int) db_val(
                "SELECT COUNT(*) FROM users WHERE role = 'admin' AND status = 'active'"
            );
            if ($adminCount > 0) {
                $status = 'pending';
                $notify = true;
            }
        }

        $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => (int) cfg('BCRYPT_COST', 10)]);
        $id = db_insert(
            'INSERT INTO users (name, email, phone, password_hash, role, status, email_verified) VALUES (?,?,?,?,?,?,1)',
            [$name, $email, $phone ?: null, $hash, $role, $status]
        );

        if ($notify) {
            try {
                db_insert(
                    "INSERT INTO notifications (user_id, type, title, message, channel) VALUES (NULL, 'admin_request', ?, ?, 'app')",
                    [
                        'New admin request pending approval',
                        "$name ($email) registered as an administrator and is waiting for approval. Review it in Admin -> Admin requests.",
                    ]
                );
            } catch (Throwable $e) {
                error_log('[register] notify: ' . $e->getMessage()); // non-fatal
            }
        }
    } catch (PDOException $e) {
        if ((string) $e->getCode() === '23000') return [null, 'An account with this email already exists.'];
        error_log('[register] ' . $e->getMessage());
        return [null, 'Could not create the account. Please try again.'];
    }

    $user = db_one('SELECT * FROM users WHERE id = ?', [$id]);
    if ($user['status'] === 'pending') {
        // Do not log a pending account in - it must be approved first.
        return [$user, null];
    }
    login_user($user);
    return [$user, null];
}

// ---- Guards ------------------------------------------------------------
function require_login(string $redirectTo = '/login'): void {
    if (!is_logged_in()) {
        $_SESSION['intended'] = $_SERVER['REQUEST_URI'] ?? '/';
        redirect($redirectTo);
    }
}

/**
 * Server-side authorization for every admin page AND admin API.
 * - not logged in      -> redirect to admin login (HTML) or 401 (JSON)
 * - logged in customer -> 403 (HTML) or 403 JSON: never admin content
 */
function require_admin(bool $json = false): void {
    $user = auth_user();
    // Server-side: only ACTIVE admins may reach any admin page or admin API.
    if ($user && $user['role'] === 'admin' && $user['status'] === 'active') return;

    if ($json) {
        if (!$user) json_response(['ok' => false, 'error' => 'Authentication required.'], 401);
        json_response(['ok' => false, 'error' => 'Admin access required.'], 403);
    }
    if (!$user) {
        $_SESSION['intended'] = $_SERVER['REQUEST_URI'] ?? '/admin';
        redirect('/admin/login');
    }
    http_response_code(403);
    render('pages/error', [
        'code' => 403,
        'title' => 'Access denied',
        'message' => 'You are signed in as a customer. Administrator pages are restricted.',
        'layout' => 'site',
    ], 'site');
    exit;
}

function require_customer_api(): array {
    $user = auth_user();
    if (!$user) json_response(['ok' => false, 'error' => 'Please sign in.'], 401);
    return $user;
}
