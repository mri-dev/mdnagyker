<div class="news_list">
	<? 
		while ( $view->last_news->walk() ):  
		$news = $view->last_news->the_news();
	?>
		<div><a title="<?=$news['cim'].' @ '.$news['letrehozva']?>" href="/hirek/<?=$news['eleres']?>"><i class="fa fa-angle-right"></i> <?=(strlen($news['cim']) > 45) ? substr($news['cim'],0,45) . '...' : $news['cim']?></a></div>
	<? endwhile; ?>
</div>