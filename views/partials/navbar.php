<?php
/** Site navbar: transparent over hero, solid after scroll; mobile drawer. */
$active = $active ?? '';
$user = $user ?? auth_user();
$links = [
    ['home', '/', 'Home'],
    ['destinations', '/destinations', 'Destinations'],
    ['experiences', '/experiences', 'Experiences'],
    ['tours', '/tours', 'Tours'],
    ['hotels', '/hotels', 'Hotels'],
    ['gallery', '/gallery', 'Gallery'],
    ['about', '/about', 'About'],
];
$drawerDest = db_all("SELECT slug, name FROM destinations WHERE status='published' ORDER BY is_featured DESC, name LIMIT 6");
$drawerTours = db_all("SELECT slug, title FROM tours WHERE status='published' ORDER BY is_featured DESC, rating DESC LIMIT 5");
$drawerHotels = db_all("SELECT slug, name FROM hotels WHERE status='approved' ORDER BY is_featured DESC, rating DESC LIMIT 5");
?>
<header class="site-header" id="site-header">
  <div class="container nav-inner">
    <a class="brand" href="<?= e(url('/')) ?>" aria-label="Rwanda Explorer home">
      <span class="brand-badge"><?= icon('map-pin', '', 20) ?></span>
      <span class="brand-text">Rwanda Explorer</span>
    </a>

    <nav class="nav-center" aria-label="Primary">
      <?php foreach ($links as [$key, $href, $label]): ?>
        <a href="<?= e(url($href)) ?>" class="nav-link<?= $active === $key ? ' is-active' : '' ?>"
           <?= $active === $key ? 'aria-current="page"' : '' ?>><?= e($label) ?></a>
      <?php endforeach; ?>
    </nav>

    <div class="nav-right">
      <button class="icon-btn" type="button" data-search-open aria-label="Open search"><?= icon('search', '', 22) ?></button>

      <?php if ($user && $user['role'] === 'customer'): ?>
        <div class="account-menu" data-dropdown>
          <button class="account-trigger" type="button" data-dropdown-toggle aria-expanded="false" aria-haspopup="true">
            <span class="avatar avatar-sm"><?= e(initials($user['name'])) ?></span>
            <span class="account-name"><?= e(excerpt($user['name'], 14)) ?></span>
            <?= icon('chevron-down', 'i-xs', 16) ?>
          </button>
          <div class="dropdown-menu" data-dropdown-menu role="menu">
            <div class="dropdown-head">
              <strong><?= e($user['name']) ?></strong>
              <span><?= e($user['email']) ?></span>
            </div>
            <a role="menuitem" href="<?= e(url('/account?tab=bookings')) ?>"><?= icon('ticket', 'i-sm') ?> My Bookings</a>
            <a role="menuitem" href="<?= e(url('/account?tab=favorites')) ?>"><?= icon('heart', 'i-sm') ?> Favorites</a>
            <a role="menuitem" href="<?= e(url('/account?tab=profile')) ?>"><?= icon('user', 'i-sm') ?> Profile</a>
            <?php if ($user['role'] === 'admin'): ?>
              <a role="menuitem" href="<?= e(url('/admin')) ?>"><?= icon('layout-dashboard', 'i-sm') ?> Dashboard</a>
            <?php endif; ?>
            <span class="dropdown-sep" role="separator"></span>
            <a role="menuitem" class="is-danger" href="<?= e(url('/logout')) ?>"><?= icon('log-out', 'i-sm') ?> Logout</a>
          </div>
        </div>
      <?php else: ?>
        <div class="account-menu" data-dropdown>
          <button class="account-trigger" type="button" data-dropdown-toggle aria-expanded="false" aria-haspopup="true">
            <?= icon('user', '', 18) ?>
            <span class="account-label">Login / Sign-up</span>
            <?= icon('chevron-down', 'i-xs', 16) ?>
          </button>
          <div class="dropdown-menu" data-dropdown-menu role="menu">
            <a role="menuitem" href="<?= e(url('/login')) ?>"><?= icon('lock', 'i-sm') ?> Customer Login</a>
            <a role="menuitem" href="<?= e(url('/register')) ?>"><?= icon('user-plus', 'i-sm') ?> Customer Register</a>
            <span class="dropdown-sep" role="separator"></span>
            <a role="menuitem" href="<?= e(url('/admin/login')) ?>"><?= icon('shield', 'i-sm') ?> Admin Login</a>
          </div>
        </div>
      <?php endif; ?>

      <button class="icon-btn hamburger" type="button" data-drawer-open
              aria-label="Open menu" aria-expanded="false" aria-controls="mobile-drawer">
        <?= icon('menu', '', 24) ?>
      </button>
    </div>
  </div>
</header>

