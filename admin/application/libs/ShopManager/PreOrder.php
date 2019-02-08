<?
namespace ShopManager;

/**
* class PreOrder
* @package ShopManager
* @version 1.0
*/
class PreOrder
{
	private $db = null;
	private $id = false;
	private $data = false;
  private $items = array();
  public $item_numbers = 0;
  public $total_price = 0;

	function __construct( $id, $arg = array() )
	{
		$this->db = $arg[db];
		$this->id = $id;

		$this->get();

		return $this;
	}

	private function get()
	{
		$cat_qry 	= $this->db->query( sprintf("
			SELECT*
			FROM 	".\ShopManager\PreOrders::DBTABLE."
			WHERE ID = %d;", $this->id));
		$data = $cat_qry->fetch(\PDO::FETCH_ASSOC);
		$this->data = $data;
	}

	public function edit( $db_fields )
	{
		$this->db->update(
			\ShopManager\PreOrders::DBTABLE,
			$db_fields,
			"ID = ".$this->id
		);
	}

	/**
	 * Aktuális kategória törlése
	 * @return void
	 */
	public function delete()
	{
		$this->db->query(sprintf("DELETE FROM ".\ShopManager\PreOrders::DBTABLE." WHERE ID = %d",$this->id));
	}

	/*===============================
	=            GETTERS            =
	===============================*/

	public function getHashkey()
	{
		return $this->data['sessionkey'];
	}

  public function dateStart( $formated = false )
	{
    if ( !$formated ) {
      return $this->data['requested_at'];
    } else {
      return date('Y. m. d. H:i', strtotime($this->data['requested_at']));
    }

    return false;
	}

  public function dateEnd( $formated = false )
	{
    if ( !$formated ) {
      return $this->data['valid_to'];
    } else {
      return date('Y. m. d. H:i', strtotime($this->data['valid_to']));
    }

    return false;
	}

	public function expired()
	{
		$now = strtotime( NOW );
		$end = strtotime( $this->data['valid_to'] );

		if ( $now >= $end ) {
			return true;
		} else {
			return false;
		}
	}

	public function expireColor()
	{
		$now = strtotime( NOW );
		$end = strtotime( $this->data['valid_to'] );
		$diff = $end - $now;

		// < 1 óra
		if ($diff <= 3600)
		{
			return '#ff6161';
		}
		// < 8 óra
		else if( $diff > 3600 && $diff <= 28800){
			return '#ff8d00';
		}
	}

  public function itemNumbers()
  {
    return $this->item_numbers;
  }

  public function totalPrice()
  {
    return $this->total_price;
  }


  public function getItems()
  {
    $this->items = array();

    $get = $this->db->query(sprintf("SELECT
      i.*,
			t.cikkszam,
			t.profil_kep,
			t.nev as nev,
			m.neve as markaNev
    FROM ".\ShopManager\PreOrders::DBITEMS." as i
		LEFT OUTER JOIN shop_termekek as t ON t.ID = i.termekID
		LEFT OUTER JOIN shop_markak as m ON t.marka = m.ID
    WHERE 1=1 and i.order_id = %d
    ", $this->getId()));

    if ($get->rowCount() != 0)
    {
      $data = $get->fetchAll(\PDO::FETCH_ASSOC);
      $this->item_numbers = 0;

      foreach ((array)$data as $d)
      {
				$d['ar'] = round($d['egysegAr']) * $d['me'];
				$d['link'] = DOMAIN.'termek/'.\PortalManager\Formater::makeSafeUrl( $d['nev'], '_-'.$d['termekID'] );
				$kep = $d['profil_kep'];
				$d['profil_kep']=\PortalManager\Formater::productImage( $kep, false, \ProductManager\Products::TAG_IMG_NOPRODUCT );

        $this->total_price += round($d['egysegAr']) * $d['me'];
        $this->item_numbers += $d['me'];
        $this->items[] = $d;
      }
    }

    return $this->items;
  }

	public function getId()
	{
		return $this->data['ID'];
	}
	/*-----  End of GETTERS  ------*/

	public function __destruct()
	{
		$this->db = null;
		$this->data = false;
	}
}
?>
