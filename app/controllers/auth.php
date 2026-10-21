<?php
/**
 * Authentication pages: customer login/register + admin login/register,
 * logout, forgot/reset password. Role-separated on purpose.
 */

/** Render an auth page with the split-screen layout. */
function auth_view(string $template, array $vars): void {
    $vars['active'] = '';
    $vars['body_class'] = 'page-auth';
    render($template, $vars, 'auth');
}

function auth_next_url(string $default = '/'): string {
    $next = (string) ($_GET['next'] ?? ($_SESSION['intended'] ?? ''));
    if ($next !== '' && $next[0] === '/' && !str_starts_with($next, '//') && !str_contains($next, '//')) {
        return $next;
    }
    return $default;
}

// ---------------------------------------------------------------- customer
function login_form(): void {
    if (auth_user()) redirect(auth_user()['role'] === 'admin' ? '/admin' : '/account');
    auth_view('auth/login', [
        'mode' => 'customer',
        'title' => 'Customer login',
        'sub' => 'Sign in to book tours, manage trips and save favourites.',
        'next' => auth_next_url('/account'),
    ]);
}

function login_submit(): void {
    csrf_verify();
    $email = post('email');
    $password = (string) ($_POST['password'] ?? '');
    [$user, $error] = attempt_login($email, $password, 'customer');
    if ($error) {
        flash_set('error', $error);
        $_SESSION['old_email'] = mb_substr($email, 0, 180);
        redirect('/login');
    }
    $next = post('next');
    if ($next !== '' && $next[0] === '/' && !str_contains($next, '//')) {
        redirect($next);
    }
    unset($_SESSION['intended']);
    redirect('/account');
}

function register_form(): void {
    if (auth_user()) redirect(auth_user()['role'] === 'admin' ? '/admin' : '/account');
    auth_view('auth/register', [
        'mode' => 'customer',
        'title' => 'Create your account',
        'sub' => 'Book in seconds, track bookings and get member-only rates.',
        'next' => auth_next_url('/account'),
    ]);
}

function register_submit(): void {
    csrf_verify();
    [$user, $error] = register_account([
        'name' => post('name'),
        'email' => post('email'),
        'phone' => post('phone'),
        'password' => (string) ($_POST['password'] ?? ''),
        'password_confirm' => (string) ($_POST['password_confirm'] ?? ''),
    ], 'customer');
    if ($error) {
        flash_set('error', $error);
        $_SESSION['old'] = [
            'name' => post('name'), 'email' => post('email'), 'phone' => post('phone'),
        ];
        redirect('/register');
    }
    flash_set('success', 'Welcome, ' . excerpt($user['name'], 30) . '! Your account is ready.');
    $next = post('next');
    if ($next !== '' && $next[0] === '/' && !str_contains($next, '//')) redirect($next);
    redirect('/account');
}

function logout_customer(): void {
    logout_user();
    session_start();
    flash_set('success', 'You have been logged out.');
    redirect('/');
}

// ------------------------------------------------------------------- admin
function admin_login_form(): void {
    if (is_admin()) redirect('/admin');
    auth_view('auth/login', [
        'mode' => 'admin',
        'title' => 'Admin login',
        'sub' => 'Restricted access - Rwanda Explorer staff only.',
        'next' => auth_next_url('/admin'),
    ]);
}

function admin_login_submit(): void {
    csrf_verify();
    $email = post('email');
    $password = (string) ($_POST['password'] ?? '');
    [$user, $error] = attempt_login($email, $password, 'admin');
    if ($error) {
        flash_set('error', $error);
        $_SESSION['old_email'] = mb_substr($email, 0, 180);
        redirect('/admin/login');
    }
    unset($_SESSION['intended']);
    redirect('/admin');
}

function admin_register_form(): void {
    if (is_admin()) redirect('/admin');
    auth_view('auth/register', [
        'mode' => 'admin',
        'title' => 'Register an admin',
        'sub' => 'Creates a dashboard account. The first admin is active; later ones need approval.',
        'next' => '/admin',
    ]);
}

function admin_register_submit(): void {
    csrf_verify();
    [$user, $error] = register_account([
        'name' => post('name'),
        'email' => post('email'),
        'phone' => post('phone'),
        'password' => (string) ($_POST['password'] ?? ''),
        'password_confirm' => (string) ($_POST['password_confirm'] ?? ''),
    ], 'admin');
    if ($error) {
        flash_set('error', $error);
        $_SESSION['old'] = ['name' => post('name'), 'email' => post('email'), 'phone' => post('phone')];
        redirect('/admin/register');
    }
    if (($user['status'] ?? '') === 'pending') {
        flash_set('info', 'Your admin request was received and is waiting for approval by an existing admin. You cannot sign in until it is approved.');
        redirect('/admin/login');
    }
    flash_set('success', 'Admin account created. Welcome to the dashboard.');
    redirect('/admin');
}

function logout_admin(): void {
    logout_user();
    session_start();
    flash_set('success', 'Signed out of the dashboard.');
    redirect('/admin/login');
}

