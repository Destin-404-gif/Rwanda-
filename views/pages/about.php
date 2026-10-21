<?php partial('page-header', [
    'tag' => 'Who we are',
    'title' => 'About <span class="accent">Rwanda Explorer</span>',
    'sub' => 'A Kigali-based travel company building honest, well-paced journeys across the Land of a Thousand Hills.',
    'image' => 'assets/img/gallery-coffee.jpg',
]);
?>
<section class="section">
  <div class="container split">
    <div class="split-media">
      <img class="rounded-lg" src="<?= e(asset('assets/img/gallery-city.jpg')) ?>" alt="Kigali city streets" loading="lazy">
      <img class="rounded-lg split-media-2" src="<?= e(asset('assets/img/dest-gorilla.jpg')) ?>" alt="Mountain gorilla in Volcanoes National Park" loading="lazy">
    </div>
    <div class="split-copy rich-text">
      <p class="tagline">Our story</p>
      <h2 class="section-title">Travel that stays <span class="accent">with you</span></h2>
      <p>Rwanda Explorer was founded in Kigali by guides, lodges hosts and travel planners who live here. We handle gorilla permits, park fees, transfers and accommodation end to end, so your only job is to show up ready.</p>
      <p>Small groups, fair wages for local guides and carbon-aware routing are not marketing lines for us - they are how every itinerary is built.</p>
      <ul class="check-list">
        <li><?= icon('check', 'i-xs', 16) ?> Licensed Rwanda tour operator with vetted guides</li>
        <li><?= icon('check', 'i-xs', 16) ?> Transparent pricing - no hidden park or permit fees</li>
        <li><?= icon('check', 'i-xs', 16) ?> 24/7 WhatsApp support while you travel</li>
        <li><?= icon('check', 'i-xs', 16) ?> Free cancellation up to 14 days before departure</li>
      </ul>
      <a class="btn btn-primary" href="<?= e(url('/contact')) ?>">Plan a trip with us <?= icon('arrow-right', 'i-sm', 16) ?></a>
    </div>
  </div>
</section>

<section class="section stats-band">
  <div class="container stats-row">
    <div class="stat"><span class="stat-num accent" data-count="<?= (int) ($stats['destinations'] ?? 0) ?>">0</span><span class="stat-label">Destinations</span></div>
    <div class="stat"><span class="stat-num accent" data-count="<?= (int) ($stats['tours'] ?? 0) ?>">0</span><span class="stat-label">Tours & safaris</span></div>
    <div class="stat"><span class="stat-num accent" data-count="<?= (int) ($stats['hotels'] ?? 0) ?>">0</span><span class="stat-label">Hotels & lodges</span></div>
    <div class="stat"><span class="stat-num accent" data-count="<?= (int) ($stats['bookings'] ?? 0) ?>">0</span><span class="stat-label">Trips organised</span></div>
  </div>
</section>

<section class="section section-alt">
  <div class="container">
    <div class="section-head">
      <div>
        <p class="tagline">The people</p>
        <h2 class="section-title">Meet the <span class="accent">team</span></h2>
      </div>
    </div>
    <div class="grid grid-4">
      <?php
      $team = [
          ['Aline Uwase', 'Founder & trip designer', 'Ten years of building itineraries from Kigali to Rusizi.', 'AU'],
          ['Eric Mugisha', 'Head guide', 'Silver-back gorilla trekking specialist and birder.', 'EM'],
          ['Diane Ingabire', 'Lodges & stays', 'Negotiates the best rooms at the best rates.', 'DI'],
          ['Patrick Niyonzima', 'Operations', 'Permits, transfers and 24/7 on-trip support.', 'PN'],
      ];
      foreach ($team as [$name, $role, $bio, $ini]): ?>
        <figure class="team-card glass">
          <span class="avatar avatar-lg"><?= e($ini) ?></span>
          <figcaption>
            <strong><?= e($name) ?></strong>
            <span class="team-role"><?= e($role) ?></span>
            <p><?= e($bio) ?></p>
          </figcaption>
        </figure>
      <?php endforeach; ?>
    </div>
  </div>
</section>
