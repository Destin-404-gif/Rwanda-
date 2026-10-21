<?php partial('page-header', [
    'tag' => 'Talk to us',
    'title' => 'Contact <span class="accent">Us</span>',
    'sub' => 'Questions about permits, dates or hotels? A real person in Kigali replies within one working day.',
    'image' => 'assets/img/hero-kigali.jpg',
]);
$s = settings();
?>
<section class="section">
  <div class="container split split-wide">
    <div class="split-copy">
      <div class="contact-cards">
        <a class="contact-card glass" href="tel:<?= e(preg_replace('/\s+/', '', $s['phone'] ?? '+250788000000')) ?>">
          <?= icon('phone', '', 22) ?>
          <span><small>Call us</small><strong><?= e($s['phone'] ?? '+250 788 000 000') ?></strong></span>
        </a>
        <a class="contact-card glass" href="mailto:<?= e($s['email'] ?? 'hello@rwandaexplorer.rw') ?>">
          <?= icon('mail', '', 22) ?>
          <span><small>Email</small><strong><?= e($s['email'] ?? 'hello@rwandaexplorer.rw') ?></strong></span>
        </a>
        <div class="contact-card glass">
          <?= icon('map-pin', '', 22) ?>
          <span><small>Office</small><strong><?= e($s['address'] ?? 'KG 7 Ave, Kigali, Rwanda') ?></strong></span>
        </div>
        <div class="contact-card glass">
          <?= icon('clock', '', 22) ?>
          <span><small>Hours</small><strong><?= e($s['hours'] ?? 'Mon-Sat, 08:00-18:00 CAT') ?></strong></span>
        </div>
      </div>

      <div class="glass side-card">
        <h3 class="side-title">Good to know</h3>
        <ul class="check-list">
          <li><?= icon('check', 'i-xs', 16) ?> Gorilla permits sell out months ahead - enquire early.</li>
          <li><?= icon('check', 'i-xs', 16) ?> Quotes are free and valid for 14 days.</li>
          <li><?= icon('check', 'i-xs', 16) ?> Prefer WhatsApp? Use the number above.</li>
        </ul>
      </div>
    </div>

    <form class="glass contact-form" method="post" action="<?= e(url('/contact')) ?>">
      <?= csrf_field() ?>
      <h2 class="section-title form-title">Send a <span class="accent">message</span></h2>
      <div class="form-row">
        <label class="field"><span>Your name *</span>
          <input type="text" name="name" required minlength="2" maxlength="80" value="<?= e(auth_user()['name'] ?? '') ?>"></label>
        <label class="field"><span>Email *</span>
          <input type="email" name="email" required maxlength="120" value="<?= e(auth_user()['email'] ?? '') ?>"></label>
      </div>
      <label class="field"><span>Subject</span>
        <select name="subject">
          <option value="">General enquiry</option>
          <option>Gorilla trekking & permits</option>
          <option>Custom itinerary</option>
          <option>Hotel booking</option>
          <option>Existing booking</option>
          <option>Partnership</option>
        </select>
      </label>
      <label class="field"><span>Message *</span>
        <textarea name="message" rows="6" required minlength="10" maxlength="3000"
                  placeholder="Dates, number of travellers, places you want to see..."></textarea></label>
      <button class="btn btn-primary btn-lg" type="submit"><?= icon('send', 'i-sm', 16) ?> Send message</button>
      <p class="form-note">By sending you agree to be contacted about your enquiry.</p>
    </form>
  </div>
</section>
