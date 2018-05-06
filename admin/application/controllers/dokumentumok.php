<? 
use FileManager\FileLister;

class dokumentumok extends Controller {
		function __construct(){	
			parent::__construct();
			parent::$pageTitle = 'Dokumentumok';

			$this->view->adm = $this->AdminUser;
			$this->view->adm->logged = $this->AdminUser->isLogged();

			if( Post::on('regFile') ) 
			{
				try 
				{
					$this->shop->registerFileDocument( $_POST );
					Helper::reload( '/dokumentumok' );
				} catch( \Exception $e ) 
				{
					$this->view->msg = Helper::makeAlertMsg( 'pError', $e->getMessage() );
				}				
			}

			$files = new FileLister('src/uploaded_files');

			$this->out( 'files', 		$this->shop->checkDocuments(false, $files, array('showOffline' => true, 'showHided' => true)));
			$this->out( 'doc_groupes', 	$this->shop->getDocumentGroupes());			
			$this->out( 'user_groupes', $this->User->getUserGroupes());	
			$this->out( 'user_containers', $this->User->getContainers());			
			$this->out( 'doc_colors', 	$this->shop->getDocumentGroupeColors());
				
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

		public function edit()
		{
			/**
			 * Fájl mentése
			 * */
			if( Post::on('saveFile') ) 
			{
				if ($_POST['data']['user_group']) {
					$_POST['data']['user_group_in'] = implode(",",$_POST['data']['user_group']);
					unset($_POST['data']['user_group']);
				} else {
					$_POST['data']['user_group_in'] = NULL;	
				}

				if ($_POST['data']['user_container']) {
					$_POST['data']['user_container_in'] = implode(",",$_POST['data']['user_container']);
					unset($_POST['data']['user_container']);
				} else {
					$_POST['data']['user_container_in'] = NULL;	
				}
				
				$this->db->update(
					'shop_documents',
					$_POST['data'],
					"ID = ".$_POST['id']
				);

				Helper::reload( '/dokumentumok' );	
			}

			$fq = $this->db->query("SELECT * FROM shop_documents WHERE ID = ".$this->gets[2]);
			$f 	= $fq->fetch(\PDO::FETCH_ASSOC);
			$this->out( 'file', $f);
		}

		public function del()
		{
			$fq = $this->db->query("SELECT * FROM shop_documents WHERE ID = ".$this->gets[2]);
			
			if ( $fq->rowCount() == 0 ) {
				Helper::reload( '/dokumentumok' );	
			}

			$f 	= $fq->fetch(\PDO::FETCH_ASSOC);
			$this->out( 'file', $f);

			/**
			 * Fájl törlése
			 * */
			if( Post::on('deleteFile') ) 
			{
				$removed = false;

				if ($f['tipus'] == 'local') 
				{
					$removed = Helper::removeFile( $_POST['file'] );	
				} else 
				{
					$removed = true;
				}
					

				if( $removed ) 
				{
					$this->db->query("DELETE FROM shop_documents WHERE ID = ".$_POST['id']);
				}	

				Helper::reload( '/dokumentumok' );	
			}

			
		}

		public function upload()
		{
			/**
			 * Fájl feltöltése
			 * */
			if( Post::on('uploadFile') ) 
			{
				$filename 	= $_FILES['file']['name'];
				$path 		= 'src/uploaded_files/'.$filename;
				$is_upload 	= false;
				$sorrend 	= (isset($_POST[data][sorrend])) ? $_POST[data][sorrend] : 0;

				$_POST['data']['user_group_in'] = implode(",",$_POST['data']['user_group']);
				unset($_POST['data']['user_group']);

				if ( count($_POST['data']['user_container']) > 0 ) 
				{
					$_POST['data']['user_container_in'] = implode(",",$_POST['data']['user_container']);
					unset($_POST['data']['user_container']);
				} 
				else 
				{
					$_POST['data']['user_container_in'] = NULL;
				}

				

				if ($_POST['source'] == 'upload') 
				{
					$is_upload = true;
				}

				if( $is_upload && empty($filename) ) 			$error = 'Kérjük, hogy válassza ki a feltöltendő fájlt!';
				if( !$error && empty($_POST['data']['cim']) ) 	$error = 'Kérjük, hogy adja meg a feltöltendő fájl elnevezését!';
				if( !$is_upload && empty($_POST['data']['filepath']) ) 	$error = 'Kérjük, hogy adja meg a dokumentum elérési útját (URL)!';

				if( !$error )
				{
					// Fájlfeltöltés
					if ($is_upload) 
					{
						$uploaded = move_uploaded_file( $_FILES['file']['tmp_name'], $path );
						if( $uploaded ) 
						{
							$this->db->insert(
								'shop_documents',
								array(
									'hashname' 				=> md5($filename),
									'filepath' 				=> $path,
									'cim' 					=> addslashes($_POST['data']['cim']),
									'lathato' 				=> (isset($_POST['data']['lathato'])) ? 1 : 0,
									'szaktanacsado_only' 	=> (isset($_POST['data']['szaktanacsado_only'])) ? 1 : 0,
									'sorrend' 				=> $sorrend,
									'groupkey' 				=> $_POST['data']['groupkey'],
									'tipus' 				=> 'local',
									'user_group_in' 		=> $_POST['data']['user_group_in'],
									'user_container_in' 	=> $_POST['data']['user_container_in']
								)
							);	
							
						} else 
						{
							$this->view->msg = Helper::makeAlertMsg( 'pError', 'Fájlfeltöltés sikertelen volt.' );	
						}	
					} 
					else
					// Link feltöltés 
					{
						$path = trim($_POST['data']['filepath']);

						$this->db->insert(
							'shop_documents',
							array(
								'hashname' 				=> md5($_POST['data']['cim'].$path),
								'filepath' 				=> $path,
								'cim' 					=> addslashes($_POST['data']['cim']),
								'lathato' 				=> (isset($_POST['data']['lathato'])) ? 1 : 0,
								'szaktanacsado_only' 	=> (isset($_POST['data']['szaktanacsado_only'])) ? 1 : 0,
								'sorrend' 				=> $sorrend,
								'groupkey' 				=> $_POST['data']['groupkey'],
								'tipus' 				=> 'external',
								'user_group_in' 		=> $_POST['data']['user_group_in']
							)
						);	
					}

					Helper::reload( '/dokumentumok' );
				} 
				else 
				{
					$this->view->msg = Helper::makeAlertMsg( 'pError', $error );
				}
					
			}
		}
		
		function __destruct(){
			// RENDER OUTPUT
				parent::bodyHead();					# HEADER
				$this->view->render(__CLASS__);		# CONTENT
				parent::__destruct();				# FOOTER
		}
	}

?>