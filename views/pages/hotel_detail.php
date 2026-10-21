<?php
partial('page-header', [
    'tag' => $hotel['city'] . ' &middot; ' . ucfirst($hotel['type']),
    'title' => e($hotel['name']),
    'sub' => excerpt((string) $hotel['description'], 170),
    'image' => $hotel['cover_image'],
]);
$amenities = jarr($hotel['amenities'] ?? []);
?>
<section class="section">
  <div class="container detail-layout">
    <article class="detail-main">
      <div class="fact-row glass">
        <div class="fact"><span class="fact-k">Rating</span><span class="fact-v"><?= icon('star', 'i-xs', 14) ?> <?= e(number_format((float) $hotel['rating'], 1)) ?></span></div>
        <div class="fact"><span class="fact-k">Stars</span><span class="fact-v"><?= str_repeat('★', (int) $hotel['stars']) ?></span></div>
        <div class="fact"><span class="fact-k">Check-in</span><span class="fact-v"><?= e($hotel['check_in_time'] ?? '14:00') ?></span></div>
        <div class="fact"><span class="fact-k">Check-out</span><span class="fact-v"><?= e($hotel['check_out_time'] ?? '11:00') ?></span></div>
      </div>

      <div class="rich-text">
        <h2>About this stay</h2>
        <?= nl2br(e((string) $hotel['description'])) ?>
      </div>

      <?php if ($amenities): ?>
        <section class="subsection">
          <h2 class="subsection-title">Amenities</h2>
          <ul class="amenity-grid">
            <?php foreach ($amenities as $a): ?>
              <li><?= icon('check', 'i-xs', 15) ?> <?= e($a) ?></li>
            <?php endforeach; ?>
          </ul>
        </section>
      <?php endif; ?>

      <?php if ($rooms): ?>
        <section class="subsection">
          <h2 class="subsection-title">Rooms & rates</h2>
          <div class="room-list">
            <?php foreach ($rooms as $r): ?>
              <div class="room-row glass">
                <div class="room-info">
                  <strong><?= e($r['name']) ?></strong>
                  <span><?= icon('users', 'i-xs', 14) ?> <?= e($r['capacity'] ?? '2') ?> guests
                    &middot; <?= e($r['beds'] ?? 'Double bed') ?></span>
                </div>
                <div class="room-price"><?= e(money($r['price'], $hotel['currency'])) ?><small>/night</small></div>
                <?php if (auth_user()): ?>
                  <button class="btn btn-primary btn-sm" type="button" data-book-hotel="<?= (int) $hotel['id'] ?>"
                          data-book-room="<?= (int) $r['id'] ?>"
                          data-book-title="<?= e($hotel['name'] . ' - ' . $r['name']) ?>"
                          data-book-price="<?= e((string) $r['price']) ?>" data-book-currency="<?= e($hotel['currency']) ?>">
                    Book
                  </button>
                <?php else: ?>
                  <a class="btn btn-primary btn-sm" href="<?= e(url('/login')) ?>?next=<?= e('/hotels/' . $hotel['slug']) ?>">Login to book</a>
                <?php endif; ?>
              </div>
            <?php endforeach; ?>
          </div>
        </section>
      <?php endif; ?>

      <?php if ($reviews): ?>
        <section class="subsection">
          <h2 class="subsection-title">Guest reviews</h2>
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
          <h2 class="subsection-title">Similar stays</h2>
          <div class="grid grid-3">
            <?php foreach ($related as $r): ?>
              <a class="tour-card glass" href="<?= e(url('/hotels/' . $r['slug'])) ?>">
                <span class="card-media"><img src="<?= e(img_url($r['cover_image'])) ?>" alt="<?= e($r['name']) ?>" loading="lazy"></span>
                <span class="card-body">
                  <span class="card-kicker"><?= e($r['city']) ?></span>
                  <strong class="card-title"><?= e($r['name']) ?></strong>
                  <span class="card-cta">View hotel <?= icon('arrow-right', 'i-sm', 15) ?></span>
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
          <span><?= e(money($hotel['base_price'], $hotel['currency'])) ?></span>
          <small>per night</small>
        </div>
        <p class="side-note"><?= icon('map-pin', 'i-xs', 15) ?> <?= e($hotel['address'] ?? $hotel['city']) ?></p>
        <button class="btn btn-primary btn-block" type="button"
                <?= auth_user() ? 'data-book-hotel="' . (int) $hotel['id'] . '" data-book-title="' . e($hotel['name']) . '" data-book-price="' . e((string) $hotel['base_price']) . '" data-book-currency="' . e($hotel['currency']) . '"'
                                : 'data-goto-login="' . e('/hotels/' . $hotel['slug']) . '"' ?>>
          <?= icon('calendar', 'i-sm', 16) ?> Book a room
        </button>
        <a class="btn btn-ghost btn-block" href="<?= e(url('/contact')) ?>">Contact property</a>
        <?php $favOn = is_favorited('hotel', (int) $hotel['id']); ?>
        <button class="fav-btn btn btn-ghost btn-block<?= $favOn ? ' is-active' : '' ?>" type="button"
                data-fav-type="hotel" data-fav-id="<?= (int) $hotel['id'] ?>"
                data-label="hotel" aria-pressed="<?= $favOn ? 'true' : 'false' ?>">
          <?= icon('heart', 'i-sm', 16) ?> <span><?= $favOn ? 'Saved to favourites' : 'Save to favourites' ?></span>
        </button>
      </div>
      <?php if (!empty($hotel['destination_slug'])): ?>
        <div class="glass side-card">
          <h3 class="side-title">Location</h3>
          <p><?= icon('map-pin', 'i-sm', 16) ?> <a href="<?= e(url('/destinations/' . $hotel['destination_slug'])) ?>"><?= e($hotel['destination_name']) ?></a></p>
        </div>
      <?php endif; ?>
    </aside>
  </div>
