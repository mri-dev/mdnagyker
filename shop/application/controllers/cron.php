<?
use ShopManager\PreOrders;
use ResourceImporter\CashmanAPI;
use ResourceImporter\ResourceImport;

class cron extends Controller{
		function __construct(){
			parent::__construct();
			parent::$pageTitle = '';

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

    public function clearExpiredPreorders()
    {
      $preorder = new PreOrders(array('db' => $this->db));
      $crm = new CashmanAPI(array('db' => $this->db));

      $preorder->addAPIHandler( $crm );
      $preorder->clearExpiredCRON();
    }

		public function sync()
		{
			$crm = new CashmanAPI(array('db' => $this->db));

			switch ( $this->gets[2] )
			{
				/**
				* Ez a funkció frissíti a cashmanfx termék árakat annál a termékeknél amik már jelen vannak. A termék ár forrás a joomla régi rendszerből letöltött prodcts.json fájlból nyeri ki
				**/
				case 'resyncPriceFromJoomla':

					//autoradiokeret.web-pro.hu/admin/src/json/products.json
					$wsjson = '/home/webprohu/autoradiokeret.web-pro.hu/admin/src/json/products.json';
					$jsonopen = file_get_contents($wsjson);
					$lista = json_decode($jsonopen, true);

					$res = new ResourceImport(array('db' => $this->db));

					$i = 0;
					//echo '<pre>';
					foreach ((array)$lista as $l) {
						$i++;
						if ($i >= 100) {
							//break;
						}

						$prices = array();

						foreach ((array)$l['prices'] as $p) {
							$pricegroup = 1;
							if ($p['name'] == 'Viszonteladó') {
									$pricegroup = 2;
							}
							if ($p['name'] == 'Nagykereskedő') {
									$pricegroup = 3;
							}
							$p['pricegroup'] = $pricegroup;
							$prices[] = $p;
						}
						$l['prices'] = $prices;
						unset($prices);

						$l['res'] = $res->findProductBySKU(trim($l['sku']));
						$l['cmdata'] = $crm->getProduct(trim($l['sku']));

						$net_price = 0;

						if ($l['prices']) {
							foreach ((array)$l['prices'] as $price ) {
								if ($price['pricegroup'] == '1') {
									$net_price = (float)$price['net_price'];
								}
							}
						}

						// Update process
						if ($l['cmdata']) {
							$items = array();
							$item = array(
								'termek_id' => trim($l['cmdata']['id']),
								'cikkszam' => trim($l['res']['cikkszam']),
								'gyarto_cikkszam' => trim($l['res']['cikkszam']),
								'gyartocikkszam' => trim($l['res']['cikkszam']),
								'megnevezes' => trim($l['res']['termek_nev']),
								'vonalkod' => trim($l['res']['cikkszam']),
								'afa' => 27,
								'netto_egysegar' => (float)$net_price,
								'termekcsoport_id' => 1,
								'koltseghely_id' => 2,
								'termek' => 1,
								'mennyisegiegyseg' => trim($l['res']['mennyisegegyseg'])
							);

							if ($l['prices']) {
								foreach ((array)$l['prices'] as $price ) {
									if ($price['pricegroup'] == '1') {
										continue;
									}
									$priceid = $price['pricegroup'];

									$item['afa'.$priceid] = 27;
									$item['netto_egysegar'.$priceid] = (float)$price['net_price'];
								}
							}

							$items[] = $item;

							$l['cmprepareddata'] = $items;
							$ins = $crm->addProduct( $items );
							$l['cmresponse'] = $ins;
						}

						//print_r($l);
					}

				break;
				case 'raktar':
					$crm = new CashmanAPI(array('db' => $this->db));
					$keszlet = $crm->getKeszlet();

					echo '<pre>';

					if (is_array($keszlet)) {
						foreach ((array)$keszlet as $termek ) {
							$this->db->squery("UPDATE xml_temp_products SET termek_keszlet = :keszlet WHERE cikkszam = :cikk", array('keszlet' => (float)$termek['keszlet'], 'cikk' => $termek['cikkszam']));
							$this->db->squery("UPDATE shop_termekek SET raktar_keszlet = :keszlet WHERE cikkszam = :cikk", array('keszlet' => (float)$termek['keszlet'], 'cikk' => $termek['cikkszam']));
						}
					}

					// Raktár termék állapot, ha van készlet
					// Állapot: 2 - raktáron, 3 - külső raktáron
					$this->db->squery("UPDATE shop_termekek SET keszletID = 2 WHERE raktar_keszlet > 0 and keszletID != 2");
					// reset
					$this->db->squery("UPDATE shop_termekek SET keszletID = 3 WHERE raktar_keszlet <= 0 and keszletID != 3");
					// Száll. idő: 8 - 1-3 munkanap.
					$this->db->squery("UPDATE shop_termekek SET szallitasID = 8 WHERE raktar_keszlet > 0 and keszletID != 8");
					// reset
					$this->db->squery("UPDATE shop_termekek SET szallitasID = 9 WHERE raktar_keszlet <= 0 and keszletID != 9");

				break;
				case 'importProgramTechToCashman':

					$res = new ResourceImport(array('db' => $this->db));

					//$raw_raktar = $res->getRaktarXML();

					$items = array();

					if ( count($raw_raktar->sor) != 0 ) {
						foreach ( $raw_raktar->sor as $sor )
						{
							if( (string)$sor->Mértékegység == 'darab' || (string)$sor->Mértékegység == 'db' || (string)$sor->Mértékegység == '' ){
								$mee = 'db';
							} else {
								$mee = (string)$sor->Mértékegység;
							}
							$tid = 0;

							$items[] = array(
								'termek_id' => $tid,
								'cikkszam' => (string)$sor->Cikkszám,
								'megnevezes' =>(string) $sor->Megnevezés,
								'megjegyzes' => (string)$sor->Tétel_megjegyzés,
								'afa' => 27,
								'netto_egysegar' => (float)$sor->Eladási_nettó_egységár,
								'termekcsoport_id' => 1,
								'mennyisegiegyseg' => $mee
							);
						}
					}

					//$return = $crm->addProduct( $items );

					print_r($return);
				break;

				case 'createProduct':
					$items = array();
					$u = uniqid();
					$items[] = array(
						'termek_id' => 0,
						'cikkszam' => 'TESZT'.$u,
						'gyarto_cikkszam' => 'TESZT'.$u,
						'gyartocikkszam' => 'TESZT'.$u,
						'megnevezes' => 'Teszt termék '.$u,
						//'vonalkod' => '1000001000004',
						'megjegyzes' => '',
						'afa' => 27,
						'netto_egysegar' => 1000,
						'termekcsoport_id' => 1,
						'koltseghely_id' => 2,
						'termek' => 1,
						'mennyisegiegyseg' => 'darab'
					);

					$ins = $crm->addProduct( $items );
					echo '<pre>';
					print_r($ins);
					echo '</pre>';
				break;

				case 'clear':
					echo memory_get_usage();
				break;

				case 'syncProducts':
					$res = new ResourceImport(array('db' => $this->db));
					$res->syncTempProducts();
				break;

				case 'importProducts':
					$res = new ResourceImport(array('db' => $this->db));
					$products = $crm->getProducts();
					$prep = $crm->autoImportProducts( 1, (array)$products['data'] );

					//echo $prep;

					/* * /
					echo '<pre>';
					print_r($products);
					echo '</pre>';
					/* */
				break;
				case 'updateProduct':
					$items = array();
					$items[] = array(
						// Kötelezőek
						'termek_id' => 1392,
						'vonalkod' => 15177,
						'megnevezes' => 'Kenwood KDC-X5200BT Bluetooth/MP3/WMA/CD/USB autórádió',
						'termekcsoport_id' => 1,
						'afa' => 27,
						'mennyisegiegyseg' => 'darab',
						'netto_egysegar' => 34567,
						'termek' => 1,
						'cikkszam' => 'KDC-X5200BT',
						// Kiegészítés:
						'minimum' => 25,

					);

					$ins = $crm->addProduct( $items );
					echo '<pre>';
					print_r($ins);
					echo '</pre>';
				break;
				case 'getProduct':
					$data = $crm->getProduct($_GET['id']);
					echo '<pre>';
					print_r($data);
					echo '</pre>';
				break;
			}
		}

		function __destruct(){
			// RENDER OUTPUT
				//parent::bodyHead();					# HEADER
				//$this->view->render(__CLASS__);		# CONTENT
				//parent::__destruct();				# FOOTER
		}
	}

?>
