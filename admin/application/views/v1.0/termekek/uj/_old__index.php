<div style="float:right;">
	<a href="/termekek/" class="btn btn-default"><i class="fa fa-arrow-left"></i> vissza</a>
</div>
<h1>Új termék</h1>
<div class="clr"></div>
<?=$this->bmsg?>
<div class="create-product">
	<form action="" method="post" role="form" enctype="multipart/form-data">
		<div class="row">
			<div class="col-md-8" style="padding-left:0;">

				<div class="con">
					<h3>Termék törzsadatok</h3>
					<div class="row">
						<div class="col-md-6">
							<label for="raktar_articleid">Típus azonosító törzskód</label>
							<input type="text" name="raktar_articleid" id="raktar_articleid" value="<?=$this->termek[raktar_articleid]?>" class="form-control">
						</div>
						<div class="col-md-6">
							<label for="raktar_variantid">Variáció azonosító</label>
							<input type="text" name="raktar_variantid" id="raktar_variantid" value="<?=$this->termek[raktar_variantid]?>" class="form-control">
						</div>
					</div>
				</div>

				<div class="con">
					<div class="checkins">
						<div class="">
							<input type="checkbox" id="lathato" name="lathato" <?=($this->termek['lathato'] == 1)?'checked':''?>/> <label for="lathato">Aktív / Látható</label>
						</div>
						<div class="">
							<input type="checkbox" name="akcios" id="akciosTgl" onclick="javascript:if($(this).is(':checked')){$('#vakcios').show(0);}else{$('#vakcios').hide(0);}" <?=($_COOKIE[cr_akcios] == 'on' && false)?'checked':''?>/> <label for="akciosTgl">Akciós</label>
						</div>
						<div class="">
							 <input type="checkbox" name="ujdonsag" id="ujdonsag" <?=($_COOKIE[cr_ujdonsag] == 'on')?'checked':''?> /> <label for="ujdonsag">Újdonság</label>
						</div>
						<div class="">
							<input type="checkbox" name="argep" id="argep" <?=($_COOKIE[cr_argep] == 'on')?'checked':'checked'?>/> <label for="argep">ÁRGÉP listába</label>
						</div>
						<div class="">
							<input type="checkbox" name="arukereso" id="arukereso" <?=($_COOKIE[cr_arukereso] == 'on')?'checked':'checked'?> /> <label for="arukereso">ÁRUKERESŐ listába</label>
						</div>
						<div class="">
							<input type="checkbox" <?=($_COOKIE[cr_pickpackszallitas] == 'on')?'checked':'checked'?> name="pickpackszallitas" id="pickpackszallitas" /> <label for="pickpackszallitas">Pick Pack Pont-ra szállítható</label>
						</div>
						<div class="" style="display: none;">
							<input type="checkbox" <?=($_COOKIE[cr_no_cetelem] == 'on')?'checked':''?> name="no_cetelem" id="no_cetelem" /> <label for="no_cetelem">Cetelem hitel alól KIZÁRVA</label>
						</div>
						<div class="">
							<input type="checkbox" <?=($_COOKIE[cr_kiemelt] == 'on')?'checked':'checked'?> name="kiemelt" id="kiemelt" /> <label for="kiemelt">Kiemelt termék</label>
						</div>
						<div class="">
							<input type="checkbox" <?=($_COOKIE[cr_show_stock] == 'on')?'checked':'checked'?> name="show_stock" id="show_stock" /> <label for="show_stock">Készletmegjelenítés</label>
						</div>
					</div>
				</div>


				<div class="con">
					<h3>Termék adatok</h3>

					<div class="row">
						<div class="form-group col-md-2">
							<label for="nagyker_kod">Nagyker kód / Cikkszám</label>
							<input type="text" class="form-control" name="nagyker_kod" id="nagyker_kod" value="<?=($this->err)?$_POST[nagyker_kod]:''?>">
						</div>
						<div class="form-group col-md-4 <?=($this->err && $_POST[nev] == '')?'has-error':''?>">
							<label for="nev">Termék neve*</label>
							<input type="text" class="form-control required reqInput" name="nev" id="nev" value="<?=($this->err)?$_POST[nev]:''?>">
						</div>
						<div class="form-group col-md-3">
							<label for="csoport_kategoria">Termék alcíme</label>
							<input type="text" class="form-control" name="csoport_kategoria" id="csoport_kategoria" value="<?=($this->err)?$_POST[csoport_kategoria]:''?>">
						</div>
						<div class="form-group col-md-3 <?=($this->err && $_POST[marka] == '')?'has-error':''?>">
							<label for="nev">Termék márka*</label>
							<select name="marka" id="marka" class="form-control required reqInput">
								<option value="">-- termék márka kiválasztása --</option>
								<option value="" disabled></option>
								<? foreach($this->markak as $d): ?>
								<option value="<?=$d[ID]?>" <?=($this->err && $_POST[marka] == $d[ID])?'selected':''?> nb="<?=$d[brutto]?>"><?=$d[neve]?> (<?=($d[brutto] == '1')?'Bruttó':'Nettó'?>)</option>
								<? endforeach; ?>
							</select>
						</div>
					</div>

					<div class="row">
						<div class="form-group col-md-3">
							<label for="szin">Típus variácó <?=\PortalManager\Formater::tooltip('Több variáció esetén variációk hozhatóak létre, melyet ennél az értéknél lehet megadni. Azonos típus azonosító törzskód és eltérő variácó meghatározásánál a rendszer automatikusan összekapcsolja a termékeket és átjárást biztosít a termék adatlapokon.<br>Pl.: zöld, piros, 16mm, 10 fm, 2x4x10mm, stb...')?></label>
							<input type="text" class="form-control" name="szin" id="szin" value="<?=($this->err)?$_POST[szin]:''?>">
						</div>
						<? if(true): ?>
						<div class="form-group col-md-3">
							<label for="meret">Kiszerelés <?=\PortalManager\Formater::tooltip('Termékenkét termékkapcsolat hozható létre, amennyiben több fajta kiszerelés van egy-egy azonos termék esetében. Adjuk meg a kiszerelést és ez alapján a vásárló válogathat.<br>Pl.: 1 liter, 1 vödör, 1 zsák, 25 kg, 100 db / csomag, stb...')?></label>
							<input type="text" class="form-control" name="meret" id="meret" value="<?=($this->err)?$_POST[meret]:''?>">
						</div>
						<? endif; ?>
						<div class="form-group col-md-2">
							<label for="raktar_keszlet">Raktárkészlet (mennyiség)</label>
							<input type="number" class="form-control" name="raktar_keszlet" value="<?=($this->err) ? $_POST['raktar_keszlet'] : '0'?>" id="raktar_keszlet">
						</div>
						<div class="form-group col-md-2">
							<label for="sorrend">Sorrend</label>
							<input type="number" class="form-control" name="sorrend" id="sorrend" value="<?=($this->err) ? $_POST['sorrend'] : '100'?>">
						</div>
						<div class="form-group col-md-2">
							<label for="fotermek">Főtermék <?=\PortalManager\Formater::tooltip('Több variáció és kiszerelés esetén kijelölhetjük, hogy melyik legyen az alapértelmezett, ami megjelenjen a terméklistázásban. A Főtermék-nek NEM jelölt termékek nem fognak megjelenni a listában, hanem csak mint variáció a kapcsolódó terméklapon!')?></label>
							<input type="checkbox" class="form-control" name="fotermek" value="1" id="fotermek" value="<?=($this->err && $_POST[fotermek] == 'on')?'checked="checked"':''?>">
						</div>
					</div>

					<div class="row">
						<div class="form-group col-md-12">
							<label for="kulcsszavak">Kulcsszavak <?=\PortalManager\Formater::tooltip('A kulcsszavak meghatározása fontos dolog, mivel ezek alapján tud pontosabb keresési találatot kapni a felhasználó. <br> <strong>A kulcsszavakat szóközzel elválasztva adja meg. Pl.: fekete úszó rövidnadrág</strong>')?></label>
							<input type="text" class="form-control" placeholder="" name="kulcsszavak" id="kulcsszavak" value="<?=($this->err) ? $_POST['kulcsszavak'] : ''?>">
						</div>
					</div>

					<div class="row">
						<div class="form-group col-md-12">
							<label for="tudastar_url">Tudástár URL <?=\PortalManager\Formater::tooltip('A termék adatlapon megjelenő tudástár hivatkozásra kattintva erre az URL-re fog érkezni a látogató. Ez lehet egy előzetesen leszűrt (kulcsszó vagy konkrét cikk alapján) tudástár hivatkozás, mely a termékkel kapcsolatos cikk(ek)et listázza ki.')?></label>
							<input type="text" class="form-control" placeholder="" name="tudastar_url" id="tudastar_url" value="<?=($this->err) ? $_POST['tudastar_url'] : ''?>">
						</div>
					</div>

					<div class="row">
						<div class="form-group col-md-12">
							<label for="rovid_leiras">Termék rövid leírása</label>
							<textarea name="rovid_leiras" class="form-control" id="rovid_leiras"><?=($this->err)?$_POST[rovid_leiras]:''?></textarea>
						</div>
					</div>

					<div class="row">
						<div class="form-group col-md-12">
							<label for="leiras">Termék leírása</label>
							<textarea name="leiras" class="form-control" id="leiras"><?=($this->err)?$_POST[leiras]:''?></textarea>
						</div>
					</div>
				</div>
			</div>

			<div class="col-md-4">
				<div class="con">
					<h3>Ár <em class="info">Kérjük, a beszerzési árat adja meg!</em></h3>
					<div class="pdright">
						<div class="input-group">
							<input type="number" name="ar" id="ar" min="0" value="0" class="form-control required reqInput">
							<span class="input-group-addon">Ft beszerzési ár</span>
						</div>
						<div align="right" class="bruttoAr">
							Bruttó beszerzési ár: <strong><span id="bruttoAr">0</span> Ft</strong>
							<input type="hidden" name="netto_ar" id="netto_ar" value="0">
							<input type="hidden" name="brutto_ar" id="brutto_ar" value="0">
						</div>
						<div id="vakcios" style="display:none;">
							<div class="input-group">
								<input type="number" name="akcios_ar" id="akcios_ar" min="0" value="0" class="form-control">
								<span class="input-group-addon">Ft akciós ár</span>
							</div>
							<div align="right" class="bruttoAr">
								Bruttó akciós ár: <strong><span id="bruttoAkciosAr">0</span> Ft</strong>
								<input type="hidden" name="akcios_netto_ar" id="akcios_netto_ar" value="0">
								<input type="hidden" name="akcios_brutto_ar" id="akcios_brutto_ar" value="0">
							</div>
						</div>
						<div class="info">A végfelhasználói ár az aktuálisan kiválasztott <a href="/markak" target="_blank" title="Márka árrések beállítása">márka árrései alapján</a> kerül automatikus kiszámításra!</div>
					</div>
				</div>

				<div class="con">
					<h3>Kategória, amibe megjelenjen <em class="info"><a href="/kategoriak" target="_blank"><i class="fa fa-gear"></i> kategóriák szerkesztése</a></em></h3>
					<div style="padding:0 0 15px 15px;">
						<div class="tree overflowed">
							<? while( $this->categories->walk() ): $item = $this->categories->the_cat(); ?>
							<div class="item deep<?=$item['deep']?>">
								<label><input name="cat[]" value="<?=$item['ID']?>" type="checkbox"><?=$item['neve']?></label>
							</div>
							<? endwhile; ?>
						</div>
					</div>
				</div>

				<? if( false ):  ?>
				<div class="con">
					<h3>Termék kapcsolatok <em class="info">Azonos termékek összekapcsolása, mint termék variáció</em></h3>
					<div class="row">
         		<div class="col-md-12">
         			<label for="productRelativesText">Keresés</label>
         			<input type="text" id="productRelativesText" exc-id="0" value="" class="form-control">
         		</div>
         	</div>
         	<br>
         	<div class="row">
         		<div class="col-md-12">
         			<label for="">Lehetséges termékek (<span id="productRelativesNumber">0</span>)</label>
         			<div class="productRelativesList" id="productRelativesList">írja be előbb a termék nevét</div>
         		</div>
         	</div>
				</div>
				<? endif; ?>

				<div class="con">
					<h3>Tulajdonságok</h3>
					<div class="row">
						<div class="form-group col-md-12">
							<label for="garancia">Garancia (hónap; -1 = élettartam)</label>
							<input class="form-control" type="number" id="garancia" value="<?=($this->err)?$_POST[garancia_honap]:''?>" min="-1" name="garancia_honap">
						</div>
					</div>
					<div class="row np">
						<div class="col-md-12">
							<div class="form-group col-md-6 <?=($this->err && $_POST[szallitasID] == '')?'has-error':''?>">
								<label for="szall">Szállítási idő*</label>
								<select name="szallitasID" id="szall" class="form-control <?=($_COOKIE[cr_szallitasID] == '')?'required':''?> reqInput">
									<option value="">-- válasszon --</option>
									<option value="" disabled="disabled"></option>
									<? foreach($this->szallitas as $sz): ?>
									<option value="<?=$sz[ID]?>" <?=($_COOKIE[cr_szallitasID] == $sz[ID])?'selected':''?>><?=$sz[elnevezes]?></option>
									<? endforeach; ?>
								</select>
							</div>
							<div class="form-group col-md-6 <?=($this->err && $_POST[keszletID] == '')?'has-error':''?>">
								<label for="keszlet">Állapot*</label>
								<select name="keszletID" id="keszlet" class="form-control <?=($_COOKIE[cr_keszletID] == '')?'required':''?> reqInput">
									<option value="">-- válasszon --</option>
									<option value="" disabled="disabled"></option>
									<? foreach($this->keszlet as $k): ?>
									<option value="<?=$k[ID]?>" <?=($_COOKIE[cr_keszletID] == $k[ID])?'selected':''?>><?=$k[elnevezes]?></option>
									<? endforeach; ?>
								</select>
							</div>
							<!--
							<div class="form-group col-md-12">
								<label for="garancia">Garancia (hónap; -1 = élettartam)</label>
								<input class="form-control" id="garancia" type="number" value="" min="-1" max="100" name="garancia">
							</div>
							-->
						</div>
					</div>
				</div>

				<div class="con">
					<h3>Képek feltöltése</h3>
					<div class="row pdright">
						<div class="form-group col-sd-12">
							<input class="form-control" type="file" name="img[]" multiple>
							<br />
							<span class="label label-primary">Max. 5 MB, .jpg képfájlok kiválasztása</span>
						</div>
					</div>
				</div>


			</div>
		</div>
		<div class="row np">
			 <div align="right" class="col-md-12">
				<div class="con">
				 <button name="ujTermek" style="text-transform:uppercase;font-size:16px;">Létrehozás <i class="fa fa-plus-circle"></i></button>
				</div>
			</div>
		</div>
	</form>