</section>

<?php if (auth_user()): ?>
<div class="modal" id="booking-modal" hidden role="dialog" aria-modal="true" aria-labelledby="booking-title">
  <div class="modal-backdrop" data-modal-close></div>
  <form class="modal-dialog form-modal" method="post" action="<?= e(url('/booking/create')) ?>">
    <?= csrf_field() ?>
    <input type="hidden" name="hotel_id" id="bk-hotel-id" value="<?= (int) $hotel['id'] ?>">
    <input type="hidden" name="room_id" id="bk-room-id" value="">
    <div class="modal-head">
      <h2 class="modal-title" id="booking-title">Book <?= e($hotel['name']) ?></h2>
      <button class="icon-btn" type="button" data-modal-close aria-label="Close"><?= icon('x', '', 22) ?></button>
    </div>
    <div class="modal-body">
      <div class="form-row">
        <label class="field"><span>Check-in</span><input type="date" name="checkin" min="<?= date('Y-m-d', strtotime('+1 day')) ?>" required></label>
        <label class="field"><span>Check-out</span><input type="date" name="checkout" min="<?= date('Y-m-d', strtotime('+2 day')) ?>" required></label>
      </div>
      <div class="form-row">
        <label class="field"><span>Rooms</span><input type="number" name="rooms" min="1" max="5" value="1" required></label>
        <label class="field"><span>Guests</span><input type="number" name="guests" min="1" max="20" value="2" required></label>
      </div>
      <label class="field"><span>Notes (optional)</span><textarea name="notes" rows="3" placeholder="Late arrival, extra bed..."></textarea></label>
      <p class="form-note">We confirm availability with the property within 24 hours - no card charged now.</p>
    </div>
    <div class="modal-foot">
      <button class="btn btn-ghost" type="button" data-modal-close>Cancel</button>
      <button class="btn btn-primary" type="submit"><?= icon('check', 'i-sm', 16) ?> Request booking</button>
    </div>
  </form>
</div>
<?php endif; ?>
