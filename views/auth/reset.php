<?php
$isAdmin = $mode === 'admin';
$action = $isAdmin ? '/admin/reset-password' : '/reset-password';
?>
<div class="auth-head">
  <p class="tagline">Account recovery</p>
  <h1 class="auth-title"><?= e($title) ?></h1>
  <p class="auth-sub"><?= e($sub) ?></p>
</div>

<form class="auth-form" method="post" action="<?= e(url($action)) ?>" novalidate>
  <?= csrf_field() ?>
  <input type="hidden" name="token" value="<?= e($token) ?>">

  <label class="field">
    <span>New password</span>
    <span class="input-wrap">
      <?= icon('lock', 'input-icon', 18) ?>
      <input type="password" name="password" id="reset-password" required minlength="8"
             autocomplete="new-password" placeholder="8+ characters" autofocus>
      <button class="input-eye" type="button" data-password-toggle="reset-password"
              aria-label="Show password"><?= icon('eye', '', 18) ?></button>
    </span>
  </label>

  <label class="field">
    <span>Confirm new password</span>
    <span class="input-wrap">
      <?= icon('lock', 'input-icon', 18) ?>
      <input type="password" name="password_confirm" required minlength="8"
             autocomplete="new-password" placeholder="Repeat password">
    </span>
  </label>

  <button class="btn btn-primary btn-lg btn-block" type="submit">
    <?= icon('check', 'i-sm', 16) ?> Update password
  </button>
</form>

<div class="auth-alt">
  <p><a href="<?= e(url($isAdmin ? '/admin/login' : '/login')) ?>"><?= icon('arrow-left', 'i-xs', 15) ?> Back to login</a></p>
</div>
