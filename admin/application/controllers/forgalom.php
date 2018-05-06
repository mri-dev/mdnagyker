<? 
use PortalManager\Traffic;

class forgalom extends Controller{
		function __construct(){	
			parent::__construct();
			parent::$pageTitle = 'Forgalom';
			
			
			$this->view->adm = $this->AdminUser;
			$this->view->adm->logged = $this->AdminUser->isLogged();
			
			
			if(Post::on('filterList')){
				$filtered = false;
				
				if($_POST[forgalom] != ''){
					setcookie('filter_forgalom',$_POST[forgalom],time()+60*24,'/'.$this->view->gets[0]);
					$filtered = true;
				}else{
					setcookie('filter_forgalom','',time()-100,'/'.$this->view->gets[0]);
				}
				if($_POST[megnevezes] != ''){
					setcookie('filter_megnevezes',$_POST[megnevezes],time()+60*24,'/'.$this->view->gets[0]);
					$filtered = true;
				}else{
					setcookie('filter_megnevezes','',time()-100,'/'.$this->view->gets[0]);
				}
				if($_POST[itemid] != ''){
					setcookie('filter_itemid',$_POST[itemid],time()+60*24,'/'.$this->view->gets[0]);
					$filtered = true;
				}else{
					setcookie('filter_itemid','',time()-100,'/'.$this->view->gets[0]);
				}
				
				if($_POST[tipus] != ''){
					setcookie('filter_tipus',$_POST[tipus],time()+60*24,'/'.$this->view->gets[0]);
					$filtered = true;
				}else{
					setcookie('filter_tipus','',time()-100,'/'.$this->view->gets[0]);
				}
				
				if($_POST[dateFrom] != ''){
					setcookie('filter_dateFrom',$_POST[dateFrom],time()+60*24,'/'.$this->view->gets[0]);
					$filtered = true;
				}else{
					setcookie('filter_dateFrom','',time()-100,'/'.$this->view->gets[0]);
				}
				if($_POST[dateTo] != ''){
					setcookie('filter_dateTo',$_POST[dateTo],time()+60*24,'/'.$this->view->gets[0]);
					$filtered = true;
				}else{
					setcookie('filter_dateTo','',time()-100,'/'.$this->view->gets[0]);
				}
				
				if($filtered){
					setcookie('filtered','1',time()+60*24*7,'/'.$this->view->gets[0]);
				}else{
					setcookie('filtered','',time()-100,'/'.$this->view->gets[0]);
				}				
				Helper::reload();
			}
			
			$this->traffic = 			new Traffic( array( 'db' => $this->db ) );
			$this->view->kulcsok 		= $this->traffic->getTipusKulcsok();
			$this->view->trafficInfo 	= $this->traffic->calcTrafficInfo();
			
			$arg = array();
			$arg[limit] = 25;
			$filters 		= Helper::getCookieFilter('filter',array('filtered'));
			$arg[filters] 	= $filters;
			$arg[dateFrom] 	= $_COOKIE[filter_dateFrom];
			$arg[dateTo] 	= $_COOKIE[filter_dateTo];
			$this->view->allTraffic		= $this->traffic->getAll($arg);
			
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
		
		function uj(){
			if(Post::on('add')){
				try{
					$opt = array();
					$this->traffic->add($opt);	
					Helper::reload('/'.$this->view->gets[0]);
				}catch(Exception $e){
					$this->view->err 	= true;
					$this->view->bmsg 	= Helper::makeAlertMsg('pError', $e->getMessage());
				}
			}
			
			$this->view->kulcsok = $this->traffic->getTipusKulcsok();
		}
		
		function tipus_kulcsok(){
			if(Post::on('add')){
				try{
					$this->traffic->addTipusKulcs($_POST);	
					Helper::reload('/'.$this->view->gets[0].'/'.$this->view->gets[1]);
				}catch(Exception $e){
					$this->view->err 	= true;
					$this->view->bmsg 	= Helper::makeAlertMsg('pError', $e->getMessage());
				}
			}
			
			switch($this->view->gets[2]){
				case 'szerkeszt':
					if(Post::on('save')){
						try{
							$this->traffic->saveTipusKulcs($_POST);	
							Helper::reload('/'.$this->view->gets[0].'/'.$this->view->gets[1]);
						}catch(Exception $e){
							$this->view->err 	= true;
							$this->view->bmsg 	= Helper::makeAlertMsg('pError', $e->getMessage());
						}
					}
					$this->view->sm = $this->traffic->getTipusKulcsAdat($this->view->gets[3]);
				break;
				case 'torles':
					if(Post::on('delId')){
						try{
							$this->traffic->delTipusKulcs($this->view->gets[3]);	
							Helper::reload('/'.$this->view->gets[0].'/'.$this->view->gets[1]);
						}catch(Exception $e){
							$this->view->err 	= true;
							$this->view->bmsg 	= Helper::makeAlertMsg('pError', $e->getMessage());
						}
					}
				break;
			}
			
			$this->view->kulcsok = $this->traffic->getTipusKulcsok();
		}
		
		function clearfilters(){
			setcookie('filter_forgalom','',time()-100,'/'.$this->view->gets[0]);
			setcookie('filter_megnevezes','',time()-100,'/'.$this->view->gets[0]);
			setcookie('filter_itemid','',time()-100,'/'.$this->view->gets[0]);
			setcookie('filter_tipus','',time()-100,'/'.$this->view->gets[0]);
			setcookie('filter_dateFrom','',time()-100,'/'.$this->view->gets[0]);
			setcookie('filter_dateTo','',time()-100,'/'.$this->view->gets[0]);
			setcookie('filtered','',time()-100,'/'.$this->view->gets[0]);
			Helper::reload('/'.$this->view->gets[0]);
		}
		
		function __destruct(){
			// RENDER OUTPUT
				parent::bodyHead();					# HEADER
				$this->view->render(__CLASS__);		# CONTENT
				parent::__destruct();				# FOOTER
		}
	}

?>