<? 
use Applications\Simple;
use Applications\Cetelem;

class order extends Controller{
		function __construct(){	
			parent::__construct();
			$title = 'Megrendelés adatlapja';
						
			if($this->view->gets[1] == ''){
				Helper::reload('/');
			}

			// PayPal befizetés logolás
			if($this->view->gets[2] == 'paid_via_paypal'){
				if(!$this->shop->orderAlreadyPaidViaPayPal($this->view->gets[1])){
					$this->shop->setOrderPaidByPayPal($this->view->gets[1]);
					Helper::reload('/'.__CLASS__.'/'.$this->view->gets[1]);
				}else{
					Helper::reload('/'.__CLASS__.'/'.$this->view->gets[1]);
				}
			}
						
			$this->view->orderAllapot = $this->shop->getMegrendelesAllapotok();
			$this->view->szallitas 	= $this->shop->getSzallitasiModok();
			$this->view->fizetes 	= $this->shop->getFizetesiModok();
			
			$this->view->order = $this->shop->getOrderData($this->view->gets[1]);
			$this->view->order_user = $this->User->get( array( 'user' => $this->view->order[email] ) );
			
			if(empty($this->view->order[items])){
				Helper::reload('/');
			}

			/** PAYU FIZETÉS */
			$order_id = $this->view->order['azonosito'];

			if( $order_id == '' ){
				Helper::reload( '/user' );
			}

			$this->view->order['szallitas_adat'] 		= json_decode($this->view->order['szallitasi_keys'], true);
			$this->view->order['szamlazas_adat'] 		= json_decode($this->view->order['szamlazasi_keys'], true);

			$this->pay = (new Simple())
				->setMerchant( 	'HUF', $this->view->settings['payu_merchant'])
				->setSecretKey( 'HUF', $this->view->settings['payu_secret'] )
				->setCurrency( 	'HUF' )
				->setOrderId( $order_id )
				->setData( $this->view->order );

			if ( $this->view->order['szallitasi_koltseg'] > 0 ) {
				$this->pay->setTransportPrice( $this->view->order['szallitasi_koltseg'] );
			}

			// Kedvezmény (%) kiszámítása
			$discount = 0;
			if ( $this->view->order['kedvezmeny'] != 0 ) {
				$discount = (int)$this->view->order['kedvezmeny'];
			}	
			$this->pay->setDiscount($discount);
			$this->pay->prepare();			
			$this->out( 'pay_btn', $this->pay->getPayButton() );

			/**
			 * CETELEM HITEL
			**/
			if ($this->view->order['fizetesiModID'] == $this->view->settings['flagkey_pay_cetelem']) 
			{
				$cetelem = (new Cetelem( 
					$this->view->settings['cetelem_shopcode'], 
					$this->view->settings['cetelem_society'], 
					$this->view->settings['cetelem_barem'], 
					array( 'db' => $this->db ) 
				))->sandboxMode( CETELEM_SANDBOX_MODE );

				$this->out('cetelem_status_code', $cetelem->getTransactionStatus($this->view->order['accessKey'], false));
				$this->out('cetelem_status', $cetelem->getTransactionStatus($this->view->order['accessKey'], true));
				$this->out('cetelem_start_url', '/gateway/cetelem/start/'.$this->view->order['accessKey']);
				$this->out('cetelem_ipn_list', $cetelem->getIPNList($this->view->order['accessKey']));
			}


												
			// SEO Információk
			$SEO = null;
			// Site info
			$SEO .= $this->view->addMeta('description','');
			$SEO .= $this->view->addMeta('keywords','');
			$SEO .= $this->view->addMeta('revisit-after','3 days');
			
			// FB info
			$SEO .= $this->view->addOG('type','website');
			$SEO .= $this->view->addOG('url',DOMAIN);
			$SEO .= $this->view->addOG('image',DOMAIN.substr(IMG,1).'noimg.jpg');
			$SEO .= $this->view->addOG('site_name',TITLE);
			
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