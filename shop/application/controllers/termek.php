<?
use ProductManager\Products;
use PortalManager\Template;

class termek extends Controller{
		function __construct(){
			parent::__construct();
			$title = '';

			$this->out( 'bodyclass', 'product-page');

			$products = new Products( array(
				'db' => $this->db,
				'user' => $this->User->get(),
				'settings' => $this->view->settings
			) );

			$product =  $products->get( Product::getTermekIDFromUrl() );
			$product['links'] = $products->getProductLinksFromStr($product['linkek']);

			$this->out( 'product', $product );
			$this->out( 'slideshow', $this->Portal->getSlideshow( $product['nev'] ) );


			// Nincs kép a termékről - átirányítás
			if( strpos( $product['profil_kep'] , 'no-product-img' ) !== false ) {
				Helper::reload('/');
			}

			// Termék kérdés
			if(Post::on('requestRecall')){
				try{
					$this->view->msg 	= Helper::makeAlertMsg('pSuccess',$this->shop->requestReCall($_POST));
				}catch(Exception $e){
					$this->view->err 	= true;
					$this->view->msg 	= Helper::makeAlertMsg('pError',$e->getMessage(), 'Telefonos szaktanácsadás &mdash; hiba:');
				}
			}

			/****
			* TOP TERMÉKEK
			*****/
			$arg = array(
				'limit' 	=> 5,
				'collectby' => 'top'
			);
			$top_products = (new Products( array(
				'db' => $this->db,
				'user' => $this->User->get()
			) ))->prepareList( $arg );
			$this->out( 'top_products', $top_products );
			$this->out( 'top_products_list', $top_products->getList() );

			/****
			* MEGNÉZETT TERMÉKEK
			*****/
			$arg = array();
			$viewed_products = (new Products( array(
				'db' => $this->db,
				'user' => $this->User->get()
			) ))->getLastviewedList( \Helper::getMachineID(), 5, $arg );
			$this->out( 'viewed_products_list', $viewed_products );

			/****
			* Live TERMÉKEK
			*****/
			$arg = array();
			$live_products = (new Products( array(
				'db' => $this->db,
				'user' => $this->User->get()
			) ))->getLiveviewedList( \Helper::getMachineID(), 5, $arg );
			$this->out( 'live_products_list', $live_products );


			// További ajánlott termékek
			if ( $product['related_products_ids'] )
			{
				// Template
				$temp = new Template( VIEW . 'templates/' );
				$this->out( 'template', $temp );

				$arg = array(
					'except' => array(
						'ID' => Product::getTermekIDFromUrl()
					),
					'limit' => 99999,
					'in_ID' => $product['related_products_ids']
				);

				$related = $products->prepareList( $arg );

				$this->out( 'related', $related );
				$this->out( 'related_list', $related->getList() );
			}

			$title = $product['nev'].' | '.$product['csoport_kategoria'];

			$this->shop->logTermekView(Product::getTermekIDFromUrl());
			$this->shop->logLastViewedTermek(Product::getTermekIDFromUrl());

			// Meta
			$desc = substr(strip_tags($this->view->product['rovid_leiras']), 0, 250).'...';

			$meta_title = $product['meta_title'];
			$meta_desc = $product['meta_desc'];

			if ($meta_title != '') {
				$title = $meta_title;
			}

			if ($meta_desc != '') {
				$desc = $meta_desc;
			}

			// SEO Információk
			$SEO = null;
			// Site info
			$SEO .= $this->view->addMeta('description',addslashes($desc));
			$keyw = $this->view->product['kulcsszavak'];
			$keyw .= " ".$this->view->product['csoport_kategoria'];
			$SEO .= $this->view->addMeta('keywords',addslashes($keyw));
			$SEO .= $this->view->addMeta('revisit-after','3 days');

			// FB info
			$SEO .= $this->view->addOG('title',addslashes($title));
			$SEO .= $this->view->addOG('description',addslashes($desc));
			$SEO .= $this->view->addOG('type','product');
			$SEO .= $this->view->addOG('url',$this->view->settings['page_url'].'/'.substr($_SERVER[REQUEST_URI],1));
			$SEO .= $this->view->addOG('image',$product[profil_kep]);
			$SEO .= $this->view->addOG('site_name', $title);

			// FB - OG - PRODUCT
			$ar = $product['brutto_ar'];
			$SEO .= '<meta property="product:original_price:amount" content="'.$ar.'" />'."\n\r";
			$SEO .= '<meta property="product:original_price:currency" content="HUF" />'."\n\r";
			$SEO .= '<meta property="product:price:amount" content="'.$ar.'" />'."\n\r";
			$SEO .= '<meta property="product:price:currency" content="HUF" />'."\n\r";

			if( $product['akcios'] == '1' && $product['akcios_fogy_ar'] > 0){
				$SEO .= '<meta property="product:price:sale_amount" content="'.$product['akcios_fogy_ar'].'" />'."\n\r";
				$SEO .= '<meta property="product:price:sale_currency" content="HUF" />'."\n\r";
			}

			$this->view->SEOSERVICE = $SEO;


			parent::$pageTitle = $title;
		}

		function __destruct(){
			// RENDER OUTPUT
				parent::bodyHead();					# HEADER
				$this->view->render(__CLASS__);		# CONTENT
				parent::__destruct();				# FOOTER
		}
	}

?>
