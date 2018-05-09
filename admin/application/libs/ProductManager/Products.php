<?
namespace ProductManager;

use ShopManager\Categories;
/**
* class Products
* @package ProductManager
* @version 1.0
*/
class Products
{
	const TAG_IMG_NOPRODUCT = 'no-product-img.png';

	private $db = null;
	private $user = null;
	private $products = null;
	private $products_number = 0;
	private $product_limit_per_page = 50;
	private $max_page = 1;
	private $current_page = 1;
	private $avaiable_sizes = array();
	private $qry_str = null;
	private $selected_sizes = array();
	public $item_ids = array();
	public $settings = array();

	public function __construct( $arg = array() ) {
		$this->db = $arg[db];
		$this->user = $arg[user];
		$this->settings = $arg['settings'];

		return $this;
	}

	public function create(Product $product)
	{
		$uploadedProductId = 0;

		$szallitasID 	= $product->getTransportTimeId();
		$keszletID 		= $product->getStatusId();

		// Kötelező mezők ellenőrzése
		if( !$product->getName() ) throw new \Exception('Termék nevének megadása kötelező!');
		if(	!$product->getManufacturerId() ) throw new \Exception('Márka kiválasztása kötelező!');
		if( !$product->getTransportTimeId() ) throw new \Exception('Szállítási időt kötelező kiválasztani!');
		if( !$product->getStatusId() ) throw new \Exception('Állapotot kötelező kiválasztani!');
		if( !$product->getCategoryList() ) throw new \Exception('Termék kategória kiválasztása kötelező!');
		if( !$product->getPrice() ) throw new \Exception('Termék árát kötelező megadni!');

		if( true ){
			$cikkszam 		= $product->getItemNumber();
			$marka 			= $product->getManufacturerId();
			$lathato 		= $product->isVisible();
			$pickpackszallitas 	= ( !$product->isAllowToPickPackPont() ) ? 0 : 1;
			$no_cetelem 	= ( $product->isAllowCetelem() ) ? 0 : 1;
			$akcios 		= ( !$product->isDiscounted() ) ? 0 : 1;
			$netto_ar 		= $product->getPrice( 'netto' );
			$brutto_ar 		= $product->getPrice( 'brutto' );
			$akcios_n_ar 	= ( $product->isDiscounted() ) ? $product->getPrice( 'netto', 'akcios' ) : 0;
			$akcios_b_ar 	= ( $product->isDiscounted() ) ? $product->getPrice( 'brutto', 'akcios' ) : 0;
			$argep 	  		= ( !$product->isListedInArgep() ) ? 0 : 1;
			$arukereso 		= ( !$product->isListedInArukereso()) ? 0 : 1;
			$ujdonsag 		= ( !$product->isNewest()) ? 0 : 1;
			$kiemelt 		= ( !$product->getVariable('kiemelt')) ? 0 : 1;
			$ajanlorendszer_kiemelt = ( !$product->getVariable('ajanlorendszer_kiemelt')) ? 0 : 1;
			$show_stock 	= ( !$product->getVariable('show_stock')) ? 0 : 1;

			$nev 			= addslashes( $product->getName() );
			$raktar_keszlet = $product->getStockNumber();
			$leiras 		= addslashes( $product->getDescription() );
			$bankihitel_leiras	= (!$product->getVariable('bankihitel_leiras')) ? NULL : $product->getVariable('bankihitel_leiras');

			$letoltesek		= addslashes( $product->getDownloads() );
			$rovid_leiras	= addslashes( $product->getShortDescription() );
			$marketing_leiras 	= (!$product->getVariable('marketing_leiras')) ? NULL : $product->getVariable('marketing_leiras');
			$garancia 		= ( $product->getGuarantee() ) ?: NULL;
			$link_list		= $product->getLinks();
			$szin 			= $product->getVariable('szin');
			$meret 			= $product->getVariable('meret');
			$kapcsolatok 	= $product->getVariable('connects');
			$raktar_articleid 	= $product->getVariable('raktar_articleid');
			$raktar_variantid 	= $product->getVariable('raktar_variantid');
			$linkek 		= '';
			$alapertelmezett_kategoria	= (!$product->getVariable('alapertelmezett_kategoria')) ? NULL : $product->getVariable('alapertelmezett_kategoria');
			$csoport_kategoria	= (!$product->getVariable('csoport_kategoria')) ? NULL : $product->getVariable('csoport_kategoria');
			$ajandek 			= (!$product->getVariable('ajandek')) ? NULL : $product->getVariable('ajandek');
			$termek_site_url 	= (!$product->getVariable('termek_site_url')) ? NULL : $product->getVariable('termek_site_url');
			$tudastar_url 	= (!$product->getVariable('tudastar_url')) ? NULL : $product->getVariable('tudastar_url');
			$referer_price_discount 	= (!$product->getVariable('referer_price_discount')) ? 0 : $product->getVariable('referer_price_discount');
			$sorrend 			= (!$product->getVariable('sorrend')) ? 0 : $product->getVariable('sorrend');

			// Csatolt hivatkozások előkészítése
			if( $link_list ) {
				foreach( $link_list as $lnev => $url ){
					if( $lnev != '' && $url != '' ){
						$linkek .= trim($lnev)."==>".trim($url)."||";
					}
				}
				$linkek = rtrim( $linkek, '||' );
			}

			$this->db->insert(
				'shop_termekek',
				array(
					'cikkszam' => $cikkszam,
					'marka' => $marka,
					'nev' => $nev,
					'leiras' => $leiras,
					'bankihitel_leiras' => $bankihitel_leiras,
					'marketing_leiras' => $marketing_leiras,
					'letoltesek' => $letoltesek,
					'rovid_leiras' => $rovid_leiras,
					'netto_ar' => $netto_ar,
					'brutto_ar' => $brutto_ar,
					'lathato' => $lathato,
					'pickpackszallitas' => $pickpackszallitas,
					'no_cetelem' => $no_cetelem,
					'akcios' => $akcios,
					'akcios_netto_ar' => $akcios_n_ar,
					'akcios_brutto_ar' => $akcios_b_ar,
					'szallitasID' => $szallitasID,
					'keszletID' => $keszletID,
					'ujdonsag' => $ujdonsag,
					'argep' => $argep,
					'arukereso' => $arukereso,
					'garancia_honap' => $garancia,
					'linkek' => $linkek,
					'raktar_keszlet' => $raktar_keszlet,
					'raktar_articleid' => $raktar_articleid,
					'raktar_variantid' => $raktar_variantid,
					'szin' => $szin,
					'meret' => $meret,
					'csoport_kategoria' => $csoport_kategoria,
					'ajandek' => $ajandek,
					'termek_site_url' => $termek_site_url,
					'tudastar_url' => $tudastar_url,
					'lathato' => $lathato,
					'kiemelt'=> $kiemelt,
					'ajanlorendszer_kiemelt' => $ajanlorendszer_kiemelt,
					'referer_price_discount' => $referer_price_discount,
					'sorrend' => $sorrend,
					'show_stock' => $show_stock
				)
			);

			$uploadedProductId = $this->db->lastInsertId();

			// Termékek összekőtése
			if( is_array($kapcsolatok) && count($kapcsolatok) > 0 ) {
				foreach ($kapcsolatok as $tid) {
					$this->connectProducts( $uploadedProductId, $tid );
				}
			}

			// Képfeltöltés
			if($_FILES[img][name][0] != ''){
				$dir 	= 'p'.$uploadedProductId;
				$idir 	= 'src/products/'.$dir;

				// Termékmappa létrehozás / Permission
				if( !file_exists($idir) ){
					mkdir( $idir, 0777, true );
				}

				// Feltöltése
				$mt 		= explode(" ",str_replace(".","",microtime()));
				$imgName 	= \PortalManager\Formater::makeSafeUrl( $this->getManufacturName($marka).'-'.$nev.'__'.date('YmdHis').$mt[0] );
				$img 		= \Images::upload(array(
					'src' => 'img',
					'upDir' => $idir,
					'noRoot' => true,
					'fileName' => $imgName,
					'maxFileSize' => 1024
				));

				/*
				$upDir 		= str_replace(array('../img/'),array(''),$img[dir]);
				$upProfil 	= str_replace(array('../img/'),array(''),$img[file]);
				*/

				$upDir 		= $img[dir];
				$upProfil 	= $img[file];

				$this->db->update(
					'shop_termekek',
					array(
						'kep_mappa' => $upDir,
						'profil_kep' => $upProfil
					),
					"ID = $uploadedProductId"
				);

				foreach( $img['allUploadedFiles'] as $kep ){
					$this->addImageToProduct( $uploadedProductId, $kep );
				}
			}


			// Kategóriákba sorolás
			if ( $product->getCategoryList() ) {
			 	$this->doCategoryConnect( $uploadedProductId, $product->getCategoryList() );
			}

			// Regiszterek
			if($pickpackszallitas == 1)
				setcookie("cr_pickpackszallitas","on",time()+3600*30,"/termekek");
			else
				setcookie("cr_pickpackszallitas",false,time()-3600,"/termekek");


			if($akcios == 1)
				setcookie("cr_akcios","on",time()+3600*30,"/termekek");
			else
				setcookie("cr_akcios",false,time()-3600,"/termekek");

			if($szuper_akcios == 1)
				setcookie("cr_szuper_akcios","on",time()+3600*30,"/termekek");
			else
				setcookie("cr_szuper_akcios",false,time()-3600,"/termekek");

			if($ujdonsag == 1)
				setcookie("cr_ujdonsag","on",time()+3600*30,"/termekek");
			else
				setcookie("cr_ujdonsag",false,time()-3600,"/termekek");

			if($argep == 1)
				setcookie("cr_argep","on",time()+3600*30,"/termekek");
			else
				setcookie("cr_argep",false,time()-3600,"/termekek");

			if($arukereso == 1)
				setcookie("cr_arukereso","on",time()+3600*30,"/termekek");
			else
				setcookie("cr_arukereso",false,time()-3600,"/termekek");

			if($szallitasID)
				setcookie("cr_szallitasID",$szallitasID,time()+3600*30,"/termekek");
			else
				setcookie("cr_szallitasID",false,time()-3600,"/termekek");

			if($keszletID)
				setcookie("cr_keszletID",$keszletID,time()+3600*30,"/termekek");
			else
				setcookie("cr_keszletID",false,time()-3600,"/termekek");

			return 'Termék sikeresen létrehozva! ';
		}

		return $product;
	}

