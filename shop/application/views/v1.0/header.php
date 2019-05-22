<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html xmlns="http://www.w3.org/1999/html4"
      xmlns:og="http://ogp.me/ns#"
      xmlns:fb="http://www.facebook.com/2008/fbml" lang="hu-HU" ng-app="mobildata">
<head>
    <title><?=$this->title?></title>
    <?=$this->addMeta('robots','index,folow')?>
    <?=$this->SEOSERVICE?>
    <?php if ( $this->settings['FB_APP_ID'] != '' ): ?>
    <meta property="fb:app_id" content="<?=$this->settings['FB_APP_ID']?>" />
    <?php endif; ?>
    <? $this->render('meta'); ?>
</head>
<body class="<?=$this->bodyclass?>" ng-controller="App" ng-init="init(<?=($this->gets[0] == 'kosar' && $this->gets[1] == 4)?'true':'false'?>)">
<div ng-show="showed" ng-controller="popupReceiver" class="popupview" data-ng-init="init({'contentWidth': 1150, 'domain': '.autoradiokeret.web-pro.hu', 'receiverdomain' : '<?=POPUP_RECEIVER_URL?>', 'imageRoot' : '<?=POPUP_IMG_ROOT?>/'})"><ng-include src="'/<?=VIEW?>popupview.html'"></ng-include></div>
<? if(!empty($this->settings[google_analitics])): ?>
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', ' <?=$this->settings[google_analitics]?>', 'auto');
  ga('send', 'pageview');
