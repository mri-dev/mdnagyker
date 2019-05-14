<?php
  $ar = $this->product['ar'];
?>
<div class="product-view">
  <div class="product-data">
    <div class="page-width">
      <?php if ($this->product_nav_row): ?>
      <div class="product-row-nav">
        <div class="prev">
          <a href="<?php echo $this->product_nav_row['prev']['link']; ?>"><i class="fa fa-angle-left"></i> <?php echo $this->product_nav_row['prev']['nev']; ?></a>
        </div>
        <div class="next">
          <a href="<?php echo $this->product_nav_row['next']['link']; ?>"><?php echo $this->product_nav_row['next']['nev']; ?> <i class="fa fa-angle-right"></i></a>
        </div>
      </div>
      <?php endif; ?>
      <div class="top-datas">
        <div class="images">
          <?php if (true): ?>
          <div class="main-img by-width autocorrett-height-by-width" data-image-ratio="4:3">
            <?  if( $this->product['akcios'] == '1' && $this->product['akcio']['szazalek'] > 0): ?>
            <div class="discount-percent"><div class="p">-<? echo $this->product['akcio']['szazalek']; ?>%</div></div>
            <? endif; ?>
            <div class="img-thb">
                <a href="<?=$this->product['profil_kep']?>" class="zoom"><img di="<?=$this->product['profil_kep']?>" src="<?=$this->product['profil_kep']?>" alt="<?=$this->product['nev']?>"></a>
            </div>
          </div>
          <div class="all">
            <?  foreach ( (array)$this->product['images'] as $img ) { ?>
            <div class="imgslide">
              <div class="wrp autocorrett-height-by-width" data-image-ratio="4:3">
                <img class="aw" i="<?=\PortalManager\Formater::productImage($img)?>" src="<?=\PortalManager\Formater::productImage($img)?>" alt="<?=$this->product['nev']?>">
              </div>
            </div>
            <? } ?>
          </div>
          <?php endif; ?>
        </div>
        <div class="main-data">
          <?php if ( true ): ?>
          <h1><?=$this->product['nev']?></h1>
          <div class="csoport">
            <?=$this->product['csoport_kategoria']?>
          </div>
          <div class="nav">
            <div class="pagi">
              <?php
                $navh = '/termekek/';
                $lastcat = $this->product['in_cats']['name'][0];
              ?>
              <ul class="cat-nav">
                <li><a href="/"><i class="fa fa-home"></i></a></li>
                <li><a href="<?=$navh?>">Webshop</a></li>
                <?php if ( !$this->product['nav'][0] && $lastcat ): ?>
                <li><a href="<?=$this->product['in_cats']['url'][0]?>"><?php echo $lastcat; ?></a></li>
                <?php endif; ?>
                <?php
                foreach ( (array)$this->product['nav'] as $nav ): $navh = \Helper::makeSafeUrl($nav['neve'],'_-'.$nav['ID']); ?>
                <li><a href="/termekek/<?=$navh?>"><?php echo $nav['neve']; ?></a></li>
                <?php endforeach; ?>
              </ul>
            </div>
          </div>

          <div class="prices">
              <div class="base">
                <?php if ($this->product['without_price']): ?>
                  <div class="current">
                    ÉRDEKLŐDJÖN!
                  </div>
                <?php else: ?>
                  <?php
                    $price_title_prefix = 'Kiskeredkedelmi';
                    $show_kisker_prices = false;
                    if ($this->user) {
                      switch ($this->user['data']['price_group_title']) {
                        case 'Viszonteladó':
                          $price_title_prefix = 'Viszonteladói';
                          $show_kisker_prices = true;
                        break;
                        case 'Nagyker vásárló':
                          $price_title_prefix = 'Nagykereskedői';
                          $show_kisker_prices = true;
                        break;
                      }
                    }
                  ?>
                  <?php if ($this->user && $this->user[data][user_group] == 'company'): ?>
                  <div class="netto">
                    <div class="pricehead"><?=$price_title_prefix?> <strong>nettó ár</strong>:</div>
                    <span class="price"><?=\PortalManager\Formater::cashFormat($ar/1.27)?> <?=$this->valuta?><? if($this->product['mertekegyseg'] != ''): ?><span class="unit-text">/<?=($this->product['mertekegyseg_ertek']!=1)?$this->product['mertekegyseg_ertek']:''?><?=$this->product['mertekegyseg']?></span><? endif; ?></span>
                  </div>
                  <?php endif; ?>
                  <div class="brutto">
                    <div class="pricehead"><?=$price_title_prefix?> <strong>bruttó ár</strong>:</div>
                    <span class="price current <?=( $this->product['akcios'] == '1' && $this->product['akcio']['mertek'] > 0)?'discounted':''?>"><?=\PortalManager\Formater::cashFormat($ar)?> <?=$this->valuta?><? if($this->product['mertekegyseg'] != ''): ?><span class="unit-text">/<?=($this->product['mertekegyseg_ertek']!=1)?$this->product['mertekegyseg_ertek']:''?><?=$this->product['mertekegyseg']?></span><? endif; ?></span>
                    <?  if( $this->product['akcios'] == '1' && $this->product['akcio']['mertek'] > 0):
                        $ar = $this->product['eredeti_ar'];
                    ?>
                    <div class="price old"><strike><?=\PortalManager\Formater::cashFormat($ar)?> <?=$this->valuta?><? if($this->product['mertekegyseg'] != ''): ?><span class="unit-text">/<?=$this->product['mertekegyseg']?></span><? endif; ?></strike></div>
                    <? endif; ?>
                  </div>
                  <?php if ($show_kisker_prices && $this->product[kisker_ar] && $this->product[kisker_ar][brutto] != '0'): ?>
                  <div class="kisker-addon-price">
                    <div class="pricehead">Kiskereskedelmi ár:</div>
                    <span class="price"><?php echo \PortalManager\Formater::cashFormat($this->product['kisker_ar']['brutto']); ?> <?=$this->valuta?> <span class="net">(<?php echo \PortalManager\Formater::cashFormat($this->product['kisker_ar']['netto']); ?> <?=$this->valuta?> + ÁFA)</span></span>
                  </div>
                  <?php endif; ?>
                <?php endif; ?>
              </div>
              <div class="cimkek">
              <? if($this->product['akcios'] == '1'): ?>
                  <img src="<?=IMG?>discount_icon.png" title="Akciós!" alt="Akciós">
              <? endif; ?>
              <? if($this->product['ujdonsag'] == '1'): ?>
                  <img src="<?=IMG?>new_icon.png" title="Újdonság!" alt="Újdonság">
              <? endif; ?>
              </div>
          </div>

          <div class="divider"></div>
          <div class="status-params">
            <div class="avaibility">
              <div class="h">Elérhetőség:</div>
              <div class="v"><?=$this->product['keszlet_info']?></div>
            </div>
            <div class="transport">
              <div class="h">Várható szállítás:</div>
              <div class="v"><span><?=$this->product['szallitas_info']?></span></div>
            </div>
            <?php if ( $ar > $this->settings['FREE_TRANSPORT_ABOVEPRICE']): ?>
            <div class="free-transport">
              <div class="free-transport-ele">
                <img src="<?=IMG?>icons/transport.svg" alt="Ingyenes Szállítás"> Ezt a terméket ingyen szállítjuk
              </div>
            </div>
            <?php endif; ?>
          </div>
          <?php if (!empty($this->product['rovid_leiras'])): ?>
            <div class="divider"></div>
            <div class="short-desc">
              <?=$this->product['rovid_leiras']?>
            </div>
          <?php endif; ?>
          <?
          if( count($this->product['hasonlo_termek_ids']['colors']) > 1 ):
              $colorset = $this->product['hasonlo_termek_ids']['colors'];
          ?>
          <div class="variation-header">
            Elérhető variációk:
          </div>
          <div class="variation-list">
          <? foreach ($colorset as $szin => $adat ) : ?>
            <div class="variation<?=($szin == $this->product['szin'] )?' actual':''?>"><a href="<?=$adat['link']?>"><?=$szin?></a></div>
          <? endforeach; ?>
          </div>
          <? endif; ?>

          <div class="divider"></div>

          <div class="cart-info">
            <div id="cart-msg"></div>
            <div class="group">
              <div class="configs full">
                <div class="list">
                  <?php foreach ((array)$this->product[variation_config] as $vconf): if(count($vconf['values']) == 0) continue; ?>
                  <div class="conf">
                    <label for="conf_c<?=$vconf['ID']?>"><?=$vconf['parameter']?>:</label>
                    <div class="sel-inp-wrapper">
                      <select data-paramid="<?=$vconf['ID']?>" name="config[<?=$vconf['ID']?>]">
                        <?php foreach ((array)$vconf['values'] as $cv): if($cv['lathato']==0) continue; ?>
                        <option value="<?=$cv['ID']?>" <?=($cv['selected']==1)?'selected="selected"':''?>><?=$cv['value']?></option>
                        <?php endforeach; ?>
                      </select>
                    </div>
                  </div>
                  <?php endforeach; ?>
                </div>
              </div>
            </div>

            <? if($this->settings['stock_outselling'] == '0' && $this->product['raktar_keszlet'] <= 0): ?>
            <div class="out-of-stock">
              A termék jelenleg nem rendelhető.
            </div>
            <? endif; ?>

            <?php if ($this->product['show_stock'] == 1): ?>
            <div class="stock-info <?=($this->product['raktar_keszlet'] <=0)?'no-stock':''?>">
              <?php if ($this->product['raktar_keszlet'] > 0): ?>
                Készleten: <strong><?php echo $this->product['raktar_keszlet']; ?> <?php echo strtolower($this->product['mertekegyseg']); ?>.</strong>
              <?php else: ?>
                Készleten: <strong>Nincs készleten jelenleg.</strong>
              <?php endif; ?>
            </div>
            <?php endif; ?>

            <?php if ( $this->product['raktar_keszlet'] > 0 || $this->settings['stock_outselling'] == '1'): ?>
            <div class="group" style="margin: 10px -10px 0 -10px;">
              <?
              // KIKAPCSOLVA
              if( count($this->product['hasonlo_termek_ids']['colors'][$this->product['szin']]['size_set']) > 1 && false ):
                  $colorset = $this->product['hasonlo_termek_ids']['colors'][$this->product['szin']]['size_set'];
                  //unset($colorset[$this->product['szin']]);
              ?>
              <div class="size-selector cart-btn dropdown-list-container">
                  <div class="dropdown-list-title"><span id=""><?=__('Kiszerelés')?>: <strong><?=$this->product['meret']?></strong></span> <? if( count( $this->product['hasonlo_termek_ids']['colors'][$this->product['szin']]['size_set'] ) > 0): ?> <i class="fa fa-angle-down"></i><? endif; ?></div>

                  <div class="number-select dropdown-list-selecting overflowed">
                  <? foreach ($colorset as $szin => $adat ) : ?>
                      <div link="<?=$adat['link']?>"><?=$adat['size']?></div>
                  <? endforeach; ?>
                  </div>
              </div>
              <? endif; ?>

              <div class="configs">
                <div class="list">
                  <?php if ( !$this->product['without_price'] ): ?>
                  <div class="conf men">
                    <label for="darab"><?=($this->product['mertekegyseg'] == '')?'Mennyiség:': ( ($this->product['mertekegyseg_ertek'] != 1) ? $this->product['mertekegyseg_ertek'].' '.$this->product['mertekegyseg'] : ucfirst($this->product['mertekegyseg']) ) .':'?></label>
                    <div class="num-inp-wrapper">
                      <input type="number" name="" id="add_cart_num" cart-count="<?=$this->product['ID']?>" value="1" min="1">
                    </div>
                  </div>
                  <?php endif; ?>
                </div>
              </div>

              <div class="order <?=($this->product['without_price'])?'requestprice':''?>">
                <?php if ( !$this->product['without_price'] ): ?>
                  <div class="finalprice">
                    Bruttó összeg:<br>
                    <div class="price"><?=\PortalManager\Formater::cashFormat($this->product['ar'])?> <?=$this->valuta?><? if($this->product['mertekegyseg'] != ''): ?><span class="unit-text">/<?=($this->product['mertekegyseg_ertek']!=1)?$this->product['mertekegyseg_ertek']:''?><?=$this->product['mertekegyseg']?></span><? endif; ?></div>
                    <?php if ($this->product['mertekegyseg_egysegar']): ?>
                    <div class="egysegar">
                     Egységár: <strong><?php echo $this->product['mertekegyseg_egysegar']; ?></strong>
                    </div>
                    <?php endif; ?>
                  </div>
                  <div class="buttonorder">
                    <input type="hidden" name="" id="cart_item<?=$this->product['ID']?>_configs" value="">
                    <button id="addtocart" cart-data="<?=$this->product['ID']?>" data-configs="" cart-remsg="cart-msg" title="Kosárba rakom" class="tocart cart-btn"> <img src="<?=IMG?>cart-shop.svg" alt=""> <?=__('kosárba rakom')?></i></button>
                  </div>
                <?php else: ?>
                  <div class="requestbutton">
                    <md-tooltip md-direction="top">
                      Erre a gombra kattintva árajánlatot kérhet erre a termékre.
                    </md-tooltip>
                    <button aria-label="Erre a gombra kattintva árajánlatot kérhet erre a termékre." class="tocart cart-btn" ng-click="requestPrice(<?=$this->product['ID']?>)"><?=__('Ajánlatot kérek')?></i></button>
                  </div>
                <?php endif; ?>
              </div>
            </div>
            <?php endif; ?>
          </div>
          <div class="divider"></div>
          <div class="group-infos">
            <div class="cikkszam">
              <div class="flex">
                <div class="title">
                  Cikkszám:
                </div>
                <div class="val">
                  <?php echo $this->product['cikkszam']; ?>
                </div>
              </div>
            </div>
            <?php if (!empty($this->product['in_cats']['name'])): ?>
            <div class="cats">
              <div class="flex">
                <div class="title">
                  Kategóriák:
                </div>
                <div class="val">
                  <div class="wrapper">
                    <div class="labels">
                      <?php
                      $ci = -1;
                      foreach ((array)$this->product['in_cats']['name'] as $cat ): $ci++; ?>
                      <div class="">
                        <a href="<?=$this->product['in_cats']['url'][$ci]?>"><?=$cat?></a>
                      </div>
                      <?php endforeach; unset($ci); ?>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <?php endif; ?>
            <?php if (!empty($this->product['kulcsszavak'])): ?>
            <div class="keywords">
              <div class="flex">
                <div class="title">
                  Címkék:
                </div>
                <div class="val">
                  <div class="wrapper">
                    <div class="labels">
                      <?php foreach ( (array)$this->product['kulcsszavak'] as $kulcsszavak ): ?>
                      <div>
                        <a href="/tag/<?=$kulcsszavak?>"><?=$kulcsszavak?></a>
                      </div>
                      <?php endforeach; ?>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <?php endif; ?>
            <div class="shares">
              <div class="flex">
                <div class="title">
                  Megosztás:
                </div>
                <div class="val">
                  <div class="wrapper">
                    <div class="social">
                      <div class="flex flexmob-exc-resp">
                        <div class="facebook" title="Megosztás Facebook-on!">
                          <?php $current_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
 ?>
                          <a href="javascript:void(0);" onclick='window.open( "https://www.facebook.com/sharer/sharer.php?u=<?=$current_link?>", "Facebook", "status = 1, height = 760, width = 560, resizable = 0" )'><i class="fa fa-facebook"></i></a>
                        </div>
                        <div class="email">
                          <a href="mailto:?subject=<? echo $this->settings['page_title'].' termék ajánlás: '.$this->product['nev'].' - '.Helper::cashFormat($this->product['ar']).' Ft'?>&body=Kedves ...!%0D%0A%0D%0AAjánlom Neked a következő terméket:%0D%0A<?=$this->product['nev']?>%0D%0A<?=$current_link?>"><i class="fa fa-envelope"></i></a>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <?php endif; ?>

        </div>
      </div>
    </div>
    <?php
    $vc = $this->product['vehicles_compatiblity'];
    ?>
    <div class="more-datas">
      <div class="page-width">
        <div class="actions">
          <div class="wrapper">
            <div class="fav">
              <div aria-label="Hozzáadás a kedvencekhez." class="fav" ng-class="(fav_ids.indexOf(<?=$this->product['ID']?>) !== -1)?'selected':''" ng-click="productAddToFav(<?=$this->product['ID']?>)">
                <div class="wrapper">
                  <i class="fa fa-star" ng-show="fav_ids.indexOf(<?=$this->product['ID']?>) !== -1"></i>
                  <i class="fa fa-star-o" ng-show="fav_ids.indexOf(<?=$this->product['ID']?>) === -1"></i>
                  <span ng-show="fav_ids.indexOf(<?=$this->product['ID']?>) === -1">Kedvenc</span>
                  <span ng-show="fav_ids.indexOf(<?=$this->product['ID']?>) !== -1">Kedvenem</span>
                </div>
                <md-tooltip md-direction="bottom">
                  Hozzáadás a kedvencekhez.
                </md-tooltip>
              </div>
            </div>
            <?php if ($this->user && $this->user['data']['user_group'] == 'company'): ?>
            <div class="sep"></div>
            <div class="lefoglal">
              <div aria-label="Termék lefoglalása." class="fav">
                <div class="wrapper">
                  <i class="fa fa-pause-circle-o"></i>
                  <a href="?reserve=now">Lefoglal</a>
                </div>
                <md-tooltip md-direction="bottom">
                  Termék lefoglalása 24 órára.
                </md-tooltip>
              </div>
            </div>
            <?php endif; ?>
          </div>
        </div>
        <nav class="tab-header">
          <ul ng-controller="ActionButtons">
            <li class="description active"><a href="#description" onclick="switchTab('description')">Leírás</a></li>
            <?php if ($this->product['parameters'] && !empty($this->product['parameters'])): ?>
            <li class="parameters"><a href="#parameters" onclick="switchTab('parameters')">Műszaki adatok</a></li>
            <?php endif; ?>
            <?php if ($this->product['documents']): ?>
            <li class="documents"><a href="#documents" onclick="switchTab('documents')">Dokumentumok</a></li>
            <?php endif; ?>
            <li class="compatiblity"><a href="#compatiblity" onclick="switchTab('compatiblity')">Kompatibilitási lista</a></li>
            <li class="ask"><a href="javascript:void(0);" ng-click="requestTermekKerdes(<?=$this->product['ID']?>)">Kérdés a termékről <i class="fa fa-ask"></i> </a></li>
          </ul>
        </nav>
        <div class="holder">
          <div class="info-texts">
            <?php if ($this->product['parameters'] && !empty($this->product['parameters'])): ?>
              <a name="parameters"></a>
              <div class="parameters tab-holder" id="tab-content-parameters">
                <div class="c">
                  <div class="params">
                    <?php foreach ( $this->product['parameters'] as $p ): ?>
                    <div class="param">
                      <div class="key">
                        <?php echo $p['neve']; ?>
                      </div>
                      <div class="val">
                        <strong><?php echo $p['ertek']; ?></strong> <span class="me"><?php echo $p['me']; ?></span>
                      </div>
                    </div>
                    <?php endforeach; ?>
                  </div>
                </div>
              </div>
            <?php endif; ?>

            <a name="description"></a>
            <div class="description tab-holder showed" id="tab-content-description">
              <div class="c">
                <?php if ( !empty($this->product['leiras']) ): ?>
                <?=\ProductManager\Product::modifyDescription($this->product['leiras'])?>
                <?php else: ?>
                  <div class="no-data">
                    <i class="fa fa-info-circle"></i> A terméknek nincs leírása.
                  </div>
                <?php endif; ?>
              </div>
            </div>

            <?php if ($this->product['documents']): ?>
            <a name="documents"></a>
            <div class="documents tab-holder" id="tab-content-documents">
              <div class="c">
                <div class="docs">
                  <?php foreach ( (array)$this->product['documents'] as $doc ): ?>
                  <div class="doc">
                    <a target="_blank" title="Kiterjesztés: <?=strtoupper($doc['ext'])?>" href="/app/dcl/<?=$doc['hashname']?>"><img src="<?=IMG?>icons/<?=$doc['icon']?>.svg" alt=""><?=$doc['cim']?><?=($doc[filesize])?' <span class="size">&bull; '.strtoupper($doc['ext']).' &bull; '.$doc[filesize].'</span>':''?></a>
                  </div>
                  <?php endforeach; ?>
                </div>
              </div>
            </div>
            <?php endif; ?>
            <a name="compatiblity"></a>
            <div class="compatiblity tab-holder" id="tab-content-compatiblity">
              <div class="c">
                <?php
                if ( empty($vc) ): ?>
                <div class="no-setted-values">
                  <i class="fa fa-question-circle-o"></i>
                  <strong>Ehhez a termékhez nem lett meghatározva gépjármű kompatibilitás!</strong><br>
                  Vásárlás előtt mindenképp tájékozódjon a gyártó weboldalán!
                </div>
                <?php else: ?>
                  <?php if ( $this->product['vehicles_compatiblity_num'] > 0 ): ?>
                    <div class="compatible-msg compatible">
                      <i class="fa fa-check-circle-o"></i>
                      <strong>Az eszköz / alkatrész <?=$this->product['vehicles_compatiblity_num']?> db gépjármű modellel kompatibilis!</strong><br>
                      Ellenőrizze a model koncepcióját és gyártási év intervallumokat, amennyiben meghatároztuk.
                    </div>
                  <?php elseif( empty($this->product['vehicles_filtered_ids']) ): ?>
                    <div class="compatible-msg unsetted">
                      <i class="fa fa-car"></i>
                      <strong>Tudja meg, hogy kompatibilis-e a termék a gépjárművével!</strong><br>
                      <span class="btn" ng-click="openVehicleSelector()"><i class="fa fa-gear"></i> szűrő beállítása</span>
                    </div>
                  <?php else: ?>
                    <div class="compatible-msg uncompatible">
                      <i class="fa fa-times-circle-o"></i>
                      <strong>Az eszköz / alkatrész nem kompatibilis az Ön által szűrt gépjármű modellekhez!</strong><br>
                      Szűrőfeltételei alapján egyik modelhez sem használható a termék!
                    </div>
                  <?php endif; ?>
                  <div class="list">
                    <?php foreach ($vc as $vg): ?>
                    <div class="manufacturer">
                      <div class="wrapper">
                        <div class="head">
                          <?=$vg['title']?>
                        </div>
                        <?php if (count($vg['models']) == 0): ?>
                        <div class="all-model">
                          Az összes modellel kompatibilis.
                        </div>
                        <?php else: ?>
                          <?php foreach ($vg['models'] as $vm): ?>
                          <div class="model <?=($vm['compatible'])?'compatible':''?>">
                            <div class="head">
                              <strong><?=$vm['title']?></strong> <?=($vm['compatible'])?'<i class="fa fa-check-circle-o"></i>':''?>
                            </div>
                            <?php if (count($vm['creation_restricts']) != 0): ?>
                              <div class="restricts">
                                <?php foreach ($vm['creation_restricts'] as $rs): ?>
                                <div class="config">
                                  <div class="title">
                                    <?=$rs['title']?>
                                  </div>
                                  <div class="date">
                                    <?=$rs['ydate']?>
                                  </div>
                                </div>
                                <?php endforeach; ?>
                              </div>
                            <?php endif; ?>
                          </div>
                          <?php endforeach; ?>
                        <?php endif; ?>
                      </div>
                    </div>
                    <?php endforeach; ?>
                  </div>
                  <pre><?php //print_r($this->product['vehicles_compatiblity']); ?></pre>
                <?php endif; ?>
              </div>
            </div>
          </div>
          <div class="related-products">
            <?php if ( $this->related_list ): ?>
              <div class="head">
                <h3>Ajánlott termékek</h3>
              </div>
              <div class="c">
                <div class="items">
                <?php if ( $this->related_list ): ?>
                  <? foreach ( $this->related_list as $p ) {
                      $p['itemhash'] = hash( 'crc32', microtime() );
                      $p['sideproducts'] = true;
                      $p = array_merge( $p, (array)$this );
                      echo $this->template->get( 'product_item', $p );
                  } ?>
                <?php endif; ?>
                </div>
              </div>
            <?php endif; ?>

            <?php if ( $this->replacements_list ): ?>
              <div class="head">
                <h3>Helyettesítő termékek</h3>
              </div>
              <div class="c">
                <div class="items">
                <?php if ( $this->replacements_list ): ?>
                  <? foreach ( $this->replacements_list as $p ) {
                      $p['itemhash'] = hash( 'crc32', microtime() );
                      $p['sideproducts'] = true;
                      $p = array_merge( $p, (array)$this );
                      echo $this->template->get( 'product_item', $p );
                  } ?>
                <?php endif; ?>
                </div>
              </div>
            <?php endif; ?>
          </div>

        </div>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript">
    $(function() {
        <? if( $_GET['buy'] == 'now'): ?>
        $('#add_cart_num').val(1);
        $('#addtocart').trigger('click');
        setTimeout( function(){ document.location.href='/kosar' }, 1000);
        <? endif; ?>
        <? if( $_GET['reserve'] == 'now'): ?>
        $('#add_cart_num').val(1);
        $('#addtocart').trigger('click');
        setTimeout( function(){ document.location.href='/kosar/elofoglalas' }, 1000);
        <? endif; ?>
        $('.number-select > div[num]').click( function (){
            $('#add_cart_num').val($(this).attr('num'));
            $('#item-count-num').text($(this).attr('num')+' db');
        });
        $('.size-selector > .number-select > div[link]').click( function (){
            document.location.href = $(this).attr('link');
        });

        findConfigCart(<?=$this->product['ID']?>);
        $('select[name*=config]').change(function(){
          findConfigCart(<?=$this->product['ID']?>);
        });

        $('.product-view .images .all img').hover(function(){
            changeProfilImg( $(this).attr('i') );
        });

        $('.product-view .images .all img').bind("mouseleave",function(){
            //changeProfilImg($('.product-view .main-view a.zoom img').attr('di'));
        });

        $('.products > .grid-container > .item .colors-va li')
        .bind( 'mouseover', function(){
            var hash    = $(this).attr('hashkey');
            var mlink   = $('.products > .grid-container > .item').find('.item_'+hash+'_link');
            var mimg    = $('.products > .grid-container > .item').find('.item_'+hash+'_img');

            var url = $(this).find('a').attr('href');
            var img = $(this).find('img').attr('data-img');

            mimg.attr( 'src', img );
            mlink.attr( 'href', url );
        });

        $('.viewSwitcher > div').click(function(){
            var view = $(this).attr('view');

            $('.viewSwitcher > div').removeClass('active');
            $('.switcherView').removeClass('switch-view-active');

            $(this).addClass('active');
            $('.switcherView.view-'+view).addClass('switch-view-active');

        });


        $('.images .all').on('init', function(slick){
          $('.images .all .imgslide > .wrp').css({
            height: $('.images .all .imgslide > .wrp').width()
          });
        });

        $('.images .all').slick({
          infinite: true,
          arrow: true,
          slidesToShow: 3,
          slidesToScroll: 1,
          speed: 400,
          autoplay: true
        });
    })

    function findConfigCart(id) {
      var configs = $('select[name*=config]');
      var p = {};

      $.each(configs, function(i,v){
        p['p'+$(v).data('paramid')] =  $(v).val();
      });

      $('#cart_item'+id+'_configs').val($.param(p));
    }

    function switchTab( tab ) {
      $('.tab-holder.showed').removeClass('showed');
      $('.tab-holder.'+tab).addClass('showed');

      $('nav.tab-header li.active').removeClass('active');
      $('nav.tab-header li.'+tab).addClass('active');
      console.log(tab);
    }

    function changeProfilImg(i){
        $('.product-view .main-img a.zoom img').attr('src',i);
        $('.product-view .main-img a.zoom').attr('href',i);
    }
</script>
