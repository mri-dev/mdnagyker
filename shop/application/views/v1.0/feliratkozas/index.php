<div class="subscribe page-width">
	<div class="responsive-view full-width">
		<form action="" method="post">
		<? if( $_GET['leave'] ): ?>
		<h1>Leiratkozás hírlevélről</h1>
		<br>
		<?=$this->msg?>
		<br>
		<div class="subs-form">
			<div class="row np">
				<div class="col-md-12">
					<label for="subs_name">Az Ön e-mail címe:*</label>
					<input type="text" name="email" id="subs_name" value="<?=($_GET['email'] ? $_GET['email'] : '')?>" class="form-control">
				</div>
			</div>
			<br>
			<div class="row np">
				<div class="col-md-12">
					<?=\Applications\Captcha::show()?>
				</div>
			</div>
			<br>
			<div class="row np">
				<div class="col-md-12">
					<button class="btn btn-danger" name="unsubscribe" value="1">Leiratkozás</button>
				</div>
			</div>
		</div>
		<? else: ?>
		<h1>Feliratkozás</h1>
		<p class="subtitle">Iratkozzon fel a(z) <?=$this->settings['page_title']?> hírleveleire, hogy mindig értesüljön az újdonságokról és naprakész legyen!</p>
		<br>
		<?=$this->msg?>
		<br>
		<div class="subs-form">
			<div class="row np">
				<div class="col-md-12">
					<label for="subs_name">Az Ön neve:*</label>
					<input type="text" name="name" value="<?=$_GET['name']?>" id="subs_name" class="form-control">
				</div>
			</div>
			<br>
			<div class="row np">
				<div class="col-md-12">
					<label for="subs_email">Az Ön e-mail címe:*</label>
					<input type="text" name="email" value="<?=$_GET['email']?>" id="subs_email" class="form-control">
				</div>
			</div>
			<br>
			<div class="row np">
				<div class="col-md-12">
					<label for="subs_phone">Az Ön telefonszáma:*</label>
					<input type="text" name="phone" value="<?=$_GET['phone']?>" id="subs_phone" class="form-control">
				</div>
			</div>
			<br><br>
			<div class="row np">
				<div class="col-md-12">
					<input type="checkbox" name="aszf" id="aszf">
					<label for="aszf">* Elfogadom az <a href="/p/aszf" target="_blank">ÁSZF</a>-et és az <a href="/p/adatvedelmi-tajekoztato" target="_blank">Adatvédelmi tájékoztató</a>ban leírtakat.</label>
				</div>
			</div>
			<br>
			<div class="row np">
				<div class="col-md-12">
					<?=\Applications\Captcha::show()?>
				</div>
			</div>
			<br>
			<div class="row np">
				<div class="col-md-12">
					<button class="btn btn-info" name="subscribe" value="1">Feliratkozás</button>
				</div>
			</div>
		</div>
		<br>
		<div class="divider"></div>
		<div class="unsubscribe">Ha Ön feliratkozott hírlevelünkre, de leszeretne íratkozni, <a href="/feliratkozas?leave=1">kattintson ide</a>!</div>
		<? endif; ?>
		</form>
	</div>
</div>
