<div class="sidebar-holder">
    <?php if ( !$this->hide_categories ): ?>
      <? $this->render('templates/sidebar_menu'); ?>
    <?php endif; ?>

    <?php if ( $this->product_listing ): ?>
    <form class="" action="" method="get">
      <div class="filters side-group">
        <div class="head">
          Keresés tulajdonságok szerint
        </div>
        <?php if ( !empty($this->productFilters) ): ?>
          <?php foreach ( $this->productFilters as $pf ):

            if(count($pf[hints]) == 0): continue; endif;
            if( ($pf[type] != 'tartomany' && $pf[type] != 'szam') &&  count($pf[hints]) <= 1) continue;
            if( ($pf[type] == 'tartomany' || $pf[type] == 'szam') &&  count($pf[hints]) <= 1) continue;
          ?>
          <div class="section-group filter-row">
            <strong><?php echo $pf['parameter']; ?></strong> <?=($pf['me'] != '')?'('.$pf['me'].')':''?>
          </div>
          <div class="section-wrapper type-<?=$pf[type]?>">
              <input type="hidden" name="fil_p_<?=$pf[ID]?>" id="p_<?=$pf[ID]?>_v" />
              <div id="pmf_<?=$pf[ID]?>">
                 <? if($pf[type] == 'tartomany'): ?>
                 <div class="pos_rel">
                    <input mode="minmax" id="r<?=$pf[ID]?>_range_min" type="hidden" name="fil_p_<?=$pf[ID]?>_min" value="<?=$_GET['fil_p_'.$pf[ID].'_min']?>" class="form-control <?=($_GET['fil_p_'.$pf[ID].'_min'])?'filtered':''?>" />
                    <input mode="minmax" id="r<?=$pf[ID]?>_range_max" type="hidden" name="fil_p_<?=$pf[ID]?>_max" value="<?=$_GET['fil_p_'.$pf[ID].'_max']?>" class="form-control <?=($_GET['fil_p_'.$pf[ID].'_max'])?'filtered':''?>" />
                    <div class="range" key="r<?=$pf[ID]?>" smin="<?=$_GET['fil_p_'.$pf[ID].'_min']?>" smax="<?=$_GET['fil_p_'.$pf[ID].'_max']?>" amin="<?=$pf[minmax][min]?>" amax="<?=$pf[minmax][max]?>"></div>
                    <div class="rangeInfo">
                       <div class="col-md-6 def"><em>(<?=$pf[minmax][min]?> - <?=$pf[minmax][max]?>)</em></div>
                       <div class="col-md-6 sel" align="right"><span id="r<?=$pf[ID]?>_range_info_min"><?=$_GET['fil_p_'.$pf[ID].'_min']?></span> - <span id="r<?=$pf[ID]?>_range_info_max"><?=$_GET['fil_p_'.$pf[ID].'_max']?></span></div>
                    </div>
                 </div>
                 <? elseif($pf[type] == 'szam'): ?>
                 <div class="pos_rel">
                    <input mode="minmax" id="r<?=$pf[ID]?>_range_min" type="hidden" name="fil_p_<?=$pf[ID]?>_min" value="<?=$_GET['fil_p_'.$pf[ID].'_min']?>" class="form-control <?=($_GET['fil_p_'.$pf[ID].'_min'])?'filtered':''?>" />
                    <input mode="minmax" id="r<?=$pf[ID]?>_range_max" type="hidden" name="fil_p_<?=$pf[ID]?>_max" value="<?=$_GET['fil_p_'.$pf[ID].'_max']?>" class="form-control <?=($_GET['fil_p_'.$pf[ID].'_max'])?'filtered':''?>" />
                    <div class="range" key="r<?=$pf[ID]?>" smin="<?=$_GET['fil_p_'.$pf[ID].'_min']?>" smax="<?=$_GET['fil_p_'.$pf[ID].'_max']?>" amin="<?=$pf[minmax][min]?>" amax="<?=$pf[minmax][max]?>"></div>
                    <div class="rangeInfo">
                       <div class="col-md-6 def"><em>(<?=$pf[minmax][min]?> - <?=$pf[minmax][max]?>)</em></div>
                       <div class="col-md-6 sel" align="right"><span id="r<?=$pf[ID]?>_range_info_min"><?=$_GET['fil_p_'.$pf[ID].'_min']?></span> - <span id="r<?=$pf[ID]?>_range_info_max"><?=$_GET['fil_p_'.$pf[ID].'_max']?></span></div>
                    </div>
                 </div>
                 <? else: ?>
                 <div class="selectors">
                    <?php if (count($pf[hints]) > 0): ?>
                    <div class="sel-item-n">
                      <?=count($pf[hints])?>
                    </div>
                    <?php endif; ?>
                    <div class="selector" key="p_<?=$pf[ID]?>" id="p_<?=$pf[ID]?>">összes</div>
                    <div class="selectorHint p_<?=$pf[ID]?>" style="display:none;">
                       <ul>
                          <? foreach($pf[hints] as $h): ?>
                          <li><label><input type="checkbox" <?=(in_array($h,$this->filters['fil_p_'.$pf[ID]]))?'checked':''?> for="p_<?=$pf[ID]?>" text="<?=$h?>" value="<?=$h?>" /> <?=$h?> <?=$pf[mertekegyseg]?></label></li>
                          <? endforeach;?>
                       </ul>
                    </div>
                 </div>
                 <? endif; ?>
              </div>
           </div>
          <?php endforeach; ?>
        <?php endif; ?>
        <div class="section-group">
          Rendezés
        </div>
        <div class="section-wrapper">
          <select name="order" class="form-control">
            <option value="ar_asc" selected="selected">Ár: növekvő</option>
            <option value="ar_desc">Ár: csökkenő</option>
            <option value="nev_asc">Név: A-Z</option>
            <option value="nev_desc">Név: Z-A</option>
          </select>
        </div>
        <div class="action-group">
          <button type="submit">Szűrés <i class="fa fa-refresh"></i></button>
        </div>
      </div>
    </form>
  <?php endif; // End of product_listing ?>

  <? if( $this->live_products_list ): ?>
  <div class="liveproducts side-group">
    <div class="head">
      Most <strong>nézik</strong>
    </div>
    <div class="wrapper">
      <div class="product-side-items imaged-style">
        <? foreach ( $this->live_products_list as $livep ) { ?>
        <div class="item">
          <div class="img">
            <a href="<?php echo $livep['link']; ?>"><img src="<?php echo $livep['profil_kep']; ?>" alt="<?php echo $livep['product_nev']; ?>"></a>
          </div>
          <div class="data">
            <a href="<?php echo $livep['link']; ?>">
              <div class="name">
                <?php echo $livep['product_nev']; ?>
              </div>
              <div class="desc">
                <?php echo $livep['csoport_kategoria']; ?>
              </div>
            </a>
          </div>
        </div>
        <? } ?>
      </div>
    </div>
  </div>
  <? endif; ?>

  <? if( $this->top_products && $this->top_products->hasItems() ): ?>
  <div class="topproducts side-group">
    <div class="head">
      Legtöbbet <strong>vásárolt</strong>
    </div>
    <div class="wrapper">
      <div class="product-side-items imaged-style">
        <? foreach ( $this->top_products_list as $topp ) { ?>
          <div class="item">
            <div class="img">
              <a href="<?php echo $livep['link']; ?>"><img src="<?php echo $livep['profil_kep']; ?>" alt="<?php echo $livep['product_nev']; ?>"></a>
            </div>
            <div class="data">
              <a href="<?php echo $livep['link']; ?>">
                <div class="name">
                  <?php echo $livep['product_nev']; ?>
                </div>
                <div class="desc">
                  <?php echo $livep['csoport_kategoria']; ?>
                </div>
              </a>
            </div>
          </div>
        <? } ?>
      </div>
    </div>
  </div>
  <? endif; ?>

  <? if( $this->viewed_products_list ): ?>
  <div class="lastviewed side-group">
    <div class="head">
      Utoljára megtekintettek
    </div>
    <div class="wrapper">
      <div class="product-side-items imaged-style">
        <? foreach ( $this->viewed_products_list as $viewed ) { ?>
          <div class="item">
            <div class="img">
              <a href="<?php echo $livep['link']; ?>"><img src="<?php echo $livep['profil_kep']; ?>" alt="<?php echo $livep['product_nev']; ?>"></a>
            </div>
            <div class="data">
              <a href="<?php echo $livep['link']; ?>">
                <div class="name">
                  <?php echo $livep['product_nev']; ?>
                </div>
                <div class="desc">
                  <?php echo $livep['csoport_kategoria']; ?>
                </div>
              </a>
            </div>
          </div>
        <? } ?>
      </div>
    </div>
  </div>
  <? endif; ?>

</div>
