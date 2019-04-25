<?
namespace ShopManager;

use ShopManager\OrderException;
use MailManager\Mailer;
use MailManager\MailTemplates;
use PortalManager\Template;
use PortalManager\PartnerReferrer;
use PortalManager\Portal;
use PortalManager\Coupon;

/**
 * class Shop
 *
 */
class Shop
{
	private $db = null;
	private $user = null;

	public $document_groups = array();

	public $document_group_colors = array();

	function __construct( $arg = array() ){
		$this->db = $arg[db];
		$this->user = $arg[user];
		$this->settings = $arg[view]->settings;


		// Dokument csoportok
		// Dokument színek
		$docs = $this->db->query("SELECT * FROM shop_documents_groups ORDER BY sorrend ASC;");

		if($docs->rowCount() > 0)
		{
			$docs = $docs->fetchAll(\PDO::FETCH_ASSOC);

			foreach ( $docs as $doc )
			{
				$this->document_groups[$doc['key']] = $doc['title'];
				$this->document_group_colors[$doc['key']] = '#'.$doc['color_hex'];
			}
		}
	}

	public function getTotalTermekNum(){
		return $this->db->query("SELECT count(ID) FROM shop_termekek WHERE lathato = 1;")->fetchColumn();
	}

	public function listUjdonsagok($arg = array()){
		$akcios_plus_szaz = AKCIOS_BRUTTO_AR_PLUSZ_SZAZALEK;
		$apsz = $akcios_plus_szaz / 100 + 1;

		$q = "SELECT
			t.ID,
			tik.modszerID,
			tik.gyujtoID,
			t.termek_kategoria,
			getTermekAr(t.marka, t.brutto_ar) as brutto_ar,
			t.akcios_brutto_ar,
			t.akcios,
			t.marka,
			TRIM(SUBSTRING_INDEX(m.neve,'::',1)) as markaNev,
			t.ujdonsag,
			t.szuper_akcios,
			t.egyedi_ar,
			getTermekUrl(t.ID,'".DOMAIN."') as url,
			t.pickpackszallitas,
			IF(t.akcios,
				IF(t.akcios_egyedi_brutto_ar != 0,
					t.akcios_egyedi_brutto_ar,
					getTermekAr(t.marka, (t.brutto_ar * ".$apsz."))),
				getTermekAr(t.marka, t.brutto_ar)
			) as brutto_ar,
			IF(t.egyedi_ar IS NOT NULL,
				t.egyedi_ar,
				getTermekAr(t.marka, IF(t.akcios,t.akcios_brutto_ar,t.brutto_ar))
			) as ar,
			t.nev as termekNev,
			ta.elnevezes as keszlet,
			GROUP_CONCAT(CONCAT('p_',p.parameterID,':',p.ertek)) as paramErtek,
			FULLIMG(t.profil_kep) as profil_kep
		FROM shop_termekek as t
		LEFT OUTER JOIN shop_termek_parameter as p ON p.termekID = t.ID
		LEFT OUTER JOIN shop_termek_in_kategoria as tik ON tik.termekID = t.ID
		LEFT OUTER JOIN shop_markak as m ON m.ID = t.marka
		LEFT OUTER JOIN shop_termek_allapotok as ta ON ta.ID = t.keszletID
		WHERE t.ID IS NOT NULL and
			t.lathato = 1 and
			t.ujdonsag IN (1)
		 ";


		 $q .= "GROUP BY t.ID ";
		 $q .= " ORDER BY RAND() ";

		$arg[multi] = '1';
		extract($this->db->q($q,$arg));

		//return false;
		return $ret;
	}

	public function listAkcios($arg = array()){
		$akcios_plus_szaz = AKCIOS_BRUTTO_AR_PLUSZ_SZAZALEK;
		$apsz = $akcios_plus_szaz / 100 + 1;

		$q = "SELECT
			t.ID,
			tik.modszerID,
			tik.gyujtoID,
			t.termek_kategoria,
			IF(t.akcios,
				IF(t.akcios_egyedi_brutto_ar != 0,
					t.akcios_egyedi_brutto_ar,
					getTermekAr(t.marka, (t.brutto_ar * ".$apsz."))),
				getTermekAr(t.marka, t.brutto_ar)) as brutto_ar,
			t.akcios_brutto_ar,
			t.akcios,
			t.marka,
			TRIM(SUBSTRING_INDEX(m.neve,'::',1)) as markaNev,
			t.ujdonsag,
			t.szuper_akcios,
			t.egyedi_ar,
			getTermekUrl(t.ID,'".DOMAIN."') as url,
			t.pickpackszallitas,
			IF(t.egyedi_ar IS NOT NULL, t.egyedi_ar, getTermekAr(t.marka, IF(t.akcios,t.akcios_brutto_ar,t.brutto_ar))) as ar,
			t.nev as termekNev,
			ta.elnevezes as keszlet,
			GROUP_CONCAT(CONCAT('p_',p.parameterID,':',p.ertek)) as paramErtek,
			FULLIMG(t.profil_kep) as profil_kep
		FROM shop_termekek as t
		LEFT OUTER JOIN shop_termek_parameter as p ON p.termekID = t.ID
		LEFT OUTER JOIN shop_termek_in_kategoria as tik ON tik.termekID = t.ID
		LEFT OUTER JOIN shop_markak as m ON m.ID = t.marka
		LEFT OUTER JOIN shop_termek_allapotok as ta ON ta.ID = t.keszletID
		WHERE t.ID IS NOT NULL and
			t.lathato = 1 and
			t.akcios IN (1)
		 ";


		 $q .= "GROUP BY t.ID ";
		 $q .= " ORDER BY RAND() ";

		$arg[multi] = '1';
		extract($this->db->q($q,$arg));

		return $ret;
	}

	public function checkSzuperakciosTermekNum(){
		$num = 0;

		$num = $this->db->query("SELECT count(ID) FROM shop_termekek WHERE lathato = 1 and szuper_akcios = 1 and akcios = 1")->fetch(\PDO::FETCH_COLUMN);

		return $num;
	}

	public function listTermekek($arg = array()){
		$akcios_plus_szaz = AKCIOS_BRUTTO_AR_PLUSZ_SZAZALEK;
		$apsz = $akcios_plus_szaz / 100 + 1;

		$filtered 	= false;
		$where 		= '';
		$arg[orderByPriority] = ($arg[parameterOrderByPriority])?true:false;

		$q = "SELECT
			t.ID,
			tik.modszerID,
			tik.gyujtoID,
			t.termek_kategoria,
			t.szuper_akcios_szazalek,
			t.utolso_darab,
			IF(t.akcios,
				IF(t.akcios_egyedi_brutto_ar != 0,
					t.akcios_egyedi_brutto_ar,
					getTermekAr(t.marka, (t.brutto_ar * ".$apsz."))),
				getTermekAr(t.marka, t.brutto_ar)
			) as brutto_ar,
			IF(t.egyedi_ar IS NOT NULL,
				t.egyedi_ar,
				getTermekAr(t.marka, IF(t.akcios,t.akcios_brutto_ar,t.brutto_ar))
			) as ar,
			t.akcios_brutto_ar,
			t.akcios,
			t.marka,
			TRIM(SUBSTRING_INDEX(m.neve,'::',1)) as markaNev,
			t.ujdonsag,
			t.szuper_akcios,
			t.egyedi_ar,
			getTermekUrl(t.ID,'".DOMAIN."') as url,
			t.pickpackszallitas,
			t.nev as termekNev,
			ta.elnevezes as keszlet,
			GROUP_CONCAT(CONCAT('p_',p.parameterID,':',p.ertek)) as paramErtek,
			FULLIMG(t.profil_kep) as profil_kep
		FROM shop_termekek as t
		LEFT OUTER JOIN shop_termek_parameter as p ON p.termekID = t.ID
		LEFT OUTER JOIN shop_termek_in_kategoria as tik ON tik.termekID = t.ID
		LEFT OUTER JOIN shop_markak as m ON m.ID = t.marka
		LEFT OUTER JOIN shop_termek_allapotok as ta ON ta.ID = t.keszletID
		WHERE ";

		$where .= "
			t.ID IS NOT NULL and
			t.lathato = 1";
		$q .= $where;

		if($arg[modszer_kategoria]){
			$w = " and tik.modszerID = ".$arg[modszer_kategoria];
			$q .= $w;
			$where .= $w;
		}
		if($arg[gyujto_kategoria]){
			$w = " and tik.gyujtoID = ".$arg[gyujto_kategoria];
			$q .= $w;
			$where .= $w;
		}
		if($arg[termek_kategoria]){
			$w = " and t.termek_kategoria = ".$arg[termek_kategoria];
			$q .= $w;
			$where .= $w;
		}

		// Szűrők
		if(count($arg[filters]) > 0){
			extract($arg[filters]);
			if($fil_nev){
				 $q .= " and t.nev like '%".$fil_nev[0]."%' ";
			}
			if($fil_marka){
				$imk = implode(',',$fil_marka);
				$q .= " and t.marka IN (".$imk.") ";
			}
			if($fil_ujdonsag){
				 $q .= " and t.ujdonsag = 1 ";
			}
			if($fil_akcios){
				 $q .= " and t.akcios = 1 ";
			}
			if($fil_szuper_akcios){
				 $q .= " and t.szuper_akcios = 1 ";
			}
			if($fil_pickpackpontra_szallithato){
				 $q .= " and t.pickpackszallitas = 1 ";
			}
			if($fil_ar_min){
				 $q .= " and getTermekAr(t.marka,IF(t.egyedi_ar IS NOT NULL,t.egyedi_ar,IF(t.akcios,t.akcios_brutto_ar,t.brutto_ar))) >= ".$fil_ar_min[0];
			}
			if($fil_ar_max){
				 $q .= " and getTermekAr(t.marka,IF(t.egyedi_ar IS NOT NULL,t.egyedi_ar,IF(t.akcios,t.akcios_brutto_ar,t.brutto_ar))) <= ".$fil_ar_max[0];
			}
		}

		$q .= "
		GROUP BY
			t.ID
		";
		if(count($arg[filters]) > 0){
			$paramFilter = array();
			foreach($arg[filters] as $fk => $fv){
				if($fv) $filtered = true;
				if(strpos($fk,'fil_p_') === 0){
					if($fv)
					$paramFilter[$fk] = $fv;
				}
			}

			//Having
			if(count($paramFilter) > 0){
				$fkq = '';

				foreach($paramFilter as $pmfk => $pmfv){
					$key = str_replace('fil_p_','',$pmfk);
					if(strpos($key,'min') === false && strpos($key,'max') === false){
						$fkq .= " (";
							foreach($pmfv as $pv){
								$fkq .= "FIND_IN_SET('p_".$key.":".$pv."',GROUP_CONCAT(CONCAT('p_',p.parameterID,':',p.ertek))) or ";
							}
						$fkq = rtrim($fkq,' or ');
						$fkq .= ") and ";
					}else{
						$v = $pmfv[0];
						$fkq .= "isInMinMax(t.ID,'".$key."',".$v.") and ";

					}
				}
				$fkq = rtrim($fkq,' and ');
				if($fkq != ''){
					$q .= " HAVING ";
					$q .= $fkq;
				}
			}

		}

		// ORDER
		$order = 't.nev ASC';
		if($arg[order] == '' || $arg[order] == 'abc_asc'){
			$order = 'CONCAT(TRIM(SUBSTRING_INDEX(m.neve,\'::\',1))," ",t.nev) ASC';
		}else if($arg[order] == 'abc_desc'){
			$order = 'CONCAT(TRIM(SUBSTRING_INDEX(m.neve,\'::\',1))," ",t.nev) DESC';
		}else if($arg[order] == 'price_asc'){
			$order = 'ar ASC';
		}else if($arg[order] == 'price_desc'){
			$order = 'ar DESC';
		}else if($arg[order] == 'view_asc'){
			$order = 'getTermekViewStat(t.ID,90) ASC';
		}else if($arg[order] == 'view_desc'){
			$order = 'getTermekViewStat(t.ID,90) DESC';
		}

		if($arg[filters][fil_szuper]){
			$order = "t.szuper_akcios DESC,  ".$order;
		}

		$q .= " ORDER BY ".$order;

		//echo '<pre>'.$q.'</pre>';

		$arg[multi] = '1';
		extract($this->db->q($q,$arg));

		$re = $ret;
		$re[info][filtered] 	= $filtered;

		$priceInfo = array(
			'min' => 0,
			'max' => 250000
		);
		$markak = array();

		$priceInfo = $this->getPrinceInfo($where, $priceInfo);
		$re[info][price][min] 	= $priceInfo[min];
		$re[info][price][max] 	= $priceInfo[max];
		$re[info][markak] 		= $this->getAwaiableMarkak($where, $markak);

		$r 		= array();
		$tidarr = array();

		foreach($data as $d){
			/*$arInfo 		= $this->getTermekArInfo($d[marka], $d[ar]);
			$d[ar] 			= $arInfo[ar];

			$brar 			= $this->getTermekArInfo($d[marka], $d[brutto_ar]);
			$d[brutto_ar] 	= $brar[ar];

			$akcios_arInfo 	= $this->getTermekArInfo($d[marka], $d[akcios_brutto_ar]);
			$d[akcios_brutto_ar] 	= $akcios_arInfo[ar];*/

			$params 		= $this->getTermekParameter($d[ID], $d[termek_kategoria], $arg);
			$d[params] 		= $params;

			$tidarr[] 	= $d[ID];
			$r[] 		= $d;
		}

		$re[termekIDs] = $tidarr;
		$re[data] = $r;

		return $re;
	}

	public function loadTermekSet($termek_id_str, $onTermekId = false, $arg = array()){
		$re 	= array();

		if( $termek_id_str == '' ){
			return false;
		}

		$q 		= "SELECT ID,nev,getTermekUrl(ID,'".DOMAIN."') as url FROM shop_termekek WHERE ID IN(".$termek_id_str.")";

		$arg[multi] = '1';
		extract($this->db->q($q,$arg));

		$edata 	= array();
		$ex 	= explode(",",$termek_id_str);
		foreach($ex as $e){
			$edata[] = $data[\Helper::getFromArrByAssocVal($data,'ID',$e)];
		}

		$data = $edata;

		$itemSetNums 	= count($data);
		$lastPos 		= $itemSetNums-1;

		if($onTermekId && $itemSetNums > 1){
			$currentPosition = \Helper::getFromArrByAssocVal($data,'ID',$onTermekId);
			if($currentPosition == 0){
				$prev = $lastPos;
				$next = $currentPosition+1;
			}else if($currentPosition > 0 && $currentPosition < $lastPos){
				$prev = $currentPosition-1;
				$next = $currentPosition+1;
			}else if($currentPosition == $lastPos){
				$prev = $currentPosition-1;
				$next = 0;
			}
		}

		$re[set] 	= $data;
		$re[prev] 	= $prev;
		$re[next] 	= $next;


		return $re;
	}

