<?
    $ar = $this->product['brutto_ar'];

    if( $this->product['akcios'] == '1' && $this->product['akcios_fogy_ar'] > 0)
    {
       $ar = $this->product['akcios_fogy_ar'];
    }
?>
<div class="product-view page-width">
	<? $this->render('templates/slideshow'); ?>
    <? if($this->msg): ?>
    <br>
    <?=$this->msg?>
    <? endif; ?>
    <div class="main-view">
        <div class="images">
            <div class="main-img">
                <? if( $ar >= $this->settings['cetelem_min_product_price'] && $ar <= $this->settings['cetelem_max_product_price'] && $this->product['no_cetelem'] != 1 ): ?>
                    <img class="cetelem" src="<?=IMG?>cetelem_badge.png" alt="Cetelem Online Hitel">
                <? endif; ?>
                <div class="img-thb">
                    <span class="helper"></span>
                    <a href="<?=$this->product['profil_kep']?>" class="zoom"><img di="<?=$this->product['profil_kep']?>" src="<?=$this->product['profil_kep']?>" alt="<?=$this->product['nev']?>"></a>
                </div>
            </div>
            <div class="all">
                <?  foreach ( $this->product['images'] as $img ) { ?>
                <div class="img-auto-cuberatio__">
                    <img class="aw" i="<?=\PortalManager\Formater::productImage($img)?>" src="<?=\PortalManager\Formater::productImage($img, 150)?>" alt="<?=$this->product['nev']?>">
                </div>
                <? } ?>
            </div>
            <div class="free-transport">
                <img src="<?=IMG?>transporter.png" alt="<?=__('Ezt a terméket ingyen szállítjuk')?>"> <?=__('Ezt a terméket ingyen szállítjuk')?>
            </div>
            <? if($this->product['links']): ?>
            <div class="links">
                <div class="divider"></div>
                <ul>
                    <? foreach( $this->product['links'] as $link ) : ?>
                    <li><a target="_blank" href="<?=$link['link']?>"><?=$link['title']?> <i class="fa fa-chevron-circle-right"></i></a></li>
                    <? endforeach; ?>
                </ul>
                <div class="clr"></div>
            </div>
          <? endif; ?>
        </div>
        <div class="data-view">
            <div class="cimkek">
            <? if($this->product['ujdonsag'] == '1'): ?>
                <img src="<?=IMG?>new_icon.png" title="Újdonság!" alt="Újdonság">
            <? endif; ?>
            </div>
            <h1><?=$this->product['nev']?></h1>
            <div class="cat"><?=$this->product['in_cat_names'][0]?></div>
            <div class="divider"></div>
            <div class="rack">
                <div class="grid-layout kpm">
                    <div class="grid-row grid-row-50">
                        <div class="status">
                            <div class="title"><?=__('Készlet információ:')?></div>
                            <strong><?=$this->product['keszlet_info']?></strong>
                            <? if($this->product['show_stock'] == 1): ?><div class="in_stock">(<?=$this->product['raktar_keszlet']?> db készleten)</div><? endif; ?>
                        </div>
                    </div>
                    <div class="grid-row grid-row-50">
                        <? if($this->product['garancia_honap']): ?>
                        <div class="garancia">
                            <img src="<?=IMG?>hu_gar.png" alt="<?=__('Garanciális idő')?>">
                            <div class="title"><?=__('Garanciális idő')?></div>
                            <div class="gar"><span class="year"><? $gh = $this->product['garancia_honap'] / 12;  ?>
                            <? if($gh < 1 ): ?>
                                <?=$this->product['garancia_honap']?> <?=__('hónap')?>
                            <? else: ?>
                                <?=$gh?> <?=__('év')?>
                            <? endif; ?></span> <?=__('EU garancia')?></div>
                        </div>
                    <? endif; ?>
                    </div>
                </div>
            </div>
            <? $this->render('templates/tanacsado'); ?>
            <div class="action-bar">
                <!--<a href="javascript:void(0);" jOpen="recall" jWidth="750" tid="<?=$this->product['ID']?>"><?=__('Telefonos szaktanácsadást kérek')?></a>-->
            </div>
            <div class="divider"></div>
            <div id="cart-msg"></div>
            <div class="grid-layout">
                <div class="grid-row grid-row-50">
                    <div class="prices">
                        <div class="base">
                            <div class="price-head"><?=__('Ár')?>:</div>
                            <?  if( $this->product['akcios'] == '1' && $this->product['akcios_fogy_ar'] > 0):
                                $ar = $this->product['akcios_fogy_ar'];
                            ?>
                            <div class="old">
                                <div class="discount_percent">-<? echo 100-round($this->product['akcios_fogy_ar'] / ($this->product['brutto_ar'] / 100)); ?>%</div>
                                <div class="price"><strike><?=\PortalManager\Formater::cashFormat($this->product['ar'])?> <?=$this->valuta?></strike></div>
                            </div>
                            <? endif; ?>
                            <div class="current">
                                <?=\PortalManager\Formater::cashFormat($ar)?> <?=$this->valuta?>
                            </div>
                        </div>

                        <? if($this->product['referer_price_discount'] != 0): ?>
                        <div class="partner-price">
                            <strong>Ajánló partnerkóddal:</strong><br>
                            <?=\PortalManager\Formater::cashFormat($ar-$this->product['referer_price_discount'])?> <?=$this->valuta?>
                        </div>
                        <? endif;?>
                    </div>
                    <div class="clr"></div>
                    <? if($this->product['ajandek']): ?>
                    <div class="gift">
                        <img src="<?=IMG?>gift_40pxh.png" alt="<?=__('Ajándék')?>">
                        <?=$this->product['ajandek']?>
                        <div class="clr"></div>
                    </div>
                    <? endif; ?>
                    <? if( $ar >= $this->settings['cetelem_min_product_price'] && $ar <= $this->settings['cetelem_max_product_price'] && $this->product['no_cetelem'] != 1 ): ?>
                    <div class="cetelem-calc" price="<?=$ar?>"></div>
                    <? endif; ?>
                </div>
                <div class="grid-row grid-row-50">
                    <div class="cart">
                        <div class="current-variation">
                            <div class="title"><?=__('Variáció')?>:</div>
                            <strong><?=$this->product['szin']?></strong>
                        </div>
                        <div class="grid-layout kpm grid-np">
                            <div class="grid-row grid-row-50">
                                <input type="text" id="add_cart_num" style="display:none;" value="0" cart-count="<?=$this->product['ID']?>" />
                                <div class="item-count cart-btn dropdown-list-container">
                                    <div class="dropdown-list-title"><span id="item-count-num">Mennyiség</span> <i class="fa fa-angle-down"></i></div>
                                    <div class="number-select dropdown-list-selecting overflowed">
                                        <?
                                        $maxi = 10;
                                        if( $this->product[raktar_keszlet] < $maxi ) $maxi = (int)$this->product[raktar_keszlet];

                                        for ( $n = 1; $n <= $maxi; $n++ ) { if($n > 10) break; ?>
                                        <div num="<?=$n?>"><?=$n?></div>
                                        <? } ?>
                                    </div>
                                </div>
                            </div>
                            <div class="grid-row grid-row-50">
                                <?
                                if( count($this->product['hasonlo_termek_ids']['colors']) > 1 ):
                                    $colorset = $this->product['hasonlo_termek_ids']['colors'];
                                    unset($colorset[$this->product['szin']]);
                                ?>

                                <div class="size-selector cart-btn dropdown-list-container">
                                    <div class="dropdown-list-title"><span id=""><?=__('Variációk')?>: <strong><?=$this->product['meret']?></strong></span> <? if( count( $this->product['hasonlo_termek_ids']['colors'][$this->product['szin']]['size_set'] ) > 0): ?> <i class="fa fa-angle-down"></i><? endif; ?></div>

                                    <div class="number-select dropdown-list-selecting overflowed">
                                    <? foreach ($colorset as $szin => $adat ) : ?>
                                        <div link="<?=$adat['link']?>"><?=$szin?></div>
                                    <? endforeach; ?>
                                    </div>
                                </div>

                                <? endif; ?>
                            </div>
                        </div>
                        <div class="add-to-card">
                            <? if( $this->product['keszletID'] != $this->settings['flagkey_itemstatus_outofstock'] ): ?>
                            <button id="addtocart" cart-data="<?=$this->product['ID']?>" cart-remsg="cart-msg" title="Kosárba" class="tocart cart-btn"><img src="<?=IMG?>shopcart_white_20pxh.png" alt="<?=__('megrendelem')?>"><?=__('megrendelem')?></i></button>
                            <? endif; ?>
                        </div>
                        <? if( $ar >= $this->settings['cetelem_min_product_price'] && $ar <= $this->settings['cetelem_max_product_price'] && $this->product['no_cetelem'] != 1 ): ?>
                        <div class="cetelem-calc-btn orange">
                            Szeretné Hitelre megvásárolni?
                            <div class="cetelem-calc-box">
                               <h3>5 perc alatt hitelbírálat!</h3>
                               <ol>
                                   <li>Tegye a kosárba a terméket a fent lévő megrendelem gombra kattintva.</li>
                                   <li>A kosárban kezdje meg a megrendelést a szokásos módon.</li>
                                   <li>A fizetési mód kiválasztásánál válassza a <strong>Cetelem Online Áruhitel</strong> opciót.</li>
                                   <li>Adja le a megrendelését.</li>
                                   <li>A leadott megrendelés adatlapján elindíthatja az online hiteligénylés folyamatát.</li>
                                   <li>Sikeres hitelbírálat esetén nyomtassa ki a dokumentumokat és küldje el részünkre.</li>
                                   <li>A dokumentumok feldolgozása után Ön átveheti a megrendelt terméket vagy a választott szállítási mód szerint eljuttatjuk Önnek.</li>
                               </ol>
                            </div>
                        </div>

                        <div class="cetelem-calc-btn">
                            Online Áruhitel részletek <i class="fa fa-info-circle"></i>
                            <div class="cetelem-calc-box">
                                <div id="node-209" class="node">
                                  <div class="content">
                                    <p><b>Referencia THM: 34,49%</b>, 2 000 000 Ft hitelösszeg és 36 hónap futamidő esetén.<br />
                                Futamidő: 36 hónap. Fix, éves ügyleti kamat: 30,00%. Kezelési díj: 0 Ft. Az igényelhető hitelösszeg: 25 000 Ft-tól  2 000 000 Ft-ig terjedhet. Szükséges önrész a vételár függvényében: 200 000 Ft-ig önrész befizetése nélkül, 200 001 Ft-tól 300 000 Ft-ig minimum a vételár 10%-a, 300 001 Ft felett minimum a vételár 20%-a.<br />
                                Reprezentatív példa a hiteltermékek összehasonlítása céljából: Vételár: 625 000 Ft, önrész 125 000 Ft, hitelösszeg 500 000 Ft, futamidő 36 hónap. Fix, éves ügyleti kamat 30,00 %, kezelési díj 0 Ft, referencia THM 34,49%, havi törlesztőrészlet 21 225 Ft, törlesztőrészletek összege: 764 100 Ft, a fizetendő teljes összeg (önrész nélkül): 764 100 Ft.  </p>
								<p>A Casada Hungary Kft. a Magyar Cetelem Bank Zrt. hitelközvetítője, a bank a hitelbírálathoz szükséges dokumentumok meghatározásának, a hitelbírálatnak a jogát fenntartja, valamint az ajánlati kötöttségét kizárja. További részletek az általános szerződési feltételekben és a vonatkozó hirdetményekben. <a href="https://www.cetelem.hu/segedlet/dokumentumok" style="color: white;">https://www.cetelem.hu/segedlet/dokumentumok</a></p>



								<p>Érvényes a hatályos hírdetmény szerint.</p>
								<!--
                                <p>A Bank a hitelbírálathoz szükséges dokumentumok meghatározásának, valamint a hitelbírálatnak a jogát fenntartja.<br />
                                Érvényes a hatályos hirdetmény szerint.</p>
                                <p>A referencia THM a megjelölt hitelösszeg és futamidő figyelembevételével került meghatározásra, a teljes hiteldíj mutató meghatározásáról, számításáról és közzétételéről szóló 83/2010. (III.25.) Korm.rend. 9. §-a alapján.</p>
								-->
                                  </div>

                                  <div class="clear-block clear">
                                    <div class="meta">
                                        </div>

                                      </div>

                                </div>
                            </div>
                        </div>
                        <? endif; ?>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <div class="divider"></div>
    <div class="info">
        <div class="viewSwitcher">
            <div view="desc" class="active"><?=__('Információ')?></div>
            <div view="jell" class=""><?=__('Jellemzők')?></div>
            <div view="szall" class=""><?=__('Szállítás és beüzemelés')?></div>
            <div view="downloads" class=""><?=__('Letöltések')?></div>
            <? if($this->product['bankihitel_leiras']): ?>
            <div view="bankfinance" class="bluebg"><?=__('Részletfizetés')?></div>
            <? endif; ?>
        </div>
        <div class="switcherView view-desc switch-view-active">
            <div class="inset">
                <div class="grid-layout">
                    <div class="grid-row grid-row-50 product-desc">
                        <?=Product::rewriteImageTinyMCE($this->product['rovid_leiras'])?>
                    </div>
                    <div class="grid-row grid-row-50">
                        <div class="buy-basic-info">
                            <? echo $this->render('templates/basic_buy_info'); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="switcherView view-jell">
            <div class="inset">
                <? if( $this->product['parameters'] ): ?>
                <table class="table table-bordered">
                    <tbody>
                        <? foreach( $this->product['parameters'] as $param ): ?>
                        <tr>
                            <td><?=__($param['neve'])?></td>
                            <td><strong><?=__($param['ertek'])?> <?=__($param['me'])?></strong></td>
                        </tr>
                        <? endforeach; ?>
                    </tbody>
                </table>
                <? endif; ?>
            </div>
        </div>
        <div class="switcherView view-szall">
            <div class="inset">
                <?=Product::rewriteImageTinyMCE($this->product['leiras'])?>
            </div>
        </div>
        <div class="switcherView view-downloads">
            <div class="inset">
                <?=Product::rewriteImageTinyMCE($this->product['letoltesek'])?>
            </div>
        </div>
        <? if($this->product['bankihitel_leiras']): ?>
        <div class="switcherView view-bankfinance">
            <div class="inset">
                <?=Product::rewriteImageTinyMCE($this->product['bankihitel_leiras'])?>
            </div>
        </div>
        <? endif; ?>
    </div>

    <? if( $this->related && $this->related->hasItems() ): ?>
    <div class="related-products">
        <h3>Ajánlott termékeink</h3>
        <div class="items">
            <? foreach ( $this->related_list as $p ) {
                $p['itemhash'] = hash( 'crc32', microtime() );
                $p['sizefilter'] = ( count($this->related->getSelectedSizes()) > 0 ) ? true : false;
                $p = array_merge( $p, (array)$this);
                echo $this->template->get( 'product_list_ajanlott', $p );
            } ?>
            <div class="clr"></div>
        </div>
    </div>
    <? endif; ?>

</div>

<script type="text/javascript">
    $(function() {
		$('.cetelem-calc').cetelemCalculator();

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
          slidesToShow: 3,
          slidesToScroll: 3
        });
    })

    function changeProfilImg(i){
        $('.product-view .main-img a.zoom img').attr('src',i);
        $('.product-view .main-img a.zoom').attr('href',i);
    }
</script>
