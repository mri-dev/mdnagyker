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

  public function autoImportProducts( $originid, $list = array() )
  {
    $prepare = array();
    $hk = array();

    // Reset IO
    $this->db->update(
      parent::DB_TEMP_PRODUCTS,
      array(
        'io' => 0
      ),
      sprintf("origin_id = %d", $originid)
    );

    $s = 0;
    foreach ( (array)$list as $row )
    {
      $s++;
      //if($s >= 100) break;
      $each = array();

      if ($row['cikkszam'] == '' || $row['vonalkod'] == '') {
        continue;
      }

      $fulldata = $this->getProduct($row['cikkszam']);
      $each['prod_id'] = $row['cikkszam'];
      $each['termek_nev'] = $row['megnevezes'];
      $each['termek_keszlet'] = $row['keszlet'];
      $each['ean_code'] = $row['vonalkod'];
      $each['beszerzes_netto'] = $fulldata['netto_beszerzes'];
      $each['ar1'] = $fulldata['netto'];
      $each['ar2'] = $fulldata['netto2'];
      $each['ar3'] = $fulldata['netto3'];
      $each['ar4'] = $fulldata['netto4'];
      $each['ar5'] = $fulldata['netto5'];
      $each['ar6'] = $fulldata['netto6'];
      $each['ar7'] = $fulldata['netto7'];
      $each['ar8'] = $fulldata['netto8'];
      $each['mennyisegegyseg'] = $fulldata['mennyisegegyseg'];

      $each['fulldata'] = $fulldata;

      unset($row);

      $prepare[] = $each;
    }
    unset($each);

    $insert_row = array();
    $insert_header = array('hashkey', 'origin_id', 'prod_id', 'last_updated', 'termek_nev', 'termek_leiras', 'termek_leiras2', 'beszerzes_netto', 'nagyker_ar_netto', 'kisker_ar_netto', 'termek_keszlet', 'termek_kep_urls', 'ean_code', 'marka_nev', 'kisker_ar_netto_akcios', 'nagyker_ar_netto_akcios', 'ar1','ar2','ar3','ar4','ar5','ar6','ar7','ar8','ar9','ar10', 'io', 'mennyisegegyseg');

    foreach ( (array)$prepare as $r ) {
      $hashkey = md5($originid.'_'.$r['prod_id'].'_'.$r['ean_code']);
      $kepek = NULL;

      if (!array_key_exists($hashkey, $hk)) {
        $insert_row[] = array(
          $hashkey,
          $originid,
          addslashes((string)$r['prod_id'].''),
          NOW,
          addslashes($r['termek_nev']),
          addslashes($r['termek_leiras']),
          addslashes($r['termek_leiras2']),
          (float)$r['beszerzes_netto'],
          (float)$r['beszerzes_netto'],
          (float)$r['ar1'],
          $r['termek_keszlet'],
          $kepek,
          addslashes((string)$r['ean_code'].''),
          addslashes($r['marka_nev']),
          (float)$r['kisker_ar_netto_akcios'],
          (float)$r['nagyker_ar_netto_akcios'],
          (float)$r['ar1'],
          (float)$r['ar2'],
          (float)$r['ar3'],
          (float)$r['ar4'],
          (float)$r['ar5'],
          (float)$r['ar6'],
          (float)$r['ar7'],
          (float)$r['ar8'],
          (float)$r['ar9'],
          (float)$r['ar10'],
          1,
          addslashes(trim($r['mennyisegegyseg']))
        );

        /*if (!is_array($r['kepek'])) {
          $r['kepek'] = explode(",", $r['kepek']);
        }*/

        /*
        foreach ((array)$r['kepek'] as $k) {
          if($k == '') continue;
          $kephash = md5($originid.'_'.$r['nagyker_termek_id'].'_'.$k);
          $img_row[] = array($kephash, $originid, (string)$r['nagyker_termek_id'].'', (string)$r['nagyker_termek_id'].'', $k);
        }
        */
      }


      // Reg. prev. hashkey exc. multiple insert
      $hk[$hashkey] += 1;
    }
    unset($prepare);
    unset($r);
    unset($hk);

    //print_r($insert_row);
    //exit;

    /* */
    if (!empty($insert_row)) {
      $dbx = $this->db->multi_insert(
        parent::DB_TEMP_PRODUCTS,
        $insert_header,
        $insert_row,
        array(
          'debug' => false,
          'steplimit' => 10,
          'duplicate_keys' => array('hashkey', 'prod_id', 'termek_nev', 'last_updated', 'termek_leiras', 'termek_leiras2', 'beszerzes_netto', 'nagyker_ar_netto', 'kisker_ar_netto', 'termek_keszlet', 'termek_kep_urls', 'ean_code', 'marka_nev', 'kisker_ar_netto_akcios', 'nagyker_ar_netto_akcios','ar1','ar2','ar3','ar4','ar5','ar6','ar7','ar8','ar9','ar10', 'io', 'mennyisegegyseg' )
        )
      );
    }
    //echo '--->'; $this->memo();
    unset($insert_row);

    $this->db->update(
      parent::DB_SOURCE,
      array(
        'download_progress' => 0,
        'last_download' => NOW
      ),
      sprintf("ID = %d", $originid)
    );


    return $dbx;
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
      'id' =>  $this->api->termekTomb[0],
      'megnevezes' => $this->api->termekTomb[1],
      "vonalkod" => $this->api->termekTomb[2],
			"cikkszam" => $this->api->termekTomb[3],
			"afa" => $this->api->termekTomb[4],
			"netto" => $this->api->termekTomb[5],
			"afa2" => $this->api->termekTomb[6],
			"netto2" => $this->api->termekTomb[7],
			"afa3" => $this->api->termekTomb[8],
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

    print_r($set);

    if ( !empty($set) )
    {
      $this->api->termekrogzites( $set );
      $ret = array(
        'uzenet' => $this->api->uzenet,
        'hiba' => $this->api->hiba,
        'error' => ($this->api->hiba == '') ? 0 : 1,
        'sended' => $set
      );
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
