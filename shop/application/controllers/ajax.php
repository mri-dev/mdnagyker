<?
use ShopManager\Cart;
use Applications\Cetelem;
use PortalManager\CasadaShop;
use PopupManager\Creative;
use PopupManager\CreativeScreens;
use ProductManager\Products;
use PortalManager\Vehicles;

class ajax extends Controller{
		function __construct()
		{
			header("Access-Control-Allow-Origin: *");
			parent::__construct();
		}

		public function autocomplete()
		{
			$ret = array();
			header('Content-Type: application/json');

			switch (  $this->gets[2] )
			{
				case 'products':
					$arg = array();
					$arg['limit'] = -1;

					if (isset($_GET['src']) && $_GET['src'] != '') {
						$search = explode(",", trim($_GET['src']));
						if (!empty($search)) {
							$arg['search'] = $search;
						}
					}
					$products = (new Products( array(
						'db' => $this->db,
						'user' => $this->User->get()
					) ))->prepareList( $arg );
					$prodlist = $products->getList();
					foreach ((array)$prodlist as $p) {
						$ret[]  = array(
							'ID' => $p['product_id'],
							'label' => $p['product_nev'],
							'cikkszam' => $p['cikkszam'],
							'img' => $p['profil_kep'],
							'value' => $p['link'],
							'ar' => $p['ar'],
						);
					}
				break;
			}

			echo json_encode($ret);
		}

