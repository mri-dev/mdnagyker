<div class="home">
	<div class="pw">
		<div class="grid-layout">
			<div class="grid-row filter-sidebar">
				<? $this->render('templates/sidebar'); ?>
			</div>
			<div class="grid-row inside-content">
				<? $this->render('templates/slideshow'); ?>

				<div class="title-header">
					<div class="">
						<h2>Újdonságok</h2>
					</div>
				</div>
				<div class="webshop-product-top <?=(count($this->ujdonsag_products_list) > 3)?'slide-style':''?>">
					<?php if (true): ?>
						<div class="items trackwidth">
							<? foreach ( $this->ujdonsag_products_list as $p ) {
									$p['itemhash'] = hash( 'crc32', microtime() );
									$p['sizefilter'] = ( count($this->ujdonsag_products->getSelectedSizes()) > 0 ) ? true : false;
									$p['show_variation'] = ($this->myfavorite) ? true : false;
									$p = array_merge( $p, (array)$this );
									echo $this->ptemplate->get( 'product_item', $p );
							} ?>
						</div>
					<?php endif; ?>
				</div>

				<div class="title-header">
					<div class="">
						<h2>Kiemelt ajánlataink</h2>
					</div>
				</div>
				<div class="webshop-product-top <?=(count($this->kiemelt_products_list) > 3)?'slide-style':''?>">
					<?php if (true): ?>
						<div class="items trackwidth">
							<? foreach ( $this->kiemelt_products_list as $p ) {
									$p['itemhash'] = hash( 'crc32', microtime() );
									$p['sizefilter'] = ( count($this->kiemelt_products->getSelectedSizes()) > 0 ) ? true : false;
									$p['show_variation'] = ($this->myfavorite) ? true : false;
									$p = array_merge( $p, (array)$this );
									echo $this->ptemplate->get( 'product_item', $p );
							} ?>
						</div>
					<?php endif; ?>
				</div>

			</div>
		</div>
	</div>
	<?php if (count($this->factorylist) > 0): ?>
	<div class="factory-preview">
		<div class="pw">
			<div class="factories">
				<?php foreach ( $this->factorylist as $f ): ?>
				<div class="fact">
					<img src="<?=IMGDOMAIN.$f['image']?>" alt="<?=$f['neve']?>">
				</div>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
	<script type="text/javascript">
		$(function(){
			$('.factories').slick({
				infinite: true,
				slidesToShow: 6,
				slidesToScroll: 1,
				dots: false,
				autoplay: true,
				speed: 1500
			});
		});
	</script>
	<?php endif; ?>
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

			$('.webshop-product-top.slide-style .items').slick({
				slidesToShow: 3,
				slidesToScroll: 3,
				dots: true
			});

			trackwidth();

			$(window).resize(function(){
				trackwidth();
			});
		})

		function trackwidth(){
			var w = $('.home > .pw').width() - $('.filter-sidebar').width();
			$('.trackwidth').css({
				width: w
			});
		}
	</script>
</div>
