<script type="text/javascript">
	$(function(){
		$('#registerBtn').registerUser();
		
		$('#copySzamToSzall').click(function(){
			$('#register input[name^=szam_]').each(function(){
				var e = $(this).attr('name');
				console.log($('#register input[name=szall_'+e.replace('szam_','')+']'));
				$('#register input[name=szall_'+e.replace('szam_','')+']').val($(this).val());
			});	
		});
	})
</script>
<div class="recall ajaxBox">
	<h3><i class="fa fa-plus-square"></i> Regisztráció</h3>
    <form action="" method="post" id="register" onsubmit="$('#registerBtn').click() return false;">
    <div class="cont" style="padding:0;">
    	<div class="head">
        	Alapadatok
        </div>
        	<div style="padding:15px;">
                <div class="row np">
                    <div class="col-md-6" style="padding:0 5px;"><label for="nev">Teljes név:</label><input required="required" type="text" id="nev" name="nev" class="form-control"/></div>
                    <div class="col-md-6" style="padding:0 5px;"><label for="email">E-mail cím:</label><input required="required" type="text" id="email" name="email" class="form-control" excCode="1002" /></div>
                </div>
                <div class="divider"></div>
                <div class="row np">
                    <div class="col-md-6" style="padding:0 5px;"><label for="pw1">Jelszó</label><input required="required" type="password" id="pw1" name="pw1" class="form-control"/></div>
                    <div class="col-md-6" style="padding:0 5px;"><label for="pw2">Jelszó újra</label><input required type="password" id="pw2" name="pw2" class="form-control"/></div>
                </div>
            </div>
        <div class="head">
        	Vásárláshoz szükséges adatok
        </div>
        <div style="padding:15px;">
        	<div class="row np">
            	<div class="col-md-6" style="padding:0 5px;">
                	<h4>Számlázási adatok</h4>
                	<div class="row np">
                    	<div class="col-md-4 form-text"><strong>Név</strong></div>
                        <div class="col-md-8"><input required="required" type="text" id="szam_nev" name="szam_nev" class="form-control"/></div>
                    </div>	
                    <div class="divider-sm"></div>
                    <div class="row np">
                    	<div class="col-md-4 form-text"><strong>Utca, házszám</strong></div>
                        <div class="col-md-8"><input required="required" type="text" id="szam_uhsz" name="szam_uhsz" class="form-control"/></div>
                    </div>
                    <div class="divider-sm"></div>
                    <div class="row np">
                    	<div class="col-md-4 form-text"><strong>Város</strong></div>
                        <div class="col-md-8"><input required="required" type="text" id="szam_city" name="szam_city" class="form-control"/></div>
                    </div>
                    <div class="divider-sm"></div>
                    <div class="row np">
                    	<div class="col-md-4 form-text"><strong>Irányítószám</strong></div>
                        <div class="col-md-8"><input required="required" type="text" id="szam_irsz" name="szam_irsz" class="form-control"/></div>
                    </div>
                    <div class="divider-sm"></div>
                    <div class="row np">
                    	<div class="col-md-12 form-text"><a href="javascript:void(0);" id="copySzamToSzall">adatok beillesztése szállítási adatokhoz</a></div>
                        
                    </div>
                    
                </div>
                <div class="col-md-6" style="padding:0 5px;">
               		<h4>Szállítási adatok</h4>
                    <div class="row np">
                    	<div class="col-md-4 form-text"><strong>Név</strong></div>
                        <div class="col-md-8"><input required="required" type="text" id="szall_nev" name="szall_nev" class="form-control"/></div>
                    </div>	
                    <div class="divider-sm"></div>
                    <div class="row np">
                    	<div class="col-md-4 form-text"><strong>Utca, házszám</strong></div>
                        <div class="col-md-8"><input required="required" type="text" id="szall_uhsz" name="szall_uhsz" class="form-control"/></div>
                    </div>
                    <div class="divider-sm"></div>
                    <div class="row np">
                    	<div class="col-md-4 form-text"><strong>Város</strong></div>
                        <div class="col-md-8"><input required="required" type="text" id="szall_city"  name="szall_city" class="form-control"/></div>
                    </div>
                    <div class="divider-sm"></div>
                    <div class="row np">
                    	<div class="col-md-4 form-text"><strong>Irányítószám</strong></div>
                        <div class="col-md-8"><input required="required" type="text" id="szall_irsz" name="szall_irsz" class="form-control"/></div>
                    </div>
                    <div class="divider-sm"></div>
                    <div class="row np">
                    	<div class="col-md-4 form-text"><strong>Telefonszám</strong></div>
                        <div class="col-md-8"><input required="required" type="text" id="szall_phone" name="szall_phone" class="form-control"/></div>
                    </div>
                </div>
            </div>
            <div class="divider"></div>
            <div class="row np">
               	<div class="col-md-8 form-btn"><input required="required" type="checkbox" id="aszfOk" name="aszfOk" /> <label for="aszfOk">Elolvastam és tudomásul vettem az <a href="/p/aszf" target="_blank">ÁSZF</a>-ben foglaltakat!</label></div>
            	<div class="col-md-4" align="right">
                	<button id="registerBtn" class="btn btn-warning">Regisztráció <i class="fa fa-check"></i></button>
                </div>
            </div>
        </div>
    </div>
	</form>
</div>