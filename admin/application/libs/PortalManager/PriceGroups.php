<?
namespace PortalManager;

use Interfaces\InstallModules;

/**
* class PriceGroups
* @package PortalManager
* @version v1.0
*/
class PriceGroups implements InstallModules
{
  const DBTABLE = 'shop_price_groups';
  const MODULTITLE = 'Ár csoportok';

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

	public function add( $data = array() )
	{
		$title = ($data['title']) ?: false;
		$key = ($data['groupkey']) ?: false;

		if ( !$title ) {
			throw new \Exception( "Kérjük, hogy adja meg az elem csoport elnevezését!" );
		}

    if ( !$key ) {
      throw new \Exception( "Kérjük, hogy válassza ki az ár illesztési kulcsot!" );
    }

		$this->db->insert(
			self::DBTABLE,
			array(
				'title'	=> $title,
        'groupkey' => $key
			)
		);
	}

	public function edit( PriceGroup $item, $new_data = array() )
	{
    $title = ($new_data['title']) ?: false;
    $key = ($new_data['groupkey']) ?: false;

    if ( !$title ) {
			throw new \Exception( "Kérjük, hogy adja meg az elem csoport elnevezését!" );
		}

    if ( !$key ) {
      throw new \Exception( "Kérjük, hogy válassza ki az ár illesztési kulcsot!" );
    }

		$item->edit(array(
			'title' => addslashes($title),
      'groupkey' => $key
		));
	}

  public function delete( PriceGroup $item )
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
      WHERE 1=1";

    // ID SET
    if( isset($arg['id_set']) && count($arg['id_set']) )
    {
      $qry .= " and ID IN (".implode(",",$arg['id_set']).") ";
    }

    $qry .= " ORDER BY title ASC;";

    $top_cat_qry = $this->db->query($qry);
    $top_item_data = $top_cat_qry->fetchAll(\PDO::FETCH_ASSOC);

    if( $top_cat_qry->rowCount() == 0 ) return $this;

    foreach ( $top_item_data as $top_cat ) {
      $this->tree_items++;
      $this->tree_steped_item[] = $top_cat;

      // Alelemek betöltése
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
    * Vehicles
    **/
    $installer->setTable( self::DBTABLE );
    // Tábla létrehozás
    $table_create =
    "(
      `ID` smallint(6) NOT NULL,
      `groupkey` varchar(20) NOT NULL,
      `title` text NOT NULL
    )";
    $installer->createTable( $table_create );

    // Indexek
    $index_create =
    "ADD PRIMARY KEY (`ID`),
    ADD UNIQUE KEY `groupkey` (`groupkey`)";
    $installer->addIndexes( $index_create );

    // Increment
    $inc_create =
    "MODIFY `ID` smallint(6) NOT NULL AUTO_INCREMENT";
    $installer->addIncrements( $inc_create );

    // Modul instalállás mentése
    $installed = $installer->setModulInstalled( __CLASS__, self::MODULTITLE, 'arcsoportok' , 'money' );

    return $installed;
  }
}
