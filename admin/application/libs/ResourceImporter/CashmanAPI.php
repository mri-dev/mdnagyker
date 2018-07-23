<?php
namespace ResourceImporter;

use ResourceImporter\ResourceImportInterface;
use ResourceImporter\CashmanFxApi;

/**
 *
 */
class CashmanAPI extends ResourceImportBase
{
  public $api = null;
  const  AUTH_CSOPORT = 'MOBILD';
  const  AUTH_USER = 'Admin';
  const  AUTH_PW = 'Bencze117';

  function __construct( $arg = array() )
  {
    /***********************************************************************/
    $serv = "https://www.cmfx.hu/fxapi/jxs.php";
    $szla = "https://www.cmfx.hu/fx/kulso_szamla/main.html?";
    $szlapdf = "https://www.cmfx.hu/FxPrint/?restartApplication&";
    $sztmp = "https://www.cmfx.hu/jx/tmp";
    $etmp = "https://www.cmfx.hu/jx/epdf";

    $this->api = new CashmanFxApi($serv, $szla, $szlapdf, $sztmp, $etmp);

    return parent::__construct( $arg );
  }

  public function incFixRowData( &$row )
  {
    $row['Csoport'] = self::AUTH_CSOPORT;
  	$row['User'] = self::AUTH_USER;
  	$row['Password'] = self::AUTH_PW;

    return $row;
  }

  public function updateStock( $data = array() )
  {

  }

  public function addProduct( $row_set = array() )
  {
    $ret = array();
    $set = array();
    foreach ($row_set as $row) {
      $this->incFixRowData($row);
      $set[] = $row;
    }
    unset($row_set);

    if ( !empty($set) )
    {
      foreach ($set as $s) {
        $this->api->termekrogzites( $s );
        $ret[$s['cikkszam']] = array(
          'uzenet' => $this->api->uzenet,
          'hiba' => $this->api->hiba,
          'error' => ($this->api->hiba == '') ? 0 : 1
        );
      }
  		return $ret;
    }
    else return false;
  }


  public function __destruct()
  {
    $this->api = null;
    parent::__destruct();
  }
}
