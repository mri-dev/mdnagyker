<?
    $szallnev = array(
        'nev' => 'Név',
        'phone' => 'Telefonszám',
        'phone_korzet' => 'Körzetszám',
        'irsz' => 'Irányítószám',
        'city' => 'Település',
        'kerulet' => 'Kerület',
        'kozterulet_nev' => 'Közterület neve',
        'kozterulet_jelleg' => 'Közterület jellege',
        'hazszam' => 'Házszám',
        'epulet' => 'Épület',
        'lepcsohaz' => 'Lepcsőház',
        'szint' => 'Szint',
        'ajto' => 'Ajtó',
    );
    $szmnev = $szallnev;
    $szmnev['adoszam'] = 'Adószám';
    $req_items = array('nev', 'irsz', 'city', 'kozterulet_jelleg', 'kozterulet_nev', 'hazszam', 'phone');

    $missed_details = array();
    if( isset($_GET['missed_details']) ) {
        $missed_details = explode(",",$_GET['missed_details']);
    }
?>
<div class="account page-width">
 <div class="grid-layout">
    <div class="grid-row grid-row-20"><? $this->render('user/inc/account-side', true); ?></div>
    <div class="grid-row grid-row-80 settings">
        <? if( count( $missed_details ) > 0 ): ?>
            <?=Helper::makeAlertMsg('pError', 'Az Ön adatai hiányosak. Mielőtt bármit is tenne, kérjük, hogy pótolja ezeket!' );?>
        <? endif; ?>
        <h1>Beállítások</h1>
        <div class="divider"></div>
        <h4>Alapadatok</h4>
        <?=$this->msg['alapadat']?>
        <div class="form-rows">
            <form action="#alapadat" method="post">
                <div class="row">
                    <div class="col-md-3"><strong>E-mail cím:</strong></div>
                    <div class="col-md-9"><?=$this->user[email]?></div>
                </div>
                <div class="row">
                    <div class="col-md-3"><strong>Felhasználónév:</strong></div>
                    <div class="col-md-9"><?=$this->user[data][username]?></div>
                </div>
                <div class="row">
                    <div class="col-md-3 form-text-md"><strong>Név</strong></div>
                    <div class="col-md-5"><input name="nev" type="text" class="form-control" value="<?=$this->user[data][nev]?>" /></div>
                </div>
                <div class="row">
                    <div class="col-md-3"><strong>Utoljára belépve</strong></div>
                    <div class="col-md-5"><?=$this->user[data][utoljara_belepett]?> (<?=Helper::distanceDate($this->user[data][utoljara_belepett])?>)</div>
                </div>
                <div class="row ">
                    <div class="col-md-3"><strong>Regisztráció</strong></div>
                    <div class="col-md-5"><?=$this->user[data][regisztralt]?> (<?=Helper::distanceDate($this->user[data][regisztralt])?>)</div>
                </div>
                <? if( false ): ?>
                <div class="row">
                    <div class="col-md-12">
                        KEDVEZMÉNYEK
                    </div>
                </div>
                <? foreach( $this->user['kedvezmenyek'] as $kedv ): ?>
                <div class="row">
                    <div class="col-md-3"><strong><?=$kedv['nev']?></strong></div>
                    <div class="col-md-5"><a href="<?=$kedv['link']?>" title="részletek"><?=$kedv['kedvezmeny']?>%</a> <? if($kedv['nev'] === 'Arena Water Card' && $kedv['kedvezmeny'] === 0): ?> <a href="javascript:void(0);" onclick="$('#add-watercard').slideToggle(400);" class="add-water-card">kártya regisztrálása</a> <? endif; ?> </div>
                </div>
                <? endforeach; ?>
                <? endif; ?>
                <div class="row">
                    <div class="col-md-12 right"><button name="saveDefault" class="btn btn-sec btn-sm"><i class="fa fa-save"></i> Változások mentése</button></div>
                </div>
            </form>
        </div>

        <? if($this->user[data][user_group] != \PortalManager\Users::USERGROUP_USER): ?>
        <div class="divider"></div>
        <h4>Céges adatok</h4>
        <?=$this->msg['ceg']?>
        <div class="form-rows">
            <form action="#ceg" method="post">
                <div class="row">
                    <div class="col-md-3 form-text-md"><strong>Cég neve:</strong></div>
                    <div class="col-md-9"><input name="company_name" type="text" class="form-control" value="<?=$this->user[data][company_name]?>" /></div>
                </div>
                <div class="row">
                    <div class="col-md-3 form-text-md"><strong>Cég címe:</strong></div>
                    <div class="col-md-9"><input name="company_address" type="text" class="form-control" value="<?=$this->user[data][company_address]?>" /></div>
                </div>
                <div class="row">
                    <div class="col-md-3 form-text-md"><strong>Cég telephely:</strong></div>
                    <div class="col-md-9"><input name="company_hq" type="text" class="form-control" value="<?=$this->user[data][company_hq]?>" /></div>
                </div>
                <div class="row">
                    <div class="col-md-3 form-text-md"><strong>Cég adószám:</strong></div>
                    <div class="col-md-9"><input name="company_adoszam" type="text" class="form-control" value="<?=$this->user[data][company_adoszam]?>" /></div>
                </div>
                <div class="row">
                    <div class="col-md-3 form-text-md"><strong>Cég bankszámlaszáma:</strong></div>
                    <div class="col-md-9"><input name="company_bankszamlaszam" type="text" class="form-control" value="<?=$this->user[data][company_bankszamlaszam]?>" /></div>
                </div>
                <div class="row">
                    <div class="col-md-12 right"><button name="saveCompany" class="btn btn-sec btn-sm"><i class="fa fa-save"></i> Változások mentése</button></div>
                </div>
            </form>
        </div>
        <? endif; ?>

        <div class="divider"></div>
        <? if( isset( $_GET['missed_details']) && in_array( 'szallitasi', $missed_details) ): ?>
            <?=Helper::makeAlertMsg('pWarning', '<BR><strong>HIÁNYZÓ ADAT:</strong><BR>Kérjük, hogy pótolja a hiányzó SZÁLLÍTÁSI adatait.' );?>
        <? endif; ?>
        <h4>Szállítási adatok</h4>
        <?=$this->msg['szallitasi']?>
        <div class="form-rows">
            <form action="#szallitasi" method="post">
            <? foreach($szallnev as $dk => $dv):
              if( $dk == 'phone_korzet' ) continue;
              $val = ($this->user[szallitasi_adat]) ? $this->user[szallitasi_adat][$dk] : '';
            ?>
            <div class="row">
                <div class="col-md-3 form-text-md"><strong><?=$szallnev[$dk]?></strong><?=(in_array($dk, $req_items))?' *':''?></div>
                <div class="col-md-9 <?=($dk=='city')?'hint-holder-col':''?>">
                <?php if ($dk == 'state'): ?>
                <?php elseif( $dk == 'irsz'): ?>
                  <input autocomplete="off" type="text" ng-keyup="findCityByIrsz($event, 'szall_city')" id="szall_irsz" name="irsz" class="form-control" value="<?=$val?>"/>
                <?php elseif( $dk == 'city'): ?>
                  <input autocomplete="off" readonly="readonly" type="text" id="szall_city" name="city" class="form-control" value="<?=$val?>"/><div class="hint-holder" ng-show="findedCity['szall_city'] && findedCity['szall_city'].length != 0" id="szall_city">
                    <div class="hint-list">
                      <div class="cityhint" ng-click="fillCityHint('szall_city', city)" ng-repeat="city in findedCity['szall_city']">{{city.varos}} <span ng-show="city.megye" class="megye">({{city.megye}} megye)</span></div>
                    </div>
                  </div>
                <?php elseif( $dk == 'kozterulet_jelleg'): ?>
                <select name="kozterulet_jelleg" class="form-control" id="szall_kozterulet_jelleg">
                    <option value="" selected="selected">-- válasszon --</option>
                    <? foreach( $this->kozterulet_jellege as $kj ): ?>
                    <option value="<?=$kj?>" <?=($val !='' && $val == $kj) ? 'selected="selected"' : ''?>><?=$kj?></option>
                    <? endforeach; ?>
                </select>
                <?php else: ?>
                    <?php if ($dk == 'phone'): ?>
                    <div style="margin: 0 -15px;">
                      <div class="row">
                        <div class="col-md-3">
                          <input name="<?=$dk?>_korzet" type="text" placeholder="körzetszám" class="form-control" id="szall_<?=$dk?>_korzet" value="<?=$this->user[szallitasi_adat]['phone_korzet']?>" />
                        </div>
                        <div class="col-md-9">
                          <input name="<?=$dk?>" type="text" class="form-control" id="szall_<?=$dk?>" value="<?=$val?>" />
                        </div>
                      </div>
                    </div>
                    <?php else: ?>
                      <input name="<?=$dk?>" type="text" class="form-control" id="szall_<?=$dk?>" value="<?=$val?>" />
                    <?php endif; ?>
                <?php endif; ?>
                </div>
            </div>
            <? endforeach; ?>
            <div class="row">
                <div class="col-md-12 right"><button name="saveSzallitasi" class="btn btn-sec btn-sm"><i class="fa fa-save"></i> Változások mentése</button></div>
            </div>
            </form>
        </div>

        <div class="divider"></div>

        <? if( isset( $_GET['missed_details']) && in_array( 'szamlazasi', $missed_details) ): ?>
            <?=Helper::makeAlertMsg('pWarning', '<BR><strong>HIÁNYZÓ ADAT:</strong><BR>Kérjük, hogy pótolja a hiányzó SZÁMLÁZÁSI adatait.' );?>
        <? endif; ?>
        <h4>Számlázási adatok</h4>
        <?=$this->msg['szamlazasi']?>
        <div class="form-rows">
            <form action="#szamlazasi" method="post">
            <? foreach($szmnev  as $dk => $dv):
              if($dk == 'phone') continue;
              if( $dk == 'phone_korzet' ) continue;
              $val = ($this->user[szamlazasi_adat]) ? $this->user[szamlazasi_adat][$dk] : '';
            ?>

            <div class="row">
                <div class="col-md-3 form-text-md"><strong><?=$szmnev[$dk]?></strong><?=(in_array($dk, $req_items))?' *':''?></div>
                <div class="col-md-9 <?=($dk=='city')?'hint-holder-col':''?>">
                <?php if ($dk == 'state'): ?>
                <?php elseif( $dk == 'irsz'): ?>
                  <input autocomplete="off" type="text" ng-keyup="findCityByIrsz($event, 'szam_city')" id="szam_irsz" name="irsz" class="form-control" value="<?=$val?>"/>
                <?php elseif( $dk == 'city'): ?>
                  <input autocomplete="off" readonly="readonly" type="text" id="szam_city" name="city" class="form-control" value="<?=$val?>"/><div class="hint-holder" ng-show="findedCity['szam_city'] && findedCity['szam_city'].length != 0" id="szam_city">
                    <div class="hint-list">
                      <div class="cityhint" ng-click="fillCityHint('szam_city', city)" ng-repeat="city in findedCity['szam_city']">{{city.varos}} <span ng-show="city.megye" class="megye">({{city.megye}} megye)</span></div>
                    </div>
                  </div>
                <?php elseif( $dk == 'kozterulet_jelleg'): ?>
                <select name="kozterulet_jelleg" class="form-control" id="szam_kozterulet_jelleg">
                    <option value="" selected="selected">-- válasszon --</option>
                    <? foreach( $this->kozterulet_jellege as $kj ): ?>
                    <option value="<?=$kj?>" <?=($val !='' && $val == $kj) ? 'selected="selected"' : ''?>><?=$kj?></option>
                    <? endforeach; ?>
                </select>
                <?php else: ?>
                    <input name="<?=$dk?>" type="text" class="form-control" id="szam_<?=$dk?>" value="<?=$val?>" />
                <?php endif; ?>
                </div>
            </div>
            <? endforeach; ?>
            <div class="row">
                <div class="col-md-12 right"><button name="saveSzamlazasi" class="btn btn-sec btn-sm"><i class="fa fa-save"></i> Változások mentése</button></div>
            </div>
            </form>
        </div>
    </div>
  </div>
</div>
