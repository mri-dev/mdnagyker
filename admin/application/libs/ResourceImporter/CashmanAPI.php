<?php
namespace ResourceImporter;

use ResourceImporter\ResourceImportInterface;
use ResourceImporter\CashmanFxApi;
use DatabaseManager\Database;

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

  public function reconnectDB()
  {
    $this->db = new Database();
    return $this;
  }

  public function incFixRowData( &$row )
  {
    $row['Csoport'] = self::AUTH_CSOPORT;
  	$row['User'] = self::AUTH_USER;
  	$row['Password'] = self::AUTH_PW;
    return $row;
  }

  public function getKeszlet( $arg = array() )
  {
    $param = array();
    $param[0] = array();
    $param[0]['ar'] = (isset($arg['ar'])) ? (int)$arg['ar'] : 1;
    $this->incFixRowData($param[0]);

    $this->api->keszlet_lista( $param );

    if($this->api->hiba=='') {
			//rendben
			//visszakapott értékek:
			//megnevezes, cikkszam, vonalkod, keszlet, mennyisegiegyseg, netto, brutto
			//echo $this->api->keszlet_listaTomb[0]['megnevezes'];
			return $this->api->keszlet_listaTomb;
		} else {
			return $this->api->hiba;
		}
  }

  function partnerRegister( $user )
  {
    $param = array();
    $param[0] = array();
    $this->incFixRowData($param[0]);

    $param[0]['partner_id'] = $user['data']['crm_partner_id'];
    $param[0]['partner_kod'] = $user['data']['ID'];
    $param[0]['nev'] = ($user['data']['user_group'] == 'user') ? $user['data']['nev'] : $user['data']['company_name'];
  	$param[0]['iranyitoszam'] = $user['szamlazasi_adat']['irsz'];
  	$param[0]['varos'] = $user['szamlazasi_adat']['city'];
    $param[0]['kerulet'] = $user['szamlazasi_adat']['kerulet'];
    $param[0]['kozterulet_neve'] = $user['szamlazasi_adat']['kozterulet_nev'];
    $param[0]['kozterulet_jellege'] = $user['szamlazasi_adat']['kozterulet_jelleg'];
    $param[0]['hazszam'] = $user['szamlazasi_adat']['hazszam'];
    $param[0]['epulet'] = $user['szamlazasi_adat']['epulet'];
    $param[0]['lepcsohaz'] = $user['szamlazasi_adat']['lepcsohaz'];
    $param[0]['szint'] = $user['szamlazasi_adat']['szint'];
    $param[0]['ajto'] = $user['szamlazasi_adat']['ajto'];

    if ($user['data']['company_adoszam'] != '') {
      $param[0]['adoszam'] = $user['data']['company_adoszam'];
    }
    if ($user['data']['company_bankszamlaszam'] != '') {
      $paramt[0]['bankszamlaszam'] = $user['data']['company_bankszamlaszam'];
    }

  	//$param[0]['kozossegi_adoszam'] = '';
  	$param[0]['mobil'] = $user['szallitasi_adat']['phone'];
  	$param[0]['email'] = $user['email'];
  	$param[0]['fizetesmod'] = "Átutalás";					//Fizetesmod lehet: Átutalás, Készpénz, Utánvét, Bankkártya, Üdülési csekk, PayPal, Szép kártya, Utalvány,
  															//Halasztott KP, Barter, Kompenzáció, Barion
  	$param[0]['fizetesi_hatarido_nap'] = '7';				//Nem kötelező, ha nincs megadva, 0 lesz
  	//$param[0]['allando_kedvezmeny'] = '';
  	$param[0]['megjegyzes'] = $user['data']['price_group_title'];
  	$param[0]['partner_ar'] = (int)str_replace('ar', '',$user['data']['price_group_key']);
  	$param[0]['orszag_id'] = '1';

    $this->api->partnerrogzites($param);

    if( $this->api->hiba == '' ) {
		    if ( $this->api->partner_id[0]) {
          $this->db->update(
            'felhasznalok',
            array(
              'crm_partner_id' => trim($this->api->partner_id[0])
            ),
            sprintf("ID = %d", (int)$user['data']['ID'])
          );
        }
		} else {
			//echo $this->api->hiba;
		}

    return array(
      'partner_id' => $this->api->partner_id[0],
      'uzenet' => $this->api->uzenet,
      'hiba' => $this->api->hiba,
      'error' => ($this->api->hiba == '') ? 0 : 1,
    );
  }

  function newInvoice( $data )
  {
    $param = array();
    $param[0] = array();
    $this->incFixRowData($param[0]);

    $szamlazasi = json_decode($data["szamlazasi_keys"], true);
    $szallitasi = json_decode($data["szallitasi_keys"], true);

    /* Számla adatok hozzáadása */
    if (
      $data['fizetes'] == 'Bankkártyás fizetés (BORGUN)' ||
      $data['fizetes'] == 'Bankkártyás fizetés (OTP SIMPLE)'
    ) {
      $data['fizetes'] = 'Bankkártya';
    }
    // Kötelező elemek
    $param[0]['partner_kod'] = ($data['crm_partner_id'] != '') ? $data['crm_partner_id'] : '';
    $param[0]['fizetesmod'] = $data['fizetes'];

    $param[0]['nev'] = $data['nev'];
    $param[0]['iranyitoszam'] = $szamlazasi['irsz'];
  	$param[0]['varos'] = $szamlazasi['city'];
    $param[0]['kerulet'] = $szamlazasi['kerulet'];
    $param[0]['kozterulet_neve'] = $szamlazasi['kozterulet_nev'];
    $param[0]['kozterulet_jellege'] = $szamlazasi['kozterulet_jelleg'];
    $param[0]['hazszam'] = $szamlazasi['hazszam'];
    $param[0]['epulet'] = $szamlazasi['epulet'];
    $param[0]['lepcsohaz'] = $szamlazasi['lepcsohaz'];
    $param[0]['szint'] = $szamlazasi['szint'];
    $param[0]['ajto'] = $szamlazasi['ajto'];
    $param[0]['orszag'] = "Magyarország";

    if (isset($data['adoszam']) && !empty($data['adoszam'])) {
  	  $param[0]['adoszam'] = $data['adoszam'];
    }
    $param[0]['szallitasicim_nev'] = $szallitasi['nev'];
    $param[0]['szallitasicim_iranyitoszam'] = $szallitasi['irsz'];
    $param[0]['szallitasicim_varos'] = $szallitasi['city'];
    $param[0]['szallitasicim_utca'] = $szallitasi['kozterulet_nev'].' '.$szallitasi['kozterulet_jelleg'].' '.$szallitasi['hazszam'].'.';
    $param[0]['szallitasicim_orszag'] = 'Magyarország';

    // Egyéb
    $param[0]['egyeb'] = "WEBSHOP megrendelés: ".$data['azonosito'];

    $param[0]['kelt'] = date('Y-m-d');
  	$param[0]['teljesites'] = date('Y-m-d');
  	$param[0]['fizetesi_hatarido'] = date('Y-m-d', strtotime(date('Y-m-d H:i:s').' + 7 days'));
  	$param[0]['fizetesi_hatarido_nap'] = "7";

    $param[0]['megjegyzes'] = $data['comment'];
    $param[0]['noflash'] = "1";
    $param[0]['nodisplay'] = "1";
    $param[0]['email_cim'] = $data['email']; // Számla ide megy

    $param[0]['deviza'] = "HUF";
    $param[0]['tipus'] = ($this->db->settings['invoice_generate_mode'] != '') ? $this->db->settings['invoice_generate_mode']  : "2"; // 1 = számla, 2 = díjbekérő

    // termékek
    $index = 0;
    foreach ((array)$data['items'] as $item)
    {
      $param[$index]['termek_id'] = $item['xml_prod_id'];
      $param[$index]['cikkszam'] = $item['cikkszam'];
      $param[$index]['megnevezes'] = $item['nev'];
      $param[$index]['mennyiseg'] = $item['me'];
      $param[$index]['netto_egysegar'] = ($item['egysegAr']/1.27);
      $param[$index]['afa'] = 27;
      $param[$index]['mennyisegiegyseg'] = $item['mennyisegegyseg'];
      $param[$index]['tetel_megjegyzes'] = 'WebshopID: '.$item['termekID'] ."\n";
      $index++;
    }

    // Szolgáltatás hozzáadás - szállítások
    $has_transport_product = $this->getTransportData($data['szallitasiModID']);
    if ( $has_transport_product && $has_transport_product['crm_product_id'] != '' ) {
      $param[$index]['termek_id'] = $has_transport_product['crm_product_id'];
      $param[$index]['cikkszam'] = $has_transport_product['crm_product_id'];
      $param[$index]['megnevezes'] = 'Szolgáltatás: '.$has_transport_product['nev'];
      $param[$index]['mennyiseg'] = 1;
      $param[$index]['netto_egysegar'] = ($has_transport_product['koltseg'] != 0) ? ($has_transport_product['koltseg']/1.27) : 0;
      $param[$index]['afa'] = 27;
      $param[$index]['mennyisegiegyseg'] = 'db';
    }

    $this->api->uj_szamla($param);

    if( $this->api->hiba == '' ) {
			//$cashManAPI->szamla változóban a számlaszám
			//a $cashManAPI->link visszaadott linket futtatni kell, hogy a számla létrejöjjön,
			// $cashManAPI->szamla_hely változóban az újranyomtatáshoz szükséges linket adja vissza
			// a $cashManAPI->tmppdf változóban az ideiglenes PDF elérési útját adja vissza, ha valaki le akarja tölteni és a saját rendszerében tárolni.
			// a pdf fájlok időközönként törlésre kerülnek
			//$cashManAPI->szamlaid változóban a számla ID (pénztárba tevéshez szükséges)
			//echo '<br><br>Link: ' . $cashManAPI->link;
			//echo '<a href="' . $this->api->link . '" target="_blank">' . $this->api->szamla . "</a><br>";
			//echo $this->api->tmppdf;
		} else {
			//echo $this->api->hiba;
		}

    return array(
      'szamla_link' => $this->api->link,
      'szamlaszam' => $this->api->szamla,
      'uzenet' => $this->api->uzenet,
      'hiba' => $this->api->hiba,
      'error' => ($this->api->hiba == '') ? 0 : 1,
      'tmppdf' => $this->api->tmppdf,
      'params' => $param
    );
  }

  public function getTransportData( $id )
  {
    $q = "SELECT * FROM shop_szallitasi_mod WHERE ID = :id";

    $qry = $this->db->squery( $q, array(
      'id' => $id
    ));

    $dat = $qry->fetch(\PDO::FETCH_ASSOC);

    return $dat;
  }

  public function updateStock( $data = array() )
  {

  }

  public function getFullItemData( $originid, $id )
  {
    $data = array();

    $q = "SELECT
      x.*,
      x.prod_id as sync_id
    FROM xml_temp_products as x WHERE 1=1 ";

    $q .= " and x.origin_id = ".$originid;
    $q .= " and x.ID = :prod_id ";

    $qry = $this->db->squery( $q, array(
      'prod_id' => $id
    ));

    $dat = $qry->fetch(\PDO::FETCH_ASSOC);

    $data = $dat;

    return $data;
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
      $hashkey = md5($originid.'_'.(int)trim($fulldata['id']));

      $each['hashkey'] = $hashkey;
      $each['cikkszam'] = $row['cikkszam'];
      $each['gyarto_kod'] = $row['cikkszam'];
      $each['prod_id'] = (int)trim($fulldata['id']);
      $each['termek_nev'] = $row['megnevezes'];
      $each['termek_keszlet'] = $row['keszlet'];
      $each['keszlet_min'] = $fulldata['keszlet_min'];
      $each['ean_code'] = $row['vonalkod'];
      $each['beszerzes_netto'] = $fulldata['netto_beszerzes'];
      $each['arucsoport'] = $fulldata['termekcsoport'];
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

    /* * /
    echo '<pre>';
    print_r($prepare);
    echo '</pre>';
    return false;
    /* */

    $insert_row = array();
    $insert_header = array('hashkey', 'origin_id', 'cikkszam', 'gyarto_kod', 'prod_id', 'last_updated', 'termek_nev', 'termek_leiras', 'termek_leiras2', 'beszerzes_netto', 'arucsoport', 'nagyker_ar_netto', 'kisker_ar_netto', 'termek_keszlet', 'termek_kep_urls', 'ean_code', 'marka_nev', 'kisker_ar_netto_akcios', 'nagyker_ar_netto_akcios', 'ar1','ar2','ar3','ar4','ar5','ar6','ar7','ar8','ar9','ar10', 'io', 'mennyisegegyseg', 'virtualis_keszlet');

    foreach ( (array)$prepare as $r )
    {
      $hashkey = $r['hashkey'];
      $kepek = NULL;

      if (!array_key_exists($hashkey, $hk)) {
        /*$insert_row[] = array(
          $hashkey,
          $originid,
          addslashes($r['cikkszam']),
          addslashes($r['gyarto_kod']),
          $r['prod_id'],
          NOW,
          addslashes($r['termek_nev']),
          addslashes($r['termek_leiras']),
          addslashes($r['termek_leiras2']),
          (float)$r['beszerzes_netto'],
          $r['arucsoport'],
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
        );*/

        $insert_row[] = array(
          'hashkey' => $hashkey,
          'origin_id' => $originid,
          'cikkszam' => ($r['cikkszam']),
          'gyarto_kod' => ($r['gyarto_kod']),
          'prod_id' => $r['prod_id'],
          'last_updated' => NOW,
          'termek_nev' =>  ($r['termek_nev']),
          'termek_leiras' => ($r['termek_leiras']),
          'termek_leiras2' => ($r['termek_leiras2']),
          'beszerzes_netto' => (float)$r['beszerzes_netto'],
          'arucsoport' => $r['arucsoport'],
          'nagyker_ar_netto' => (float)$r['beszerzes_netto'],
          'kisker_ar_netto' => (float)$r['ar1'],
          'termek_keszlet' => $r['termek_keszlet'],
          'termek_kep_urls' => $kepek,
          'ean_code' => ((string)$r['ean_code'].''),
          'marka_nev' => ($r['marka_nev']),
          'kisker_ar_netto_akcios' => (float)$r['kisker_ar_netto_akcios'],
          'nagyker_ar_netto_akcios' => (float)$r['nagyker_ar_netto_akcios'],
          'ar1' => (float)$r['ar1'],
          'ar2' => (float)$r['ar2'],
          'ar3' => (float)$r['ar3'],
          'ar4' => (float)$r['ar4'],
          'ar5' => (float)$r['ar5'],
          'ar6' => (float)$r['ar6'],
          'ar7' => (float)$r['ar7'],
          'ar8' => (float)$r['ar8'],
          'ar9' => (float)$r['ar9'],
          'ar10' => (float)$r['ar10'],
          'io' => 1,
          'mennyisegegyseg' => (trim($r['mennyisegegyseg'])),
          'virtualis_keszlet' => (float)$r['keszlet_min']
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

    $this->reconnectDB();

    /* */
    if (!empty($insert_row)) {
      $dbx = $this->db->multi_insert_v2(
        parent::DB_TEMP_PRODUCTS,
        $insert_header,
        $insert_row,
        array(
          'debug' => true,
          'steplimit' => 50,
          'duplicate_keys' => array('hashkey', 'cikkszam', 'gyarto_kod', 'prod_id', 'termek_nev', 'last_updated', 'termek_leiras', 'termek_leiras2', 'beszerzes_netto', 'nagyker_ar_netto', 'kisker_ar_netto', 'termek_keszlet', 'termek_kep_urls', 'ean_code', 'marka_nev', 'kisker_ar_netto_akcios', 'nagyker_ar_netto_akcios','arucsoport', 'ar1','ar2','ar3','ar4','ar5','ar6','ar7','ar8','ar9','ar10', 'io', 'mennyisegegyseg', 'virtualis_keszlet' )
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

    $list = array();

    foreach ($this->api->keszlet_listaTomb as $tomb ) {
      $list[] = $tomb;
    }

    return array(
      'uzenet' => $this->api->uzenet,
      'hiba' => $this->api->hiba,
      'error' => ($this->api->hiba == '') ? 0 : 1,
      'data' => $list
    );
  }

  public function getProduct( $cikkszam )
  {
    $this->api->termekTomb = false;
    $param = array();
    $param[0] = array(
      'cikkszam' => $cikkszam
    );
    $this->incFixRowData($param[0]);
    $this->api->termekadatok($param);

    if ($this->api->hiba != '') {
      if ($this->api->hiba == 'HIBA:076 Nincs ilyen termék!') {
        return false;
      } else {
        return $this->api->hiba;
      }
    }

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
    $this->db= null;
    $this->api = null;
    parent::__destruct();
  }
}
