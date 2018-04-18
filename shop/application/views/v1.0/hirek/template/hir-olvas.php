<div class="news-content">
	<div class="head">
		<h1><?=$cim?></h1>
		<div class="subline">
			<div class="backurl">
				<a href="/hirek"><i class="fa fa-long-arrow-left" aria-hidden="true"></i> vissza a hírekhez</a>
			</div>
			<div class="share">
				<div class="fb-like" data-href="<?=DOMAIN?>hirek/<?=$eleres?>" data-layout="button_count" data-action="like" data-show-faces="false" data-share="true"></div>
			</div>
			<div class="date"><i class="fa fa-clock-o"></i> <?=substr(\PortalManager\Formater::dateFormat($letrehozva, $date_format),0,-6)?></div>
			<div class="nav">
				<ul class="cat-nav">
					<li><a href="/"><i class="fa fa-home"></i></a></li>
					<li><a href="/hirek">Hírek</a></li>
				</ul>
			</div>
		</div>
	</div>
	<div class="content">
		<?=\PortalManager\News::textRewrites($szoveg)?>
	</div>
</div>
