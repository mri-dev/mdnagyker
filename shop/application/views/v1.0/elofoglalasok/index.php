<?php
  $nums = $this->preorder->itemNums();
?>
<div class="elofoglalas-page">
  <div class="wrapper">
    <?php if ( $nums != 0 ): ?>
      <h1>Előfoglalások</h1>
      <div class="list">
        <div class="header">
          <div class="wrapper">
            <div class="status"></div>
            <div class="head"></div>
            <div class="price">Össz. ár</div>
            <div class="dateStart">Foglalás ideje</div>
            <div class="dateEnd">Foglalás lejár</div>
          </div>
        </div>
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
                  <a href="javascript:void(0);"><strong><?=$item_nums?> db</strong> tétel foglalva</a>
                </div>
                <div class="hashkey"><?=$item->getHashkey()?></div>
              </div>
              <div class="price">
                <?=\Helper::cashFormat($item->totalPrice())?> Ft
              </div>
              <div class="dateStart">
                <?=$item->dateStart(true)?>
              </div>
              <div class="dateEnd">
                <?=$item->dateEnd(true)?>
              </div>
            </div>
          </div>
          <div class="item-details" id="ref<?=$item->getHashkey()?>">
            <div class="header">
              <div class="wrapper">
                <div class="name">
                  Termék
                </div>
                <div class="me">
                  Me.
                </div>
                <div class="eprice">
                  Egységár
                </div>
                <div class="price">
                  Ár
                </div>
              </div>
            </div>
            <div class="items">
              <?php foreach ($items as $p): ?>
              <div class="product">
                <div class="wrapper">
                  <div class="img">
                    <a href="<?=$p['link']?>" target="_blank"><img src="<?=$p['profil_kep']?>" alt="<?=$p['nev']?>"></a>
                  </div>
                  <div class="name">
                    <strong><a href="<?=$p['link']?>" target="_blank"><?=$p['nev']?></a></strong>
                    <div class="code">
                      <?=$p['cikkszam']?>
                    </div>
                  </div>
                  <div class="me">
                    <?=$p['me']?> db
                  </div>
                  <div class="eprice">
                    <?=Helper::cashFormat($p['egysegAr'])?> Ft
                  </div>
                  <div class="price">
                    <?=Helper::cashFormat($p['ar'])?> Ft
                  </div>
                </div>
              </div>
              <?php endforeach; ?>
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