	public function save(Product $product)
	{
		if ( !$product->getId() ) {
			 throw new \Exception('Mentés sikertelen! Nem találjuk a termék azonosítóját!');
		}

		$szallitasID 	= $product->getTransportTimeId();
		$keszletID 		= $product->getStatusId();

		// Kötelező mezők ellenőrzése
		if( !$product->getName() ) throw new \Exception('Termék nevének megadása kötelező!');
		if(	!$product->getManufacturerId() ) throw new \Exception('Márka kiválasztása kötelező!');
		if( !$product->getTransportTimeId() ) throw new \Exception('Szállítási időt kötelező kiválasztani!');
		if( !$product->getStatusId() ) throw new \Exception('Állapotot kötelező kiválasztani!');
		if( !$product->getCategoryList() ) throw new \Exception('Termék kategória kiválasztása kötelező!');

		if( true ){
			$cikkszam 		= $product->getItemNumber();
			$marka 			= $product->getManufacturerId();
			$lathato 		= $product->isVisible();
			$pickpackszallitas 	= ( !$product->isAllowToPickPackPont() ) ? 0 : 1;
			$no_cetelem 	= ( $product->isAllowCetelem() ) ? 0 : 1;
			$akcios 		= ( !$product->isDiscounted() ) ? 0 : 1;
			$netto_ar 		= $product->getPrice( 'netto' );
			$brutto_ar 		= $product->getPrice( 'brutto' );
			$akcios_n_ar 	= ( $product->isDiscounted() ) ? $product->getPrice( 'netto', 'akcios' ) : 0;
			$akcios_b_ar 	= ( $product->isDiscounted() ) ? $product->getPrice( 'brutto', 'akcios' ) : 0;
			$argep 	  		= ( !$product->isListedInArgep() ) ? 0 : 1;
			$arukereso 		= ( !$product->isListedInArukereso()) ? 0 : 1;
			$ujdonsag 		= ( !$product->isNewest()) ? 0 : 1;
			$kiemelt 		= ( !$product->getVariable('kiemelt')) ? 0 : 1;
			$ajanlorendszer_kiemelt 		= ( !$product->getVariable('ajanlorendszer_kiemelt')) ? 0 : 1;
			$show_stock 	= ( !$product->getVariable('show_stock')) ? 0 : 1;

			$nev 			= addslashes( $product->getName() );
			$raktar_keszlet = $product->getStockNumber();
			$leiras 		= addslashes( $product->getDescription() );
			$bankihitel_leiras	= (!$product->getVariable('bankihitel_leiras')) ? NULL : $product->getVariable('bankihitel_leiras');
			$rovid_leiras	= addslashes( $product->getShortDescription() );
			$marketing_leiras 	= (!$product->getVariable('marketing_leiras')) ? NULL : $product->getVariable('marketing_leiras');
			$letoltesek		= addslashes( $product->getDownloads() );
			$garancia 		= ( $product->getGuarantee() ) ?: NULL;
			$link_list		= $product->getLinks();
			$meta_title 	= addslashes( $product->getMetaTitle() );
			$meta_desc 	 	= addslashes( $product->getMetaDesc() );
			$nev 			= addslashes( $product->getName() );
			$linkek 		= '';
			$kulcsszavak	= (!$product->getVariable('kulcsszavak')) ? NULL : $product->getVariable('kulcsszavak');
			$raktar_articleid	= (!$product->getVariable('raktar_articleid')) ? NULL : $product->getVariable('raktar_articleid');
			$raktar_variantid	= (!$product->getVariable('raktar_variantid')) ? NULL : $product->getVariable('raktar_variantid');
			$raktar_supplierid	= (!$product->getVariable('raktar_supplierid')) ? NULL : $product->getVariable('raktar_supplierid');
			$raktar_number	= (!$product->getVariable('raktar_number')) ? NULL : $product->getVariable('raktar_number');
			$alapertelmezett_kategoria	= (!$product->getVariable('alapertelmezett_kategoria')) ? NULL : $product->getVariable('alapertelmezett_kategoria');
			$csoport_kategoria	= (!$product->getVariable('csoport_kategoria')) ? NULL : $product->getVariable('csoport_kategoria');
			$ajandek 			= (!$product->getVariable('ajandek')) ? NULL : $product->getVariable('ajandek');
			$termek_site_url 	= (!$product->getVariable('termek_site_url')) ? NULL : $product->getVariable('termek_site_url');
			$tudastar_url 	= (!$product->getVariable('tudastar_url')) ? NULL : $product->getVariable('tudastar_url');
			$referer_price_discount 	= (!$product->getVariable('referer_price_discount')) ? 0 : $product->getVariable('referer_price_discount');
			$sorrend 			= (!$product->getVariable('sorrend')) ? 0 : $product->getVariable('sorrend');

			// Csatolt hivatkozások előkészítése
			if( $link_list ) {
				foreach( $link_list as $lnev => $url ){
					if( $lnev != '' && $url != '' ){
						$linkek .= trim($lnev)."==>".trim($url)."||";
					}
				}
				$linkek = rtrim( $linkek, '||' );
			}

			$meret 	= ( !$product->getVariable( 'meret' ) ) ? NULL : $product->getVariable( 'meret' );
			$szin 	= ( !$product->getVariable( 'szin' ) ) ? NULL : $product->getVariable( 'szin' );

			$this->db->update(
				'shop_termekek',
				array(
					'cikkszam' => $cikkszam,
					'marka' => $marka,
					'nev' => $nev,
					'leiras' => $leiras,
					'bankihitel_leiras' => $bankihitel_leiras,
					'rovid_leiras' => $rovid_leiras,
					'meta_title' => $meta_title,
					'meta_desc' => $meta_desc,
					'marketing_leiras' => $marketing_leiras,
					'letoltesek' => $letoltesek,
					'lathato' => $lathato,
					'pickpackszallitas' => $pickpackszallitas,
					'no_cetelem' => $no_cetelem,
					'akcios' => $akcios,
					'szallitasID' => $szallitasID,
					'keszletID' => $keszletID,
					'ujdonsag' => $ujdonsag,
					'argep' => $argep,
					'arukereso' => $arukereso,
					'garancia_honap' => $garancia,
					'kulcsszavak' => $kulcsszavak,
					'linkek' => $linkek,
					'raktar_keszlet' => $raktar_keszlet,
					'meret' => $meret,
					'szin' => $szin,
					'fotermek' => ($product->isMainProduct() ? 1 : 0),
					'raktar_articleid' => $raktar_articleid,
					'raktar_variantid' => $raktar_variantid,
					'raktar_supplierid' => $raktar_supplierid,
					'raktar_number' => $raktar_number,
					'alapertelmezett_kategoria' => $alapertelmezett_kategoria,
					'csoport_kategoria' => $csoport_kategoria,
					'ajandek' => $ajandek,
					'kiemelt'=> $kiemelt,
					'ajanlorendszer_kiemelt' => $ajanlorendszer_kiemelt,
					'termek_site_url' => $termek_site_url,
					'tudastar_url' => $tudastar_url,
					'referer_price_discount' => $referer_price_discount,
					'sorrend' => $sorrend,
					'show_stock' => $show_stock
				),
				sprintf("ID = %d", $product->getId())
			);

			// Kategóriákba sorolás
			if ( $product->getCategoryList() ) {
			 	$this->doCategoryConnect( $product->getId(), $product->getCategoryList() );
			}

			// Árváltozások módosítása
				// Sima ár változtatása
				if( $product->getVariable('ar_by') ) {
	 				if( $product->getVariable('ar_by') == 'netto' ){
						$brutto_ar = $netto_ar * 1.27;
					}else if( $product->getVariable('ar_by') == 'brutto' ){
						$netto_ar = $brutto_ar/1.27;
					}else{
						$donot_ar = true;
					}
				}

				// Akciós ár változtatás
				if( $product->getVariable('akcios_ar_by') ) {
					if( $product->getVariable('akcios_ar_by') == 'netto' ){
						$akcios_b_ar = $akcios_n_ar * 1.27;
					}else if( $product->getVariable('akcios_ar_by') == 'brutto' ){
						$akcios_n_ar = $akcios_b_ar/1.27;
					}else{
						$donot_akcios_ar = true;
					}
				}

				if(!$donot_ar){
					$this->db->update(
						'shop_termekek',
						array(
							'netto_ar' 	=> $netto_ar,
							'brutto_ar' => $brutto_ar
						),
						sprintf("ID = %d", $product->getId())
					);
				}

				if(!$donot_akcios_ar){
					if ( $akcios_n_ar == 0 || $akcios_b_ar == 0 ) {
						$this->db->update(
							'shop_termekek',
							array(
								'akcios' => 0
							),
							sprintf("ID = %d", $product->getId())
						);
					}

					$this->db->update(
						'shop_termekek',
						array(
							'akcios_netto_ar' 	=> $akcios_n_ar,
							'akcios_brutto_ar' => $akcios_b_ar
						),
						sprintf("ID = %d", $product->getId())
					);
				};

			return 'Termék adatok sikeresen módosítva! ';
		}

		return $product;
	}

