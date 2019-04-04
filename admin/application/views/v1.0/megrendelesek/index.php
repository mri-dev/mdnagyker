<?php
$nevek = array(
	'nev' => 'Név',
	'adoszam' => 'Adószám',
	'city' => 'Város',
	'irsz' => 'Irányítószám',
	'phone' => 'Telefonszám',
	'kerulet' => 'Kerület',
	'kozterulet_nev' => 'Közterület neve',
	'kozterulet_jelleg' => 'Közterület jellege',
	'epulet' => 'Épület',
	'hazszam' => 'Házszám',
	'lepcsohaz' => 'Lépcsőház',
	'ajto' => 'Ajtó',
	'szint' => 'Szint',
);
?>
<div style="float:right;">
	<? if(isset($_COOKIE['filter_archivalt'])): ?>
	<a href="/megrendelesek?cleararchive=1" class="btn btn-success"><i class="fa fa-circle"></i> Aktuális megrendelések</a>
	<? else: ?>
	<a href="/megrendelesek?showarchive=1" class="btn btn-danger"><i class="fa fa-archive"></i> Archivált megrendelések</a>
	<? endif; ?>
	<?php if (false): ?>
	<a href="/partnerSale" class="btn btn-default"><i class="fa fa-briefcase"></i> Ajánlókódos megrendelések</a>
	<?php endif; ?>
	<a href="/<?=$this->gets[0]?>/allapotok" class="btn btn-default"><i class="fa fa-bars"></i> Megrendelés állapotok</a>
	<a href="/<?=$this->gets[0]?>/termek_allapotok" class="btn btn-default"><i class="fa fa-bars"></i> Megrendelt termékek állapotai</a>
</div>
<h1>
<? if(isset($_COOKIE['filter_archivalt'])): ?>
Archivált megrendelések
<? else: ?>
Megrendelések
<? endif; ?>
	<span>
       	<? if($_COOKIE[filtered] == '1'): ?><span class="filtered">Szűrt listázás <a href="/megrendelesek/clearfilters/" title="szűrés eltávolítása" class="actions"><i class="fa fa-times-circle"></i></a></span><? endif; ?>
    </span>
</h1>
<?=$this->msg?>
<pre><?php //print_r($this->megrendelesek['data']); ?></pre>
<form action="" method="post">
<div class="right transport-action-buttons">
	<button type="button" class="mpl_pp" onclick="collectExportingTransports('pp');">MPL - Posta Pont címirat (.csv)</button>
  <button type="button" class="mpl_futar" onclick="collectExportingTransports('mpl');">MPL - Házhoz címirat (.csv)</button>
