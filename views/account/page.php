<?php
/** Customer account page with tabs. */
$tabs = [
    'overview' => ['Overview', 'layout-dashboard'],
    'bookings' => ['Bookings', 'ticket'],
    'favorites' => ['Favourites', 'heart'],
    'profile' => ['Profile', 'user'],
];
$statusClass = [
    'pending' => 'badge-pending', 'confirmed' => 'badge-ok', 'completed' => 'badge-done',
    'cancelled' => 'badge-bad', 'refunded' => 'badge-info',
];
?>
<section class="page-hero page-hero-sm">
  <div class="container">
    <p class="tagline">My account</p>
    <h1 class="page-title">Hello, <span class="accent"><?= e(excerpt($user['name'], 24)) ?></span></h1>
    <p class="page-sub"><?= e($user['email']) ?> &middot; member since <?= e(date('M Y', strtotime($user['created_at']))) ?></p>
  </div>
</section>

<section class="section">
  <div class="container account-layout">
    <nav class="account-tabs" role="tablist" aria-label="Account sections">
      <?php foreach ($tabs as $key => [$label, $ico]): ?>
        <a role="tab" aria-selected="<?= $tab === $key ? 'true' : 'false' ?>"
           class="account-tab<?= $tab === $key ? ' is-active' : '' ?>"
           href="<?= e(url('/account?tab=' . $key)) ?>"><?= icon($ico, 'i-sm', 17) ?> <?= e($label) ?></a>
      <?php endforeach; ?>
      <a class="account-tab" href="<?= e(url('/logout')) ?>"><?= icon('log-out', 'i-sm', 17) ?> Logout</a>
    </nav>

    <div class="account-main">
      <?php foreach (flash_pull() as $f): ?>
        <div class="alert alert-<?= e($f['type']) ?>" role="alert">
          <?= icon($f['type'] === 'error' ? 'alert-circle' : 'check-circle', 'i-sm', 18) ?>
          <span><?= e($f['message']) ?></span>
        </div>
      <?php endforeach; ?>

      <!-- ------------------------------------------------ overview -->
      <?php if ($tab === 'overview'): ?>
        <div class="grid grid-4 stat-cards">
          <div class="stat-card glass"><span class="stat-card-num"><?= (int) $stats['bookings'] ?></span><span>Total bookings</span></div>
          <div class="stat-card glass"><span class="stat-card-num"><?= (int) $stats['upcoming'] ?></span><span>Upcoming trips</span></div>
          <div class="stat-card glass"><span class="stat-card-num"><?= (int) $stats['favourites'] ?></span><span>Favourites</span></div>
          <div class="stat-card glass"><span class="stat-card-num"><?= (int) $stats['points'] ?></span><span>Loyalty points</span></div>
        </div>

        <div class="grid grid-2">
          <div class="glass panel">
            <div class="panel-head"><h2 class="panel-title">Next trips</h2>
              <a class="link-secondary" href="<?= e(url('/account?tab=bookings')) ?>">All bookings</a></div>
            <?php $upcoming = array_values(array_filter($bookings, fn($b) => in_array($b['status'], ['pending', 'confirmed'], true))); ?>
            <?php if (!$upcoming): ?>
              <p class="empty-state">No upcoming trips. <a href="<?= e(url('/tours')) ?>">Browse tours</a></p>
            <?php else: foreach (array_slice($upcoming, 0, 3) as $b): ?>
              <div class="mini-booking">
                <span><?= icon($b['booking_type'] === 'tour' ? 'compass' : 'building', 'i-sm', 18) ?></span>
                <div>
                  <strong><?= e($b['tour_title'] ?: $b['hotel_name'] ?: 'Booking #' . $b['id']) ?></strong>
                  <small><?= e($b['travel_date'] ?: ($b['check_in'] . ' → ' . $b['check_out'])) ?> · <?= (int) $b['guests'] ?> guest(s)</small>
                </div>
                <span class="badge <?= $statusClass[$b['status']] ?? 'badge-info' ?>"><?= e($b['status']) ?></span>
              </div>
            <?php endforeach; endif; ?>
          </div>

          <div class="glass panel">
            <div class="panel-head"><h2 class="panel-title">Quick actions</h2></div>
            <div class="quick-actions">
              <a class="btn btn-primary" href="<?= e(url('/tours')) ?>"><?= icon('compass', 'i-sm', 16) ?> Find a tour</a>
              <a class="btn btn-ghost" href="<?= e(url('/hotels')) ?>"><?= icon('building', 'i-sm', 16) ?> Find a hotel</a>
              <a class="btn btn-ghost" href="<?= e(url('/account?tab=favorites')) ?>"><?= icon('heart', 'i-sm', 16) ?> Favourites</a>
              <a class="btn btn-ghost" href="<?= e(url('/contact')) ?>"><?= icon('message-square', 'i-sm', 16) ?> Contact support</a>
            </div>
            <?php if ($notifications): ?>
              <div class="panel-sub">
                <h3>Latest updates</h3>
                <?php foreach (array_slice($notifications, 0, 3) as $n): ?>
                  <div class="mini-note">
                    <strong><?= e($n['title']) ?></strong>
                    <small><?= e(excerpt((string) $n['message'], 90)) ?> &middot; <?= e(time_ago((string) $n['created_at'])) ?></small>
                  </div>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>
          </div>
        </div>

      <!-- ------------------------------------------------ bookings -->
      <?php elseif ($tab === 'bookings'): ?>
        <div class="panel-head"><h2 class="panel-title">My bookings</h2>
          <a class="link-secondary" href="<?= e(url('/tours')) ?>">Book a new trip</a></div>
        <?php if (!$bookings): ?>
          <div class="empty-state glass">
            <p>You have no bookings yet.</p>
            <a class="btn btn-primary" href="<?= e(url('/tours')) ?>">Explore tours</a>
          </div>
        <?php else: ?>
          <div class="booking-list">
            <?php foreach ($bookings as $b): ?>
              <article class="booking-card glass">
                <div class="booking-card-head">
                  <div>
                    <span class="card-kicker">#<?= e($b['reference']) ?> · <?= e(ucfirst($b['booking_type'])) ?></span>
                    <strong class="card-title"><?= e($b['tour_title'] ?: $b['hotel_name'] ?: 'Booking #' . $b['id']) ?></strong>
                  </div>
                  <span class="badge <?= $statusClass[$b['status']] ?? 'badge-info' ?>"><?= e($b['status']) ?></span>
                </div>
                <div class="booking-facts">
                  <span><?= icon('calendar', 'i-xs', 15) ?>
                    <?= $b['travel_date'] ? e(date('d M Y', strtotime($b['travel_date'])))
                        : e(date('d M Y', strtotime($b['check_in']))) . ' → ' . e(date('d M Y', strtotime($b['check_out']))) ?></span>
                  <span><?= icon('users', 'i-xs', 15) ?> <?= (int) $b['guests'] ?> guest(s)<?= $b['rooms'] > 1 ? ', ' . (int) $b['rooms'] . ' rooms' : '' ?></span>
                  <span><?= icon('dollar-sign', 'i-xs', 15) ?> <?= e(money($b['total'], $b['currency'])) ?></span>
                  <span class="badge <?= $b['payment_status'] === 'paid' ? 'badge-ok' : 'badge-pending' ?>"><?= e(str_replace('_', ' ', $b['payment_status'])) ?></span>
                </div>
                <?php if (in_array($b['status'], ['pending', 'confirmed'], true)): ?>
                  <form method="post" action="<?= e(url('/booking/' . (int) $b['id'] . '/cancel')) ?>"
                        data-confirm="Cancel booking #<?= e($b['reference']) ?>?">
                    <?= csrf_field() ?>
                    <button class="btn btn-ghost btn-sm is-danger" type="submit"><?= icon('x', 'i-xs', 14) ?> Cancel booking</button>
                  </form>
                <?php endif; ?>
              </article>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>

      <!-- ------------------------------------------------ favourites -->
      <?php elseif ($tab === 'favorites'): ?>
        <div class="panel-head"><h2 class="panel-title">Favourites</h2>
          <a class="link-secondary" href="<?= e(url('/tours')) ?>">Browse more</a></div>
        <?php if (!$favs): ?>
          <div class="empty-state glass">
            <p>Nothing saved yet - tap the heart on any tour or hotel.</p>
            <a class="btn btn-primary" href="<?= e(url('/tours')) ?>">Find favourites</a>
          </div>
        <?php else: ?>
          <div class="grid grid-cards">
            <?php foreach ($favs as $f): if (!$f['t_title'] && !$f['h_title']) continue;
              $isTour = $f['target_type'] === 'tour';
              $href = $isTour ? '/tours/' . $f['t_slug'] : '/hotels/' . $f['h_slug'];
              $name = $isTour ? $f['t_title'] : $f['h_title'];
              $img = $isTour ? $f['t_img'] : $f['h_img'];
              $price = $isTour ? money($f['t_price'], ((float) $f['t_price'] > 5000) ? 'RWF' : 'USD')
                               : money($f['h_price'], $f['h_cur'] ?: 'RWF'); ?>
              <div class="tour-card glass fav-card">
                <a class="card-media" href="<?= e(url($href)) ?>">
                  <img src="<?= e(img_url($img)) ?>" alt="<?= e($name) ?>" loading="lazy">
                  <span class="price-badge"><?= e($price) ?></span>
                </a>
                <div class="card-body">
                  <span class="card-kicker"><?= e(ucfirst($f['target_type'])) ?></span>
                  <a class="card-title link-title" href="<?= e(url($href)) ?>"><?= e($name) ?></a>
                  <form method="post" action="<?= e(url('/api/favorites/toggle')) ?>" data-fav-remove>
                    <?= csrf_field() ?>
                    <input type="hidden" name="target_type" value="<?= e($f['target_type']) ?>">
                    <input type="hidden" name="target_id" value="<?= (int) $f['target_id'] ?>">
                    <button class="btn btn-ghost btn-sm is-danger" type="submit"><?= icon('heart', 'i-xs', 14) ?> Remove</button>
                  </form>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>

      <!-- ------------------------------------------------ profile -->
      <?php elseif ($tab === 'profile'): ?>
        <div class="grid grid-2">
          <form class="glass panel form-panel" method="post" action="<?= e(url('/account/profile')) ?>">
            <?= csrf_field() ?>
            <div class="panel-head"><h2 class="panel-title">Personal details</h2></div>
            <label class="field"><span>Full name</span>
              <input type="text" name="name" required minlength="2" maxlength="120" value="<?= e($user['name']) ?>"></label>
            <label class="field"><span>Email (fixed)</span>
              <input type="email" value="<?= e($user['email']) ?>" disabled></label>
            <label class="field"><span>Phone</span>
              <input type="tel" name="phone" maxlength="20" value="<?= e($user['phone'] ?? '') ?>" placeholder="+250 788 000 000"></label>
            <label class="field"><span>Nationality</span>
              <input type="text" name="nationality" maxlength="80" value="<?= e($user['nationality'] ?? '') ?>" placeholder="Rwandan"></label>
            <button class="btn btn-primary" type="submit"><?= icon('check', 'i-sm', 16) ?> Save details</button>
          </form>

          <form class="glass panel form-panel" method="post" action="<?= e(url('/account/password')) ?>">
            <?= csrf_field() ?>
            <div class="panel-head"><h2 class="panel-title">Change password</h2></div>
            <label class="field"><span>Current password</span>
              <input type="password" name="current_password" required autocomplete="current-password"></label>
            <label class="field"><span>New password</span>
              <input type="password" name="password" id="acc-pw" required minlength="8" autocomplete="new-password">
            </label>
            <label class="field"><span>Confirm new password</span>
              <input type="password" name="password_confirm" required minlength="8" autocomplete="new-password"></label>
            <p class="form-note">Use 8+ characters with letters and numbers.</p>
            <button class="btn btn-primary" type="submit"><?= icon('lock', 'i-sm', 16) ?> Update password</button>
          </form>
        </div>
      <?php endif; ?>
    </div>
  </div>
</section>
