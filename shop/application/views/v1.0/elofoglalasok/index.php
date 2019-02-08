<?php
  $nums = $this->preorder->itemNums();
?>
<div class="elofoglalas-page">
  <div class="wrapper">
    <?php if ( $nums != 0 ): ?>
      <h1>Előfoglalások</h1>
      <?php if (isset($_GET['created']) && $_GET['created'] == '1'): ?>
        <br>
        <?php echo \Helper::makeAlertMsg('pSuccess', 'Sikeresen rögzítette az előfoglalást! A lefoglalt termékeket '.$this->settings['elofoglalas_ora'].' órán belül kizárólag Ön vásárolhatja meg.'); ?>
      <?php endif; ?>
      <div class="list">
        <div class="header">
          <div class="wrapper">
            <div class="status"></div>
            <div class="head"></div>
            <div class="price">Össz. ár</div>
            <div class="dateStart">Foglalás ideje</div>
            <div class="dateEnd">Hátralévő idő</div>
          </div>
        </div>
        <?php while ( $this->preorder->walk() ):
          $item = $this->preorder->the_item();
          $items = $item->getItems();
          $item_nums = $item->item_numbers;
        ?>
          <div class="item">
            <div class="wrapper">
              <div class="status" style="background:<?=$item->expireColor()?>;">
                <i class="fa fa-pause-circle-o"></i>
              </div>
              <div class="head">
                <div class="item-info">
                  <?php if (isset($_GET['session']) && $_GET['session'] == $item->getHashkey()): ?>
                    <i class="fa fa-thumb-tack"></i>
                  <?php endif; ?>
                  <a href="javascript:void(0);" data-show-details="<?=$item->getHashkey()?>"><strong><?=$item_nums?> db</strong> tétel foglalva</a>
                </div>
                <div class="hashkey"><?=$item->getHashkey()?></div>
              </div>
              <div class="price">
                <?=\Helper::cashFormat($item->totalPrice())?> Ft
              </div>
              <div class="dateStart">
                <?=$item->dateStart(true)?>
              </div>
              <div class="dateEnd" title="<?=$item->dateEnd(true)?>">
                <strong style="color:<?=$item->expireColor()?>;"><?=\Helper::distanceDate($item->dateEnd(false))?></strong> <?php if ($item->expired()): ?>lejárt<?php endif; ?>
              </div>
            </div>
          </div>
          <div class="item-details <?php if (isset($_GET['session']) && $_GET['session'] == $item->getHashkey()): ?>thumbed<?php endif; ?>" id="ref<?=$item->getHashkey()?>" <?=(isset($_GET['session']) && $_GET['session'] == $item->getHashkey())?'style="display:block;"':''?>>
            <div class="holder">
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
            <div class="actions">
              <?php if (!$item->expired()): ?>
              <form class="" action="" method="post">
                <button type="submit" class="cancel" name="cancelPreorder" value="<?=$item->getHashkey()?>">Foglalás törlése <i class="fa fa-trash"></i></button>
                <button type="submit" class="submit" name="startOrder" value="<?=$item->getHashkey()?>">Lefoglalt termékek megvásárlása <i class="fa fa-arrow-circle-right"></i></button>
              </form>
              <div class="clr"></div>
              <?php endif; ?>
            </div>
          </div>
        <?php endwhile; ?>
      </div>
      <script type="text/javascript">
        $(function(){
          $('*[data-show-details]').click(function(){
            var ref = $(this).data('show-details');
            $('.item-details:not(#ref'+ref+')').slideUp(400);
            $('.item-details#ref'+ref).slideDown(400);
          });
        });
      </script>
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
          Az Önnek megtetsző termékeket lefoglalhatja <strong><?=$this->settings['elofoglalas_ora']?> órára</strong>. A lefoglalt tételeket a megrendelés leadásáig elrakjuk üzleti partnereinkek, így más nem vásárolhatja meg.<br>Tegye kosárba a termékeket és válassza az előfoglalás opciót az előfoglalás rögzítéséhez.<br><br>
          <strong style="color:#e07d80;">A szolgáltatás kizárólag nagykereskedelmi és viszonteladó felhasználóinknak érhető el!</strong>
        </div>
      </div>
    <?php endif; ?>
  </div>
</div>
