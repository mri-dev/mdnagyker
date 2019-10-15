<?
use Applications\CSVParser;
use Applications\EMAGApi;
use ProductManager\Products;

class app extends Controller{
		private $AUTH_USER 	= '';
		private $AUTH_PW 	= '';

		function __construct(){
			parent::__construct();

			$this->authAccess();
		}

		private function authAccess(){
			if($_SERVER[PHP_AUTH_USER] !== $this->AUTH_USER && $_SERVER[PHP_AUTH_PW] !== $this->AUTH_PW){

				/*header("WWW-Authenticate: Basic realm=\"GOLDFISHING\"");
				header("HTTP\ 1.0 401 Unauthorized");

				echo "Sikertelen azonosítás. Illetéktelenek nem férhetnek hozzá a fájlokhoz!";
				exit;*/
			}
		}

		public function emag()
		{

			if ($_GET['key'] != 'xGX39pPGkgPJvmpasIpvUW8gu2QELvY5') {
				header('HTTP/1.0 403 Forbidden');
				exit;
			}

			try {
				$emag = new EMAGApi(array(
					'username' => EMAG_USERNAME,
					'password' => EMAG_PASSWORD
				),
				array('db' => $this->db));

				switch ( $this->view->gets[2] )
				{
					case 'vat':
						$vats = $emag
						->setEndpoint('vat')
						->setAction('read')
						->filter(array(

						))
						->run();

						/* */
						echo '<pre>';
							print_r($vats);
						echo '</pre>';
						/* */
					break;
					// Leszinkronizálás az eMAG-ról, hogy melyik termék szerepel már a marketplace-en
					case 'syncDownProductUsage':
						$pages = $emag
						->setEndpoint('product_offer')
						->setAction('count')
						->filter(array(
							'itemsPerPage' => 100
						))
						->run();

						if ( $pages && !$pages['isError'] )
						{
							$total_page = (int)$pages['results']['noOfPages'];
							$total_items = (int)$pages['results']['noOfItems'];
							$itemperpage = (int)$pages['results']['itemsPerPage'];

							$insert_row = array();

							$pl = 2;
							for($p = 1; $p <= $total_page; $p++)
							{
								$pl--;
								//if($pl < 0) break;
								$page = $emag
								->setEndpoint('product_offer')
								->setAction('read')
								->filter(array(
									'itemsPerPage' => 100,
									'currentPage' => $p
								))
								->run();

								if ( $page && !$page['isError'] && $page['results'] )
								{
									foreach ((array)$page['results'] as $r) {
										if(empty($r['part_number_key'])) continue;
										$insert_row[] = array(
											$r['part_number'],
											$r['sale_price'],
											$r['part_number_key'],
											$r['status'],
											$r['id'],
											NOW
										);
									}
								}

								/* */
								echo '<pre>';
									print_r($page);
								echo '</pre>';
								/* */

								usleep(500);
							}

							if (!empty($insert_row) && false) {
								$dbx = $this->db->multi_insert(
									'emag__products',
									array('cikkszam', 'price', 'PNK', 'status', 'internal_id', 'last_sync'),
									$insert_row,
									array(
										'debug' => $debug,
										'duplicate_keys' => array('PNK','cikkszam', 'price', 'status', 'internal_id', 'last_sync' )
									)
								);
							}
						}
					break;

					// A még nem feltöltött termékek feltöltése a marketplace-re
					case 'uploadProducts':
						$products = new Products(array('db' => $this->db));
						// Még nem feltöltött termékek lekérése
						$unuploaded_products_raw = array();
						$pq = $this->db->query("SELECT
							t.ID, t.cikkszam, t.nev, t.marka, t.profil_kep, t.alapertelmezett_kategoria, t.garancia_honap, t.leiras, t.xml_import_res_id, t.mertekegyseg, t.raktar_keszlet, getTermekAr(t.ID, 0) as ar,
							m.neve as marka_nev
						FROM `shop_termekek` as t
						LEFT OUTER JOIN shop_markak as m ON m.ID = t.marka
						WHERE t.cikkszam = 'TS-A1370F' and t.cikkszam NOT IN (SELECT e.cikkszam FROM emag__products as e) LIMIT 0,50");
						if ($pq->rowCount() != 0)
						{
							$prods = $pq->fetchAll(\PDO::FETCH_ASSOC);
							foreach ((array)$prods as $p) {
								$p['images'] = $products->getProductImages($p['ID']);
								$p['leiras'] = preg_replace('/\{.*\}/','',$p['leiras']);
								$p['leiras'] = str_replace('../../../../src/uploads/',SOURCE.'uploads/',$p['leiras']);
								$unuploaded_products_raw[] = $p;
							}
						}

						$offers = array();
						if ($unuploaded_products_raw)
						{
							foreach ( (array)$unuploaded_products_raw as $d )
							{
								$id = $this->ownproduct_id($d['ID'], '9', 8);
								//$id = $this->ownproduct_id( '90003224' );

								// Images
								$images = array();
								if ( $d['images'] && !empty($d['images']) ) {
									foreach ((array)$d['images'] as $img) {
										$images[] = array(
											'url' => str_replace('//cp.','//www.cp.',DOMAIN).$img,
											'display_type' => ($d['profil_kep'] == $img) ? 1 : 2,
										);
									}
								}

								$offers[] = array(
									'id' => $id,
									'name' => $d['nev'],
									'vendor_category_id' => (int)$d['alapertelmezett_kategoria'],
									'sale_price' => (float)$d['ar']/1.27,
									'part_number' => $d['cikkszam'],
									'description' => $d['leiras'],
									'brand' => $d['marka_nev'],
									'images' => $images,
									'warranty' => (int)$d['garancia_honap'],
									'url' => str_replace('//cp.','//www.',DOMAIN).'termek/'.\Helper::makeSafeUrl($d['nev'],'_-'.$d['ID']),
									'stock' => array(
										'warehouse_id' => 1,
										'value' => (float)$d['raktar_keszlet']
									),
									'status' => 0,
									'vat_id' => 1
								);
							}
						}

						if ($offers) {
							$createrow = $emag
								->setEndpoint('product_offer')
								->setAction('save')
								->filter($offers)
								->run();
						}

						echo '<pre>';
							print_r($createrow);
							//print_r($unuploaded_products_raw);
						echo '</pre>';

					break;

					default:
						die('Nincs ilyen végpont: '.$this->view->gets[2]); exit;
					break;
				}
			} catch (\Exception $e) {
				die($e->getMessage()); exit;
			}

			unset( $emag );
		}

		private function ownproduct_id( $id, $prefix = '9', $target_num = 8 )
		{
			$target = $target_num;

			if ( strlen($id) == '8' and substr($id, 0, 1) == '9' )
			{
				return preg_replace("/^[90+]+/i",'', $id);
			}

			$ct = strlen($id);
			$nullc = $target-$ct-strlen($prefix);
			$myid = $prefix.str_repeat('0', $nullc).$id;

			return $myid;
		}

		public function userimport()
		{
			$lista 		= array();
			$prepared 	= array();

			// Products json betöltése a régi weboldalról
			$wsjson = '/home/webprohu/autoradiokeret.web-pro.hu/admin/src/json/users.json';
			$jsonopen = file_get_contents($wsjson);
			$lista = json_decode($jsonopen, true);

			/* * /
			echo '<pre>';
			print_r($lista);
			exit;
			/* */

			$price_group_obj = array(
				'COM_VIRTUEMART_SHOPPERGROUP_GUEST' => 1,
				'COM_VIRTUEMART_SHOPPERGROUP_DEFAULT' => 1,
				'Kiskereskedelmi ár:' => 1,
				'Viszonteladó' => 2,
				'Nagykereskedő' => 3
			);

			foreach ($lista as $data)
			{
				$email = trim($data['email']);

				if (!array_key_exists($email, $prepared))
				{
					$phone = trim($data['details']['phone_1']);

					if ($phone == '') {
						$phone = trim($data['details']['phone_2']);
					}

					$user_group = 'user';
					if (trim($data['details']['company']) != '') {
						$user_group = 'company';
						$is_company = true;
					} else {
						$is_company = false;
					}

					$price_group = 1;
					if ($data['shopper_group_name'] != '') {
						$price_group = $price_group_obj[$data['shopper_group_name']];
					}


					$inserted = $this->db->query("SELECT 1 FROM felhasznalok WHERE email = '".$email."';")->rowCount();

					if ( $inserted != 0 ) {
						continue;
					}

					$userdata = array(
						'szallitas_nev' 	=> $data['details']['first_name'].' '.$data['details']['last_name'],
						'szallitas_irsz' 	=> $data['details']['zip'],
						'szallitas_state' 	=> NULL,
						'szallitas_city' 	=> $data['details']['city'],
						'szallitas_kozterulet_nev' 	=> addslashes($data['details']['address_1']),
						'szallitas_phone' 	=> $phone,
						'szamlazas_nev' 	=> $data['Vezetéknév'].' '.$data['Keresztnév'],
						'szamlazas_irsz' 	=> $data['Irányítószám'],
						'szamlazas_state' 	=> NULL,
						'szamlazas_city' 	=> $data['Település'],
						'szamlazas_kozterulet_nev' 	=> addslashes($data['Utca, házszám'])
					);

					if ($is_company) {
						$userdata['company_name'] = addslashes($data['details']['company']);
						$userdata['company_address'] = $data['details']['zip'].' '.$data['details']['city'].', '.addslashes($data['details']['address_1']);
						$userdata['company_hq'] = $data['details']['zip'].' '.$data['details']['city'].', '.addslashes($data['details']['address_1']);

						if ($data['details']['bank_account_nr'] != '') {
							$userdata['company_bankszamlaszam'] = $data['details']['bank_account_nr'];
						}
					}

					$prepared[$email] = array(
						'felhasznalok' 		=> array(
							'ID' 			=> $data['id'],
							'email' 		=> $email,
							'nev' 			=> addslashes($data['name']),
							'username' 			=> addslashes($data['username']),
							'jelszo' 		=> $data['password'],
							'aktivalva' 	=> $data['registerDate'],
							'regisztralt' 	=> $data['registerDate'],
							'old_user' 		=> 1,
							'user_group' 	=> $user_group,
							'price_group' => $price_group,
							'newslater' => (int)$data['details']['vm_ccnewsletter']
						),
						'felhasznalo_adatok' => $userdata
					);
				}
			}


			unset($data);
			unset($lista);

			/* */
			if($prepared){
				$inserts_felh 			= array();
				$inserts_felh_head 		= array();
				$inserts_felh_d 		= array();
				$inserts_felh_d_head	= array('fiok_id', 'nev', 'ertek');

				foreach ( $prepared as $email => $data )
				{
					if ( !$inserts_felh_head )
					{
						foreach ($data['felhasznalok'] as $key => $value)
						{
							$inserts_felh_head[] = $key;
						}
					}

					$inserts_felh[] = $data['felhasznalok'];

					foreach ($data['felhasznalo_adatok'] as $key => $value)
					{
						$ins = array(
							'fiok_id' 	=> $data['felhasznalok']['ID'],
							'nev' 		=> $key,
							'ertek' 	=> $value
						);

						$inserts_felh_d[] = $ins;
					}
				}
			}
			/* */

			/* * /
			echo '<pre>';
			print_r($inserts_felh);

			echo '<pre>';
			print_r($inserts_felh_d);
			/* */

			/* * /
			// Updater
			foreach ($inserts_felh_d as $data )
			{
				if( $data['nev'] == 'szallitas_phone') {
					if($data['ertek'] == '') continue;
					$up  = "UPDATE felhasznalo_adatok SET ertek = '".$data['ertek']."' WHERE fiok_id = ".$data['fiok_id'] . " and nev = 'szallitas_phone';";

					//$this->db->query($up);
				}
				//
			}
			/* */

			/* */
			$this->db->multi_insert(
				'felhasznalok',
				$inserts_felh_head,
				$inserts_felh
			);

			$this->db->multi_insert(
				'felhasznalo_adatok',
				$inserts_felh_d_head,
				$inserts_felh_d
			);
			/* */

			echo 'OK';
		}

		function clearadmin(){
			$timestamp 		= date('Y-m-d H:i:s');
			$last_timestamp = $_GET['last_orderhead_timestamp'];

			$show = true;

			if($show){
				header('Content-type: text/html');
				$xml = '<?xml version="2.0" encoding="UTF-8" standalone="yes" ?>';
				$xml .= "<!DOCTYPE ORDERS>\n";
			}

			switch($this->view->gets[2]){
				// Megrendelések
				case 'orders':

				$arg = array();
				$arg[excInProgress] = '1';
				$order = $this->admin->listAllOrder($arg);

				if(!$show){
					print_r($order);
				}

				if($show):
				$xml .= "<ORDERS>\n";
					foreach($order as $o):
					$szamlazasi_adat 	= json_decode($o[szamlazasi_keys],true);
					$szallitasi_adat 	= json_decode($o[szallitasi_keys],true);
					$verified 			= ($o[allapot] == 4) ? 1 : 0;
					$szallitas_ar 		= $o[szallitasi_koltseg];
					$kedvezmeny 		= ($o[kedvezmeny] > 0) ? ($o[kedvezmeny] * -1) : 0;
					$xml .= "<ORDER>\n";
						$xml .= "<ORDERHEAD_CODE>".$o[ID]."</ORDERHEAD_CODE>\n";
						$xml .= "<ORDERHEAD_TIMESTAMP>".$o[idopont]."</ORDERHEAD_TIMESTAMP>\n";

						$xml .= "<ORDERHEAD_PARTNER_CODE>".$o[userID]."</ORDERHEAD_PARTNER_CODE>\n";
						$xml .= "<ORDERHEAD_PARTNER_NAME>".$o[nev]."</ORDERHEAD_PARTNER_NAME>\n";
						$xml .= "<ORDERHEAD_PARTNER_ZIP>".$szamlazasi_adat[irsz]."</ORDERHEAD_PARTNER_ZIP>\n";
						$xml .= "<ORDERHEAD_PARTNER_CITY>".$szamlazasi_adat[city]."</ORDERHEAD_PARTNER_CITY>\n";
						$xml .= "<ORDERHEAD_PARTNER_ADDRESS>".$szamlazasi_adat[uhsz]."</ORDERHEAD_PARTNER_ADDRESS>\n";

						$xml .= "<ORDERHEAD_PARTNER_COUNTRY_INTERNATIONAL_CODE>HU</ORDERHEAD_PARTNER_COUNTRY_INTERNATIONAL_CODE>\n";

						$xml .= "<ORDERHEAD_PARTNER_LANG_CODE>HU</ORDERHEAD_PARTNER_LANG_CODE>\n";

						$xml .= "<ORDERHEAD_PARTNER_OTHER_DATA></ORDERHEAD_PARTNER_OTHER_DATA>\n";

						$xml .= "<ORDERHEAD_PARTNER_MAIL_IS_SAME>0</ORDERHEAD_PARTNER_MAIL_IS_SAME>\n";
						$xml .= "<ORDERHEAD_PARTNER_MAIL_NAME>".$szallitasi_adat[nev]."</ORDERHEAD_PARTNER_MAIL_NAME>\n";
						$xml .= "<ORDERHEAD_PARTNER_MAIL_ZIP>".$szallitasi_adat[irsz]."</ORDERHEAD_PARTNER_MAIL_ZIP>\n";
						$xml .= "<ORDERHEAD_PARTNER_MAIL_CITY>".$szallitasi_adat[city]."</ORDERHEAD_PARTNER_MAIL_CITY>\n";
						$xml .= "<ORDERHEAD_PARTNER_MAIL_ADDRESS>".$szallitasi_adat[uhsz]."</ORDERHEAD_PARTNER_MAIL_ADDRESS>\n";

						$xml .= "<ORDERHEAD_PARTNER_MAIL_COUNTRY_INTERNATIONAL_CODE>HU</ORDERHEAD_PARTNER_MAIL_COUNTRY_INTERNATIONAL_CODE>\n";

						$xml .= "<ORDERHEAD_PAYMENTMETHOD_CODE>".$o[fizetesMod]."</ORDERHEAD_PAYMENTMETHOD_CODE>\n";

						//$xml .= "<ORDERHEAD_DATE_SHIPPED>".date('Y-m-d')."</ORDERHEAD_DATE_SHIPPED>\n";
						//$xml .= "<ORDERHEAD_DATE_PAYMENT_DUE>".date('Y-m-d')."</ORDERHEAD_DATE_PAYMENT_DUE>\n";
						/*
						$xml .= "<ORDERHEAD_NO_VAT>1</ORDERHEAD_NO_VAT>\n";
						$xml .= "<ORDERHEAD_NOVATREASON_CODE>FAD</ORDERHEAD_NOVATREASON_CODE>\n";
						*/

						$xml .= "<ORDERHEAD_CURRENCY_ABBREVIATION>HUF</ORDERHEAD_CURRENCY_ABBREVIATION>\n";

						$xml .= "<ORDERHEAD_SUBJECT>GOLDFISHING.HU - ".$o[azonosito]."</ORDERHEAD_SUBJECT>\n";
						$xml .= "<ORDERHEAD_EXTRA_COMMENT></ORDERHEAD_EXTRA_COMMENT>\n";

						$xml .= "<ORDERHEAD_PAID>0</ORDERHEAD_PAID>\n";

						$xml .= "<ORDERHEAD_VERIFIED>".$verified."</ORDERHEAD_VERIFIED>\n";

						foreach($o[items][data] as $i):
							$ar = number_format($i[egysegAr]/1.27,2,".","");
						$xml .= "<ORDERITEM>\n";
							$xml .= "<ORDERITEM_CODE>".$i[ID]."</ORDERITEM_CODE>\n";

							$xml .= "<ORDERITEM_PRODUCT_CODE>".$i[termekID]."</ORDERITEM_PRODUCT_CODE>\n";
							//$xml .= "<ORDERITEM_STAT_NO>VTSZ 12345</ORDERITEM_STAT_NO>\n";
							//$xml .= "<ORDERITEM_PART_NO>abc123</ORDERITEM_PART_NO>\n";
							$xml .= "<ORDERITEM_NAME>".$i[termekNev]."</ORDERITEM_NAME>\n";
							//$xml .= "<ORDERITEM_NAME_TRANSLATION>Online Product 1</ORDERITEM_NAME_TRANSLATION>\n";
							$xml .= "<ORDERITEM_COMMENT></ORDERITEM_COMMENT>\n";
							$xml .= "<ORDERITEM_COMMENT_TRANSLATION></ORDERITEM_COMMENT_TRANSLATION>\n";
							$xml .= "<ORDERITEM_UNIT>db</ORDERITEM_UNIT>\n";
							$xml .= "<ORDERITEM_UNIT_TRANSLATION>pcs</ORDERITEM_UNIT_TRANSLATION>\n";
							$xml .= "<ORDERITEM_VAT_CODE>1</ORDERITEM_VAT_CODE>\n";
							$xml .= "<ORDERITEM_VAT_PERCENT>27</ORDERITEM_VAT_PERCENT>\n";
							$xml .= "<ORDERITEM_VAT_NAME>27 %</ORDERITEM_VAT_NAME>\n";

							$xml .= "<ORDERITEM_LIST_PRICE>".$ar."</ORDERITEM_LIST_PRICE>\n";
							$xml .= "<ORDERITEM_PRICE>".$ar."</ORDERITEM_PRICE>\n";
							$xml .= "<ORDERITEM_QTY>".$i[me]."</ORDERITEM_QTY>\n";

							$xml .= "<ORDERITEM_DISCOUNT_TYPE>0</ORDERITEM_DISCOUNT_TYPE>\n";
							$xml .= "<ORDERITEM_DISCOUNT_PERCENT></ORDERITEM_DISCOUNT_PERCENT>\n";
						$xml .= "</ORDERITEM>\n";
						endforeach;
						// Szállítási költség
							$szallitas_ar = number_format($szallitas_ar/1.27,2,".","");
							$xml .= "<ORDERITEM>\n";
								$xml .= "<ORDERITEM_CODE>".microtime()."</ORDERITEM_CODE>\n";

								$xml .= "<ORDERITEM_PRODUCT_CODE>100001</ORDERITEM_PRODUCT_CODE>\n";
								//$xml .= "<ORDERITEM_STAT_NO>VTSZ 12345</ORDERITEM_STAT_NO>\n";
								//$xml .= "<ORDERITEM_PART_NO>abc123</ORDERITEM_PART_NO>\n";
								$xml .= "<ORDERITEM_NAME>Szállítási költség</ORDERITEM_NAME>\n";
								//$xml .= "<ORDERITEM_NAME_TRANSLATION>Online Product 1</ORDERITEM_NAME_TRANSLATION>\n";
								$xml .= "<ORDERITEM_COMMENT></ORDERITEM_COMMENT>\n";
								$xml .= "<ORDERITEM_COMMENT_TRANSLATION></ORDERITEM_COMMENT_TRANSLATION>\n";
								$xml .= "<ORDERITEM_UNIT>db</ORDERITEM_UNIT>\n";
								$xml .= "<ORDERITEM_UNIT_TRANSLATION>pcs</ORDERITEM_UNIT_TRANSLATION>\n";
								$xml .= "<ORDERITEM_VAT_CODE>1</ORDERITEM_VAT_CODE>\n";
								$xml .= "<ORDERITEM_VAT_PERCENT>27</ORDERITEM_VAT_PERCENT>\n";
								$xml .= "<ORDERITEM_VAT_NAME>Szolgáltatás</ORDERITEM_VAT_NAME>\n";

								$xml .= "<ORDERITEM_LIST_PRICE>".$szallitas_ar."</ORDERITEM_LIST_PRICE>\n";
								$xml .= "<ORDERITEM_PRICE>".$szallitas_ar."</ORDERITEM_PRICE>\n";
								$xml .= "<ORDERITEM_QTY>1</ORDERITEM_QTY>\n";

								$xml .= "<ORDERITEM_DISCOUNT_TYPE>0</ORDERITEM_DISCOUNT_TYPE>\n";
								$xml .= "<ORDERITEM_DISCOUNT_PERCENT></ORDERITEM_DISCOUNT_PERCENT>\n";
							$xml .= "</ORDERITEM>\n";
						// Kedvezmény
							//$kedvezmeny = number_format($kedvezmeny/1.27,0,".","");
							$xml .= "<ORDERITEM>\n";
								$xml .= "<ORDERITEM_CODE>".microtime()."</ORDERITEM_CODE>\n";

								$xml .= "<ORDERITEM_PRODUCT_CODE>100002</ORDERITEM_PRODUCT_CODE>\n";
								//$xml .= "<ORDERITEM_STAT_NO>VTSZ 12345</ORDERITEM_STAT_NO>\n";
								//$xml .= "<ORDERITEM_PART_NO>abc123</ORDERITEM_PART_NO>\n";
								$xml .= "<ORDERITEM_NAME>Kedvezmény</ORDERITEM_NAME>\n";
								//$xml .= "<ORDERITEM_NAME_TRANSLATION>Online Product 1</ORDERITEM_NAME_TRANSLATION>\n";
								$xml .= "<ORDERITEM_COMMENT></ORDERITEM_COMMENT>\n";
								$xml .= "<ORDERITEM_COMMENT_TRANSLATION></ORDERITEM_COMMENT_TRANSLATION>\n";
								$xml .= "<ORDERITEM_UNIT>db</ORDERITEM_UNIT>\n";
								$xml .= "<ORDERITEM_UNIT_TRANSLATION>pcs</ORDERITEM_UNIT_TRANSLATION>\n";
								$xml .= "<ORDERITEM_VAT_CODE>1</ORDERITEM_VAT_CODE>\n";
								$xml .= "<ORDERITEM_VAT_PERCENT>0</ORDERITEM_VAT_PERCENT>\n";
								$xml .= "<ORDERITEM_VAT_NAME>Kedvezmény</ORDERITEM_VAT_NAME>\n";

								$xml .= "<ORDERITEM_LIST_PRICE>".$kedvezmeny."</ORDERITEM_LIST_PRICE>\n";
								$xml .= "<ORDERITEM_PRICE>".$kedvezmeny."</ORDERITEM_PRICE>\n";
								$xml .= "<ORDERITEM_QTY>1</ORDERITEM_QTY>\n";

								$xml .= "<ORDERITEM_DISCOUNT_TYPE>0</ORDERITEM_DISCOUNT_TYPE>\n";
								$xml .= "<ORDERITEM_DISCOUNT_PERCENT></ORDERITEM_DISCOUNT_PERCENT>\n";
							$xml .= "</ORDERITEM>\n";
					$xml .= "</ORDER>\n";
					endforeach;
					$xml .= "</ORDERS>\n";
				endif;
				break;
			}

			echo $xml;
		}

		function __destruct(){
			// RENDER OUTPUT
				//parent::bodyHead();					# HEADER
				//$this->view->render(__CLASS__);		# CONTENT
				//parent::__destruct();				# FOOTER
		}
	}

?>
