<?php
$o = $this->order;
$nevek = array(
    'nev' => 'Név',
    'hazszam' => 'Házszám',
    'city' => 'Település',
    'kerulet' => 'Kerület',
    'kozterulet_nev' => 'Közterület neve',
    'kozterulet_jelleg' => 'Közterület jellege',
    'adoszam' => 'Adószám',
    'irsz' => 'Irányítószám',
    'epulet' => 'Épület',
    'lepcsohaz' => 'Lépcsőház',
    'szint' => 'Szint',
    'ajto' => 'Ajtó',
    'phone' => 'Telefonszám',
);
$vegosszeg = 0;
$termek_ar_total = 0;

foreach($o[items] as $d):
    $vegosszeg += $d[subAr];
    $termek_ar_total += $d[subAr];
endforeach;

if($o[szallitasi_koltseg] > 0) $vegosszeg += $o[szallitasi_koltseg];
//  if($o[kedvezmeny] > 0) $vegosszeg -= $o[kedvezmeny];

$discount = $o[kedvezmeny_szazalek];
?>
<div class="order re-order page-width">
  <form class="" action="" method="post">
  <div class="header">
    <h1><i class="fa fa-refresh"></i><br><strong><?php echo $this->order['azonosito']; ?></strong><br>megrendelés újrarendelése</h1>
    <div class="desc">
      A termékek újra rendelése során eltérő lehet a termékek ára a korábban leadott rendeléshez képest. Kérjük, hogy tekintse át a megrendelendő tételeket.
    </div>
    <div class="order-status">
      <div class="h">
        Korábbi megrendelés státusza:
      </div>
      <div class="">
        <span style="color:<?=$this->orderAllapot[$o[allapot]][szin]?>;"><strong><?=$this->orderAllapot[$o[allapot]][nev]?></strong></span>
      </div>
    </div>
    <br>
    <?php echo $this->msg; ?>
  </div>
  <div class="datas">
    <h4>Újra rendelendő termékek</h4>
    <div class="items">
       <div class="mobile-table-container overflowed">
       <div class="items-table">
       <table class="table table-bordered">
           <thead>
               <tr>
                   <td>Termék</td>
                   <td width="80" class="center">Mennyiség</td>
                   <td width="80" class="center">Egység</td>
                   <td width="140" class="center">Egységár</td>
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
                                   <img src="<?=\PortalManager\Formater::productImage($d[profil_kep], false, \ProductManager\Products::TAG_IMG_NOPRODUCT)?>" alt="<?=$d[nev]?>">
                               </a>
                           </div>
                           <div class="name">
                               <a href="<?=$d[url]?>" target="_blank"><?=$d[nev]?></a>
                               <div class="sel-types">
                                 <?php if ($d['configs']): ?>
                                   <i class="fa fa-gear" title="Kiválasztott konfiguráció"></i>
                                   &nbsp;
                                   <?php foreach ((array)$d['configs'] as $cid => $c): ?>
                                     <em><?php echo $c['parameter']; ?>:</em> <strong><?php echo $c['value']; ?></strong>
                                   <?php endforeach; ?>
                                 <?php endif; ?>
                               </div>
                           </div>
                       </div>
                   </td>
                   <td class="center">
                     <input type="number" class="form-control" name="reorder[me][<?=$d['termekID']?>]" value="<?=$d[me]?>">
                   </td>
                   <td class="center">
                     <?php if ($d['mertekegyseg_ertek'] != 1): ?>
                       <?=$d['mertekegyseg_ertek']?> <?=$d['mertekegyseg']?>
                     <?php else: ?>
                      <?=$d['mertekegyseg']?>
                     <?php endif; ?>
                   </td>
                   <td class="center"><span><?=Helper::cashFormat($d[egysegAr])?> Ft</span></td>
                   <td class="center"><span><?=Helper::cashFormat($d[subAr])?> Ft</span></td>
               </tr>
               <? endforeach; ?>

               <tr>
                  <td class="right"></td>
                   <td class="center" colspan="2">
                     <button class="btn form-control btn-sm btn-sec" type="submit" name="saveReorderData" value="1"><i class="fa fa-refresh"></i> frissítés</button>
                   </td>
                   <td class="right"><strong>Termékek ára</strong></td>
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
                  <? foreach($szam as $h => $d): if($d == '') continue; ?>
                      <div class="col-md-4"><?=$nevek[$h]?></div>
                      <div class="col-md-8"><?=($d  != '')?$d:'&nbsp;'?></div>
                  <? endforeach; ?>
              </div>
           </div>
           <div class="col-sm-6 order-info">
              <div class="head"><strong>Szállítási adatok</strong></div>
               <div class="inforows">
                  <? $szall = json_decode($o[szallitasi_keys],true); ?>
                  <? foreach($szall as $h => $d): if($d == '') continue; ?>
                      <div class="col-md-4"><?=$nevek[$h]?></div>
                      <div class="col-md-8"><?=($d  != '')?$d:'&nbsp;'?></div>
                  <? endforeach; ?>
              </div>
           </div>
       </div>
  </div>
  <div class="datas">
    <h4>Véglegesítés</h4>
    <br>
    <div class="row np">
      <div class="col-sm-12 center">
        <textarea name="reorder[comment]" class="form-control" style="min-height: 120px;" placeholder="Megjegyzés..."></textarea>
      </div>
    </div>
    <br>
    <div class="row np">
      <div class="col-sm-12 center">
        <input type="checkbox" id="aszf_ok" name="aszf_ok"><label for="aszf_ok">Megrendelésemmel elfogadom a(z) <?=$this->settings['page_title']?> mindenkor hatályos <a href="<?=$this->settings['ASZF_URL']?>" target="_blank">Általános Szerződési Feltételek</a>et!</label>
        <br><br>
        <button type="submit" class="btn btn-success reorder-button" name="doReorder" value="1">Megrendelés újra rendelése <i class="fa fa-arrow-circle-right"></i></button>
      </div>
    </div>
  </div>

  <pre><?php //print_r($this->order); ?></pre>
  </form>
</div>
