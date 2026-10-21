<?php partial('page-header', [
    'tag' => 'Book a trip',
    'title' => 'Tours & <span class="accent">Safaris</span>',
    'sub' => 'Gorilla permits, Big Five drives, city walks and multi-day itineraries across Rwanda.',
    'image' => 'assets/img/dest-safari.jpg',
]);
?>
<section class="section">
  <div class="container">
    <form class="filter-bar glass" method="get" action="<?= e(url('/tours')) ?>">
      <label class="sr-only" for="category">Category</label>
      <select id="category" name="category" class="input-select">
        <option value="">All categories</option>
        <?php foreach (($categories ?? []) as $c): ?>
          <option value="<?= e($c) ?>"<?= ($filter ?? '') === $c ? ' selected' : '' ?>><?= e($c) ?></option>
        <?php endforeach; ?>
      </select>
      <label class="sr-only" for="max">Max price (USD)</label>
      <input id="max" class="input-select" type="number" name="max" min="0" step="10"
             placeholder="Max price $" value="<?= e($max ?? '') ?>">
      <button class="btn btn-primary" type="submit"><?= icon('filter', 'i-sm', 16) ?> Filter</button>
      <?php if (!empty($filter) || !empty($max)): ?>
        <a class="link-secondary" href="<?= e(url('/tours')) ?>">Clear</a>
      <?php endif; ?>
      <span class="filter-count"><?= count($tours) ?> tour<?= count($tours) === 1 ? '' : 's' ?></span>
    </form>

    <?php if (!$tours): ?>
      <p class="empty-state">No tours match those filters.</p>
    <?php else: ?>
      <div class="grid grid-cards">
        <?php foreach ($tours as $t): ?>
          <a class="tour-card glass" href="<?= e(url('/tours/' . $t['slug'])) ?>">
            <span class="card-media">
              <img src="<?= e(img_url($t['cover_image'])) ?>" alt="<?= e($t['title']) ?>" loading="lazy" decoding="async">
              <span class="price-badge"><?= e(money($t['price'], $t['price'] > 5000 ? 'RWF' : 'USD')) ?></span>
            </span>
            <span class="card-body">
              <span class="card-kicker"><?= e($t['category']) ?> &middot; <?= e($t['duration']) ?></span>
              <strong class="card-title"><?= e($t['title']) ?></strong>
              <span class="card-text"><?= e(excerpt((string) $t['description'], 90)) ?></span>
              <span class="card-meta">
                <span class="stars"><?= icon('star', 'i-xs', 14) ?> <?= e(number_format((float) $t['rating'], 1)) ?></span>
                <span><?= icon('map-pin', 'i-xs', 14) ?> <?= e($t['destination_name'] ?? 'Rwanda') ?></span>
              </span>
              <span class="card-cta">View tour <?= icon('arrow-right', 'i-sm', 15) ?></span>
            </span>
          </a>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</section>
