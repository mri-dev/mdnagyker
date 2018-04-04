<div class="cat-menu">
  <div class="title">
    <i class="fa fa-bars"></i> Összes termék
  </div>
  <ul>
    <?php foreach ( $this->categories->tree  as $cat ) { ?>
    <li class="menu-item item<?=$cat['ID']?> deep<?=$cat['deep']?>">
      <a href="<?=$cat['link']?>"><?=$cat['neve']?></a><? if($cat['child']): ?><div class="toggler"><i class="fa fa-angle-right"></i></div><? endif; ?>
      <?php $child = $cat['child']; ?>
      <?php if ($child): ?>
      <ul class="sub">
        <li class="title"><?=$cat['neve']?></li>
        <?php foreach ( $child as $cat): ?>
          <li class="menu-item item<?=$cat['ID']?> deep<?=$cat['deep']?> childof<?=$cat['szulo_id']?>"><a href="<?=$cat['link']?>"><?=$cat['neve']?></a><? if($cat['child']): ?><div class="toggler"><i class="fa fa-angle-right"></i></div><? endif; ?></li>
        <?php $child = $cat['child']; endforeach; ?>
      </ul>
      <?php endif; ?>
    </li>
    <?php } ?>
  </ul>
</div>
