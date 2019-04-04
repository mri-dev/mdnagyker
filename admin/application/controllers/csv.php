<?
use Applications\CSVGenerator;

class csv extends Controller{
		function __construct(){
			parent::__construct();
		}

		function sprinter_transport(){
			$orderKeyStr 	= $this->view->gets[2];

			$multi_order = array();
			$multi_order = explode( ',', $orderKeyStr );



			$fields = array();
			// Fejrész
			$fields[] = array(
				'c_nev',
				'c_szemely',
				'c_irsz',
				'c_helyseg',
				'c_utca',
				'c_telefon',
				'c_email',
				'c_vevokod',
				'szamlaszam',
				'aruertek',
				'utanvetel',
				'fuvardij',
				'darab',
				'egyseg',
				'suly',
				'tartalom',
				'termekneve',
				'szallido',
				'okmany',
				'szombati',
				'kezbemil',
				'kezbsms',
				'szlafizhat',
				'azon_kelle',
				'felado_osztaly',
				'fizetofel',
				'instrukcio',
				'kezbesites',
				'visszaru',
				'feladobetujel'
			);

			$file_name = Helper::makeSafeUrl('ARENA.HU_ORDERS_SPRINTER_TRANSPORT_'.date('Y_m_d_His'),'');

			foreach ($multi_order as $orderKey) {
				$order 		= $this->AdminUser->getOrderData($orderKey);

				if(!$order[azonosito]){ continue; }

				$szall = json_decode($order[szallitasi_keys],true);

				$utanvetel = $order['utanvatel_osszeg'];

				// Adat
				$fields[] = array(
					$szall[nev],
					$szall[nev],
					$szall[irsz],
					$szall[city],
					$szall[uhsz],
					$szall[phone],
					$order[email],
					'',
					'',
					'',
					$utanvetel, // utánvétel
					'',
					'',
					'',
					'',
					'',
					'sportszer',
					'1 munkanapos',
					'',
					'',
					$order['email'],
					'',
					'',
					'',
					'',
					'',
					$order[comment], //Instrukció, megjegyzés
					'',
					'',
					'AEN'
				);

				/*$fields[] = array(
					$order[azonosito],
					$szall[nev],
					$order[email],
					$szall[phone],
					$szall[irsz],
					$szall[city],
					$szall[uhsz]
				);*/
			}



			CSVGenerator::prepare(
				false,
				$fields,
				$file_name);

			CSVGenerator::run();
		}

		public function pp()
		{
			$orderKeyStr 	= $this->view->gets[2];

			$multi_order = array();
			$multi_order = explode( ',', $orderKeyStr );

			//sorszam
			//nev
			//iranyitoszam
			//telepules
			//tomeg
			//erteknyilvanitas
			//arufizetes
			//szolgaltatasok
			//ugyfeladat1
			//ugyfeladat2
			//email
			//telefon
			//cimzett_kozterulet_nev
			//cimzett_kozterulet_jelleg
			//cimzett_kozterulet_hsz
			//megjegyzes
			//kezbesito_hely
			//meretX
			//meretY
			//meretZ
			//masolatok_szama
			//inverz_masolat
			//hulladek

			$head 	= array(
				'sorszam',
				'nev',
				'iranyitoszam',
				'telepules',
				'tomeg',
				'erteknyilvanitas',
				'arufizetes',
				'szolgaltatasok',
				'ugyfeladat1',
				'ugyfeladat2',
				'email',
				'telefon',
				'cimzett_kozterulet_nev',
				'cimzett_kozterulet_jelleg',
				'cimzett_kozterulet_hsz',
				'megjegyzes',
				'kezbesito_hely',
				'meretX',
				'meretY',
				'meretZ',
				'masolatok_szama',
				'inverz_masolat',
				'hulladek'
			);

			$fields = array();

			$file_name = 'MPL_PostaPont'.'__'.date('Ymd_Hi').'-__'.time();

			foreach ( $multi_order as $orderKey ) {
				$order = $this->AdminUser->getOrderData($orderKey);
				if(!$order[azonosito]){
					continue;
				}

				$szall = json_decode($order[szallitasi_keys],true);
				$szolg = 'KH_PP,ORZ_5,IDO';

				if ($order['utanvatel_osszeg'] != 0) {
					$szolg .= ",UVT";
				}

				$fields[] 	= array(
					$order['ID'],
					(string)$szall['nev'],
					$szall['irsz'],
					$szall['city'],
					(!$order['csomag_suly'] || $order['csomag_suly'] == 0) ? '2000': $order['csomag_suly'],
					'',
					($order['utanvatel_osszeg'] == 0) ? '': $order['utanvatel_osszeg'],
					$szolg,
					'Rendelés azonosító: '.$order['azonosito'],
					'',
					$order['email'],
					$szall['phone'],
					$szall['kozterulet_nev'],
					$szall['kozterulet_jelleg'],
					$szall['hazszam'],
					($szall['comment'] == '') ? '': $szall['comment'],
					$order['postapont'],
					'0',
					'0',
					'0',
					'',
					'',
					'',
					''
				);
			}

			CSVGenerator::prepare(
				$head,
				$fields,
				$file_name);

			CSVGenerator::changeEncoding('windows-1252');

			CSVGenerator::run();
		}

