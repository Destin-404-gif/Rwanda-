<?php
$posts = db_all("SELECT slug, title FROM blog_posts WHERE status='published' ORDER BY published_at DESC LIMIT 4");
?>
<footer class="site-footer">
  <div class="container footer-grid">
    <div class="footer-brand">
      <a class="brand" href="<?= e(url('/')) ?>">
        <span class="brand-badge"><?= icon('map-pin', '', 20) ?></span>
        <span class="brand-text">Rwanda Explorer</span>
      </a>
      <p><?= e(setting('tagline', 'Best overall for tourist attractions, tour bookings, hotels, and travel guides.')) ?></p>
      <div class="footer-social">
        <a href="<?= e(url('/contact')) ?>" aria-label="Facebook"><?= icon('facebook', '', 18) ?></a>
        <a href="<?= e(url('/contact')) ?>" aria-label="Instagram"><?= icon('instagram', '', 18) ?></a>
        <a href="<?= e(url('/contact')) ?>" aria-label="Twitter"><?= icon('twitter', '', 18) ?></a>
        <a href="<?= e(url('/contact')) ?>" aria-label="YouTube"><?= icon('youtube', '', 18) ?></a>
      </div>
    </div>

    <div>
      <h4 class="footer-title">Explore</h4>
      <ul class="footer-links">
        <li><a href="<?= e(url('/destinations')) ?>">Destinations</a></li>
        <li><a href="<?= e(url('/tours')) ?>">Tours & Safaris</a></li>
        <li><a href="<?= e(url('/hotels')) ?>">Hotels & Lodges</a></li>
        <li><a href="<?= e(url('/experiences')) ?>">Experiences</a></li>
        <li><a href="<?= e(url('/gallery')) ?>">Gallery</a></li>
      </ul>
    </div>

    <div>
      <h4 class="footer-title">Company</h4>
      <ul class="footer-links">
        <li><a href="<?= e(url('/about')) ?>">About us</a></li>
        <li><a href="<?= e(url('/guide')) ?>">Travel guide</a></li>
        <li><a href="<?= e(url('/blog')) ?>">Blog</a></li>
        <li><a href="<?= e(url('/contact')) ?>">Contact</a></li>
        <li><a href="<?= e(url('/account')) ?>">My account</a></li>
      </ul>
    </div>

    <div>
      <h4 class="footer-title">Recent articles</h4>
      <ul class="footer-links">
        <?php foreach ($posts as $p): ?>
          <li><a href="<?= e(url('/blog/' . $p['slug'])) ?>"><?= e($p['title']) ?></a></li>
        <?php endforeach; ?>
      </ul>
    </div>

    <div>
      <h4 class="footer-title">Contact</h4>
      <ul class="footer-contact">
        <li><?= icon('map-pin', 'i-sm', 16) ?> <span><?= e(setting('address', 'KG 7 Ave, Kigali, Rwanda')) ?></span></li>
        <li><?= icon('phone', 'i-sm', 16) ?> <a href="tel:<?= e(preg_replace('/\s+/', '', setting('phone', '+250788000000'))) ?>"><?= e(setting('phone', '+250 788 000 000')) ?></a></li>
        <li><?= icon('mail', 'i-sm', 16) ?> <a href="mailto:<?= e(setting('email', 'hello@rwandaexplorer.rw')) ?>"><?= e(setting('email', 'hello@rwandaexplorer.rw')) ?></a></li>
      </ul>
    </div>
  </div>

  <div class="container footer-bottom">
    <p>&copy; <?= date('Y') ?> Rwanda Explorer. All rights reserved.</p>
    <p>
      <a href="<?= e(url('/guide')) ?>">Travel guide</a>
      <a href="<?= e(url('/contact')) ?>">Terms & privacy</a>
      <a href="<?= e(url('/admin/login')) ?>">Admin</a>
    </p>
  </div>
</footer>
