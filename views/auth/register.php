<?php
$isAdmin = $mode === 'admin';
$action = $isAdmin ? '/admin/register' : '/register';
$old = $_SESSION['old'] ?? [];
unset($_SESSION['old']);
?>
<div class="auth-head">
  <p class="tagline"><?= $isAdmin ? 'Restricted' : 'Join us' ?></p>
  <h1 class="auth-title"><?= e($title) ?></h1>
  <p class="auth-sub"><?= e($sub) ?></p>
</div>

<form class="auth-form" method="post" action="<?= e(url($action)) ?>" novalidate>
  <?= csrf_field() ?>
  <input type="hidden" name="next" value="<?= e($next ?? '/') ?>">

  <label class="field">
    <span>Full name</span>
    <span class="input-wrap">
      <?= icon('user', 'input-icon', 18) ?>
      <input type="text" name="name" required minlength="2" maxlength="120"
             autocomplete="name" placeholder="Aline Uwase" value="<?= e($old['name'] ?? '') ?>" autofocus>
    </span>
  </label>

  <label class="field">
    <span>Email address</span>
    <span class="input-wrap">
      <?= icon('mail', 'input-icon', 18) ?>
      <input type="email" name="email" required maxlength="180"
             autocomplete="email" placeholder="you@example.com" value="<?= e($old['email'] ?? '') ?>">
    </span>
  </label>

  <label class="field">
    <span>Phone <small class="optional">(optional)</small></span>
    <span class="input-wrap">
      <?= icon('phone', 'input-icon', 18) ?>
      <input type="tel" name="phone" maxlength="20" autocomplete="tel"
             placeholder="+250 788 000 000" value="<?= e($old['phone'] ?? '') ?>">
    </span>
  </label>

  <div class="form-row">
    <label class="field">
      <span>Password</span>
      <span class="input-wrap">
        <?= icon('lock', 'input-icon', 18) ?>
        <input type="password" name="password" id="reg-password" required minlength="8"
               autocomplete="new-password" placeholder="8+ characters">
        <button class="input-eye" type="button" data-password-toggle="reg-password"
                aria-label="Show password"><?= icon('eye', '', 18) ?></button>
      </span>
    </label>
    <label class="field">
      <span>Confirm</span>
      <span class="input-wrap">
        <?= icon('lock', 'input-icon', 18) ?>
        <input type="password" name="password_confirm" required minlength="8"
               autocomplete="new-password" placeholder="Repeat password">
      </span>
    </label>
  </div>

  <ul class="pw-hint" data-pw-hint>
    <li data-rule="len">At least 8 characters</li>
    <li data-rule="mix">Letters and numbers</li>
    <li data-rule="match">Passwords match</li>
  </ul>

  <button class="btn btn-primary btn-lg btn-block" type="submit">
    <?= icon('user-plus', 'i-sm', 16) ?> <?= $isAdmin ? 'Create admin account' : 'Create account' ?>
  </button>
</form>

<div class="auth-alt">
  <?php if ($isAdmin): ?>
    <p>Already registered? <a href="<?= e(url('/admin/login')) ?>">Admin login</a></p>
    <p>Customer? <a href="<?= e(url('/register')) ?>">Customer register</a></p>
  <?php else: ?>
    <p>Already have an account? <a href="<?= e(url('/login')) ?>">Sign in</a></p>
    <p>Staff? <a href="<?= e(url('/admin/register')) ?>">Admin register</a></p>
  <?php endif; ?>
</div>
