<?php /** Admin own profile. */
$u = $adminUser ?? auth_user();
?>
<div class="page-top">
  <div>
    <p class="tagline">Account</p>
    <h1 class="admin-title">My profile</h1>
    <p class="admin-sub">Signed in as <?= e($u['email']) ?> &middot; role: admin</p>
  </div>
</div>

<div class="admin-grid-2">
  <form class="glass panel form-panel" method="post" action="<?= e(url('/admin/profile')) ?>">
    <?= csrf_field() ?>
    <div class="panel-head"><h2 class="panel-title">Personal details</h2></div>
    <div class="profile-avatar-row">
      <span class="avatar avatar-lg accent-avatar"><?= e(initials($u['name'])) ?></span>
      <div><strong><?= e($u['name']) ?></strong>
        <small>Member since <?= e(date('d M Y', strtotime($u['created_at'] ?? 'now'))) ?></small></div>
    </div>
    <label class="field"><span>Full name</span>
      <input type="text" name="name" required minlength="2" maxlength="120" value="<?= e($u['name']) ?>"></label>
    <label class="field"><span>Email (fixed)</span>
      <input type="email" value="<?= e($u['email']) ?>" disabled></label>
    <label class="field"><span>Phone</span>
      <input type="tel" name="phone" maxlength="20" value="<?= e($u['phone'] ?? '') ?>" placeholder="+250 788 000 000"></label>
    <button class="btn btn-primary" type="submit"><?= icon('check', 'i-sm', 16) ?> Save details</button>
  </form>

  <form class="glass panel form-panel" method="post" action="<?= e(url('/admin/profile')) ?>">
    <?= csrf_field() ?>
    <input type="hidden" name="do" value="password">
    <div class="panel-head"><h2 class="panel-title">Change password</h2></div>
    <label class="field"><span>Current password</span>
      <input type="password" name="current_password" required autocomplete="current-password"></label>
    <label class="field"><span>New password</span>
      <input type="password" name="password" required minlength="8" autocomplete="new-password"></label>
    <label class="field"><span>Confirm new password</span>
      <input type="password" name="password_confirm" required minlength="8" autocomplete="new-password"></label>
    <p class="form-note">8+ characters with letters and numbers. Changing it signs out other sessions on next load.</p>
    <button class="btn btn-primary" type="submit"><?= icon('lock', 'i-sm', 16) ?> Update password</button>
  </form>
</div>
