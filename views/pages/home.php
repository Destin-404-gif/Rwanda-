<?php
/** Homepage: cinematic hero + destination strip + sections. */
$slides = $slides ?: [];
$slideCount = max(1, count($slides));
$hasPromo = is_file(APP_ROOT . '/assets/video/promo.mp4');
?>
<section class="hero" id="hero" data-slider data-slide-count="<?= (int) $slideCount ?>" aria-roledescription="carousel" aria-label="Rwanda highlights">
  <div class="hero-media">
    <?php foreach ($slides as $i => $s): ?>
      <div class="hero-slide<?= $i === 0 ? ' is-active' : '' ?>" data-slide="<?= (int) $i ?>" aria-hidden="<?= $i === 0 ? 'false' : 'true' ?>">
        <img src="<?= e(img_url($s['image'])) ?>"
             alt="<?= e($s['title_line1'] . ' ' . $s['title_line2'] . ' - ' . ($s['subtitle'] ?? '')) ?>"
             <?= $i === 0 ? 'fetchpriority="high"' : 'loading="lazy"' ?> decoding="async">
      </div>
    <?php endforeach; ?>
  </div>
  <div class="hero-overlay" aria-hidden="true"></div>

  <div class="hero-inner container">
    <div class="hero-copy">
      <p class="tagline">The Land of a Thousand Hills</p>
      <h1 class="hero-title"><span>Visit</span><span class="accent">Rwanda</span></h1>
      <p class="hero-sub">Gorilla trekking, Big Five safaris, rainforest canopy walks and crater-lake islands - planned end to end by our Kigali team.</p>
      <div class="hero-actions">
        <a class="btn btn-primary btn-lg" href="<?= e(url('/experiences')) ?>">Explore now <?= icon('arrow-right', 'i-sm', 18) ?></a>
        <a class="link-secondary" href="<?= e(url('/tours')) ?>">View tours</a>
      </div>
    </div>
  </div>

  <div class="hero-dots" role="tablist" aria-label="Choose a slide">
    <?php foreach ($slides as $i => $s): ?>
      <button type="button" class="hero-dot<?= $i === 0 ? ' is-active' : '' ?>" role="tab"
              aria-selected="<?= $i === 0 ? 'true' : 'false' ?>"
              aria-label="Slide <?= (int) $i + 1 ?>: <?= e($s['card_title'] ?? $s['title_line2']) ?>"
              data-goto="<?= (int) $i ?>"></button>
    <?php endforeach; ?>
  </div>

  <div class="hero-counter" aria-hidden="true">
    <span class="hero-counter-current" data-counter-current>01</span>
    <span class="hero-counter-line"></span>
    <span class="hero-counter-total">/ <?= str_pad((string) $slideCount, 2, '0', STR_PAD_LEFT) ?></span>
  </div>

  <div class="hero-strip-wrap">
    <div class="container">
      <div class="hero-strip" role="list">
        <?php foreach ($slides as $i => $s): ?>
          <button type="button" class="strip-card glass<?= $i === 0 ? ' is-active' : '' ?>" role="listitem"
                  data-goto="<?= (int) $i ?>" aria-label="Show slide <?= (int) $i + 1 ?>: <?= e($s['card_title'] ?? '') ?>">
            <span class="strip-thumb">
              <img src="<?= e(img_url($s['card_thumb'] ?: $s['image'])) ?>" alt="" loading="lazy" decoding="async">
            </span>
            <span class="strip-text">
              <strong><?= e($s['card_title'] ?? $s['title_line2']) ?></strong>
              <small><?= e($s['card_desc'] ?? $s['subtitle'] ?? '') ?></small>
            </span>
          </button>
        <?php endforeach; ?>

        <button type="button" class="strip-card glass strip-promo" data-video-open role="listitem"
                aria-label="Watch our promo video">
          <span class="strip-thumb strip-thumb-play">
            <span class="play-btn" aria-hidden="true"><?= icon('play', '', 20) ?></span>
          </span>
          <span class="strip-text">
            <strong>Watch our promo</strong>
            <small><?= $hasPromo ? '1:30 min' : 'Coming soon' ?></small>
          </span>
        </button>
      </div>
    </div>
  </div>
</section>

