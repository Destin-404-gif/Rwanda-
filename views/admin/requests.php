<?php /** Admin requests: approve or reject pending admin registrations. */ ?>
<div class="page-top">
  <div>
    <p class="tagline">People</p>
    <h1 class="admin-title">Admin requests</h1>
    <p class="admin-sub">New admin registrations waiting for approval. Approved admins can sign in; rejected ones cannot.</p>
  </div>
</div>

<section class="glass panel">
  <div class="panel-head"><h2 class="panel-title">Pending requests (<?= count($pending) ?>)</h2></div>
  <?php if (!$pending): ?>
    <p class="empty-state">No pending requests - every admin registration has been handled.</p>
  <?php else: ?>
    <ul class="notif-list">
      <?php foreach ($pending as $r): ?>
        <li class="notif-item is-unread">
          <span class="notif-dot" aria-hidden="false"></span>
          <div class="notif-body">
            <div class="notif-top">
              <strong><?= e($r['name']) ?></strong>
              <small><?= e(time_ago((string) $r['created_at'])) ?></small>
            </div>
            <p><?= e($r['email']) ?><?= $r['phone'] ? ' &middot; ' . e($r['phone']) : '' ?></p>
            <div class="notif-foot" style="display:flex; gap:.5rem; margin-top:.5rem;">
              <form method="post" action="<?= e(url('/admin/requests/' . (int) $r['id'] . '/approve')) ?>">
                <?= csrf_field() ?>
                <input type="hidden" name="decision" value="approve">
                <button class="btn btn-primary btn-sm" type="submit"><?= icon('check', 'i-sm', 15) ?> Approve</button>
              </form>
              <form method="post" action="<?= e(url('/admin/requests/' . (int) $r['id'] . '/reject')) ?>">
                <?= csrf_field() ?>
                <input type="hidden" name="decision" value="reject">
                <button class="btn btn-ghost btn-sm is-danger" type="submit"><?= icon('x', 'i-sm', 15) ?> Reject</button>
              </form>
            </div>
          </div>
        </li>
      <?php endforeach; ?>
    </ul>
  <?php endif; ?>
</section>

<section class="glass panel">
  <div class="panel-head"><h2 class="panel-title">Recently rejected (<?= count($rejected) ?>)</h2></div>
  <?php if (!$rejected): ?>
    <p class="empty-state">Nothing rejected yet.</p>
  <?php else: ?>
    <ul class="notif-list">
      <?php foreach ($rejected as $r): ?>
        <li class="notif-item">
          <span class="notif-dot" aria-hidden="true"></span>
          <div class="notif-body">
            <div class="notif-top">
              <strong><?= e($r['name']) ?></strong>
              <small><?= e(time_ago((string) $r['created_at'])) ?></small>
            </div>
            <p><?= e($r['email']) ?><?= $r['phone'] ? ' &middot; ' . e($r['phone']) : '' ?></p>
          </div>
        </li>
      <?php endforeach; ?>
    </ul>
  <?php endif; ?>
</section>
