<? class markak extends Controller{
		function __construct(){	
			parent::__construct();
			parent::$pageTitle = 'Márkák / Adminisztráció';
			
			 
 			$this->view->adm = $this->AdminUser;
			$this->view->adm->logged = $this->AdminUser->isLogged();
			
			
			if(Post::on('addUjMarka')){
				try{
					$this->AdminUser->addMarka($_POST);
					Helper::reload();
				}catch(Exception $e){
					$this->view->err 	= true;
					$this->view->bmsg 	= Helper::makeAlertMsg('pError', $e->getMessage()); 
				}
			}
			
			$this->view->markak 	= $this->AdminUser->getMarkak();
			$this->view->nagykerek 	= $this->AdminUser->getNagykerek();
			
			switch($this->view->gets[1]){
				case 'szerkeszt':
					if(Post::on('saveMarka')){
						try{
							$this->AdminUser->editMarka($_POST);
						//	Helper::reload();
						}catch(Exception $e){
							$this->view->err 	= true;
							$this->view->emsg 	= Helper::makeAlertMsg('pError', $e->getMessage()); 
						}
					}
					
					$this->view->marka 		= $this->AdminUser->getMarka($this->view->gets[2]);
					$this->view->markaSavok = $this->AdminUser->getMarkaArresek($this->view->gets[2]); 
				break;
				case 'torles':
					if(Post::on('delId')){
						$this->AdminUser->delMarka($this->view->gets[2]);
						Helper::reload('/markak');
					}
				break;
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