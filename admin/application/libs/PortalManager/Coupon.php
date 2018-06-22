<?
namespace PortalManager;

class Coupon
{
	private $db;

	public 	$coupon_id;
	private $data;
	private $orderTotal = -1;
	private $exluded_user = false;

	public function __construct( $arg = array() )
	{
		$this->db = $arg['db'];

		return $this;
	}

	public function setExcludedUser( $id)
	{
		$this->exluded_user = $id;

		return $this;
	}

	public function setOrderTotal( $price = -1 )
	{
		$this->orderTotal = $price;

		return $this;
	}

	public function get( $coupon_id )
	{
		$this->coupon_id = $coupon_id;

		$qry = "
		SELECT 		*
		FROM 		".\PortalManager\Coupons::DB_TABLE."
		WHERE 		1=1 and coupon_code = '".$coupon_id."';";

		$get = $this->db->query( $qry );

		if( $get->rowCount() == 0 ) return $this;

		$this->data = $get->fetch(\PDO::FETCH_ASSOC);

		return $this;
	}

	public function create($post, $created_user_id = false)
	{
		extract($post);
		$for_products_keys = NULL;
		$active 		= ($created_user_id) ? 0 : 1;
		$author 		= ($created_user_id) ? $created_user_id : NULL;
		$admin_checked 	= ($created_user_id) ? 0 : 1;

		if ($created_user_id )
		{
			// Automatikus kuponkód generálás, ha nem admin hozza létre.
			if( empty($coupon_code) )
			{
				$coupon_code = 'CASADA-'.date('Y').'-'.strtoupper(strrev(uniqid()));
			}
		}

		if ( empty($coupon_code) ) {
			throw new \Exception("Kérjük, hogy adja meg a kuponnak a kódját!");
		}

		// Kuponkód ellenőrzése, hogy létezik-e már
		$check_code = $this->db->squery("SELECT 1 FROM ".\PortalManager\Coupons::DB_TABLE." WHERE coupon_code = :coupon_code;", array( 'coupon_code' => $coupon_code ))->rowCount();
		if ($check_code != 0) {
			throw new \Exception("Ez a kuponkód már létezik. Adjon meg más kuponkódot.");
		}

		if ( empty($name) ) {
			throw new \Exception("Kérjük, hogy adja meg a kuponnak az elnevezését! Ezt fogják látni a felhasználók is.");
		}

		if ( empty($discount_value) || $discount_value <= 0  ) {
			throw new \Exception("Kérjük, hogy adja meg a kupon kedvezmény értékét.");
		}
		if ( empty($discount_type) ) {
			throw new \Exception("Kérjük, hogy válassza ki a kupon kedvezmény típusát.");
		}

		// Termék specifikus
		if (isset($exlude_for_product))
		{
			if( count($for_products) != 0 )
			{
				foreach ($for_products as $key => $value) {
					$for_products_keys .= $key.",";
				}
			}

			$for_products_keys = rtrim($for_products_keys, ",");
		}


		// Kupon tulajdonos
		if (isset($has_author))
		{
			$author = $author_id;
		}

		$this->db->insert(
			\PortalManager\Coupons::DB_TABLE,
			array(
				'coupon_code' 	=> $coupon_code,
				'name' 			=> addslashes($name),
				'usage_left' 	=> $usage_left,
				'active_from' 	=> $active_from,
				'min_order_value'	=> ($min_order_value == 0) ? NULL : $min_order_value,
				'active_to' 	=> $active_to,
				'for_products' 	=> $for_products_keys,
				'discount_type' => $discount_type,
				'discount_value'=> $discount_value,
				'active' 		=> $active,
				'author' 		=> $author,
				'admin_checked'	=> $admin_checked
			)
		);

		// TODO: Admin értesítés, ha felhasználó regisztrál kupont
		if ( $created_user_id )
		{
			# code...
		}
	}

	public function delete()
	{
		if (  !$this->coupon_id ) return false;

		$this->db->squery("DELETE FROM ".\PortalManager\Coupons::DB_TABLE." WHERE coupon_code = :id", array('id'=> $this->coupon_id));
	}

	public function save($post)
	{
	 	extract($post);
		$for_products_keys = NULL;
		$active 		= ($created_user_id) ? 0 : 1;
		$author 		= NULL;

		if ( empty($name) ) {
			throw new \Exception("Kérjük, hogy adja meg a kuponnak az elnevezését! Ezt fogják látni a felhasználók is.");
		}

		if ( empty($discount_value) || $discount_value <= 0  ) {
			throw new \Exception("Kérjük, hogy adja meg a kupon kedvezmény értékét.");
		}
		if ( empty($discount_type) ) {
			throw new \Exception("Kérjük, hogy válassza ki a kupon kedvezmény típusát.");
		}

		// Termék specifikus
		if (isset($for_products))
		{
			if( count($for_products) != 0 )
			{
				foreach ($for_products as $key => $value) {
					$for_products_keys .= $key.",";
				}
			}

			$for_products_keys = rtrim($for_products_keys, ",");
		}

		// Kupon tulajdonos
		if ( !empty($author_id) )
		{
			$author = $author_id;
		}


		$this->db->update(
			\PortalManager\Coupons::DB_TABLE,
			array(
				'name' 			=> addslashes($name),
				'usage_left' 	=> $usage_left,
				'min_order_value'	=> ($min_order_value == 0) ? NULL : $min_order_value,
				'active_from' 	=> $active_from,
				'active_to' 	=> $active_to,
				'for_products' 	=> $for_products_keys,
				'discount_type' => $discount_type,
				'discount_value'=> $discount_value,
				'active' 		=> $active,
				'author' 		=> $author
			),
			"coupon_code = '".$this->getCode()."'"
		);
	}

