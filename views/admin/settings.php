<?php /** Admin settings. */ ?>
<div class="page-top">
  <div>
    <p class="tagline">Configuration</p>
    <h1 class="admin-title">Settings</h1>
    <p class="admin-sub">Site identity and contact details.</p>
  </div>
</div>

<form method="post" action="<?= e(url('/admin/settings')) ?>" class="settings-form">
  <?= csrf_field() ?>

  <section class="glass panel">
    <div class="panel-head"><h2 class="panel-title">General</h2></div>
    <div class="form-row">
      <label class="field"><span>Site name</span>
        <input type="text" name="site_name" maxlength="120" value="<?= e($settings['site_name'] ?? 'Rwanda Explorer') ?>"></label>
      <label class="field"><span>Tagline</span>
        <input type="text" name="tagline" maxlength="200"
               value="<?= e($settings['tagline'] ?? 'Explore Rwanda: gorillas, safaris, lakes and hills.') ?>"></label>
    </div>
    <div class="form-row">
      <label class="field"><span>Default currency</span>
        <select name="currency">
          <option value="RWF"<?= ($settings['currency'] ?? 'RWF') === 'RWF' ? ' selected' : '' ?>>RWF</option>
          <option value="USD"<?= ($settings['currency'] ?? '') === 'USD' ? ' selected' : '' ?>>USD</option>
        </select></label>
      <label class="field"><span>Footer note</span>
        <input type="text" name="footer_note" maxlength="200" value="<?= e($settings['footer_note'] ?? '') ?>"></label>
    </div>
  </section>

  <section class="glass panel">
    <div class="panel-head"><h2 class="panel-title">Contact</h2></div>
    <div class="form-row">
      <label class="field"><span>Phone</span>
        <input type="tel" name="phone" maxlength="40" value="<?= e($settings['phone'] ?? '+250 788 000 000') ?>"></label>
      <label class="field"><span>Email</span>
        <input type="email" name="email" maxlength="180" value="<?= e($settings['email'] ?? 'hello@rwandaexplorer.rw') ?>"></label>
    </div>
    <div class="form-row">
      <label class="field"><span>Address</span>
        <input type="text" name="address" maxlength="255" value="<?= e($settings['address'] ?? 'KG 7 Ave, Kigali, Rwanda') ?>"></label>
      <label class="field"><span>Opening hours</span>
        <input type="text" name="hours" maxlength="120" value="<?= e($settings['hours'] ?? 'Mon-Sat, 08:00-18:00 CAT') ?>"></label>
    </div>
  </section>

  <section class="glass panel">
    <div class="panel-head"><h2 class="panel-title">Social links</h2></div>
    <div class="form-row">
      <label class="field"><span>Facebook URL</span>
        <input type="url" name="facebook" maxlength="300" value="<?= e($settings['facebook'] ?? '') ?>" placeholder="https://facebook.com/..."></label>
      <label class="field"><span>Instagram URL</span>
        <input type="url" name="instagram" maxlength="300" value="<?= e($settings['instagram'] ?? '') ?>" placeholder="https://instagram.com/..."></label>
    </div>
    <div class="form-row">
      <label class="field"><span>Twitter / X URL</span>
        <input type="url" name="twitter" maxlength="300" value="<?= e($settings['twitter'] ?? '') ?>" placeholder="https://x.com/..."></label>
      <label class="field"><span>YouTube URL</span>
        <input type="url" name="youtube" maxlength="300" value="<?= e($settings['youtube'] ?? '') ?>" placeholder="https://youtube.com/..."></label>
    </div>
  </section>

  <div class="form-actions">
    <button class="btn btn-primary btn-lg" type="submit"><?= icon('check', 'i-sm', 16) ?> Save settings</button>
  </div>
</form>