	public function getLoadedIDS()
	{
		if ( !$this->products ) {
			return array();
		}

		return $this->item_ids;
	}

	public function connectProducts( $id1, $id2 )
	{
		$c1 = $this->db->query("SELECT 1 FROM shop_termek_ajanlo_xref WHERE base_id = $id1 and target_id = $id2;");
		if ( $c1->rowCount() == 0 ) {
			$this->db->insert(
				"shop_termek_ajanlo_xref",
				array(
					'base_id' => $id1,
					'target_id' => $id2
				)
			);
		}
	}

	public function disconnectProducts( $id1, $id2 )
	{
		$this->db->query("DELETE FROM shop_termek_ajanlo_xref WHERE (base_id = $id1 and target_id = $id2)");
	}

	public function getLiveviewedList( $mID, $limit = 5, $arg = array() )
	{
		$data = array();

		$q = "SELECT
			v.*,
			t.nev as product_nev,
			t.ID as product_id,
			t.profil_kep,
			t.csoport_kategoria
		FROM `shop_utoljaraLatottTermek` as v
		LEFT OUTER JOIN shop_termekek as t ON t.ID = v.termekID
		LEFT OUTER JOIN shop_markak as m ON m.ID = t.marka
		WHERE v.mID != '$mID' and t.ID IS NOT NULL and t.lathato = 1
		GROUP BY t.ID
		ORDER BY v.idopont DESC
		LIMIT 0,$limit";

		$arg[multi] = '1';
		extract($this->db->q($q,$arg));

		$bdata = array();

		foreach($data as $d){
			$kep = $d['profil_kep'];
			$d['profil_kep'] 		=  \PortalManager\Formater::productImage( $kep, false, self::TAG_IMG_NOPRODUCT );
			$d['profil_kep_small'] 	=  \PortalManager\Formater::productImage( $kep, 75, self::TAG_IMG_NOPRODUCT );
			$d['link'] = DOMAIN.'termek/'.\PortalManager\Formater::makeSafeUrl( $d['product_nev'], '_-'.$d['product_id'] );

			$bdata[]	 			= $d;
		}

		return $bdata;
	}

	public function getLastviewedList( $mID, $limit = 5, $arg = array() )
	{
		$data = array();

		$q = "SELECT
			v.*,
			t.nev as product_nev,
			t.ID as product_id,
			t.profil_kep,
			t.csoport_kategoria
		FROM `shop_utoljaraLatottTermek` as v
		LEFT OUTER JOIN shop_termekek as t ON t.ID = v.termekID
		LEFT OUTER JOIN shop_markak as m ON m.ID = t.marka
		WHERE v.mID = '$mID' and t.ID IS NOT NULL and t.lathato = 1
		ORDER BY v.idopont DESC
		LIMIT 0,$limit";

		$arg[multi] = '1';
		extract($this->db->q($q,$arg));

		$bdata = array();

		foreach($data as $d){
			$kep = $d['profil_kep'];
			$d['profil_kep'] 		=  \PortalManager\Formater::productImage( $kep, false, self::TAG_IMG_NOPRODUCT );
			$d['profil_kep_small'] 	=  \PortalManager\Formater::productImage( $kep, 75, self::TAG_IMG_NOPRODUCT );
			$d['link'] = DOMAIN.'termek/'.\PortalManager\Formater::makeSafeUrl( $d['product_nev'], '_-'.$d['product_id'] );

			$bdata[]	 			= $d;
		}

		return $bdata;
	}

