<?
namespace PortalManager;

class Installer
{
  const MODULETABLE = 'modules';
  private $db = null;
  private $table = null;
  private $engine = 'MyISAM';
  private $charset = 'utf8';

  public function __construct( $arg = array() )
  {
    $this->db = $arg[db];
    return $this;
  }

  public function listModules( $arg = array() )
  {
    $q = "SELECT
    m.*
    FROM ".self::MODULETABLE." as m
    WHERE 1=1 ";

    if (isset($arg['only_active'])) {
      $q .= " and m.active = 1";
    }

    $q .= " ORDER BY m.pos ASC";

    $data = $this->db->query($q);

    if ($data->rowCount() != 0) {
      $data = $data->fetchAll(\PDO::FETCH_ASSOC);

      return $data;
    } else return array();
  }

  public function setTable( $table )
  {
    $this->table = $table;
  }

  public function createTable( $query = false )
  {
    // Drop table
    if ( $this->table && $query ) {
      $this->db->query( "DROP TABLE IF EXISTS `".$this->table."`" );
      $this->db->query( "CREATE TABLE `".$this->table."` ".$query." ENGINE=".$this->engine." DEFAULT CHARSET=".$this->charset );
    }
  }

  public function addIndexes ( $query = false )
  {
    if ( $this->table && $query ) {
      $this->db->query("ALTER TABLE `".$this->table."` ".$query);
    }
  }

  public function addIncrements ( $query = false )
  {
    if ( $this->table && $query ) {
      $this->db->query("ALTER TABLE `".$this->table."` ".$query);
    }
  }

  public function setModulInstalled( $classname, $menutitle, $slug, $faico = 'gear', $pos = 100 )
  {
    $check_installed = $this->db->query("SHOW TABLES LIKE '".$this->table."'")->fetchColumn();

    if ( $check_installed === false ) {
      return false;
    }

    $this->db->insert(
      self::MODULETABLE,
      array(
        'menu_title' => addslashes($menutitle),
        'menu_slug' => $slug,
        'classname' => addslashes($classname),
        'faico' => $faico,
        'active' => 1,
        'pos' => $pos
      )
    );

    return true;
  }

  public function __destruct()
  {
    $this->db = null;
    $this->table = null;
  }
}
