<br>
<script type="text/javascript">
	$(function(){
		$('#newparam').click(function(){
			ujParameter();
		});
	})

	function ujParameter(){
		$('.params .row:last-child').after('<br><div class="row">'+
		'<div class="col-md-2">'+
		'<input type="hidden" name="paramId[]" value="0" />'+
	    '<input type="text" name="paramNev[]" class="form-control">'+
	    '</div>'+
	    '<div class="col-md-1">'+
	    '<input type="text" name="paramMe[]" class="form-control">'+
	    '</div>'+
		'<div class="col-md-2" align="center">'+
	    	'<em>új</em>'+
	    '</div>'+
		'</div>');
	}
</script>
<div class="row" style="font-size:0.8em;">
	<div class="col-md-2">
    	<strong>Paraméter</strong>
    </div>
    <div class="col-md-1">
    	<strong>Mértékegység</strong>
    </div>
	<div class="col-md-2">
    	<strong>Prioritás</strong>
    </div>
    <? if(false): ?>
    <div class="col-md-2" align="center">
    	<strong>Csúszka használat (csak számoknál)</strong>
    </div>
		<? endif; ?>
    <div class="col-md-2">
    	<strong>Konfigurációs ulcs</strong>
    </div>
</div>
<br>
<div class="params">
	<? foreach($this->parameterek as $d): ?>
    <div class="row">
		<div class="col-md-2">
        	<input type="hidden" name="paramId[]" value="<?=$d[ID]?>" />
	    	<input type="text" name="paramNev[]" value="<?=$d[parameter]?>" class="form-control">
	    </div>
	    <div class="col-md-1">
	    	<input type="text" name="paramMe[]" value="<?=$d[mertekegyseg]?>"  class="form-control">
	    </div>
		<div class="col-md-2">
	    	<input type="number" name="paramPriority[<?=$d[ID]?>]" value="<?=$d[priority]?>" class="form-control">
	    </div>
	    <? if(false): ?>
        <div class="col-md-2">
	    	<input type="checkbox" name="paramRange[<?=$d[ID]?>]" value="1" <?=($d[is_range] == '1')?'checked':''?> class="form-control">
	    </div>
	    <? endif; ?>
        <div class="col-md-2">
	    	<input type="checkbox" name="paramKulcs[<?=$d[ID]?>]" value="1" <?=($d[kulcs] == '1')?'checked':''?> class="form-control">
	    </div>
	</div>
    <br>
    <? endforeach; ?>
	<div class="row">
		<div class="col-md-2">
        <input type="hidden" name="paramId[]" value="0" />
	    	<input type="text" name="paramNev[]" class="form-control">
	    </div>
	    <div class="col-md-1">
	    	<input type="text" name="paramMe[]" class="form-control">
	    </div>
        <div class="col-md-2" align="center">
	    	<em>új</em>
	    </div>
	</div>
</div>
<br>
<div style="float:right;">
	<button class="btn btn-success btn-3x" name="addParameter">Paraméterek mentése <i class="fa fa-check-square"></i></button>
</div>
<div style="float:left;">
	<button type="button" id="newparam"><i class="fa fa-plus-circle"></i> új paraméter</button>
</div>
<div class="clr"></div>
