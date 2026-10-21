<?php
/** Generic CRUD list view (config-driven). */
$isNewDisabled = !empty($cfg['no_create']);
$titleCol = $cfg['title_col'] ?? 'id';
$subCol = $cfg['sub_col'] ?? null;
$imageCol = $cfg['image_col'] ?? null;
$statusOptions = [];
foreach ($cfg['fields'] as $f) {
    if (($f['name'] ?? '') === 'status' && isset($f['options'])) $statusOptions = $f['options'];
}
?>
<div class="page-top">
  <div>
    <p class="tagline">Manage</p>
    <h1 class="admin-title"><?= e($cfg['label']) ?></h1>
    <p class="admin-sub"><?= (int) $total ?> record<?= (int) $total === 1 ? '' : 's' ?><?= $q ? ' matching &ldquo;' . e($q) . '&rdquo;' : '' ?></p>
  </div>
  <div class="page-top-actions">
    <a class="btn btn-ghost" href="<?= e(url('/api/admin/' . $resource)) ?>" target="_blank" rel="noopener">
      <?= icon('database', 'i-sm', 16) ?> JSON
    </a>
    <?php if (!$isNewDisabled): ?>
      <a class="btn btn-primary" href="<?= e(url('/admin/' . $resource . '/new')) ?>">
        <?= icon('plus', 'i-sm', 16) ?> New <?= e(strtolower($cfg['singular'])) ?>
      </a>
    <?php endif; ?>
  </div>
</div>

<section class="glass panel">
  <div class="panel-head panel-head-forms">
    <form class="inline-search" method="get" action="<?= e(url('/admin/' . $resource)) ?>" role="search">
      <label class="sr-only" for="q">Search <?= e($cfg['label']) ?></label>
      <?= icon('search', 'input-icon', 16) ?>
      <input id="q" type="search" name="q" value="<?= e($q) ?>" placeholder="Search <?= e(strtolower($cfg['label'])) ?>..." maxlength="80">
      <button class="btn btn-primary btn-sm" type="submit">Search</button>
      <?php if ($q): ?><a class="link-secondary" href="<?= e(url('/admin/' . $resource)) ?>">Clear</a><?php endif; ?>
    </form>
    <span class="panel-meta"><?= $pages > 1 ? "Page $page of $pages" : count($rows) . ' shown' ?></span>
  </div>

  <?php if (!$rows): ?>
    <p class="empty-state">
      <?= $q ? 'Nothing matches your search.' : 'No ' . strtolower($cfg['label']) . ' yet.' ?>
      <?php if (!$isNewDisabled): ?>
        <a href="<?= e(url('/admin/' . $resource . '/new')) ?>">Create the first one</a>.
      <?php endif; ?>
    </p>
  <?php else: ?>
    <div class="table-wrap">
      <table class="admin-table">
        <thead>
          <tr>
            <th class="th-media">Media</th>
            <th><?= e($cfg['singular']) ?></th>
            <?php if ($subCol): ?><th>Details</th><?php endif; ?>
            <th>Status</th>
            <th class="th-actions">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($rows as $row): ?>
            <tr>
              <td class="td-media">
                <?php if ($imageCol && !empty($row[$imageCol])): ?>
                  <img class="row-thumb" src="<?= e(img_url($row[$imageCol])) ?>" alt="" loading="lazy">
                <?php else: ?>
                  <span class="row-thumb row-thumb-empty"><?= icon($cfg['icon'], 'i-sm', 16) ?></span>
                <?php endif; ?>
              </td>
              <td>
                <a class="row-title" href="<?= e(url('/admin/' . $resource . '/' . (int) $row['id'] . '/edit')) ?>">
                  <?= e($row[$titleCol] ?? ('#' . $row['id'])) ?>
                </a>
                <small class="cell-sub mono">#<?= (int) $row['id'] ?></small>
              </td>
              <?php if ($subCol): ?>
                <td><?= e(excerpt((string) ($row[$subCol] ?? ''), 60)) ?></td>
              <?php endif; ?>
              <td>
                <?php if (isset($row['status'])): ?>
                  <span class="badge badge-<?= e($row['status']) ?>"><?= e($statusOptions[$row['status']] ?? $row['status']) ?></span>
                <?php elseif (isset($row['payment_status'])): ?>
                  <span class="badge badge-<?= e($row['payment_status']) ?>"><?= e(str_replace('_', ' ', $row['payment_status'])) ?></span>
                <?php else: ?>
                  <span class="badge badge-info">-</span>
                <?php endif; ?>
              </td>
              <td class="table-actions">
                <a class="icon-btn" href="<?= e(url('/admin/' . $resource . '/' . (int) $row['id'] . '/edit')) ?>"
                   aria-label="Edit"><?= icon('pencil', 'i-sm', 16) ?></a>
                <form method="post" action="<?= e(url('/admin/' . $resource . '/' . (int) $row['id'] . '/delete')) ?>"
                      data-confirm="Delete <?= e($cfg['singular']) ?> #<?= (int) $row['id'] ?>? This cannot be undone.">
                  <?= csrf_field() ?>
                  <button class="icon-btn is-danger" type="submit" aria-label="Delete"><?= icon('trash-2', 'i-sm', 16) ?></button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <?php if ($pages > 1): ?>
      <nav class="pagination" aria-label="Pagination">
        <?php for ($p = 1; $p <= $pages; $p++): ?>
          <a class="page-link<?= $p === $page ? ' is-active' : '' ?>"
             href="<?= e(url('/admin/' . $resource . '?page=' . $p . ($q ? '&q=' . urlencode($q) : ''))) ?>"><?= $p ?></a>
        <?php endfor; ?>
      </nav>
    <?php endif; ?>
  <?php endif; ?>
</section>
