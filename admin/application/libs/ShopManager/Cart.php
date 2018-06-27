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

		$re 		= array();
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
			t.pickpackszallitas,
			CONCAT(m.neve,' ',t.nev) as termekNev,
			t.meret,
			t.szin,
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
		WHERE c.gepID = ".$this->machine_id;

		$qry = $this->db->query($q);

		$data = $qry->fetchAll(\PDO::FETCH_ASSOC);

		$kedvezmenyes = false;
		if( $this->user && $this->user[kedvezmeny] > 0 ) {
			$kedvezmenyes = true;
		}

		foreach($data as $d){
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
			$d['profil_kep'] = \PortalManager\Formater::productImage($d['profil_kep'], 75, \ProductManager\Products::TAG_IMG_NOPRODUCT );

			$dt[] = $d;
		}

		$re[itemNum]			= $itemNum;
		$re[totalPrice]			= $totalPrice;
		$re[totalPriceTxt]		= number_format($totalPrice,0,""," ");
		$re[items] 				= $dt;

		return $re;
	}

	public function __destruct()
	{
		$this->db = null;
		$this->user = null;
		$this->settings = null;
	}
}
?>
