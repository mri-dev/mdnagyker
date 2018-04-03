
	<?php if ( !$this->homepage ): ?>
		</div> <!-- .inside-content -->
		<div class="clr"></div>
		</div><!-- #main -->
		<div class="clr"></div>
	</div><!-- website -->
	<?php endif; ?>

	<footer>
		<div class="main">
			<div class="pw">
				<div class="wrapper">
					<div class="flex">
						<div class="links">
							<div class="wrapper">
								<div class="flex">
									<div class="segitseg">
										<h3>Tájékoztatók</h3>
										<ul>
											<? foreach ( $this->menu_footer->tree as $menu ): ?>
												<li>
													<? if($menu['link']): ?><a href="<?=($menu['link']?:'')?>"><? endif; ?>
														<span class="item <?=$menu['css_class']?>" style="<?=$menu['css_styles']?>">
															<? if($menu['kep']): ?><img src="<?=\PortalManager\Formater::sourceImg($menu['kep'])?>"><? endif; ?>
															<?=$menu['nev']?></span>
													<? if($menu['link']): ?></a><? endif; ?>
													<? if($menu['child']): ?>
														<? foreach ( $menu['child'] as $child ) { ?>
															<div class="item <?=$child['css_class']?>">
																<?
																// Inclue
																if(strpos( $child['nev'], '=' ) === 0 ): ?>
																	<? echo $this->templates->get( str_replace('=','',$child['nev']), array( 'view' => $this ) ); ?>
																<? else: ?>
																<? if($child['link']): ?><a href="<?=$child['link']?>"><? endif; ?>
																<? if($child['kep']): ?><img src="<?=\PortalManager\Formater::sourceImg($child['kep'])?>"><? endif; ?>
																<span style="<?=$child['css_styles']?>"><?=$child['nev']?></span>
																<? if($child['link']): ?></a><? endif; ?>
																<? endif; ?>
															</div>
														<? } ?>
													<? endif; ?>
												</li>
											<? endforeach; ?>
										</ul>
									</div>
								</div>
							</div>
						</div>
						<div class="subs">
							<div class="subbox">
								<div class="wrapper">
									<div class="title">
										<h3>Feliratkozás</h3>
									</div>
									<div class="form">
										<form class="" action="/feliratkozas" method="get">
											<div class="name">
												<div class="flex flexmob-exc-resp">
													<div class="ico">
														<i class="fa fa-user"></i>
													</div>
													<div class="input">
														<input type="text" name="name" value="" placeholder="Név">
													</div>
												</div>
											</div>
											<div class="email">
												<div class="flex flexmob-exc-resp">
													<div class="ico">
														<i class="fa fa-envelope"></i>
													</div>
													<div class="input">
														<input type="text" name="email" value="" placeholder="E-mail">
													</div>
												</div>
											</div>
											<div class="phone">
												<div class="flex flexmob-exc-resp">
													<div class="ico">
														<i class="fa fa-phone"></i>
													</div>
													<div class="input">
														<input type="text" name="phone" value="" placeholder="Telefon">
													</div>
												</div>
											</div>
											<div class="button">
												<button type="submit" name="subscribe">Mehet</button>
											</div>
										</form>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="info">
			<div class="pw">
				<div class="flex">
					<div class="copy flex-col-40">
						&copy; <span class="author"><?=$this->settings['page_title']?></span>: <?=$this->settings['page_description']?> &nbsp;&nbsp; 2013 Minden jog fenntartva!
					</div>
					<div class="contact">
						<div class="flex">
							<div class="telefon">
								<div class="wrapper">
									<i class="fa fa-phone"></i>
									<div class="title">
										Telefon:
									</div>
									<div class="val">
										<a href="tel:<?=$this->settings['page_author_phone']?>"><?=$this->settings['page_author_phone']?></a>
									</div>
								</div>
							</div>
							<div class="email">
								<div class="wrapper">
									<i class="fa fa-envelope-o"></i>
									<div class="title">
										E-mail:
									</div>
									<div class="val">
										<a href="mailto:<?=$this->settings['primary_email']?>"><?=$this->settings['primary_email']?></a>
									</div>
								</div>
							</div>
							<div class="address">
								<div class="wrapper">
									<i class="fa fa-map-marker"></i>
									<div class="title">
										Cím:
									</div>
									<div class="val">
										<?=$this->settings['page_author_address']?>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="social flex-col-15">
						<div class="flex flexmob-exc-resp">
							<?php if ( !empty($this->settings['social_facebook_link'])) : ?>
							<div class="facebook">
								<a target="_blank" title="Facebook oldalunk" href="<?=$this->settings['social_facebook_link']?>"><i class="fa fa-facebook"></i></a>
							</div>
							<?php endif; ?>
							<?php if ( !empty($this->settings['social_youtube_link'])) : ?>
							<div class="youtube">
								<a target="_blank" title="Youtube csatornánk" href="<?=$this->settings['social_youtube_link']?>"><i class="fa fa-youtube"></i></a>
							</div>
							<?php endif; ?>
							<?php if ( !empty($this->settings['social_googleplus_link'])) : ?>
							<div class="googleplus">
								<a target="_blank" title="Google+ oldalunk" href="<?=$this->settings['social_googleplus_link']?>"><i class="fa fa-google-plus"></i></a>
							</div>
							<?php endif; ?>
							<?php if ( !empty($this->settings['social_twitter_link'])) : ?>
							<div class="twitter">
								<a target="_blank" title="Twitter oldalunk" href="<?=$this->settings['social_twitter_link']?>"><i class="fa fa-twitter"></i></a>
							</div>
							<?php endif; ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</footer>
</body>
</html>
