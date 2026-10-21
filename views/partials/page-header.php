<?php
/** Standard inner-page hero. Vars: tag, title, sub, image(optional) */
$tag = $tag ?? '';
$title = $title ?? '';
$sub = $sub ?? '';
$image = $image ?? '';
$style = $image ? ' style="background-image:linear-gradient(180deg,rgba(11,15,10,.55),rgba(11,15,10,.92)),url(' . e(img_url($image)) . ')"' : '';
?>
<section class="page-hero"<?= $style ?>>
  <div class="container">
    <?php if ($tag): ?><p class="tagline"><?= e($tag) ?></p><?php endif; ?>
    <h1 class="page-title"><?= $title /* may contain safe accent span */ ?></h1>
    <?php if ($sub): ?><p class="page-sub"><?= e($sub) ?></p><?php endif; ?>
  </div>
</section>
