<?php partial('page-header', [
    'tag' => 'Stories & tips',
    'title' => 'Travel <span class="accent">blog</span>',
    'sub' => 'Field notes from our guides: trekking advice, hidden corners and cultural deep-dives.',
    'image' => 'assets/img/gallery-tea.jpg',
]); ?>
<section class="section">
  <div class="container">
    <?php if (!$posts): ?>
      <p class="empty-state">No articles published yet - check back soon.</p>
    <?php else: ?>
      <div class="grid grid-cards">
        <?php foreach ($posts as $p): ?>
          <a class="tour-card glass blog-card" href="<?= e(url('/blog/' . $p['slug'])) ?>">
            <span class="card-media">
              <img src="<?= e(img_url($p['cover_image'] ?: 'assets/img/dest-hills.jpg')) ?>" alt="<?= e($p['title']) ?>" loading="lazy" decoding="async">
            </span>
            <span class="card-body">
              <span class="card-kicker"><?= e($p['category'] ?: 'Guide') ?> &middot; <?= e(date('d M Y', strtotime($p['published_at'] ?: $p['created_at']))) ?></span>
              <strong class="card-title"><?= e($p['title']) ?></strong>
              <span class="card-text"><?= e($p['excerpt'] ?: excerpt((string) $p['content'], 110)) ?></span>
              <span class="card-cta">Read article <?= icon('arrow-right', 'i-sm', 15) ?></span>
            </span>
          </a>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</section>