</script>
<? endif; ?>
<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/hu_HU/sdk.js#xfbml=1&version=v2.3";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>
<!--Start of Tawk.to Script-->
<script type="text/javascript">
var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
(function(){
var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
s1.async=true;
s1.src='https://embed.tawk.to/5cdc1ae8d07d7e0c6393bffb/default';
s1.charset='UTF-8';
s1.setAttribute('crossorigin','*');
s0.parentNode.insertBefore(s1,s0);
})();
</script>
<!--End of Tawk.to Script-->
<header>
  <div class="top">
    <div class="pw">
      <div class="flex">
        <div class="social">
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
        <div class="contact hide-on-mobile">
          <div class="flex">
            <div class="telefon">
              <div class="wrapper">
                <i class="fa fa-phone"></i>
                <div class="val">
                  <a href="tel:<?=$this->settings['page_author_phone']?>"><?=$this->settings['page_author_phone']?></a>
                </div>
              </div>
            </div>
            <div class="email">
              <div class="wrapper">
                <i class="fa fa-envelope"></i>
                <div class="val">
                  <a href="mailto:<?=$this->settings['primary_email']?>"><?=$this->settings['primary_email']?></a>
                </div>
              </div>
            </div>
            <div class="address">
              <div class="wrapper">
                <i class="fa fa-map-marker"></i>
                <div class="val">
                  <?=$this->settings['page_author_address']?>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="navs">
          <div class="flex">
            <div class="viszonteladok">
              <a href="/viszontelado"><i class="fa fa-users"></i> Viszonteladók jelentkezése</a>
            </div>
            <div class="login">
              <?php if ($this->user): ?>
              <a href="/user/"><i class="fa fa-user"></i> Fiókom</a>
              <?php else: ?>
              <a href="/user/login"><i class="fa fa-user"></i> Belépés</a>
              <?php endif; ?>
            </div>
            <div class="div"></div>
            <div class="kedvencek">
              <a href="/kedvencek"><i class="fa fa-star"></i> Kedvencek <span class="badge">{{fav_num}}</span></a>
            </div>
            <?php if (!$this->user || ($this->user && $this->user['data']['user_group'] == 'company')): ?>
            <div class="div"></div>
            <div class="elofoglalas">
              <a href="/elofoglalasok"><i class="fa fa-pause-circle-o"></i> Előfoglalás <span class="badge">{{prodres_num}}</span></a>
            </div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="main">
    <div class="pw">
      <div class="flex">
        <div class="logo">
          <a href="<?=$this->settings['page_url']?>"><img src="<?=IMG?>mobildatabt_logo.svg" alt="<?=$this->settings['page_title']?>"></a>
        </div>
        <div class="searcher">
          <div class="searchform">
            <form class="" action="/termekek/" method="get">
            <div class="flex flexmob-exc-resp">
              <div class="input">
                <input type="text" name="src" value="<?=$_GET['src']?>" placeholder="Keresési kifejezés megadása">
                <div class="autocomplete-holder" id="searcher_autocomplete">

                </div>
              </div>
              <div class="button">
                <button type="submit"><i class="fa fa-search"></i></button>
              </div>
              <?php if (true): ?>
                <div class="more">
                  <a href="javascript:void(0);" mb-event="true" data-mb='{ "event": "toggleOnClick", "target" : "#moresearch" }'>Összetett keresés [+]</a>
                </div>
              <?php endif; ?>
            </div>

            <?php if (true): ?>
            <div class="more-search" id="moresearch">
              <div class="wrapper">

                <div class="row">
                  <div class="col-md-4 left">
                    Logikai kapcsolat
                  </div>
                  <div class="col-md-8 left">
                    <div class="">
                      <input type="radio" <?=(isset($_GET['srel']) && !empty($_GET['srel']) && $_GET['srel'] == 'and')?'checked="checked"':''?> name="srel" value="and"> <strong>ÉS</strong> (mindegyik szó szerepel)
                    </div>
                    <div class="">
                      <input type="radio" <?=((isset($_GET['srel']) && !empty($_GET['srel']) && $_GET['srel'] == 'or') || $_GET['srel'] == '')?'checked="checked"':''?> name="srel" value="or"> <strong>VAGY</strong>(bármely szó szerepel)
                    </div>
                  </div>
                </div>
                <?php if ($this->categories): $srcfilters = array(); $srcfilters = explode(",",$_GET['cat']); ?>
                <div class="row">
                  <div class="col-md-4 left">
                    Kategória
                  </div>
                  <div class="col-md-8 left">
                    <?php $srccatid  = 'srccat'; ?>
                    <div class="selector-wrapper">
                      <input type="hidden" name="cat" id="p_<?=$srccatid?>_v" />
                      <div id="pmf_<?=$srccatid?>">
                        <div class="selectors">
                           <div class="selector" key="p_<?=$srccatid?>" id="p_<?=$srccatid?>">összes</div>
                           <div class="selectorHint p_<?=$srccatid?>" style="display:none;">
                              <ul>
                                 <?php foreach ( $this->categories->tree  as $cat ) { ?>
                                 <li><input type="checkbox" <?=(in_array($cat['ID'],$srcfilters))?'checked':''?> id="p_<?=$srccatid?>_v<?=$cat['ID']?>" for="p_<?=$srccatid?>" text="<?=$cat['neve']?>" value="<?=$cat['ID']?>" /><label for="p_<?=$srccatid?>_v<?=$cat['ID']?>"><?=$cat['neve']?></label></li>
                                 <?php } ?>
                              </ul>
                           </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <?php endif; ?>

                <div class="row">
                  <div class="col-md-12 left">
                    <div class="info-text">
                      <div class="info">
                        A keresési kulcsszavak elválasztásához használjon vesszőt! Pl.: gladen, koaxiális, 10 cm, 3 ohm
                      </div>
                      <? $srctags = explode(",",$_GET['src']); ?>
                      <?php if ($srctags[0] != ''): ?>
                        <div class="searched-info">
                          <div class=""><strong>Keresett kulcsszavak:</strong></div>
                          <div class="keys">
                            <?php $srctagstr = ''; foreach ($srctags as $key): $srctagstr .= '<span>'.trim($key).'</span>'; endforeach; ?>
                            <?php echo $srctagstr; ?>
                          </div>
                        </div>
                      <?php endif; ?>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <?php endif; ?>
            </form>
          </div>
        </div>
        <div class="actions">
          <div class="cart">
            <div class="holder" id="mb-cart" mb-event="true" data-mb='{ "event": "toggleOnClick", "target" : "#mb-cart" }'>
              <div class="ico">
                <span class="badge" id="cart-item-num-v">0</span>
                <img src="<?=IMG?>icons/cart.svg" alt="Kosár" />
              </div>
              <div class="info">
                <div class="cash"><span class="amount" id="cart-item-prices">0</span> Ft</div>
              </div>
              <div class="floating">
                <div id="cartContent" class="overflowed">
                  <div class="noItem"><div class="inf">A kosár üres</div></div>
                </div>
                <div class="whattodo">
                  <div class="flex">
                    <div class="doempty">
                      <a href="/kosar/?clear=1">Kosár ürítése <i class="fa fa-trash"></i></a>
                    </div>
                    <div class="doorder">
                      <a href="/kosar">Megrendelése <i class="fa fa-arrow-circle-o-right"></i></a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="bottom">
    <div class="pw">
      <div class="flex <?=(!in_array($this->gets[0], array('termekek','home', 't', 'termek')))?'goleft':''?>">
        <div class="nav nav-fix">
          <ul>
            <?php if ($this->product): ?>
            <li class="cats">
              <a href="/termekek"><i class="fa fa-sliders"></i> Összes termék <i class="fa fa-angle-down"></i></a>
              <div class="list">
                <?php $this->render('templates/sidebar_menu'); ?>
              </div>
            </li>
            <?php endif; ?>
            <li class="kiemelt"><a href="/termekek/kiemelt">Kiemelt ajánlataink</a></li>
            <li class="akciok"><a href="/termekek/akciok">Akciók</a></li>
          </ul>
        </div>
        <div class="nav">
  				<ul>
  					<? foreach ( $this->menu_header->tree as $menu ): ?>
  					<li>
  						<a href="<?=($menu['link']?:'')?>">
  							<? if($menu['kep']): ?><img src="<?=\PortalManager\Formater::sourceImg($child['kep'])?>"><? endif; ?>
  							<?=$menu['nev']?> <? if($menu['child']): ?><i class="fa fa-angle-down"></i><? endif; ?></a>
    						<? if($menu['child']): ?>
    						<div class="sub nav-sub-view">
    								<div class="inside">
                      <ul>
                      <? foreach($menu['child'] as $child): ?>
                      <li class="<?=$child['css_class']?>">
                        <? if($child['link']): ?><a href="<?=$child['link']?>"><? endif; ?>
                        <span style="<?=$child['css_styles']?>"><?=$child['nev']?></span>
                        <? if($child['link']): ?></a><? endif; ?>
                      </li>
                      <? endforeach; ?>
                      </ul>
    								</div>
    						</div>
    						<? endif; ?>
  					</li>
  					<? endforeach; ?>
  				</ul>
  			</div>
      </div>
    </div>
  </div>
  <?php if (false): ?>
  <div class="sec-bottom">
    <div class="pw">
      <? if( count($this->highlight_text) > 0 ): ?>
      <div class="highlight-view">
      	<div class="items">
      		<div class="hl-cont">
      			<? if( count($this->highlight_text['data']) > 1 ): ?>
      			<a href="javascript:void(0);" title="Előző" class="prev handler" key="prev"><i class="fa fa-angle-left"></i> |</a>
      			<a href="javascript:void(0);" title="Következő" class="next handler" key="next">| <i class="fa fa-angle-right"></i></a>
      			<? endif; ?>
      			<ul>
      				<? $step = 0; foreach( $this->highlight_text['data'] as $text ): $step++; ?>
      				<li class="<?=($step == 1)?'active':''?>" index="<?=$step?>"><?=$text['tartalom']?></li>
      				<? endforeach; ?>
      			</ul>
            <div class="clr"></div>
      		</div>
      	</div>
      </div>
      <? endif; ?>
    </div>
  </div>
  <?php endif; ?>
</header>
<?php if ( !$this->homepage ): ?>
<!-- Content View -->
<div class="website">
		<?=$this->gmsg?>
		<div class="general-sidebar"></div>
		<div class="site-container <?=($this->gets[0]=='termek' )?'productview':''?>">
			<div class="clr"></div>
			<div class="inside-content">
<?php endif; ?>
