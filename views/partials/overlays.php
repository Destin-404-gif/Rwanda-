<?php
/**
 * Overlay portal - rendered ONCE at the end of <body> on every public page.
 * Single backdrop + single close button per overlay. Only one overlay may be
 * open at a time (enforced in app.js); stacking: navbar < drawer < search < modal.
 */
$hasPromo = is_file(APP_ROOT . '/assets/video/promo.mp4');
?>
<div class="search-overlay" id="search-overlay" hidden role="dialog" aria-modal="true" aria-label="Search the site">
  <div class="search-overlay-inner">
    <form class="search-form" action="<?= e(url('/search')) ?>" method="get" role="search">
      <label class="sr-only" for="overlay-search">Search destinations, tours, hotels</label>
      <?= icon('search', 'search-form-icon', 24) ?>
      <input id="overlay-search" type="search" name="q" placeholder="Search destinations, tours, hotels..."
             autocomplete="off" maxlength="80" required>
      <button class="btn btn-primary" type="submit">Search</button>
      <button class="icon-btn search-close" type="button" data-search-close aria-label="Close search"><?= icon('x', '', 24) ?></button>
    </form>
    <div class="search-suggest">
      <span>Popular:</span>
      <a href="<?= e(url('/search?q=gorilla')) ?>">Gorilla trekking</a>
      <a href="<?= e(url('/search?q=lake+kivu')) ?>">Lake Kivu</a>
      <a href="<?= e(url('/search?q=safari')) ?>">Safari</a>
      <a href="<?= e(url('/search?q=kigali')) ?>">Kigali</a>
    </div>
    <div class="search-results" id="search-results" aria-live="polite"></div>
  </div>
</div>

<!-- Promo film: one modal, one backdrop, one close button, content always sharp -->
<div class="modal" id="video-modal" hidden aria-hidden="true">
  <div class="modal-backdrop" data-modal-close aria-hidden="true"></div>
  <div class="modal-dialog video-dialog" role="dialog" aria-modal="true"
       aria-labelledby="video-modal-title" tabindex="-1">
    <div class="modal-head">
      <h2 id="video-modal-title" class="modal-title">Rwanda Explorer &ndash; Promo Film</h2>
      <button class="modal-close" type="button" data-modal-close aria-label="Close video"><?= icon('x', '', 22) ?></button>
    </div>
    <div class="modal-body video-body">
      <?php if ($hasPromo): ?>
        <div class="video-frame">
          <video id="promo-video" controls playsinline preload="none"
                 poster="<?= e(asset('assets/img/hero-kivu.jpg')) ?>"
                 data-src="<?= e(asset('assets/video/promo.mp4')) ?>"></video>
          <p class="video-error" id="video-error" hidden>
            The film could not be loaded - please try again later, or explore our
            <a href="<?= e(url('/tours')) ?>">tours</a>.
          </p>
        </div>
      <?php else: ?>
        <div class="video-frame video-coming">
          <div class="video-coming-inner">
            <span class="play-btn" aria-hidden="true"><?= icon('play', '', 26) ?></span>
            <strong>Promo video coming soon</strong>
            <p>Our film is still in the edit suite. In the meantime, explore our
              <a href="<?= e(url('/experiences')) ?>">experiences</a> and
              <a href="<?= e(url('/tours')) ?>">tours</a>.</p>
          </div>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<div class="modal" id="lightbox" hidden aria-hidden="true">
  <div class="modal-backdrop" data-modal-close aria-hidden="true"></div>
  <div class="modal-dialog lightbox-dialog" role="dialog" aria-modal="true"
       aria-labelledby="lightbox-title" tabindex="-1">
    <div class="modal-head">
      <h2 id="lightbox-title" class="modal-title">Gallery</h2>
      <button class="modal-close" type="button" data-modal-close aria-label="Close image"><?= icon('x', '', 22) ?></button>
    </div>
    <div class="modal-body lightbox-body">
      <img id="lightbox-img" src="" alt="">
      <p id="lightbox-caption" class="lightbox-caption"></p>
    </div>
  </div>
</div>