		public function mpl()
		{
			$orderKeyStr 	= $this->view->gets[2];

			$multi_order = array();
			$multi_order = explode( ',', $orderKeyStr );

			//sorszam
			//nev
			//iranyitoszam
			//telepules
			//tomeg
			//erteknyilvanitas
			//arufizetes
			//szolgaltatasok
			//ugyfeladat1
			//ugyfeladat2
			//email
			//telefon
			//cimzett_kozterulet_nev
			//cimzett_kozterulet_jelleg
			//cimzett_kozterulet_hsz
			//megjegyzes
			//kezbesito_hely
			//meretX
			//meretY
			//meretZ
			//masolatok_szama
			//inverz_masolat
			//hulladek

			$head 	= array(
				'sorszam',
				'nev',
				'iranyitoszam',
				'telepules',
				'tomeg',
				'erteknyilvanitas',
				'arufizetes',
				'szolgaltatasok',
				'ugyfeladat1',
				'ugyfeladat2',
				'email',
				'telefon',
				'cimzett_kozterulet_nev',
				'cimzett_kozterulet_jelleg',
				'cimzett_kozterulet_hsz',
				'megjegyzes',
				'meretX',
				'meretY',
				'meretZ',
				'masolatok_szama',
				'inverz_masolat',
				'hulladek'
			);
			$fields = array();

			$file_name = 'MPL_Hazhoz'.'__'.date('Ymd_Hi').'-__'.time();

			foreach ( $multi_order as $orderKey ) {
				$order = $this->AdminUser->getOrderData($orderKey);
				if(!$order[azonosito]){
					return false;
				}

				$szall = json_decode($order[szallitasi_keys],true);
				$szolg = 'KH_HA,ORZ_5,IDO';

				if ($order['utanvatel_osszeg'] != 0) {
					$szolg .= ",UVT";
				}

				$fields[] 	= array(
					$order['ID'],
					(string)$szall['nev'],
					$szall['irsz'],
					$szall['city'],
					(!$order['csomag_suly'] || $order['csomag_suly'] == 0) ? '2000': $order['csomag_suly'],
					'',
					($order['utanvatel_osszeg'] == 0) ? '': $order['utanvatel_osszeg'],
					$szolg,
					'Rendelés azonosító: '.$order['azonosito'],
					'',
					$order['email'],
					$szall['phone'],
					$szall['kozterulet_nev'],
					$szall['kozterulet_jelleg'],
					$szall['hazszam'],
					$szall['comment'],
					'0',
					'0',
					'0',
					'',
					'',
					''
				);
			}

			CSVGenerator::prepare(
				$head,
				$fields,
				$file_name);

			CSVGenerator::changeEncoding('windows-1252');

			CSVGenerator::run();
		}


		function __destruct(){
			// RENDER OUTPUT
				//parent::bodyHead();					# HEADER
				//$this->view->render(__CLASS__);		# CONTENT
				//parent::__destruct();				# FOOTER
		}
	}

?>
