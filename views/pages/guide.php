<?php partial('page-header', [
    'tag' => 'Know before you go',
    'title' => 'Rwanda travel <span class="accent">guide</span>',
    'sub' => 'Visas, seasons, health, money and getting around - everything we tell our own guests.',
    'image' => 'assets/img/dest-hills.jpg',
]);
$sections = [
    ['When to visit', 'calendar', [
        'Dry seasons (Jun-Sep, Dec-Feb) are best for gorilla trekking and safari - trails are firm and views clear.',
        'Green seasons (Mar-May, Oct-Nov) bring fewer visitors, lush hills and lower lodge rates.',
        'Akagera game viewing is strongest in the drier months around waterholes.',
    ]],
    ['Visas & entry', 'file-text', [
        'Most nationalities get visa on arrival or can apply for the East Africa Tourist Visa (Rwanda, Uganda, Kenya).',
        'Passport must be valid 6+ months with two blank pages.',
        'Yellow fever vaccination certificate is required if arriving from an endemic country.',
    ]],
    ['Health & safety', 'shield', [
        'Anti-malarial prophylaxis recommended for Akagera and lake shores; Kigali and the volcanoes are low risk.',
        'Gorilla permits require you to be well - trekking with illness is not allowed.',
        'Kigali is one of Africa\'s safest capitals; normal city awareness applies.',
    ]],
    ['Money & costs', 'dollar-sign', [
        'Currency is the Rwandan Franc (RWF); USD widely accepted for permits and lodges.',
        'Cards work in hotels and larger restaurants; carry cash for markets and matatus.',
        'Tipping 10% for guides and porters is appreciated, not obligatory.',
    ]],
    ['Getting around', 'compass', [
        'Domestic flights connect Kigali with Rusizi (Kivu) and Akagera in under an hour.',
        'Mototaxis (Boda) and app taxis cover Kigali; intercity buses are cheap and reliable.',
        '4x4 transfer is the norm for park roads - we include this in every itinerary.',
    ]],
    ['What to pack', 'briefcase', [
        'Long sleeves and trousers in neutral colours for trekking, plus rain shell any month.',
        'Sturdy broken-in boots, gardening gloves for vegetation, and 2L water capacity.',
        'Camera with spare batteries; gorilla trekking photography is permitted without flash.',
    ]],
];
?>
<section class="section">
  <div class="container">
    <div class="grid grid-3 guide-grid">
      <?php foreach ($sections as [$title, $ico, $items]): ?>
        <article class="guide-card glass">
          <span class="guide-icon"><?= icon($ico, '', 24) ?></span>
          <h2 class="guide-title"><?= e($title) ?></h2>
          <ul>
            <?php foreach ($items as $it): ?><li><?= e($it) ?></li><?php endforeach; ?>
          </ul>
        </article>
      <?php endforeach; ?>
    </div>

    <div class="cta-inner glass guide-cta">
      <div>
        <p class="tagline">Still unsure?</p>
        <h2 class="section-title">Ask us <span class="accent">anything</span></h2>
        <p class="cta-text">Send your dates and we will answer with a realistic plan and price.</p>
      </div>
      <div class="cta-actions">
        <a class="btn btn-primary btn-lg" href="<?= e(url('/contact')) ?>">Talk to an expert</a>
        <a class="link-secondary" href="<?= e(url('/blog')) ?>">Read the blog</a>
      </div>
    </div>
  </div>
</section>
