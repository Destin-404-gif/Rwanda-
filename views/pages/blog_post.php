<?php
partial('page-header', [
    'tag' => $post['category'] ?: 'Blog',
    'title' => e($post['title']),
    'sub' => ($post['excerpt'] ?: '') . ' ' . ($post['author'] ? 'By ' . $post['author'] : ''),
    'image' => $post['cover_image'] ?: 'assets/img/dest-hills.jpg',
]);
?>
<section class="section">
  <div class="container container-narrow">
    <article class="rich-text article-body">
      <?= nl2br(e((string) $post['content'])) ?>
    </article>

    <div class="article-foot">
      <span class="card-kicker"><?= icon('calendar', 'i-xs', 14) ?> <?= e(date('d F Y', strtotime($post['published_at'] ?: $post['created_at']))) ?></span>
      <a class="link-secondary" href="<?= e(url('/blog')) ?>"><?= icon('arrow-left', 'i-sm', 16) ?> All articles</a>
    </div>

    <?php if ($others): ?>
      <section class="subsection">
        <h2 class="subsection-title">Keep reading</h2>
        <div class="grid grid-3">
          <?php foreach ($others as $o): ?>
            <a class="tour-card glass" href="<?= e(url('/blog/' . $o['slug'])) ?>">
              <span class="card-media"><img src="<?= e(img_url($o['cover_image'] ?: 'assets/img/dest-hills.jpg')) ?>" alt="<?= e($o['title']) ?>" loading="lazy"></span>
              <span class="card-body">
                <span class="card-kicker"><?= e($o['category'] ?: 'Guide') ?></span>
                <strong class="card-title"><?= e($o['title']) ?></strong>
                <span class="card-cta">Read <?= icon('arrow-right', 'i-sm', 15) ?></span>
              </span>
            </a>
          <?php endforeach; ?>
        </div>
      </section>
    <?php endif; ?>
  </div>
</section>
