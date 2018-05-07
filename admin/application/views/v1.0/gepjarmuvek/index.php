<h1>Gépjárművek</h1>
<? if($this->err): ?>
	<?=$this->bmsg?>
<? endif; ?>
<div class="row">
  <div class="col-md-4">
		<? if( $this->vehicle_d ): ?>
		<div class="con con-del">
			<h2>Gépjármű elem törlése</h2>
			Biztos benne, hogy törli a(z) <strong><u><?=$this->vehicle_d->getName()?></u></strong> elnevezésű gépjárműt? A művelet nem visszavonható!
			<div class="row np">
				<div class="col-md-12 right">
					<form action="" method="post">
						<a href="/gepjarmuvek/" class="btn btn-danger"><i class="fa fa-times"></i> Mégse</a>
						<button name="delVehicle" value="1" class="btn btn-success">Igen, véglegesen törlöm <i class="fa fa-check"></i></button>
					</form>
				</div>
			</div>
		</div>
		<? else: ?>
		<div class="con <?=($this->vehicle ? 'con-edit':'')?>">
			<h2><?=($this->vehicle ? 'Gépjármű szerkesztése':'Új gépjármű létrehozás')?></h2>
			<div>
				<form action="" method="post">
					<div class="row np">
						<div class="col-md-12" style="padding-right:8px;">
							<label for="name">Elnevezés*</label>
							<input type="text" id="name" name="name" value="<?= ( $this->err ? $_POST['name'] : ($this->vehicle ? $this->vehicle->getName():'') ) ?>" class="form-control">
						</div>
					</div>
					<br>
					<div class="row np">
						<div class="col-md-12">
							<label for="img">Logó</label>
							<div class="input-group">
                  <input type="text" id="img" class="form-control" name="logo" value="<?= ( $this->err ? $_POST['image'] : ($this->vehicle ? $this->vehicle->getImage():'') ) ?>">
                  <span class="input-group-addon">
                      <a title="Kép kiválasztása galériából" href="/src/js/tinymce/plugins/filemanager/dialog.php?type=1&amp;lang=hu_HU&amp;field_id=img" data-fancybox-type="iframe" class="iframe-btn"><i class="fa fa-th"></i></a>
                  </span>
              </div>
						</div>
					</div>
					<br>
					<div class="row np">
						<div class="col-md-12">
							<label for="parent_id">Szülő elem</label>
							<select name="parent_id" id="parent_id" class="form-control">
								<option value="" selected="selected">&mdash; ne legyen &mdash;</option>
								<option value="" disabled="disabled"></option>
								<?
									while( $this->vehicles->walk() ):
									$item = $this->vehicles->the_item();
								?>
								<option value="<?=$item['ID']?>_<?=$item['deep']?>" <?=($this->err && $_POST['parent_id'] == $item['ID'].'_'.$item['deep'] ? 'selected="selected"' : ($this->vehicle && $this->vehicle->getParentKey() == $item['ID'].'_'.$item['deep'] ? 'selected="selected"' : '' ))?>><? for($s=$item['deep']; $s>0; $s--){echo '&mdash;';}?><?=$item['title']?></option>
								<? endwhile; ?>
							</select>
						</div>
					</div>
					<br>
					<div class="row np">
						<div class="col-md-12 right">
							<? if($this->vehicle): ?>
							<a href="/gepjarmuvek/" class="btn btn-danger"><i class="fa fa-times"></i> mégse</a>
							<? endif; ?>
							<button name="<?=($this->vehicle ? 'saveVehicle':'addVehicle')?>" value="1" class="btn btn-<?=($this->vehicle ? 'success':'primary')?>"><?=($this->vehicle ? 'Változások mentése <i class="fa fa-save">':'Hozzáadás <i class="fa fa-plus">')?></i></button>
						</div>
					</div>
				</form>
			</div>
		</div>
		<? endif; ?>
  </div>
  <div class="col-md-8">
		<div class="con">
			<h2>Lista</h2>
			<div class="row np row-head">
				<div class="col-md-11"><em>Típusjelzés / elnevezés</em></div>
				<div class="col-md-1"></div>
			</div>
			<div class="categories vehicle-list">
				<?
					while( $this->vehicles->walk() ):
					$item = $this->vehicles->the_item();
				?>
				<div class="row np deep<?=$item['deep']?> <?=($this->vehicle && $this->vehicle->getId() == $item['ID'] ? 'on-edit' : ( $this->vehicle_d && $this->vehicle_d->getId() == $item['ID'] ? 'on-del':'') )?>">
					<div class="col-md-11">
						<?php if ($item['logo'] != ''): ?>
						<div class="logo">
							<img src="<?=$item['logo']?>" alt="">
						</div>	
						<?php endif; ?>
						<a href="/gepjarmuvek/szerkeszt/<?=$item['ID']?>" title="Szerkesztés"><strong><?=$item['title']?></strong></a>
						 <? if( $item['oldal_hashkeys'] ): ?> | <span style="color: black;">Csatolt oldalak: <?=count(explode(",",$item[oldal_hashkeys]))?> db</span><? endif; ?>
						<div><? if($item['hashkey']): ?> <span class="hashkey">#<?=$item['hashkey']?></span> <? endif; ?></div>
					</div>
          <div class="col-md-1 actions" align="right">
          	<a href="/gepjarmuvek/torles/<?=$item['ID']?>" title="Törlés"><i class="fa fa-times"></i></a>
          </div>
				</div>
				<? endwhile; ?>
			</div>
		</div>
  </div>
</div>
