<div class="home">
	<div class="pw">
		<div class="grid-layout">
			<div class="grid-row filter-sidebar">
				<? $this->render('templates/sidebar'); ?>
			</div>
			<div class="grid-row inside-content">
				<? $this->render('templates/slideshow'); ?>
			</div>
		</div>
	</div>
	<div class="news">
		<div class="pw">
			<div class="articles">
				<?
				$step = 0;
				while ( $this->news->walk() ) {
					$step++;
					$arg = $this->news->the_news();
					$arg['date_format'] = $this->settings['date_format'];
					echo $this->template->get( 'slide', $arg );
				}?>
			</div>
		</div>
	</div>
	<script type="text/javascript">
		$(function(){
			$('.news .articles').slick({
				infinite: true,
			  slidesToShow: 3,
			  slidesToScroll: 1,
				dots: true
			});
		})
	</script>
</div>
