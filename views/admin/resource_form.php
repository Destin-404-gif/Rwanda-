<?php
/** Generic create/edit form (config-driven). */
$action = $isEdit
    ? '/admin/' . $resource . '/' . (int) $row['id'] . '/edit'
    : '/admin/' . $resource . '/new';
$readonly = $cfg['readonly'] ?? [];
$noEdit = $cfg['no_edit'] ?? [];

function admin_field_value(?array $row, array $f) {
    $v = $row[$f['name']] ?? null;
    if ($v === null) return null;
    if (($f['type'] ?? '') === 'lines' || ($f['type'] ?? '') === 'keyed_lines') {
        $decoded = jarr($v);
        if (($f['type'] ?? '') === 'lines') return implode("\n", $decoded);
        $out = [];
        foreach ($decoded as $item) $out[] = ($item['title'] ?? '') . ' | ' . ($item['text'] ?? '');
        return implode("\n", $out);
    }
    return $v;
}

function admin_relation_options(array $f, $current): string {
    $table = $f['table']; $col = $f['label_col'];
    try {
        $opts = db_all("SELECT id, $col AS label FROM $table ORDER BY $col ASC");
    } catch (Throwable $e) {
        return '<option value="">Table unavailable</option>';
    }
    $html = '<option value="">— none —</option>';
    foreach ($opts as $o) {
        $sel = ((string) $current !== '' && (string) $current === (string) $o['id']) ? ' selected' : '';
        $html .= '<option value="' . (int) $o['id'] . '"' . $sel . '>' .
            e($o['label'] ?: ('#' . $o['id'])) . '</option>';
    }
    return $html;
}
?>
<div class="page-top">
  <div>
    <p class="tagline"><?= $isEdit ? 'Editing' : 'Creating' ?></p>
    <h1 class="admin-title"><?= $isEdit
        ? 'Edit ' . e($cfg['singular']) : 'New ' . e(strtolower($cfg['singular'])) ?></h1>
    <p class="admin-sub"><?= $isEdit
        ? 'Record #' . (int) $row['id'] : 'All required fields are marked with *.' ?></p>
  </div>
  <div class="page-top-actions">
    <a class="btn btn-ghost" href="<?= e(url('/admin/' . $resource)) ?>">
      <?= icon('arrow-left', 'i-sm', 16) ?> Back to list
    </a>
  </div>
</div>

