<?php
  $ar = $this->product['brutto_ar'];

  if( $this->product['akcios'] == '1' && $this->product['akcios_fogy_ar'] > 0)
  {
     $ar = $this->product['akcios_fogy_ar'];
  }
?>
<div class="product-view">
  <div class="product-data">
    <div class="page-width">
      <div class="top-datas">
        <div class="images">
          <?php if (true): ?>
          <div class="main-img img-auto-cuberatio">
            <? if( $ar >= $this->settings['cetelem_min_product_price'] && $ar <= $this->settings['cetelem_max_product_price'] && $this->product['no_cetelem'] != 1 ): ?>
                <img class="cetelem" src="<?=IMG?>cetelem_badge.png" alt="Cetelem Online Hitel">
            <? endif; ?>
            <div class="img-thb">
                <a href="<?=$this->product['profil_kep']?>" class="zoom"><img di="<?=$this->product['profil_kep']?>" src="<?=$this->product['profil_kep']?>" alt="<?=$this->product['nev']?>"></a>
            </div>
          </div>
          <div class="all">
            <?  foreach ( $this->product['images'] as $img ) { ?>
            <div class="imgslide img-auto-cuberatio__">
              <div class="wrp">
                <img class="aw" i="<?=\PortalManager\Formater::productImage($img)?>" src="<?=\PortalManager\Formater::productImage($img, 150)?>" alt="<?=$this->product['nev']?>">
              </div>
            </div>
            <? } ?>
          </div>
          <?php endif; ?>
        </div>
        <div class="main-data">
          <div class="nav">
            <div class="pagi">
              <?php
                $navh = '/termekek/';
                $lastcat = end($this->product['nav']);
              ?>
              <ul class="cat-nav">
                <li><a href="/"><i class="fa fa-home"></i></a></li>
                <li><a href="<?=$navh?>">Webshop</a></li>
                <?php
                foreach ( $this->product['nav'] as $nav ): $navh = \Helper::makeSafeUrl($nav['neve'],'_-'.$nav['ID']); ?>
                <li><a href="/termekek/<?=$navh?>"><?php echo $nav['neve']; ?></a></li>
                <?php endforeach; ?>
              </ul>
            </div>
          </div>
          <?php if ($_GET['d'] == 1): ?>


          <h1><?=$this->product['nev']?></h1>
          <div class="csoport">
            <?=$this->product['csoport_kategoria']?>
          </div>
          <div class="prices">
              <div class="base">
                <?php if ($this->product['without_price']): ?>
                  <div class="price-head"><?=__('ÁR')?>:</div>
                  <div class="current">
                    ÉRDEKLŐDJÖN!
                  </div>
                <?php else: ?>
                  <div class="price-head"><?=__('ÁR')?>:</div>
                  <?  if( $this->product['akcios'] == '1' && $this->product['akcios_fogy_ar'] > 0):
                      $ar = $this->product['akcios_fogy_ar'];
                  ?>
                  <div class="old">
                      <div class="price"><strike><?=\PortalManager\Formater::cashFormat($this->product['ar'])?> <?=$this->valuta?></strike></div>
                  </div>
                  <div class="discount_percent">-<? echo 100-round($this->product['akcios_fogy_ar'] / ($this->product['brutto_ar'] / 100)); ?>%</div>
                  <? endif; ?>
                  <div class="current">
                      <?=\PortalManager\Formater::cashFormat($ar)?> <?=$this->valuta?>
                  </div>
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
          <? if($this->product['show_stock'] == '1' && $this->product['raktar_keszlet'] > 0): ?>
          <div class="stock">
            Készleten: <strong><?php echo $this->product['raktar_keszlet']; ?> db</strong>
          </div>
          <? endif; ?>
          <div class="divider"></div>
          <div class="short-desc">
            <?=$this->product['rovid_leiras']?>
          </div>

          <?
          if( count($this->product['hasonlo_termek_ids']['colors']) > 1 ):
              $colorset = $this->product['hasonlo_termek_ids']['colors'];
          ?>
          <div class="divider"></div>
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
            <div class="group">
              <div class="variation">
                <div class="h">
                  Variáció:
                </div>
                <div class="v">
                  <strong><?=$this->product['szin']?></strong>
                </div>
              </div>
              <div class="rack-status">
                <div class="h">
                  Elérhetőség:
                </div>
                <div class="v">
                  <?=$this->product['keszlet_info']?>
                </div>
              </div>
              <div class="transport-status">
                <div class="h">
                  Várható kiszállítás:
                </div>
                <div class="v">
                  <?=$this->product['szallitas_info']?>
                </div>
              </div>
            </div>
            <div id="cart-msg"></div>
            <? if($this->settings['stock_outselling'] == '0' && $this->product['raktar_keszlet'] <= 0): ?>
            <div class="out-of-stock">
              A termék jelenleg nem rendelhető.
            </div>
            <? endif; ?>

            <?php if ( $this->product['raktar_keszlet'] > 0 || $this->settings['stock_outselling'] == '1'): ?>
            <div class="group" style="margin: 10px -10px 0 0;">
              <?
              if( count($this->product['hasonlo_termek_ids']['colors'][$this->product['szin']]['size_set']) > 1 ):
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
              <div class="order <?=($this->product['without_price'])?'requestprice':''?>">
                <?php if ( !$this->product['without_price'] ): ?>
                <div class="men">
                  <input type="number" name="" id="add_cart_num" cart-count="<?=$this->product['ID']?>" value="1" min="1">
                </div>
                <?php endif; ?>
                <?php if ( !$this->product['without_price'] ): ?>
                  <div class="buttonorder">
                    <button id="addtocart" cart-data="<?=$this->product['ID']?>" cart-remsg="cart-msg" title="Kosárba" class="tocart cart-btn"><?=__('kosárba')?></i></button>
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
            <div class="group helpdesk-actions">
                <div class="tudastar">
                  <div class="wrapper icoed">
                    <div class="ico">
                      <div class="wrap">
                          <i class="fa fa-lightbulb-o"></i>
                      </div>
                    </div>
                    <div class="text">
                      <a href="<?=($this->product['tudastar_url'] != '')?$this->product['tudastar_url']:'/tudastar'?>" target="_blank">
                        TUDÁSTÁR
                        <div class="t">
                          Olvassa el!
                        </div>
                      </a>
                    </div>
                  </div>
                </div>
                <div class="callhelp">
                  <div class="wrapper icoed">
                    <div class="ico">
                      <i class="fa fa-phone"></i>
                    </div>
                    <div class="text">
                      Segíthetünk?
                      <div class="phone">
                        <a href="tel:<?=$this->settings['page_author_phone']?>"><?=$this->settings['page_author_phone']?></a>
                      </div>
                    </div>
                  </div>
                </div>
                <div aria-label="Hozzáadás a kedvencekhez." class="fav" ng-class="(fav_ids.indexOf(<?=$this->product['ID']?>) !== -1)?'selected':''" ng-click="productAddToFav(<?=$this->product['ID']?>)">
                  <div class="wrapper" title="Kedvencekhez adás">
                    <i class="fa fa-heart" ng-show="fav_ids.indexOf(<?=$this->product['ID']?>) !== -1"></i>
                    <i class="fa fa-heart-o" ng-show="fav_ids.indexOf(<?=$this->product['ID']?>) === -1"></i>
                  </div>
                  <md-tooltip md-direction="bottom">
                    Hozzáadás a kedvencekhez.
                  </md-tooltip>
                </div>
            </div>
          </div>
          <?php endif; ?>
          
        </div>
      </div>
    </div>
    <div class="more-datas">
      <div class="page-width">
        <nav class="tab-header">
          <ul>
            <li class="description active"><a href="#description" onclick="switchTab('description')">Leírás</a></li>
            <?php if ($this->product['parameters'] && !empty($this->product['parameters'])): ?>
            <li class="parameters"><a href="#parameters" onclick="switchTab('parameters')">Műszaki adatok</a></li>
            <?php endif; ?>
            <?php if ($this->product['documents']): ?>
            <li class="documents"><a href="#documents" onclick="switchTab('documents')">Dokumentumok</a></li>
            <?php endif; ?>
            <li class="compatiblity"><a href="#compatiblity" onclick="switchTab('compatiblity')">Kompatibilitási lista</a></li>
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
                <?=$this->product['leiras']?>
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
                Kompatibilitási lista
              </div>
            </div>
          </div>
          <?php if ( $this->related_list ): ?>
          <div class="related-products">
            <div class="c">
              <div class="items">
              <?php if ( $this->related_list ): ?>
                <? foreach ( $this->related_list as $p ) {
                    $p['itemhash'] = hash( 'crc32', microtime() );
                    $p = array_merge( $p, (array)$this );
                    echo $this->template->get( 'product_item', $p );
                } ?>
              <?php endif; ?>
              </div>
            </div>
          </div>
          <?php endif; ?>
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
        $('.number-select > div[num]').click( function (){
            $('#add_cart_num').val($(this).attr('num'));
            $('#item-count-num').text($(this).attr('num')+' db');
        });
        $('.size-selector > .number-select > div[link]').click( function (){
            document.location.href = $(this).attr('link');
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

        $('.images .all').slick({
          infinite: true,
          slidesToShow: 5,
          slidesToScroll: 1,
          speed: 400,
          autoplay: true
        });
    })

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
