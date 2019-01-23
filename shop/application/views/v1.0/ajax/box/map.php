<?
    $gps = $this->shop->getGPS();
?>
<div class="recall">
	<h3><i class="fa fa-globe"></i> <?=$this->shop->getName()?> <br> <small><?=$this->shop->getAddress()?></small> </h3>
    <div class="cont" style="padding: 0;">		
        <iframe style="width: 100%; height: 500px; border:0;" allowfullscreen src="https://www.google.com/maps/embed/v1/place?q=<?=$this->shop->getAddress()?>&zoom=15language=hu&key=<?=APIKEY_GOOGLE_MAP_EMBEDKEY?>" frameborder="0"></iframe>
    </div>
</div>