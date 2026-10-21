<?php partial('page-header', [
    'tag' => 'Where to sleep',
    'title' => 'Hotels & <span class="accent">Lodges</span>',
    'sub' => 'Beachfront hotels, volcano lodges and design stays - handpicked and bookable online.',
    'image' => 'assets/img/gallery-lodge.jpg',
]);
?>
<section class="section">
  <div class="container">
    <form class="filter-bar glass" method="get" action="<?= e(url('/hotels')) ?>">
      <label class="sr-only" for="city">City</label>
      <select id="city" name="city" class="input-select">
        <option value="">All cities</option>
        <?php foreach (($cities ?? []) as $c): ?>
          <option value="<?= e($c) ?>"<?= ($filter ?? '') === $c ? ' selected' : '' ?>><?= e($c) ?></option>
        <?php endforeach; ?>
      </select>
      <label class="sr-only" for="max">Max price per night</label>
      <input id="max" class="input-select" type="number" name="max" min="0" step="10"
             placeholder="Max price per night" value="<?= e($max ?? '') ?>">
      <button class="btn btn-primary" type="submit"><?= icon('filter', 'i-sm', 16) ?> Filter</button>
      <?php if (!empty($filter) || !empty($max)): ?>
        <a class="link-secondary" href="<?= e(url('/hotels')) ?>">Clear</a>
      <?php endif; ?>
      <span class="filter-count"><?= count($hotels) ?> stay<?= count($hotels) === 1 ? '' : 's' ?></span>
    </form>

    <?php if (!$hotels): ?>
      <p class="empty-state">No hotels match those filters.</p>
    <?php else: ?>
      <div class="grid grid-cards">
        <?php foreach ($hotels as $h): ?>
          <a class="tour-card glass" href="<?= e(url('/hotels/' . $h['slug'])) ?>">
            <span class="card-media">
              <img src="<?= e(img_url($h['cover_image'])) ?>" alt="<?= e($h['name']) ?>" loading="lazy" decoding="async">
              <span class="price-badge"><?= e(money($h['base_price'], $h['currency'])) ?><small>/night</small></span>
            </span>
            <span class="card-body">
              <span class="card-kicker"><?= e(ucfirst($h['type'])) ?> &middot; <?= e($h['city']) ?></span>
              <strong class="card-title"><?= e($h['name']) ?></strong>
              <span class="card-text"><?= e(excerpt((string) $h['description'], 90)) ?></span>
              <span class="card-meta">
                <span class="stars"><?= icon('star', 'i-xs', 14) ?> <?= e(number_format((float) $h['rating'], 1)) ?></span>
                <span><?= str_repeat('★', (int) $h['stars']) ?></span>
              </span>
              <span class="card-cta">View hotel <?= icon('arrow-right', 'i-sm', 15) ?></span>
            </span>
          </a>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</section>
