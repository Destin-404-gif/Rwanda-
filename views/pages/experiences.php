<?php partial('page-header', [
    'tag' => 'What to do',
    'title' => 'Curated <span class="accent">Experiences</span>',
    'sub' => 'Wildlife, safari, adventure, water and culture - grouped the way travellers actually browse.',
    'image' => 'assets/img/dest-canopy.jpg',
]);
$expImages = [
    'Wildlife' => 'assets/img/dest-gorilla.jpg',
    'Safari' => 'assets/img/dest-safari.jpg',
    'Adventure' => 'assets/img/dest-canopy.jpg',
    'Water' => 'assets/img/gallery-boat.jpg',
    'Culture' => 'assets/img/gallery-intore.jpg',
];
$expBlurb = [
    'Wildlife' => 'Meet habituated mountain gorillas and golden monkeys in the Virunga volcanoes.',
    'Safari' => 'Track the Big Five across the savannahs and wetlands of Akagera National Park.',
    'Adventure' => 'Canopy walkways, volcano climbs and multi-day treks through ancient forest.',
    'Water' => 'Island hopping, kayaking and sunset cruises on Lake Kivu.',
    'Culture' => 'Intore dancers, coffee farms and village visits across the thousand hills.',
];
$order = ['Wildlife', 'Safari', 'Adventure', 'Water', 'Culture'];
?>
<section class="section">
  <div class="container">
    <div class="grid grid-cards">
      <?php foreach ($order as $cat): if (empty($groups[$cat])) continue; $list = $groups[$cat]; ?>
        <a class="dest-card glass" href="<?= e(url('/tours?category=' . urlencode($cat))) ?>">
          <span class="card-media">
            <img src="<?= e(asset($expImages[$cat])) ?>" alt="<?= e($cat) ?> in Rwanda" loading="lazy" decoding="async">
          </span>
          <span class="card-body">
            <span class="card-kicker"><?= count($list) ?> tour<?= count($list) > 1 ? 's' : '' ?></span>
            <strong class="card-title"><?= e($cat) ?></strong>
            <span class="card-text"><?= e($expBlurb[$cat]) ?></span>
            <span class="card-cta">See tours <?= icon('arrow-right', 'i-sm', 15) ?></span>
          </span>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="section section-alt">
  <div class="container">
    <div class="section-head">
      <div>
        <p class="tagline">Go deeper</p>
        <h2 class="section-title">Experiences by <span class="accent">destination</span></h2>
      </div>
    </div>
    <div class="grid grid-3">
      <?php foreach (array_slice($destinations, 0, 6) as $d): ?>
        <a class="exp-card glass" href="<?= e(url('/destinations/' . $d['slug'])) ?>">
          <span class="card-media"><img src="<?= e(img_url($d['hero_image'])) ?>" alt="<?= e($d['name']) ?>" loading="lazy"></span>
          <span class="card-body">
            <span class="card-kicker"><?= e($d['region']) ?></span>
            <strong class="card-title"><?= e($d['name']) ?></strong>
            <span class="card-cta">Explore <?= icon('arrow-right', 'i-sm', 15) ?></span>
          </span>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>
