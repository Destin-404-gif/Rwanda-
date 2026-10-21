<?php /** Admin analytics. */
$fmt = fn($v) => number_format((float) $v);
$maxTour = 1; foreach ($byTour as $r) $maxTour = max($maxTour, (float) $r['revenue']);
$maxHotel = 1; foreach ($byHotel as $r) $maxHotel = max($maxHotel, (float) $r['revenue']);
?>
<div class="page-top">
  <div>
    <p class="tagline">Insights</p>
    <h1 class="admin-title">Analytics</h1>
    <p class="admin-sub">Where the money and the demand come from.</p>
  </div>
  <div class="page-top-actions">
    <a class="btn btn-ghost" href="<?= e(url('/admin')) ?>"><?= icon('arrow-left', 'i-sm', 16) ?> Dashboard</a>
  </div>
</div>

<div class="admin-grid-2">
  <section class="glass panel">
    <div class="panel-head"><h2 class="panel-title">Top earning tours</h2></div>
    <?php if (!$byTour): ?>
      <p class="empty-state">No tour bookings yet.</p>
    <?php else: ?>
      <ul class="bar-list">
        <?php foreach ($byTour as $r): ?>
          <li>
            <span class="bar-label"><?= e($r['title']) ?><small><?= (int) $r['bookings'] ?> bookings</small></span>
            <span class="bar-track"><span class="bar-fill" style="width:<?= max(4, (int) round($r['revenue'] / $maxTour * 100)) ?>%"></span></span>
            <span class="bar-value mono"><?= e($fmt($r['revenue'])) ?></span>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>
  </section>

  <section class="glass panel">
    <div class="panel-head"><h2 class="panel-title">Top earning hotels</h2></div>
    <?php if (!$byHotel): ?>
      <p class="empty-state">No hotel bookings yet.</p>
    <?php else: ?>
      <ul class="bar-list">
        <?php foreach ($byHotel as $r): ?>
          <li>
            <span class="bar-label"><?= e($r['name']) ?><small><?= (int) $r['bookings'] ?> bookings</small></span>
            <span class="bar-track"><span class="bar-fill" style="width:<?= max(4, (int) round($r['revenue'] / $maxHotel * 100)) ?>%"></span></span>
            <span class="bar-value mono"><?= e($fmt($r['revenue'])) ?></span>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>
  </section>
</div>

<div class="admin-grid-2">
  <section class="glass panel">
    <div class="panel-head"><h2 class="panel-title">Bookings by status</h2></div>
    <div class="chip-row">
      <?php foreach ($byStatus as $r): ?>
        <span class="chip"><span class="badge badge-<?= e($r['status']) ?>"><?= e($r['status']) ?></span>
          <?= (int) $r['cnt'] ?> &middot; <?= e(money($r['amt'], 'RWF')) ?></span>
      <?php endforeach; ?>
      <?php if (!$byStatus): ?><p class="empty-state">No data yet.</p><?php endif; ?>
    </div>
  </section>

  <section class="glass panel">
    <div class="panel-head"><h2 class="panel-title">Booking source</h2></div>
    <div class="chip-row">
      <?php foreach ($bySource as $r): ?>
        <span class="chip"><?= e(ucfirst($r['source'])) ?> <strong><?= (int) $r['cnt'] ?></strong></span>
      <?php endforeach; ?>
      <?php if (!$bySource): ?><p class="empty-state">No data yet.</p><?php endif; ?>
    </div>
  </section>
</div>

<section class="glass panel">
  <div class="panel-head"><h2 class="panel-title">Demand by destination</h2></div>
  <div class="table-wrap">
    <table class="admin-table">
      <thead><tr><th>Destination</th><th>Bookings</th></tr></thead>
      <tbody>
      <?php foreach ($topDest as $d): ?>
        <tr><td><?= e($d['name']) ?></td><td class="mono"><?= (int) $d['bookings'] ?></td></tr>
      <?php endforeach; ?>
      <?php if (!$topDest): ?><tr><td colspan="2" class="table-empty">No data yet.</td></tr><?php endif; ?>
      </tbody>
    </table>
  </div>
</section>