	public function getAwaiableMarkak($where, $arr){
		$q = "SELECT
			TRIM(SUBSTRING_INDEX(m.neve,'::',1)) as markaNev
		FROM shop_termekek as t
		LEFT OUTER JOIN shop_markak as m ON m.ID = t.marka
		LEFT OUTER JOIN shop_termek_in_kategoria as tik ON tik.termekID = t.ID
		WHERE ".$where."
		GROUP BY markaNev";
		$arg[multi] = '1';
		extract($this->db->q($q,$arg));
		foreach($data as $d){
			$arr[] = $d[markaNev];
		}
		return $arr;
	}
	public function getSlideShowItems($arg = array()){
		$q = "SELECT * FROM slideshow WHERE lathato = 1 ORDER BY sorrend ASC";
		$arg[multi] = '1';
		extract($this->db->q($q,$arg));

		return $data;
	}
	public function getHasonloTermekek($searchString, $arg = array()){
		$akcios_plus_szaz = AKCIOS_BRUTTO_AR_PLUSZ_SZAZALEK;
		$apsz = $akcios_plus_szaz / 100 + 1;

		$tures 		= 25;
		$isParamSrc = (empty($arg[key_params])) ? false : true;

		$q = "SELECT
			t.ID,
			t.nev,
			t.akcios,
			t.szuper_akcios,
			t.szuper_akcios_szazalek,
			t.utolso_darab,
			IF(t.akcios,
				IF(t.akcios_egyedi_brutto_ar != 0,
					t.akcios_egyedi_brutto_ar,
					getTermekAr(t.marka, (t.brutto_ar * ".$apsz."))),
				getTermekAr(t.marka, t.brutto_ar)
			) as brutto_ar,
			IF(t.egyedi_ar IS NOT NULL,
				t.egyedi_ar,
				getTermekAr(t.marka, IF(t.akcios,t.akcios_brutto_ar,t.brutto_ar))
			) as ar,
			getTermekUrl(t.ID,'".DOMAIN."') as url,
			FULLIMG(t.profil_kep) as profil_kep,
			TRIM(SUBSTRING_INDEX(m.neve,'::',1)) as markaNev,
			szovegHasonlosag(t.nev,'".$searchString."') as same,
			GROUP_CONCAT(CONCAT('p_',p.parameterID,':',p.ertek)) as paramErtek
		FROM `shop_termekek` as t
		LEFT OUTER JOIN shop_termek_parameter as p ON p.termekID = t.ID
		LEFT OUTER JOIN shop_markak as m ON m.ID = t.marka
		WHERE
			t.ID IS NOT NULL and
			t.lathato = 1 ";
		if($arg[excID] != ''){
			$q .= " and t.ID != ".$arg[excID];
		}
		if(!$isParamSrc){
			$q .= "
				and szovegHasonlosag(t.nev,'".$searchString."') < 100 and szovegHasonlosag(nev,'".$searchString."') > $tures
			";
		}


		$q.= " GROUP BY t.ID ";
		//Having
		$paramFilter = $arg[key_params];
		if(count($paramFilter) > 0){
			$fkq = '';

			foreach($paramFilter as $pf){
				$fkq .= " FIND_IN_SET('p_".$pf[parameterID].":".$pf[ertek]."',GROUP_CONCAT(CONCAT('p_',p.parameterID,':',p.ertek))) and ";
			}
			$fkq = rtrim($fkq,' and ');
			if($fkq != ''){
				$q .= " HAVING ";
				$q .= $fkq;
			}
		}

		$q .= "ORDER BY same DESC, t.nev ASC ";

		$arg[multi] = '1';
		extract($this->db->q($q,$arg));

		$keys = array();

		foreach($data as $d){
			$keys[] = $d[ID];
		}
		$ret[keys] = $keys;
		return $ret;
	}
	public function getFreshTermekek($arg = array()){
		$akcios_plus_szaz = AKCIOS_BRUTTO_AR_PLUSZ_SZAZALEK;
		$apsz = $akcios_plus_szaz / 100 + 1;

		$q = "SELECT
			t.ID,
			t.nev,
			t.akcios,
			t.szuper_akcios,
			t.ujdonsag,
			t.szuper_akcios_szazalek,
			t.utolso_darab,
			IF(t.akcios,
				IF(t.akcios_egyedi_brutto_ar != 0,
					t.akcios_egyedi_brutto_ar,
					getTermekAr(t.marka, (t.brutto_ar * ".$apsz."))),
				getTermekAr(t.marka, t.brutto_ar)
			) as brutto_ar,
			IF(t.egyedi_ar IS NOT NULL,
				t.egyedi_ar,
				getTermekAr(t.marka, IF(t.akcios,t.akcios_brutto_ar,t.brutto_ar))
			) as ar,
			getTermekUrl(t.ID,'".DOMAIN."') as url,
			FULLIMG(t.profil_kep) as profil_kep,
			TRIM(SUBSTRING_INDEX(m.neve,'::',1)) as markaNev
		FROM shop_termekek as t
		LEFT OUTER JOIN shop_markak as m ON m.ID = t.marka
		WHERE t.ID IS NOT NULL and
		t.lathato = 1
		ORDER BY t.letrehozva DESC";
		$arg[multi] = '1';
		extract($this->db->q($q,$arg));

		$keys = array();

		foreach($data as $d){
			$keys[] = $d[ID];
		}
		$ret[keys] = $keys;
		return $ret;
	}
	public function getMenu($key){
		$q = "SELECT nev,url FROM menu WHERE gyujto = '$key' ORDER BY sorrend ASC;";
		$arg[multi] = '1';
		extract($this->db->q($q,$arg));

		return $data;
	}
	public function getSzuperakciosTermekek($arg = array()){
		$akcios_plus_szaz = AKCIOS_BRUTTO_AR_PLUSZ_SZAZALEK;
		$apsz = $akcios_plus_szaz / 100 + 1;
		$q = "SELECT
			t.ID,
			t.nev,
			t.akcios,
			t.szuper_akcios,
			t.ujdonsag,
			t.szuper_akcios_szazalek,
			t.utolso_darab,
			IF(t.akcios,
				IF(t.akcios_egyedi_brutto_ar != 0,
					t.akcios_egyedi_brutto_ar,
					getTermekAr(t.marka, (t.brutto_ar * ".$apsz."))),
				getTermekAr(t.marka, t.brutto_ar)
			) as brutto_ar,
			IF(t.egyedi_ar IS NOT NULL,
				t.egyedi_ar,
				getTermekAr(t.marka, IF(t.akcios,t.akcios_brutto_ar,t.brutto_ar))
			) as ar,
			getTermekUrl(t.ID,'".DOMAIN."') as url,
			FULLIMG(t.profil_kep) as profil_kep,
			TRIM(SUBSTRING_INDEX(m.neve,'::',1)) as markaNev
		FROM shop_termekek as t
		LEFT OUTER JOIN shop_markak as m ON m.ID = t.marka
		WHERE t.ID IS NOT NULL and
		t.lathato = 1 and
		t.szuper_akcios = 1
		ORDER BY getTermekAr(t.marka,IF(t.egyedi_ar IS NOT NULL,t.egyedi_ar,IF(t.akcios,t.akcios_brutto_ar,t.brutto_ar))) ASC";
		$arg[multi] = '1';
		extract($this->db->q($q,$arg));

		$keys = array();

		foreach($data as $d){
			$keys[] = $d[ID];
		}
		$ret[keys] = $keys;
		return $ret;
	}
	public function getTermekAdat($id, $arg = array()){
		$akcios_plus_szaz = AKCIOS_BRUTTO_AR_PLUSZ_SZAZALEK;
		$apsz = $akcios_plus_szaz / 100 + 1;

		$re = array();
		if($id == '') return false;
		// getTermekAr(t.marka,IF(t.egyedi_ar IS NOT NULL,t.egyedi_ar,IF(t.akcios,t.akcios_brutto_ar,t.brutto_ar))) as ar,
		$q = "SELECT
			t.* ,
			m.elorendelheto,
			getTermekUrl(t.ID, '".DOMAIN."') as url,
			IF(t.akcios,
				IF(t.akcios_egyedi_brutto_ar != 0,
					t.akcios_egyedi_brutto_ar,
					getTermekAr(t.marka, (t.brutto_ar * ".$apsz."))),
				getTermekAr(t.marka, t.brutto_ar)
			) as brutto_ar,
			IF(t.egyedi_ar IS NOT NULL,
				t.egyedi_ar,
				getTermekAr(t.marka, IF(t.akcios,t.akcios_brutto_ar,t.brutto_ar))
			) as ar,
			TRIM(SUBSTRING_INDEX(m.neve,'::',1)) as markaNev,
			ta.elnevezes as allapotNev,
			ts.elnevezes as szallitasNev
		FROM shop_termekek as t
		LEFT OUTER JOIN shop_markak as m ON m.ID = t.marka
		LEFT OUTER JOIN shop_termek_allapotok as ta ON ta.ID = t.keszletID
		LEFT OUTER JOIN shop_szallitasi_ido as ts ON ts.ID = t.szallitasID
		WHERE
			t.ID = $id
		";

		$data = $this->db->query($q);

		if($data->rowCount() == 0) return false;

		$re[data] = $data->fetch(\PDO::FETCH_ASSOC);
		$re[key_params] = $this->getKeyParameter($re[params]);
		$re[images] = $this->getAllTermekImg($id);

		return $re;
	}
	function getKeyParameter($params_arry){
		$ret = array();

		if(count($params_arry) == 0) return $ret;

		foreach($params_arry as $p){
			if($p[kulcs] == '1'){
				$ret[] = $p;
			}
		}

		return $ret;
	}
	function getAllTermekImg($termekID){
		$imgs = array();
		if($termekID == '') return $imgs;
		$q = "SELECT FULLIMG(kep) as kep FROM shop_termek_kepek WHERE termekID = $termekID ORDER BY sorrend ASC, kep ASC";
		extract($this->db->q($q,array('multi'=> '1')));

		foreach($data as $i){
			$imgs[] = $i[kep];
		}

		return $imgs;
	}
	public function getAkciosTermekek($arg = array()){
	$akcios_plus_szaz = AKCIOS_BRUTTO_AR_PLUSZ_SZAZALEK;
	$apsz = $akcios_plus_szaz / 100 + 1;

		$q = "SELECT
			t.ID,
			t.nev,
			t.akcios,
			t.szuper_akcios,
			t.ujdonsag,
			t.szuper_akcios_szazalek,
			t.utolso_darab,
			IF(t.akcios,
				IF(t.akcios_egyedi_brutto_ar != 0,
					t.akcios_egyedi_brutto_ar,
					getTermekAr(t.marka, (t.brutto_ar * ".$apsz."))),
				getTermekAr(t.marka, t.brutto_ar)
			) as brutto_ar,
			IF(t.egyedi_ar IS NOT NULL,
				t.egyedi_ar,
				getTermekAr(t.marka, IF(t.akcios,t.akcios_brutto_ar,t.brutto_ar))
			) as ar,
			getTermekUrl(t.ID,'".DOMAIN."') as url,
			FULLIMG(t.profil_kep) as profil_kep,
			TRIM(SUBSTRING_INDEX(m.neve,'::',1)) as markaNev
		FROM shop_termekek as t
		LEFT OUTER JOIN shop_markak as m ON m.ID = t.marka
		WHERE t.ID IS NOT NULL and
		t.lathato = 1 and
		t.akcios = 1 and
		t.szuper_akcios = 0 and
		t.ujdonsag = 0
		ORDER BY getTermekAr(t.marka,IF(t.egyedi_ar IS NOT NULL,t.egyedi_ar,IF(t.akcios,t.akcios_brutto_ar,t.brutto_ar))) ASC";
		$arg[multi] = '1';
		extract($this->db->q($q,$arg));

		$keys = array();

		foreach($data as $d){
			$keys[] = $d[ID];
		}
		$ret[keys] = $keys;
		return $ret;
	}
	public function getUjdonsagTermekek($arg = array()){
		$akcios_plus_szaz = AKCIOS_BRUTTO_AR_PLUSZ_SZAZALEK;
		$apsz = $akcios_plus_szaz / 100 + 1;

		$q = "SELECT
			t.ID,
			t.nev,
			t.akcios,
			t.szuper_akcios,
			t.ujdonsag,
			t.szuper_akcios_szazalek,
			t.utolso_darab,
			IF(t.akcios,
				IF(t.akcios_egyedi_brutto_ar != 0,
					t.akcios_egyedi_brutto_ar,
					getTermekAr(t.marka, (t.brutto_ar * ".$apsz."))),
				getTermekAr(t.marka, t.brutto_ar)
			) as brutto_ar,
			IF(t.egyedi_ar IS NOT NULL,
				t.egyedi_ar,
				getTermekAr(t.marka, IF(t.akcios,t.akcios_brutto_ar,t.brutto_ar))
			) as ar,
			getTermekUrl(t.ID,'".DOMAIN."') as url,
			FULLIMG(t.profil_kep) as profil_kep,
			TRIM(SUBSTRING_INDEX(m.neve,'::',1)) as markaNev
		FROM shop_termekek as t
		LEFT OUTER JOIN shop_markak as m ON m.ID = t.marka
		WHERE t.ID IS NOT NULL and
		t.lathato = 1 and
		t.ujdonsag = 1
		ORDER BY rand() DESC";
		$arg[multi] = '1';
		extract($this->db->q($q,$arg));

		$keys = array();

		foreach($data as $d){
			$keys[] = $d[ID];
		}
		$ret[keys] = $keys;
		return $ret;
	}
	public function getTermekParamList(){
		$r = array();
		$q = "SELECT ID,parameter,mertekegyseg FROM shop_termek_kategoria_parameter";

		$arg[multi] = '1';
		extract($this->db->q($q,$arg));

		foreach($data as $d){
			$r[$d[ID]] = $d;
		}

		return $r;
	}
	public function getMegrendelesAllapotok(){
		$q = "SELECT * FROM order_allapot ORDER BY sorrend ASC";

		extract($this->db->q($q,array('multi'=>'1')));

		$back = array();
		foreach($data as $d){
			$back[$d[ID]] = $d;
		}

		return $back;
	}
	public function kereses($arg = array()){
		$akcios_plus_szaz = AKCIOS_BRUTTO_AR_PLUSZ_SZAZALEK;
		$apsz = $akcios_plus_szaz / 100 + 1;

		$back 	= array();
		$params = $this->getTermekParamList();
		$arg[orderByPriority] = true;

		$q = "SELECT
			t.ID,
			tik.modszerID,
			tik.gyujtoID,
			t.termek_kategoria,
			t.szuper_akcios_szazalek,
			t.utolso_darab,
			IF(t.akcios,
				IF(t.akcios_egyedi_brutto_ar != 0,
					t.akcios_egyedi_brutto_ar,
					getTermekAr(t.marka, (t.brutto_ar * ".$apsz."))),
				getTermekAr(t.marka, t.brutto_ar)
			) as brutto_ar,
			IF(t.egyedi_ar IS NOT NULL,
				t.egyedi_ar,
				getTermekAr(t.marka, IF(t.akcios,t.akcios_brutto_ar,t.brutto_ar))
			) as ar,
			t.akcios_brutto_ar,
			t.termek_kategoria,
			t.akcios,
			t.marka,
			TRIM(SUBSTRING_INDEX(m.neve,'::',1)) as markaNev,
			t.ujdonsag,
			t.szuper_akcios,
			t.egyedi_ar,
			getTermekUrl(t.ID,'".DOMAIN."') as url,
			t.pickpackszallitas,
			t.nev as termekNev,
			ta.elnevezes as keszlet,
			tk.neve as kategoriaNev,
			getTermekParamString(t.ID,t.termek_kategoria) as params,
			FULLIMG(t.profil_kep) as profil_kep
		FROM shop_termekek as t
		LEFT OUTER JOIN shop_termek_parameter as p ON p.termekID = t.ID
		LEFT OUTER JOIN shop_termek_in_kategoria as tik ON tik.termekID = t.ID
		LEFT OUTER JOIN shop_markak as m ON m.ID = t.marka
		LEFT OUTER JOIN shop_termek_allapotok as ta ON ta.ID = t.keszletID
		LEFT OUTER JOIN shop_termek_kategoriak as tk ON tk.ID = t.termek_kategoria
		WHERE
		";
		$q .= "
			t.ID is NOT NULL and
			t.lathato = 1
		";
		if($arg[search] != ''){
			$q .= " and CONCAT(TRIM(SUBSTRING_INDEX(m.neve,'::',1)),' ',t.nev) LIKE '%".$arg[search]."%' ";
		}
		if($arg[inKat][0]){
			$q .= " and t.termek_kategoria IN (".implode(",",$arg[inKat]).") ";
		}
		if($arg[inMarka][0]){
			$q .= " and t.marka IN (".implode(",",$arg[inMarka]).") ";
		}

		if($arg[akcio] == '1'){
			$q .= " and t.akcios = 1 and t.ujdonsag = 0 and t.szuper_akcios = 0";
		}
		if($arg[superakcio] == '1'){
			$q .= " and (t.szuper_akcios = 1 and t.akcios = 1)";
		}
		if($arg[ujdonsag] == '1'){
			$q .= " and t.ujdonsag = 1";
		}

		$q .= " GROUP BY t.ID ";

		// ORDER
		$order = 't.nev ASC';
		if($arg[order] == '' || $arg[order] == 'abc_asc'){
			$order = 't.nev ASC';
		}else if($arg[order] == 'abc_desc'){
			$order = 't.nev DESC';
		}else if($arg[order] == 'price_asc'){
			$order = 'ar ASC';
		}else if($arg[order] == 'price_desc'){
			$order = 'ar DESC';
		}

		$q .= " ORDER BY ".$order;

		$arg[multi] = '1';
		extract($this->db->q($q,$arg));
		$r = array();

		foreach($data as $d){
			$inp = $this->getTermekParameter($d[ID],$d[termek_kategoria],$arg);

			$d[params] = $inp;
			$r[] = $d;
		}

		$ret[data] = $r;
		$back = $ret;

		return $back;
	}
	private function getPrinceInfo($where, $info_arry){
		if($where == '')return $info_arry;
		if(count($info_arry) == 0) return $info_arry;
		$min = 0;
		$max = 0;

		$q = "SELECT getTermekAr(t.marka,IF(t.egyedi_ar IS NOT NULL,t.egyedi_ar,IF(t.akcios,t.akcios_brutto_ar,t.brutto_ar))) as ar FROM shop_termekek as t LEFT OUTER JOIN shop_termek_in_kategoria as tik ON tik.termekID = t.ID WHERE ".$where." GROUP BY t.ID";
		extract($this->db->q($q));

		foreach($data as $d){
			$ar = $d[ar];

			if($min == 0) $min = $ar;
			if($max == 0) $max = $ar;

			if($ar < $min) $min = $ar;
			if($ar > $max) $max = $ar;
		}

		$info_arry[min] = $min;
		$info_arry[max] = $max;

		return $info_arry;
	}

