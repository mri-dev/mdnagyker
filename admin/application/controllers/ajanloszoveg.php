<? 
use PortalManager\Portal;

class ajanloszoveg extends Controller{
		function __construct(){	
			parent::__construct();
			parent::$pageTitle = 'Ajánló szöveg / Adminisztráció';
			
			$this->view->adm = $this->AdminUser;
			$this->view->adm->logged = $this->AdminUser->isLogged();
			
				$portal = new Portal( array( 'db' => $this->db ) );

				// Admin létrehozása
				if (Post::on('addHighlight')) {
					try {
						$portal->addHighlight( $_POST );
						//Helper::reload();
					} catch ( Exception $e ) {
						$this->view->err = true;
						$this->view->msg = Helper::makeAlertMsg('pError', $e->getMessage()); 
					}
				}

				// Admin szerkesztése
				if (Post::on('saveHighlight')) {
					try {
						$portal->saveHighlight( $this->view->gets[2], $_POST );
						Helper::reload( '/ajanloszoveg/?msgkey=msg&msg=Változások sikeresen mentve!' );
					} catch ( Exception $e ) {
						$this->view->err = true;
						$this->view->msg = Helper::makeAlertMsg('pError', $e->getMessage()); 
					}
				}

				// Admin törlése
				if (Post::on('delHighlight')) {
					try {
						$portal->delHighlight( $_POST['delId']);
						Helper::reload( '/ajanloszoveg' );
					} catch ( Exception $e ) {
						$this->view->err = true;
						$this->view->msg = Helper::makeAlertMsg('pError', $e->getMessage()); 
					}
				}

		 	$arg = array(
		 		'limit' => 999,
		 		'page' => ( $this->view->gets[1] == 'torles' ? 1 : false),
		 		'admin' => 1
		 	);
			$this->out( 'ajanlok', $portal->getHighlightItems( $arg ) );

			if ( $this->view->gets[1] == 'szerkeszt' ) {
				$this->out( 'ajanlo', $portal->getHighlightItem( $this->view->gets[2] ) );
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
		}
		
		
		function __destruct(){
			// RENDER OUTPUT
				parent::bodyHead();					# HEADER
				$this->view->render(__CLASS__);		# CONTENT
				parent::__destruct();				# FOOTER
		}
	}

?>