	public function prepareList( $arg = array() )
	{
		$this->products = array();
		$this->products_number = 0;

		if ( $arg['limit'] ) {
			if( $arg['limit'] > 0 ) {
				$this->product_limit_per_page = ( is_numeric($this->product_limit_per_page) && $this->product_limit_per_page > 0) ? (int)$arg['limit'] : $this->product_limit_per_page;
			} else if( $arg['limit'] == -1 ){
				$this->product_limit_per_page = 999999999999;
			}
		}

		$admin_listing = ( $arg['admin'] ) ? true : false;

		/*==========  Lekérdezés  ==========*/
		$qry = "
		SELECT SQL_CALC_FOUND_ROWS
			p.ID as product_id,
			p.nev as product_nev,
			p.cikkszam,
			p.kulcsszavak,
			p.pickpackszallitas,
			p.no_cetelem,
			p.akcios,
			p.ujdonsag,
			p.netto_ar,
			p.marketing_leiras,
			p.brutto_ar,
			p.akcios_netto_ar,
			p.akcios_brutto_ar,
			p.egyedi_ar,
			p.marka as marka_id,
			p.szallitasID,
			p.keszletID,
			p.raktar_keszlet,
			p.raktar_articleid,
			p.profil_kep,
			p.kep_mappa,
			p.lathato,
			p.kiemelt,
			p.without_price,
			p.szin,
			p.csoport_kategoria,
			p.ajanlatunk,
			p.meret,
			p.garancia_honap,
			p.termek_site_url,
			p.ajandek,
			p.rovid_leiras,
			GROUP_CONCAT(CONCAT('p_',pa.parameterID,':',pa.ertek)) as paramErtek,
			IF(p.egyedi_ar IS NOT NULL,
				p.egyedi_ar,
				getTermekAr(p.marka, IF(p.akcios,p.akcios_brutto_ar,p.brutto_ar))
			) as ar,
			p.fotermek,
			(SELECT GROUP_CONCAT(kategoria_id) FROM shop_termek_in_kategoria WHERE termekID = p.ID ) as in_cat,
			(SELECT neve FROM shop_termek_kategoriak WHERE ID = p.alapertelmezett_kategoria ) as alap_kategoria";

		if ( isset($arg['collectby']) && $arg['collectby'] == 'top' ) {
			$qry .= " ,(SELECT sum(me) FROM `stat_nezettseg_termek` WHERE termekID = p.ID and datediff(now(),datum) < 60) as v";
		}

		$qry .= " FROM
		shop_termekek as p
		LEFT OUTER JOIN shop_termek_parameter as pa ON pa.termekID = p.ID
		WHERE 1 = 1
		";

		$whr = '';
		$size_whr = '';
		$add = '';

		if (!$admin_listing) {
			$add = " and p.lathato = 1 and p.profil_kep IS NOT NULL ";
			$whr .= $add;
			$size_whr .= $add;

			if(!empty($arg['meret']) && $arg['meret'][0] != ''){
				$add = " and p.meret IN ('".trim(implode("','",$arg['meret']))."') ";
				$whr .= $add;
			} else {
				/* $add = " and p.fotermek = 1 ";
				$whr .= $add;*/
			}

		}

		/**
		* WHERE
		**/
		// Favorite
		if ( isset($arg['favorite']) && $arg['favorite'] === true )
		{
			$mid = \Helper::getMachineID();
			$getfavids = $this->db->query("SELECT termekID FROM shop_termek_favorite WHERE mid = '{$mid}'")->fetchAll(\PDO::FETCH_ASSOC);

			$favids = array();
			foreach ((array)$getfavids as $fid) {
				$favids[] = (int)$fid['termekID'];
			}

			if (empty($favids)) {
				$favids[] = 0;
			}

			$add = " and p.ID IN (".implode(",",$favids).") ";
			$whr .= $add;
			$size_whr .= $add;
		}

		if ( $arg['in_ID'] ) {
			$add = " and p.ID IN (".implode(",",$arg['in_ID']).") ";
			$whr .= $add;
			$size_whr .= $add;
		}

		if ( $arg['kiemelt'] ) {
			$add = " and p.kiemelt = 1 ";
			$whr .= $add;
			$size_whr .= $add;
		}

		if ( $arg['akcios'] === true ) {
			$add = " and p.akcios = 1 ";
			$whr .= $add;
			$size_whr .= $add;
		}

		if ( $arg['in_cat'] ) {
			$add = " and FIND_IN_SET(".$arg['in_cat'].",(SELECT GROUP_CONCAT(kategoria_id) FROM shop_termek_in_kategoria WHERE termekID = p.ID )) ";
			$whr .= $add;
			$size_whr .= $add;
		}

		if ( $arg['csoport_kategoria'] ) {
			$add = " and p.csoport_kategoria = '{$arg[csoport_kategoria]}' ";
			$whr .= $add;
			$size_whr .= $add;
		}

		// Keresés
		if ( $arg['search'] && is_array($arg['search']) && !empty($arg['search']) ) {
			$add = " and (";
				foreach ($arg['search'] as $src ) {
					$add .= "(p.nev LIKE '%".$src."%' or p.kulcsszavak LIKE '%".$src."%' or p.rovid_leiras LIKE '%".$src."%') and ";
				}
				$add = rtrim($add," and ");
			$add .= ") ";

			$whr .= $add;
			$size_whr .= $add;
		}

		if ( !empty($arg['meret']) && $arg['meret'][0] != '' ) {
			$this->selected_sizes = $arg['meret'];

			$add = " and (";
			foreach ( $arg['meret'] as $size ) {
				if( $size == "" ) continue;

				$add .= "(FIND_IN_SET('".$size."' ,(SELECT GROUP_CONCAT(t.meret) FROM shop_termek_kapcsolatok as tk LEFT OUTER JOIN shop_termekek as t ON t.ID = tk.termek_to WHERE tk.termek_from = p.ID)) or '$size' = p.meret) or";
			}
			$add = rtrim($add," or");
			$add .= ")";
			$whr .= $add;
		}

		// Excepts
		if( $arg['except'] ) {
			foreach ($arg['except'] as $key => $value) {
				if( is_array($value) ) {
					$value_set = false;

					if( count($value) > 0 ) {
						$value_set = implode(',', $value);
					}

					if($value_set){
						$add = " and p.".$key." NOT IN (".$value_set.") ";
						$whr .= $add;
						$size_whr .= $add;
					}
				} else {
					$add = " and p.".$key." != '".$value."' ";
					$whr .= $add;
					$size_whr .= $add;
				}
			}
		}

		if(count($arg['filters']) > 0){
			foreach($arg['filters'] as $key => $v){
				switch($key)
				{
					case 'ID':
						if( is_array($v) ) {
							$value_set = false;

							if( count($v) > 0 ) {
								$value_set = implode(',', $v);
							}

							if($value_set){
								$add = " and p.".$key." IN (".$value_set.") ";
								$whr .= $add;
								$size_whr .= $add;
							}
						} else {
							$add = " and p.".$key." LIKE '".$v."%' ";
							$whr .= $add;
							$size_whr .= $add;

						}

					break;
					case 'cikkszam':
						$add = " and p.".$key." LIKE '".$v."%' ";
						$whr .= $add;
						$size_whr .= $add;
					break;
					case 'nev':
						$add = " and p.".$key." LIKE '%".$v."%' ";
						$whr .= $add;
						$size_whr .= $add;
					break;
					default:
						if( is_array($v) ) {
							$value_set = false;

							if( count($v) > 0 ) {
								$value_set = implode(',', $v);
							}

							if($value_set){
								$add = " and p.".$key." IN (".$value_set.") ";
								$whr .= $add;
								$size_whr .= $add;
							}
						} else {
							$add = " and ".$key." = '".$v."' ";
							$whr .= $add;
							$size_whr .= $add;
						}

					break;
				}

			}
		}
		$qry .= $whr;

		// Paraméter filters
		/* */
		$paramFilter = array();
    foreach ((array)$arg[paramfilters] as $fk => $fv) {
        if ($fv) {
            $filtered = true;
        }

        if (strpos($fk, 'fil_p_') === 0) {
            if ($fv) {
                $paramFilter[$fk] = $fv;
            }

        }
    }

		$having = '';
    if (count($paramFilter) > 0) {
        $fkq = '';
        foreach ($paramFilter as $pmfk => $pmfv) {
            $key = str_replace('fil_p_', '', $pmfk);
            if (strpos($key, 'min') === false && strpos($key, 'max') === false) {
                $fkq .= " (";
                foreach ($pmfv as $pv) {
                    $fkq .= "FIND_IN_SET('p_" . $key . ":" . $pv . "',GROUP_CONCAT(CONCAT('p_',pa.parameterID,':',pa.ertek))) or ";
                }
                $fkq = rtrim($fkq, ' or ');
                $fkq .= ") and ";
            } else {
                $v = $pmfv[0];
                $fkq .= "isInMinMax(p.ID,'" . $key . "'," . $v . ") and ";
            }
        }
        $fkq = rtrim($fkq, ' and ');
        if ($fkq != '') {
            $having .= " HAVING ";
            $having .= $fkq;
        }
    }
		//echo $having;
		/* */

		// GROUP BY
		if ( !$admin_listing ) {
			if( isset($arg['favorite']) && !empty($arg['favorite']) ) {
				$add = "GROUP BY p.ID";
				$whr .= $add;
				$qry .= $add;
			} else {
				if( !empty($arg['meret']) ) {
					$add = "GROUP BY p.raktar_articleid";
					$whr .= $add;
					$qry .= $add;
				} else {
					$add = "GROUP BY p.raktar_articleid";
					$whr .= $add;
					$qry .= $add;
				}
			}
		} else {
			$add = "GROUP BY p.ID";
			$whr .= $add;
			$qry .= $add;
		}

		$qry .= $having;

		// ORDER
		// ORDER if collect
		if ( isset($arg['collectby'])) {
			if ( $arg['collectby'] == 'top' ) {
				$add = " ORDER BY v DESC ";
				$qry .= $add;
			}
		} else {
			if ( isset($arg['customorder']))
			{
				switch ($arg['customorder']['by']) {
					case 'popular':
					 $add =  " ORDER BY (SELECT SUM(me) as total FROM `stat_nezettseg_termek` WHERE termekID = p.ID GROUP BY termekID) ".$arg['customorder']['how'];
					 $qry .= $add;
					break;
				}
			} else
			{
				if( $arg['order'] ) {
					$add =  " ORDER BY ".$arg['order']['by']." ".$arg['order']['how'];
					$qry .= $add;
				} else {
					$add =  " ORDER BY ar ASC, fotermek DESC, p.ID DESC ";
					$qry .= $add;
				}
			}
		}

		// Összes kategórián belüli termék ID összegyűjtése
		$ids_query = $this->db->query( "SELECT p.ID FROM shop_termekek as p WHERE 1=1 ".$whr );

		if ( $ids_query->rowCount() != 0 ) {
			$ids_gets = $ids_query->fetchAll(\PDO::FETCH_ASSOC);
			$this->item_ids = array();
			foreach ( (array)$ids_gets as $aid ) {
				$this->item_ids[] = (int)$aid['ID'];
			}
		}

		// LIMIT
		$current_page = ($arg['page'] ?: 1);
		$start_item = $current_page * $this->product_limit_per_page - $this->product_limit_per_page;
		$qry .= " LIMIT ".$start_item.",".$this->product_limit_per_page.";";

		//echo $qry . '<br>';

		$this->qry_str = $qry;

		$get = $this->db->query( $qry );

		$data =  $get->fetchAll(\PDO::FETCH_ASSOC);

		$this->products_number = $this->db->query("SELECT FOUND_ROWS();")->fetch(\PDO::FETCH_COLUMN);

		$this->max_page = ceil($this->products_number / $this->product_limit_per_page);
		$this->current_page = $current_page;

		// Get sizes
		$sqry = "
		SELECT 		p.meret
		FROM 		shop_termekek as p
		WHERE 		p.ID IS NOT NULL
		";
		$sqry .= $size_whr;
		/* $sqry .= " ORDER BY
		IF(
			p.meret RLIKE '[a-z]',
			IF(	p.meret RLIKE '[1-9]{1}[a-z]',
				-1,
				-3
			), -10) ASC,
		CAST(p.meret as unsigned) ASC ";*/
		$sqry .= " ORDER BY CAST(p.meret as unsigned) ASC";

		$s_qry_data = $this->db->query( $sqry )->fetchAll(\PDO::FETCH_ASSOC);

		foreach ( $s_qry_data as $s ) {
			if($s['meret'] == '') continue;
			if (!in_array($s['meret'], $this->avaiable_sizes)) {
				$this->avaiable_sizes[] = $s['meret'];
			}
		}

		$bdata = array();


		foreach($data as $d){
			$brutto_ar 			= $d['brutto_ar'];
			$akcios_brutto_ar 	= $d['akcios_brutto_ar'];

			$kep = $d['profil_kep'];
			$d['profil_kep'] 		=  \PortalManager\Formater::productImage( $kep, false, self::TAG_IMG_NOPRODUCT );
			$d['profil_kep_small'] 	=  \PortalManager\Formater::productImage( $kep, 75, self::TAG_IMG_NOPRODUCT );

			$arInfo 		= $this->getProductPriceCalculate( $d['marka_id'], $brutto_ar );
			$akcios_arInfo 	= $this->getProductPriceCalculate( $d['marka_id'], $akcios_brutto_ar );

			if( $d['akcios'] == '1') {
				$arInfo['ar'] = $arInfo['ar'];
			}

			$arInfo['ar'] 			= ($this->settings['round_price_5'] == '1') ? round($arInfo['ar'] / 5) * 5 : $arInfo['ar'] ;
			$akcios_arInfo['ar'] 	= ($this->settings['round_price_5'] == '1') ? round($akcios_arInfo['ar'] / 5) * 5 : $akcios_arInfo['ar'] ;

			// Kategória lista, ahol szerepel a termék
			$in_cat = $this->getCategoriesWhereProductIn( $d['product_id'] );

			$d['link'] 				= DOMAIN.'termek/'.\PortalManager\Formater::makeSafeUrl( $d['product_nev'], '_-'.$d['product_id'] );
			$d['hasonlo_termek_ids']= $this->getProductRelatives( $d['product_id'] );
			$d['parameters'] 		= $this->getParameters( $d['product_id'], $d['alapertelmezett_kategoria'] );
			$d['inKatList'] 		= $in_cat;
			$d['ar'] 				= $arInfo['ar'];
			$d['akcios_fogy_ar']	= $akcios_arInfo['ar'];
			$d['arres_szazalek'] 	= $arInfo['arres'];

			$bdata[]	 			= $d;
		}

		$this->products = $bdata;

		return $this;
	}