</div>
<div class="tbl-container overflowed">
<table class="table termeklista table-bordered">
	<thead>
    	<tr>
			<th width="40">#</th>
            <th width="100">Azonosító</th>
            <th>Név/E-mail</th>
            <th width="200">Állapot</th>
            <th width="150">Átvételi mód</th>
            <th width="130">Fizetési mód</th>
            <th width="50">Tétel</th>
            <th width="100">Végösszeg</th>
            <th width="50">Kedvezmény</th>
            <th width="140">Megrendelve</th>
            <th width="35"></th>
        </tr>
	</thead>
    <tbody>
    	<tr class="search <? if($_COOKIE[filtered] == '1'): ?>filtered<? endif;?>">
    		<td><input type="text" name="ID" class="form-control" value="<?=$_COOKIE[filter_ID]?>" /></td>
            <td><input type="text" name="azonosito" class="form-control" value="<?=$_COOKIE[filter_azonosito]?>" /></td>
            <td><input type="text" name="access" class="form-control" value="<?=$_COOKIE[filter_access]?>" placeholder="Név vagy e-mail részlet..." /></td>
            <td><select class="form-control"  name="fallapot" style="max-width:200px;">
            	<option value="" selected="selected"># Mind</option>
                	<? foreach($this->allapotok[order] as $m): ?>
                    <option value="<?=$m[ID]?>" <?=($m[ID] == $_COOKIE[filter_fallapot])?'selected':''?>><?=$m[nev]?></option>
                    <? endforeach; ?>
                </select></td>
            <td><select class="form-control"  name="fszallitas" style="max-width:150px;">
            	<option value="" selected="selected"># Mind</option>
                	<? foreach($this->szallitas as $m): ?>
                    <option value="<?=$m[ID]?>" <?=($m[ID] == $_COOKIE[filter_fszallitas])?'selected':''?>><?=$m[nev]?></option>
                    <? endforeach; ?>
                </select></td>
            <td><select class="form-control"  name="ffizetes" style="max-width:150px;">
            	<option value="" selected="selected"># Mind</option>
                	<? foreach($this->fizetes as $m): ?>
                    <option value="<?=$m[ID]?>" <?=($m[ID] == $_COOKIE[filter_ffizetes])?'selected':''?>><?=$m[nev]?></option>
                    <? endforeach; ?>
                </select></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
    		<td align="center">
            	<button name="filterList" value="1" class="btn btn-default"><i class="fa fa-search"></i></button>
            </td>
    	</tr>
    	<? if(count($this->megrendelesek[data]) > 0): foreach($this->megrendelesek[data] as $d):  ?>
		<?
		$preorders 	= 0;
		$itemNum 	= 0;
		foreach($d[items][data] as $item){
			if($item[elorendelt] == '1') $preorders += $item[me];
			$itemNum 	+= $item[me];
		}?>
    	<tr id="o_<?=$d[ID]?>" class="o">
				<td align="center" valign="middle" style="border-left:5px solid <?=$this->allapotok[order][$d[allapot]][szin]?>;"><?=$d[ID]?> <input type="checkbox" class="selecting" name="export[]" value="<?=$d[accessKey]?>"></td>
            <td align="center" valign="middle">
            	<?=($d['prev_order'])?'<i class="fa fa-refresh" title="Gyors újrarendelés: '.$d['prev_order'].'."></i>':''?><a href="javascript:void(0);"  mid="<?=$d[ID]?>" ><?=$d[azonosito]?></a>
            </td>
            <td>
                <div class="ind feat">
                <? if( !is_null($d['coupon_code']) ): ?><i class="fa fa-ticket" title="Felhasznált kuponkód"></i><? endif; ?>
                <? if( !is_null($d['referer_code']) ): ?><i class="fa fa-users" title="Ajánló partnerkód kedvezmény"></i><? endif; ?>
                <? if( $d['used_cash'] != 0 ): ?><i class="fa fa-money" title="Felhasznált virtuális egyenleg"></i><? endif; ?>
                </div>

				<input type="hidden" name="accessKey[<?=$d[ID]?>]" value="<?=$d[accessKey]?>" />
            	<div class="nev"><?=$d[nev]?> (<em style="font-weight:normal;"><?=$d[email]?></em>)</div>
                <div>
                <? if($d['userID']): ?>
                <span class="hasRegistered">Regisztrált tag <i class="fa fa-check"></i></span>

								<span class="hasRegisteredBy<?php if ($d['user'] && $d['user']['user_group'] == \PortalManager\Users::USERGROUP_COMPANY ){ echo 'Company'; }else{ echo 'User'; } ?>"><?php echo $d['user']['ar_csoport']; ?> [<?php echo $d['user']['price_group_key'] ?>]</span>
                <? endif; ?>
			<a href="<?=HOMEDOMAIN?>order/<?=$d[accessKey]?>" target="_blank">Adatlap</a>
			<? if( $d[comment] != '' ): ?>
				&nbsp;&nbsp;<span style="color:#eb6464;"><i class="fa fa-file-text-o"></i> Megjegyzés</span>
			<? endif; ?>
                <span class="sprinter-trans-export" style="display:none;">&nbsp;<label><input type="checkbox" class="sprinter_exp" name="sprinter_exp[]" value="<?=$d[accessKey]?>"> sprinter futár (.csv)</label></span>
								</div>
            </td>
            <td class="center">
                <strong style="color:<?=$this->allapotok[order][$d[allapot]][szin]?>;"><?=$this->allapotok[order][$d[allapot]][nev]?></strong>
                <?
                // PayU pay info
                if($d[fizetesiModID] == $this->settings['flagkey_pay_payu']): ?>
                <div>
                   <? if( $d['payu_fizetve'] == 1 && $d['payu_teljesitve'] == 0 ): ?>
                    <span class="payu-paidonly">Fizetve. Visszaigazolásra vár.</span>
                    <? elseif($d['payu_fizetve'] == 1 && $d['payu_teljesitve'] == 1): ?>
                    <span class="payu-paid-done">Fizetve. Elfogadva.</span>
                    <? endif; ?>
                </div>
                <? endif;?>
                 <?
                // Cetelem info
                if($d[fizetesiModID] == $this->settings['flagkey_pay_cetelem']): ?>
                <div>
                   <?=$this->cetelem->getTransactionStatus($d[accessKey], true)?>
                </div>
                <? endif;?>
            </td>
            <td class="center"><?=$this->szallitas[$d[szallitasiModID]][nev]?></td>
            <td class="center">
                <?=$this->fizetes[$d[fizetesiModID]][nev]?>
            </td>
            <td class="center"><?=$d[items][tetel]?></td>
            <td class="center"><strong><?=Helper::cashFormat($d[items][total]+$d[szallitasi_koltseg]+($d[kedvezmeny]*-1))?> Ft</strong></td>
            <td class="center"><?=Helper::cashFormat($d['kedvezmeny'])?> Ft</td>
            <td class="center"><?=\PortalManager\Formater::dateFormat($d[idopont], $this->settings['date_format'])?></td>
            <td class="center"><button name="filterList" title="Részletek" mid="<?=$d[ID]?>" type="button" class="btn btn-default btn-sm watch"><i class="fa fa-eye"></i></button></td>
        </tr>
       	<tr class="oInfo" id="oid_<?=$d[ID]?>" style="display:none;">
       		<td colspan="25" style="padding:0;">
            	<div class="row orderInfo">
                	<div class="col-md-7">
                        <? if($d[kedvezmeny_szazalek] > 0): ?>
                        <div class="discounted-info-price">A termékek árai <?=$d[kedvezmeny_szazalek]?>%-kal csökkentetve jelennek meg! </div>
                        <? endif; ?>
                    	<table class="items" width="100%">
                        	<thead>
                        		<tr>
                        			<th colspan="2">Termék</th>
                              <th width="150">Cikkszám</th>
                        			<th width="50">Me.</th>
                        			<th width="80">E. Ár</th>
                        			<th width="120">Össz. Ár</th>
                        			<th>Állapot</th>
                        		</tr>
                        	</thead>
                    		<tbody>
                            	<?
                                $c_total = 0;
                                foreach($d[items][data] as $item): $c_total += $item[subAr]; ?>
                    					<tr>
	                                	<td width="35"><div class="img"><img src="<?=\PortalManager\Formater::productImage($item[profil_kep], false, \ProductManager\Products::TAG_IMG_NOPRODUCT)?>" alt="" /></div>
																		</td>
                    								<td>
									   									<a href="<?=HOMEDOMAIN.'termek/'.\PortalManager\Formater::makeSafeUrl($item[termekNev],'_-'.$item[termekID])?>" target="_blank"><?=($item[termekNev]) ?: '-törölt termék-'?></a>
																			<?php if ($item['configs']): ?>
																				<div class="config">
																					<i class="fa fa-gear" title="Kiválasztott konfiguráció"></i>
																					<?php foreach ((array)$item['configs'] as $conf ): ?>
																						<span><?=$conf['parameter']?>: <strong><?=$conf['value']?></strong></span>
																					<?php endforeach; ?>
																				</div>
																			<?php endif; ?>
                                      <div class="item-number">
																				 <span class="number tid" title="Termék ID">#<?=$item['termekID']?></span>
																				 <span class="number tid" title="Cashman FX ID">CMFX #<?=$item['cmfx_id']?></span>
																			</div>
                                    </td>
																		<td class="center"><span class="cikkszam"><?=$item['cikkszam']?></span></td>
                                    <td class="center">
                                    	<input type="number" name="termekMe[<?=$d[ID]?>][<?=$item[ID]?>]" value="<?=$item[me]?>" min="0" class="form-control" />
                                        <input type="hidden" value="<?=$item[me]?>" name="prev_termekMe[<?=$d[ID]?>][<?=$item[ID]?>]" />
                                    </td>
                                    <td class="center">
																			<input type="number" name="termekAr[<?=$d[ID]?>][<?=$item[ID]?>]" value="<?=$item[egysegAr]?>" min="0" class="form-control" />
                                      <input type="hidden" value="<?=$item[egysegAr]?>" name="prev_termekAr[<?=$d[ID]?>][<?=$item[ID]?>]" />
									</td>
                                    <td class="center"><?=Helper::cashFormat($item[subAr])?> Ft</td>
                                    <td class="center" width="200">
                                    <select class="form-control" name="termekAllapot[<?=$d[ID]?>][<?=$item[ID]?>]" style="max-width:200px;">
										<? foreach($this->allapotok[termek] as $m):  ?>
                                        <option style="color:<?=$m[szin]?>;" value="<?=$m[ID]?>" <?=($m[ID] == $item[allapotID])?'selected':''?>><?=$m[nev]?></option>
                                        <? endforeach; ?>
                                    </select>
                                    <input type="hidden" value="<?=$item[allapotID]?>" name="prev_termekAllapot[<?=$d[ID]?>][<?=$item[ID]?>]" />
                                    </td>
                    			</tr>
                                <? endforeach; ?>
                                <tr style="background:#f3f3f3;">
                                    <td class="right" colspan="5">Termékek összesített ára:</td>
                                    <td class="center"><strong><?=Helper::cashFormat($c_total)?> Ft</strong></td>
                                    <td class="right" colspan="2">
                                        <a href="javascript:void(0);" onclick="addNewItem(<?=$d[ID]?>);">termék hozzáadás <i class="fa fa-plus"></i></a>
                                    </td>
                                </tr>
                    		</tbody>
                    	</table>
                        <? if( !is_null($d['coupon_code']) ): ?>
                        <div class="coupon-used">
                            <div class="row">
                                <div class="col-sm-3 left">Felhasznált kupon:</div>
                                <div class="col-sm-9 right"><strong><?=$d['coupon']->getTitle()?></strong> (<?=$d['coupon_code']?>)</div>
                            </div>
                        </div>
                        <? endif; ?>
                        <? if( !is_null($d['referer_code']) ): ?>
                        <div class="referer-used">
                            <div class="row">
                                <div class="col-sm-3 left">Ajánló partner (kód):</div>
                                <div class="col-sm-9 right"><strong><a href="/felhasznalok/?ID=<?=$d['referer']->getPartnerID()?>" target="_blank"><?=$d['referer']->getPartnerName(false)?></a></strong> (<?=$d['referer_code']?>)</div>
                            </div>
                        </div>
                        <? endif; ?>
                        <? if( $d['used_cash'] != 0 ): ?>
                        <div class="referer-used">
                            <div class="row">
                                <div class="col-sm-3 left">Felhasznált egyenleg:</div>
                                <div class="col-sm-9 right"><strong><?=\Helper::cashFormat($d['used_cash'])?> Ft</strong></div>
                            </div>
                        </div>
                        <? endif; ?>
                        <div id="newitem_c<?=$d[ID]?>"></div>
                    </div>
                    <div class="col-md-5" style="border-left:1px solid #eee;">
                    	<div class="row">
                        	<div class="col-md-6 selectCol"><strong>Megrendelés állapot:</strong></div>
                            <div class="col-md-6 right">
                            <select class="form-control" name="allapotID[<?=$d[ID]?>]">
								<? foreach($this->allapotok[order] as $m): ?>
                                <option style="color:<?=$m[szin]?>;" value="<?=$m[ID]?>" <?=($m[ID] == $d[allapot])?'selected':''?>><?=$m[nev]?></option>
                                <? endforeach; ?>
                            </select>
                            <input type="hidden" value="<?=$d[allapot]?>" name="prev_allapotID[<?=$d[ID]?>]" />
                            </div>
                        </div>

                        <div class="row">
                        	<div class="col-md-10 selectCol"><strong>Kedvezmény (Ft):</strong></div>
                            <div class="col-md-2">
                            <input type="number" class="form-control" name="kedvezmeny[<?=$d[ID]?>]" min="0" value="<?=$d[kedvezmeny]?>" />
                            <input type="hidden" value="<?=$d[kedvezmeny]?>" name="prev_kedvezmeny[<?=$d[ID]?>]" />
                            </div>
                        </div>

                        <div class="row">
                        	<div class="col-md-9 selectCol"><strong>Szállítási költség (Ft):</strong></div>
                            <div class="col-md-3">
                            <input type="number" class="form-control" name="szallitasi_koltseg[<?=$d[ID]?>]" min="0" value="<?=$d[szallitasi_koltseg]?>" />
                            <input type="hidden" value="<?=$d[szallitasi_koltseg]?>" name="prev_szallitasi_koltseg[<?=$d[ID]?>]" />
                            </div>
                        </div>

                        <div class="row">
                        	<div class="col-md-7 selectCol"><strong>Átvételi mód:</strong></div>
                            <div class="col-md-5">
                           	<select class="form-control"  name="szallitas[<?=$d[ID]?>]">
                                <? foreach($this->szallitas as $m): ?>
                                <option value="<?=$m[ID]?>" <?=($m[ID] == $d[szallitasiModID])?'selected':''?>><?=$m[nev]?></option>
                                <? endforeach; ?>
                            </select>
                            <input type="hidden" value="<?=$d[szallitasiModID]?>" name="prev_szallitas[<?=$d[ID]?>]" />
                            </div>
                        </div>
                        <?
                        // PickPackPont
                        if($d[szallitasiModID] == $this->settings['flagkey_pickpacktransfer_id']): ?>
                         <div class="row">
                         	<div class="col-md-12">
                            <div class="selPPP" align="right">
                                <label for="pickpackpont_uzlet_kod">Kiválasztott Pick Pack Pont:</label>
                                <input type="text" id="pickpackpont_uzlet_kod" name="pickpackpont_uzlet_kod" class="form-control" value="<?=$d['pickpackpont_uzlet_kod']?>">
                                <input type="hidden" value="<?=$d['pickpackpont_uzlet_kod']?>" name="prev_pickpackpont_uzlet_kod" />
                                <div class="right"><a href="http://online.sprinter.hu/terkep/#/" target="_blank" style="color:black;">térképes kereső</a></div>
                            </div>
                            </div>
                       	</div>
                        <? endif; ?>
                        <?
                        // PostaPont
                        if($d[szallitasiModID] == $this->settings['flagkey_postaponttransfer_id']): ?>
                         <div class="row">
                            <div class="col-md-12">
                            <div class="selPP" align="right">
                                Kiválasztott PostPont:<br>
                                <strong><?=$d[postapont]?></strong>
                                </div>
                            </div>
                        </div>
                        <? endif; ?>

                        <div class="row">
                        	<div class="col-md-7 selectCol"><strong>Fizetési mód:</strong></div>
                            <div class="col-md-5">
                               	<select class="form-control"  name="fizetes[<?=$d[ID]?>]">
                                    <? foreach($this->fizetes as $m): ?>
                                    <option value="<?=$m[ID]?>" <?=($m[ID] == $d[fizetesiModID])?'selected':''?>><?=$m[nev]?></option>
                                    <? endforeach; ?>
                                </select>
                                <input type="hidden" value="<?=$d[fizetesiModID]?>" name="prev_fizetes[<?=$d[ID]?>]" />
                            </div>
                            <? if( $d['fizetesiModID'] == $this->settings['flagkey_pay_payu'] ): ?>
                            <div class="col-md-12">
                                    <?
                                    /**
                                     * PAYU IDN
                                     */
                                    if( $d['payu_fizetve'] == 1 && $d['payu_teljesitve'] == 0 && false ): ?>
                                    <div class="right" style="margin-bottom:5px;">
                                        <a target="_blank" href="<?=HOMEDOMAIN?>gateway/payu/idn/<?=$d['accessKey']?>" class="btn btn-sm btn-success">Fizetés elfogadása manuálisan (PayU IDN)</a>
                                    </div>
                                    <? endif; ?>
                                    <div class="payu-ipn-msgs">
                                        <a title="Kattintson az IPN-ek megjelenítéséhez!" href="javascript:void(0);" onclick="$('#payuipns_<?=$d['ID']?>').slideToggle(400);">
                                            OTP Simple szerver üzenetek (IPN) (<?=count($d['payu_ipn'])?> db)
                                        </a>
                                        <div class="ipn-list" id="payuipns_<?=$d['ID']?>" style="display:none;">
                                            <? if( count($d['payu_ipn']) > 0): foreach( $d['payu_ipn'] as $ipn ): ?>
                                            <div>
                                                <span class="status"><em><?=$ipn['statusz']?></em></span>
                                                <span class="time"><em><?=$ipn['idopont']?></em></span>
                                                <div class="clr"></div>
                                            </div>
                                            <? endforeach; else: ?>
                                                <div class="no-ipn">Nincs  OTP Simple szerver üzenet!</div>
                                            <? endif; ?>
                                        </div>
                                    </div>
                            </div>
                            <? endif; ?>

                            <? if( $d['fizetesiModID'] == $this->settings['flagkey_pay_cetelem'] ): ?>
                            <?
                                $ipns = $this->cetelem->getIPNList($d['accessKey']);

                            ?>
                            <div class="col-md-12">
                                <div class="payu-ipn-msgs">
                                    <a title="Kattintson az IPN-ek megjelenítéséhez!" href="javascript:void(0);" onclick="$('#cetelemipns_<?=$d['ID']?>').slideToggle(400);">
                                        Cetelem szerver állapot-visszajelzések (<?=count($ipns)?> db)
                                    </a>
                                    <div class="ipn-list" id="cetelemipns_<?=$d['ID']?>" style="display:none;">
                                        <? if( count($ipns) > 0): foreach( $ipns as $ipn ): ?>
                                        <div>
                                            <span class="status" style="background: <?=$ipn['statusz']['color']?>; color: white; text-shadow: none;"><em><?=$ipn['statusz']['text']?></em></span>
                                            <span class="time"><em><?=$ipn['idopont']?></em></span>
                                            <div class="clr"></div>
                                        </div>
                                        <? endforeach; else: ?>
                                            <div class="no-ipn">Nincs Cetelem szerver állapot-visszajelzés!</div>
                                        <? endif; ?>
                                    </div>
                                </div>
                            </div>
                            <? endif; ?>
                        </div>
												<div class="row">
													<div class="col-md-10 selectCol"><strong>Csomag súlya (gramm):</strong></div>
														<div class="col-md-2">
														<input type="number" class="form-control" name="csomag_suly[<?=$d[ID]?>]" min="0" value="<?=$d[csomag_suly]?>" />
														</div>
												</div>
						<div class="row">
                        	<div class="col-md-7 selectCol"><strong>Vásárlói megjegyzés:</strong></div>
                            <div class="col-md-5">
								<em><?=$d[comment]?></em>
                            </div>
                        </div>
                        <div class="divider"></div>
                        <div class="row" style="margin-top:10px;">
                        	<div class="col-md-6">
                            	<div><strong>Számlázási adatok</strong></div>
                                <? $szam = json_decode($d[szamlazasi_keys]);?>
                                <div>
                                	<? foreach($szam as $szmk => $szmv): ?>
                                   		<div class="row">
                                        	<div class="col-md-6 np selectCol em"><?=$nevek[$szmk]?></div>
                                        	<div class="col-md-6 np right"><input name="szamlazasi_adat[<?=$d[ID]?>][<?=$szmk?>]" type="text" class="form-control" value="<?=$szmv?>" /></div>
                                             <input type="hidden" value="<?=$szmv?>" name="prev_szamlazasi_adat[<?=$d[ID]?>][<?=$szmk?>]" />
                                        </div>
                                    <? endforeach; ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                            	<? $szall = json_decode($d[szallitasi_keys]);?>
                            	<div><strong>Szállítási adatok</strong></div>
                                <div>
                                	<? foreach($szall as $szllk => $szllv): ?>
                                   		<div class="row">
                                        	<div class="col-md-6 selectCol np em"><?=$nevek[$szllk]?></div>
                                        	<div class="col-md-6 np right"><input name="szallitasi_adat[<?=$d[ID]?>][<?=$szllk?>]" type="text" class="form-control" value="<?=$szllv?>" /></div>
                                            <input type="hidden" value="<?=$szllv?>" name="prev_szallitasi_adat[<?=$d[ID]?>][<?=$szllk?>]" />
                                        </div>
                                    <? endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
					<div class="col-md-6 left save">
						<? if($d['archivalva'] == 1): ?>
                       <button type="button" class="btn btn-success btn-sm">Archiválva <i class="fa fa-check"></i></button>
						<? else: ?>
                       <button type="button" data-orderid="<?=$d[ID]?>" class="btn btn-danger archive-order btn-sm">Archiválás <i class="fa fa-archive"></i></button>
						<? endif; ?>
                    </div>
                    <div class="col-md-6 right save">
											<input type="checkbox" id="alert_email_out" name="alert_email_out[<?=$d[ID]?>]" checked="checked" value="1"> <label for="alert_email_out">e-mail értesítés változásról</label>
                    	&nbsp;&nbsp;&nbsp; <button name="saveOrder" value="<?=$d[ID]?>" class="btn btn-success btn-sm">Változások mentése <i class="fa fa-save"></i></button>
                    </div>
                </div>
            </td>
       	</tr>
        <? endforeach; else: ?>
        <tr>
	    	<td colspan="15" align="center">
            	<div style="padding:25px;">Nincs találat!</div>
            </td>
        </tr>
        <? endif; ?>
    </tbody>
