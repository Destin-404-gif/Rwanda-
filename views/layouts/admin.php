<?php
/** Admin dashboard layout: collapsible sidebar + topbar. */
$title = $title ?? 'Admin';
$adminUser = $adminUser ?? auth_user();
$unread = $unreadNotifications ?? 0;

$navGroups = [
    ['Main', [
        ['/admin', 'layout-dashboard', 'Dashboard', 'dashboard'],
        ['/admin/analytics', 'bar-chart', 'Analytics', 'analytics'],
        ['/admin/notifications', 'bell', 'Notifications', 'notifications', $unread],
    ]],
    ['Inventory', [
        ['/admin/destinations', 'map-pin', 'Destinations', 'destinations'],
        ['/admin/tours', 'compass', 'Tours', 'tours'],
        ['/admin/tour-dates', 'calendar', 'Departures', 'tour-dates'],
        ['/admin/hotels', 'building', 'Hotels', 'hotels'],
        ['/admin/rooms', 'home', 'Rooms', 'rooms'],
    ]],
    ['Sales', [
        ['/admin/bookings', 'ticket', 'Bookings', 'bookings'],
        ['/admin/payments', 'credit-card', 'Payments', 'payments'],
        ['/admin/permits', 'file-text', 'Permits', 'permits'],
        ['/admin/coupons', 'key', 'Coupons', 'coupons'],
    ]],
    ['Content', [
        ['/admin/gallery', 'image', 'Gallery', 'gallery'],
        ['/admin/hero-slides', 'camera', 'Hero slides', 'hero-slides'],
        ['/admin/blog', 'book-open', 'Blog posts', 'blog'],
        ['/admin/testimonials', 'message-square', 'Testimonials', 'testimonials'],
    ]],
    ['People', [
        ['/admin/customers', 'users', 'Customers', 'customers'],
        ['/admin/guides', 'compass', 'Guides', 'guides'],
        ['/admin/reviews', 'star', 'Reviews', 'reviews'],
        ['/admin/requests', 'shield', 'Admin requests', 'requests', (int) db_val(
            "SELECT COUNT(*) FROM users WHERE role = 'admin' AND status = 'pending'"
        )],
    ]],
    ['System', [
        ['/admin/settings', 'settings', 'Settings', 'settings'],
        ['/admin/profile', 'user', 'My profile', 'profile'],
    ]],
];
$activeKey = $active ?? 'dashboard';
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($title) ?></title>
<meta name="robots" content="noindex,nofollow">
<meta name="theme-color" content="#0B0F0A">
<base href="<?= e(url('/')) ?>">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700;800;900&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= e(asset('assets/css/app.css')) ?>">
<link rel="stylesheet" href="<?= e(asset('assets/css/admin.css')) ?>">
<link rel="icon" href="<?= e(asset('assets/img/dest-gorilla.jpg')) ?>" type="image/jpeg">
<script>
window.__BASE__ = <?= json_encode(base_path()) ?>;
window.__CSRF__ = <?= json_encode(csrf_token()) ?>;
window.__ADMIN__ = <?= json_encode([
    'unread' => (int) $unread,
    'user' => $adminUser ? ['name' => $adminUser['name'], 'email' => $adminUser['email'], 'role' => $adminUser['role']] : null,
]) ?>;
</script>
</head>
<body class="page-admin">
<a class="skip-link" href="#admin-main">Skip to content</a>

