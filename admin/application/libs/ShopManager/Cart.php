<?
namespace ShopManager;

/**
* class Cart
* @package ShopManager
* @version 1.0
*/
class Cart
{
	private $db = null;
	private $user = null;
	private $machine_id = null;
	private $settings = null;

	function __construct( $machine_id, $arg = array() )
	{
		$this->db = $arg[db];
		$this->user = $arg[user];
		$this->machine_id = $machine_id;

		if (isset($arg['settings'])) {
			$this->settings = $arg['settings'];
		}
	}

	public function get()
	{
		if ( !$this->machine_id ) {
			return false;
		}

		$re = array();
		$itemNum 	= 0;
		$totalPrice = 0;
		$uid = (int)$this->user[data][ID];

		// Clear cart if item num 0
		$this->db->query("DELETE FROM shop_kosar WHERE me <= 0 and gepID = {$this->machine_id};");

		$q = "SELECT
			c.ID,
			c.termekID,
			c.me,
			c.hozzaadva,
			c.configs,
			t.pickpackszallitas,
			t.nev as termekNev,
			t.meret,
			t.szin,
			t.mertekegyseg,
			t.mertekegyseg_ertek,
			ta.elnevezes as allapot,
			t.profil_kep,
			getTermekAr(c.termekID, ".$uid.") as ar,
			(getTermekAr(c.termekID, ".$uid.") * c.me) as sum_ar,
			szid.elnevezes as szallitasIdo
		FROM shop_kosar as c
		LEFT OUTER JOIN shop_termekek AS t ON t.ID = c.termekID
		LEFT OUTER JOIN shop_markak as m ON m.ID = t.marka
		LEFT OUTER JOIN shop_termek_allapotok as ta ON ta.ID = t.keszletID
		LEFT OUTER JOIN shop_szallitasi_ido as szid ON szid.ID = t.szallitasID
		WHERE t.lathato = 1 and c.gepID = ".$this->machine_id;

		$qry = $this->db->query($q);

		$data = $qry->fetchAll(\PDO::FETCH_ASSOC);

		$kedvezmenyes = false;
		if( $this->user && $this->user[kedvezmeny] > 0 ) {
			$kedvezmenyes = true;
		}

		foreach($data as $d){
			$d['mertekegyseg'] = trim($d['mertekegyseg']);

			if( $kedvezmenyes ) {
				\PortalManager\Formater::discountPrice( $d[ar], $this->user[kedvezmeny], true );
				\PortalManager\Formater::discountPrice( $d[sum_ar], $this->user[kedvezmeny], true );
			}

			if ($this->settings['round_price_5'] == '1')
			{
				$d[ar] = round($d[ar] / 5) * 5;
			}

			$itemNum 	+= $d[me];
			$totalPrice += $d[me] * $d[ar];
			$d['url'] 	= '/termek/'.\PortalManager\Formater::makeSafeUrl($d['termekNev'],'_-'.$d['termekID']);
			$d['profil_kep'] = \PortalManager\Formater::productImage($d['profil_kep'], false, \ProductManager\Products::TAG_IMG_NOPRODUCT );
			$d['configs'] = $this->collectConfigData($d['configs']);
			$d['mertekegyseg_egysegar'] = $this->calcEgysegAr($d['mertekegyseg'], $d['mertekegyseg_ertek'], $d['ar']);

			$dt[] = $d;
		}

		$re[itemNum] = $itemNum;
		$re[totalPrice] = $totalPrice;
		$re[totalPriceTxt] = number_format($totalPrice ,0, "", " ");
		$re[items] = $dt;

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

	public function calcEgysegAr( $me, $mevar, $price)
	{
		$ea = 0;
		$mert = $me;
		switch ( $me ) {
			case 'méter':
				$ea = $price / $mevar;
			break;
			case 'ml':
				$ea = $price / $mevar * 1000;
				$mert = 'l';
			break;
		}

		if ($ea == 0 || $mevar == 1) {
			return false;
		} else {
			return number_format($ea,2, ".", " ") . ' Ft/'.$mert;
		}
	}

	public function __destruct()
	{
		$this->db = null;
		$this->user = null;
		$this->settings = null;
	}
}
?>
