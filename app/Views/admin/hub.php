<?php /** @var string $title @var string $intro @var list<array{0:string,1:string,2:string}> $cards */ ?>
<div class="page-head"><div><h1><?= e($title) ?></h1><p class="sub"><?= e($intro) ?></p></div></div>
<div class="grid c3">
  <?php foreach ($cards as [$href, $name, $desc]): ?>
  <a class="stat" href="<?= e($href) ?>" style="text-decoration:none"><h3 style="margin-bottom:8px"><?= e($name) ?></h3><span><?= e($desc) ?></span></a>
  <?php endforeach; ?>
</div>