<div class="admin-shell" id="admin-shell">
  <!-- ------------------------------------------------------ sidebar -->
  <aside class="admin-sidebar" id="admin-sidebar" aria-label="Dashboard menu">
    <div class="admin-brand">
      <a href="<?= e(url('/')) ?>">
        <span class="brand-badge"><?= icon('map-pin', '', 20) ?></span>
        <span class="brand-text">Rwanda Explorer</span>
      </a>
      <button class="icon-btn sidebar-close" type="button" data-sidebar-close aria-label="Close sidebar">
        <?= icon('x', '', 22) ?>
      </button>
    </div>

    <nav class="admin-nav">
      <?php foreach ($navGroups as [$group, $items]): ?>
        <div class="admin-nav-group">
          <p class="admin-nav-label"><?= e($group) ?></p>
          <?php foreach ($items as $item): $href = $item[0]; $ico = $item[1]; $label = $item[2]; $key = $item[3]; $badge = $item[4] ?? 0; ?>
            <a class="admin-nav-link<?= $activeKey === $key ? ' is-active' : '' ?>"
               href="<?= e(url($href)) ?>" <?= $activeKey === $key ? 'aria-current="page"' : '' ?>>
              <?= icon($ico, 'i-sm', 17) ?>
              <span><?= e($label) ?></span>
              <?php if ((int) $badge > 0): ?>
                <span class="nav-badge" data-unread-badge><?= (int) $badge > 99 ? '99+' : (int) $badge ?></span>
              <?php endif; ?>
            </a>
          <?php endforeach; ?>
        </div>
      <?php endforeach; ?>
    </nav>

    <div class="admin-side-foot">
      <a class="btn btn-ghost btn-sm btn-block" href="<?= e(url('/')) ?>" target="_blank" rel="noopener">
        <?= icon('globe', 'i-sm', 15) ?> View site
      </a>
      <a class="btn btn-ghost btn-sm btn-block is-danger" href="<?= e(url('/admin/logout')) ?>">
        <?= icon('log-out', 'i-sm', 15) ?> Sign out
      </a>
    </div>
  </aside>
  <div class="admin-backdrop" data-sidebar-close hidden></div>

  <!-- ------------------------------------------------------- topbar -->
  <div class="admin-content">
    <header class="admin-topbar">
      <button class="icon-btn sidebar-open" type="button" data-sidebar-open aria-label="Open menu">
        <?= icon('menu', '', 22) ?>
      </button>

      <div class="topbar-search">
        <?= icon('search', 'input-icon', 17) ?>
        <input type="search" id="admin-quick-search" placeholder="Jump to: destinations, tours, hotels..."
               autocomplete="off" aria-label="Quick search">
        <div class="quick-results" id="quick-results" hidden></div>
      </div>

      <div class="topbar-right">
        <div class="account-menu" data-dropdown>
          <button class="icon-btn" type="button" data-dropdown-toggle aria-label="Notifications" aria-haspopup="true">
            <?= icon('bell', '', 20) ?>
            <?php if ($unread > 0): ?><span class="icon-badge"><?= (int) $unread > 99 ? '99+' : (int) $unread ?></span><?php endif; ?>
          </button>
          <div class="dropdown-menu dropdown-notif" data-dropdown-menu>
            <div class="dropdown-head"><strong>Notifications</strong>
              <a href="<?= e(url('/admin/notifications')) ?>">View all</a></div>
            <div id="topbar-notifs" class="notif-mini"><p class="notif-loading">Loading…</p></div>
          </div>
        </div>

        <div class="account-menu" data-dropdown>
          <button class="account-trigger" type="button" data-dropdown-toggle aria-haspopup="true" aria-expanded="false">
            <span class="avatar avatar-sm"><?= e(initials($adminUser['name'] ?? 'Admin')) ?></span>
            <span class="account-name"><?= e(excerpt($adminUser['name'] ?? 'Admin', 16)) ?></span>
            <?= icon('chevron-down', 'i-xs', 15) ?>
          </button>
          <div class="dropdown-menu" data-dropdown-menu role="menu">
            <div class="dropdown-head">
              <strong><?= e($adminUser['name'] ?? '') ?></strong>
              <span><?= e($adminUser['email'] ?? '') ?></span>
            </div>
            <a role="menuitem" href="<?= e(url('/admin/profile')) ?>"><?= icon('user', 'i-sm') ?> My profile</a>
            <a role="menuitem" href="<?= e(url('/admin/settings')) ?>"><?= icon('settings', 'i-sm') ?> Settings</a>
            <span class="dropdown-sep" role="separator"></span>
            <a role="menuitem" class="is-danger" href="<?= e(url('/admin/logout')) ?>"><?= icon('log-out', 'i-sm') ?> Sign out</a>
          </div>
        </div>
      </div>
    </header>

    <main id="admin-main" class="admin-main">
      <?php foreach (flash_pull() as $f): ?>
        <div class="alert alert-<?= e($f['type']) ?>" role="alert">
          <?= icon($f['type'] === 'error' ? 'alert-circle' : 'check-circle', 'i-sm', 18) ?>
          <span><?= e($f['message']) ?></span>
        </div>
      <?php endforeach; ?>

      <?php echo $content; ?>
    </main>
  </div>
</div>

<div id="toast-root" class="toast-root" role="status" aria-live="polite"></div>
<script src="<?= e(asset('assets/js/app.js')) ?>" defer></script>
<script src="<?= e(asset('assets/js/admin.js')) ?>" defer></script>
</body>
</html>
