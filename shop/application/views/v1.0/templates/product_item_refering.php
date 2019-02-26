<div class="item">
	<? 
		$ar = $brutto_ar;
		if( $akcios == '1' ): 
			$ar = $akcios_fogy_ar; 
		endif; 

		// Cashoff ár
		$dar = $ar-$user['data']['cash']; 
		if( $dar < 0) $dar = 0;
	?>
	<div class="image">
		<? if($dar == 0): ?>
			<div class="free-item">INGYEN ELVIHETI!</div>
		<? endif; ?>
		<a href="<?=$link?>"><img class="aw" title="<?=$product_nev?>" src="<?=$profil_kep?>" alt="<?=$product_nev?>"></a>
	</div>
	<div class="title"><?=$product_nev?></div>
	<div class="subtitle"><?=__($csoport_kategoria)?></div>
	<div class="prices">
		<div class="default">
			<div><strong>Teljes ár:</strong></div>
			
			<div class="current price-text"><?=Helper::cashFormat($ar)?> <?=$valuta?></div>
		</div>
		<div class="dist_price">
			<div class="text">Jelenlegi virtuális ponttal:</div>
			<div class="price-text"><?=Helper::cashFormat($dar)?> <?=$valuta?></div>		
		</div>
		<div class="clr"></div>
	</div>
	<div class="buttons">
		<div class="holder">
			<div class="watch autoheight"><a href="<?=$link?>"><?=__('megnézem')?></a></div>
			<div class="order autoheight"><a href="<?=$link?>?buy=now"><?=__('megrendelem')?></a></div>
		</div>					
	</div>	
</div>