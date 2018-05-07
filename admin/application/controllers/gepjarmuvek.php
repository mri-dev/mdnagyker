<?
use PortalManager\Vehicles;
use PortalManager\Vehicle;

class gepjarmuvek extends Controller{
		function __construct(){
			parent::__construct();
			parent::$pageTitle = 'Gépjárművek / Adminisztráció';

 			$this->view->adm = $this->AdminUser;
			$this->view->adm->logged = $this->AdminUser->isLogged();

      $vehicles = new Vehicles( array('db'=> $this->db) );
			////////////////////////////////////////////////////////////////////////////////////////

			// Új
			if( Post::on('addVehicle') )
			{
				try {
					$vehicles->add( $_POST );
					Helper::reload();
				} catch ( Exception $e ) {
					$this->view->err = true;
					$this->view->bmsg	= Helper::makeAlertMsg('pError', $e->getMessage());
				}
			}

			// Szerkesztés
			if ( $this->view->gets[1] == 'szerkeszt') {
				// Kategória adatok
				$item_data = new Vehicle( $this->view->gets[2],  array( 'db' => $this->db )  );
				$this->out( 'vehicle', $item_data );

				// Változások mentése
				if( Post::on('saveVehicle') )
				{
					try {
						$vehicles->edit( $item_data, $_POST );
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
				$item_data = new Vehicle( $this->view->gets[2], array( 'db' => $this->db )  );
				$this->out( 'vehicle_d', $item_data );

				// Törlése
				if( Post::on('delVehicle') )
				{
					try {
						$vehicles->delete( $item_data );
						Helper::reload( '/gepjarmuvek' );
					} catch ( Exception $e ) {
						$this->view->err = true;
						$this->view->bmsg = Helper::makeAlertMsg('pError', $e->getMessage());
					}
				}
			}

      // LOAD
			////////////////////////////////////////////////////////////////////////////////////////
			$vehicle_trees	= $vehicles->getTree();
			// Kategoriák
			$this->out( 'vehicles', $vehicle_trees );

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
