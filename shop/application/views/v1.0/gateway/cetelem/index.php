<div class="cetelem-transaction-view">
	<div class="head">
		Cetelem Áruhitel
		<div class="subline">Megrendelés azonosító: <strong><?=$this->order['azonosito']?></strong> </div>
	</div>
	<div class="con">
		<div class="whitebox">
			<? echo $this->render('gateway/cetelem/'.$this->gets[2].'/index'); ?>
		</div>
	</div>	
</div>