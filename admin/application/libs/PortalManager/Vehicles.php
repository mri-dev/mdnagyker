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
  const DBSELECTED = 'Vehicles_selected';
  const DBSHOPXREF = 'Vehicles_shop_termek_xref';
  const DBXREFCREATIONRESTRICT = 'Vehicles_shop_termek_xref_creationrestrict';
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

  /**
	 * Létrehzás
	 * @param array $data új elem létrehozásához szükséges adatok
	 * @return void
	 */
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

  /**
	 * Szerkesztése
	 * @param  Vehicle $item PortalManager\Vehicle class
	 * @param  array    $new_data
	 * @return void
	 */
	public function edit( Vehicle $item, $new_data = array() )
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

  public function delete( Vehicle $item )
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

    foreach ( $top_item_data as $top_cat ) {
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

		$child_item_data	= $child_cat_qry->fetchAll(\PDO::FETCH_ASSOC);

		if( $child_cat_qry->rowCount() == 0 ) return false;

		foreach ( $child_item_data as $child_cat ) {
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

  public function prepareTreeForSelector( $tree = array() )
  {
    $t = array();

    foreach ( (array)$tree as $tr) {
      $child = $tr['child'];
      if ( !empty($child) ) {
        $tr['child'] = $this->prepareTreeForSelector( $child );
      }
      $t[$tr['ID']] = $tr;
    }

    return $t;
  }

  public function saveFilter( $mid, $ids = array() )
  {
    if ($mid == '') {
      return false;
    }

    // Remove previous
    $this->db->query("DELETE FROM ".self::DBSELECTED." WHERE mid = '{$mid}'");

    if (!empty($ids)) {
      $insert = array();
      $header = array('mid', 'vehicle_id');
      foreach ( (array)$ids as $id ) {
        $insert[] = array($mid, (int)$id);
      }
      unset($ids);
      if (!empty($insert)) {
        $this->db->multi_insert(
          self::DBSELECTED,
          $header,
          $insert
        );
      }
    }
  }

  public function getProductCompatibilityList( $product_id = 0 )
  {
    $ret = array();
    $re = array();

    $q = "SELECT
      v.parent_id,
      v.title,
      x.ID as xid,
      x.vehicle_id,
      vp.title as parent_title
    FROM ".self::DBSHOPXREF." as x
    LEFT OUTER JOIN ".self::DBTABLE." as v ON v.ID = x.vehicle_id
    LEFT OUTER JOIN ".self::DBTABLE." as vp ON vp.ID = v.parent_id
    WHERE 1=1 ";

    $q .= " and x.termek_id = " . $product_id;

    $qry = $this->db->query( $q );

    if ( $qry->rowCount() != 0 )
    {
      $data = $qry->fetchAll(\PDO::FETCH_ASSOC);

      foreach ($data as $d)
      {
        if ( empty($d['parent_id']) ) {
          if ( !isset($ret[$d['vehicle_id']]) ) {
            $ret[$d['vehicle_id']] = $d;
          }
        } else {
          $d['creation_restricts'] = $this->getCreationRestricts($d['xid']);
          $ret[$d['parent_id']]['title'] = $d['parent_title'];
          $ret[$d['parent_id']]['vehicle_id'] = $d['parent_id'];
          $ret[$d['parent_id']]['parent_id'] = null;
          $ret[$d['parent_id']]['models'][$d['vehicle_id']] = $d;
        }
      }
    }

    return $ret;
  }

  public function getSelectedQueryFilterIDS( $mid, $more_info = false )
  {
    $re = false;
    $ret = array();
    if ($mid == '') {
      return false;
    }

    $q = "SELECT
    s.vehicle_id,
    v.parent_id
    FROM ".self::DBSELECTED." as s
    LEFT OUTER JOIN ".self::DBTABLE." as v ON v.ID = s.vehicle_id
    WHERE 1=1 and s.mid = '{$mid}'";

    $qry = $this->db->query( $q );

    if ( $qry->rowCount() != 0 )
    {
      $re = array();
      $data = $qry->fetchAll(\PDO::FETCH_ASSOC);
      foreach ( $data as $d ) {
        if ( is_null($d['parent_id']) ) {
          if( !isset($re[$d['vehicle_id']]) ){
            $re[$d['vehicle_id']] = array();
          }
        } else {
          $re[$d['parent_id']][] = $d['vehicle_id'];
        }
      }

      $ret = array();
      foreach ( $re as $rid => $r ) {
        if( is_array($r) && empty($r) ) {
          $ret[$rid] = $this->getChildIDS( $rid );
        } else {
          $ret[$rid] = $r;
        }
      }
      unset($re);
    }

    if ( !$more_info ) {
      $coll = array();
      foreach ($ret as $rid => $r ) {
        foreach ($r as $rd) {
          $coll[] = $rd;
        }
      }
      $ret = $coll;
      unset($coll);
    }

    return $ret;
  }

  public function getCreationRestricts( $xref_id = 0 )
  {
    $ret = array();

    $q = "SELECT s.ID, s.title, s.ydate_start, s.ydate_end FROM ".self::DBXREFCREATIONRESTRICT." as s WHERE s.xref_id = ".$xref_id. " ORDER BY s.ydate_start ASC, s.ydate_end ASC ";

    $qry = $this->db->query($q);

    if ( $qry->rowCount() == 0 ) {
      return array();
    } else {
      $data = $qry->fetchAll(\PDO::FETCH_ASSOC);
      $ret = array();
      foreach ($data as $d) {
        $ydate = '';
        if ( !is_null($d['ydate_start']) && !is_null($d['ydate_end']) ) {
          $ydate = $d['ydate_start'].' &mdash; '.$d['ydate_end'];
        } else if( !is_null($d['ydate_start']) && is_null($d['ydate_end'])){
          $ydate = $d['ydate_start'].'-tól';
        } else if( is_null($d['ydate_start']) && !is_null($d['ydate_end'])){
          $ydate = $d['ydate_end'].'-ig';
        }
        $d['ydate'] = $ydate;
        $ret[] = $d;
      }
      return $ret;
    }

    return $ret;
  }

  public function getChildIDS( $parent_id = 0 )
  {
    $q = "SELECT v.ID FROM ".self::DBTABLE." as v WHERE v.parent_id = ".$parent_id;

    $qry = $this->db->query($q);
    if ( $qry->rowCount() == 0 ) {
      return array();
    } else {
      $data = $qry->fetchAll(\PDO::FETCH_ASSOC);
      $ret = array();
      foreach ($data as $d) {
        $ret[] = (int)$d['ID'];
      }
      return $ret;
    }
  }

  public function getFilterIDS( $mid )
  {
    if ($mid == '') {
      return false;
    }

    $q = "SELECT
      vehicle_id
    FROM ".self::DBSELECTED."
    WHERE mid = '{$mid}'";

    $qry= $this->db->query($q);

    if ($qry->rowCount() == 0) {
      return array('num' => 0, 'ids' => array() );
    } else {
      $data = $qry->fetchAll(\PDO::FETCH_ASSOC);
      $ids = array();
      foreach ((array)$data as $id) {
        $ids[] = $id['vehicle_id'];
      }
      unset($data);
      return array('num' => $qry->rowCount(), 'ids' => $ids);
    }
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
      `ID` mediumint(9) NOT NULL,
      `title` varchar(150) NOT NULL,
      `slug` varchar(150) NOT NULL,
      `logo` text,
      `parent_id` mediumint(9) DEFAULT NULL,
      `deep` smallint(6) NOT NULL DEFAULT '0'
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

    /**
    * Vehicles_selected
    **/
    $installer->setTable( self::DBSELECTED );
    // Tábla létrehozás
    $table_create =
    "(
      `mid` varchar(25) NOT NULL,
      `vehicle_id` mediumint(9) NOT NULL
    )";
    $installer->createTable( $table_create );

    // Indexek
    $index_create =
    "ADD PRIMARY KEY (`ID`),
    ADD KEY `mid` (`mid`),
    ADD KEY `vehicle_id` (`vehicle_id`);";
    $installer->addIndexes( $index_create );

    /**
    * Vehicles_shop_termek_xref
    **/
    $installer->setTable( self::DBSHOPXREF );
    $table_create =
    "(
      `ID` int(11) NOT NULL,
      `vehicle_id` int(11) NOT NULL,
      `termek_id` int(11) NOT NULL
    )";
    $installer->createTable( $table_create );

    // Indexek
    $index_create =
    "ADD PRIMARY KEY (`ID`),
    ADD KEY `vehicle_id` (`vehicle_id`),
    ADD KEY `termek_id` (`termek_id`)";
    $installer->addIndexes( $index_create );

    // Increment
    $inc_create =
    "MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT";
    $installer->addIncrements( $inc_create );

    /**
    * Vehicles_shop_termek_xref_creationrestrict
    **/
    $installer->setTable( self::DBXREFCREATIONRESTRICT );
    // Tábla létrehozás
    $table_create =
    "(
      `ID` int(11) NOT NULL,
      `xref_id` int(11) NOT NULL,
      `title` text,
      `ydate_start` decimal(7,2) DEFAULT NULL,
      `ydate_end` decimal(7,2) DEFAULT NULL
    )";
    $installer->createTable( $table_create );

    // Indexek
    $index_create =
    "ADD PRIMARY KEY (`ID`),
    ADD KEY `xref_id` (`xref_id`)";
    $installer->addIndexes( $index_create );

    // Increment
    $inc_create =
    "MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT";
    $installer->addIncrements( $inc_create );



    // Modul instalállás mentése
    $installed = $installer->setModulInstalled( __CLASS__, self::MODULTITLE, 'gepjarmuvek' , 'car' );

    return $installed;
  }
}
