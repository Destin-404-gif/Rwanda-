<?php
partial('page-header', [
    'tag' => $tour['category'] . ' &middot; ' . $tour['duration'],
    'title' => e($tour['title']),
    'sub' => excerpt((string) $tour['description'], 170),
    'image' => $tour['cover_image'],
]);
$currency = $tour['price'] > 5000 ? 'RWF' : 'USD';
$itin = jarr($tour['itinerary'] ?? []);
?>
<section class="section">
  <div class="container detail-layout">
    <article class="detail-main">
      <div class="fact-row glass">
        <div class="fact"><span class="fact-k">Duration</span><span class="fact-v"><?= e($tour['duration']) ?></span></div>
        <div class="fact"><span class="fact-k">Group size</span><span class="fact-v"><?= e($tour['group_size'] ?: 'Flexible') ?></span></div>
        <div class="fact"><span class="fact-k">Difficulty</span><span class="fact-v"><?= e($tour['difficulty'] ?: 'Moderate') ?></span></div>
        <div class="fact"><span class="fact-k">Rating</span><span class="fact-v"><?= icon('star', 'i-xs', 14) ?> <?= e(number_format((float) $tour['rating'], 1)) ?></span></div>
      </div>

      <div class="rich-text">
        <h2>Tour overview</h2>
        <?= nl2br(e((string) $tour['description'])) ?>
        <?php if ($itin): ?>
          <h3>Day-by-day itinerary</h3>
          <ol class="itinerary">
            <?php foreach ($itin as $i => $day): ?>
              <li>
                <strong>Day <?= (int) $i + 1 ?>: <?= e($day['title'] ?? $day['day'] ?? '') ?></strong>
                <p><?= e($day['text'] ?? $day['description'] ?? '') ?></p>
              </li>
            <?php endforeach; ?>
          </ol>
        <?php endif; ?>
        <?php $incl = jarr($tour['included'] ?? []); if ($incl): ?>
          <h3>What is included</h3>
          <ul class="check-list">
            <?php foreach ($incl as $inc): ?><li><?= icon('check', 'i-xs', 16) ?> <?= e($inc) ?></li><?php endforeach; ?>
          </ul>
        <?php endif; ?>
        <?php $excl = jarr($tour['excluded'] ?? []); if ($excl): ?>
          <h3>What is not included</h3>
          <ul class="cross-list">
            <?php foreach ($excl as $x): ?><li><?= icon('x', 'i-xs', 16) ?> <?= e($x) ?></li><?php endforeach; ?>
          </ul>
        <?php endif; ?>
      </div>

      <?php if ($reviews): ?>
        <section class="subsection">
          <h2 class="subsection-title">Reviews</h2>
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

      <?php if ($related): ?>
        <section class="subsection">
          <h2 class="subsection-title">Similar tours</h2>
          <div class="grid grid-3">
            <?php foreach ($related as $r): ?>
              <a class="tour-card glass" href="<?= e(url('/tours/' . $r['slug'])) ?>">
                <span class="card-media"><img src="<?= e(img_url($r['cover_image'])) ?>" alt="<?= e($r['title']) ?>" loading="lazy"></span>
                <span class="card-body">
                  <span class="card-kicker"><?= e($r['category']) ?> &middot; <?= e($r['duration']) ?></span>
                  <strong class="card-title"><?= e($r['title']) ?></strong>
                  <span class="card-cta">View tour <?= icon('arrow-right', 'i-sm', 15) ?></span>
                </span>
              </a>
            <?php endforeach; ?>
          </div>
        </section>
      <?php endif; ?>
    </article>

    <aside class="detail-side">
      <div class="glass side-card booking-card">
        <div class="booking-price">
          <span><?= e(money($tour['price'], $currency)) ?></span>
          <small>per person</small>
        </div>
        <?php if ($dates): ?>
          <h3 class="side-title">Upcoming departures</h3>
          <ul class="date-list">
            <?php foreach ($dates as $d): ?>
              <li>
                <span><?= icon('calendar', 'i-xs', 15) ?> <?= e(date('d M Y', strtotime($d['start_date']))) ?></span>
                <small><?= e($d['spots_left'] ?? '') ?> spots left</small>
              </li>
            <?php endforeach; ?>
          </ul>
        <?php endif; ?>
        <?php if ($currentUser = auth_user()): ?>
          <button class="btn btn-primary btn-block" type="button" data-book-tour="<?= (int) $tour['id'] ?>"
                  data-book-title="<?= e($tour['title']) ?>" data-book-price="<?= e((string) $tour['price']) ?>"
                  data-book-currency="<?= e($currency) ?>">
            <?= icon('ticket', 'i-sm', 16) ?> Book this tour
          </button>
          <a class="btn btn-ghost btn-block" href="<?= e(url('/contact')) ?>">Ask a question</a>
        <?php else: ?>
          <a class="btn btn-primary btn-block" href="<?= e(url('/login')) ?>?next=<?= e('/tours/' . $tour['slug']) ?>">
            <?= icon('lock', 'i-sm', 16) ?> Login to book
          </a>
          <a class="btn btn-ghost btn-block" href="<?= e(url('/register')) ?>">Create account</a>
        <?php endif; ?>
        <?php $favOn = is_favorited('tour', (int) $tour['id']); ?>
        <button class="fav-btn btn btn-ghost btn-block<?= $favOn ? ' is-active' : '' ?>" type="button"
                data-fav-type="tour" data-fav-id="<?= (int) $tour['id'] ?>"
                data-label="tour" aria-pressed="<?= $favOn ? 'true' : 'false' ?>">
          <?= icon('heart', 'i-sm', 16) ?> <span><?= $favOn ? 'Saved to favourites' : 'Save to favourites' ?></span>
        </button>
      </div>

      <?php if (!empty($tour['destination_slug'])): ?>
        <div class="glass side-card">
          <h3 class="side-title">Location</h3>
          <p><?= icon('map-pin', 'i-sm', 16) ?> <a href="<?= e(url('/destinations/' . $tour['destination_slug'])) ?>"><?= e($tour['destination_name']) ?></a></p>
        </div>
      <?php endif; ?>
    </aside>
  </div>
