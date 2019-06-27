<? $data = $this->data; ?>
<h1><?=$data[nev]?> <small><?=$data[email]?></small></h1>
<?=$this->msg?>
<form action="" method="post">
	<div style="margin: 0 -10px;">
		<div class="row">
			<div class="col-sm-12">
				<div class="con con-del">
					<h3 style="margin: 0 0 5px 0;">FIÓK VÉGLEGES TÖRLÉSE</h3>
					<div class="divider" style="margin-bottom: 10px;"></div>
          Biztos, hogy véglegesen törölni szeretné a felhasználói fiókot? Minden vonatkozó adat el fog veszni. A művelet nem visszavonható!
          <br><br>
          <button type="submit" class="btn btn-danger" name="delAccount" value="1">Fiók végleges törlése</button>
				</div>
			</div>
		</div>
	</div>
</form>