<!-- =================================================== Featured destinations -->
<section class="section" id="destinations">
  <div class="container">
    <div class="section-head">
      <div>
        <p class="tagline">Where to go</p>
        <h2 class="section-title">Featured <span class="accent">Destinations</span></h2>
      </div>
      <a class="link-secondary" href="<?= e(url('/destinations')) ?>">All destinations <?= icon('arrow-right', 'i-sm', 16) ?></a>
    </div>

    <div class="grid grid-cards">
      <?php foreach ($featured as $d): ?>
        <a class="dest-card glass" href="<?= e(url('/destinations/' . $d['slug'])) ?>">
          <span class="card-media">
            <img src="<?= e(img_url($d['hero_image'])) ?>" alt="<?= e($d['name']) ?>" loading="lazy" decoding="async">
          </span>
          <span class="card-body">
            <span class="card-kicker"><?= e($d['region']) ?></span>
            <strong class="card-title"><?= e($d['name']) ?></strong>
            <span class="card-text"><?= e(excerpt((string) ($d['short_description'] ?: $d['description']), 84)) ?></span>
            <span class="card-cta">Discover <?= icon('arrow-right', 'i-sm', 15) ?></span>
          </span>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- =================================================== Experiences -->
<section class="section section-alt">
  <div class="container">
    <div class="section-head">
      <div>
        <p class="tagline">What to do</p>
        <h2 class="section-title">Signature <span class="accent">Experiences</span></h2>
      </div>
      <a class="link-secondary" href="<?= e(url('/experiences')) ?>">All experiences <?= icon('arrow-right', 'i-sm', 16) ?></a>
    </div>

    <div class="grid grid-3">
      <?php
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
      $groups = [];
      foreach ($tours as $t) $groups[$t['category']][] = $t;
      $i = 0;
      foreach ($groups as $cat => $list): $i++; ?>
        <a class="exp-card glass" href="<?= e(url('/tours?category=' . urlencode($cat))) ?>">
          <span class="card-media">
            <img src="<?= e(asset($expImages[$cat] ?? 'assets/img/dest-hills.jpg')) ?>" alt="<?= e($cat) ?> experiences" loading="lazy" decoding="async">
          </span>
          <span class="card-body">
            <span class="card-kicker"><?= count($list) ?> tour<?= count($list) > 1 ? 's' : '' ?></span>
            <strong class="card-title"><?= e($cat) ?></strong>
            <span class="card-text"><?= e($expBlurb[$cat] ?? 'Explore ' . strtolower($cat) . ' experiences across Rwanda.') ?></span>
            <span class="card-cta">See tours <?= icon('arrow-right', 'i-sm', 15) ?></span>
          </span>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- =================================================== Popular tours -->
<section class="section" id="tours">
  <div class="container">
    <div class="section-head">
      <div>
        <p class="tagline">Best sellers</p>
        <h2 class="section-title">Popular <span class="accent">Tours</span></h2>
      </div>
      <a class="link-secondary" href="<?= e(url('/tours')) ?>">All tours <?= icon('arrow-right', 'i-sm', 16) ?></a>
    </div>
    <div class="grid grid-cards">
      <?php foreach (array_slice($tours, 0, 6) as $t): ?>
        <a class="tour-card glass" href="<?= e(url('/tours/' . $t['slug'])) ?>">
          <span class="card-media">
            <img src="<?= e(img_url($t['cover_image'])) ?>" alt="<?= e($t['title']) ?>" loading="lazy" decoding="async">
            <span class="price-badge"><?= e(money($t['price'], $t['price'] > 5000 ? 'RWF' : 'USD')) ?></span>
          </span>
          <span class="card-body">
            <span class="card-kicker"><?= e($t['category']) ?> &middot; <?= e($t['duration']) ?></span>
            <strong class="card-title"><?= e($t['title']) ?></strong>
            <span class="card-meta">
              <span class="stars"><?= icon('star', 'i-xs', 14) ?> <?= e(number_format((float) $t['rating'], 1)) ?></span>
              <span><?= icon('users', 'i-xs', 14) ?> <?= e($t['group_size'] ?: 'All ages') ?></span>
            </span>
            <span class="card-cta">View tour <?= icon('arrow-right', 'i-sm', 15) ?></span>
          </span>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- =================================================== Hotels -->
