<?php partial('page-header', [
    'tag' => 'Seen in Rwanda',
    'title' => 'Photo <span class="accent">Gallery</span>',
    'sub' => 'Volcanoes, canopy walkways, lake islands, city streets and Intore dancers.',
    'image' => 'assets/img/gallery-city.jpg',
]);
$cats = array_unique(array_filter(['lakes', 'mountains', 'wildlife', 'culture', 'city', 'lodges']));
?>
<section class="section">
  <div class="container">
    <form class="filter-bar glass" method="get" action="<?= e(url('/gallery')) ?>">
      <label class="sr-only" for="category">Category</label>
      <select id="category" name="category" class="input-select">
        <option value="">All categories</option>
        <?php foreach (($categories ?? []) as $c): ?>
          <option value="<?= e($c) ?>"<?= ($filter ?? '') === $c ? ' selected' : '' ?>><?= e(ucfirst($c)) ?></option>
        <?php endforeach; ?>
      </select>
      <button class="btn btn-primary" type="submit"><?= icon('filter', 'i-sm', 16) ?> Filter</button>
      <span class="filter-count"><?= count($items) ?> photo<?= count($items) === 1 ? '' : 's' ?></span>
    </form>

    <?php if (!$items): ?>
      <p class="empty-state">No photos in this category yet.</p>
    <?php else: ?>
      <div class="gallery-grid gallery-page">
        <?php foreach ($items as $g): ?>
          <button type="button" class="gallery-item" data-lightbox="<?= e(img_url($g['url'])) ?>"
                  data-caption="<?= e($g['title'] ?? '') . ($g['category'] ? ' - ' . ucfirst($g['category']) : '') ?>">
            <img src="<?= e(img_url($g['url'])) ?>" alt="<?= e($g['title'] ?? 'Rwanda photo') ?>" loading="lazy" decoding="async">
            <span class="gallery-overlay"><span><?= e($g['title'] ?? '') ?></span></span>
          </button>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</section>
