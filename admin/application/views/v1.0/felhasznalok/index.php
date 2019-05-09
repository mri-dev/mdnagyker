<div style="float:right;">
    <a href="/account/?t=create&ret=/felhasznalok" class="btn btn-primary"><i class="fa fa-plus"></i> új felhasználó</a>
</div>
<h1>Felhasználók <span><strong><?=Helper::cashFormat($this->users[info][total_num])?> db</strong> felhasználó <? if($_COOKIE[filtered] == '1'): ?><span class="filtered">Szűrt listázás <a href="/<?=$this->gets[0]?>/clearfilters/" class="btn btn-danger">eltávolítás</a></span><? endif; ?></span></h1>
<?
	$str = array(
		'nev' => 'Név',
		'uhsz' => 'Utca, házszám',
		'irsz' => 'Irányítószám',
		'city' => 'Város',
		'phone' => 'Telefonszám'
	);
?>
<form action="" method="post">
<table class="table termeklista table-bordered">
	<thead>
    	<tr>
			<th title="Felhasználó ID" width="40">#</th>
	        <th>Név</th>
            <th width="200">E-mail</th>
            <th width="250">Számlázási adat</th>
            <th width="250">Szállítási adat</th>
            <th width="100" title="Megrendeléseinek összesített értéke">Fizetett össz.</th>
            <th width="50">Kedvezménye</th>
            <th width="100">Engedélyezve</th>
            <th width="100">Aktiválva</th>
            <th width="120">Utoljára belépett</th>
            <th width="120">Regisztrált</th>
            <th width="20"></th>
        </tr>
	</thead>
    <tbody>
    	<tr class="search <? if($_COOKIE[filtered] == '1'): ?>filtered<? endif;?>">
    		<td><input type="text" name="ID" class="form-control" value="<?=$_COOKIE[filter_ID]?>" /></td>
    		<td><input type="text" name="nev" class="form-control" placeholder="felhasználó neve..." value="<?=$_COOKIE[filter_nev]?>" /></td>
            <td><input type="text" name="email" class="form-control" placeholder="e-mail cím..." value="<?=$_COOKIE[filter_email]?>" /></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td><select class="form-control"  name="engedelyezve" style="max-width:100px;">
            	<option value="" <?=(!$_COOKIE[filter_engedelyezve])?'selected':''?>># Mind</option>
                	<option value="0" <?=($_COOKIE[filter_engedelyezve] == '0')?'selected':''?>>Nem</option>
                    <option value="1" <?=($_COOKIE[filter_engedelyezve] == '1')?'selected':''?>>Igen</option>
                </select></td>
    		<td><select class="form-control"  name="aktivalva" style="max-width:100px;">
            	<option value="" selected># Mind</option>
                	<option value="0" <?=($_COOKIE[filter_aktivalva] == '0')?'selected':''?>>Nem</option>
                    <option value="1" <?=($_COOKIE[filter_aktivalva] == '1')?'selected':''?>>Igen</option>
                </select></td>
            <td></td>
            <td></td>
    		<td align="center">
            	<button name="filterList" class="btn btn-default"><i class="fa fa-search"></i></button>
            </td>
    	</tr>
    	<? if(count($this->users[data]) > 0): foreach($this->users[data] as $d):  ?>
    	<tr>
	    	<td align="center"><?=$d[ID]?></td>
	          <td>
          		<strong><?=$d[nev]?></strong> <?php if ( $d['user_group'] == 'company' ): ?>
                <span class="company">(<?php echo $d['total_data']['data']['company_name']; ?>)</span>
              <?php endif; ?>
              <div>
                <span class="usergroup" title="Felhasználói csoport"><?=$d['user_group_name']?></span>
                <span class="pricegroup" title="Ár csoport"><?=$d['price_group']['title']?></span>
              </div>
            </td>
            <td align="center"><?=$d[email]?></td>
            <td>
                <? if( $d['total_data']['szamlazasi_adat'] ): ?>
                  <strong><?=$d['total_data']['szamlazasi_adat']['nev']?></strong><br>
                  <?=$d['total_data']['szamlazasi_adat']['irsz']?> <?=$d['total_data']['szamlazasi_adat']['city']?>, <?=$d['total_data']['szamlazasi_adat']['kozterulet_nev']?> <?=$d['total_data']['szamlazasi_adat']['kozterulet_jelleg']?> <?=$d['total_data']['szamlazasi_adat']['hazszam']?>.
                  <?php if ( $d['total_data']['szamlazasi_adat']['adoszam'] != '' ): ?>
                    <div>Adószám: <strong><?php echo $d['total_data']['szamlazasi_adat']['adoszam']; ?></strong></div>
                  <?php endif; ?>
                <? else: ?>
                    &mdash; hiányzó adat &mdash;
                <? endif; ?>
            </td>
            <td>
                <? if( $d['total_data']['szallitasi_adat'] ): ?>
                    <strong><?=$d['total_data']['szallitasi_adat']['nev']?></strong><br>
                    <?=$d['total_data']['szallitasi_adat']['irsz']?> <?=$d['total_data']['szallitasi_adat']['city']?>, <?=$d['total_data']['szallitasi_adat']['kozterulet_nev']?> <?=$d['total_data']['szallitasi_adat']['kozterulet_jelleg']?> <?=$d['total_data']['szallitasi_adat']['hazszam']?>. <br>
                    Telefon: <?=$d['total_data']['szallitasi_adat']['phone']?>
                <? else: ?>
                    &mdash; hiányzó adat &mdash;
                <? endif; ?>
            </td>
            <td align="center">
            	<?=Helper::cashFormat($d[totalOrderPrices])?> Ft
            </td>
            <td align="center">
            	<?=$d[total_data][kedvezmeny]?>%
            </td>
            <td align="center"><?=($d[engedelyezve] == 1)?'<i title="Engedélyezve" mode="engedelyezve" class="fa fa-check vtgl" fid="'.$d[ID].'"></i>':'<i mode="engedelyezve" class="fa fa-times vtgl" fid="'.$d[ID].'" title="Tiltva"></i>'?></td>
            <td align="center"><?=(!is_null($d[aktivalva]))?'<i title="Aktiválva" class="fa fa-check"></i>':'<i class="fa fa-times" title="Nincs aktiválva"></i>'?></td>
            <td align="center"><?=Helper::softDate($d[utoljara_belepett])?>	<br><em>(<?=Helper::distanceDate($d[utoljara_belepett])?>)</em></td>
            <td align="center"><?=Helper::softDate($d[regisztralt])?> <br><em>(<?=Helper::distanceDate($d[regisztralt])?>)</em></td>
            <td class="center">
                <div class="dropdown">
                    <i class="fa fa-gear dropdown-toggle" title="Beállítások" id="dm<?=$d['ID']?>" data-toggle="dropdown"></i>
                      <ul class="dropdown-menu" role="menu" aria-labelledby="dm<?=$d['ID']?>">
                        <li role="presentation"><a role="menuitem" tabindex="-1" href="/account/?t=edit&ID=<?=$d['ID']?>&ret=<?=$_SERVER[REQUEST_URI]?>">Szerkesztés <i class="fa fa-pencil"></i></a></li>
                      </ul>
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
</form>
<ul class="pagination">
  <li><a href="/<?=$this->gets[0]?>/<?=($this->gets[1] != '')?$this->gets[1].'/':'-/'?>1">&laquo;</a></li>
  <? for($p = 1; $p <= $this->users[info][pages][max]; $p++): ?>
  <li class="<?=(Helper::currentPageNum() == $p)?'active':''?>"><a href="/<?=$this->gets[0]?>/<?=($this->gets[1] != '')?$this->gets[1].'/':'-/'?><?=$p?>"><?=$p?></a></li>
  <? endfor; ?>
  <li><a href="/<?=$this->gets[0]?>/<?=($this->gets[1] != '')?$this->gets[1].'/':'-/'?><?=$this->users[info][pages][max]?>">&raquo;</a></li>
</ul>
<script type="text/javascript">
    $(function(){
        $('.termeklista i.vtgl').click(function(){
            visibleToggler($(this));
        });
    })
    function visibleToggler(e){
        var id      = e.attr('fid');
        var src     = e.attr('class').indexOf('check');
        var mode    = e.attr('mode');

        if(src >= 0){
            e.removeClass('fa-check').addClass('fa-spinner fa-spin');
            doChange(e, mode, id, false);
        }else{
            e.removeClass('fa-times').addClass('fa-spinner fa-spin');
            doChange(e, mode, id, true);
        }
    }
    function doChange(e, mode, id, show){
        var v = (show) ? '1' : '0';
        $.post("<?=AJAX_POST?>",{
            type : 'userChangeActions',
            mode : mode,
            id  : id,
            val : v
        },function(d){
            if(!show){
                e.removeClass('fa-spinner fa-spin').addClass('fa-times');
            }else{
                e.removeClass('fa-spinner fa-spin').addClass('fa-check');
            }
        },"html");
    }
</script>
