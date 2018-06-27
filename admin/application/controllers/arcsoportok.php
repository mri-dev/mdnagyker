<?
use PortalManager\PriceGroups;
use PortalManager\PriceGroup;

class arcsoportok extends Controller{
		function __construct(){
			parent::__construct();
			parent::$pageTitle = 'Ár csoportok / Adminisztráció';

 			$this->view->adm = $this->AdminUser;
			$this->view->adm->logged = $this->AdminUser->isLogged();

      $PriceGroups = new PriceGroups( array('db'=> $this->db) );
			////////////////////////////////////////////////////////////////////////////////////////

			// Új
			if( Post::on('addPriceGroup') )
			{
				try {
					$PriceGroups->add( $_POST );
					Helper::reload();
				} catch ( Exception $e ) {
					$this->view->err = true;
					$this->view->bmsg	= Helper::makeAlertMsg('pError', $e->getMessage());
				}
			}

			// Szerkesztés
			if ( $this->view->gets[1] == 'szerkeszt') {
				// Kategória adatok
				$item_data = new PriceGroup( $this->view->gets[2],  array( 'db' => $this->db )  );
				$this->out( 'PriceGroup', $item_data );

				// Változások mentése
				if( Post::on('savePriceGroup') )
				{
					try {
						$PriceGroups->edit( $item_data, $_POST );
						Helper::reload();
					} catch ( Exception $e ) {
						$this->view->err = true;
						$this->view->bmsg = Helper::makeAlertMsg('pError', $e->getMessage());
					}
				}
			}

			// Törlés
			if ( $this->view->gets[1] == 'torles') {
				// Adatok
				$item_data = new PriceGroup( $this->view->gets[2], array( 'db' => $this->db )  );
				$this->out( 'PriceGroup_d', $item_data );

				// Törlése
				if( Post::on('delPriceGroup') )
				{
					try {
						$PriceGroups->delete( $item_data );
						Helper::reload( '/'.__CLASS__ );
					} catch ( Exception $e ) {
						$this->view->err = true;
						$this->view->bmsg = Helper::makeAlertMsg('pError', $e->getMessage());
					}
				}
			}

      // LOAD
			////////////////////////////////////////////////////////////////////////////////////////
			$PriceGroup_trees	= $PriceGroups->getTree();
			// Kategoriák
			$this->out( 'PriceGroups', $PriceGroup_trees );

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

		function __destruct(){
			// RENDER OUTPUT
				parent::bodyHead();					# HEADER
				$this->view->render(__CLASS__);		# CONTENT
				parent::__destruct();				# FOOTER
		}
	}

?>