<section class="section section-alt" id="hotels">
  <div class="container">
    <div class="section-head">
      <div>
        <p class="tagline">Where to sleep</p>
        <h2 class="section-title">Handpicked <span class="accent">Hotels</span></h2>
      </div>
      <a class="link-secondary" href="<?= e(url('/hotels')) ?>">All hotels <?= icon('arrow-right', 'i-sm', 16) ?></a>
    </div>
    <div class="grid grid-cards">
      <?php foreach (array_slice($hotels, 0, 6) as $h): ?>
        <a class="tour-card glass" href="<?= e(url('/hotels/' . $h['slug'])) ?>">
          <span class="card-media">
            <img src="<?= e(img_url($h['cover_image'])) ?>" alt="<?= e($h['name']) ?>" loading="lazy" decoding="async">
            <span class="price-badge"><?= e(money($h['base_price'], $h['currency'])) ?><small>/night</small></span>
          </span>
          <span class="card-body">
            <span class="card-kicker"><?= e(ucfirst($h['type'])) ?> &middot; <?= e($h['city']) ?></span>
            <strong class="card-title"><?= e($h['name']) ?></strong>
            <span class="card-meta">
              <span class="stars"><?= icon('star', 'i-xs', 14) ?> <?= e(number_format((float) $h['rating'], 1)) ?></span>
              <span><?= str_repeat('★', (int) $h['stars']) ?></span>
            </span>
            <span class="card-cta">View hotel <?= icon('arrow-right', 'i-sm', 15) ?></span>
          </span>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- =================================================== Stats -->
<section class="section stats-band">
  <div class="container stats-row">
    <div class="stat"><span class="stat-num accent" data-count="<?= (int) $stats['destinations'] ?>">0</span><span class="stat-label">Destinations</span></div>
    <div class="stat"><span class="stat-num accent" data-count="<?= (int) $stats['tours'] ?>">0</span><span class="stat-label">Tours & safaris</span></div>
    <div class="stat"><span class="stat-num accent" data-count="<?= (int) $stats['hotels'] ?>">0</span><span class="stat-label">Hotels & lodges</span></div>
    <div class="stat"><span class="stat-num accent" data-count="24">0</span><span class="stat-label">Local experts</span></div>
  </div>
</section>

<!-- =================================================== Gallery -->
<section class="section" id="gallery">
  <div class="container">
    <div class="section-head">
      <div>
        <p class="tagline">Seen in Rwanda</p>
        <h2 class="section-title">From the <span class="accent">Gallery</span></h2>
      </div>
      <a class="link-secondary" href="<?= e(url('/gallery')) ?>">Open gallery <?= icon('arrow-right', 'i-sm', 16) ?></a>
    </div>
    <div class="gallery-grid">
      <?php foreach ($gallery as $g): ?>
        <button type="button" class="gallery-item" data-lightbox="<?= e(img_url($g['url'])) ?>" data-caption="<?= e($g['title'] ?? '') ?>">
          <img src="<?= e(img_url($g['url'])) ?>" alt="<?= e($g['title'] ?? 'Rwanda photo') ?>" loading="lazy" decoding="async">
          <span class="gallery-overlay"><span><?= e($g['title'] ?? '') ?></span></span>
        </button>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- =================================================== Testimonials -->
<section class="section section-alt" id="testimonials">
  <div class="container">
    <div class="section-head">
      <div>
        <p class="tagline">Guest stories</p>
        <h2 class="section-title">What travellers <span class="accent">say</span></h2>
      </div>
    </div>
    <div class="grid grid-4">
      <?php foreach ($testimonials as $t): ?>
        <figure class="quote-card glass">
          <div class="quote-stars" aria-label="<?= (int) $t['rating'] ?> out of 5 stars">
            <?= str_repeat('<span aria-hidden="true">★</span>', (int) $t['rating']) ?>
          </div>
          <blockquote><?= e($t['text']) ?></blockquote>
          <figcaption>
            <span class="avatar avatar-sm"><?= e(initials($t['name'])) ?></span>
            <span><strong><?= e($t['name']) ?></strong><small><?= e($t['country'] ?? '') ?></small></span>
          </figcaption>
        </figure>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- =================================================== CTA -->
<section class="section cta-band">
  <div class="container cta-inner glass">
    <div>
      <p class="tagline">Plan your trip</p>
      <h2 class="section-title">Ready to explore <span class="accent">Rwanda?</span></h2>
      <p class="cta-text">Tell us your dates and we will build the itinerary - permits, lodges, guides and transfers included.</p>
    </div>
    <div class="cta-actions">
      <a class="btn btn-primary btn-lg" href="<?= e(url('/contact')) ?>">Get a free quote <?= icon('arrow-right', 'i-sm', 18) ?></a>
      <a class="link-secondary" href="<?= e(url('/guide')) ?>">Read the travel guide</a>
    </div>
  </div>
</section>
