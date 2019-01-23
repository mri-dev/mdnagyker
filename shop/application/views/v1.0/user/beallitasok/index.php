<?
    $szallnev = array(
        'nev' => 'Név',
        'phone' => 'Telefon',
        'uhsz' => 'Utca, házszám',
        'irsz' => 'Irányítószám',
        'city' => 'Város',
        'state' => 'Megye'
    );
    $szmnev = $szallnev;
    $szmnev['adoszam'] = 'Adószám';

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
                <div class="row np">
                    <div class="col-md-3"><strong>E-mail cím:</strong></div>
                    <div class="col-md-9"><?=$this->user[email]?></div>
                </div>
                <div class="row np">
                    <div class="col-md-3 form-text-md"><strong>Név</strong></div>
                    <div class="col-md-5"><input name="nev" type="text" class="form-control" value="<?=$this->user[data][nev]?>" /></div>
                </div>
                <div class="row np">
                    <div class="col-md-3"><strong>Utoljára belépve</strong></div>
                    <div class="col-md-5"><?=$this->user[data][utoljara_belepett]?> (<?=Helper::distanceDate($this->user[data][utoljara_belepett])?>)</div>
                </div>
                <div class="row np">
                    <div class="col-md-3"><strong>Regisztráció</strong></div>
                    <div class="col-md-5"><?=$this->user[data][regisztralt]?> (<?=Helper::distanceDate($this->user[data][regisztralt])?>)</div>
                </div>
                <? if( false ): ?>
                <div class="row np">
                    <div class="col-md-12">
                        KEDVEZMÉNYEK
                    </div>
                </div>
                <? foreach( $this->user['kedvezmenyek'] as $kedv ): ?>
                <div class="row np">
                    <div class="col-md-3"><strong><?=$kedv['nev']?></strong></div>
                    <div class="col-md-5"><a href="<?=$kedv['link']?>" title="részletek"><?=$kedv['kedvezmeny']?>%</a> <? if($kedv['nev'] === 'Arena Water Card' && $kedv['kedvezmeny'] === 0): ?> <a href="javascript:void(0);" onclick="$('#add-watercard').slideToggle(400);" class="add-water-card">kártya regisztrálása</a> <? endif; ?> </div>
                </div>
                <? endforeach; ?>
                <? endif; ?>
                <div class="row np">
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
                <div class="row np">
                    <div class="col-md-3 form-text-md"><strong>Cég neve:</strong></div>
                    <div class="col-md-9"><input name="company_name" type="text" class="form-control" value="<?=$this->user[data][company_name]?>" /></div>
                </div>
                <div class="row np">
                    <div class="col-md-3 form-text-md"><strong>Cég címe:</strong></div>
                    <div class="col-md-9"><input name="company_address" type="text" class="form-control" value="<?=$this->user[data][company_address]?>" /></div>
                </div>
                <div class="row np">
                    <div class="col-md-3 form-text-md"><strong>Cég telephely:</strong></div>
                    <div class="col-md-9"><input name="company_hq" type="text" class="form-control" value="<?=$this->user[data][company_hq]?>" /></div>
                </div>
                <div class="row np">
                    <div class="col-md-3 form-text-md"><strong>Cég adószám:</strong></div>
                    <div class="col-md-9"><input name="company_adoszam" type="text" class="form-control" value="<?=$this->user[data][company_adoszam]?>" /></div>
                </div>
                <div class="row np">
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
                $val = ($this->user[szallitasi_adat]) ? $this->user[szallitasi_adat][$dk] : '';
            ?>
            <div class="row np">
                <div class="col-md-3 form-text-md"><strong><?=$szallnev[$dk]?></strong></div>
                <div class="col-md-9">
                    <? if($dk != 'state'): ?>
                    <input name="<?=$dk?>" type="text" class="form-control" value="<?=$val?>" />
                    <? else: ?>
                    <select name="<?=$dk?>" class="form-control" id="szall_state">
                        <? foreach( $this->states as $s ): ?>
                            <option value="<?=$s?>" <?=($val == $s) ? 'selected="selected"' : ''?>><?=$s?></option>
                        <? endforeach; ?>
                    </select>
                    <? endif; ?>
                </div>
            </div>
            <? endforeach; ?>
            <div class="row np">
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
            <? foreach($szmnev  as $dk => $dv):  if($dk == 'phone') continue;
             $val = ($this->user[szamlazasi_adat]) ? $this->user[szamlazasi_adat][$dk] : '';
            ?>
            <div class="row np">
                <div class="col-md-3 form-text-md"><strong><?=$szmnev[$dk]?></strong></div>
                <div class="col-md-9">
                    <? if($dk != 'state'): ?>
                    <input name="<?=$dk?>" type="text" class="form-control" value="<?=$val?>" />
                    <? else: ?>
                    <select name="<?=$dk?>" class="form-control" id="szall_state">
                        <? foreach( $this->states as $s ): ?>
                            <option value="<?=$s?>" <?=($val == $s) ? 'selected="selected"' : ''?>><?=$s?></option>
                        <? endforeach; ?>
                    </select>
                    <? endif; ?>
                </div>
            </div>
            <? endforeach; ?>
            <div class="row np">
                <div class="col-md-12 right"><button name="saveSzamlazasi" class="btn btn-sec btn-sm"><i class="fa fa-save"></i> Változások mentése</button></div>
            </div>
            </form>
        </div>
    </div>
  </div>
</div>
