<?
namespace ShopManager;

use Interfaces\InstallModules;

/**
* class PreOrders
* @package ShopManager
* @version v1.0
*/
class PreOrders implements InstallModules
{
  const DBTABLE = 'preorder';
  const DBITEMS = 'preorder_termekek';
  const MODULTITLE = 'Előfoglalások';

  private $db = null;
  public $tree = false;
	private $current_item = false;
	private $tree_steped_item = false;
	private $tree_items = 0;
	private $walk_step = 0;

  public function __construct( $arg = array() )
  {
    $this->db = $arg[db];

    if( !$this->checkInstalled() && strpos($_SERVER['REQUEST_URI'], '/install') !== 0) {
      \Helper::reload('/install?module='.__CLASS__);;
    }

    return $this;
  }

  public function validHour()
  {
    $hour = 24;

    $get = (int)$this->db->query("SELECT bErtek FROM beallitasok WHERE bKulcs = 'elofoglalas_ora'")->fetchColumn();

    $hour = ($get && $get != 0) ? $get : $hour;

    return $hour;
  }

  public function orderHandler( $data, $cart )
  {
    if ( empty($cart) || $cart['itemNum'] == 0 ) {
      throw new \Exception("Az Ön kosara üres. Így nem adhat le előfoglalást.");
    }

    $hour = $this->validHour();

    print_r($data);
    print_r($cart);

    // Foglalás kezdete
    $start_date = date('Y-m-d H:i:s');

    // Foglalás fenntartása
    $end_date = date('Y-m-d H:i:s', strtotime('+'.$hour.' hours'));


    // Foglalás rögzítése

    // Kosár tételek beszúrása
    foreach ( $cart['items'] as $item )
    {

    }

    // Kosár ürítése
    // E-mail értesítés
  }

	public function add( $data = array() )
	{
		$deep = 0;
		$name = ($data['name']) ?: false;
		$parent = ($data['parent_id']) ?: NULL;
    $image = ( isset($data['logo']) ) ? $data['logo'] : NULL;
    $slug = \Helper::makeSafeUrl(trim($name));

		if ($parent) {
			$xparent = explode('_',$parent);
			$parent = (int)$xparent[0];
			$deep = (int)$xparent[1] + 1;
		}

		if ( !$name ) {
			throw new \Exception( "Kérjük, hogy adja meg az elem elnevezését!" );
		}

		$this->db->insert(
			self::DBTABLE,
			array(
				'title'	=> $name,
        'slug' => $slug,
				'parent_id' => $parent,
				'deep' => $deep,
  			'logo' => $image,
			)
		);
	}
	public function edit( $item, $new_data = array() )
	{
		$deep = 0;
		$name = ($new_data['name']) ?: false;
		$parent = ($new_data['parent_id']) ?: NULL;
		$image = ( isset($new_data['logo']) ) ? $new_data['logo'] : NULL;
    $slug = \Helper::makeSafeUrl(trim($name));

		if ($parent) {
			$xparent = explode('_',$parent);
			$parent = (int)$xparent[0];
			$deep = (int)$xparent[1] + 1;
		}

		if ( !$name ) {
			throw new \Exception( "Kérjük, hogy adja meg a gépjármű elnevezését!" );
		}

		$item->edit(array(
			'title' => $name,
      'slug' => $slug,
			'parent_id' => $parent,
			'deep' => $deep,
			'logo' => $image
		));
	}

  public function delete( $item )
	{
		$item->delete();
	}

  public function getTree( $arg = array() )
  {
    $tree = array();

    // Legfelső színtű kategóriák
    $qry = "
      SELECT *
      FROM ".self::DBTABLE."
      WHERE 1=1 and parent_id IS NULL";

    // ID SET
    if( isset($arg['id_set']) && count($arg['id_set']) )
    {
      $qry .= " and ID IN (".implode(",",$arg['id_set']).") ";
    }

    $qry .= " ORDER BY title ASC;";

    $top_cat_qry = $this->db->query($qry);
    $top_item_data = $top_cat_qry->fetchAll(\PDO::FETCH_ASSOC);

    if( $top_cat_qry->rowCount() == 0 ) return $this;

    foreach ( $top_item_data as $top_cat )
    {
      $this->tree_items++;
      $this->tree_steped_item[] = $top_cat;
      $tree[] = $top_cat;
    }

    $this->tree = $tree;

    return $this;
  }

  public function walk()
	{
		if( !$this->tree_steped_item ) return false;

		$this->current_item = $this->tree_steped_item[$this->walk_step];

		$this->walk_step++;

		if ( $this->walk_step > $this->tree_items ) {
			// Reset Walk
			$this->walk_step = 0;
			$this->current_item = false;

			return false;
		}

		return true;
	}

  public function the_item()
	{
		return $this->current_item;
	}

  public function __destruct()
  {
    $this->db = null;
    $this->tree = false;
		$this->current_item = false;
		$this->tree_steped_item = false;
		$this->tree_items = 0;
		$this->walk_step = 0;
  }


  /*******************************
  * Installer
  ********************************/
  public function checkInstalled()
  {
    $check_installed = $this->db->query("SHOW TABLES LIKE '".self::DBTABLE."'")->fetchColumn();

    if ( $check_installed === false ) {
      $cn = addslashes(__CLASS__);
      $this->db->query("DELETE FROM modules WHERE classname = '$cn'");
    }

    return ($check_installed === false) ? false : true;
  }

  public function installer( \PortalManager\Installer $installer )
  {
    $installed = false;


    /**
    * preorder
    **/
    $installer->setTable( self::DBTABLE );
    // Tábla létrehozás
    $table_create =
    "(
      `ID` int(11) NOT NULL,
      `sessionkey` varchar(40) NOT NULL,
      `name` varchar(250) NOT NULL,
      `email` varchar(250) NOT NULL,
      `isconfirmed` tinyint(1) NOT NULL DEFAULT '0',
      `requested_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
      `valid_to` datetime DEFAULT NULL
    )";
    $installer->createTable( $table_create );

    // Indexek
    $index_create =
    "ADD PRIMARY KEY (`ID`),
    ADD UNIQUE KEY `sessionkey` (`sessionkey`),
    ADD KEY `isconfirmed` (`isconfirmed`)";
    $installer->addIndexes( $index_create );

    // Increment
    $inc_create =
    "MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT";
    $installer->addIncrements( $inc_create );

    /**
    * preorder_termekek
    **/
    $installer->setTable( self::DBITEMS );
    // Tábla létrehozás
    $table_create =
    "(
      `ID` int(7) NOT NULL,
      `order_id` int(10) NOT NULL,
      `gepID` int(10) NOT NULL,
      `termekID` int(11) NOT NULL,
      `me` int(4) NOT NULL DEFAULT '1',
      `egysegAr` int(7) NOT NULL DEFAULT '0',
      `egysegArKedvezmeny` int(11) DEFAULT '0' COMMENT 'Forint kedvezmény',
      `hozzaadva` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
    )";
    $installer->createTable( $table_create );

    // Indexek
    $index_create =
    "ADD PRIMARY KEY (`ID`),
    ADD KEY `gepID` (`gepID`),
    ADD KEY `termekID` (`termekID`),
    ADD KEY `orderKey` (`order_id`)";
    $installer->addIndexes( $index_create );

    // Increment
    $inc_create =
    "MODIFY `ID` int(7) NOT NULL AUTO_INCREMENT";
    $installer->addIncrements( $inc_create );

    // Modul instalállás mentése
    $installed = $installer->setModulInstalled( __CLASS__, self::MODULTITLE, 'pre_orders' , 'circle-thin' );

    return $installed;
  }
}
