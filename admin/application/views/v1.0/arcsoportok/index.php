<h1>Ár csoportok</h1>
<? if($this->err): ?>
	<?=$this->bmsg?>
<? endif; ?>
<div class="row">
  <div class="col-md-4">
		<? if( $this->PriceGroup_d ): ?>
		<div class="con con-del">
			<h2>Ár csoport elem törlése</h2>
			Biztos benne, hogy törli a(z) <strong><u><?=$this->PriceGroup_d->getTitle()?></u></strong> elnevezésű ár csoportot? A művelet nem visszavonható!
			<div class="row np">
				<div class="col-md-12 right">
					<form action="" method="post">
						<a href="/arcsoportok/" class="btn btn-danger"><i class="fa fa-times"></i> Mégse</a>
						<button name="delPriceGroup" value="1" class="btn btn-success">Igen, véglegesen törlöm <i class="fa fa-check"></i></button>
					</form>
				</div>
			</div>
		</div>
		<? else: ?>
		<div class="con <?=($this->PriceGroup ? 'con-edit':'')?>">
			<h2><?=($this->PriceGroup ? 'Ár csoport szerkesztése':'Új ár csoport létrehozás')?></h2>
			<div>
				<form action="" method="post">
					<div class="row np">
						<div class="col-md-12" style="padding-right:8px;">
							<label for="title">Csoport elnevezése*</label>
							<input type="text" id="title" name="title" value="<?= ( $this->err ? $_POST['title'] : ($this->PriceGroup ? $this->PriceGroup->getTitle():'') ) ?>" class="form-control">
						</div>
					</div>
          <br>
          <div class="row np">
						<div class="col-md-12" style="padding-right:8px;">
							<label for="title">Ár illesztési kulcs*</label>
              <select class="form-control" name="groupkey">
                <option value="">-- válasszon --</option>
                <?php for( $i = 1; $i <= 10; $i++ ){ ?>
                  <option value="ar<?=$i?>" <?=($this->PriceGroup  && $this->PriceGroup->getKey() == 'ar'.$i) ? 'selected="selected"':''?>>ar<?=$i?></option>
                <? } ?>
              </select>
						</div>
					</div>
					<br>
					<div class="row np">
						<div class="col-md-12 right">
							<? if($this->PriceGroup): ?>
							<a href="/arcsoportok/" class="btn btn-danger"><i class="fa fa-times"></i> mégse</a>
							<? endif; ?>
							<button name="<?=($this->PriceGroup ? 'savePriceGroup':'addPriceGroup')?>" value="1" class="btn btn-<?=($this->PriceGroup ? 'success':'primary')?>"><?=($this->PriceGroup ? 'Változások mentése <i class="fa fa-save">':'Hozzáadás <i class="fa fa-plus">')?></i></button>
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
				<div class="col-md-9"><em>Csoport elnevezés</em></div>
  			<div class="col-md-2 center"><em>Illesztési kulcs</em></div>
				<div class="col-md-1"></div>
			</div>
			<div class="categories PriceGroup-list">
				<?
					while( $this->PriceGroups->walk() ):
					$item = $this->PriceGroups->the_item();
				?>
				<div class="row np deep<?=$item['deep']?> <?=($this->PriceGroup && $this->PriceGroup->getId() == $item['ID'] ? 'on-edit' : ( $this->PriceGroup_d && $this->PriceGroup_d->getId() == $item['ID'] ? 'on-del':'') )?>">
					<div class="col-md-9">
						<a href="/arcsoportok/szerkeszt/<?=$item['ID']?>" title="Szerkesztés"><strong><?=$item['title']?></strong></a>
						 <? if( $item['oldal_hashkeys'] ): ?> | <span style="color: black;">Csatolt oldalak: <?=count(explode(",",$item[oldal_hashkeys]))?> db</span><? endif; ?>
						<div><? if($item['hashkey']): ?> <span class="hashkey">#<?=$item['hashkey']?></span> <? endif; ?></div>
					</div>
          <div class="col-md-2 center">
          	<?=$item['groupkey']?>
          </div>
          <div class="col-md-1 actions" align="right">
          	<a href="/arcsoportok/torles/<?=$item['ID']?>" title="Törlés"><i class="fa fa-times"></i></a>
          </div>
				</div>
				<? endwhile; ?>
			</div>
		</div>
  </div>
</div>