	/*===============================
	=            GETTERS            =
	===============================*/

	public function getTitle()
	{
		return $this->data['name'];
	}

	public function getCode()
	{
		return $this->data['coupon_code'];
	}

	public function isActive()
	{
		return ( $this->data['active'] == '1' ) ? true : false;
	}

	public function getType()
	{
		return $this->data['discount_type'];
	}

	public function getValue()
	{
		return $this->data['discount_value'];
	}

	public function isOffForAllProduct()
	{
		$flag = true;

		if( !is_null($this->data['for_categories']) ) 	$flag = false;
		if( !is_null($this->data['for_products']) ) 	$flag = false;

		return $flag;
	}

	public function getDiscountStr()
	{
		switch ( $this->getType() ) {
			case 'percentage':
				return '-'.$this->getValue().' %';
			break;
			case 'cash':
				return '-'.$this->getValue().' Ft';
			break;
		}
	}

	public function getMinOrderValue()
	{
		if ( is_null($this->data['min_order_value']) ) {
			return false;
		}

		return (int)$this->data['min_order_value'];
	}

	public function isReachedPriceLimit()
	{
		if ( !$this->getMinOrderValue() ) {
			return true;
		}

		if ( $this->orderTotal == -1) {
			return true;
		}

		if ( $this->orderTotal != -1 && $this->orderTotal >= $this->getMinOrderValue() ) {
			return true;
		}


		return false;
	}

	public function limitLeft()
	{
		return (int)$this->data['usage_left'];
	}

	public function validFromDate()
	{
		return $this->data['active_from'];
	}

	public function validToDate()
	{
		return $this->data['active_to'];
	}

	public function isRunning()
	{
		$run = true;

		if( !$this->isActive() ) $run = false;
		if( $this->exluded_user && $this->hasAuthor() && $this->exluded_user == $this->getAuthorID() ) $run = false;
		if( (strtotime(NOW)-strtotime($this->data['active_from'])) < 0 || (strtotime(NOW)-strtotime($this->data['active_to'])) > 0) $run = false;
		if( $this->limitLeft() <= 0 ) $run = false;
		if ( !$this->isReachedPriceLimit() ) $run = false;

		return $run;
	}

	public function isProductExluded()
	{
		if( !is_null($this->data['for_products']) )
		{
			return true;
		}

		return false;
	}

	/*=====  End of GETTERS  ======*/

	public function thisProductAllowed( $articleid )
	{
		$allowed_articles = explode(",",$this->data['for_products']);

		if ( in_array($articleid, $allowed_articles) )
		{
			return true;
		}

		return false;
	}

	public function getAllowedProducts()
	{
		if ( !$this->isProductExluded() )
		{
			return false;
		}

		$allowed_articles = explode(",",$this->data['for_products']);

		return $allowed_articles;
	}

	public function hasAuthor()
	{
		return ( is_null($this->data['author']) )  ? false : true;
	}

	public function getAuthorID()
	{
		return $this->data['author'];
	}

	public function getAuthor()
	{
		return $this->db->squery("SELECT nev, email FROM ".\PortalManager\Users::TABLE_NAME." WHERE ID = :id", array('id' => $this->getAuthorID()))->fetch(\PDO::FETCH_ASSOC);
	}

	/**
	 * Kedvezmény mennyiségének kiszámolása
	 * */
	public function calcPrice( $price )
	{
		$calc = 0;

		switch ( $this->getType() ) {
			case 'percentage':
				$calc = $price / 100 * $this->getValue();
			break;
			case 'cash':
				$calc = $this->getValue();
			break;
		}

		return $calc;
	}

	/**
	 * Kedvezmény leszámítás
	 * */
	public function discountPrice( $old_price )
	{
		$calc = 0;

		switch ( $this->getType() ) {
			case 'percentage':
				$calc = $old_price - ($old_price / 100 * $this->getValue());
			break;
			case 'cash':
				$calc = $old_price - $this->getValue();
			break;
		}

		return $calc;
	}

	// Felhasználás logolása
	public function used()
	{
		$this->db->query("UPDATE ".\PortalManager\Coupons::DB_TABLE." SET usage_left = usage_left - 1 WHERE coupon_code = '".$this->getCode()."';");
	}

	public function __destruct()
	{
		$this->db 			= null;
		$this->coupon_id 	= null;
		$this->data 		= null;
	}
}

?>
