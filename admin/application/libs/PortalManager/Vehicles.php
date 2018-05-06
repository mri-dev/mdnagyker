<?
namespace PortalManager;

use Interfaces\InstallModules;

/**
* class Vehicles
* @package PortalManager
* @version v1.0
*/
class Vehicles implements InstallModules
{
  const DBTABLE = 'Vehicles';
  const MODULTITLE = 'Gépjárművek';

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

  public function getTree( $arg = array() )
  {
    $tree = array();

    // Legfelső színtű kategóriák
    $qry = "
      SELECT *
      FROM ".self::DBTABLE."
      WHERE 1=1 ";

    // ID SET
    if( isset($arg['id_set']) && count($arg['id_set']) )
    {
      $qry .= " and ID IN (".implode(",",$arg['id_set']).") ";
    }

    $qry .= " ORDER BY title ASC;";

    $top_cat_qry = $this->db->query($qry);
    $top_cat_data = $top_cat_qry->fetchAll(\PDO::FETCH_ASSOC);

    if( $top_cat_qry->rowCount() == 0 ) return $this;

    foreach ( $top_cat_data as $top_cat ) {
      $this->tree_items++;
      $this->tree_steped_item[] = $top_cat;

      $top_cat['kep']	= ($top_cat['kep'] == '') ? '/src/images/no-image.png' : $top_cat['logo'];

      // Alelemek betöltése
      $top_cat['child'] = $this->getChildItems($top_cat['ID']);
      $tree[] = $top_cat;
    }

    $this->tree = $tree;

    return $this;
  }

  /**
	 * Alelemek listázása
	 * @param  int $parent_id	Szülő ID
	 * @return array 			Szülő al-elemei
	 */
	public function getChildItems( $parent_id )
	{
		$tree = array();

		// Gyerek elemek
		$child_cat_qry 	= $this->db->query( sprintf("
			SELECT *
			FROM ".self::DBTABLE."
			WHERE	parent_id = %d
			ORDER BY title ASC;", $parent_id));

		$child_cat_data	= $child_cat_qry->fetchAll(\PDO::FETCH_ASSOC);

		if( $child_cat_qry->rowCount() == 0 ) return false;

		foreach ( $child_cat_data as $child_cat ) {
			$this->tree_items++;
			$child_cat['kep']	= ($child_cat['kep'] == '') ? '/src/images/no-image.png' : $child_cat['logo'];
			$this->tree_steped_item[] = $child_cat;

			$child_cat['child'] = $this->getChildItems($child_cat['ID']);
			$tree[] = $child_cat;
		}

		return $tree;
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

    $installer->setTable( self::DBTABLE );

    // Tábla létrehozás
    $table_create =
    "(
      `ID` mediumint(9) NOT NULL,
      `title` varchar(150) NOT NULL,
      `slug` varchar(150) NOT NULL,
      `logo` text,
      `parent_id` mediumint(9) DEFAULT NULL
    )";
    $installer->createTable( $table_create );

    // Indexek
    $index_create =
    "ADD PRIMARY KEY (`ID`),
    ADD KEY `title` (`title`),
    ADD KEY `parent_id` (`parent_id`)";
    $installer->addIndexes( $index_create );

    // Increment
    $inc_create =
    "MODIFY `ID` mediumint(9) NOT NULL AUTO_INCREMENT";
    $installer->addIncrements( $inc_create );

    // Modul instalállás mentése
    $installed = $installer->setModulInstalled( __CLASS__, self::MODULTITLE, 'gepjarmuvek' , 'car' );

    return $installed;
  }
}