</table>
</div>
</form>
<ul class="pagination">
  <li><a href="/<?=$this->gets[0]?>/<?=($this->gets[1] != '')?$this->gets[1].'/':'-/'?>1">&laquo;</a></li>
  <? for($p = 1; $p <= $this->megrendelesek[info][pages][max]; $p++): ?>
  <li class="<?=(Helper::currentPageNum() == $p)?'active':''?>"><a href="/<?=$this->gets[0]?>/<?=($this->gets[1] != '')?$this->gets[1].'/':'-/'?><?=$p?>"><?=$p?></a></li>
  <? endfor; ?>
  <li><a href="/<?=$this->gets[0]?>/<?=($this->gets[1] != '')?$this->gets[1].'/':'-/'?><?=$this->megrendelesek[info][pages][max]?>">&raquo;</a></li>
</ul>

<script type="text/javascript">
    $(function(){
        $('*[mid]').click(function(){
            var e = $(this);
            var id = e.attr('mid');

            $('.oInfo').hide(0);
            $('.o').removeClass('opened');

            $('#o_'+id).addClass('opened');
            $('#oid_'+id).show(0);
        });

		$('button.archive-order').click(function(){
			var oid = $(this).data('orderid');
			if( !$(this).hasClass('btn-success') )
			{
				$(this).html('Archiválás folyamatban...');
				var ti = $(this);
				$.post('/ajax/post/',{
					type: 'setOrderArchived',
					id: oid
				}, function(r){
					ti.removeClass('btn-danger').addClass('btn-success').html('Archiválva <i class="fa fa-check"></i>');
				}, "html");
			}
			});
    })

		function collectExportingTransports( transporter ) {
      var items = $('input[type=checkbox][class*=selecting]:checked');
      var keys = '';
      items.each( function(i,e){
          keys += $(e).val()+",";
      });
			if (keys == '') {
				alert('A címirat(ok) letöltéséhez jelölje ki a kívánt megrendeléseket.');
			} else {
				keys = keys.slice(0, -1);
				document.location.href = '/csv/'+transporter+'/'+keys;
			}
    }

    function addNewItem (contid) {
       var cont = $('#newitem_c'+contid);

       $.post( "<?=AJAX_GET?>", {
          type :'loadAddNewItemsOnOrder'
       }, function(d){
          cont.append(d);
       },"html" );
    }
</script>