<form class="glass panel resource-form" method="post" action="<?= e(url($action)) ?>" enctype="multipart/form-data">
  <?= csrf_field() ?>

  <?php foreach ($cfg['fields'] as $f):
      $name = $f['name']; $type = $f['type'] ?? 'text';
      $label = $f['label'] ?? $name;
      $isReadonly = in_array($name, $readonly, true);
      $locked = $isReadonly || in_array($name, $noEdit, true) && $isEdit;
      $value = admin_field_value($row, $f);
      if ($value === null && !$isEdit && isset($f['default'])) $value = $f['default'];
      $id = 'f-' . $name;
      if ($type === 'checkbox' || $type === 'image' || $type === 'lines' || $type === 'keyed_lines' || $type === 'textarea') {
          $full = true;
      } else {
          $full = in_array($type, ['textarea'], true);
      }
  ?>
    <?php if ($type === 'checkbox'): ?>
      <label class="checkbox-field<?= $locked ? ' is-locked' : '' ?>">
        <input type="checkbox" name="<?= e($name) ?>" value="1" id="<?= e($id) ?>"
               <?= !empty($value) ? 'checked' : '' ?><?= $locked ? ' disabled' : '' ?>>
        <span><?= e($label) ?></span>
      </label>

    <?php elseif ($type === 'image'): ?>
      <fieldset class="field field-image<?= $full ? ' field-full' : '' ?>">
        <legend><?= e($label) ?><?= ($f['required'] ?? false) ? ' *' : '' ?></legend>
        <div class="image-field">
          <label class="image-pick" for="<?= e($id) ?>">
            <?= icon('image', '', 22) ?>
            <span>Choose image<small>JPG / PNG / WebP / GIF, max 6 MB</small></span>
            <input type="file" name="<?= e($name) ?>" id="<?= e($id) ?>"
                   accept="image/jpeg,image/png,image/webp,image/gif"<?= $locked ? ' disabled' : '' ?>>
          </label>
          <div class="image-preview" data-image-preview>
            <?php if (!empty($value)): ?>
              <img src="<?= e(img_url((string) $value)) ?>" alt="Current image">
              <small class="image-path mono"><?= e($value) ?></small>
            <?php else: ?>
              <span class="image-placeholder">No image yet</span>
            <?php endif; ?>
          </div>
        </div>
        <p class="form-note">Uploaded to <code>uploads/</code> and auto-resized to 1920px.
          On InfinityFree keep <code>uploads/</code> and <code>tmp/</code> at 777 (see DEPLOY.md).</p>
      </fieldset>

    <?php elseif ($type === 'relation'): ?>
      <label class="field">
        <span><?= e($label) ?><?= ($f['required'] ?? false) ? ' *' : '' ?></span>
        <select name="<?= e($name) ?>" id="<?= e($id) ?>"<?= $locked ? ' disabled' : '' ?>>
          <?= admin_relation_options($f, $value) ?>
        </select>
      </label>

    <?php elseif ($type === 'select'): ?>
      <label class="field">
        <span><?= e($label) ?><?= ($f['required'] ?? false) ? ' *' : '' ?></span>
        <select name="<?= e($name) ?>" id="<?= e($id) ?>"<?= $locked ? ' disabled' : '' ?>>
          <?php foreach (($f['options'] ?? []) as $optVal => $optLabel): ?>
            <option value="<?= e($optVal) ?>"<?= (string) $value === (string) $optVal ? ' selected' : '' ?>>
              <?= e($optLabel) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </label>

    <?php elseif ($type === 'textarea'): ?>
      <label class="field field-full">
        <span><?= e($label) ?><?= ($f['required'] ?? false) ? ' *' : '' ?></span>
        <textarea name="<?= e($name) ?>" id="<?= e($id) ?>" rows="<?= (int) ($f['rows'] ?? 5) ?>"
                  <?= $locked ? 'disabled' : '' ?>><?= e((string) ($value ?? '')) ?></textarea>
      </label>

    <?php elseif ($type === 'lines' || $type === 'keyed_lines'): ?>
      <label class="field field-full">
        <span><?= e($label) ?></span>
        <textarea name="<?= e($name) ?>" id="<?= e($id) ?>" rows="5" class="mono-input"
                  placeholder="<?= $type === 'lines' ? 'One item per line' : 'Day title | description of the day' ?>"
                  <?= $locked ? 'disabled' : '' ?>><?= e((string) ($value ?? '')) ?></textarea>
        <small class="field-help"><?= $type === 'lines'
            ? 'One item per line - stored as JSON.'
            : 'One itinerary step per line, separated by | (title | text).' ?></small>
      </label>

    <?php elseif ($type === 'slug'): ?>
      <label class="field">
        <span><?= e($label) ?></span>
        <input type="text" name="<?= e($name) ?>" id="<?= e($id) ?>" maxlength="180"
               value="<?= e((string) ($value ?? '')) ?>" placeholder="auto-generated-from-title"
               data-slug-source="<?= e($f['from'] ?? '') ?>"<?= $locked ? ' readonly' : '' ?>>
        <small class="field-help">URL: /<?= e($resource) ?>/<?= e((string) ($value ?? '{slug}')) ?></small>
      </label>

    <?php elseif ($type === 'number'): ?>
      <label class="field">
        <span><?= e($label) ?><?= ($f['required'] ?? false) ? ' *' : '' ?></span>
        <input type="number" name="<?= e($name) ?>" id="<?= e($id) ?>" step="1"
               value="<?= e((string) ($value ?? 0)) ?>"<?= $locked ? ' readonly' : '' ?>>
      </label>

    <?php elseif ($type === 'decimal'): ?>
      <label class="field">
        <span><?= e($label) ?><?= ($f['required'] ?? false) ? ' *' : '' ?></span>
        <input type="number" name="<?= e($name) ?>" id="<?= e($id) ?>" step="0.01" min="0"
               value="<?= e((string) ($value ?? 0)) ?>"<?= $locked ? ' readonly' : '' ?>>
      </label>

    <?php elseif ($type === 'date'): ?>
      <label class="field">
        <span><?= e($label) ?><?= ($f['required'] ?? false) ? ' *' : '' ?></span>
        <input type="date" name="<?= e($name) ?>" id="<?= e($id) ?>"
               value="<?= e((string) ($value ?? '')) ?>"<?= $locked ? ' readonly' : '' ?>>
      </label>

    <?php elseif ($type === 'datetime'): ?>
      <label class="field">
        <span><?= e($label) ?></span>
        <input type="datetime-local" name="<?= e($name) ?>" id="<?= e($id) ?>"
               value="<?= e($value ? date('Y-m-d\TH:i', strtotime((string) $value)) : '') ?>"<?= $locked ? ' readonly' : '' ?>>
      </label>

    <?php else: /* text */ ?>
      <label class="field">
        <span><?= e($label) ?><?= ($f['required'] ?? false) ? ' *' : '' ?></span>
        <input type="text" name="<?= e($name) ?>" id="<?= e($id) ?>" maxlength="255"
               value="<?= e((string) ($value ?? '')) ?>"<?= $locked ? ' readonly' : '' ?>>
        <?php if ($isReadonly): ?><small class="field-help">Read-only field.</small><?php endif; ?>
      </label>
    <?php endif; ?>
  <?php endforeach; ?>

  <div class="form-actions">
    <button class="btn btn-primary btn-lg" type="submit">
      <?= icon('check', 'i-sm', 16) ?> <?= $isEdit ? 'Save changes' : 'Create ' . strtolower($cfg['singular']) ?>
    </button>
    <a class="btn btn-ghost btn-lg" href="<?= e(url('/admin/' . $resource)) ?>">Cancel</a>
    <?php if ($isEdit): ?>
      <button class="btn btn-ghost btn-lg is-danger" type="submit"
              formaction="<?= e(url('/admin/' . $resource . '/' . (int) $row['id'] . '/delete')) ?>"
              data-submit-confirm="Delete this <?= e($cfg['singular']) ?> permanently?">
        <?= icon('trash-2', 'i-sm', 16) ?> Delete
      </button>
    <?php endif; ?>
  </div>
</form>
