<?php
/** Admin overview: KPIs, bookings chart, recent activity. */
$fmt = fn($v) => (float) $v >= 1000000
    ? number_format((float) $v / 1000000, 1) . 'M'
    : number_format((float) $v);
$chartData = [];
foreach ($chart as $c) $chartData[] = ['ym' => $c['ym'], 'cnt' => (int) $c['cnt'], 'amt' => (float) $c['amt']];
?>
<div class="page-top">
  <div>
    <p class="tagline">Control centre</p>
    <h1 class="admin-title">Dashboard</h1>
    <p class="admin-sub">Bookings, revenue and content at a glance - <?= e(date('l, d F Y')) ?></p>
  </div>
  <div class="page-top-actions">
    <a class="btn btn-ghost" href="<?= e(url('/admin/analytics')) ?>"><?= icon('bar-chart', 'i-sm', 16) ?> Analytics</a>
    <a class="btn btn-primary" href="<?= e(url('/admin/bookings/new')) ?>"><?= icon('plus', 'i-sm', 16) ?> New booking</a>
  </div>
</div>

<div class="kpi-grid">
  <div class="kpi glass">
    <span class="kpi-ico kpi-lime"><?= icon('dollar-sign', '', 20) ?></span>
    <div><span class="kpi-num"><?= e($fmt($stats['revenue'])) ?></span>
      <span class="kpi-label">Total revenue (RWF)</span>
      <small class="kpi-delta">This month: <?= e($fmt($stats['revenue_month'])) ?></small></div>
  </div>
  <div class="kpi glass">
    <span class="kpi-ico"><?= icon('ticket', '', 20) ?></span>
    <div><span class="kpi-num"><?= (int) $stats['bookings'] ?></span>
      <span class="kpi-label">Total bookings</span>
      <small class="kpi-delta kpi-warn"><?= (int) $stats['pending'] ?> awaiting confirmation</small></div>
  </div>
  <div class="kpi glass">
    <span class="kpi-ico"><?= icon('users', '', 20) ?></span>
    <div><span class="kpi-num"><?= (int) $stats['customers'] ?></span>
      <span class="kpi-label">Customers</span>
      <small class="kpi-delta"><?= (int) $stats['tours'] ?> tours / <?= (int) $stats['hotels'] ?> hotels live</small></div>
  </div>
  <div class="kpi glass">
    <span class="kpi-ico kpi-lime"><?= icon('message-square', '', 20) ?></span>
    <div><span class="kpi-num"><?= (int) $stats['reviews_pending'] ?></span>
      <span class="kpi-label">Reviews to moderate</span>
      <small class="kpi-delta"><?= (int) $stats['destinations'] ?> destinations published</small></div>
  </div>
</div>

<div class="admin-grid-2">
  <section class="glass panel">
    <div class="panel-head">
      <h2 class="panel-title">Bookings - last 6 months</h2>
      <div class="legend">
        <span class="legend-item legend-lime">Bookings</span>
        <span class="legend-item legend-dim">Revenue</span>
      </div>
    </div>
    <div class="chart-wrap">
      <canvas id="bookings-chart" height="260"
              data-chart='<?= e(json_encode($chartData)) ?>'></canvas>
      <?php if (!$chartData): ?><p class="empty-state">No bookings yet - the chart fills up as orders come in.</p><?php endif; ?>
    </div>
  </section>

  <section class="glass panel">
    <div class="panel-head">
      <h2 class="panel-title">Quick actions</h2>
    </div>
    <div class="quick-actions">
      <a class="btn btn-primary" href="<?= e(url('/admin/tours/new')) ?>"><?= icon('plus', 'i-sm', 16) ?> Add tour</a>
      <a class="btn btn-ghost" href="<?= e(url('/admin/hotels/new')) ?>"><?= icon('plus', 'i-sm', 16) ?> Add hotel</a>
      <a class="btn btn-ghost" href="<?= e(url('/admin/gallery/new')) ?>"><?= icon('image', 'i-sm', 16) ?> Upload photo</a>
      <a class="btn btn-ghost" href="<?= e(url('/admin/blog/new')) ?>"><?= icon('book-open', 'i-sm', 16) ?> Write post</a>
      <a class="btn btn-ghost" href="<?= e(url('/admin/destinations/new')) ?>"><?= icon('map-pin', 'i-sm', 16) ?> Add destination</a>
      <a class="btn btn-ghost" href="<?= e(url('/admin/hero-slides/new')) ?>"><?= icon('camera', 'i-sm', 16) ?> New hero slide</a>
    </div>
    <?php if ($stats['reviews_pending'] > 0): ?>
      <a class="quick-flag" href="<?= e(url('/admin/reviews')) ?>">
        <?= icon('alert-circle', 'i-sm', 16) ?>
        <?= (int) $stats['reviews_pending'] ?> review(s) waiting for moderation
      </a>
    <?php endif; ?>
    <?php if ($stats['pending'] > 0): ?>
      <a class="quick-flag" href="<?= e(url('/admin/bookings')) ?>">
        <?= icon('clock', 'i-sm', 16) ?>
        <?= (int) $stats['pending'] ?> booking(s) need confirmation
      </a>
    <?php endif; ?>
  </section>
</div>

<section class="glass panel">
  <div class="panel-head">
    <h2 class="panel-title">Recent bookings</h2>
    <a class="link-secondary" href="<?= e(url('/admin/bookings')) ?>">All bookings <?= icon('arrow-right', 'i-sm', 15) ?></a>
  </div>
  <div class="table-wrap">
    <table class="admin-table">
      <thead>
        <tr><th>Ref</th><th>Item</th><th>Customer</th><th>Date</th><th>Total</th><th>Status</th><th></th></tr>
      </thead>
      <tbody>
        <?php if (!$recent): ?>
          <tr><td colspan="7" class="table-empty">No bookings yet.</td></tr>
        <?php else: foreach ($recent as $b): ?>
          <tr>
            <td class="mono">#<?= e($b['reference']) ?></td>
            <td><strong><?= e($b['tour_title'] ?: $b['hotel_name'] ?: ucfirst($b['booking_type'])) ?></strong>
                <small class="cell-sub"><?= e(ucfirst($b['booking_type'])) ?></small></td>
            <td><?= e($b['customer_name'] ?: 'Guest') ?><small class="cell-sub"><?= e($b['customer_email']) ?></small></td>
            <td><?= e(date('d M Y', strtotime($b['travel_date'] ?: ($b['check_in'] ?: $b['created_at'])))) ?></td>
            <td class="mono"><?= e(money($b['total'], $b['currency'])) ?></td>
            <td><span class="badge badge-<?= e($b['status']) ?>"><?= e($b['status']) ?></span></td>
            <td class="table-actions">
              <a class="icon-btn" href="<?= e(url('/admin/bookings/' . (int) $b['id'] . '/edit')) ?>"
                 aria-label="Edit booking"><?= icon('pencil', 'i-sm', 16) ?></a>
            </td>
          </tr>
        <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>
</section>
