<form action="" method="post">
<div class="account page-width">	
	<div class="grid-layout">
		<div class="grid-row grid-row-20">
			<? $this->render('user/inc/account-offline', true); ?>
		</div>
		<div class="grid-row grid-row-80">
			<h1>Új jelszó generálása</h1>
			<?=$this->msg?>

			<div class="row np">		
				<div class="col-md-12">
					<label for="login_email">E-mail cím</label>
					<input type="text" required="required" name="email" value="<?=($this->err) ? $_POST['email'] : ''?>" excCode="1001" id="email" class="form-control <?=($this->err == 1001) ? 'input-text-error' : ''?>" />
				</div>
				<div class="col-md-12 right">
					<button name="resetPassword" class="btn btn-pr btn-md">Új jelszó generálása <i class="fa fa-arrow-circle-right"></i></button>
				</div>
			</div>			
		</div>
	</div>
</div>
</form>