		function post(){
			extract($_POST);
			$ret = array(
				'success' => 0,
				'msg' => false
			);
			switch($type)
			{
				case 'Helpdesk':
					$helpdesk = $this->Helpdesk;
					switch ( $action ) {
						case 'getCategories':
							$catfilters = array();
							if (!empty($cats)) {
								foreach ((array)$cats as $key => $c) {
									$catfilters[] = (int)$c['ID'];
								}
							}
							$data = $helpdesk->getCategories(true, array(
								'search' => $_POST['search'],
								'in_cat' => $catfilters
							));
							$ret['search'] = $_POST['search'];
							$ret['cats'] = $catfilters;
							$ret['data'] = $data['data'];
							$ret['count'] = $data['count'];

							$this->setSuccess(false, $ret);
						break;
					}
					echo json_encode($ret);
					return;
				break;
				case 'cetelemCalculator':
					$ret['data'] 	= false;
					$ret['show'] 	= false;
					$data 			= array();
					$ret['price'] 	= $price;
					$ret['ownPrice']= $ownPrice;

					// Cetelem API
					$cetelem = new Cetelem(
						$this->view->settings['cetelem_shopcode'],
						$this->view->settings['cetelem_society'],
						$this->view->settings['cetelem_barem'],
						array( 'db' => $this->db )
					);
					$cetelem->sandboxMode( CETELEM_SANDBOX_MODE );
					$data = $cetelem->calc($price, $ownPrice);
					$ret[data] = $data;
				break;

				case 'logPopupClick':

					$this->db->insert(
						'popup_clicks',
						array(
							'creative_id' => $creative,
							'screen_id' => $screen,
							'session_id' => $sessionid,
							'closed' => $closed
						)
					);
				break;
				case 'getPopupScreenVariables':

					$ret['data'] 	= false;
					$ret['show'] 	= false;
					$data 			= array();
					$go 			= true;

					if (isset($url)) {
						$ret['url'] = $url;
					}

					$creative = (new Creative(array('db'=> $this->db)))->loadByURI( $url );
					$creative_settings = $creative->getSettings();

					// Időpont megvizsgálása, hogy mikor látta a creatívot utoljára
					$last_view_delay = $creative->getSessionLastViewAsSec($sessionid);
					$ret['last_view_diff_insec'] = $last_view_delay;

					// Megjelenés korlátozás
					$viewed_numbers = (int)$creative->getSessionViewedNumbers($sessionid);
					$ret['viewed_numbers'] = $viewed_numbers;

					// Időeltelés vizsgálat
					if ( $last_view_delay <= $creative_settings[view_sec_btw] )
					{
						$go = false;
					}

					// Megjelenés vizsgálat
					if ( $viewed_numbers >= $creative_settings[view_max] )
					{
						$go = false;
					}

					if ($go && $creative->hasData())
					{
						$cr = array();

						$cr['id'] 		= $creative->getID();
						$cr['type'] 	= $creative->getType();
						$cr['settings'] = $creative_settings;

						$data['creative'] = $cr;

						$screen = (new CreativeScreens($cr['id'], array('db' => $this->db)))->loadForAction($sessionid);

						$data['screen'] 		= $screen;
						$data['screen_loaded'] 	= $screen[id];

						if (empty($screen)) {
							$data = array();
						}
					}


					if (!empty($data))
					{
						$ret['data'] 	= $data;
						$ret['success'] = 1;
						$ret['show'] 	= true;
					}

				break;

				case 'logPopupScreenshow':
					$ret[post] = $_POST;
					$creative = (new Creative(array('db'=> $this->db)))->load( $creative );
					$creative->logShow( $sessionid, $screen );
				break;

				case 'log':
					switch($mode){
						case 'searching':
							$this->shop->logSearching($val);
						break;
					}
				break;
				case 'cart':
					switch($mode){
						case 'add':
							$err = false;

							if(!$err && $t == '') $err = $this->escape('Hibás termék azonosító, próbálja meg később!',$ret);
							if(!$err && ($m == '' || $m == 0)) $err = $this->escape('Kérjük adja meg hogy hány terméket szeretne a kosárba helyezni!',$ret);

							try{
								$this->shop->addToCart(Helper::getMachineID(), $t, $m, $config);
							}catch(Exception $e){
								$err = $this->escape($e->getMessage(),$ret);
							}

							if(!$err)
							$this->setSuccess('A terméket sikeresen a kosárba helyezte! <a href="/kosar">Tovább a kosárhoz >></a>',$ret);

							echo json_encode($ret);
							return;
						break;
						case 'remove':
							$err = false;
							if(!$err && $id == '') $err = $this->escape('Hibás termék azonosító, próbálja meg később!',$ret);

							try{
								$this->shop->removeFromCart(Helper::getMachineID(), $id);
							}catch(Exception $e){
								$err = $this->escape($e->getMessage(),$ret);
							}

							if(!$err)
							$this->setSuccess('A terméket sikeresen eltávolította a kosárból!',$ret);

							echo json_encode($ret);
							return;
						break;
						case 'addItem':
							$err = false;
							if(!$err && $id == '') $err = $this->escape('Hibás termék azonosító, próbálja meg később!',$ret);

							try{
								$this->shop->addItemToCart(Helper::getMachineID(), $id);
							}catch(Exception $e){
								$err = $this->escape($e->getMessage(),$ret);
							}

							if(!$err)
							$this->setSuccess('Sikeresen megnövelte a termék mennyiségét a kosárban!',$ret);

							echo json_encode($ret);
							return;
						break;
						case 'removeItem':
							$err = false;
							if(!$err && $id == '') $err = $this->escape('Hibás termék azonosító, próbálja meg később!',$ret);

							try{
								$this->shop->removeItemFromCart(Helper::getMachineID(), $id);
							}catch(Exception $e){
								$err = $this->escape($e->getMessage(),$ret);
							}

							if(!$err)
							$this->setSuccess('Sikeresen csökkentette a termék mennyiségét a kosárban!',$ret);

							echo json_encode($ret);
							return;
						break;
					}
				break;
				case 'user':
					switch($mode){
						case 'add':
							$err = false;
							try{
								$re = $this->User->add($_POST);
							}catch(Exception $e){
								$err = $this->escape($e->getMessage(),$ret);
								$ret[errorCode] = $e->getCode();
							}

							if(!$err)
							$this->setSuccess('Regisztráció sikeres! Kellemes vásárlást kívánunk!',$ret);

							echo json_encode($ret);
							return;
						break;
						case 'login':
							$err = false;
							try{
								$re = $this->User->login($_POST[data]);

								if( $re && $re[remember]){
									setcookie('ajx_login_usr', $re[email], time() + 60*60*24*3, '/' );
									setcookie('ajx_login_pw', $re[pw], time() + 60*60*24*3, '/' );
								}else{
									setcookie('ajx_login_usr', null, time() - 3600, '/' );
									setcookie('ajx_login_pw', null , time() -3600, '/' );
								}

							}catch(Exception $e){
								$err = $this->escape($e->getMessage(),$ret);
								$ret[errorCode] = $e->getCode();
							}

							if(!$err)
							$this->setSuccess('Sikeresen bejelentkezett!',$ret);

							echo json_encode($ret);
							return;
						break;
						case 'resetPassword':
							$err = false;
							try{
								$re = $this->User->resetPassword($_POST[data]);
							}catch(Exception $e){
								$err = $this->escape($e->getMessage(),$ret);
								$ret[errorCode] = $e->getCode();
							}

							if(!$err)
							$this->setSuccess('Új jelszó sikeresen generálva!',$ret);

							echo json_encode($ret);
							return;
						break;
					}
				break;
				case 'getTermItem':
					$ret['pass'] = $_POST;
					$id = (int)$_POST['id'];

					$products = new Products( array(
						'db' => $this->db,
						'user' => $this->User->get()
					) );

					$product = $products->get( $id );
					$ret['product'] = $product;

				break;
				case 'modalMessage':
					$err = false;
					$ret['pass'] = $_POST;
					$datas = $_POST['datas'];

					switch ($_POST['modalby'])
					{
						// Ingyenes visszahívás
						case 'recall':
							try {
								$remsg = $this->shop->requestReCall( $datas );
							} catch (\Exception $e) {
								$err = $this->escape( $e->getMessage(), $ret );
							}
						break;
						// Ingyenes ajánlatkérés
						case 'ajanlat':
							try {
								$remsg = $this->shop->requestOffer( $datas );
							} catch (\Exception $e) {
								$err = $this->escape( $e->getMessage(), $ret );
							}
						break;
						// Termék ár kérés
						case 'requesttermprice':
							try {
								$remsg = $this->shop->requestTermprice( $datas );
							} catch (\Exception $e) {
								$err = $this->escape( $e->getMessage(), $ret );
							}
						break;
						// Termék kérdés
						case 'termekkerdes':
							try {
								$remsg = $this->shop->requestTermKerdes( $datas );
							} catch (\Exception $e) {
								$err = $this->escape( $e->getMessage(), $ret );
							}
						break;
					}

					if(!$err) $this->setSuccess( $remsg ,$ret );

				break;
				case 'Preorders':
					$mid = Helper::getMachineID();
					$ret['pass'] = $_POST;
					$err = false;

					switch ($_POST['action']) {
						case 'getNum':
							$num = 0;
							$own = ($_POST['own'] == '1') ? true : false;

							if ($own) {
								$iq = "SELECT count(ID) FROM preorder WHERE gepID = '{$mid}' and valid_to >= now()";
								$getn = (int)$this->db->query( $iq )->fetchColumn();
								$num = $getn;
								$ret['num'] = $num;
							}
						break;
					}
				break;
				case 'productFavorite':
					$mid = Helper::getMachineID();
					$ret['pass'] = $_POST;
					$err = false;
					$tid = (int)$_POST['tid'];

					if( $_POST['action'] == 'add' || $_POST['action'] == 'remove' )
					{
						if (!$tid || empty($tid)) {
							$err = $this->escape( 'Hiba történt! Hiányzik a termék ID-ja a kedvencekhez adásához.', $ret );
						}

						$cat = $this->db->query( $cq = "SELECT
							c.ID
						FROM shop_termek_favorite as c
						WHERE
							c.mid ='{$mid}' and
							c.termekID = $tid" )->fetch(\PDO::FETCH_ASSOC);
						$catn = $this->db->query("SELECT nev FROM shop_termekek WHERE ID = {$tid}")->fetchColumn();
					}

					switch ( $_POST['action'] )
					{
						case 'add':
							if ( (int)$cat['ID'] != 0 ) {
								$err = $this->escape( 'A(z) '.$catn.' termék már a kedvenceihez lett adva korábban.', $ret );
							}

							if (!$err) {
								$this->db->insert(
									'shop_termek_favorite',
									array(
										'mid' => $mid,
										'termekID' => (int)$tid
									)
								);
								$this->setSuccess('Sikeresen hozzáadta a(z) '.$catn.' terméket a kedvenc termékeihez!',$ret);
							}
						break;
						case 'remove':
							if ( (int)$cat['ID'] == 0 ) {
								$err = $this->escape( 'A(z) '.$catn.' termék nem szerepel már a kedvencei között.', $ret );
							}

							if (!$err) {
								$this->db->query( "DELETE FROM shop_termek_favorite WHERE ID = {$cat['ID']}" );
								$this->setSuccess('Sikeresen törölte a(z) '.$catn.' terméket a kedvenc termékeiből!',$ret);
							}
						break;
						case 'get':
							$num = 0;
							$own = ($_POST['own'] == '1') ? true : false;

							if ($own) {
								$getn = (int)$this->db->query("SELECT count(f.ID) FROM shop_termek_favorite as f LEFT OUTER JOIN shop_termekek as t ON t.ID = f.termekID WHERE f.mid = '{$mid}' and t.lathato = 1")->fetchColumn();

								$num = $getn;
								$ret['num'] = $num;

								$getids = $this->db->query("SELECT termekID FROM shop_termek_favorite WHERE mid = '{$mid}'")->fetchAll(\PDO::FETCH_ASSOC);

								$favids = array();
								foreach ((array)$getids as $fid) {
									$favids[] = (int)$fid['termekID'];
								}

								$ret['ids'] = $favids;
							}

						break;
					}
				break;
			}
			echo json_encode($ret);
		}

		private function setSuccess($msg, &$ret){
			$ret[msg] 		= $msg;
			$ret[success] 	= 1;
			return true;
		}
		private function escape($msg, &$ret){
			$ret[msg] 		= $msg;
			$ret[success] 	= 0;
			return true;
		}

		function update () {

			switch ( $this->view->gets[2] ) {
				// Pick Pack Pontok listájának frissítése
				// {DOMAIN}/ajax/update/updatePickPackPont
				/*
				case 'updatePickPackPont':
					$this->model->openLib('PickPackPont',array(
						'database' => $this->model->db,
						'update' => true
					));
				break;
				*/
			}
		}

		function get(){
			extract($_POST);

			switch($type){
				case 'vehicles':
					switch ( $mode ) {
						case 'getList':
							$vehicles = new Vehicles(array('db' => $this->db));
							$vehicles->getTree();
							$ret['data'] = $vehicles->prepareTreeForSelector($vehicles->tree);
							echo json_encode($ret);
						break;
						case 'saveFilter':
							$mid 	= Helper::getMachineID();
							$vehicles = new Vehicles(array('db' => $this->db));
							$vehicles->saveFilter( $mid, $ids );
							echo json_encode($ret);
						break;
						case 'getFilter':
							$mid 	= Helper::getMachineID();
							$vehicles = new Vehicles(array('db' => $this->db));
							$filters = $vehicles->getFilterIDS( $mid );
							$ret['num'] = $filters['num'];
							$ret['ids'] = $filters['ids'];
							echo json_encode($ret);
						break;
					}
				break;
				case 'settings':
					$_POST['key'] = ($_POST['key'] != '') ? (array)$_POST['key'] : array();

					if ( empty($_POST['key']) ) {
						$ret['data'] = $this->view->settings;
					} else {
						$settings = array();

						foreach ( $_POST['key'] as $key ) {
							$settings[$key] = $this->view->settings[$key];
						}

						$ret['data'] = $settings;
					}

					$ret['pass'] = $_POST;
					echo json_encode($ret);
				break;
				case 'irszCityHint':
					$re = array();
					$re['data'] = array();
					$re['pass'] = $_POST;

					$irsz = (int)$_POST['irsz'];

					$q = "SELECT * FROM iranyitoszamok WHERE irsz = :irsz ORDER BY varos ASC";
					$qry = $this->db->squery( $q, array( 'irsz' => $irsz ) );

					if ($qry->rowCount() != 0) {
						$data = $qry->fetchAll(\PDO::FETCH_ASSOC);
						$red = array();
						foreach ( (array)$data as $d ) {
							$red[] = $d;
						}
						unset($data);
						$re['data'] = $red;
						unset($red);
					}

					echo json_encode( $re );
				break;
				case 'cartInfo':
					$mid 	= Helper::getMachineID();
					$cart = new Cart($mid, array( 'db' => $this->db, 'user' => $this->User->get(), 'settings' => $this->view->settings ));
					echo json_encode($cart->get());
				break;

				case 'pickpackpont':
					$this->ppp = $this->model->openLib('PickPackPont',array(
						'database' => $this->model->db
					));

					$this->pickpack->data 	= $this->ppp->getList();
					switch($mode){
						case 'getCities':
							$this->pickpack->varosok 	= $this->ppp->getCities($this->pickpack->data);
							$data = $this->pickpack->varosok[$arg[megye]];
							echo json_encode($data);
						break;
						case 'getPoints':
							$this->pickpack->uzletek 	= $this->ppp->getPoints($this->pickpack->data);
							$data = $this->pickpack->uzletek[$arg[megye]][$arg[varos]];
							echo json_encode($data);
						break;
						case 'getPointData':
							$data = $this->ppp->getPointData($arg[id]);
							echo json_encode($data);
						break;
					}
				break;
			}

			$this->view->render(__CLASS__.'/'.__FUNCTION__.'/'.$type, true);
		}

		function box(){
			extract($_POST);

			switch($type){
				case 'recall':
					$this->view->t = $this->shop->getTermekAdat($tid);
				break;
				case 'askForTermek':
					$this->view->t = $this->shop->getTermekAdat($tid);
				break;
				case 'map':
					$shop = new CasadaShop( (int)$tid, array(
						'db' => $this->db
					));

					$this->out('shop',$shop);
				break;
			}

			$this->view->render(__CLASS__.'/'.__FUNCTION__.'/'.$type, true);
		}

		function __destruct(){
		}
	}

?>
