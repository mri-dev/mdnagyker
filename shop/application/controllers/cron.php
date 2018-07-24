<?
use ShopManager\PreOrders;
use ResourceImporter\CashmanAPI;
use ResourceImporter\ResourceImport;

class cron extends Controller{
		function __construct(){
			parent::__construct();
			parent::$pageTitle = '';

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
		}

    public function clearExpiredPreorders()
    {
      $preorder = new PreOrders(array('db' => $this->db));
      $crm = new CashmanAPI(array('db' => $this->db));

      $preorder->addAPIHandler( $crm );
      $preorder->clearExpiredCRON();
    }

		public function sync()
		{
			$crm = new CashmanAPI(array('db' => $this->db));

			switch ( $this->gets[2] )
			{
				case 'importProgramTechToCashman':

					$res = new ResourceImport(array('db' => $this->db));

					//$raw_raktar = $res->getRaktarXML();

					$items = array();

					if ( count($raw_raktar->sor) != 0 ) {
						foreach ( $raw_raktar->sor as $sor )
						{
							if( (string)$sor->Mértékegység == 'darab' || (string)$sor->Mértékegység == 'db' || (string)$sor->Mértékegység == '' ){
								$mee = 'db';
							} else {
								$mee = (string)$sor->Mértékegység;
							}
							$tid = 0;

							$items[] = array(
								'termek_id' => $tid,
								'cikkszam' => (string)$sor->Cikkszám,
								'megnevezes' =>(string) $sor->Megnevezés,
								'megjegyzes' => (string)$sor->Tétel_megjegyzés,
								'afa' => 27,
								'netto_egysegar' => (float)$sor->Eladási_nettó_egységár,
								'termekcsoport_id' => 1,
								'mennyisegiegyseg' => $mee
							);
						}
					}

					//$return = $crm->addProduct( $items );

					print_r($return);
				break;

				case 'importProducts':
					$res = new ResourceImport(array('db' => $this->db));
					$products = $crm->getProducts();
					$crm->autoImportProducts( (array)$products['data'] );

					echo '<pre>';
					print_r($products);
					echo '</pre>';
				break;

				case 'getProduct':
					$data = $crm->getProduct('1440-02');
					echo '<pre>';
					print_r($data);
					echo '</pre>';
				break;
			}
		}

		function __destruct(){
			// RENDER OUTPUT
				//parent::bodyHead();					# HEADER
				//$this->view->render(__CLASS__);		# CONTENT
				//parent::__destruct();				# FOOTER
		}
	}

?>
