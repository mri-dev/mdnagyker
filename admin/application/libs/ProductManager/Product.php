<?
namespace ProductManager;


/**
* class Product
* @package ProductManager
* @version 1.0
*/
class Product
{
	private $id = false;
	private $product = array(
		'cikkszam' => NULL,
		'nev' => NULL,
		'marka' => NULL,
		'rovid_leiras' => NULL,
		'meta_title' => NULL,
		'meta_desc' => NULL,
		'leiras' => NULL,
		'szallitas' => 0,
		'allapot' => 0,
		'akcios' => 0,
		'ujdonsag' => 0,
		'arukereso' => 1,
		'argep' => 1,
		'pickpackpont' => 1,
		'no_cetelem' => 0,
		'garancia' => false,
		'cat' => false,
		'ar' => 0,
		'ar_netto' => 0,
		'ar_brutto' => 0,
		'ar_akcios' => 0,
		'ar_akcios_netto' => 0,
		'ar_akcios_brutto' => 0,
		'ar_egyedi' => 0,
		'linkek' => false,
		'kepek' => false,
		'szin' => NULL,
		'meret' => NULL,
		'fotermek' => 1,
		'lathato' => 0,
		'raktar_keszlet' => 0,
		'tudastar_url' => NULL,
	);

	public function __construct() {
		return $this;
	}

	public function setData( $data_array = array() )
	{
		if( empty($data_array) ) return $this;

		foreach ( $data_array as $key => $value) {
			if (array_key_exists($key, $this->product)) {
				$value = ( $value != '' ) ? $value : NULL ;
				$this->product[$key] = $value;
			} else {
				$this->product[$key] = $value;
			}
		}

		return $this;
	}

	public static function modifyDescription ( $txt )
	{
		$txt = preg_replace('/<a/','<a class="zoom"',$txt);
		$txt = preg_replace('/\{.*\}/','',$txt);

		$txt = str_replace('../../../../src/uploads/',SOURCE.'uploads/',$txt);

		return $txt;
	}

	/*===============================
	=            SETTERS            =
	===============================*/

	public function setId( $value )
	{
		$this->id = $value;

		return $this;
	}

	/*-----  End of SETTERS  ------*/


	/*==============================
	=            GETTER            =
	==============================*/

	public function getVariable( $key )
	{
		if( !isset($this->product[$key]) ) return false;

		return $this->product[$key];
	}

	public function getId()
	{
		return $this->id;
	}

	public function getItemNumber()
	{
		return $this->product['cikkszam'];
	}

	public function getName()
	{
		return $this->product['nev'];
	}

	public function getTudastarURL()
	{
		return $this->product['tudastar_url'];
	}

	public function getManufacturerId()
	{
		return $this->product['marka'];
	}

	public function getShortDescription()
	{
		return $this->product['rovid_leiras'];
	}

	public function getMetaTitle()
	{
		return $this->product['meta_title'];
	}

	public function getMetaDesc()
	{
		return $this->product['meta_desc'];
	}

	public function getDescription()
	{
		return $this->product['leiras'];
	}

	public function getDownloads()
	{
		return $this->product['letoltesek'];
	}

	public function getTransportTimeId()
	{
		return $this->product['szallitas'];
	}

	public function getStatusId()
	{
		return $this->product['allapot'];
	}
	public function isVisible()
	{
		return $this->product['lathato'];
	}

	public function isDiscounted()
	{
		return ($this->product['akcios'] == 1 ? true : false );
	}

	public function isNewest()
	{
		return ($this->product['ujdonsag'] == 1 ? true : false );
	}

	public function isListedInArukereso()
	{
		return ($this->product['arukereso'] == 1 ? true : false );
	}

	public function isListedInArgep()
	{
		return ($this->product['argep'] == 1 ? true : false );
	}

	public function isAllowToPickPackPont()
	{
		return ($this->product['pickpackpont'] == 1 ? true : false );
	}

	public function isAllowToCsomagautomata()
	{
		return ($this->product['csomagautomata'] == 1 ? true : false );
	}
	public function isAllowCetelem()
	{
		return ($this->product['no_cetelem'] == 0 ? true : false );
	}


	public function getGuarantee()
	{
		return ($this->product['garancia']) ?: NULL;
	}

	public function getCategoryList()
	{
		return $this->product['cat'];
	}

	public function getColor()
	{
		return $this->product['szin'];
	}

	public function getSize()
	{
		return $this->product['meret'];
	}

	public function isMainProduct()
	{
		return ($this->product['fotermek'] == 1 ? true : false);
	}

	public function getStockNumber()
	{
		return ($this->product['raktar_keszlet'] > 0) ? (int)$this->product['raktar_keszlet'] : 0;
	}

	public function getLinks()
	{
		$linkek = false;
		if ( $this->product['linkek']['nev'] ) {
			$linkek = array();
			foreach ($this->product['linkek']['nev'] as $i => $nev) {
				$linkek[$nev] = $this->product['linkek']['url'][$i];
			}
		}

		return $linkek;
	}

	/**
	 * Termék árának lekérdezése
	 * @param  string $type netto vagy brutto
	 * @param  string $flag egyéb árak nevezői. Pl.: akcios
	 * @return float 		a termék ára
	 */
	public function getPrice( $type = false, $flag = false )
	{
		$flag = ( $flag ) ? '_'.$flag : '';

		switch ( $type ) {
			case 'netto':
				return (float)$this->product['ar'.$flag.'_netto'];
				break;
			case 'brutto':
				return (float)$this->product['ar'.$flag.'_brutto'];
				break;
			default:
				return (float)$this->product['ar'.$flag];
				break;
		}
	}

	/*-----  End of GETTER  ------*/
}
?>