<div class="drawer-backdrop" data-drawer-close hidden></div>
<aside class="mobile-drawer" id="mobile-drawer" aria-hidden="true" aria-label="Site menu">
  <div class="drawer-head">
    <a class="brand" href="<?= e(url('/')) ?>" data-drawer-close>
      <span class="brand-badge"><?= icon('map-pin', '', 20) ?></span>
      <span class="brand-text">Rwanda Explorer</span>
    </a>
    <button class="icon-btn" type="button" data-drawer-close aria-label="Close menu"><?= icon('x', '', 24) ?></button>
  </div>

  <nav class="drawer-nav" aria-label="Mobile">
    <?php foreach ($links as [$key, $href, $label]): ?>
      <a href="<?= e(url($href)) ?>" class="drawer-link<?= $active === $key ? ' is-active' : '' ?>" data-drawer-close><?= e($label) ?></a>
    <?php endforeach; ?>

    <div class="drawer-group" data-accordion>
      <button class="drawer-link drawer-toggle" type="button" aria-expanded="false" data-accordion-toggle>
        Destinations <?= icon('chevron-down', 'i-xs', 16) ?>
      </button>
      <div class="drawer-sub" data-accordion-panel hidden>
        <a href="<?= e(url('/destinations')) ?>" data-drawer-close>All destinations</a>
        <?php foreach ($drawerDest as $d): ?>
          <a href="<?= e(url('/destinations/' . $d['slug'])) ?>" data-drawer-close><?= e($d['name']) ?></a>
        <?php endforeach; ?>
      </div>
    </div>

    <div class="drawer-group" data-accordion>
      <button class="drawer-link drawer-toggle" type="button" aria-expanded="false" data-accordion-toggle>
        Tours <?= icon('chevron-down', 'i-xs', 16) ?>
      </button>
      <div class="drawer-sub" data-accordion-panel hidden>
        <a href="<?= e(url('/tours')) ?>" data-drawer-close>All tours</a>
        <?php foreach ($drawerTours as $t): ?>
          <a href="<?= e(url('/tours/' . $t['slug'])) ?>" data-drawer-close><?= e($t['title']) ?></a>
        <?php endforeach; ?>
      </div>
    </div>

    <div class="drawer-group" data-accordion>
      <button class="drawer-link drawer-toggle" type="button" aria-expanded="false" data-accordion-toggle>
        Hotels <?= icon('chevron-down', 'i-xs', 16) ?>
      </button>
      <div class="drawer-sub" data-accordion-panel hidden>
        <a href="<?= e(url('/hotels')) ?>" data-drawer-close>All hotels</a>
        <?php foreach ($drawerHotels as $h): ?>
          <a href="<?= e(url('/hotels/' . $h['slug'])) ?>" data-drawer-close><?= e($h['name']) ?></a>
        <?php endforeach; ?>
      </div>
    </div>

    <a href="<?= e(url('/blog')) ?>" class="drawer-link" data-drawer-close>Blog</a>
    <a href="<?= e(url('/guide')) ?>" class="drawer-link" data-drawer-close>Travel Guide</a>
    <a href="<?= e(url('/contact')) ?>" class="drawer-link" data-drawer-close>Contact</a>

    <button class="drawer-link drawer-search" type="button" data-search-open>
      <?= icon('search', 'i-sm', 18) ?> Search
    </button>

    <div class="drawer-account">
      <?php if ($user): ?>
        <a class="btn btn-primary btn-block" href="<?= e(url('/account')) ?>" data-drawer-close><?= icon('user', 'i-sm', 16) ?> My Account</a>
        <?php if ($user['role'] === 'admin'): ?>
          <a class="btn btn-ghost btn-block" href="<?= e(url('/admin')) ?>" data-drawer-close><?= icon('layout-dashboard', 'i-sm', 16) ?> Admin Dashboard</a>
        <?php endif; ?>
        <a class="btn btn-ghost btn-block is-danger" href="<?= e(url('/logout')) ?>" data-drawer-close><?= icon('log-out', 'i-sm', 16) ?> Logout</a>
      <?php else: ?>
        <a class="btn btn-primary btn-block" href="<?= e(url('/login')) ?>" data-drawer-close><?= icon('lock', 'i-sm', 16) ?> Customer Login</a>
        <a class="btn btn-ghost btn-block" href="<?= e(url('/register')) ?>" data-drawer-close><?= icon('user-plus', 'i-sm', 16) ?> Customer Register</a>
        <a class="btn btn-ghost btn-block" href="<?= e(url('/admin/login')) ?>" data-drawer-close><?= icon('shield', 'i-sm', 16) ?> Admin Login</a>
      <?php endif; ?>
    </div>
  </nav>
</aside>
