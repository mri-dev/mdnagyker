<?php
use ShopManager\OrderException;

class reorder extends Controller{
		function __construct(){
			parent::__construct();

			if($this->view->gets[1] == ''){
				Helper::reload('/');
			}

		  $this->view->order = $this->shop->getOrderData( $this->view->gets[1], 'accessKey', array('reorder' => true ) );
			$this->view->order_user = $this->User->get( array( 'user' => $this->view->order[email] ) );
      $title = $this->view->order['azonosito'].' megrendelés újrarendelése';

      $this->view->orderAllapot = $this->shop->getMegrendelesAllapotok();
			$this->view->szallitas 	= $this->shop->getSzallitasiModok();
			$this->view->fizetes 	= $this->shop->getFizetesiModok();

			if ( Post::on('saveReorderData') )
			{
				try{
					unset($_POST['saveReorderData']);
					$this->shop->reorderSave( $this->view->order['azonosito'], $this->view->order['azonosito'], $_POST );
					Helper::reload();
				}catch(Exception $e){
					$this->out( 'msg', \Helper::makeAlertMsg('pError', $e->getMessage()));
				}
			}

			if ( Post::on('doReorder') )
			{
				try{
					$step = $this->shop->doOrder($_POST, array(
						'user' => $this->view->user,
						'reorder' => $this->view->gets[1]
					));
					Helper::reload('/kosar/done/'.$step);
					Helper::reload('/kosar/'.$step.'#step');
				}catch(OrderException $e){
					$this->view->orderExc = $e->getErrorData();
					$this->out( 'msg', \Helper::makeAlertMsg('pError', $this->view->orderExc['msg']) );
				}
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