</section>

<?php if ($currentUser = auth_user()): ?>
<div class="modal" id="booking-modal" hidden role="dialog" aria-modal="true" aria-labelledby="booking-title">
  <div class="modal-backdrop" data-modal-close></div>
  <form class="modal-dialog form-modal" method="post" action="<?= e(url('/booking/create')) ?>">
    <?= csrf_field() ?>
    <input type="hidden" name="tour_id" value="<?= (int) $tour['id'] ?>">
    <div class="modal-head">
      <h2 class="modal-title" id="booking-title">Book <?= e($tour['title']) ?></h2>
      <button class="icon-btn" type="button" data-modal-close aria-label="Close"><?= icon('x', '', 22) ?></button>
    </div>
    <div class="modal-body">
      <div class="form-row">
        <label class="field">
          <span>Travel date</span>
          <input type="date" name="travel_date" min="<?= date('Y-m-d', strtotime('+1 day')) ?>" required>
        </label>
        <label class="field">
          <span>Travellers</span>
          <input type="number" name="guests" min="1" max="20" value="2" required>
        </label>
      </div>
      <label class="field">
        <span>Notes (optional)</span>
        <textarea name="notes" rows="3" placeholder="Dietary needs, hotel pickup, children..."></textarea>
      </label>
      <p class="form-note">Total: <strong><?= e(money($tour['price'], $currency)) ?></strong> x travellers - our team confirms availability within 24 hours.</p>
    </div>
    <div class="modal-foot">
      <button class="btn btn-ghost" type="button" data-modal-close>Cancel</button>
      <button class="btn btn-primary" type="submit"><?= icon('check', 'i-sm', 16) ?> Confirm booking request</button>
    </div>
  </form>
</div>
<?php endif; ?>
