<?php partial('page-header', [
    'tag' => 'Where to go',
    'title' => 'Destinations in <span class="accent">Rwanda</span>',
    'sub' => 'National parks, volcanic peaks, crater lakes and the capital - every region with its own rhythm.',
    'image' => 'assets/img/dest-hills.jpg',
]); ?>
<section class="section">
  <div class="container">
    <form class="filter-bar glass" method="get" action="<?= e(url('/destinations')) ?>">
      <label class="sr-only" for="region">Filter by region</label>
      <select id="region" name="region" class="input-select">
        <option value="">All regions</option>
        <?php foreach (($regions ?? []) as $r): ?>
          <option value="<?= e($r) ?>"<?= ($filter ?? '') === $r ? ' selected' : '' ?>><?= e($r) ?></option>
        <?php endforeach; ?>
      </select>
      <button class="btn btn-primary" type="submit"><?= icon('filter', 'i-sm', 16) ?> Filter</button>
      <?php if (!empty($filter)): ?>
        <a class="link-secondary" href="<?= e(url('/destinations')) ?>">Clear</a>
      <?php endif; ?>
      <span class="filter-count"><?= count($destinations) ?> destination<?= count($destinations) === 1 ? '' : 's' ?></span>
    </form>

    <?php if (!$destinations): ?>
      <p class="empty-state">No destinations match this filter.</p>
    <?php else: ?>
      <div class="grid grid-cards">
        <?php foreach ($destinations as $d): ?>
          <a class="dest-card glass" href="<?= e(url('/destinations/' . $d['slug'])) ?>">
            <span class="card-media">
              <img src="<?= e(img_url($d['hero_image'])) ?>" alt="<?= e($d['name']) ?>" loading="lazy" decoding="async">
            </span>
            <span class="card-body">
              <span class="card-kicker"><?= e($d['region']) ?><?= (int) $d['is_featured'] ? ' &middot; Featured' : '' ?></span>
              <strong class="card-title"><?= e($d['name']) ?></strong>
              <span class="card-text"><?= e(excerpt((string) ($d['short_description'] ?: $d['description']), 96)) ?></span>
              <span class="card-cta">Discover <?= icon('arrow-right', 'i-sm', 15) ?></span>
            </span>
          </a>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</section>
