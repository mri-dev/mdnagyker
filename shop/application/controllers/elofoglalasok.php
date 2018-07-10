<?php
use ShopManager\PreOrders;

class elofoglalasok extends Controller
{
		function __construct()
		{
			parent::__construct();
			parent::$pageTitle = 'Előfoglalás';

			$mid = (isset($_GET['a']) && !empty($_GET['a'])) ? $_GET['a'] : \Helper::getMachineID();

			$preorder = new PreOrders(array('db' => $this->db));
			$preorder->getTree(array(
				'gepID' => $mid,
				'sessionOrder' => (isset($_GET['session']) && !empty($_GET['session'])) ? $_GET['session'] : false
			));
			$this->out('preorder', $preorder);

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
