<div style="float: right;">
	<a href="/kuponok" class="btn btn-default"><i class="fa fa-arrow-left"></i> vissza</a>
</div>
<h1>Új kupon <small>Kuponok</small></h1>
<?=$this->msg?>
<div class="coupon-view">
	<form method="post" action="">
	<div class="row">
		<div class="col-sm-6">
			<div class="con">
				<h3>Kupon alapadatok</h3>
				<div class="divider"></div>
				<div style="margin-top:10px;">
					<div class="row">
						<div>
							<div class="col-sm-6">
								<label for="coupon_code">Kupon kódja</label>
								<input type="text" id="coupon_code" name="coupon_code" value="<?=(isset($_POST[coupon_code])) ? $_POST[coupon_code] : $this->settings['orderkey_prefix'].'-'.date('Y').'-'.strtoupper(strrev(uniqid()))?>" class="form-control">
								<small>Ha nem generált kóddal szeretne kupont létrehozni, akkor írja be a kívánt kuponkódot.</small>
							</div>
							<div class="col-sm-6">
								<div class="code-status" ng-show="coupon_status">
									<div class="code"><strong>"{{coupon_code}}"</strong> kód állapota:</div>
									<div class="status">{{coupon_status}}</div>
								</div>
							</div>
						</div>
					</div>
					<br>
					<div class="row">
						<div class="col-sm-12">
							<label for="name">Kupon elnevezése</label>
							<input type="text" id="name" name="name" value="<?=$_POST[name]?>" class="form-control">
						</div>
					</div>
					<br>
					<div class="row">
						<div class="col-sm-6">
							<label for="active_from">Aktív &mdash; időponttól</label>
							<input type="date" id="active_from" name="active_from" value="<?=$_POST[active_from]?>" class="form-control">
						</div>
						<div class="col-sm-6">
							<label for="active_to">Aktív &mdash; időpontig</label>
							<input type="date" id="active_to" name="active_to" value="<?=$_POST[active_to]?>" class="form-control">
						</div>
					</div>
					<br>
					<div class="row">
						<div class="col-sm-6">
							<label for="usage_left">Felhasználási limit (darab)</label>
							<input type="number" id="usage_left" name="usage_left" value="<?=$_POST[usage_left]?>" class="form-control">
						</div>
						<div class="col-sm-6">
							<label for="min_order_value">Megrendelés összeg limit (Ft)</label>
							<input type="number" id="min_order_value" name="min_order_value" value="<?=$_POST[min_order_value]?>" class="form-control">
						</div>
					</div>
					<br>
					<div class="row">
						<div class="col-sm-6">
							<label for="discount_type">Kedvezmény típusa</label>
							<select name="discount_type" class="form-control">
								<option selected="selected" value="">-- válasszon --</option>
								<option disabled="disabled" value=""></option>
								<option value="percentage">Százalék (%)</option>
								<option value="cash">Pénzösszeg (Ft)</option>
							</select>
						</div>
						<div class="col-sm-6">
							<label for="discount_value">Kedvezmény érték</label>
							<input type="number" id="discount_value" name="discount_value" value="0" class="form-control">
							<small>Százalék típus esetén százalék értéket írjon be, pl.: 5 (%).<br> Pénzösszeg típus esetén pedig konkrét összeget, pl.: 5000 (Ft)</small>
						</div>
					</div>
					<br>
				</div>
			</div>

			<div class="con">
				<div class="row np">
					<div class="col-sm-12 right">
						<button class="btn btn-success" name="createCoupon">Létrehozás <i class="fa fa-plus-circle"></i></button>
					</div>
				</div>
			</div>
		</div>
		<?php if (false): ?>
		<div class="col-sm-6">
			<div class="con">
				<h3>Termék szükítés</h3>
				<small>Válassza ki azokat a termékeket, amelyikre legyen érvényes a kupon. <strong>Ne válasszon ki terméket, ha minden termékre legyen érvényes a kupon!</strong></small>
				<div class="divider"></div>
				<div style="margin-top:10px;">
					<label style="color: black; font-size: 12px;">
					<input type="checkbox" name="exlude_for_product" onclick="if($(this).is(':checked')){$('#prod-list').slideDown(200);}else{$('#prod-list').slideUp(200);}">
					Igen, kiválasztom, hogy melyik termékekre legyen élrvényes a kupon.</label>
					</label>
					<br>
					<div class="prod-list" id="prod-list" style="display: none;">
					<div class="divider"></div>
					<? foreach($this->footer_products as $t): ?>
					<div>
						<input type="checkbox" id="fp_<?=$t['product_id']?>" name="for_products[<?=$t['raktar_articleid']?>]"> <label for="fp_<?=$t['product_id']?>"><?=$t['product_nev']?></label>
					</div>
					<? endforeach; ?>
					</div>
				</div>
			</div>
			<div class="con">
				<h3>Kupon tulajdonos</h3>
				<small>Tulajdonos kiválasztása esetén az ügyfélkapuban a tulajdonos látja a kuponnal várásolt megrendelések alapadatait: vásárlás ideje, megrendelés állapota, fizetett összeg.</strong></small>
				<div class="divider"></div>
				<div style="margin-top:10px;">
					<label style="color: black; font-size: 12px;">
					<input type="checkbox" name="has_author" onclick="if($(this).is(':checked')){$('#auth-list').slideDown(200);}else{$('#auth-list').slideUp(200);}">
					Igen, kiválasztom a kupon tulajdonosát.</label>
					</label>
					<br>
					<div class="prod-list" id="auth-list" style="display: none;">
					<div class="divider"></div>
					<br>
						<div class="row np">
	        				<div class="col-sm-10">
	        					<input type="text" class="form-control userReceiver" cid="1" placeholder="Felhasználó keresése név vagy email szerint...">
	        					<div class="userReceiver-list" id="userReceiver_list1"></div>
	        				</div>
	        				<div class="col-sm-2">
	        					<input type="text" name="author_id" id="adder_1_uid" class="form-control" placeholder="ID">
	        				</div>
		    			</div>
					</div>
				</div>
			</div>
		</div>
		<?php endif; ?>
	</div>
	</form>
</div>

<script type="text/javascript">
    $(function(){

        $('.userReceiver').bind('keyup', function(){
        	var src = $(this).val();
        	var cid = $(this).attr('cid');
        	var ct  = $('#userReceiver_list'+cid);

        	$.post('<?=AJAX_POST?>', {
        		type : 'searchUsers',
        		search: src
        	}, function(d){
        		if( d.num < 25 ) {
        			var ins = '';
	        		$.each( d.data, function(i, e) {
	        			ins += '<div onclick="saveReceiver('+cid+','+e.ID+')" class="each">';
	        			ins += '<span class="name">'+e.nev+'</span>';
	        			ins += '<span class="email"> ('+e.email+') </span>';
	        			ins += '</div>';
	        		});

	        		ct.html(ins);
        		} else {
        			ct.html("");
        		}
        	}, "json");

        });


    })

	function saveReceiver( cid, id ){
		$('#adder_'+cid+'_uid').val(id);
    }
</script>
