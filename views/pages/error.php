<?php
$code = $code ?? 404;
partial('page-header', [
    'tag' => 'Error ' . $code,
    'title' => ($code === 404 ? 'Lost in the <span class="accent">hills</span>' : 'Something went <span class="accent">wrong</span>'),
    'sub' => $message ?? 'The page you are looking for does not exist or has been moved.',
    'image' => 'assets/img/hero-volcanoes.jpg',
]);
?>
<section class="section">
  <div class="container error-actions">
    <a class="btn btn-primary btn-lg" href="<?= e(url('/')) ?>"><?= icon('home', 'i-sm', 16) ?> Back home</a>
    <a class="btn btn-ghost btn-lg" href="<?= e(url('/destinations')) ?>">Browse destinations</a>
    <a class="link-secondary" href="<?= e(url('/contact')) ?>">Report this problem</a>
  </div>
</section>
