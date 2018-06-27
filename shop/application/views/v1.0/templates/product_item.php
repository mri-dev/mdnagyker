<div class="item">
  <?php
    $wo_price = ($without_price == '1') ? true : false;
    if( $akcios == '1' ) $ar = $akcios_fogy_ar;
  ?>
  <div class="wrapper">
    <div class="image">
      <?php if ( $akcios == '1' ): ?>
      <div class="discount-percent">
        <div class="p">
          -<? echo 100-round($akcios_fogy_ar / ($brutto_ar / 100)); ?>%
        </div>
      </div>
      <?php endif; ?>
			<a href="<?=$link?>"><img title="<?=$product_nev?>" src="<?=$profil_kep?>" alt="<?=$product_nev?>"></a>
      <div class="short-desc">
        <?php echo $rovid_leiras; ?>
      </div>
		</div>
    <div class="subtitle"><?=__($csoport_kategoria)?></div>
    <div class="title">
      <h3><a href="<?=$link?>"><?=$product_nev?></a></h3>
    </div>
    <?php if ($show_variation): ?>
    <div class="variation">
      <?php if (isset($meret)): ?>
        <span class="kiszereles" title="Kiszerelés"><?=$meret?>:</span>
      <?php endif; ?>
      <strong title="Termék variáció"><?=$szin?></strong>
    </div>
    <?php endif; ?>

    <div class="prices">
      <div class="wrapper <?=($wo_price)?'wo-price':''?>">
        <?php if ( $wo_price ): ?>
          <div class="ar">
            <strong>ÉRDEKLŐDJÖN!</strong><br>
            Kérje szakértőnk tanácsát!
          </div>
        <?php else: ?>
          <?php if ( $akcios == '1' ): ?>
            <div class="ar akcios">
              <div class="current"><?=Helper::cashFormat($ar)?> <?=$valuta?></div>
              <div class="old"><?=Helper::cashFormat($brutto_ar)?> <?=$valuta?></div>
            </div>
          <?php else: ?>
            <div class="ar">
              <div class="current"><?=Helper::cashFormat($ar)?> <?=$valuta?></div>
            </div>
          <?php endif; ?>
        <?php endif; ?>
      </div>
    </div>

    <div class="add">
      <button type="button" id="btn-add-p<?=$product_id?>" cart-data="<?=$product_id?>" cart-progress="btn-add-p<?=$product_id?>" cart-me="1" cart-remsg="cart-msg" class="cart tocart"><img src="<?=IMG?>shopcart-ico.svg" alt="Kosárba"> Kosárba</button>
    </div>

    <div class="buttons">
      <div class="link">
        <a href="<?=$link?>"><i class="fa fa-eye"></i> Megnézem</a>
      </div>
      <div class="fav" ng-class="(fav_ids.indexOf(<?=$product_id?>) !== -1)?'selected':''" title="Kedvencekhez adom" ng-click="productAddToFav(<?=$product_id?>, $event)">
        <i class="fa fa-star" ng-show="fav_ids.indexOf(<?=$product_id?>) !== -1"></i>
        <i class="fa fa-star-o" ng-show="fav_ids.indexOf(<?=$product_id?>) === -1"></i> Kedvencekhez
      </div>
    </div>
  </div>
</div>