	/**
	* Nem előrendelhető termékek eltávolítása a felhasználó kosarából
	*
	* @param integer $mid Felhasználó gép ID-ja. Hívás \Helper::getMachineID()
	*
	* @return void
	*/
	public function clearCartForPreorder ($mid)
	{
		if($mid == '') return false;

		$remove = array();

		//Kosár
		$arg 	= array( 'multi' => 1 );
		$q 		= "
		SELECT 				c.termekID,
							m.elorendelheto
		FROM 				shop_kosar as c
		LEFT OUTER JOIN 	shop_termekek as t ON t.ID = c.termekID
		LEFT OUTER JOIN 	shop_markak as m ON m.ID = t.marka
		WHERE  				c.gepID = '$mid'
		";
		extract($this->db->q($q, $arg));

		foreach( $data as $d ){

			if( $d[elorendelheto] == '0' ){
				$remove[] = $d[termekID];
				$this->db->query("DELETE FROM shop_kosar WHERE gepID = '$mid' and termekID = ".$d[termekID]);
			}

		}

		return $remove;
	}

	public function addItemToCart($mid, $termekID){
		if($mid == '')
			throw new \Exception('Nem sikerült hozzáadni a terméket a kosárhoz. Frissítse le az oldalt és próbálja újra!');
		if($termekID == '')
			throw new \Exception('Termék azonosító hiányzik!');

		$c = $this->db->query("SELECT me FROM shop_kosar WHERE termekID = $termekID and gepID = $mid;")->fetch(\PDO::FETCH_ASSOC);

		$this->db->query("UPDATE shop_kosar SET me = me + 1  WHERE termekID = $termekID and gepID = $mid");
	}
	public function clearCart($mid){
		if($mid == '') return false;
		$this->db->query("DELETE FROM shop_kosar WHERE gepID = $mid");
	}
	public function removeItemFromCart($mid, $termekID){
		if($mid == '')
			throw new \Exception('Nem sikerült hozzáadni a terméket a kosárhoz. Frissítse le az oldalt és próbálja újra!');
		if($termekID == '')
			throw new \Exception('Termék azonosító hiányzik!');

		$c = $this->db->query("SELECT me FROM shop_kosar WHERE termekID = $termekID and gepID = $mid;")->fetch(\PDO::FETCH_ASSOC);

		$cn = $c[me];

		if($cn == 1){
			$this->db->query("DELETE FROM shop_kosar WHERE termekID = $termekID and gepID = $mid");
		}else if($cn > 1){
			$this->db->query("UPDATE shop_kosar SET me = me - 1  WHERE termekID = $termekID and gepID = $mid");
		}
	}

	public function addToCart($mid, $termekID, $me, $config){
		if($mid == '')
		throw new \Exception('Nem sikerült hozzáadni a terméket a kosárhoz. Frissítse le az oldalt és próbálja újra!');


		$cq = "SELECT ID FROM shop_kosar WHERE termekID = $termekID and gepID = $mid";
		if ($config == '' || empty($config) || !isset($config)) {
			$cq .= " and configs IS NULL";
		} else {
			$cq .= " and configs = '{$config}'";
		}
		$ci = $this->db->query( $cq );

		if($ci->rowCount() == 0){
			//$c = $ci->fetch(\PDO::FETCH_ASSOC);
			$this->db->insert('shop_kosar',
				array(
					'termekID' => $termekID,
					'gepID' => $mid,
					'me' => $me,
					'configs' => ($config == '' || empty($config) || !isset($config)) ? NULL : $config
				)
			);

		}else{
			$uq = "UPDATE shop_kosar SET me = me + $me  WHERE termekID = $termekID and gepID = $mid";
			if ($config == '' || empty($config) || !isset($config)) {

			} else {
				$cq .= " and configs = '{$config}'";
			}
			$this->db->query($uq);
		}

	}

	public function removeFromCart($mid, $cartID){
		if($mid == '')
			throw new \Exception('Nem sikerült hozzáadni a terméket a kosárhoz. Frissítse le az oldalt és próbálja újra!');
		if($cartID == '')
			throw new \Exception('Termék azonosító hiányzik!');
		$q = "DELETE FROM shop_kosar WHERE ID = $cartID and gepID = $mid";
		$this->db->query($q);
	}

	public function getSzallitasiModok(){
		$q = "SELECT * FROM shop_szallitasi_mod WHERE allapot = 1";
		extract($this->db->q($q,array('multi'=> '1')));
		return $data;
	}

	public function cartInfo( $mid, $arg = array() ){
		$re 		= array();
		$kedvezmeny = 0;
		$itemNum 	= 0;
		$totalPrice = 0;
		$totalPrice_before_discount = 0;
		$disc_partner = false;
		$disc_coupon = false;
		$no_cetelem_items = array(
			'total' => 0,
			'items' => array()
		);

		$uid = (int)$this->user[data][ID];

		if($mid == '') return false;

		if( $arg['coupon'] ) {
			$coupon = $arg['coupon'];
			$disc_coupon = $coupon->getCode();
		}

		// Clear cart if item num 0
		$this->db->query("DELETE FROM shop_kosar WHERE me <= 0 and gepID = $mid;");

		$q = "SELECT
			c.ID,
			c.termekID,
			c.me,
			c.hozzaadva,
			c.configs,
			t.pickpackszallitas,
			t.no_cetelem,
			m.elorendelheto,
			t.nev as termekNev,
			t.szin,
			t.meret,
			t.raktar_keszlet,
			t.mertekegyseg,
			t.mertekegyseg_ertek,
			IF(t.raktar_keszlet = 0, false, IF(t.raktar_keszlet = 1, 'utolsó darab', CONCAT(t.raktar_keszlet,' db'))) as keszlet_data,
			t.raktar_articleid,
			getTermekUrl(t.ID,'".DOMAIN."') as url,
			ta.elnevezes as allapot,
			t.profil_kep,
			getTermekAr(c.termekID, ".$uid.") as ar,
			(getTermekAr(c.termekID, ".$uid.") * c.me) as sum_ar,
			t.referer_price_discount,
			szid.elnevezes as szallitasIdo
		FROM shop_kosar as c
		LEFT OUTER JOIN shop_termekek AS t ON t.ID = c.termekID
		LEFT OUTER JOIN shop_markak as m ON m.ID = t.marka
		LEFT OUTER JOIN shop_termek_allapotok as ta ON ta.ID = t.keszletID
		LEFT OUTER JOIN shop_szallitasi_ido as szid ON szid.ID = t.szallitasID
		WHERE c.gepID = $mid";
		$arg[multi] = '1';
		extract($this->db->q($q, $arg));
		$dt = array();

		$kedvezmenyes = false;
		if( $this->user && $this->user[kedvezmeny] > 0 ) {
			$kedvezmenyes = true;
		}

		foreach($data as $d)
		{
			$d['mertekegyseg'] = trim($d['mertekegyseg']);
			$d['configs'] = $this->collectConfigData($d['configs']);

			if ($this->settings['round_price_5'] == '1')
			{
				$d[ar] = round($d[ar] / 5) * 5;
			}



			if ($d['no_cetelem'] == '1')
			{
				$no_cetelem_items['total']++;
				$no_cetelem_items['items'][] = $d;
			}

			$d['prices'] = array(
				'old_each' 		=> 0,
				'old_sum' 		=> 0,
				'current_each' 	=> $d[ar],
				'current_sum' 	=> $d[ar] * $d[me]
			);

			/*
			if( $this->user['kedvezmeny'] > 0 ) {
				\PortalManager\Formater::discountPrice( $d[ar], $this->user[kedvezmeny] );
				\PortalManager\Formater::discountPrice( $d[sum_ar], $this->user[kedvezmeny] );
			}
			*/

			if( $arg['referer_discount'] )
			{
				$d['prices']['old_each'] 	= $d[ar];
				$d['prices']['old_sum'] 	= $d[ar] * $d[me];

				$totalPrice_before_discount += $d[ar] * $d[me];

				$d[ar] 		= 	( $d['ar'] - $d['referer_price_discount'] );
				//$kedvezmeny 	+= 	$d['referer_price_discount'] * $d['me'];
				$disc_partner 	= true;

				$d['prices']['current_each'] 	= $d['ar'];
				$d['prices']['current_sum'] 	= $d['ar'] * $d['me'];
			}

			// Kupon, ha termékekre van
			if( $coupon && !$coupon->isOffForAllProduct() )
			{
				// Ha bizonyos termékcsalád részesül csak kedvezményben
				if( $coupon->isProductExluded() )
				{
					if( $coupon->thisProductAllowed( $d['raktar_articleid'] ) )
					{
						$d['prices']['old_each'] 		= $d[ar];
						$d['prices']['old_sum'] 		= $d[ar] * $d[me];

						$d['prices']['current_each'] 	= $coupon->discountPrice( $d['ar'] );
						$d['prices']['current_sum'] 	= $coupon->discountPrice( $d['ar'] )  * $d['me'];
					}
				}

			}

			$itemNum 	+= $d[me];
			$totalPrice += $d[ar] * $d[me];


			$d['profil_kep'] = \PortalManager\Formater::productImage( $d['profil_kep'], false );

			if( $d['prices']['old_each'] != 0 && $d['prices']['old_each'] != $d['prices']['current_each']  )
			{
				$d['discounted'] 	= true;
				$kedvezmeny 		+= $d['prices']['old_sum'] - $d['prices']['current_sum'];
			} else {
				$d['discounted'] 		= false;
			}

			$d['mertekegyseg_egysegar'] = \ProductManager\Products::calcEgysegAr($d['mertekegyseg'], $d['mertekegyseg_ertek'], $d['ar']);

			$dt[] = $d;
		}

		if( $coupon && $coupon->isOffForAllProduct() )
		{
			$kedvezmeny = $coupon->calcPrice( $totalPrice );
			$totalPrice_before_discount = $totalPrice;
			$totalPrice = $totalPrice - $kedvezmeny;
		}
		else if($coupon)
		{
			$totalPrice_before_discount = $totalPrice;
			$totalPrice = $totalPrice - $kedvezmeny;
		}

		$re[itemNum]			= $itemNum;
		$re[kedvezmeny]			= $kedvezmeny;
		$re[totalPrice]			= $totalPrice;
		$re[discount][partner]	= $disc_partner;
		$re[discount][coupon]	= $disc_coupon;
		$re[totalPriceTxt]		= number_format($totalPrice,0,""," ");
		$re[totalPrice_before_discount]			= $totalPrice_before_discount;
		$re[totalPriceTxt_before_discount]		= number_format($totalPrice_before_discount,0,""," ");
		$re[excludes_from_cetelem]	= $no_cetelem_items;
		$re[items] 				= $dt;

		return $re;
	}

