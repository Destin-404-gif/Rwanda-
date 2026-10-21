<?php partial('page-header', [
    'tag' => 'Find it fast',
    'title' => $q ? 'Results for <span class="accent">"' . e($q) . '"</span>' : 'Search <span class="accent">Rwanda</span>',
    'sub' => $q ? $total . ' result' . ($total === 1 ? '' : 's') . ' across destinations, tours, hotels and articles.' : 'Search destinations, tours, hotels and travel articles.',
    'image' => 'assets/img/hero-volcanoes.jpg',
]);
$labels = ['destinations' => 'Destinations', 'tours' => 'Tours', 'hotels' => 'Hotels', 'blog' => 'Blog'];
?>
<section class="section">
  <div class="container">
    <form class="filter-bar glass" method="get" action="<?= e(url('/search')) ?>" role="search">
      <label class="sr-only" for="q">Search</label>
      <input id="q" class="input-select input-grow" type="search" name="q" value="<?= e($q) ?>"
             placeholder="Search destinations, tours, hotels..." minlength="2" maxlength="80" required>
      <label class="sr-only" for="type">Type</label>
      <select id="type" name="type" class="input-select">
        <option value="">Everything</option>
        <option value="destinations"<?= $type === 'destinations' ? ' selected' : '' ?>>Destinations</option>
        <option value="tours"<?= $type === 'tours' ? ' selected' : '' ?>>Tours</option>
        <option value="hotels"<?= $type === 'hotels' ? ' selected' : '' ?>>Hotels</option>
      </select>
      <button class="btn btn-primary" type="submit"><?= icon('search', 'i-sm', 16) ?> Search</button>
    </form>

    <?php if (!$q): ?>
      <p class="empty-state">Try "gorilla", "Kivu" or "safari".</p>
    <?php elseif (!$total): ?>
      <p class="empty-state">No matches for <strong><?= e($q) ?></strong>. Try a different keyword or <a href="<?= e(url('/contact')) ?>">ask our team</a>.</p>
    <?php else: ?>
      <?php $prefixes = ['destinations' => '/destinations/', 'tours' => '/tours/', 'hotels' => '/hotels/', 'blog' => '/blog/']; ?>
      <?php foreach ($labels as $key => $label): if (empty($results[$key])) continue; ?>
        <section class="subsection">
          <h2 class="subsection-title"><?= e($label) ?> <span class="count-pill"><?= count($results[$key]) ?></span></h2>
          <div class="search-list">
            <?php foreach ($results[$key] as $r): ?>
              <a class="search-result glass" href="<?= e(url(($prefixes[$key] ?? '/blog/') . $r['slug'])) ?>">
                <img src="<?= e(img_url($r['image'])) ?>" alt="" loading="lazy">
                <span>
                  <strong><?= e($r['name']) ?></strong>
                  <small><?= e($r['meta']) ?></small>
                </span>
                <?= icon('arrow-right', 'i-sm', 16) ?>
              </a>
            <?php endforeach; ?>
          </div>
        </section>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</section>
