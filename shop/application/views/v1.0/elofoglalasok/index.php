<?php
  $nums = $this->preorder->itemNums();
?>
<div class="elofoglalas-page">
  <div class="wrapper">
    <?php if ( $nums != 0 ): ?>
      <h1>Előfoglalások</h1>
      <div class="list">
        <?php while ( $this->preorder->walk() ):
          $item = $this->preorder->the_item();
          $items = $item->getItems();
          $item_nums = $item->item_numbers;
        ?>
          <div class="item">
            <div class="wrapper">
              <div class="status">
                <i class="fa fa-pause-circle-o"></i>
              </div>
              <div class="head">
                <div class="item-info">
                  <?=$item_nums?> db tétel foglalva
                </div>
                <div class="hashkey"><?=$item->getHashkey()?></div>
              </div>
              <div class="price">
                <?=\Helper::cashFormat($item->totalPrice())?> Ft
              </div>
              <div class="dateStar">
                <?=$item->dateStart(true)?>
              </div>
              <div class="dateEnd">
                <?=$item->dateEnd(true)?>
              </div>
            </div>
          </div>
        <?php endwhile; ?>
      </div>
    <?php else: ?>
      <div class="no-foglalas">
        <div class="ico">
          <i class="fa fa-pause-circle-o"></i>
        </div>
        <h1>Előfoglalások</h1>
        <h3>Önnek jelenleg nincs aktív előfoglalása.</h3>
        <div class="desc">
          <i class="fa fa-question-circle-o"></i>
          <h4>Mit jelent az előfoglalás?</h4>
          Az Önnek megtetsző termékeket lefoglalhatja <strong><?=$this->settings['elofoglalas_ora']?> órára</strong>. A lefoglalt tételeket a megrendelés leadásáig elrakjuk, így más nem vásárolhatja meg.<br>Tegye kosárba a termékeket és válassza az előfoglalás opciót az előfoglalás rögzítéséhez.
        </div>
      </div>
    <?php endif; ?>
  </div>
</div>
