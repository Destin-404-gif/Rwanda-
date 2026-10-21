<?php
/** Shared variables for layouts. */
$title = $title ?? 'Rwanda Explorer';
$description = $description ?? setting('tagline', 'Explore Rwanda: gorillas, safaris, lakes and hills.');
$active = $active ?? '';
$body_class = $body_class ?? '';
$currentUser = auth_user();
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<title><?= e($title) ?></title>
<meta name="description" content="<?= e($description) ?>">
<meta name="theme-color" content="#0B0F0A">
<base href="<?= e(url('/')) ?>">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700;800;900&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= e(asset('assets/css/app.css')) ?>">
<link rel="icon" href="<?= e(asset('assets/img/dest-gorilla.jpg')) ?>" type="image/jpeg">
<script>window.__BASE__ = <?= json_encode(base_path()) ?>; window.__CSRF__ = <?= json_encode(csrf_token()) ?>; window.__USER__ = <?= json_encode($currentUser ? ['id' => (int) $currentUser['id'], 'role' => $currentUser['role']] : null) ?>;</script>
</head>
<body class="<?= e($body_class) ?>">
<a class="skip-link" href="#main">Skip to content</a>
<?php partial('navbar', ['active' => $active, 'user' => $currentUser]); ?>
<main id="main">
<?php echo $content; ?>
</main>
<?php partial('footer'); ?>
<?php partial('overlays'); ?>
<div id="toast-root" class="toast-root" role="status" aria-live="polite"></div>
<script src="<?= e(asset('assets/js/app.js')) ?>" defer></script>
</body>
</html>
