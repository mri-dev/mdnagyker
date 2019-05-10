<div style="float:right;">
	<a href="/kategoriak/" class="btn btn-default"><i class="fa fa-arrow-left"></i> vissza</a>
</div>
<h1>Termék kategória paraméterek</h1>
<div class="clr"></div>
<script type="text/javascript">
	$(function(){
		$('#paramlist').bind('change',function(){
			loadTermkatParameters($(this).val());
		});
	})

	function loadTermkatParameters(katid){
		$.post('/ajax/get',{
			type : 'loadTermkatParameters',
			katid : katid
		},function(d){
			$('#paramedit').show(0);
			$('#paramcontent').html(d);
		},"html");
	}
</script>
<? if($this->gets[2] == 'del'): ?>
<form action="" method="post">
<input type="hidden" name="delParamId" value="<?=$this->gets[3]?>" />
<div class="row">
	<div class="col-md-12">
    	<div class="panel panel-danger">
        	<div class="panel-heading">
            <h2><i class="fa fa-times"></i> Paraméter törlése</h2>
            </div>
        	<div class="panel-body">
            	<div style="float:right;">
                	<a href="/kategoriak/parameterek/" class="btn btn-danger"><i class="fa fa-times"></i> NEM</a>
                    <button class="btn btn-success">IGEN <i class="fa fa-check"></i> </button>
                </div>
            	<strong>Biztos, hogy törli a paramétert?</strong>
            </div>
        </div>
    </div>
</div>
</form>
<? endif; ?>
<form action="" method="post">
<div class="row">
	<div class="col-md-12">
    	<div class="con">
        	<h2>Paraméterek szerkesztése</h2>
            <select name="termkatID" id="paramlist" class="form-control">
            	<option value="">-- termék kategória kiválasztása --</option>
                <option value="" disabled></option>
								<? while( $this->categories->walk() ): $item = $this->categories->the_cat(); ?>
								<option value="<?=$item['ID']?>"><?=str_repeat('&mdash;',$item['deep'])?> <?=$item['neve']?></option>
								<? endwhile; ?>
            </select>
        </div>
    </div>
</div>

<div class="row" id="paramedit" style="display:none;">
	<div class="col-md-12">
    	<div class="con">
            <div id="paramcontent">

            </div>
        </div>
    </div>
</div>
<div class="row">
	<div class="col-md-12">
		<div class="con">
        <h2>Aktuális paraméterek</h2>
	    <? foreach($this->listOfTermekKatParam as $kat => $param): ?>
			<div class="katparam head"><?=Product::clear($kat)?></div>
		    <?
			Helper::arry($param,'parameter','ASC');
			foreach($param as $p): ?>
		    <div class="katparam param">
            	<div style="float:right;"><a title="törlés" href="/kategoriak/parameterek/del/<?=$p[ID]?>"><i class="fa fa-times"></i></a></div>
		    	<em title="Termék listázásban, paraméter megjelenés prioritás">(<?=$p[priority]?>)</em> &nbsp
				<strong><?=$p[parameter]?> </strong>
				<? if(!is_null($p[mertekegyseg]) && $p[mertekegyseg] != ''): ?>(<?=$p[mertekegyseg]?>) <? endif; ?>
                &nbsp;&nbsp;&nbsp;
				<? if($p[is_range] == 1): ?>&nbsp;<i class="fa fa-arrows-h" title="Csúszkás használat" style="color:black;"></i> <? endif; ?>
                <? if($p[kulcs] == '1'): ?>&nbsp;<i title="Konfigurációs kulcs: termékenként meghatározható milyen egyedi értékek alapján válogathat a kosárba tétel esetén a felhasználó / vásárló." class="fa fa-key" style="color:#ed3e2a; font-size:16px;"></i><? endif; ?>
                <div class="clr"></div>
		   	</div>
		    <? endforeach; ?>
		<? endforeach; ?>
	    </div>
    </div>
</div>

</form>
