<div class="order page-width">
    <div class="row">
    <div class="col-md-12">
        <div class="responsive-view full-width">
            <?
                $o = $this->order;
                $nevek = array(
                    'nev' => 'Név',
                    'uhsz' => 'Utca, házszám',
                    'city' => 'Város',
                    'adoszam' => 'Adószám',
                    'irsz' => 'Irányítószám',
                    'phone' => 'Telefonszám',
                    'state' => 'Megye'
                );
                $vegosszeg = 0;
                $termek_ar_total = 0;
                if(!empty($o[items])):

                foreach($o[items] as $d):
                    $vegosszeg += $d[subAr];
                    $termek_ar_total += $d[subAr];
                endforeach;

                if($o[szallitasi_koltseg] > 0) $vegosszeg += $o[szallitasi_koltseg];
              //  if($o[kedvezmeny] > 0) $vegosszeg -= $o[kedvezmeny];

                $discount = $o[kedvezmeny_szazalek];
            ?>
            <div class="box orderpage">
                <div class="head">
                    <div class="serial"><?=$o[azonosito]?></div>
                    <h1><?=$o[nev]?> rendelése</h1>
                    <div class="sub">
                        <span><em>Megrendelés leadva:</em> <?=\PortalManager\Formater::dateFormat($o[idopont], $this->settings['date_format'])?></span>
                    </div>
                </div>
                <div class="divider"></div>
                <div class="orderState">
                    <?=$this->rmsg?>
                    <h5>Megrendelés állapota:</h5>
                    <div class="orderStatus">
                        <span style="color:<?=$this->orderAllapot[$o[allapot]][szin]?>;"><strong><?=$this->orderAllapot[$o[allapot]][nev]?></strong></span>
                        <? // PayPal fizetés
                            if($this->fizetes[Helper::getFromArrByAssocVal($this->fizetes,'ID',$o[fizetesiModID])][nev] == 'PayPal' && $o[paypal_fizetve] == 0): ?>
                                <div style="padding:10px 0;">
                                    <form name="_xclick" action="https://www.paypal.com/cgi-bin/webscr" method="post">
                                        <input type="hidden" name="cmd" value="_xclick">
                                        <INPUT TYPE="hidden" name="charset" value="utf-8">
                                        <input type="hidden" name="business" value="<?=$this->settings['paypal_email']?>">
                                        <input type="hidden" name="currency_code" value="HUF">
                                        <input type="hidden" name="item_name" value="<?=$this->settings['page_title']?> megrendelés: <?=$o[azonosito]?>">
                                        <input type="hidden" name="amount" value="<?=$vegosszeg?>">
                                        <INPUT TYPE="hidden" NAME="return" value="<?=DOMAIN?>order/<?=$o[accessKey]?>/paid_via_paypal#pay">
                                        <input type="image" src="<?=IMG?>i/paypal_payout.svg" border="0" style="height:35px;" name="submit" alt="Make payments with PayPal - it's fast, free and secure!">
                                    </form>
                                </div>
                            <? endif; ?>
                    </div>
                </div>
                <div class="p10 divBtm items">
                     <h4>Megrendelt termékek</h4>
                     <div>
                        <div class="mobile-table-container overflowed">
                        <div class="items-table">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <td>Termék</td>
                                    <td width="150" class="center">Állapot</td>
                                    <td width="80" class="center">Me.</td>
                                    <td width="120" class="center">Egységár</td>
                                    <td width="120" class="center">Ár</td>
                                </tr>
                            </thead>
                            <tbody>
                                <? foreach($o[items] as $d): ?>
                                <tr>
                                    <td>
                                        <div class="cont">
                                            <div class="img img-thb" onClick="document.location.href='<?=$d[url]?>'">
                                                <span class="helper"></span>
                                                <a href="<?=$d[url]?>" target="_blank">
                                                    <img src="<?=\PortalManager\Formater::productImage($d[profil_kep], 75, \ProductManager\Products::TAG_IMG_NOPRODUCT)?>" alt="<?=$d[nev]?>">
                                                </a>
                                            </div>
                                            <div class="name">
                                                <a href="<?=$d[url]?>" target="_blank"><?=$d[nev]?></a>
                                                <div class="sel-types">
                                                  <? if($d['szin']): ?><em>Variáció:</em> <strong><?=$d['szin']?></strong><? endif;?>
                                                  <? if($d['meret']): ?><em>Kiszerelés:</em> <strong><?=$d['meret']?></strong><? endif;?>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="center"><span style="color:<?=$d[allapotSzin]?>;"><strong><?=$d[allapotNev]?></strong></span></td>
                                    <td class="center"><span><?=$d[me]?></span></td>
                                    <td class="center"><span><?=Helper::cashFormat($d[egysegAr])?> Ft</span></td>
                                    <td class="center"><span><?=Helper::cashFormat($d[subAr])?> Ft</span></td>
                                </tr>
                                <? endforeach; ?>
                                <tr>
                                    <td class="right" colspan="4"><strong>Termékek ára összesen</strong></td>
                                    <td class="center"><span><?=Helper::cashFormat($termek_ar_total)?> Ft</span></td>
                                </tr>
                                <tr>
                                    <td class="right" colspan="4"><div><strong>Szállítási költség</strong></div></td>
                                    <td class="center"><span><?=Helper::cashFormat($o[szallitasi_koltseg])?> Ft</span></td>
                                </tr>
                                <tr>
                                    <td class="right" colspan="4"><div><strong>Kedvezmény</strong></div></td>
                                    <td class="center"><span><?=($o[kedvezmeny] > 0)?'-'.Helper::cashFormat( $o[kedvezmeny] ) . ' Ft' : '-'?> </span></td>
                                </tr>
                                <tr style="font-size:18px;">
                                    <td class="right" colspan="4"><strong>Végösszeg</strong></td>
                                    <td class="center"><span><strong><?=Helper::cashFormat($vegosszeg - $o[kedvezmeny])?> Ft</strong></span></td>
                                </tr>
                            </tbody>
                        </table>
                        </div>
                        </div>
                     </div>
                </div>
                <a name="pay"></a>
                <div class="datas">
                     <h4>Adatok</h4>
                     <div class="row np">
                        <div class="col-md-12">
                            <div class="head"><strong>Kiválasztott szállítási mód:</strong></div>
                            <div class="data">
                            <?=$this->szallitas[Helper::getFromArrByAssocVal($this->szallitas,'ID',$o[szallitasiModID])][nev]?> <em><?=Product::transTime($o[szallitasiModID])?></em>
                            <?
                            // PickPackPont
                            if( $o[szallitasiModID] == $this->settings['flagkey_pickpacktransfer_id'] ): ?>
                            <div class="showSelectedPickPackPont">
                                <div class="head">Kiválasztott <strong>Pick Pack</strong> átvételi pont:</div>
                                <div class="p5">
                                   <?=$o['pickpackpont_uzlet_kod']?>
                                </div>
                            </div>
                            <? endif; ?>
                            <?
                            // PostaPont
                            if($o[szallitasiModID] == $this->settings['flagkey_postaponttransfer_id']): ?>
                            <div class="showSelectedPostaPont">
                                <div class="head">Kiválasztott <strong>PostaPont</strong>:</div>
                                <div class="p5">
                                    <div class="row np">
                                        <div class="col-md-12 center">
                                           <?=$o['postapont']?>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <? endif; ?>
                            </div>
                        </div>
                     </div>
                     <div class="row np">
                        <div class="col-md-12">
                            <div class="head"><strong>Kiválasztott fizetési mód:</strong></div>
                            <div class="data">

                            <? if($o['fizetesiModID'] == $this->settings['flagkey_pay_cetelem']): ?> <img src="<?=IMG?>/cetelem_badge.png" alt="Cetelem" style="height: 32px; float: left; margin: -5px 10px 0 0;"> <? endif; ?>
                            <?=$this->fizetes[Helper::getFromArrByAssocVal($this->fizetes,'ID',$o[fizetesiModID])][nev]; ?>
                            <?
                            // PayU kártyás fizetés
                            if( $o['fizetesiModID'] == $this->settings['flagkey_pay_payu'] && $o['payu_fizetve'] == 0 ): ?>
                                <br>
                                <?=$this->pay_btn?>
                            <? elseif( $o['fizetesiModID'] == $this->settings['flagkey_pay_payu'] && $o['payu_fizetve'] == 1 ): ?>
                                <? if( $o['payu_teljesitve'] == 0 ): ?>
                                <span class="payu-paidonly">Fizetve. Visszaigazolásra vár.</span>
                                <? else: ?>
                                <span class="payu-paid-done">Fizetve. Elfogadva.</span>
                                <? endif; ?>
                            <? endif; ?>

                            <? // PayPal fizetés
                            if($this->fizetes[Helper::getFromArrByAssocVal($this->fizetes,'ID',$o[fizetesiModID])][nev] == 'PayPal' && $o[paypal_fizetve] == 0): ?>
                                <div style="padding:10px 0;">
                                    <form name="_xclick" action="https://www.paypal.com/cgi-bin/webscr" method="post">
                                        <input type="hidden" name="cmd" value="_xclick">
                                        <INPUT TYPE="hidden" name="charset" value="utf-8">
                                        <input type="hidden" name="business" value="">
                                        <input type="hidden" name="currency_code" value="HUF">
                                        <input type="hidden" name="item_name" value="Megrendelés: <?=$o[azonosito]?>">
                                        <input type="hidden" name="amount" value="<?=$vegosszeg?>">
                                        <INPUT TYPE="hidden" NAME="return" value="<?=DOMAIN?>order/<?=$o[accessKey]?>/paid_via_paypal#pay">
                                        <input type="image" src="<?=IMG?>i/paypal_payout.svg" border="0" style="height:35px;" name="submit" alt="Make payments with PayPal - it's fast, free and secure!">
                                    </form>
                                </div>
                            <? elseif($o[paypal_fizetve] == 1): ?>
                                <br /><br />
                                <span style="font-size:13px;" class="label label-success">PayPal: Vételár fizetve!</span>
                            <? endif; ?>

                            <?
                            // Cetelem hitel
                            if( $o['fizetesiModID'] == $this->settings['flagkey_pay_cetelem'] ): ?>
                                <br><br>
                                <div class="cetelem-status">
                                    <div class="row">
                                        <div class="col-sm-3"><strong>Hiteligénylés állapota:</strong></div>
                                        <div class="col-sm-9">
                                            <? echo $this->cetelem_status; ?>
                                        </div>
                                    </div>
                                </div>
                                <? echo $this->render('templates/cetelem_order'); ?>

                            <? endif; ?>
                            </div>
                        </div>
                     </div>
                     <? if($o[coupon_code]): ?>
                     <div class="row np">
                        <div class="col-md-12">
                            <div class="head"><strong>Felhasznált kuponkód:</strong></div>
                            <div class="data">
                                <?=$o[coupon_code]?>
                            </div>
                        </div>
                     </div>
                    <? endif; ?>
                    <? if($o[referer_code]): ?>
                     <div class="row np">
                        <div class="col-md-12">
                            <div class="head"><strong>Felhasznált ajánló partnerkód:</strong></div>
                            <div class="data">
                                <?=$o[referer_code]?>
                            </div>
                        </div>
                     </div>
                    <? endif; ?>
                    <? if($o[used_cash] != 0): ?>
                     <div class="row np">
                        <div class="col-md-12">
                            <div class="head"><strong>Felhasznált virtuális egyenleg:</strong></div>
                            <div class="data">
                                <?=$o[used_cash]?> Ft
                            </div>
                        </div>
                     </div>
                    <? endif; ?>
                     <div class="row np">
                        <div class="col-sm-12">
                            <div class="head"><strong>Vásárlói megjegyzés a megrendeléshez:</strong></div>
                            <div class="data">
                            <em><?=($o[comment] == '') ? '&mdash; nincs megjegyzés &mdash; ' : $o[comment]?></em>
                            </div>
                        </div>
                     </div>
                     <div class="row np">
                         <div class="col-sm-6 order-info">
                            <div class="head"><strong>Számlázási adatok</strong></div>
                            <div class="inforows">
                                <? $szam = json_decode($o[szamlazasi_keys],true); ?>
                                <? foreach($szam as $h => $d): ?>
                                    <div class="col-md-4"><?=$nevek[$h]?></div>
                                    <div class="col-md-8"><?=$d?></div>
                                <? endforeach; ?>
                            </div>
                         </div>
                         <div class="col-sm-6 order-info">
                            <div class="head"><strong>Szállítási adatok</strong></div>
                             <div class="inforows">
                                <? $szall = json_decode($o[szallitasi_keys],true); ?>
                                <? foreach($szall as $h => $d): ?>
                                    <div class="col-md-4"><?=$nevek[$h]?></div>
                                    <div class="col-md-8"><?=$d?></div>
                                <? endforeach; ?>
                            </div>
                         </div>
                     </div>
                </div>
            </div>
            <? else: ?>
            <div class="box">
                <div class="noItem">
                    <div>Hibás megrendelés azonosító</div>
                </div>
            </div>
            <? endif; ?>
        </div>
    </div>
</div>
</div>