	public function getFilters( $get, $prefix )
	{
		$re = array();

			foreach ($get as $gk => $gv) {
					if (strpos($gk, $prefix . '_') === 0) {
							/*if(strpos($gv,',') !== false){
							$x = false;
							if($gv != ''){
							$x = explode(',',rtrim($gv,','));
							}
							$g = $x;
							}else{
							$g = $gv;
							}*/
							$x = false;
							if ($gv != '') {
									$x = explode(',', rtrim($gv, ','));
							}
							$g = $x;
							if ($g == '') {
									$g = false;
							}

							$re[$gk] = $g;
					}
			}

			return $re;
	}

	public function productFilters( $ids = array() )
	{
		$back = array();

			if (empty($ids)) {
				return $back;
			}

			$q = "SELECT
				tp.parameterID as paramID,
				tp.ertek as value,
				tk.parameter,
				tk.mertekegyseg,
				tk.priority as sorrend,
				tk.is_range,
				tk.kulcs
			FROM shop_termek_parameter as tp
			LEFT OUTER JOIN shop_termek_kategoria_parameter as tk ON tk.ID = tp.parameterID
			WHERE 1=1 and tp.termekID IN (".implode(",", $ids).")
			ORDER BY tk.priority ASC
			";

			//echo $q;

			$qry = $this->db->query($q);

			if ($qry->rowCount() == 0) {
				return $back;
			}

			$data = $qry->fetchAll(\PDO::FETCH_ASSOC);
			foreach ($data as $d)
			{
				$v = $d['value'];
				if($v == '') continue;

				if (!isset($back[$d['paramID']]['ID'])) {
					$back[$d['paramID']]['parameter'] = $d['parameter'];
					$back[$d['paramID']]['sort'] = $d['sorrend'];
					$back[$d['paramID']]['ID'] = $d['paramID'];
					$back[$d['paramID']]['me'] = $d['mertekegyseg'];
				}

				preg_match_all('/^([0-9+]) (db)$/i', $v, $fn);

				if (isset($fn) && isset($fn[1][0])) {
					$v = $fn[1][0];
					$back[$d['paramID']]['is_range'] = true;
				}

				if (isset($fn) && isset($fn[2][0])) {
					$back[$d['paramID']]['mertekegyseg'] = trim($fn[2][0]);
				}

				if (!in_array($v, $back[$d['paramID']]['hints'])) {
					if (is_numeric($v)) {
						$back[$d['paramID']]['is_range'] = true;
					}
					$back[$d['paramID']]['hints'][] = $v;
				}
			}

			unset($qry);
			unset($data);

			$temp = $back;
			$back = array();
			foreach ($temp as $t) {
				asort($t['hints']);
				$t['type'] = $this->getParameterType($t, $t['hints']);
				$t['minmax'] = $this->getParameterMinMax($t[type], $t['hints']);
				$back[] = $t;
			}

			//$back = $ids;

			return $back;
	}

	private function getParameterMinMax($type, $hints)
  {
      $re  = array();
      $min = 0;
      $max = 0;

      if ($type == 'szam' || $type == 'tartomany') {

          foreach ($hints as $h) {
              if ($type == 'szam') {
                  if ($min == 0) {
                      $min = $h;
                  }

                  if ($max == 0) {
                      $max = $h;
                  }

                  if ($h < $min) {
                      $min = $h;
                  }

                  if ($h > $max) {
                      $max = $h;
                  }

              } else if ($type == 'tartomany') {
                  $xn   = explode('-', $h);
                  $xmin = (int) $xn[0];
                  $xmax = (int) $xn[1];

                  if ($min == 0) {
                      $min = $xmin;
                  }

                  if ($max == 0) {
                      $max = $xmax;
                  }

                  if ($xmin < $min) {
                      $min = $xmin;
                  }

                  if ($xmax > $max) {
                      $max = $xmax;
                  }

              }
          }
      }

      $re[min] = $min;
      $re[max] = $max;

      return $re;
  }

	private function getParameterType($parameter, $hints)
  {
      $re = false;

      if ($parameter[me] == '') {
          $re = 'szoveg';
      } else {
          $re = 'szoveg';
          foreach ($hints as $h) {
              if (is_numeric($h)) {
                  $re = 'szoveg';
                  if ($parameter[is_range] == 1) {
                      $re = 'szam';
                  }
              } else {
                  if (strpos($h, '-') !== false) {
                      $re = 'tartomany';
                  }
              }
          }
      }

      return $re;
  }

	public function getList()
	{
		return $this->products;
	}

	public function hasItems()
	{
		return ($this->products_number == 0) ? false : true;
	}

	private function getProductPriceCalculate($markaID, $bruttoAr){
		$re 	  = array();
		$re[info] =  array();
		$re[arres] = 0;
		$re[ar]   =  $bruttoAr;

		if (!$markaID) {
			return $re;
		}

		// Márka adatok
		$mq = "SELECT fix_arres FROM shop_markak WHERE ID = $markaID";
		$marka = $this->db->query($mq)->fetch(\PDO::FETCH_ASSOC);

		if( !is_null($marka['fix_arres']) ) {
			// Fix árrés
			$re[info] 	= 'FIX : '.$marka[fix_arres].'%';
			$re[arres] 	= $marka[fix_arres];
			$re[ar] 	= round($bruttoAr * ($marka[fix_arres]/100+1));
		} else {
			// Sávos árrés
			$savok = $this->db->query("SELECT ar_min, ar_max, arres FROM shop_marka_arres_savok WHERE markaID = $markaID ORDER BY ar_min ASC")->fetchAll(\PDO::FETCH_ASSOC);

			foreach( $savok as $s ){
				$min = $s[ar_min];
				$max = $s[ar_max];
				$max = (is_null($max)) ? 999999999999999 : $max;

				if( $bruttoAr >= $min && $bruttoAr <= $max ) {
					$re[info] 	= $min.' - '.$max.' : '.$s[arres].'%';
					$re[arres] 	= $s[arres];
					$re[ar] 	= round($bruttoAr * ($s[arres]/100+1));
					break;
				} else {
					$re[info] 	= $min.' - '.$max.' : '.$s[arres].'%';
					$re[arres] 	= $s[arres];
					$re[ar] 	= round($bruttoAr * ($s[arres]/100+1));
				}

			}
		}

		// Kedvezményes ár csökkentés
		if( $this->user && $this->user[kedvezmeny] > 0 ) {
			\PortalManager\Formater::discountPrice( $re[ar], $this->user[kedvezmeny] );
		}

		return $re;
	}

	public function getParameters($termekID, $katID = false){
		if (!$termekID) {
			return false;
		}
		$q = "SELECT
			p.* ,
			pm.parameter as neve,
			pm.mertekegyseg as me
		FROM shop_termek_parameter as p
		LEFT OUTER JOIN shop_termek_kategoria_parameter as pm ON pm.ID = p.parameterID
		 WHERE p.termekID = $termekID ";
		if($katID){
			$q .= " and katID = $katID ";
		}
		$q .= "
		 ORDER BY pm.priority ASC";
		extract($this->db->q($q,array('multi'=> '1')));
		$back = array();
		foreach($data as $d){
			$back[$d[parameterID]] = $d;
		}
		return $back;
	}

	/**
	 * Termék kategóriákba való becsatolása/kicsatolás
	 * A program megvizsgálja a már becsatolt kategóriákat a terméknél. Ha a listában nem szerepel egy kategória ID, ami a már becsatoltak közt van, akkor azt eltávolítja.
	 *
	 * @param  int $product_id
	 * @param  array $cat_list   kategória ID lista
	 * @return boolean
	 */
	public function doCategoryConnect( $product_id, $cat_list )
	{
		if ( !$cat_list || empty( $cat_list ) ) return false;

		// Kategória ID-k, ahova már be lett csatolva a termék
		$already_connected = $this->getProductInCategory( $product_id );


		foreach ( $cat_list as $i => $cat_id ) {
			//Ha még nem lett becsatolva egy kategóriába
			if (!in_array( $cat_id, $already_connected )) {
				$this->addProductToCategory( $product_id, $cat_id );
			} else {
				if ( ($key = array_search($cat_id, $already_connected)) !== false ) {
				    unset( $already_connected[$key] );
				}
			}
		}

		// Termék kiszedése azokból a kategóriákból, amelyikeknél kiszedtük a pipát
		$this->removeProductFromCategories( $product_id, $already_connected );

		return true;
	}

