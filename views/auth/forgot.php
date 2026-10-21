<?php
$isAdmin = $mode === 'admin';
$action = $isAdmin ? '/admin/forgot-password' : '/forgot-password';
$debugLink = $_SESSION['debug_reset_link'] ?? null;
unset($_SESSION['debug_reset_link']);
?>
<div class="auth-head">
  <p class="tagline">Account recovery</p>
  <h1 class="auth-title"><?= e($title) ?></h1>
  <p class="auth-sub"><?= e($sub) ?></p>
</div>

<form class="auth-form" method="post" action="<?= e(url($action)) ?>" novalidate>
  <?= csrf_field() ?>
  <label class="field">
    <span>Email address</span>
    <span class="input-wrap">
      <?= icon('mail', 'input-icon', 18) ?>
      <input type="email" name="email" required maxlength="180" autocomplete="email"
             placeholder="you@example.com" autofocus>
    </span>
  </label>
  <button class="btn btn-primary btn-lg btn-block" type="submit">
    <?= icon('send', 'i-sm', 16) ?> Send reset link
  </button>
</form>

<?php if ($debugLink): ?>
  <div class="alert alert-info dev-link">
    <strong>Dev mode:</strong> <a href="<?= e($debugLink) ?>"><?= e($debugLink) ?></a>
    <small>(In production this link is emailed - outbound SMTP is disabled on InfinityFree, so log it instead.)</small>
  </div>
<?php endif; ?>

<div class="auth-alt">
  <p><a href="<?= e(url($isAdmin ? '/admin/login' : '/login')) ?>"><?= icon('arrow-left', 'i-xs', 15) ?> Back to login</a></p>
  <?php if ($isAdmin): ?>
    <p>Customer? <a href="<?= e(url('/forgot-password')) ?>">Customer reset</a></p>
  <?php else: ?>
    <p>Staff? <a href="<?= e(url('/admin/forgot-password')) ?>">Admin reset</a></p>
  <?php endif; ?>
</div>
