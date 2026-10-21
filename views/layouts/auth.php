<?php
/** Split-screen auth layout (login / register / reset). */
$title = $title ?? 'Sign in';
$description = $description ?? 'Sign in to Rwanda Explorer.';
$isAdmin = (($mode ?? 'customer') === 'admin');
$art = $isAdmin ? 'assets/img/hero-volcanoes.jpg' : 'assets/img/hero-kivu.jpg';
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($title) ?> | Rwanda Explorer</title>
<meta name="description" content="<?= e($description) ?>" robots="noindex,nofollow">
<meta name="theme-color" content="#0B0F0A">
<base href="<?= e(url('/')) ?>">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700;800;900&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= e(asset('assets/css/app.css')) ?>">
<script>window.__BASE__ = <?= json_encode(base_path()) ?>; window.__CSRF__ = <?= json_encode(csrf_token()) ?>;</script>
</head>
<body class="page-auth <?= $isAdmin ? 'is-admin' : 'is-customer' ?>">
<div class="auth-shell">
  <aside class="auth-art" style="background-image:linear-gradient(160deg,rgba(11,15,10,.35),rgba(11,15,10,.92)),url('<?= e(asset($art)) ?>')">
    <a class="brand auth-brand" href="<?= e(url('/')) ?>">
      <span class="brand-badge"><?= icon('map-pin', '', 20) ?></span>
      <span class="brand-text">Rwanda Explorer</span>
    </a>
    <div class="auth-quote">
      <p class="tagline"><?= $isAdmin ? 'Control centre' : 'Land of a Thousand Hills' ?></p>
      <p class="auth-quote-text"><?= $isAdmin
          ? 'Bookings, inventory, content and analytics - all in one dark, fast dashboard.'
          : '"The best trips start with a single step - and end with a story you tell for years."' ?></p>
      <div class="auth-quote-stats">
        <span><strong>5</strong> national parks</span>
        <span><strong>10+</strong> signature tours</span>
        <span><strong>24/7</strong> support</span>
      </div>
    </div>
    <p class="auth-art-foot">&copy; <?= date('Y') ?> Rwanda Explorer</p>
  </aside>

  <main class="auth-main">
    <?php foreach (flash_pull() as $f): ?>
      <div class="alert alert-<?= e($f['type']) ?>" role="alert">
        <?= icon($f['type'] === 'error' ? 'alert-circle' : 'check-circle', 'i-sm', 18) ?>
        <span><?= e($f['message']) ?></span>
      </div>
    <?php endforeach; ?>

    <div class="auth-card">
      <?php echo $content; ?>
    </div>

    <p class="auth-switch">
      <a href="<?= e(url('/')) ?>"><?= icon('arrow-left', 'i-xs', 15) ?> Back to the site</a>
    </p>
  </main>
</div>
<div id="toast-root" class="toast-root" role="status" aria-live="polite"></div>
<script src="<?= e(asset('assets/js/app.js')) ?>" defer></script>
</body>
</html>
