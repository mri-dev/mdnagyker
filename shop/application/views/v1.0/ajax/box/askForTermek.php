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
	<h3><i class="fa fa-question-circle"></i> Kérdés egy termékről</h3>
    <div class="cont">
     	<h4>Kapcsolódó termék</h4>
    	<div class="termek">
        	<div class="i"><img src="<?=$this->t[data][profil_kep]?>" alt=""></div>	
            <div>
				<?=$this->t[data][markaNev]?> <?=$this->t[data][nev]?>
           	</div>
        </div>
        <div class="form">
        	<form action="" method="post">
            <input type="hidden" name="tid" value="<?=$this->t[data][ID]?>" />
            <h4>Adatok megadása</h4>
                <div class="row">
                    <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-font"></i></span>
                            <input type="text" name="nev" class="form-control" placeholder="Az Ön neve...">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-globe"></i></span>
                            <input type="text" name="email" class="form-control" placeholder="E-mail címe...">
                        </div>
                    </div>
                </div>
                <br />
                <div class="row">
                    <div class="col-md-12">
                        <textarea name="ask" class="form-control" placeholder="Kérjük, hogy ide fogalmazza meg kérdését..."></textarea>
                    </div>
                </div>
                <div class="row">
					<div class="col-md-8">
						<div id="captchadiv"></div>
					</div>
                	<div class="col-md-4" align="right">
                    	<br>
                    	<button class="btn btn-success" name="requestAsk">Elküld <i class="fa fa-arrow-circle-right"></i></button>
                 	</div>
                </div>
            </form>
        </div>
    </div>
</div>