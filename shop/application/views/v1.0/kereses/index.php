<div class="search page-width">
    <div class="responsive-view category-listing">
        <div class="category-title head">
             <div class="filters">
                <form action="/<?=$this->gets[0]?>/<?=$this->gets[1]?>/-/1<?=( $this->cget != '' ) ? '?'.$this->cget : ''?>" method="get">
                <ul>     
                    <li><button class="btn btn-default btn-sm">szűrés <i class="fa fa-refresh"></i></button></li><li>                    
                        <select name="order" class="form-control">
                            <option value="nev_asc"     <?=($_GET['order'] == 'nev_asc')?'selected="selected"':''?>>Név: A-Z</option>
                            <option value="nev_desc"    <?=($_GET['order'] == 'nev_desc')?'selected="selected"':''?>>Név: Z-A</option>
                            <option value="ar_asc"      <?=($_GET['order'] == 'ar_asc')?'selected="selected"':''?>>Ár: növekvő</option>
                            <option value="ar_desc"     <?=($_GET['order'] == 'ar_desc')?'selected="selected"':''?>>Ár: csökkenő</option>
                        </select>
                    </li><li><?=__('Rendezés')?></li>                          
                </ul>
                </form>
            </div>
            <h1>Keresés <small><? if($_GET['v'] == 'akciok'): ?>Akciós termékek<? endif; ?></small></h1>
           
            <div class="subtitles">
               <div class="search-hashs">
                    <? if($this->search_hashs[0] != "") foreach( $this->search_hashs as $hash ): ?>
                    <span>#<?=$hash?></span>
                    <? endforeach; ?>
                </div> 
                <div class="result-num"><strong><?=$this->products->getItemNumbers()?> db</strong> találat</div>            
            </div>             
        </div>
        <div class="divider"></div>          
        <div class="searchResult">               
            <div class="products webshop-product-top">
                <? if( !$this->products->hasItems()): ?>
                <div class="no-product-items">
                    Nincsennek termékek a keresés feltételei alapján!
                </div>
                <? else: ?>
                    <div class="grid-container">
                        <div class="items">
                        <? foreach ( $this->product_list as $p ) { 
                            $p['itemhash'] = hash( 'crc32', microtime() );
                            $p['sizefilter'] = ( count($this->products->getSelectedSizes()) > 0 ) ? true : false;
                            echo $this->template->get( 'product_item', $p );                   
                        } ?>
                        </div>
                    </div>
                    <div class="clr"></div>
                    <?=$this->navigator?>
                <br>
                <? endif; ?>
            </div>
        </div>        
    </div>
</div>
<script type="text/javascript">
    $(function(){
        $('.listHead input[type=radio]').change(function(){
            $('.searchResult').find('form').submit();
        }); 
        $('.products > .grid-container > .item .colors-va li').bind( 'mouseover', function(){
            var hash    = $(this).attr('hashkey');
            var mlink   = $('.products > .grid-container > .item').find('.item_'+hash+'_link');
            var mimg    = $('.products > .grid-container > .item').find('.item_'+hash+'_img');

            var url = $(this).find('a').attr('href');
            var img = $(this).find('img').attr('data-img');
            
            mimg.attr( 'src', img );
            mlink.attr( 'href', url );
        });
    })
</script>