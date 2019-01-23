<script type="text/javascript">
	  function showRecaptcha() {
		Recaptcha.create("<?=CAPTCHA_PUBLIC_KEY?>", 'captchadiv', {
			tabindex: 1,
			theme: "red",
			callback: Recaptcha.focus_response_field
		});
	  }
	  $(function(){
		showRecaptcha();
	  })
</script>
<div class="recall">
	<h3><i class="fa fa-phone"></i> Visszahívás kérése</h3>
    <div class="cont">
		<? if($this->t[data]): ?>
     	<h4>Kapcsolódó termék</h4>
    	<div class="termek">
            <div>
				<strong><?=$this->t[data][nev]?></strong> 
           	</div>
        </div>
        <br>
        <div class="divider"></div>
        <br>
		<? endif; ?>
        
        <div class="form">
        	<form action="" method="post">
            <input type="hidden" name="tid" value="<?=$this->t[data][ID]?>" />
        	<h4>Adatok megadása</h4>
                <div class="row">
                    <div class="col-md-6">
                    	<div class="input-group">
                        	<span class="input-group-addon"><i class="fa fa-font"></i></span>
                        	<input type="text" name="nev" class="form-control" placeholder="Az Ön neve..." value="<?=($this->user)?$this->user[data][nev]:''?>">
                        </div>
                    </div>
                    <div class="col-md-6">
                    	<div class="input-group">
                        	<span class="input-group-addon"><i class="fa fa-phone"></i></span>
                        	<input type="text" name="phone" class="form-control" placeholder="Telefonszáma..." value="<?=($this->user)?$this->user[data][szallitas_phone]:''?>">
                        </div>
                    </div>
                </div>
                
            <h4>Mikor hívhatjuk vissza?</h4>
            	<div class="row">
                	<div class="col-md-12">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-clock-o"></i></span>
                            <select name="idoszak" id="" class="form-control">
                            	<option value="egesz_nap">9-17 óráig</option>
                                <option value="delelott">9-12 óráig</option>
                                <option value="delutan">12-17 óráig</option>
                            </select>
                        </div>
                    </div>
                </div>
            <h4>Megjegyzés</h4>
                <div class="row">
                    <div class="col-md-12">
                        <textarea name="comment" class="form-control"></textarea>
                    </div>
                </div>
                <div class="row">
					<div class="col-md-8">
						<div id="captchadiv"></div>
					</div>
                	<div class="col-md-4" align="right">
                    	<br>
                    	<button class="btn btn-pr" name="requestRecall">Elküld <i class="fa fa-arrow-circle-right"></i></button>
                 	</div>
                </div>
            </form>
        </div>
    </div>
</div>