<?php
$isAdmin = $mode === 'admin';
$action = $isAdmin ? '/admin/login' : '/login';
$oldEmail = $_SESSION['old_email'] ?? '';
unset($_SESSION['old_email']);
?>
<div class="auth-head">
  <p class="tagline"><?= $isAdmin ? 'Staff access' : 'Welcome back' ?></p>
  <h1 class="auth-title"><?= e($title) ?></h1>
  <p class="auth-sub"><?= e($sub) ?></p>
</div>

<form class="auth-form" method="post" action="<?= e(url($action)) ?>" novalidate>
  <?= csrf_field() ?>
  <input type="hidden" name="next" value="<?= e($next ?? '/') ?>">

  <label class="field">
    <span>Email address</span>
    <span class="input-wrap">
      <?= icon('mail', 'input-icon', 18) ?>
      <input type="email" name="email" required maxlength="180" autocomplete="username"
             placeholder="you@example.com" value="<?= e($oldEmail) ?>" autofocus>
    </span>
  </label>

  <label class="field">
    <span class="field-row">
      <span>Password</span>
      <a class="field-link" href="<?= e(url($isAdmin ? '/admin/forgot-password' : '/forgot-password')) ?>">Forgot password?</a>
    </span>
    <span class="input-wrap">
      <?= icon('lock', 'input-icon', 18) ?>
      <input type="password" name="password" id="login-password" required minlength="8"
             autocomplete="current-password" placeholder="Your password">
      <button class="input-eye" type="button" data-password-toggle="login-password"
              aria-label="Show password"><?= icon('eye', '', 18) ?></button>
    </span>
  </label>

  <button class="btn btn-primary btn-lg btn-block" type="submit">
    <?= icon('log-out', 'i-sm', 16) ?> <?= $isAdmin ? 'Enter dashboard' : 'Sign in' ?>
  </button>
</form>

<div class="auth-alt">
  <?php if ($isAdmin): ?>
    <p>No account yet? <a href="<?= e(url('/admin/register')) ?>">Register an admin</a></p>
    <p>Customer? <a href="<?= e(url('/login')) ?>">Sign in here</a></p>
  <?php else: ?>
    <p>New here? <a href="<?= e(url('/register')) ?>">Create an account</a></p>
    <p>Staff? <a href="<?= e(url('/admin/login')) ?>">Admin login</a></p>
  <?php endif; ?>
</div>