// ------------------------------------------------------- password recovery
/** Shared forgot/reset logic for both roles. */
function forgot_form_generic(string $role): void {
    auth_view('auth/forgot', [
        'mode' => $role,
        'title' => 'Reset your password',
        'sub' => 'We will email you a secure reset link (valid for 60 minutes).',
    ]);
}

function forgot_submit_generic(string $role): void {
    csrf_verify();
    $email = mb_strtolower(trim(post('email')));
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        try {
            $user = db_one('SELECT id, name FROM users WHERE email = ? AND role = ?', [$email, $role]);
            if ($user) {
                $token = bin2hex(random_bytes(32));
                db_insert(
                    'INSERT INTO password_resets (user_id, token, expires_at) VALUES (?, ?, NOW() + INTERVAL 60 MINUTE)',
                    [(int) $user['id'], hash('sha256', $token)]
                );
                // No outbound SMTP on InfinityFree: log + show the link in dev.
                $link = url('/reset-password?role=' . $role . '&token=' . $token);
                error_log("[password-reset] $email -> $link");
                if (cfg('SHOW_RESET_LINK', false)) {
                    $_SESSION['debug_reset_link'] = $link;
                }
            }
        } catch (Throwable $e) {
            error_log('[forgot] ' . $e->getMessage());
        }
    }
    // Same message either way - no account enumeration.
    flash_set('success', 'If that email has an account, a reset link is on its way. Check your inbox (and spam).');
    redirect($role === 'admin' ? '/admin/forgot-password' : '/forgot-password');
}

function reset_form_generic(string $role): void {
    $token = (string) input('token');
    $user = null;
    if ($token !== '') {
        try {
            $row = db_one(
                'SELECT pr.user_id FROM password_resets pr
                 JOIN users u ON u.id = pr.user_id
                 WHERE pr.token = ? AND pr.used_at IS NULL AND pr.expires_at > NOW() AND u.role = ?',
                [hash('sha256', $token), $role]
            );
            $user = $row ?: null;
        } catch (Throwable $e) {
            error_log('[reset] ' . $e->getMessage());
        }
    }
    if (!$user) {
        flash_set('error', 'This reset link is invalid or has expired. Request a new one.');
        redirect($role === 'admin' ? '/admin/forgot-password' : '/forgot-password');
    }
    auth_view('auth/reset', [
        'mode' => $role,
        'title' => 'Choose a new password',
        'sub' => 'At least 8 characters with letters and numbers.',
        'token' => $token,
    ]);
}

function reset_submit_generic(string $role): void {
    csrf_verify();
    $token = (string) post('token');
    $password = (string) ($_POST['password'] ?? '');
    $confirm = (string) ($_POST['password_confirm'] ?? '');

    if ($errs = password_errors($password)) {
        flash_set('error', implode(' ', $errs));
        redirect('/reset-password?role=' . $role . '&token=' . urlencode($token));
    }
    if ($password !== $confirm) {
        flash_set('error', 'Passwords do not match.');
        redirect('/reset-password?role=' . $role . '&token=' . urlencode($token));
    }

    try {
        $row = db_one(
            'SELECT pr.id, pr.user_id FROM password_resets pr
             JOIN users u ON u.id = pr.user_id
             WHERE pr.token = ? AND pr.used_at IS NULL AND pr.expires_at > NOW() AND u.role = ?',
            [hash('sha256', $token), $role]
        );
        if (!$row) {
            flash_set('error', 'This reset link is invalid or has expired.');
            redirect($role === 'admin' ? '/admin/forgot-password' : '/forgot-password');
        }
        $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => (int) cfg('BCRYPT_COST', 10)]);
        db()->prepare('UPDATE users SET password_hash = ? WHERE id = ?')->execute([$hash, (int) $row['user_id']]);
        db()->prepare('UPDATE password_resets SET used_at = NOW() WHERE id = ?')->execute([(int) $row['id']]);
        db()->prepare('DELETE FROM login_attempts WHERE email IN (SELECT email FROM users WHERE id = ?)')
            ->execute([(int) $row['user_id']]);
    } catch (Throwable $e) {
        error_log('[reset] ' . $e->getMessage());
        flash_set('error', 'Could not update the password. Please try again.');
        redirect($role === 'admin' ? '/admin/forgot-password' : '/forgot-password');
    }

    flash_set('success', 'Password updated. You can sign in now.');
    redirect($role === 'admin' ? '/admin/login' : '/login');
}

function forgot_form(): void   { forgot_form_generic('customer'); }
function forgot_submit(): void { forgot_submit_generic('customer'); }
function reset_form(): void    { reset_form_generic('customer'); }
function reset_submit(): void  { reset_submit_generic('customer'); }

function admin_forgot_form(): void   { forgot_form_generic('admin'); }
function admin_forgot_submit(): void { forgot_submit_generic('admin'); }
function admin_reset_form(): void    { reset_form_generic('admin'); }
function admin_reset_submit(): void  { reset_submit_generic('admin'); }