</div>

<script type="text/javascript">
	var brutto = 0;

	$(function(){
		$('#marka').bind('change',function(){
			if($(this).val() == '0'){
				$('#ujMarka').show(0).focus();
			}else{
				$('#ujMarka').hide(0).val('');
			}
		});

		$('#nev').bind( 'change', function (){
			$('#productRelativesText').val( $(this).val() );
			loadProductRelatives();
		} );

		if( $('#akciosTgl').is(':checked') ){
			$('#vakcios').show(0);
		}else{
			$('#vakcios').hide(0);
		}

		$('#addMoreGyujtkat').click(function(){
			addNewLister();
		});

		$('#termek_kategoria').bind('change',function(){
			loadParameterek($(this).val());
		});

		$('#marka').bind('change',function(){
			var nb = $('option:selected', this).attr('nb');
			brutto = nb;
		});

		$('#ar').bind('keyup keydown blur',function(){
			var ar = $(this).val();
			showNettoBrutto(ar);
		});
		$('#akcios_ar').bind('keyup keydown blur',function(){
			var ar = $(this).val();
			showNettoBruttoAkcios(ar);
		});

		$('form input[type=file]').change(function(e){
			checkSelectedImg($(this));
		});

		$('.reqInput').bind('change',function(){
			var e = $(this);
			if(e.val() == ''){
				e.addClass('required');
			}else{
				e.removeClass('required');
			}
		});

		loadModszerek();
		$('#addMoreLink').click(function(){
			addNewLinkRow();
		});
	})

	function loadProductRelatives ( callback ) {
		var handler = $('#productRelativesText');
		var excid = handler.attr('exc-id');
		var srctext = handler.val();

		$('#productRelativesList').html( '<i class="fa fa-spinner fa-spin"></i> betöltés...' );

		$.post("<?=AJAX_POST?>",{
			type 	: 'loadProducts',
			by  	: 'nev',
			val 	: srctext,
			template : 'relatives',
			fromid 	: excid,
			mode 	: 'json',
			howdo 	: 'static'
		},function(d){
			var ret = jQuery.parseJSON(d);
			$('#productRelativesList').html( ret.result );
			$('#productRelativesNumber').text( ret.info.results );
			callback();
		},"html");
	}

	function connectProductRelatives( e, foid, tid ) {
		$.post("<?=AJAX_POST?>",{
			type 	: 'addProductConnects',
			idfrom  : foid,
			idto 	: tid
		},function(d){
			loadProductRelatives();
		},"html");
	}

	var loadedGyM = 0;
	function loadModszerek(){
		$.post('/ajax/get/',{
			type : 'loadModszerek',
			i : loadedGyM
		},function(d){
			$('.selModszer.i'+loadedGyM).html(d);
		},"html");
	}
	function addNewLinkRow(){
		var e ='<br />'+
			'<div class="row link">'+
				'<div class="col-md-1"><em>új</em></div>'+
				'<div class="col-md-4"><input type="text" name="linkNev[]" class="form-control" value=""  placeholder="Felirat"/></div>'+
			   ' <div class="col-md-7"><input type="text" name="linkUrl[]" class="form-control" value="" placeholder="URL"/></div>'+
			'</div>';

		$(e).insertAfter('.alink .link:last');
	}
	function checkSelectedImg(i){
		var img 			= i[0].files;
		var wrongSizeNum 	= 0;
		var wrongTypeNum 	= 0;

		$.each(img,function(){
			if(this.type != 'image/jpeg'){
				wrongTypeNum++;
			}
			if(this.size > (5150*1024)){
				wrongSizeNum++;
			}

		});
		$('span.ixs').remove();
		if(wrongSizeNum != 0 || wrongTypeNum != 0){
			$('form input[type=file]').after('<span class="label ixs label-danger"><i class="fa fa-warning"></i> '+wrongSizeNum+' db kép lépi át a méretet, '+wrongTypeNum+' db nem jpeg kép! </span>');
		}else{
			$('form input[type=file]').after('<span class="label ixs label-success">A kép(ek) megfelelőek!</span>');
		}
	}
	function loadParameterek(termkatid){
		$.post('/ajax/get/',{
			type : 'loadCreateTermkatParameters',
			katid : termkatid
		},function(d){
			$('#fillerParams').html(d);
		},"html");
	}

	function addNewLister(){
		newp = loadedGyM + 1;
		$('.katjel.i'+loadedGyM).after('<div class="katjel i'+newp+'"><div class="selModszer i'+newp+'"></div><div class="selGyujto i'+newp+'"></div></div>');
		loadedGyM++;
		loadModszerek();
	}

	function showNettoBrutto(ar){
		var netto = 0;
		if(brutto != 1){
			netto = ar;
			ar = (ar * 1.27).toFixed(0);
		}else{
			netto = (ar/1.27).toFixed(0);
		}
		$('#netto_ar').val(netto);
		$('#brutto_ar').val(ar);
		$('#bruttoAr').text(ar);
	}
	function showNettoBruttoAkcios(ar){
		var netto = 0;
		if(brutto != 1){
			netto = ar;
			ar = (ar * 1.27).toFixed(0);
		}else{
			netto = (ar/1.27).toFixed(0);
		}
		$('#akcios_netto_ar').val(netto);
		$('#akcios_brutto_ar').val(ar);
		$('#bruttoAkciosAr').text(ar);
	}
</script>
