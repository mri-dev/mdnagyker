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

  public function autoImportProducts( $list = array() )
  {
    $prepare = array();

    foreach ( (array)$list as $row )
    {
      $each = array();

      if ($row['cikkszam'] == '') {
        continue;
      }

      $each['prod_id'] = $row['cikkszam'];
      $each['termek_nev'] = $row['megnevezes'];
      $each['termek_keszlet'] = $row['keszlet'];
      $each['ean_code'] = $row['vonalkod'];
      $each['ar1'] = $row['netto'];

      unset($row);

      $prepare[] = $each;
    }
    unset($each);
    unset($context);

    print_r($prepare);

    return $prepare;
  }

  public function getProducts()
  {
    $param = array();
    $param[0] = array(
      'ar' => 1,
      'mennyiseg' => 0
    );
    $this->incFixRowData($param[0]);

    $this->api->keszlet_lista( $param );

    return array(
      'uzenet' => $this->api->uzenet,
      'hiba' => $this->api->hiba,
      'error' => ($this->api->hiba == '') ? 0 : 1,
      'data' => $this->api->keszlet_listaTomb
    );
  }

  public function getProduct( $cikkszam )
  {
    $param = array();
    $param[0] = array(
      'cikkszam' => $cikkszam
    );
    $this->incFixRowData($param[0]);
    $this->api->termekadatok($param);

    return array(
      'id' =>  $this->api->termekTomb[1],
      'megnevezes' => $this->api->termekTomb[1],
      "vonalkod" => $this->api->termekTomb[2],
			"cikkszam" => $this->api->termekTomb[3],
			"afa" => $this->api->termekTomb[4],
			"netto" => $this->api->termekTomb[5],
			"afa2" => $this->api->termekTomb[6],
			"netto2" => $this->api->termekTomb[7],
			"afa3: " => $this->api->termekTomb[8],
			"netto3" => $this->api->termekTomb[9],
			"afa4" => $this->api->termekTomb[10],
			"netto4" => $this->api->termekTomb[11],
			"afa5" => $this->api->termekTomb[12],
			"netto5" => $this->api->termekTomb[13],
			"afa6" => $this->api->termekTomb[14],
			"netto6" => $this->api->termekTomb[15],
			"afa7" => $this->api->termekTomb[16],
			"netto7" => $this->api->termekTomb[17],
			"afa8" => $this->api->termekTomb[18],
			"netto8" => $this->api->termekTomb[19],
			"netto_beszerzes" => $this->api->termekTomb[20],
			"termek" => $this->api->termekTomb[21],
			"keszlet_min" => $this->api->termekTomb[22],
			"beszallito" => $this->api->termekTomb[23],
			"gyarto" => $this->api->termekTomb[24],
			"meret_hosszusag" => $this->api->termekTomb[25],
			"meret_szelesseg " => $this->api->termekTomb[26],
			"meret_magassag" => $this->api->termekTomb[27],
			"termekcsoport" => $this->api->termekTomb[28],
			"mennyisegegyseg" => $this->api->termekTomb[29],
			"vtszszj" => $this->api->termekTomb[30],
			"kn_kod" => $this->api->termekTomb[31],
    );
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
