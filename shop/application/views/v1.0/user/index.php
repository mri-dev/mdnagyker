<div class="account page-width">
    <? if( $this->user['alerts']['has_alert'] > 0 ): ?>
    <div class="alert-view">
        <h2>Értesítések</h2>
        <div>
            <? foreach( $this->user['alerts']['alerts'] as $alert ): ?>
            <div class="item <?=$alert[type]?>">
                <? if($alert['url']): ?>
                <a class="url" href="<?=$alert['url']?>">megtekint <i class="fa fa-arrow-circle-right"></i></a>
                <? endif; ?>
                <div class="text">
                    <?=$alert['text']?>
                </div>
            </div>
            <? endforeach;?>
        </div>
    </div>
    <br>
    <div class="divider"></div>
    <br>
    <? endif; ?>
    <div class="grid-layout">
        <div class="grid-row grid-row-20"><? $this->render('user/inc/account-side', true); ?></div>
        <div class="grid-row grid-row-80">
            <h1>Megrendeléseim</h1>
            <div class="orderpage">
                <div class="">
                    <div class="flatInfoBox">
                        <div class="" align="center">
                            <div style="color:#d41c4f;; font-size:1.8em;"><?=count($this->orders[progress])?> db</div>
                            <div>folyamatban</div>
                        </div>
                        <div class="" align="center">
                            <div style="color:#444;; font-size:1.8em;"><?=count($this->orders[done])?> db</div>
                            <div>lezárt megrendelés</div>
                        </div>
                        <? if(false): ?>
                        <div class="" align="center">
                            <div style="color:#7CC359; font-size:1.5em;"><?=$this->user[data][cash]?></div>
                            <div>virtuális egyenleg</div>
                        </div>
                        <? endif; ?>
                    </div>
                </div>
                <div class="items divBtm">
                    <h4>Folyamatban lévő megrendelések (<?=count($this->orders[progress])?>)</h4>
                    <div>
                        <div class="mobile-table-container overflowed">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <td width="110">Azonosító</td>
                                        <td width="80">Tétel</td>
                                        <td width="120">Fizetendő</td>
                                        <td width="195">Megrendelve</td>
                                        <td width="50"></td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <? if(count($this->orders[progress])>0): foreach($this->orders[progress] as $d): ?>
                                    <tr>
                                        <td align="left">
                                            <span class="transid"><?=$d[azonosito]?></span> <br>
                                            <strong style="color:<?=$d[allapotSzin]?>;"><?=$d[allapotNev]?></strong>
                                            <? if( $d['fizetesiModID'] == $this->settings['flagkey_pay_payu'] ): ?>
                                                <? if( $d['payu_fizetve'] == 1 && $d['payu_teljesitve'] == 0 ): ?>
                                                <span class="payu-paidonly">Fizetve. Visszaigazolásra vár.</span>
                                                <? elseif($d['payu_fizetve'] == 1 && $d['payu_teljesitve'] == 1): ?>
                                                <span class="payu-paid-done">Fizetve. Elfogadva.</span>
                                                <? endif; ?>
                                            <? endif; ?>
                                        </td>
                                        <td align="center"><?=$d[itemNums]?> db</td>
                                        <td align="center"><strong><?=Helper::cashFormat($d[totalPrice]+$d[szallitasi_koltseg]-$d[kedvezmeny])?> Ft</strong></td>
                                        <td align="center"><?=\PortalManager\Formater::dateFormat($d[idopont], $this->settings['date_format'])?></td>
                                        <td align="center"><a class="btn btn-default btn-sm" style="color:black" href="/order/<?=$d[accessKey]?>" target="_blank">részletek <i class="fa fa-arrow-circle-right"></i></a></td>
                                    </tr>
                                    <? endforeach; else: ?>
                                    <tr>
                                        <td colspan="10">
                                            <div class="noItem">
                                                <div>Nincs folyamatban lévő megrendelése!</div>
                                            </div>
                                        </td>
                                    </tr>
                                    <? endif; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="a-up"><a href="#top" class="right">lap tetejére <i class="fa fa-angle-up"></i></a></div>
                     </div>
                </div>

                <div class="p10 items" style="background:#fff;">
                    <h4>Lezárt megrendelések (<?=count($this->orders[done])?>)</h4>
                    <div>
                        <div class="mobile-table-container overflowed">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <td width="110">Azonosító</td>
                                    <td width="80">Tétel</td>
                                    <td width="120">Fizetendő</td>
                                    <td width="195">Megrendelve</td>
                                    <td width="50"></td>
                                </tr>
                            </thead>
                            <tbody>
                                <? if(count($this->orders[done])>0): foreach($this->orders[done] as $d): ?>
                                <tr>
                                    <td align="left">
                                            <span class="transid"><?=$d[azonosito]?></span> <br>
                                            <strong style="color:<?=$d[allapotSzin]?>;"><?=$d[allapotNev]?></strong>
                                        </td>
                                    <td align="center"><?=$d[itemNums]?> db</td>
                                    <td align="center"><strong><?=Helper::cashFormat($d[totalPrice]+$d[szallitasi_koltseg]-$d[kedvezmeny])?> Ft</strong></td>
                                    <td align="center"><?=\PortalManager\Formater::dateFormat($d[idopont], $this->settings['date_format'])?></td>
                                    <td align="center"><a class="btn btn-default btn-sm" style="color:black" href="/order/<?=$d[accessKey]?>" target="_blank">részletek <i class="fa fa-arrow-circle-right"></i></a></td>
                                </tr>
                                <? endforeach; else: ?>
                                <tr>
                                    <td colspan="10">
                                        <div class="noItem">
                                            <div>Nincs lezárt megrendelése!</div>
                                        </div>
                                    </td>
                                </tr>
                                <? endif; ?>
                            </tbody>
                        </table>
                        </div>
                        <div class="a-up"><a href="#top" class="right">lap tetejére <i class="fa fa-angle-up"></i></a></div>
                     </div>
                </div>
            </div>

        </div>
    </div>
</div>
