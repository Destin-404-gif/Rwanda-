<?php /** Admin notifications inbox. */ ?>
<div class="page-top">
  <div>
    <p class="tagline">Inbox</p>
    <h1 class="admin-title">Notifications</h1>
    <p class="admin-sub">Booking requests, contact messages and system alerts.</p>
  </div>
  <div class="page-top-actions">
    <a class="btn btn-ghost" href="<?= e(url('/admin/notifications')) ?><?= $filter === 'unread' ? '' : '?filter=unread' ?>">
      <?= icon('filter', 'i-sm', 16) ?> <?= $filter === 'unread' ? 'Show all' : 'Unread only' ?>
    </a>
    <form method="post" action="<?= e(url('/admin/notifications/read')) ?>">
      <?= csrf_field() ?>
      <button class="btn btn-primary" type="submit"><?= icon('check', 'i-sm', 16) ?> Mark all read</button>
    </form>
  </div>
</div>

<section class="glass panel">
  <div class="panel-head"><h2 class="panel-title"><?= count($items) ?> notification<?= count($items) === 1 ? '' : 's' ?></h2></div>

  <?php if (!$items): ?>
    <p class="empty-state">Nothing here yet - new bookings and messages appear in this list.</p>
  <?php else: ?>
    <ul class="notif-list">
      <?php foreach ($items as $n): ?>
        <li class="notif-item<?= (int) $n['is_read'] ? '' : ' is-unread' ?>">
          <span class="notif-dot" aria-hidden="<?= (int) $n['is_read'] ? 'true' : 'false' ?>"></span>
          <div class="notif-body">
            <div class="notif-top">
              <strong><?= e($n['title']) ?></strong>
              <small><?= e(time_ago((string) $n['created_at'])) ?> &middot; <?= e($n['type'] ?: 'system') ?></small>
            </div>
            <p><?= e($n['message']) ?></p>
          </div>
        </li>
      <?php endforeach; ?>
    </ul>
    <form method="post" action="<?= e(url('/admin/notifications/read')) ?>" class="notif-foot">
      <?= csrf_field() ?>
      <button class="btn btn-ghost btn-sm" type="submit">Mark everything as read</button>
    </form>
  <?php endif; ?>
</section>
