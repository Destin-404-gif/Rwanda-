<?php
partial('page-header', [
    'tag' => $dest['region'],
    'title' => e($dest['name']),
    'sub' => excerpt((string) ($dest['short_description'] ?: $dest['description']), 160),
    'image' => $dest['hero_image'],
]);
$heroStats = [];
if (!empty($dest['best_season'])) $heroStats[] = ['Best season', $dest['best_season']];
if (!empty($dest['entry_fee'])) $heroStats[] = ['Entry fee', money($dest['entry_fee'], 'USD')];
if (!empty($dest['how_to_get_there'])) $heroStats[] = ['Getting there', $dest['how_to_get_there']];
?>
<section class="section">
  <div class="container detail-layout">
    <article class="detail-main">
      <?php if ($heroStats): ?>
        <div class="fact-row glass">
          <?php foreach ($heroStats as [$k, $v]): ?>
            <div class="fact"><span class="fact-k"><?= e($k) ?></span><span class="fact-v"><?= e($v) ?></span></div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <div class="rich-text">
        <h2>About <?= e($dest['name']) ?></h2>
        <?= nl2br(e((string) $dest['description'])) ?>
        <?php $highlights = jarr($dest['activities'] ?? []); if ($highlights): ?>
          <h3>Highlights</h3>
          <ul class="check-list">
            <?php foreach ($highlights as $h): ?>
              <li><?= icon('check', 'i-xs', 16) ?> <?= e($h) ?></li>
            <?php endforeach; ?>
          </ul>
        <?php endif; ?>
      </div>

      <?php if ($tours): ?>
        <section class="subsection">
          <h2 class="subsection-title">Tours in <?= e($dest['name']) ?></h2>
          <div class="grid grid-3">
            <?php foreach ($tours as $t): ?>
              <a class="tour-card glass" href="<?= e(url('/tours/' . $t['slug'])) ?>">
                <span class="card-media">
                  <img src="<?= e(img_url($t['cover_image'])) ?>" alt="<?= e($t['title']) ?>" loading="lazy">
                  <span class="price-badge"><?= e(money($t['price'], $t['price'] > 5000 ? 'RWF' : 'USD')) ?></span>
                </span>
                <span class="card-body">
                  <span class="card-kicker"><?= e($t['category']) ?> &middot; <?= e($t['duration']) ?></span>
                  <strong class="card-title"><?= e($t['title']) ?></strong>
                  <span class="card-cta">View tour <?= icon('arrow-right', 'i-sm', 15) ?></span>
                </span>
              </a>
            <?php endforeach; ?>
          </div>
        </section>
      <?php endif; ?>

      <?php if ($hotels): ?>
        <section class="subsection">
          <h2 class="subsection-title">Where to stay</h2>
          <div class="grid grid-3">
            <?php foreach ($hotels as $h): ?>
              <a class="tour-card glass" href="<?= e(url('/hotels/' . $h['slug'])) ?>">
                <span class="card-media">
                  <img src="<?= e(img_url($h['cover_image'])) ?>" alt="<?= e($h['name']) ?>" loading="lazy">
                  <span class="price-badge"><?= e(money($h['base_price'], $h['currency'])) ?><small>/night</small></span>
                </span>
                <span class="card-body">
                  <span class="card-kicker"><?= e($h['city']) ?></span>
                  <strong class="card-title"><?= e($h['name']) ?></strong>
                  <span class="card-cta">View hotel <?= icon('arrow-right', 'i-sm', 15) ?></span>
                </span>
              </a>
            <?php endforeach; ?>
          </div>
        </section>
      <?php endif; ?>

      <?php if ($reviews): ?>
        <section class="subsection">
          <h2 class="subsection-title">Traveller reviews</h2>
          <div class="grid grid-2">
            <?php foreach ($reviews as $r): ?>
              <figure class="quote-card glass">
                <div class="quote-stars"><?= str_repeat('<span aria-hidden="true">★</span>', (int) $r['rating']) ?></div>
                <blockquote><?= e($r['comment']) ?></blockquote>
                <figcaption>
                  <span class="avatar avatar-sm"><?= e(initials($r['author_name'] ?: 'Guest')) ?></span>
                  <span><strong><?= e($r['author_name'] ?: 'Guest') ?></strong><small><?= e(time_ago((string) $r['created_at'])) ?></small></span>
                </figcaption>
              </figure>
            <?php endforeach; ?>
          </div>
        </section>
      <?php endif; ?>
    </article>

    <aside class="detail-side">
      <div class="glass side-card">
        <h3 class="side-title">Plan this trip</h3>
        <p>Our Kigali team can build a private itinerary around <?= e($dest['name']) ?> with permits, transfers and lodges included.</p>
        <a class="btn btn-primary btn-block" href="<?= e(url('/contact')) ?>">Get a free quote</a>
        <a class="btn btn-ghost btn-block" href="<?= e(url('/tours')) ?>">See all tours</a>
      </div>
      <?php if ($others): ?>
        <div class="glass side-card">
          <h3 class="side-title">More destinations</h3>
          <ul class="side-list">
            <?php foreach ($others as $o): ?>
              <li><a href="<?= e(url('/destinations/' . $o['slug'])) ?>">
                <img src="<?= e(img_url($o['hero_image'])) ?>" alt="" loading="lazy"><span><?= e($o['name']) ?></span>
              </a></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>
    </aside>
  </div>
</section>
