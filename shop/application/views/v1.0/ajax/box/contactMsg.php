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
	<h3><i class="fa fa-envelope-o"></i> Kapcsolat üzenet küldése</h3>
    <div class="cont">
		<form action="" method="post">
			<div class="box sendMsg">
				<div class="p10">
					<div><input type="text" class="form-control" name="name" value="<?=($this->err)?$_POST[name]:''?>" placeholder="Az Ön neve" /></div>
					<br />
					<div><input type="email" class="form-control" name="email" value="<?=($this->err)?$_POST[email]:''?>" placeholder="E-mail cím" /></div>
					<br />
					<div><input type="text" class="form-control" name="phone" value="<?=($this->err)?$_POST[phone]:''?>" placeholder="Telefonszám" /></div>
					<br />
					<div><input type="text" class="form-control" name="subject" value="<?=($this->err)?$_POST[subject]:''?>" placeholder="Üzenet témája" /></div>
					<br />
					<textarea name="msgText" id="" class="form-control" style="min-height:150px;" placeholder="Üzenetének tartalma..."><?=($this->err)?$_POST[msgText]:''?></textarea>
					<br />
					<div id="captchadiv"></div>
					<br />
					<button class="btn btn-pr" name="sendKapcsolat">Üzenet küldése</button>
				</div>
			</div>
		</form>
    </div>
</div>