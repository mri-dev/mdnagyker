<? if( true ): ?>
    <div class="category-listing page-width">
        <? $this->render('templates/slideshow'); ?>
        <div class="list-view webshop-product-top">
          <div class="grid-layout">
            <div class="grid-row filter-sidebar">
              <? $this->render('templates/sidebar'); ?>
            </div>
            <div class="grid-row products">
              <div>
                  <? if($this->parent_menu&& count($this->parent_menu) > 0): ?>
                  <div class="sub-categories">
                      <div class="title">
                          <h3><? $subk = ''; foreach($this->parent_row as $sc) { $subk .= $sc.' / '; } echo rtrim($subk,' / '); ?> alkategóriái</h3>
                          <? if($this->parent_category): ?>
                          <a class="back" href="<?=$this->parent_category->getURL()?>"><i class="fa fa-arrow-left"></i> vissza: <?=$this->parent_category->getName()?></a>
                           <? endif; ?>
                      </div>
                      <div class="holder">
                        <? foreach( $this->parent_menu as $cat ): ?>
                        <div class="item">
                          <div class="wrapper">
                            <div class="img"><a href="<?=$cat['link']?>"><img src="<?=rtrim(IMGDOMAIN,"/").$cat['kep']?>" alt="<?=$cat['neve']?>"></a></div>
                            <div class="title"><a href="<?=$cat['link']?>"><?=$cat['neve']?></a></div>
                          </div>
                        </div>
                        <? endforeach; ?>
                      </div>
                  </div>
                  <? endif; ?>

                  <div class="category-title head">
                      <?php if ($this->myfavorite): ?>
                        <h1>Kedvencnek jelölt termékek</h1>
                        <div class="push-cart-favorite">
                          <a href="/kedvencek/?order=1&after=/kosar">Kedvenceket a kosárba teszem <i class="fa fa-cart-plus"></i></a>
                        </div>
                      <?php elseif($this->category->getName() != ''): ?>
                        <h1><?=$this->category->getName()?></h1>
                      <?php else: ?>
                        <h1>Termékek</h1>
                        <?php if (isset($this->searched_by)): ?>
                          <div class="search-for">
                           <i class="fa fa-search"></i> Keresés, mint: <?php foreach ($this->searched_by as $s): ?>
                              <span><?=$s?></span>
                            <?php endforeach; ?>
                          </div>
                        <?php endif; ?>
                      <?php endif; ?>
                      <?php $navh = '/termekek/'; ?>
                      <ul class="cat-nav">
                        <li><a href="/"><i class="fa fa-home"></i></a></li>
                        <li><a href="<?=$navh?>">Webshop</a></li>
                        <?php if ($this->myfavorite): ?>
                        <li>Kedvencek</li>
                        <?php endif; ?>
                        <?php
                        foreach ( $this->cat_nav as $nav ): $navh = \Helper::makeSafeUrl($nav['neve'],'_-'.$nav['ID']); ?>
                        <li><a href="/termekek/<?=$navh?>"><?php echo $nav['neve']; ?></a></li>
                        <?php endforeach; ?>
                      </ul>
                      <div id="cart-msg"></div>
                  </div>

                  <? if( !$this->products->hasItems()): ?>
                  <div class="no-product-items">
                      <?php if ($this->myfavorite): ?>
                        <div class="icon"><i class="fa fa-fire"></i></div>
                        <strong>Nincsenek kedvencnek jelölt termékei!</strong><br>
                        Kedvencnek jelölhet bármilyen terméket, hogy később gyorsan és könnyedén megtalálja.
                      <?php else: ?>
                        <div class="icon"><i class="fa fa-fire"></i></div>
                        <strong>Nincsenek termékek ebben a kategóriában!</strong><br>
                        A szűrőfeltételek alapján nincs megfelelő termék, amit ajánlani tudunk. Böngésszen további termékeink között.
                      <?php endif; ?>
                  </div>
                  <? else: ?>
                      <div class="grid-container">

                      <? /* foreach ( $this->product_list as $p ) {
                          $p['itemhash'] = hash( 'crc32', microtime() );
                          $p['sizefilter'] = ( count($this->products->getSelectedSizes()) > 0 ) ? true : false;
                          echo $this->template->get( 'product_list_item', $p );
                      }*/ ?>
                          <div class="items">
                              <? foreach ( $this->product_list as $p ) {
                                  $p['itemhash'] = hash( 'crc32', microtime() );
                                  $p['sizefilter'] = ( count($this->products->getSelectedSizes()) > 0 ) ? true : false;
                                  $p['show_variation'] = ($this->myfavorite) ? true : false;
                                  $p = array_merge( $p, (array)$this );
                                  echo $this->template->get( 'product_item', $p );
                              } ?>
                          </div>
                      </div>
                      <div class="clr"></div>
                      <? echo $this->navigator; ?>
                  <br>
                  <? endif; ?>
              </div>
            </div>
          </div>

        </div>
    </div>
<? else: ?>
    <?=$this->render('home')?>
<? endif; ?>
