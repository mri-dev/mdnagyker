<div><? if( !$list ): ?>nincs találat<? endif; ?></div>
<ul class="product-li-items mode-<?=$mode?> tree">
	<? foreach( $list as $d ): ?>
	<li class="item item_<?=$id?>_<?=$d['product_id']?>">
		<div class="v">
			<div class="btn">
				<? if( $howdo == 'static'): ?>
					<input type="checkbox" name="productConnects[]" value="<?=$d['product_id']?>">
				<? else: ?>
					<? if($mode == 'add'):?>
					<button onclick="connectProductRelatives(this, <?=$id?>, <?=$d['product_id']?>);" type="button" title="Összekapcsolás a termékkel" class="btn btn-sm btn-default"><i class="fa fa-plus-circle"></i></button>
					<? elseif( $mode == 'remove' ): ?>
					<button onclick="removeProductRelatives(this, <?=$id?>, <?=$d['product_id']?>);" type="button" title="Lekapcsolás a termékről" class="btn btn-sm btn-danger"><i class="fa fa-minus-circle"></i></button>
					<? endif;?>
				<? endif;?>
			</div>
			<div class="img"><img src="<?=$d['profil_kep']?>" alt=""></div>
			<div>
				<div class="nev">
					<a href="/termekek/t/edit/<?=$d['product_id']?>"><?=$d['product_nev']?></a>
					<span class="item-no" title="Cikkszám">(#<?=$d['cikkszam']?>)</span>  
					<? if($d['fotermek']): ?>
					<span class="mainpro">főtermék</span>
					<? endif; ?>
				</div>				
			</div>
			<div class="pa"><?=$d['meret']?> &nbsp;&nbsp; <?=$d['szin']?></div>
			<div class="clr"></div>
		</div>
	</li>
	<? endforeach; ?>	
</ul>