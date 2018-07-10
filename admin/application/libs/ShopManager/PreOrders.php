<?
namespace ShopManager;

use Interfaces\InstallModules;
use MailManager\Mailer;
use MailManager\MailTemplates;
use PortalManager\Template;
use ShopManager\PreOrder;

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
  private $settings = array();
  public $tree = false;
	private $current_item = false;
	private $tree_steped_item = false;
	private $tree_items = 0;
	private $walk_step = 0;
  protected $api  = null;

  public function __construct( $arg = array() )
  {
    $this->db = $arg[db];
    $this->settings = (array)$this->db->settings;

    if( !$this->checkInstalled() && strpos($_SERVER['REQUEST_URI'], '/install') !== 0) {
      \Helper::reload('/install?module='.__CLASS__);;
    }

    return $this;
  }

  public function addAPIHandler( $apiobj )
  {
    $this->api = $apiobj;
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
    $hash = md5(uniqid());
    $mid = \Helper::getMachineID();

    //print_r($data);
    //print_r($cart);

    //return false;

    // Foglalás kezdete
    $start_date = date('Y-m-d H:i:s');
    // Foglalás fenntartása
    $end_date = date('Y-m-d H:i:s', strtotime('+'.$hour.' hours'));


    // Foglalás rögzítése
    $this->db->insert(
      self::DBTABLE,
      array(
        'sessionkey' => $hash,
        'gepID' => $mid,
        'name' => addslashes($data['name']),
        'email' => addslashes($data['email']),
        'requested_at' => $start_date,
        'valid_to' => $end_date
      )
    );

    // Új foglalás ID-ja
    $iid = $this->db->lastInsertId();

    // Kosár tételek beszúrása
    $cart_header = array('order_id', 'gepID', 'termekID', 'me', 'egysegAr');
    $cart_insert = array();
    $stock_set = array();

    foreach ( (array)$cart['items'] as $item )
    {
      $cart_insert[] = array( $iid, $mid, $item['termekID'], $item['me'], $item['prices']['current_each'] );
      $stock_set[] = array(
        'ID' => $item['termekID'],
        'stock' => ((int)$item['me'] * -1)
      );
    }

    if (!empty($cart_insert)) {
      $this->db->multi_insert(
        self::DBITEMS,
        $cart_header,
        $cart_insert
      );
    }
    unset($cart_header);
    unset($cart_insert);

    // Raktárkészlet fogyás elvégzése
    if ( $this->api && method_exists($this->api, 'updateStock') )
    {
      $this->api->updateStock( $stock_set );
    }
    unset($stock_set);

    // Kosár ürítése
    if ( false ) {
      $this->db->query("DELETE FROM shop_kosar WHERE gepID = '$mid'");
    }

    // E-mail értesítés
    if ( true )
    {
      // Értesítő e-mail az adminisztrátornak
      $mail = new Mailer(
        $this->settings['page_title'],
        SMTP_USER,
        $this->settings['mail_sender_mode']
      );
      $mail->add( $data['email'] );

      $arg = array(
        'settings' => $this->settings,
        'infoMsg' => 'Ezt az üzenetet a rendszer küldte. Kérjük, hogy ne válaszoljon rá!',
        'foglal_ora' => $this->settings['elofoglalas_ora'],
        'expire_at' => $end_date,
        'name' => $data['name'],
        'hash' => $hash,
        'mid' => $mid,
        'cart' => $cart['items']
      );
      $mail->setSubject( 'Előfoglalás visszaigazolás.' );
      $mail->setMsg( (new Template( VIEW . 'templates/mail/' ))->get( 'preorder_user', $arg ) );
      $re = $mail->sendMail();
    }

    unset($cart);
    unset($data);

    return $hash;
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
      SELECT ID
      FROM ".self::DBTABLE."
      WHERE 1=1 ";

    // ID SET
    if( isset($arg['id_set']) && count($arg['id_set']) )
    {
      $qry .= " and ID IN (".implode(",",$arg['id_set']).") ";
    }

    if( isset($arg['gepID']) && !empty($arg['gepID']) )
    {
      $qry .= " and gepID = '".$arg['gepID']."' ";
    }

    if( isset($arg['session']) )
    {
      $qry .= " and sessionkey = '".$arg['session']."' ";
    }

    if( isset($arg['sessionOrder']) && $arg['sessionOrder'] !== false )
    {
      $qry .= " ORDER BY (IF(sessionkey = '".$arg['sessionOrder']."', 1, 0)) DESC ,valid_to DESC";
    } else {
      $qry .= " ORDER BY valid_to DESC";
    }

    $top_cat_qry = $this->db->query($qry);
    $top_item_data = $top_cat_qry->fetchAll(\PDO::FETCH_ASSOC);

    if( $top_cat_qry->rowCount() == 0 ) return $this;

    foreach ( $top_item_data as $top_cat )
    {
      $this->tree_items++;
      $this->tree_steped_item[] = new PreOrder($top_cat['ID'], array('db' => $this->db));
      $tree[] = $top_cat;
    }

    $this->tree = $tree;

    return $this;
  }

  public function clearExpiredCRON()
  {
    $stock_set = array();
    $delete_orders = array();

    // Lejártak összegyűjtése
    $qry = "SELECT
      pt.order_id,
      pt.termekID,
      pt.me,
      p.sessionkey
    FROM `preorder_termekek` as pt
    LEFT OUTER JOIN preorder as p ON p.ID = pt.order_id
    WHERE now() >= p.valid_to";
    $qry = $this->db->query( $qry );

    if ($qry->rowCount() == 0) {
      return false;
    }

    $expires = $qry->fetchAll(\PDO::FETCH_ASSOC);

    foreach ( (array)$expires as $e )
    {
      $stock_set[] = array(
        'ID' => $e['termekID'],
        'stock' => (int)$e['me']
      );

      if (!in_array($e['order_id'], $delete_orders)) {
        $delete_orders[] = $e['order_id'];
      }
    }

    // Raktárkészlet frissítés
    if ( !empty($stock_set) )
    {
      if ( $this->api && method_exists($this->api, 'updateStock') )
      {
        $this->api->updateStock( $stock_set );
      }
    }
    unset($stock_set);

    // Összegyűjtött rendelések törlése
    foreach ( (array)$delete_orders as $oid )
    {
      // Tételek törlése
      $this->db->query("DELETE FROM ".self::DBITEMS." WHERE order_id = {$oid}");
      // Előfoglalás törlése
      $this->db->query("DELETE FROM ".self::DBTABLE." WHERE ID = {$oid}");
    }

    unset($delete_orders);
    unset($expires);
    unset($qry);
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

  public function itemNums()
  {
    return $this->tree_items;
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
      `gepID` varchar(20) NOT NULL,
      `name` varchar(250) NOT NULL,
      `email` varchar(250) NOT NULL,
      `requested_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
      `valid_to` datetime DEFAULT NULL
    )";
    $installer->createTable( $table_create );

    // Indexek
    $index_create =
    "ADD PRIMARY KEY (`ID`),
    ADD UNIQUE KEY `sessionkey` (`sessionkey`),
    ADD KEY `isconfirmed` (`isconfirmed`),
    ADD KEY `gepID` (`gepID`)";
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
