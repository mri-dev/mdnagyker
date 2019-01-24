<? 
use Applications\XMLGenerator;

class xml extends Controller{
		function __construct(){	
			parent::__construct();
		}
		
		public function postapont()
		{
			$orderKey 	= $this->view->gets[2];
			$order 		= $this->AdminUser->getOrderData($orderKey);

			if(!$order[azonosito]){
				return false;
			}

			$szolgaltatas = '';
			$szolgaltatas .= 'KH_PM'; // Postán maradó
			
			$file_name = 'postapont'.'__'.Helper::makeSafeUrl($order[nev], '_-'.$order[azonosito].'-_-'.$order[email].'-__'.time());
						
			$szall = json_decode($order[szallitasi_keys],true);

			$pp = explode('(', $order['postapont']);
			$postapont_nev = trim($pp[0]);
			
			// Utánvételes rendelés
			$utanvet = "0";
			if ($order['fizetesiModID'] == 1) {
			
				$utanvet = (int)0;

				foreach ( $order['items'] as $i ) {
				 	$utanvet += $i['subAr'];
				 } 

				 // Kedvezmény levonása 
				 $utanvet -= $order['kedvezmeny'];

				 // Szállítási költség hozzáadása
				 $utanvet += $order['szallitasi_koltseg'];

				 $szolgaltatas .= ',UVT'; // Utánvétel / Árufizetés
			}



			if ($_GET['type'] == 'data' ) {
				echo '<pre>';
					print_r($order);
				echo '</pre>';
				return true;
			}

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
				'inverz_masolat'
			);

			$fields 	= array();
			$fields[] 	= array(
				$order['ID'],
				(string)$szall['nev'],
				$szall['irsz'],
				$szall['city'],
				'1000',
				'',
				$utanvet,
				$szolgaltatas,
				'Rendelés azonosító: '.$order['azonosito'],
				'',
				$order['email'],
				$szall['phone'],
				'',
				'',
				'',
				'',
				$postapont_nev,
				'0',
				'0',
				'0',
				'',
				''
			);

			$pp = (new XMLGenerator( 'import', 'sor', $head, $fields ))
				->setEncode( 'windows-1250' )
				->build()
				->export( $file_name );
			
		
		}
		
		function __destruct(){
			// RENDER OUTPUT
				//parent::bodyHead();					# HEADER
				//$this->view->render(__CLASS__);		# CONTENT
				//parent::__destruct();				# FOOTER
		}
	}

?>