	/**
	 * Termék eltávolítás kategóriákból
	 * @param  int $product_id
	 * @param  array $category_list kategória ID lista, ahonnan ki szeretnénk szedni a termékeket
	 * @return void
	 */
	public function removeProductFromCategories( $product_id, $category_list = array() )
	{
		if( empty($category_list) ) return false;

		if ( array_key_exists('page_hashkeys',$category_list)) {
			unset($category_list['page_hashkeys']);
		}

		$katliststr = implode(',', $category_list);

		$dq = "DELETE FROM shop_termek_in_kategoria WHERE termekID = $product_id and kategoria_id IN ($katliststr)";

		$this->db->query( $dq );
	}

	/**
	 * Termék becsatolása egy adott kategóriába
	 * @param int $product_id
	 * @param int $cat_id
	 */
	public function addProductToCategory( $product_id, $cat_id )
	{
		$this->db->insert(
			'shop_termek_in_kategoria',
			array(
				'kategoria_id' => $cat_id,
				'termekID' => $product_id,
			)
		);
	}
	/**
	 * Egy adott termék kategória ID listája, ahova be van csatolva
	 * @param  int $product_id
	 * @return array             Kategória ID lista, ahova be lett csatolva a termék
	 */
	public function getProductInCategory( $product_id, $get_names = false )
	{
		if ( !$product_id ) return false;

		$cat_ids = array();

		$qry = $this->db->query( sprintf( "
			SELECT 				k.kategoria_id,
								IF(kat.szulo_id IS NULL, kat.neve, CONCAT(p.neve, ' / ', kat.neve)) as neve,
								kat.hashkey,
								kat.oldal_hashkeys
			FROM 				shop_termek_in_kategoria as k
			LEFT OUTER JOIN 	shop_termek_kategoriak as kat ON kat.ID = k.kategoria_id
			LEFT OUTER JOIN 	shop_termek_kategoriak as p ON p.ID = kat.szulo_id
			WHERE 				k.termekID = %d
			ORDER BY 			p.sorrend ASC, kat.sorrend ASC", $product_id ) );

		if( $qry->rowCount() == 0 ) return $cat_ids;

		$data = $qry->fetchAll(\PDO::FETCH_ASSOC);

		$page_hashkeys = '';
		foreach ( $data as $v ) {
			if ($get_names) {
				$cat_ids['id'][] = $v['kategoria_id'];
				$cat_ids['name'][] = $v['neve'];
				$cat_ids['hashkey'][] = $v['hashkey'];

				if( $v['oldal_hashkeys'] ) {
					$page_hashkeys .= ','.$v['oldal_hashkeys'];
				}

			} else {
				$cat_ids[] = $v['kategoria_id'];
			}

		}

		$cat_ids['page_hashkeys'] = ltrim( $page_hashkeys, ',' );

		return $cat_ids;
	}

	public function getCategoriesWhereProductIn( $product_id )
	{
		if ( !$product_id ) return false;

		$cat_ids = array();

		$qry = $this->db->query( sprintf( "
			SELECT  			c.kategoria_id as id,
								IF(cat.szulo_id IS NOT NULL, CONCAT(pc.neve,' / ', cat.neve), cat.neve) as neve
			FROM 				shop_termek_in_kategoria as c
			LEFT OUTER JOIN 	shop_termek_kategoriak as cat ON cat.ID = c.kategoria_id
			LEFT OUTER JOIN 	shop_termek_kategoriak as pc ON pc.ID = cat.szulo_id
			WHERE 				c.termekID = %d
			ORDER BY			cat.deep ASC, cat.sorrend ASC", $product_id ) );

		if( $qry->rowCount() == 0 ) return $cat_ids;

		$data = $qry->fetchAll(\PDO::FETCH_ASSOC);


		return $data;
	}

	/**
	 * Márka neve, ID alapján
	 * @param  int $id márka id
	 * @return string
	 */
	public function getManufacturName( $id ) {
		$nev = '';
		if($id == '') return $nev;

		$q = $this->db->query("SELECT neve FROM shop_markak WHERE ID = $id")->fetch(\PDO::FETCH_COLUMN);
		$nev = $q;

		return $nev;
	}
	/**
	 * Termékkép mentése adatbázisba
	 * @param int  $termekID
	 * @param string  $kep 	kép elérhetősége
	 * @param int $sorrend  kép sorrend
	 */
	public function addImageToProduct( $termekID, $kep, $sorrend = 0 ) {
		$this->db->insert(
			'shop_termek_kepek',
			array(
				'termekID' => $termekID,
				'kep' => $kep,
				'sorrend' => $sorrend
			)
		);
	}

	public function removeImageFromProduct( $termekID )
	{
		if ( !$termekID ) {
			return false;
		}

		$this->db->query("DELETE FROM shop_termek_kepek WHERE termekID = $termekID;");
	}

	public function setProfilImageToProduct( $termekID, $kep )
	{
		if ( !$termekID ) {
			return false;
		}

		$this->db->query("UPDATE shop_termekek SET profil_kep = '$kep' WHERE ID = $termekID;");
	}

	public function getAllProductImages()
	{
		$imgs = array();
		$data = $this->db->query("SELECT kep FROM shop_termek_kepek;")->fetchAll(\PDO::FETCH_ASSOC);

		foreach ($data as $kep ) {
			$img = $kep['kep'];

			$img = preg_replace('/\/{2,}/', '/', $img);

			if ( !in_array($img, $imgs)) {
				$imgs[] = $img;
			}

 		}

		return $imgs;
	}

	public function getProductImages( $product_id )
	{
		$imgs = array();

		if ( !$product_id ) {
			return $imgs;
		}

		$data = $this->db->query("SELECT kep FROM shop_termek_kepek WHERE termekID = $product_id ORDER BY sorrend ASC;")->fetchAll(\PDO::FETCH_ASSOC);

		foreach ($data as $kep ) {
			$img = $kep['kep'];

			$img = preg_replace('/\/{2,}/', '/', $img);

			$imgs[] = $kep['kep'];
 		}

		return $imgs;
	}

	public function getProductRelatives( $product_id )
	{
		$set = array();

		if (!$product_id) {
			return $set;
		}

		/*$data = $this->db->query("
			SELECT			r.termek_to,

							t.nev,
							t.fotermek,
							t.szin,
							t.profil_kep,
							t.meret
			FROM 			shop_termek_kapcsolatok as r
			LEFT OUTER JOIN shop_termekek as t ON t.ID = r.termek_to
			WHERE 			r.termek_from = $product_id and t.ID IS NOT NULL
			ORDER BY 		t.meret ASC;")->fetchAll(\PDO::FETCH_ASSOC);
		*/

		$q .= "
		SELECT			t.ID as termek_to,
						t.nev,
						t.fotermek,
						t.szin,
						t.profil_kep,
						t.meret
		FROM 			shop_termekek as t
		WHERE 			t.lathato = 1 and profil_kep IS NOT NULL and t.raktar_articleid = (SELECT raktar_articleid FROM shop_termekek WHERE ID = $product_id)
		";

		$q .= " ORDER BY CAST(t.meret as unsigned) ASC";

		$data = $this->db->query( $q )->fetchAll(\PDO::FETCH_ASSOC);

		$color_set = array();

		foreach ($data as $id ) {
			if( (!empty($this->selected_sizes) && in_array($id['meret'], $this->selected_sizes ) ) || empty($this->selected_sizes)){
				$id['link'] = DOMAIN.'termek/'.\PortalManager\Formater::makeSafeUrl($id['nev'],'_-'.$id['termek_to']);
				$set['ids'][] = $id['termek_to'];
				$set['set'][$id['termek_to']] = $id;

				// Color stack
				if (!array_key_exists( $id['szin'], $color_set )) {
					if( !$id[szin] ) continue;
					$color_set[$id['szin']] = array(
						'img' 	=> \PortalManager\Formater::productImage($id['profil_kep'],false, self::TAG_IMG_NOPRODUCT),
						'sizes' => 1,
						'ID' => $id['termek_to'],
						'link' 	=> DOMAIN.'termek/'.\PortalManager\Formater::makeSafeUrl($id['nev'],'_-'.$id['termek_to'])
					);
				} else {
					if( !$id[szin] ) continue;
					$color_set[$id['szin']]['sizes'] = (int)$color_set[$id['szin']]['sizes']+1;
				}
				$color_set[$id['szin']]['size_stack'] .= $id['meret'].', ';
				$color_set[$id['szin']]['size_set'][] = array(
					'size' => $id['meret'],
					'link' => DOMAIN.'termek/'.\PortalManager\Formater::makeSafeUrl($id['nev'],'_-'.$id['termek_to'])
				);
			}

 		}

 		$set['colors'] = $color_set;

		return $set;
	}

	/**
	 * Termék adatainak lekérése ID alapján
	 * @param  int $product_id
	 * @param  array  $opt        Opcionális paraméterek:
	 *                            string rows vesszővel elválasztva, visszaadja a kívánt rekordokat
	 * @return array
	 */
	public function get( $product_id, array $opt = array() ) {
		if( $product_id === '' || !isset( $product_id ) ) return false;

		$categories = new Categories( array( 'db' => $this->db ) );

		$row = "t.*";

		if (isset($opt['rows'])) {
			$row = rtrim( $opt['rows'], ',' );
		}

		$q = $this->db->query("
			SELECT 			$row,
							k.neve as kategoriaNev,
							ta.elnevezes as keszletNev,
							sza.elnevezes as szallitasNev
			FROM 			shop_termekek as t
			LEFT OUTER JOIN shop_termek_kategoriak as k ON k.ID = t.alapertelmezett_kategoria
			LEFT OUTER JOIN shop_termek_allapotok as ta ON ta.ID = t.keszletID
			LEFT OUTER JOIN shop_szallitasi_ido as sza ON sza.ID = t.szallitasID
			WHERE 			t.ID = $product_id

		");

		$data = $q->fetch(\PDO::FETCH_ASSOC);


		$brutto_ar 			= $data['brutto_ar'];
		$akcios_brutto_ar 	= $data['akcios_brutto_ar'];

		$kep = $data['profil_kep'];
		$data['profil_kep'] 		=  \PortalManager\Formater::productImage( $kep, false, self::TAG_IMG_NOPRODUCT );
		$data['profil_kep_small'] 	=  \PortalManager\Formater::productImage( $kep, 75, self::TAG_IMG_NOPRODUCT );

		$arInfo 		= $this->getProductPriceCalculate( $data['marka'], $brutto_ar );
		$akcios_arInfo 	= $this->getProductPriceCalculate( $data['marka'], $akcios_brutto_ar );

		if( $d['akcios'] == '1') {
			$arInfo['ar'] = $arInfo['ar'];
		}

		$arInfo['ar'] 			= ($this->settings['round_price_5'] == '1') ? round($arInfo['ar'] / 5) * 5 : $arInfo['ar'] ;
		$akcios_arInfo['ar'] 	= ($this->settings['round_price_5'] == '1') ? round($akcios_arInfo['ar'] / 5) * 5 : $akcios_arInfo['ar'] ;

		$data['rovid_leiras'] 		= $this->addLinkToString( $data, $data['rovid_leiras'] );
		$data['ar'] 				= $arInfo['ar'];
		$data['akcios_fogy_ar']		= $akcios_arInfo['ar'];
		$data['arres_szazalek'] 	= $arInfo['arres'];
		$data['hasonlo_termek_ids'] = $this->getProductRelatives( $product_id );
		$in_kat = $this->getProductInCategory( $product_id, true );
		$data['in_cat_ids'] 		= $in_kat['id'];
		$data['in_cat_names'] 		= $in_kat['name'];
		$data['in_cat_hashkey']		= $in_kat['hashkey'];
		$data['in_cat_page_hashkeys']= $in_kat['page_hashkeys'];
		$data['images'] 			= $this->getProductImages( $product_id );
		$data['parameters']			= $this->getParameters( $product_id, $data['alapertelmezett_kategoria'] );
		$data['related_products_ids']	= $this->getRelatedIDS( $product_id );
		$data['nav'] = array_reverse($categories->getCategoryParentRow((int)$data['alapertelmezett_kategoria'], false));

		$data['keszlet_info'] = $this->checkProductStockName( $data['keszletID'], $data['raktar_keszlet'], true );
		$data['szallitas_info'] = $this->checkProductTransportName( $data['szallitasID'], $data['raktar_keszlet'] );

		// Csatolt dokumentumokat
		$data['documents'] = $this->getTermDocuments( $product_id );
		// Linkek
		$data['link_lista']	= $this->getProductLinksFromStr( $data['linkek'] );
		// Csatolt link hivatkozások
		$this->getProductLinksFromCategoryHashkeys( $data['in_cat_page_hashkeys'], $data['link_lista'] );

		return $data;
	}

	public function checkProductTransportName( $szallitasID, $keszlet = 1 )
	{
		$outselling = ($this->settings['stock_outselling'] == 1) ? true : false;

		if ( $outselling ) {
			$statkey = (int)$this->settings['stock_outselling_transport'];
		}  else {
			$statkey = (int)$this->settings['stock_outselling_transport_off'];
		}

		if ( $statkey == 0 || $keszlet > 0 ) {
			$statkey = $szallitasID;
		}

		$stockdata = $this->db->query("SELECT elnevezes FROM shop_szallitasi_ido WHERE ID = ".$statkey)->fetch(\PDO::FETCH_ASSOC);

		return $stockdata['elnevezes'];
	}

	public function checkProductStockName( $keszeltID, $keszlet = 1, $formated = false )
	{
		$outselling = ($this->settings['stock_outselling'] == 1) ? true : false;

		if ( $outselling ) {
			$statkey = (int)$this->settings['stock_outselling_status'];
		}  else {
			$statkey = (int)$this->settings['stock_outselling_status_off'];
		}

		if ( $statkey == 0 || $keszlet > 0 ) {
			$statkey = $keszeltID;
		}

		$stockdata = $this->db->query("SELECT elnevezes, color FROM shop_termek_allapotok WHERE ID = ".$statkey)->fetch(\PDO::FETCH_ASSOC);

		if ( $formated ) {
			return '<span style="color:'.$stockdata['color'].';">'.$stockdata['elnevezes'].'</span>';
		} else {
			return $stockdata['elnevezes'];
		}
	}

	public function getTermDocuments( $termid = 0 )
	{
		$data = array();

		$qry = "SELECT
			d.*
		FROM shop_documents_termek_xref as dx
		LEFT OUTER JOIN shop_documents as d ON d.ID = dx.doc_id
		WHERE 1=1 and d.lathato = 1 and dx.termek_id = {$termid}";

		$qry .= " ORDER BY d.sorrend ASC, d.cim ASC";
		$list = $this->db->query( $qry );

		if ( $list->rowCount() != 0 ) {
			$lista = $list->fetchAll(\PDO::FETCH_ASSOC);
			foreach ( $lista as $doc ) {
				$xcim = explode(".", $doc['filepath']);
				$ext = ($doc['tipus'] == 'external') ? 'url' : end($xcim);
				$doc['ext'] = $ext;
				$doc['icon'] = $this->getTermDocumentExtensionIcon( $ext );
				$doc['filesize'] = $this->getTermDocumentFilesize( $ext, $doc['filepath'] );
				$data[] = $doc;
			}
			return $data;
		} else {
			return $data;
		}
	}

	protected function getTermDocumentFilesize( $ext = false, $filepath )
	{
		if ($ext == 'url' ) {
			return false;
		} else {
			return \Helper::formatSizeUnits(filesize('../admin/'.$filepath));
		}
	}

	protected function getTermDocumentExtensionIcon( $ext = false )
	{
		if ( $ext == '' || !$ext || $ext == 'url' ) {
			return 'docst-url';
		} else {
			return 'docst-'.$ext;
		}
	}

	public function getRelatedIDS( $product_id )
	{
		$ids = array();

		if( $product_id === '' || !isset( $product_id ) ) return false;

		$q = $this->db->squery("SELECT target_id FROM shop_termek_ajanlo_xref WHERE base_id = :id GROUP BY target_id;", array( 'id' => $product_id ) );

		if ( $q->rowCount() == 0 ) {
			return false;
		}

		foreach ($q->fetchAll(\PDO::FETCH_ASSOC) as $d )
		{
			$ids[] = $d['target_id'];
		}

		return $ids;
	}

	/**
	 * Egy termék összes elérhető adatának lekérése termék ID alapján
	 * @param  int $product_id
	 * @return boolean|array
	 */
	public function getAllData( $product_id ){
		if( $product_id === '' || !isset( $product_id ) ) return false;

		$q = $this->db->query("
		SELECT 				t.*, t.ID as termekID
		FROM 				shop_termekek as t
		WHERE 				t.ID = $product_id");

		return $q->fetch(\PDO::FETCH_ASSOC);
	}

	public function getProductLinksFromStr( $linkstr )
	{
		$links = array();

		$each = explode("||", $linkstr);

		if ( $each[0] == '' ) {
			return $links;
		}

		foreach ($each as $value) {
			$link = explode("==>", $value);
			$links[] = array(
				'title' => $link[0],
				'link' => $link[1]
			);
		}

		return $links;
	}

	/**
	 * Oldal hivatkozások termékhez csatolt hashkey-ekből
	 * */
	public function getProductLinksFromHashkeys( $productID, &$linkset )
	{
		$hashkeys = explode( ",", $this->db->query("SELECT link_hashkeys FROM shop_termekek WHERE ID = $productID and link_hashkeys IS NOT NULL;")->fetchColumn() );

		// Linkek begyűjtése
		foreach ( $hashkeys as $link_key ) {
			$eleres = $this->db->query("SELECT eleres FROM oldalak WHERE hashkey = '$link_key' and hashkey IS NOT NULL and lathato = 1;")->fetchColumn();

			$title = $this->hashkeyRewrite( $link_key );

			if( $eleres ){
				if( $title == '' ) continue;
				$linkset[] = array(
					'title' => $title,
					'link' => '/p/'.$eleres
				);
			}
		}
	}

	/**
	 * Oldal hivatkozások kategóriához csatolt hashkey-ekből
	 * */
	public function getProductLinksFromCategoryHashkeys( $hashkeys, &$linkset )
	{
		$title_in = array();
		$hashkeys = explode( ",", $hashkeys );
		// Linkek begyűjtése
		foreach ( $hashkeys as $link_key ) {
			$eleres = $this->db->query("SELECT eleres FROM oldalak WHERE hashkey = '$link_key' and hashkey IS NOT NULL and lathato = 1;")->fetchColumn();

			$title = $this->hashkeyRewrite( $link_key );

			if( $eleres ){
				if( $title == '' ) continue;
				if( !in_array( $title, $title_in) ) {
					$title_in[] = $title;
					$linkset[] = array(
						'title' => $title,
						'link' => '/p/'.$eleres
					);
				}
			}
		}

		unset($title_in);
	}

	/**
	 * Hashkey feliratának módosítása karakterek alapján
	 * */
	private function hashkeyRewrite( $link_key)
	{
		$title = $link_key;

		if( strpos( $link_key, 'WS_MERET_' ) === 0 ) {
			$title = 'Mérettáblázat';
		} else if( strpos( $link_key, 'WS_ANYAG_' ) === 0 ) {
			$title = 'Anyagösszetétel';
		} else if( strpos( $link_key, 'WS_EGYEB_' ) === 0 ) {
			// Egyéb linkek
			if( strpos( $link_key, 'USZOHOSSZUSAG' ) !== false ) {
				$title = 'Úszóhosszúság';
			}
		}

		return $title;
	}

	/**
	*  Felmenő kategóriák ID-jának lekérdezése
	**/
	public function getCategorySet( $cat_id )
	{
		$set = array();

		$walk = true;

		while( $walk ) {
			$q = $this->db->query("SELECT ID, szulo_id, neve, hashkey FROM shop_termek_kategoriak WHERE ID = $cat_id;");
			$qd = $q->fetch(\PDO::FETCH_ASSOC);

			if( is_null( $qd['szulo_id'] ) ) {
				$set[] = array(
					'ID' => $qd['ID'],
					'nev' => $qd['neve'],
					'hash' => $qd['hashkey']
				);

				$walk = false;
			} else {
				$set[] = array(
					'ID' => $qd['ID'],
					'nev' => $qd['neve'],
					'hash' => $qd['hashkey']
				);
				$cat_id = $qd['szulo_id'];
			}
		}




		return $set;
	}

	public function addLinkToString( $product, $text )
	{
		// Formázás eltávolítás, kivétel a sortörés
		//$text = strip_tags( $text, '<br>' );

		$gender = '';

		if(
			strpos( strtolower($product['csoport_kategoria']), 'férfi' ) !== false
		) {
			$gender = 'férfi';
		} else if(
			strpos( strtolower($product['csoport_kategoria']), 'női' ) !== false
		) {
			$gender = 'női';
		} else if(
			strpos( strtolower($product['csoport_kategoria']), 'lányka' ) !== false ||
			strpos( strtolower($product['csoport_kategoria']), 'lány' ) !== false) {
			$gender = 'lány';
		} else if(
			strpos( strtolower($product['csoport_kategoria']), 'fiú' ) !== false) {
			$gender = 'fiú';
		}

		/**
		 * Hivatkozások
		**/
		$rep = array(
			'waterfeel x-life eco' => 'WATERFEELXLIFEECO',
			'waterfeel x-life' => 'WATERFEELXLIFE',
			'max life' => 'MAXLIFE',
			'max-life' => 'MAX-LIFE',
			'powerskin carbon pro' => 'POWERSKINCARBONPRO',
			'powerskin carbon flex' => 'POWERSKINCARBONFLEX',
			'powerskin carbon air' => 'POWERSKINCARBONAIR',
			'powerskin carbon st' => 'POWERSKICARBONST',
			'bodylift' => 'BODYLIFT',
			'sensitive' => 'SENSITIVE',
		);

		$replaces = array(
			'waterfeel x-life eco' => $this->getPageByKeywords( array( 'waterfeel x-life-eco', $gender ), \Helper::makeSafeUrl( 'waterfeel x-life-eco', '' ) ),
			'waterfeel x-life' => $this->getPageByKeywords( array( 'waterfeel x-life', $gender ), \Helper::makeSafeUrl( 'waterfeel x-life', '' ) ),
			'powerskin carbon pro' => $this->getPageByKeywords( array( 'powerskin carbon pro', $gender ), \Helper::makeSafeUrl( 'powerskin carbon pro', '' ) ),
			'powerskin carbon flex' => $this->getPageByKeywords( array( 'powerskin carbon flex', $gender ), \Helper::makeSafeUrl( 'powerskin carbon flex', '' ) ),
			'powerskin carbon air' => $this->getPageByKeywords( array( 'powerskin carbon air', $gender ), \Helper::makeSafeUrl( 'powerskin carbon air', '' ) ),
			'powerskin carbon st' => $this->getPageByKeywords( array( 'powerskin carbon st', $gender ), \Helper::makeSafeUrl( 'powerskin carbon st', '' ) ),
			'max life' => $this->getPageByKeywords( array( 'max life', $gender ), \Helper::makeSafeUrl( 'max life', '' ) ),
			'max-life' => $this->getPageByKeywords( array( 'max life', $gender ), \Helper::makeSafeUrl( 'max life', '' ) ),
			'bodylift' => $this->getPageByKeywords( array( 'bodylift' ), \Helper::makeSafeUrl( 'bodylift', '' ) ),
			'sensitive' => $this->getPageByKeywords( array( 'sensitive', $gender ), \Helper::makeSafeUrl( 'sensitive', '' ) ),
		);

		foreach ( $replaces as $search => $link ) {
			$reptext =  $rep[$search];
			$text = preg_replace( '/('.$search.')/i', $reptext, $text );
		}

		foreach ( $replaces as $search => $link ) {
			$reptext =  $rep[$search];
			$text = str_replace( $reptext, '<a href="'.$link[url].'" title="'.$link[title].'" target="_blank">'.ucwords($search).'</a>', $text );
		}


		return $text;
	}

	public function getPageByKeywords( $keyword_set = array(), $anchor = '' )
	{
		$q = "SELECT eleres, cim FROM oldalak WHERE ID IS NOT NULL and hashkey_keywords IS NOT NULL and lathato = 1";

		$src = ' and (';
		foreach ( $keyword_set as $key ) {
			if( $key != '') {
				$src .= " hashkey_keywords LIKE '%$key%' and ";
			}
		}
		$src = rtrim($src, ' and ');
		$src .= ')';

		$q .= $src;

		$qq = $this->db->query( $q )->fetch(\PDO::FETCH_ASSOC);

		if( $qq[eleres] == '' ) return false;

		return array(
			'url' => '/p/'.$qq[eleres] . ( $anchor != '' ? '#'.$anchor : ''),
			'title' => $qq[cim]
		);
	}

	public function pushFavoriteToCart( $redirect = false )
	{
		$ids = array();
		$mid = \Helper::getMachineID();
		$favs = $this->db->query("SELECT termekID FROM shop_termek_favorite WHERE mid = '$mid'");

		if ( $favs->rowCount() != 0) {
			$favs = $favs->fetchAll(\PDO::FETCH_ASSOC);

			foreach ( $favs as $f ) {
				$id = (int)$f['termekID'];
				$c = $this->db->query("SELECT ID FROM shop_kosar WHERE gepID = '$mid' and termekID = $id");
				if ($c->rowCount() == 0) {
					$this->db->insert(
						'shop_kosar',
						array(
							'gepID' => $mid,
							'termekID' => $id,
							'me' => 1
						)
					);
				}
			}

			if ( $redirect ) {
				\Helper::reload( $redirect );
			}
		}

		return false;
	}

	/*===============================
	=            GETTERS            =
	===============================*/

	public function getItemNumbers()
	{
		return $this->products_number;
	}

	public function getSelectedSizes()
	{
		return $this->selected_sizes;
	}

	public function getAvaiableSizes()
	{
		return $this->avaiable_sizes;
	}

	public function getMaxPage()
	{
		return $this->max_page;
	}

	public function getCurrentPage()
	{
		return $this->current_page;
	}

	public function getQueryString()
	{
		return $this->qry_str;
	}

	/*-----  End of GETTERS  ------*/

	public function __destruct()
	{
		$this->db = null;
		$this->user = null;
		$this->products = null;
		$this->products_number = 0;
		$this->product_limit_per_page = 50;
		$this->max_page = 1;
		$this->current_page = 1;
		$this->avaiable_sizes = array();
		$this->qry_str = null;
		$this->selected_sizes = array();
	}
}
?>