	public function collectConfigData( $rawconfig )
	{
		if ($rawconfig == '') {
			return false;
		}
		parse_str($rawconfig, $configs);

		if (count($configs) == 0) {
			return false;
		}

		$list = array();
		foreach ((array)$configs as $cp => $cv) {
			$paramid = (int)str_replace("p","",$cp);
			$value = $this->db->squery("SELECT
				c.config_value as value,
				c.parameter_id,
				p.parameter as nev
			FROM shop_termek_variation_configs as c
			LEFT OUTER JOIN shop_termek_kategoria_parameter as p ON p.ID = c.parameter_id
			WHERE 1=1 and c.ID = :id
			ORDER BY CAST(p.priority as unsigned) ASC
			", array('id' => $cv));
			if ($value->rowCount() != 0) {
				$value = $value->fetch(\PDO::FETCH_ASSOC);
				$list[$paramid] = array(
					'ID' => (int)$cv,
					'param_id' => $paramid,
					'parameter' => $value['nev'],
					'value' => $value['value']
				);
			}

		}

		return $list;
	}

	public function getFilterString($filter_arry){
		$ret = '';

		if(count($filter_arry) > 0){
			$ret .= '?';
			foreach($filter_arry as $fk => $fv){
				if($fv){
					$n = count($fv);

					if($n == 1){
						$d = $fv[0];
					}else{
						$d = implode(urlencode(","),$fv);
					}
					$ret .= $fk.'='.$d.'&amp;';
				}
			}
		}
		$ret = rtrim($ret,'&amp;');

		return $ret;
	}

	public function getFilters($get, $prefix){
		$re = array();

		foreach($get as $gk => $gv){
			if(strpos($gk,$prefix.'_') === 0){
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
				if($gv != ''){
					$x = explode(',',rtrim($gv,','));
				}
				$g = $x;
				if($g == '') $g = false;
				$re[$gk] = $g;
			}
		}

		return $re;
	}

	function getTermekParameter($termekID, $katID = false, $arg = array()){
		$q = "SELECT
			p.* ,
			pm.parameter as neve,
			pm.mertekegyseg as me,
			pm.kulcs
		FROM shop_termek_parameter as p
		LEFT OUTER JOIN shop_termek_kategoria_parameter as pm ON pm.ID = p.parameterID
		 WHERE p.termekID = $termekID ";
		if($katID){
			$q .= " and katID = $katID ";
		}
		if($arg[orderByPriority]){
			$q .= "
			ORDER BY pm.priority ASC, pm.parameter ASC";
		}else{
			$q .= "
			ORDER BY pm.parameter ASC";
		 }
		extract($this->db->q($q,array('multi'=> '1')));
		$back = array();
		foreach($data as $d){
			$back[$d[parameterID]] = $d;
		}
		return $back;
	}

	private function getTermekAr($markaID, $bruttoAr){
		$ari =  $this->getTermekArInfo($markaID, $bruttoAr);
		return $ari[ar];
	}

	public function getMarkak($arg = array()){
		$rows 	=  'ID, TRIM(SUBSTRING_INDEX(neve,"::",1)) as neve,GROUP_CONCAT(ID) as IDS, arres_mod, fix_arres, brutto';
		$q 		= "SELECT $rows FROM shop_markak GROUP BY TRIM(SUBSTRING_INDEX(neve,'::',1));";

		extract($this->db->q($q,array('multi'=> '1')));

		$back = array();
		foreach($data as $d){
			$back[] = $d;
		}
		return $back;
	}

	public function getParameterHint($modszer = false, $gyujto = false){
		$hints = array();

		$h = "SELECT
			GROUP_CONCAT(CONCAT('p_',p.parameterID,':',p.ertek)) as paramErtek
		FROM `shop_termekek` as t
		LEFT OUTER JOIN shop_termek_parameter as p ON p.termekID = t.ID
		LEFT OUTER JOIN shop_termek_in_kategoria as tik ON tik.termekID = t.ID
		WHERE tik.modszerID = $modszer
		GROUP BY t.ID";

		extract($this->db->q($h,array('multi'=> '1')));
		$box = array();
		foreach($data as $d){
			$exp_one = explode(",",$d[paramErtek]);
			foreach($exp_one as $e){
				$exp_two 	= explode(":",$e);
				$key 		= str_replace('p_','',$exp_two[0]);

				if(!in_array($exp_two[1],$box[$key])){
					$box[$key][] = $exp_two[1];
				}

			}
		}
		$data = $box;

		$hints = $data;

		return $hints;
	}

	private function getTermekArInfo($markaID, $bruttoAr){
		$re 	  = array();
		$re[info] =  array();
		$re[arres] = 0;
		$re[ar]   =  $bruttoAr;

		// Márka adatok
		$marka = $this->db->query("SELECT fix_arres FROM shop_markak WHERE ID = $markaID")->fetch(\PDO::FETCH_ASSOC);

		if(!is_null($marka[fix_arres])){
		// Fix árrés
			$re[info] 	= 'FIX : '.$marka[fix_arres].'%';
			$re[arres] 	= $marka[fix_arres];
			$re[ar] 	= round($bruttoAr * ($marka[fix_arres]/100+1));
		}else{
		// Sávos árrés
			$savok = $this->db->query("SELECT ar_min, ar_max, arres FROM shop_marka_arres_savok WHERE markaID = $markaID ORDER BY ar_min ASC")->fetchAll(\PDO::FETCH_ASSOC);
			foreach($savok as $s){
				$min = $s[ar_min];
				$max = $s[ar_max];
				$max = (is_null($max)) ? 999999999999999 : $max;

				if($bruttoAr >= $min && $bruttoAr <= $max){
					$re[info] 	= $min.' - '.$max.' : '.$s[arres].'%';
					$re[arres] 	= $s[arres];
					$re[ar] 	= round($bruttoAr * ($s[arres]/100+1));
					break;
				}else{
					$re[info] 	= $min.' - '.$max.' : '.$s[arres].'%';
					$re[arres] 	= $s[arres];
					$re[ar] 	= round($bruttoAr * ($s[arres]/100+1));
				}

			}
		}

		return $re;
	}


	public function getTermekKeys(&$view){
		$get 	= \Helper::GET();
		$szurok = array();

		if($get[0] == 'termekek'){
			$view->kategoria->modszer 	= $this->getStringID($get[1]);
			$view->kategoria->gyujto 	= $this->getStringID($get[2]);
			$view->kategoria->termek 	= $this->getStringID($get[3]);

			$view->kategoria->modszer_text 	= $this->getKategoriaStringByID('modszer', $view->kategoria->modszer);
			$view->kategoria->gyujto_text 	= $this->getKategoriaStringByID('gyujto', $view->kategoria->gyujto);
			$view->kategoria->termek_text 	= $this->getKategoriaStringByID('termek', $view->kategoria->termek);

			if($view->kategoria->modszer && ($view->kategoria->gyujto || $view->kategoria->termek))
				$navi =  $view->kategoria->modszer_text;
			if($view->kategoria->modszer && $view->kategoria->gyujto && $view->kategoria->termek)
				$navi .=  ' / '.$view->kategoria->gyujto_text;


			if($view->kategoria->modszer)
			$onKat = $view->kategoria->modszer_text;
			if($view->kategoria->gyujto)
			$onKat = $view->kategoria->gyujto_text;
			if($view->kategoria->termek)
			$onKat = $view->kategoria->termek_text;

			if($view->kategoria->termek){
				$szurok = $this->getParamsForFilter($view->kategoria->termek,array(
					'modszer' 	=> $view->kategoria->modszer,
					'gyujto' 	=> $view->kategoria->gyujto
				));
			}

			$view->szuroParams 				= $szurok;
			$view->markak 					= $this->getMarkak(array('row' => '*'));
			$view->kategoria->navi_text 	= ($navi == '') ? 'Kérjük, hogy válasszon módszert és kategóriákat a pontosabb szűrésért.' : $navi;
			$view->kategoria->ON 			= ($onKat == '') ? 'Termékek' : $onKat;

		}else if($get[0] == 't'){
			$view->kategoria->modszer 	= $_COOKIE[__list_modszer];
			$view->kategoria->gyujto 	= $_COOKIE[__list_gyujto];
			$view->kategoria->termek 	= $_COOKIE[__list_kategoria];

			$view->termekID = \Product::getTermekIDFromUrl();
		}

		return $view;
	}
	public function getParamsForFilter($katID = false, $arg = array()){
		$back = array();
		if(!$katID) return $back;
		$modszer 	= ($arg[modszer]) 	? $arg[modszer] : false;
		$gyujto 	= ($arg[gyujto]) 	? $arg[gyujto] : false;

		$q = "SELECT * FROM `shop_termek_kategoria_parameter` WHERE kategoriaID = $katID ORDER BY parameter ASC;";

		$arg[multi] = '1';
		extract($this->db->q($q,$arg));

		$loadParams = $this->getParameterHint($modszer, $gyujto);

		foreach($data as $d){
			$hint 		= $loadParams[$d[ID]];
			$type 		= $this->getParameterType($d, $hint);
			asort($hint);

			$d[hints]   	= $hint;
			$d[type] 		= $type;
			$d[minmax] 		= $this->getParameterMinMax($d[type],$hint);
			$back[] 		= $d;
		}

		return $back;
	}

	private function getParameterMinMax($type, $hints){
		$re 	= array();
		$min 	= 0;
		$max 	= 0;

		if($type == 'szam' || $type == 'tartomany'){
			foreach($hints as $h){
				if($type == 'szam'){
					if($min == 0) $min = $h;
					if($max == 0) $max = $h;

					if($h < $min) $min = $h;
					if($h > $max) $max = $h;
				}else if($type == 'tartomany'){
					$xn 	= explode('-',$h);
					$xmin 	= (int)$xn[0];
					$xmax	= (int)$xn[1];

					if($min == 0) $min = $xmin;
					if($max == 0) $max = $xmax;

					if($xmin < $min) $min = $xmin;
					if($xmax > $max) $max = $xmax;
				}
			}
		}

		$re[min] = $min;
		$re[max] = $max;

		return $re;
	}

	private function getParameterType($parameter, $hints){
		$re = false;

		if($parameter[mertekegyseg] == ''){
			$re = 'szoveg';
		}else{
			$re = 'szoveg';
			foreach($hints as $h){
				if(is_numeric($h)){
					$re = 'szoveg';
					if($parameter[is_range] == 1){
						$re = 'szam';
					}
				}else{
					if(strpos($h,'-') !== false){
						$re = 'tartomany';
					}
				}
			}
		}


		return $re;
	}
	private function getKategoriaStringByID($type, $id){
		$str = false;
		if($id == '') return $str;
		switch($type){
			case 'modszer':
				$str = \Product::clear($this->db->query("SELECT neve FROM shop_modszerek WHERE ID = $id")->fetchColumn());
			break;
			case 'gyujto':
				$str = \Product::clear($this->db->query("SELECT neve FROM shop_gyujto_kategoriak WHERE ID = $id")->fetchColumn());
			break;
			case 'termek':
				$str = \Product::clear($this->db->query("SELECT neve FROM shop_termek_kategoriak WHERE ID = $id")->fetchColumn());
			break;
		}
		return $str;
	}
	private function getStringID($str){
		$x = explode("_-",$str);
		if(count($x) > 1){
			$id = $x[1];
		}else return false;
		return $id;
	}
	public function getTermekKategoriak(){
		$q = "SELECT * FROM shop_termek_kategoriak ORDER BY neve ASC;";

		$arg[multi] = '1';
		extract($this->db->q($q,$arg));
		return $data;
	}
	public function getUsableModszerek($arg = array()){
		$selectedModszer = ($arg[shopInfo]->kategoria->modszer) ? $arg[shopInfo]->kategoria->modszer : false;

		$q = "SELECT
		  ik.modszerID,
		  m.neve as modszer,
		  count(ik.id) as tct
		FROM `shop_termek_in_kategoria` as ik
		LEFT OUTER JOIN shop_termekek as t ON t.id = ik.termekID
		LEFT OUTER JOIN shop_modszerek as m ON m.id = ik.modszerID
		WHERE
			t.lathato = 1
		GROUP BY ik.modszerID
		ORDER BY m.neve ASC;";

		$arg[multi] = '1';
		extract($this->db->q($q,$arg));

		$re 	= array();
		$select = array();
		$index 	= 0;

		if($selectedModszer) $index = 1;
		foreach($data as $d){
			$d[gyujtok] 		= array();
			$d[kategoriak] 		= array();

				// Gyűjtők csatolása
				if(count($arg[gyujtok]) > 0){
					foreach($arg[gyujtok] as $gy){
						if($d[modszerID] == $gy[modszerID]){
							$gy[kategoriak] = $this->getUsedGyujtoKategoriak($d[modszerID], $gy[gyujtoID]);
							$d[gyujtok][] = $gy;
						}
					}
				}
				// Elérhető kategóriák csatolása
				if(count($d[gyujtok]) == 0){
					$d[kategoriak] = $this->getUsedKategoriak($d[modszerID]);
				}
			if($selectedModszer == $d[modszerID]){
				$select = $d;
			}else{
				$re[$index] = $d;
			}
			$index++;
		}
		if($selectedModszer && !empty($select)){
			$re[0] = $select;
		}
		ksort($re);
		return $re;
	}
	private function getUsedKategoriak($modszerID = false, $arg = array()){
		$re = array();
		$gyujto = ($arg[gyujtoID]) ? $arg[gyujtoID] : false;

		if(!$modszerID) return $re;

		$q = "SELECT
		  k.ID as kategoriaID,
		  k.neve as kategoriaNev,
		  count(t.id) as tct
		FROM `shop_termek_in_kategoria` as ik
		LEFT OUTER JOIN shop_termekek as t ON t.id = ik.termekID
		LEFT OUTER JOIN shop_termek_kategoriak as k ON k.id = t.termek_kategoria
		WHERE
			ik.modszerID = $modszerID and";
		if($gyujto){
			$q .= " ik.gyujtoID = $gyujto and ";
		}
		$q .= "
			t.lathato = 1 and
			t.termek_kategoria IS NOT NULL
		GROUP BY t.termek_kategoria
		ORDER BY k.neve ASC;";

		$arg[multi] = '1';
		extract($this->db->q($q,$arg));

		$ret = $data;

		return $ret;
	}
	private function getUsedGyujtoKategoriak($modszerID = false, $gyujtoID = false){
		$re = array();
		if(!$modszerID || !$gyujtoID) return $re;

		$q = "SELECT
		  k.ID as kategoriaID,
		  k.neve as kategoriaNev,
		  count(t.id) as tct
		FROM `shop_termek_in_kategoria` as ik
		LEFT OUTER JOIN shop_termekek as t ON t.id = ik.termekID
		LEFT OUTER JOIN shop_termek_kategoriak as k ON k.id = t.termek_kategoria
		WHERE
			ik.modszerID = $modszerID and
			ik.gyujtoID = $gyujtoID and ";
		$q .= "
			t.lathato = 1 and
			t.termek_kategoria IS NOT NULL
		GROUP BY t.termek_kategoria
		ORDER BY k.neve ASC;";

		$arg[multi] = '1';
		extract($this->db->q($q,$arg));

		$ret = $data;
		return $ret;
	}
	public function getUsableGyujtok($arg = array()){
		$q = "SELECT
			tk.*,
			gyk.neve as gyujtoNev,
			count(tk.ID) as tct
		FROM `shop_termek_in_kategoria` as tk
		LEFT OUTER JOIN shop_termekek as t ON tk.termekID = t.ID
		LEFT OUTER JOIN shop_gyujto_kategoriak as gyk ON gyk.ID = tk.gyujtoID
		WHERE
			gyk.neve IS NOT NULL and
			t.lathato = 1
		GROUP BY tk.gyujtoID
		ORDER BY
			gyk.neve ASC";

		$arg[multi] = '1';
		extract($this->db->q($q,$arg));

		return $data;
	}
	public function sendKapcsolatMsg($post){
		extract($post);

		if(!Captcha::verify())throw new \Exception('Az ellenőrző kód hibás!');

		if($name == '') throw new \Exception('Kérjük, ne felejtse megadni saját nevét!');
		if($email == '') throw new \Exception('Kérjük, ne felejtse megadni saját e-mail címét!');
		if($subject == '') throw new \Exception('Kérjük, ne felejtse megadni az üzenet témáját!');
		if($msgText == '') throw new \Exception('Kérjük, fogalmazza meg, hogy miben segíthetünk Önnek!');
		$t = $this->getTermekAdat($tid);

		$msg = '';

		$msg .= "<strong>Kapcsolat > Üzenet küldés</strong>";
		$msg .= '<div>---</div>';
		$msg .= '<div>Név: <strong>'.$name.'</strong></div>';
		$msg .= '<div>E-mail cím: <strong>'.$email.'</strong></div>';
		$msg .= '<div>Telefonszám: <strong>'.$phone.'</strong></div>';
		$msg .= '<div>Időpont: <strong>'.NOW.'</strong></div>';
		$msg .= '<div><br /></div>';
		$msg .= '<div>Üzenet</div>';
		$msg .= '<div><strong>'.(($msgText == '')?'-':$msgText).'</strong></div>';


		if(false){
			\Helper::smtpMail(array(
				'recepiens' => array(EMAIL),
				'msg' 	=> $msg,
				'tema' 	=> 'Kapcsolat',
				'from' 	=> $email,
				'fromName' 	=> $name,
				'sub' 	=> TITLE.': Kapcsolat üzenet'
			));
		}else{
			\Helper::smtpMail(array(
				'recepiens' => array(ALERT_EMAIL),
				'msg' 		=> 'Új kapcsolat üzenet érkezett.<br><br><a href="'.ADMIN.'uzenetek">Üzenetek listája</a>',
				'tema' 		=> 'Értesítő',
				'from' 		=> NOREPLY_EMAIL,
				'fromName'	=> TITLE,
				'sub' 		=> 'Értesítő - Új kapcsolat üzenet'
			));
			$this->logMessage(array(
				'felado_nev' 	=> $name,
				'felado_email' 	=> $email,
				'item_id' 		=> '',
				'tipus' 		=> 'contactMsg',
				'uzenet_targy' 	=> 'Kapcsolat üzenet',
				'uzenet' 		=> $msg
			));
		}


		return 'Köszönjük megkeresését. Hamarosan felvesszük Önnel a kapcsolatot!';
	}

	public function requestReCall( $post )
	{
		extract($post);

		if($name == '') throw new \Exception('Kérjük, ne felejtse megadni saját nevét!');
		if($phone == '') throw new \Exception('Kérjük, ne felejtse megadni saját telefonszámát!');
		if($subject == '') throw new \Exception('Kérjük, ne felejtse beírni, hogy milyen ügyben keres minket!');

		// Admin értesítése beérkezésről
		$mail = new Mailer( $this->settings['page_title'], SMTP_USER, $this->settings['mail_sender_mode'] );
		$mail->add( $this->settings['alert_email'] );

		$arg = array(
			'settings' => $this->settings,
			'name' => $name,
			'phone' => $phone,
			'subject' => $subject,
			'targy' => 'Ingyenes visszahívás'
		);

		$mail->setSubject( 'Értesítő: Ingyenes visszahívás kérés érkezet.' );
		$mail->setMsg( (new Template( VIEW . 'templates/mail/' ))->get( 'admin_requestrecall', $arg ) );
		$re = $mail->sendMail();

		// Üzenet mentése
		$this->logMessage(array(
			'felado_nev' => $name,
			'felado_telefon'=> $phone,
			'tipus' => 'recall',
			'uzenet_targy' => 'Ingyenes visszahívás',
			'uzenet' => $subject
		));

		return 'Köszönjük érdeklődését! Ingyenes visszahívás kérés igénylés elküldve. Hamarosan jelentkezünk!';
	}

	public function requestOffer($post)
	{
		extract($post);

		if($name == '') throw new \Exception('Kérjük, ne felejtse megadni saját nevét!');
		if($phone == '') throw new \Exception('Kérjük, ne felejtse megadni saját telefonszámát!');
		if($email == '') throw new \Exception('Kérjük, ne felejtse megadni saját e-mail címét!');
		if($message == '') throw new \Exception('Kérjük, ne felejtse részletezni, hogy mire szeretne ajánlatot kérni!');

		// Admin értesítése beérkezésről
		$mail = new Mailer( $this->settings['page_title'], SMTP_USER, $this->settings['mail_sender_mode'] );
		$mail->add( $this->settings['alert_email'] );

		$arg = array(
			'settings' => $this->settings,
			'name' => $name,
			'phone' => $phone,
			'email' => $email,
			'message' => $message,
			'targy' => 'Ingyenes ajánlatkérés'
		);

		$mail->setSubject( 'Értesítő: Ingyenes ajánlatkérés érkezet.' );
		$mail->setMsg( (new Template( VIEW . 'templates/mail/' ))->get( 'admin_requestoffer', $arg ) );
		$re = $mail->sendMail();

		// Felhasználó értesítés kézbesítésről
		$mail = new Mailer( $this->settings['page_title'], SMTP_USER, $this->settings['mail_sender_mode'] );
		$mail->add( $email );

		$arg = array(
			'settings' => $this->settings,
			'name' => $name,
			'phone' => $phone,
			'email' => $email,
			'message' => $message,
			'targy' => 'Ingyenes ajánlatkérését fogadtuk'
		);
		$arg['mailtemplate'] = (new MailTemplates(array('db'=>$this->db)))->get('request_offer', $arg);

		$mail->setSubject( 'Visszaigazolás: Ingyenes ajánlatkérését fogadtuk.' );
		$mail->setMsg( (new Template( VIEW . 'templates/mail/' ))->get( 'mailtemplateholder', $arg ) );
		$re = $mail->sendMail();

		// Üzenet mentése
		$this->logMessage(array(
			'felado_nev' => $name,
			'felado_telefon'=> $phone,
			'felado_email'=> $email,
			'tipus' => 'ajanlat',
			'uzenet_targy' => 'Ingyenes ajánlatkérés',
			'uzenet' => $message
		));

		return 'Köszönjük megkeresését. Hamarosan felvesszük Önnel a kapcsolatot a személyre szabott ajánlatunkkal!';
	}

	public function requestTermprice($post)
	{
		extract($post);

		if($name == '') throw new \Exception('Kérjük, ne felejtse megadni saját nevét!');
		if($phone == '') throw new \Exception('Kérjük, ne felejtse megadni saját telefonszámát!');
		if($email == '') throw new \Exception('Kérjük, ne felejtse megadni saját e-mail címét!');

		$termek = '<style>
			.mail-termek-row{
				position: relative;
				background: #f7f7f7;
				padding: 10px;
				border: 2px solid #e9e9e9;
				border-radius: 5px;
				margin: 15px 0;
			}
			.mail-termek-row .img{
				float: left;
				width: 20%;
				max-width: 20%;
				box-sizing: border-box;
			}
			.mail-termek-row .img img{
				width: 100%;
				max-width: 100%;
			}
			.mail-termek-row .data{
				float: left;
				width: 80%;
				box-sizing: border-box;
				padding-left: 15px;
			}
			.mail-termek-row .data .name a{
				display: block;
				color: #f09f0f;
				font-weight: bold;
				margin-bottom: 15px;
				font-size: 18px;
			}
			.mail-termek-row .data .cat{
				color: #575757;
				font-size: 15px;
			}
			.mail-termek-row .data .cat strong {
				color: #3a3a3a;
			}
			.mail-termek-row .data .sdesc{
				color: #919191;
				font-size: 12px;
				line-height: 1.5;
				text-align:justify;
				margin-top: 5px;
			}
			.mail-termek-row .data .meta{
				color: #acacac;
				font-size: 10px;
				margin-top: 5px;
			}
		</style>';
		$termek .= '<div class="mail-termek-row" style>
			<div class="img"><img src="'.$product['profil_kep'].'" alt="'.$product['nev'].'"/></div>
			<div class="data">
				<div class="name"><a href="'.$this->settings['page_url'].'/termek/'.\Helper::makeSafeUrl($product['nev'],'_-'.$product['ID']).'">'.$product['nev'].'</a></div>
				<div class="cat"><strong>'.$product['kategoriaNev'].'</strong> / '.$product['csoport_kategoria'].'</div>
				<div class="sdesc">'.$product['rovid_leiras'].'</div>
				<div class="meta">#'.$product['ID'].' &nbsp; Cikkszám:'.$product['cikkszam'].'</div>
			</div>
			<div style="clear:both;"></div>
		</div>';

		// Admin értesítése beérkezésről
		$mail = new Mailer( $this->settings['page_title'], SMTP_USER, $this->settings['mail_sender_mode'] );
		$mail->add( $this->settings['alert_email'] );

		$arg = array(
			'settings' => $this->settings,
			'name' => $name,
			'phone' => $phone,
			'email' => $email,
			'termek' => $termek,
			'message' => $message,
			'targy' => 'Termék ár kérés'
		);

		$mail->setSubject( 'Értesítő: Termék ár kérés érkezet.' );
		$mail->setMsg( (new Template( VIEW . 'templates/mail/' ))->get( 'admin_requesttermprice', $arg ) );
		$re = $mail->sendMail();

		// Felhasználó értesítés kézbesítésről
		$mail = new Mailer( $this->settings['page_title'], SMTP_USER, $this->settings['mail_sender_mode'] );
		$mail->add( $email );

		$arg = array(
			'settings' => $this->settings,
			'name' => $name,
			'phone' => $phone,
			'email' => $email,
			'message' => $message,
			'termek' => $termek,
			'termek_nev' => $product['nev'],
			'targy' => 'Termék ár kérését fogadtuk'
		);
		$arg['mailtemplate'] = (new MailTemplates(array('db'=>$this->db)))->get('request_product_price', $arg);

		$mail->setSubject( 'Visszaigazolás: Termék ár kérését fogadtuk.' );
		$mail->setMsg( (new Template( VIEW . 'templates/mail/' ))->get( 'mailtemplateholder', $arg ) );
		$re = $mail->sendMail();

		// Üzenet mentése
		$this->logMessage(array(
			'item_id' => $product['ID'],
			'felado_nev' => $name,
			'felado_telefon'=> $phone,
			'felado_email'=> $email,
			'tipus' => 'requesttermprice',
			'uzenet_targy' => 'Termék ár kérés',
			'uzenet' => $message
		));

		return 'Köszönjük megkeresését. Hamarosan felvesszük Önnel a kapcsolatot a termékkel kapcsolatban!';
	}

	public function requestTermKerdes($post)
	{
		extract($post);

		if($name == '') throw new \Exception('Kérjük, ne felejtse megadni saját nevét!');
		if($phone == '') throw new \Exception('Kérjük, ne felejtse megadni saját telefonszámát!');
		if($email == '') throw new \Exception('Kérjük, ne felejtse megadni saját e-mail címét!');

		$termek = '<style>
			.mail-termek-row{
				position: relative;
				background: #f7f7f7;
				padding: 10px;
				border: 2px solid #e9e9e9;
				border-radius: 5px;
				margin: 15px 0;
			}
			.mail-termek-row .img{
				float: left;
				width: 20%;
				max-width: 20%;
				box-sizing: border-box;
			}
			.mail-termek-row .img img{
				width: 100%;
				max-width: 100%;
			}
			.mail-termek-row .data{
				float: left;
				width: 80%;
				box-sizing: border-box;
				padding-left: 15px;
			}
			.mail-termek-row .data .name a{
				display: block;
				color: #f09f0f;
				font-weight: bold;
				margin-bottom: 15px;
				font-size: 18px;
			}
			.mail-termek-row .data .cat{
				color: #575757;
				font-size: 15px;
			}
			.mail-termek-row .data .cat strong {
				color: #3a3a3a;
			}
			.mail-termek-row .data .sdesc{
				color: #919191;
				font-size: 12px;
				line-height: 1.5;
				text-align:justify;
				margin-top: 5px;
			}
			.mail-termek-row .data .meta{
				color: #acacac;
				font-size: 10px;
				margin-top: 5px;
			}
		</style>';
		$termek .= '<div class="mail-termek-row" style>
			<div class="img"><img src="'.$product['profil_kep'].'" alt="'.$product['nev'].'"/></div>
			<div class="data">
				<div class="name"><a href="'.$this->settings['page_url'].'/termek/'.\Helper::makeSafeUrl($product['nev'],'_-'.$product['ID']).'">'.$product['nev'].'</a></div>
				<div class="cat"><strong>'.$product['in_cat_names'][0].'</div>
				<div class="sdesc">'.$product['rovid_leiras'].'</div>
				<div class="meta">#'.$product['ID'].' &nbsp; Cikkszám:'.$product['cikkszam'].'</div>
			</div>
			<div style="clear:both;"></div>
		</div>';

		// Admin értesítése beérkezésről
		$mail = new Mailer( $this->settings['page_title'], SMTP_USER, $this->settings['mail_sender_mode'] );
		$mail->add( $this->settings['alert_email'] );

		$arg = array(
			'settings' => $this->settings,
			'name' => $name,
			'phone' => $phone,
			'email' => $email,
			'termek' => $termek,
			'message' => $message,
			'targy' => 'Termék kérés'
		);

		$mail->setSubject( 'Értesítő: Termék kérdés érkezet.' );
		$mail->setMsg( (new Template( VIEW . 'templates/mail/' ))->get( 'admin_requestask', $arg ) );
		$re = $mail->sendMail();

		// Felhasználó értesítés kézbesítésről
		$mail = new Mailer( $this->settings['page_title'], SMTP_USER, $this->settings['mail_sender_mode'] );
		$mail->add( $email );

		$arg = array(
			'settings' => $this->settings,
			'name' => $name,
			'phone' => $phone,
			'email' => $email,
			'message' => $message,
			'termek' => $termek,
			'termek_nev' => $product['nev'],
			'targy' => 'Termék kérését fogadtuk'
		);
		$arg['mailtemplate'] = (new MailTemplates(array('db'=>$this->db)))->get('request_product_ask', $arg);

		$mail->setSubject( 'Visszaigazolás: Termék kérését fogadtuk.' );
		$mail->setMsg( (new Template( VIEW . 'templates/mail/' ))->get( 'mailtemplateholder', $arg ) );
		$re = $mail->sendMail();

		// Üzenet mentése
		$this->logMessage(array(
			'item_id' => $product['ID'],
			'felado_nev' => $name,
			'felado_telefon'=> $phone,
			'felado_email'=> $email,
			'tipus' => 'requesttermask',
			'uzenet_targy' => 'Termék kérdés',
			'uzenet' => $message
		));

		return 'Köszönjük megkeresését. Hamarosan felvesszük Önnel a kapcsolatot a termékkel kapcsolatban!';
	}

	const ORDER_COOKIE_KEY_STEP = 'orderStep';

	public function doOrder($post, $arg = array()) {
		extract($post);
		$errArr = false;
		$gets 	= \Helper::GET();
		$step 	= $gets[1];
		$step 	= (!$step) ? 0 : $step;
		$is_reorder = (isset($arg['reorder'])) ? $arg['reorder'] : false;

		if ($is_reorder) {
			$step = 4;
		}

		$post_str = json_encode($post, JSON_UNESCAPED_UNICODE);

		switch($step){
			case 0:
				$err 		= false;
				$inputErr 	= array();

				if($nev == ''){
					$err 		= 'Alapvető adatok megadása kötelező vagy jelentkezzen be.';
					$inputErr[] = 'nev';
				}

				if($email == ''){
					$err 		= 'Alapvető adatok megadása kötelező vagy jelentkezzen be.';
					$inputErr[] = 'email';
				}


				if($err){
					$errArr[input] = $inputErr;
					throw new OrderException($err, $errArr);
				}else{
					setcookie(self::ORDER_COOKIE_KEY_STEP,$step+1,time()+3600*24,'/');
					\Helper::setStoredPOSTData('order_step_'.($step+1),$post_str);
				}
			break;
			case 1:
				$err 		= false;
				$inputErr 	= array();

				// Számlázási adatok check
				if($szam_nev == ''){
					$err 		= 'Kötelező adat: Számlázási név hiányzik!';
					$inputErr[] = 'szam_nev';
				}
				if($szam_hazszam == ''){
					$err 		= 'Kötelező adat: Számlázási cím / Házszám hiányzik!';
					$inputErr[] = 'szam_hazszam';
				}
				if($szam_city == ''){
					$err 		= 'Kötelező adat: Számlázási cím / Település hiányzik!';
					$inputErr[] = 'szam_city';
				}
				if($szam_irsz == ''){
					$err 		= 'Kötelező adat: Számlázási cím / Irányítószám hiányzik!';
					$inputErr[] = 'szam_irsz';
				}
				if($szam_kozterulet_nev == ''){
					$err 		= 'Kötelező adat: Számlázási cím / Közterület neve hiányzik!';
					$inputErr[] = 'szam_kozterulet_nev';
				}
				if($szam_kozterulet_jelleg == ''){
					$err 		= 'Kötelező adat: Számlázási cím / Közterület jellege hiányzik!';
					$inputErr[] = 'szam_kozterulet_jelleg';
				}

				// Szállítási
				if($szall_nev == ''){
					$err 		= 'Kötelező adat: Szállítási név hiányzik!';
					$inputErr[] = 'szall_nev';
				}
				if($szall_hazszam == ''){
					$err 		= 'Kötelező adat: Számlázási cím / Házszám hiányzik!';
					$inputErr[] = 'szall_hazszam';
				}
				if($szall_city == ''){
					$err 		= 'Kötelező adat: Szállítási cím / Település hiányzik!';
					$inputErr[] = 'szall_city';
				}
				if($szall_irsz == ''){
					$err 		= 'Kötelező adat: Szállítási cím / Irányítószám hiányzik!';
					$inputErr[] = 'szall_irsz';
				}
				if($szall_kozterulet_nev == ''){
					$err 		= 'Kötelező adat: Szállítási cím / Közterület neve hiányzik!';
					$inputErr[] = 'szall_kozterulet_nev';
				}
				if($szall_kozterulet_jelleg == ''){
					$err 		= 'Kötelező adat: Szállítási cím / Közterület jellege hiányzik!';
					$inputErr[] = 'szall_kozterulet_jelleg';
				}
				if($szall_phone == ''){
					$err 		= 'Kötelező adat: Telefonszám hiányzik!';
					$inputErr[] = 'szall_phone';
				}
				if($szall_phone_korzet == ''){
					$err 		= 'Kötelező adat: Telefonszám körzetszám hiányzik!';
					$inputErr[] = 'szall_phone_korzet';
				}
				if($err){
					$errArr[input] = $inputErr;
					throw new OrderException($err, $errArr);
				}else{
					setcookie(self::ORDER_COOKIE_KEY_STEP,$step+1,time()+3600*24,'/');
					\Helper::setStoredPOSTData('order_step_'.($step+1),$post_str);
				}
			break;
			case 2:
				$err 		= false;
				$inputErr 	= array();
				if($atvetel == ''){
					$err 		= 'Átvételi mód kiválasztása kötelező!';
					$inputErr[] = 'atvetel';
				}

				if($err){
					$errArr[input] = $inputErr;
					throw new OrderException($err, $errArr);
				}else{
					setcookie(self::ORDER_COOKIE_KEY_STEP,$step+1,time()+3600*24,'/');

					\Helper::setStoredPOSTData( 'order_step_'.($step+1) ,$post_str );
				}
			break;
			case 3:
				$err 		= false;
				$inputErr 	= array();
				if($fizetes == ''){
					$err 		= 'Fizetési mód kiválasztása kötelező!';
					$inputErr[] = 'fizetes';
				}
				if($err){
					$errArr[input] = $inputErr;
					throw new OrderException($err, $errArr);
				}else{
					setcookie(self::ORDER_COOKIE_KEY_STEP,$step+1,time()+3600*24,'/');
					\Helper::setStoredPOSTData('order_step_'.($step+1),$post_str);
				}
			break;
			case 4:
				$mid 	= \Helper::getMachineID();
				$err 		= false;
				$inputErr 	= array();

				if ($is_reorder) {
					$reorder = $this->prepareReorder( $is_reorder, $post );
				}

				if ( !$post['aszf_ok'] ) {
					$err 		= 'Megrendelés leadásához el kell fogadni az Általános Szerződési Feltételeket!';
					$inputErr[] = 'aszf_on';
				}

				if($err)
				{
					$errArr[input] = $inputErr;
					throw new OrderException($err, $errArr);
				}else
				{
					$go 					= true;
					$orderID 				= 0;					
					$uid 					= ($orderUserID == '') ? 0 : $orderUserID;
					$total 					= 0;
					$pppkod 				= ($ppp_uzlet_str) ? "'".$ppp_uzlet_str."'" : 'NULL';
					$pp_pont 				= ($pp_selected_point) ? "'".$pp_selected_point."'": 'NULL';
					$kedvezmeny_ft 			= 0;
					$allow_partner_discount = false;
					$allow_coupon_discount 	= false;
					$referer_partner_id 	= ($referer_partner_id)? $referer_partner_id : NULL;
					$coupon_code 			= ($coupon_id)? $coupon_id : NULL;
					$used_cash 				= (isset($virtual_cash)) ? $virtual_cash : 0;

					if ( $used_cash )
					{
						$kedvezmeny_ft = $used_cash;
					}

					// Újra rendelés esetén adatfelülírások
					if ( $is_reorder && $reorder )
					{
						$nev = $reorder['nev'];
						$uid 					= $reorder['userID'];
						$szallitasi_koltseg = $reorder['szallitasi_koltseg'];
						$email = $reorder['email'];
						$atvetel = $reorder['szallitasiModID'];
						$fizetes = $reorder['fizetesiModID'];
						$kedvezmeny_ft = $reorder['kedvezmeny'];
						$comment = $post['reorder']['comment'];
						$pppkod = ($reorder['pickpackpont_uzlet_kod'] == '') ? 'NULL' : $reorder['pickpackpont_uzlet_kod'];
						$pp_pont = ($reorder['postapont'] == '') ? 'NULL' : $reorder['postapont'];
					}

					/**
					 * Partner ajánló rendszer levonás
					 * */
					$partner_ref = (new PartnerReferrer ( $referer_partner_id, array(
						'db' 		=> $this->db,
						'settings' 	=> $this->settings
					)))
					->setExcludedUser( $uid )
					->load();

					if( $partner_ref->isValid() ) {
						$allow_partner_discount = true;
					}

					/**
					 * Kupon ellenőrzés
					 * */
					if( $coupon_code )
					{
						$coupon = new Coupon(array(
							'db' => $this->db
						));
						$coupon->get($coupon_code);

						if ($coupon->isRunning())
						{
							$allow_coupon_discount = true;
						}
					}

					$mid = ($mid == '') ? 0 : $mid;

					$cart = array();
					$cart_data = $this->db->query("
						SELECT
							c.*,
							t.nev,
							t.raktar_articleid,
							t.mertekegyseg,
							t.mertekegyseg_ertek,
							getTermekUrl(t.ID,'".$this->settings['domain']."') as url,
							getTermekAr(c.termekID, ".$uid.") as ar,
							t.referer_price_discount,
							m.neve as markaNev
						FROM shop_kosar as c
						LEFT OUTER JOIN shop_termekek as t ON t.ID = c.termekID
						LEFT OUTER JOIN shop_markak as m ON m.ID = t.marka
						WHERE c.gepID = $mid");
					$cart = $cart_data->fetchAll(\PDO::FETCH_ASSOC);

					if( count($cart) == 0 ){
						return false;
					}

					$referer_partner_id =($referer_partner_id) ? "'".$referer_partner_id."'" : 'NULL';
					$coupon_code 		= ($coupon_code) ? "'".$coupon_code."'" : 'NULL';
					$pre_order = ($is_reorder && $reorder) ? "'".$reorder['azonosito']."'" : 'NULL';

					$uid = ($uid == 0) ? 'NULL' : $uid;

					// Create new order
					if($go){
						// Újra rendelés esetén adatfelülírások
						if ($is_reorder && $reorder )
						{
							$szamlazasi_keys = json_decode($reorder['szamlazasi_keys'], true);
							$szallitasi_keys = json_decode($reorder['szallitasi_keys'], true);
						} else {
							$szamlazasi_keys = \Helper::getArrayValueByMatch($post,'szam_');
							$szallitasi_keys = \Helper::getArrayValueByMatch($post,'szall_');
						}

						$iq = "INSERT INTO orders(nev,azonosito,prev_order,email,userID,gepID,szallitasiModID,fizetesiModID,kedvezmeny,szallitasi_koltseg,szamlazasi_keys,szallitasi_keys,pickpackpont_uzlet_kod,comment,postapont,referer_code,coupon_code, used_cash) VALUES(
						'$nev',
						nextOrderID(),
						$pre_order,
						'$email',
						$uid,
						'$mid',
						'$atvetel',
						'$fizetes',
						'$kedvezmeny_ft',
						'$szallitasi_koltseg',
						'".json_encode($szamlazasi_keys,JSON_UNESCAPED_UNICODE)."',
						'".json_encode($szallitasi_keys,JSON_UNESCAPED_UNICODE)."',
						$pppkod,
						'$comment',
						$pp_pont,
						$referer_partner_id,
						$coupon_code,
						$used_cash
						);";

						$this->db->query($iq);

						$orderID 	= $this->db->lastInsertId();
						$accessKey	= md5($orderID.'.'.$email);

						$this->db->update('orders',
							array(
								'accessKey' => $accessKey
							),
							"ID = $orderID"
						);

						if ( $used_cash )
						{
							// Kedvezmény levonása
							if ( $orderUserID )
							{
								$this->db->query("UPDATE ".\PortalManager\Users::TABLE_NAME." SET cash = cash - ".$used_cash." WHERE ID = ".$orderUserID);

								// Log cash
								$this->db->insert(
									'cash_log',
									array(
										'felh_id' 		=> $orderUserID,
										'referer' 		=> 'Egyenleg felhasználás',
										'referer_id' 	=> $orderID,
										'cash' 			=> $used_cash,
										'direction' 	=> 'out'
									)
								);
							}
						}
					}

					// Copy items to order items and connect with order parent
					if($go){
						$temp_cart 		= array();

						foreach($cart as $d)
						{
							$kedv = 0;

							if( $kedvezmeny > 0 )
							{
								//\PortalManager\Formater::discountPrice( $d[ar], $kedvezmeny );
							}

							if( $allow_partner_discount )
							{
								$kedv 			= $d['referer_price_discount'];
								$kedvezmeny_ft 	+= $d['referer_price_discount'] * $d['me'];
							}

							if ($allow_coupon_discount && !$coupon->isOffForAllProduct())
							{
								if ($coupon->thisProductAllowed($d['raktar_articleid']))
								{
									$kedv 			= $coupon->calcPrice( $d['ar'] );
									$kedvezmeny_ft += $coupon->calcPrice( $d['ar'] ) * $d['me'];
								}

							}

							$total += ( $d[ar] * $d[me] );

							$this->db->insert('order_termekek', array(
									'orderKey' => $orderID,
									'gepID' => $mid,
									'userID' => $uid,
									'email' => $email,
									'termekID' => $d['termekID'],
									'configs' => $d['configs'],
									'me' => $d['me'],
									'egysegAr' => $d['ar'],
									'egysegArKedvezmeny'=> $kedv
							));

							// Készlet levonás
							if ( $this->settings['stock_withdrawal'] == '1' ) {
								$levon = (int)$d['me'];
								$this->db->query("UPDATE shop_termekek SET raktar_keszlet = raktar_keszlet - ".$levon." WHERE ID = ".$d['termekID']);
							}

							$d['configs'] = $this->collectConfigData($d['configs']);

							$temp_cart[] = $d;
						}

						// A teljes megrendelésből vonja le a kupon kedvezményt
						if ($allow_coupon_discount && $coupon->isOffForAllProduct())
						{
							$kedvezmeny_ft = $coupon->calcPrice( $total );
						}

						// Kedvezmény mentése
						$this->db->query("UPDATE orders SET kedvezmeny = $kedvezmeny_ft WHERE ID = $orderID;");

						$cart = $temp_cart;
						unset( $temp_cart );

					}


					// Clear shoping cart by machineID
					if($go){
						$this->db->query("DELETE FROM shop_kosar WHERE gepID = $mid");
					}
						// Alert orders and admin about new order
						$orderData = $this->db->query("SELECT * FROM orders WHERE ID = $orderID")->fetch(\PDO::FETCH_ASSOC);

						// Admin alert
						$total 		= 0;

						$is_pickpackpont 	= ( $this->getSzallitasiModeData($atvetel,'nev') 	== $this->settings['flagkey_pickpacktransfer_id'] ) ? true : false;
						$is_eloreutalas 	= ( $this->getFizetesiModeData($fizetes,'nev') 		== 'Banki átutalás' ) ? true : false;
						$is_payu 			= ( $this->getFizetesiModeData($fizetes,'nev') 		== 'Bankkártyás fizetés (PayU)' ) ? true : false;

						// Értesítő e-mail az adminisztrátornak
						$mail = new Mailer(
							$this->settings['page_title'],
							SMTP_USER,
							$this->settings['mail_sender_mode']
						);
						$mail->add( $this->settings['alert_email'] );

						$arg = array(
							'settings' 		=> $this->settings,
							'infoMsg' 		=> 'Ezt az üzenetet a rendszer küldte. Kérjük, hogy ne válaszoljon rá!',
							'nev' 			=> $nev,
							'email' 		=> $email,
							'uid' 			=> $uid,
							'orderData' 	=> $orderData,
							'cart' 			=> $cart,
							'total' 		=> $total,
							'szallitasi_koltseg' => $szallitasi_koltseg,
							'kedvezmeny' 	=> $kedvezmeny_ft,
							'szamlazasi_keys' => $szamlazasi_keys,
							'szallitasi_keys' => $szallitasi_keys,
							'atvetel' 		=> $this->getSzallitasiModeData($atvetel,'nev'),
							'fizetes' 		=> $this->getFizetesiModeData($fizetes,'nev'),
							'ppp_uzlet_str' => $pppkod,
							'is_pickpackpont' => $is_pickpackpont,
							'orderID' 		=> $orderID,
							'megjegyzes' 	=> $comment
						);
						$mail->setSubject( 'Értesítő: Új megrendelés érkezett' );
						$mail->setMsg( (new Template( VIEW . 'templates/mail/' ))->get( 'order_new_admin', $arg ) );
						$re = $mail->sendMail();

						// User alert
						$total 		= 0;
						$mail = new Mailer(
							$this->settings['page_title'],
							$this->settings['email_noreply_address'],
							$this->settings['mail_sender_mode']
						);

						$mail->add( $email );

						$arg = array(
							'settings' 		=> $this->settings,
							'infoMsg' 		=> 'Ezt az üzenetet a rendszer küldte. Kérjük, hogy ne válaszoljon rá!',
							'nev' 			=> $nev,
							'email' 		=> $email,
							'uid' 			=> $uid,
							'orderData' 	=> $orderData,
							'cart' 			=> $cart,
							'total' 		=> $total,
							'szallitasi_koltseg' => $szallitasi_koltseg,
							'kedvezmeny' 	=> $kedvezmeny_ft,
							'szamlazasi_keys' => $szamlazasi_keys,
							'szallitasi_keys' => $szallitasi_keys,
							'atvetel' 		=> $this->getSzallitasiModeData($atvetel,'nev'),
							'fizetes' 		=> $this->getFizetesiModeData($fizetes,'nev'),
							'ppp_uzlet_str' => $pppkod,
							'is_pickpackpont' => $is_pickpackpont,
							'orderID' 		=> $orderID,
							'megjegyzes' 	=> $comment,
							'is_eloreutalas' => $is_eloreutalas,
							'accessKey' => $accessKey
						);

						$mail->setSubject( 'Megrendelését fogadtuk: '.$orderData[azonosito] );
						$mail->setMsg( (new Template( VIEW . 'templates/mail/' ))->get( 'order_new_user', $arg ) );
						$re = $mail->sendMail();

						// Feliratkozás
						if ( isset($_POST['subscribe']) ) {
							$portal = new Portal( array( 'db' => $this->db ));
							$portal->feliratkozas( $nev, $email, 'megrendelés' );
						}

						// Kupon felhasználtság
						if ($allow_coupon_discount)
						{
							$coupon->used();
						}

						// Clear cookies
						setcookie('__order_step_1poststr',null,time()-3600,'/');
						setcookie('__order_step_2poststr',null,time()-3600,'/');
						setcookie('__order_step_3poststr',null,time()-3600,'/');
						setcookie('__order_step_4poststr',null,time()-3600,'/');
						//setcookie('orderStep',null,time()-3600,'/');
						setcookie('partner_code',null,time()-3600,'/');
						setcookie('coupon_code',null,time()-3600,'/');

						setcookie('acceptedOrder',null,time()-3600,'/kosar');
						setcookie('lastOrderedKey',$accessKey,time()+3600,'/kosar');

						if($is_reorder && $reorder) {
							// reorder config clear
							$this->db->squery("DELETE FROM reorder_config WHERE preorder = :porder", array('porder' => $reorder['azonosito']));
							return $accessKey;
						}
				}
			break;
		}

		return $step+1;
	}

	public function prepareReorder( $key, $post )
	{
		$mid = \Helper::getMachineID();

		// old order data
		$order = $this->getOrderData( $key );

		// kosár ürítése
		$this->clearCart( $mid);

		// kosár előkészítése
		if ($order['items']) {
			foreach ($order['items'] as $c) {
				$this->db->insert(
					'shop_kosar',
					array(
						'gepID' => $mid,
						'termekID' => $c['termekID'],
						'configs' => $c['configs'],
						'me' => (isset($post['reorder']['me'][$c['termekID']])) ? (int)$post['reorder']['me'][$c['termekID']] : (int)$c['me'],
					)
				);
			}
		}

		return $order;
	}

	public function reorderSave( $key, $email, $data )
	{
		$mid = \Helper::getMachineID();
		$uid = (int)$this->user[data][ID];

		if ($email == '') {
			throw new \Exception("Hiányzó e-mail cím a korábbi megrendelésnél (".$key.")!");

		}

		foreach ((array)$data['reorder']['me'] as $tid => $me) {
			$ch = $this->db->squery("SELECT ID FROM reorder_config WHERE preorder = :order and termekID = :tid", array('order' => $key, 'tid' => $tid));

			if ( $ch->rowCount() == 0 ) {
				$this->db->insert(
					'reorder_config',
					array(
						'preorder' => $key,
						'gepID' => $mid,
						'userID' => ($uid == '')?NULL:$uid,
						'termekID' => $tid,
						'me' => (int)$me
					)
				);
			} else {
				$chid = $ch->fetchColumn();
				$this->db->update(
					'reorder_config',
					array(
						'me' => $me
					),
					sprintf("ID = %d", $chid)
				);
			}
		}
	}

	public function getOrderData( $key, $by = 'accessKey', $arg = array())
	{
		$q = "SELECT o.* FROM orders as o WHERE o.$by = '$key'";
		extract($this->db->q($q));

		$arg['azonosito'] = $data['azonosito'];
		$data[items] = $this->getOrderItems($data[ID], $arg);

		return $data;
	}

	private function getOrderItems($orderID, $arg = array()){
		if($orderID == '') return false;

		$is_reorder = (isset($arg['reorder'])) ? true : false;
		$uid = (int)$this->user[data][ID];

		if ($is_reorder) {
			$preorder_me = array();
			$mesq = $this->db->squery("SELECT termekID, me FROM reorder_config WHERE preorder = :order", array('order' => $arg['azonosito']));
			if ($mesq->rowCount() != 0) {
				$mes = $mesq->fetchAll(\PDO::FETCH_ASSOC);
				foreach ((array)$mes as $m) {
					$preorder_me[$m['termekID']] = (int)$m['me'];
				}
			}
		}

		$q = "SELECT
			ok.*,
			t.nev,
			(ok.egysegAr * ok.me) as subAr,
			t.profil_kep,
			t.meret,
			t.szin,
			t.cikkszam,
			t.mertekegyseg,
			t.mertekegyseg_ertek,
			t.raktar_variantid,
			getTermekUrl(t.ID,'".$this->settings['domain']."') as url,
			getTermekAr(t.ID ,".$uid.") as ar,
			otp.nev as allapotNev,
			otp.szin as allapotSzin,
			(SELECT kedvezmeny_szazalek FROM orders WHERE ID = $orderID) as kedvezmeny_szazalek
		FROM order_termekek as ok
		LEFT OUTER JOIN shop_termekek as t ON t.ID = ok.termekID
		LEFT OUTER JOIN shop_markak as m ON m.ID = t.marka
		LEFT OUTER JOIN order_termek_allapot as otp ON ok.allapotID = otp.ID
		WHERE ok.orderKey = $orderID";

		$arg[multi] = '1';
		extract($this->db->q($q,$arg));


		$bdata = array();
		foreach ($data as $d) {
			if ($is_reorder) {
				$origin_ar = $d['ar'];
				$d['egysegAr'] = $origin_ar;
				$d['me'] = (int)$preorder_me[$d['termekID']];
				$d['subAr'] = $d['egysegAr'] * (int)$preorder_me[$d['termekID']];
			}
			$kedvezmenyes = ($d[kedvezmeny_szazalek] > 0) ? true : false;
			if( $kedvezmenyes ) {
				\PortalManager\Formater::discountPrice( $d[ar], $d[kedvezmeny_szazalek] );
				\PortalManager\Formater::discountPrice( $d[subAr], $d[kedvezmeny_szazalek] );
				\PortalManager\Formater::discountPrice( $d[egysegAr], $d[kedvezmeny_szazalek] );
			}
			$d['configs'] = $this->collectConfigData($d['configs']);
			$bdata[] = $d;
		}


		return $bdata;
	}
	private function getFizetesiModeData($id, $row = false){
		$q = "SELECT * FROM shop_fizetesi_modok WHERE ID = $id";

		extract($this->db->q($q));
		if(!$row){
			return $data;
		}else{
			return $data[$row];
		}

	}
	private function getSzallitasiModeData($id, $row = false){
		$q = "SELECT * FROM shop_szallitasi_mod WHERE ID = $id";

		extract($this->db->q($q));
		if(!$row){
			return $data;
		}else{
			return $data[$row];
		}

	}
	public function getFizetesiModok(){
		$q = "SELECT
			f.*,
			(SELECT GROUP_CONCAT(ID) as IDS FROM `shop_szallitasi_mod` WHERE FIND_IN_SET(f.ID,fizetesi_mod) > 0) as in_szallitas_mod
		FROM shop_fizetesi_modok as f WHERE f.allapot = 1";
		extract($this->db->q($q,array('multi' => '1')));
		$back = array();

		foreach($data as $d){
			$d['in_szallitas_mod'] = explode(',',$d[in_szallitas_mod]);
			$back[] = $d;
		}

		return $back;
	}

	function getArgepTermekek(){
		$akcios_plus_szaz = AKCIOS_BRUTTO_AR_PLUSZ_SZAZALEK;
		$apsz = $akcios_plus_szaz / 100 + 1;


		$back = array();
		$q = $this->db->query("SELECT
			t.ID,
			t.szuper_akcios,
			t.termek_kategoria,
			CONCAT(m.neve,' ',t.nev) as nev,
			IF(t.egyedi_ar IS NOT NULL,
				t.egyedi_ar,
				getTermekAr(t.marka, IF(t.akcios,t.akcios_brutto_ar,t.brutto_ar))
			) as ar,
			getTermekUrl(t.ID,'".DOMAIN."') as url,
			t.leiras,
			szall.elnevezes as szallitas,
			FULLIMG(t.profil_kep) as kep
		FROM
			shop_termekek as t
		LEFT OUTER JOIN shop_szallitasi_ido as szall ON szall.ID = t.szallitasID
		LEFT OUTER JOIN shop_markak as m ON m.ID = t.marka
		WHERE
			t.argep = 1 and
			t.lathato = 1
		ORDER BY
			id DESC;");
		$d = $q->fetchAll(\PDO::FETCH_ASSOC);

		foreach($d as $id){
			$id[param] = $this->getTermekParameter($id[ID],$id[termek_kategoria]);
			$back[] = $id;
		}

		return $back;
	}

	function getArukeresoTermekek(){
		$akcios_plus_szaz = AKCIOS_BRUTTO_AR_PLUSZ_SZAZALEK;
		$apsz = $akcios_plus_szaz / 100 + 1;

		$back = array();
		$q = $this->db->query("SELECT
			t.ID,
			t.szuper_akcios,
			m.neve as markaNev,
			tk.e_nev as kategoriaNev,
			t.termek_kategoria,
			t.nev,
			IF(t.egyedi_ar IS NOT NULL,
				t.egyedi_ar,
				getTermekAr(t.marka, IF(t.akcios,t.akcios_brutto_ar,t.brutto_ar))
			) as ar,
			getTermekUrl(t.ID,'".DOMAIN."') as url,
			t.leiras,
			szall.elnevezes as szallitas,
			FULLIMG(t.profil_kep) as kep
		FROM
			shop_termekek as t
		LEFT OUTER JOIN shop_szallitasi_ido as szall ON szall.ID = t.szallitasID
		LEFT OUTER JOIN shop_markak as m ON m.ID = t.marka
		LEFT OUTER JOIN shop_termek_kategoriak as tk ON tk.ID = t.termek_kategoria
		WHERE
			t.argep = 1 and
			t.lathato = 1
		ORDER BY
			id DESC;");
		$d = $q->fetchAll(\PDO::FETCH_ASSOC);

		foreach($d as $id){
			$id[param] = $this->getTermekParameter($id[ID],$id[termek_kategoria]);
			$back[] = $id;
		}

		return $back;
	}

	public function getKedvezmenyesAr($ar){
		$kedv 	= 0;
		$newAr 	= 0;

		// Kedvezmény sávok
		$sv = "SELECT * FROM torzsvasarloi_kedvezmeny ORDER BY ar_from ASC;";

		extract($this->db->q($sv,array('multi' => '1')));

		foreach($data as $d){
			$from 	= (int)$d[ar_from];
			$to 	= (int)$d[ar_to];
			$k 		= (float)$d[kedvezmeny];

			if($to === 0) $to = 999999999;

			if($ar >= $from && $ar <= $to){
				$kedv = $k;
				break;
			}

		}

		$newAr = round(($ar - ($ar * ($kedv / 100))) / 5 ) * 5;

		return $newAr;
	}

	function logTermekView($termekID){
		if ($termekID == '' || !isset($termekID)) {
			return false;
		}
		$date = date('Y-m-d');
		$c = $this->db->query("SELECT 1 FROM stat_nezettseg_termek WHERE datum = '$date' and termekID = $termekID")->rowCount();

		if($c == 0){
			$this->db->insert("stat_nezettseg_termek",
				array(
					'termekID' => $termekID,
					'datum' => $date
				)
			);
		}else{
			$this->db->query("UPDATE stat_nezettseg_termek SET me = me + 1 WHERE termekID = $termekID and datum = '$date'");
		}
	}


	function logKategoriaView( $cat_id = NULL ){
		$date 	= date('Y-m-d');

		if ( !$cat_id ) {
			return false;
		}

		$cq = "SELECT 1 FROM stat_nezettseg_kategoria WHERE datum = '$date' and kategoriaID = $cat_id";

		$c = $this->db->query($cq)->rowCount();

		if($c == 0){
			$this->db->insert("stat_nezettseg_kategoria",
				array(
					'kategoriaID' => $cat_id,
					'datum' => $date
				)
			);
		}else{
			$this->db->query("UPDATE stat_nezettseg_kategoria SET me = me + 1 WHERE datum = '$date' and kategoriaID = $kategoria");
		}
	}

	function logSearching($searchString){
		$date 	= date('Y-m-d');
		$txt 	= trim($searchString);

		$c = $this->db->query("SELECT 1 FROM stat_kereses WHERE datum = '$date' and szoveg = '$txt'")->rowCount();

		if($c == 0 && trim($txt) != ''){
			$this->db->insert("stat_kereses",
				array('szoveg' => $txt,'datum' => $date)
			);
		}else{
			$this->db->query("UPDATE stat_kereses SET me = me + 1 WHERE szoveg = '$txt' and datum = '$date'");
		}
	}

	function logLastViewedTermek($termek_id){
		$mid = \Helper::getMachineID();

		if($termek_id == '' || $mid == '') return false;

		$list = $this->db->query("SELECT * FROM `shop_utoljaraLatottTermek` WHERE mID = '$mid'  and termekID = '$termek_id' LIMIT 0,10");

		if($list->rowCount() == 0){
			$this->db->insert('shop_utoljaraLatottTermek',
				array('mID' => $mid,'termekID' => $termek_id)
			);
		}else{
			$this->db->update('shop_utoljaraLatottTermek',
			array(
				'idopont' => NOW
			),
			"mID = '$mid' and termekID = '$termek_id'");
		}
	}

	function getLastViewedTermek($mID, $limit = 5, $arg = array()){
		$akcios_plus_szaz = AKCIOS_BRUTTO_AR_PLUSZ_SZAZALEK;
		$apsz = $akcios_plus_szaz / 100 + 1;

		$q = "SELECT
			v.*,
			CONCAT(m.neve,' ',t.nev) as termekNev,
			FULLIMG(t.profil_kep) as profil_kep,
			getTermekUrl(t.ID,'".DOMAIN."') as url,
			t.akcios,
			t.szuper_akcios_szazalek,
			t.utolso_darab,
			IF(t.akcios,
			IF(t.akcios_egyedi_brutto_ar != 0,
				t.akcios_egyedi_brutto_ar,
				getTermekAr(t.marka, (t.brutto_ar * ".$apsz."))),
			getTermekAr(t.marka, t.brutto_ar)) as brutto_ar,
			IF(t.egyedi_ar IS NOT NULL, t.egyedi_ar, getTermekAr(t.marka, IF(t.akcios,t.akcios_brutto_ar,t.brutto_ar))) as ar
		FROM `shop_utoljaraLatottTermek` as v
		LEFT OUTER JOIN shop_termekek as t ON t.ID = v.termekID
		LEFT OUTER JOIN shop_markak as m ON m.ID = t.marka
		WHERE v.mID = '$mID' and t.ID IS NOT NULL
		ORDER BY v.idopont DESC
		LIMIT 0,$limit";

		$arg[multi] = '1';
		extract($this->db->q($q,$arg));

		return $data;
	}

	function getMostViewedTermekek($limit = 10){
		$akcios_plus_szaz = AKCIOS_BRUTTO_AR_PLUSZ_SZAZALEK;
		$apsz = $akcios_plus_szaz / 100 + 1;

		$q = "SELECT
			t.ID as termekID,
			CONCAT(m.neve,' ',t.nev) as termekNev,
			FULLIMG(t.profil_kep) as profil_kep,
			getTermekUrl(t.ID,'".DOMAIN."') as url,
			t.akcios,
			t.szuper_akcios_szazalek,
			t.utolso_darab,
			IF(t.akcios,
			IF(t.akcios_egyedi_brutto_ar != 0,
				t.akcios_egyedi_brutto_ar,
				getTermekAr(t.marka, (t.brutto_ar * ".$apsz."))),
			getTermekAr(t.marka, t.brutto_ar)) as brutto_ar,
			IF(t.egyedi_ar IS NOT NULL, t.egyedi_ar, getTermekAr(t.marka, IF(t.akcios,t.akcios_brutto_ar,t.brutto_ar))) as ar,
			(SELECT sum(me) FROM `stat_nezettseg_termek` WHERE termekID = t.ID and datediff(now(),datum) < 60) as v
		FROM `shop_termekek` as t
		LEFT OUTER JOIN shop_markak as m ON m.ID = t.marka
		WHERE t.ID IS NOT NULL
		ORDER BY v DESC, t.nev ASC
		LIMIT 0, $limit";

		$arg[multi] = '1';
		extract($this->db->q($q,$arg));

		return $data;
	}

	function orderAlreadyPaidViaPayPal($accessKey){
		$check = $this->db->query("SELECT paypal_fizetve FROM orders WHERE accessKey = '$accessKey'")->fetch(\PDO::FETCH_COLUMN);

		if($check == 1){
			return true;
		}else{
			return false;
		}
	}

	function setOrderPaidByPayPal($accessKey){
		// Log
			$this->db->update("orders",
				array(
					'paypal_fizetve' => 1,
					'allapot' => 10
				),
			"accessKey = '$accessKey'"
			);

		// Admin alert
		$order = $this->getOrderData($accessKey);

		$vegosszeg 	= 0;
		$param 		= array();
		$msg 		= '';
		$msg 		.= '<div><strong>'.$order[azonosito].' megrendelés kifizetésre került PayPal-on keresztül!</strong></div>';
		$msg 		.= '<br /><br />';
		$msg 		.= '<div>Megrendelés: <a href="http://cp.goldfishing.hu/megrendelesek/?ID='.$order[ID].'">http://cp.goldfishing.hu/megrendelesek/?ID='.$order[ID].'</a></div>';

		foreach($order[items] as $d):
			$vegosszeg += $d[subAr];
		endforeach;

		if($order[szallitasi_koltseg] > 0) $vegosszeg += $order[szallitasi_koltseg];
		if($order[kedvezmeny] > 0) $vegosszeg -= $order[kedvezmeny];

		$msg 		.= '<div><h3>Információk</h3></div>';
		$msg 		.= '<table width="100%" border="1" style="border-collapse:collapse; border:2px solid #dddddd; background:#ffffff;" cellpadding="10" cellspacing="0">';
		$msg 		.= '<tbody>';
			$msg 		.= '<tr>';
				$msg 		.= '<td width="180" align="left">Megrendelés összege</td>';
				$msg 		.= '<td align="left"><strong>'.\Helper::cashFormat($vegosszeg).' Ft</strong></td>';
			$msg 		.= '</tr>';
			$msg 		.= '<tr>';
				$msg 		.= '<td width="150" align="left">Tranzakció ideje</td>';
				$msg 		.= '<td align="left"><strong>'.NOW.'</strong></td>';
			$msg 		.= '</tr>';
		$msg 		.= '</tbody>';
		$msg 		.= '</table>';
		$msg 		.= '<br /><br />';
		$msg 		.= 'A megrendelés állapota automatikusan <strong>"PayPal visszaigazolásra vár"</strong> állapotra lett állítva!';

		$msg 		.= '<br /><br />';
		$msg 		.= '<span style="color:#CC0000;">A tranzakció tájékoztató jellegű. A befizetést a PayPal fiókjában is ellenőrizni kell, hogy a befizetés megtörtént-e.</span>';

		$param[msg] 		= $msg;
		$param[elnevezes] 	= 'Értesítő';

		$remsg = \Helper::smtpMail(array(
			'recepiens' => array(ALERT_EMAIL),
			'msg' 	=> $msg,
			'tema' 	=> 'Értesítő',
			'from' 	=> NOREPLY_EMAIL,
			'sub' 	=> 'PayPal befizetés: '.$order[azonosito].' megrendelés'
		));
	}

	public $messageType = array( 'ajanlat', 'recall', 'requesttermprice', 'requesttermask' );

	function logMessage($opts = array()){
		$item_id 		= 'NULL';

		extract($opts);

		if($felado_nev == '' || $uzenet_targy == '' || $uzenet == '' || $tipus == '') return false;

		if(!in_array($tipus,$this->messageType)) return false;

		$this->db->insert("uzenetek",
			array(
				'item_id' => $item_id,
				'felado_email' => $felado_email,
				'felado_telefon' => $felado_telefon,
				'felado_nev' => $felado_nev,
				'uzenet_targy' => $uzenet_targy,
				'uzenet' => $uzenet,
				'tipus' => $tipus
			)
		);
	}

	public function setSeenUnwatchedDocuments( $uid, $file_list = array() )
	{
		if( !$uid ) return false;
		if( count($file_list) == 0 ) return false;

		foreach ($file_list as $group => $files) {
			if( $files )
			foreach ( $files as $file )
			{
				if( !$file['watched'] )
				{

					$this->db->insert(
						'shop_documents_viewed',
						array(
							'doc_id' 	=> $file['doc_id'],
							'felh_id' 	=> $uid
						)
					);
				}
			}
		}
	}

	public function getDocumentGroupes()
	{
		return $this->document_groups;
	}
	public function getDocumentGroupeColors()
	{
		return $this->document_group_colors;
	}

	public function checkDocuments( $user, \FileManager\FileLister $files, $arg = array() )
	{
		$docs = array();
		$temp = array();

		// Felhasználó konténerei, amibe benne van
		$user_container_ids = array();

		if($user)
		{
			$user_containers 	= $this->db->squery($iq = "SELECT container_id FROM ".\PortalManager\Users::TABLE_CONTAINERS_XREF." WHERE user_id = :id;", array( 'id' => $user[ID] ))->fetchAll(\PDO::FETCH_ASSOC);

			if (count($user_containers) > 0)
			{
				foreach ($user_containers as $c) {
					$user_container_ids[] = $c['container_id'];
				}
			}
		}


		if( true )
		foreach ( $files->getFolderItems() as $file )
		{
			$file['hashname'] = md5( $file['name'] );

			// Check
			$cq = "SELECT
			d.ID,
			d.cim,
			d.feltoltve,
			d.lathato,
			d.szaktanacsado_only,
			d.groupkey,
			d.user_group_in,
			d.user_container_in,
			(SELECT SUM(clicked) FROM shop_documents_click WHERE doc_id = d.ID) as clicks
			FROM shop_documents as d
			WHERE 1=1 ";

			if( $arg['showHided'] === true ) {

			} else {
				$cq .= " and d.lathato = 1 ";
			}

			$cq .= " and d.hashname = '{$file[hashname]}'";

			if( isset($arg['user_group_in']) ) {
				$cq .= " and d.user_group_in LIKE '%".$arg['user_group_in']."%' ";
			}

			$c = $this->db->query( $cq );

			if( $c->rowCount() != 0 )
			{
				$data = $c->fetch(\PDO::FETCH_ASSOC);

				$file['click'] 			= $data['clicks'];
				$file['doc_title'] 		= $data['cim'];
				$file['user_group_in'] 	= $data['user_group_in'];
				$file['doc_uploaded'] 	= $data['feltoltve'];
				$file['doc_id'] 		= $data['ID'];
				$file['doc_icon_fa'] 	= $icon;
				$file['doc_groupkey'] 	= $data['groupkey'];
				$file['doc_group'] 		= $this->document_groups[$data['groupkey']];
				$file['watched'] 		= $watched;
				$file['lathato'] 		= $data['lathato'];
				$file['szaktanacsado_only'] = $data['szaktanacsado_only'];

				$temp[$file['hashname']] = $file;

			}
			elseif( $arg['showOffline'] === true )
			{
				$temp[$file['hashname']] = $file;
			}
		}

		// External links

		$eq = "SELECT d.*, (SELECT SUM(clicked) FROM shop_documents_click WHERE doc_id = d.ID) as clicks FROM shop_documents as d WHERE 1=1 ";

		if( isset($arg['showOffline']) ) {

		}

		if( isset($arg['user_group_in']) ) {
			$eq .= " and d.user_group_in LIKE '%".$arg['user_group_in']."%' ";
		}

		$eq .= " ORDER BY sorrend ASC;";

		$links_q = $this->db->query($eq);

		$links = $links_q->fetchAll(\PDO::FETCH_ASSOC);

		if( $links_q->rowCount() != 0 )
		{
			foreach ( $links as $d ) {
				$row = $d;

				if (!is_null($d['user_container_in'])) {
					$cont_loc = explode(",",$d['user_container_in']);
					$cont_loc_allow = true;

					foreach ($cont_loc as $cid )
					{
						if (!$cont_loc_allow) {
							break;
						}

						if ($user && !in_array($cid, $user_container_ids)) {
							$cont_loc_allow = false;
						}
					}

					if (!$cont_loc_allow) {
						continue;
					}
				}
				if ($d['tipus'] == 'local')
				{
					$row = array_merge($row, $temp[$d['hashname']]);

					switch ( $row['extension'] )
					{
						case 'doc': case 'docx':
							$icon = 'file-word-o';
						break;
						case 'xlsx': case 'xls':
							$icon = 'file-excel-o';
						break;
						case 'rar': case 'zip':
							$icon = 'file-archive-o';
						break;
						case 'pdf':
							$icon = 'file-pdf-o';
						break;
						case 'jpg': case 'gif': case 'png': case 'jpeg':
							$icon = 'file-photo-o';
						break;
						default:
							$icon = 'external-link';
						break;
					}
				} else
				{
					$row['extension'] = 'link';
				}

				// Check watched
				$watched 	= false;
				if( $user )
				{
					$cq = "SELECT 1 FROM shop_documents_viewed WHERE felh_id = '{$user[ID]}' and doc_id = '{$d['ID']}';";

					$is_watched = $this->db->query($cq)->fetchColumn();

					if( $is_watched ) {
						$watched  = true;
					}
				}

				$row['click'] 			= $d['clicks'];
				$row['src_path'] 		= $d['filepath'];
				$row['doc_title'] 		= $d['cim'];
				$row['doc_uploaded'] 	= $d['feltoltve'];
				$row['doc_id'] 			= $d['ID'];
				$row['doc_icon_fa'] 	= $icon;
				$row['doc_groupkey'] 	= $d['groupkey'];
				$row['doc_group'] 		= $this->document_groups[$d['groupkey']];
				$row['watched'] 		= $watched;

				$docs[] = $row;
			}
		}

		// Stacking by group
		if ( isset($arg['stacked']) )
		{
			$stack = array();

			if ($docs)
			foreach ( $docs as $d ) {
				$stack[$d['doc_groupkey']][] = $d;
			}

			// Reset
			$docs = array();

			foreach ($this->document_groups as $key => $value)
			{
				if($stack[$key])
				{
					$docs[$key] = $stack[$key];
				}
			}
			unset($stack);
		}

		return $docs;
	}

	public function getDocuments()
	{
		$data = array();

		$qry = "SELECT
			d.*,
			d.ID as doc_id
		FROM shop_documents as d
		WHERE 1=1 and d.lathato = 1 ";

		$qry .= " ORDER BY d.sorrend ASC, d.cim ASC";
		$list = $this->db->query( $qry );

		if ( $list->rowCount() != 0 ) {
			$lista = $list->fetchAll(\PDO::FETCH_ASSOC);
			foreach ( $lista as $doc ) {
				$xcim = explode(".", $doc['filepath']);
				$ext = ($doc['tipus'] == 'external') ? false : end($xcim);
				$doc['ext'] = $ext;
				$data[] = $doc;
			}
			return $data;
		} else {
			return $data;
		}
	}

	public function saveTermDocuments( $termid = 0, $docsids = array() )
	{
		$synced = 0;
		if ( !empty( $docsids ) ) {
			foreach ( $docsids as $docid ) {
				$check = $this->db->query("SELECT ID FROM shop_documents_termek_xref WHERE termek_id = {$termid} and doc_id = {$docid}");

				if ( $check->rowCount() == 0 ) {
					$synced++;
					$this->db->insert(
						'shop_documents_termek_xref',
						array(
							'termek_id' => $termid,
							'doc_id' => $docid
						)
					);
				}
			}
		}

		return $synced;
	}

	public function removeDocumentFromTerm( $termid = 0, $docid = 0)
	{
		$this->db->query("DELETE FROM shop_documents_termek_xref WHERE termek_id = {$termid} and doc_id = {$docid}");
	}

	public function getDocumentList( $termid = 0 )
	{
		$data = array();

		$qry = "SELECT
			d.*,
			dx.doc_id
		FROM shop_documents_termek_xref as dx
		LEFT OUTER JOIN shop_documents as d ON d.ID = dx.doc_id
		WHERE 1=1 and d.lathato = 1 ";

		$qry .= sprintf(" and dx.termek_id = %d", $termid );
		$qry .= " ORDER BY dx.sort ASC, d.cim ASC";

		$list = $this->db->query( $qry );

		if ( $list->rowCount() != 0 ) {
			$lista = $list->fetchAll(\PDO::FETCH_ASSOC);
			foreach ( $lista as $doc ) {
				$xcim = explode(".", $doc['filepath']);
				$ext = ($doc['tipus'] == 'external') ? false : end($xcim);
				$doc['ext'] = $ext;
				$data[] = $doc;
			}
			return $data;
		} else {
			return $data;
		}
	}

	public function registerFileDocument( $post )
	{
		extract($post);

		if( empty( $name ) ) throw new \Exception( 'Kérjük, hogy adja meg a regisztrálandó fájl nevét!' );

		$this->db->insert(
			'shop_documents',
			array(
				'hashname' 	=> md5($filename),
				'cim' 		  => addslashes(trim($name)),
				'keywords'  => addslashes(trim($keywords)),
				'filepath' 	=> $filepath
			)
		);
	}

	public function getFactoryList( $row = 'm.*', $arg = array() )
	{
		$data = array();

		$qry = "SELECT
			{$row}
		FROM shop_markak as m
		WHERE 1=1 ";

		if ( isset($arg['onlyimaged']) ) {
			$qry .= " and m.image IS NOT NULl";
		}

		if ( isset($arg['order']) ) {
			$qry .= " ORDER BY ".$arg['order'];
		} else {
			$qry .= " ORDER BY rand() ";
		}

		$list = $this->db->query( $qry );

		if ( $list->rowCount() != 0 ) {
			$data = $list->fetchAll(\PDO::FETCH_ASSOC);
			return $data;
		} else {
			return $data;
		}
	}

	public function __destruct()
	{
		$this->db = null;
		$this->user = null;
	}
}

?>
