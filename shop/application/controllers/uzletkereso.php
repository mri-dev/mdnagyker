<? 
use PortalManager\CasadaShops;

class uzletkereso extends Controller
{
		function __construct(){	
			parent::__construct();
			parent::$pageTitle = 'Üzletkereső';

			$shops = new CasadaShops(array(
				'db' => $this->db
			));

			if( isset($_COOKIE['geo_latlng']) )
			{
				$myPos = explode(",",$_COOKIE['geo_latlng']);
				$shops->setMyPosition($myPos);
			} 
			
			$this->out('near_shop',$shops->getNearofMe());
			$this->out('shops',$shops->getList());
			$this->out('facelist', $this->User->getResellerFaceList());
							
			// SEO Információk
			$SEO = null;
			// Site info			
			$SEO .= $this->view->addMeta('description', 'Aktuális hivatalos Casada üzleteink és Casada Pontok.');
			$SEO .= $this->view->addMeta('keywords','casada hivatalos üzletek pontok átvétel tanácsadás teszt kipróbálás');
			$SEO .= $this->view->addMeta('revisit-after','3 days');
			
			// FB info
			$SEO .= $this->view->addOG('type','website');
			$SEO .= $this->view->addOG('url', CURRENT_URI );
			$SEO .= $this->view->addOG('image', $this->view->settings['domain'].'/admin'.$this->view->settings['logo']);
			$SEO .= $this->view->addOG('site_name', $this->view->settings['page_title']);			
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