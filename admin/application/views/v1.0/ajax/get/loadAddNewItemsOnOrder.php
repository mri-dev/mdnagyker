<?php
	$hintsession = 'prodhint'.uniqid();
?>
<div class="row" style="padding:4px 0;">
	<div class="col-md-1" style="line-height:35px;">
		Keres:
	</div>
	<div class="col-md-4">
		<div class="input-group">
			<input type="text" value="" id="<?=$hintsession?>_src" class="form-control" placeholder="Név, cikkszám...">
			<div class="input-group-btn">
				<button type="button" class="btn btn-sm btn-default" onclick="autohintProduct($('#<?=$hintsession?>_src'), '<?=$hintsession?>')"><i class="fa fa-search"></i></button>
			</div>
		</div>
	</div>
	<div class="col-md-1">
		<input type="text" name="new_product[]" id="<?=$hintsession?>_value" value="" readonly="readonly" class="form-control" placeholder="#ID">
	</div>
	<div class="col-md-1" style="line-height:35px;">
		Mennyiség:
	</div>
	<div class="col-md-1">
		<input type="number" value="1" min="1" name="new_product_number[]" class="form-control">
	</div>
	<div class="col-md-1" style="line-height:35px;">
		Állapot:
	</div>
	<div class="col-md-3">
		<select class="form-control" name="new_product_allapot[]">
			<? foreach($this->allapotok as $m):  ?>
            <option style="color:<?=$m[szin]?>;" value="<?=$m[ID]?>"><?=$m[nev]?></option>
            <? endforeach; ?>
        </select>
	</div>
	<div class="col-md-11 col-md-offset-1">
		<div class="hint-holder">
			<div class="hint-box" id="<?=$hintsession?>_hints"></div>
		</div>
	</div>
</div>
<div class="divider"></div>
