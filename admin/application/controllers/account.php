<?
use ProductManager\Products;

class account extends Controller
{
	function __construct(){
		parent::__construct();
		parent::$pageTitle = 'Fiókok';

      $this->view->adm = $this->AdminUser;
		$this->view->adm->logged = $this->AdminUser->isLogged();

		$products = new Products( array(
			'db' => $this->db,
			'user' => $this->User->get()
		) );
		$price_groups = $products->priceGroupList();
		// Ár csoportok
		$this->view->price_groups = $price_groups;

		if (Post::on('createUserByAdmin')) {
			try {
				$this->User->createByAdmin($_POST);
				$return = '';
				if(isset($_GET['ret'])) {
					$return = $_GET['ret'];
				}
				Helper::reload($return);

			} catch (\Exception $e) {
				$this->view->err 	= true;
				$this->view->msg 	= Helper::makeAlertMsg('pError', $e->getMessage());
			}
		}

		if (Post::on('saveUserByAdmin')) {
			try {
				$this->User->saveByAdmin($_GET['ID'],$_POST);
				$return = '';
				if(isset($_GET['ret'])) {
					$return = $_GET['ret'];
				}
				Helper::reload($return);
			} catch (\Exception $e) {
				$this->view->err 	= true;
				$this->view->msg 	= Helper::makeAlertMsg('pError', $e->getMessage());
			}
		}

		if (Post::on('delAccount')) {
			try {
				$this->User->deleteUser( $_GET['ID'] );
				$return = '';
				if(isset($_GET['ret'])) {
					$return = $_GET['ret'];
				}
				Helper::reload($return);
			} catch (\Exception $e) {
				$this->view->err 	= true;
				$this->view->msg 	= Helper::makeAlertMsg('pError', $e->getMessage());
			}
		}

		// Szerkesztés
		if ($_GET['t'] == 'edit' || $_GET['t'] == 'delete')
		{
			$data 	= $this->User->get(array('user' => $_GET['ID'], 'userby' => 'ID'));
			$this->out('data',$data['data']);
		}

		$this->out('user_groupes',$this->User->getUserGroupes());



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
