<?php
namespace ResourceImporter;

class ResourceImportBase
{
  const DB_SOURCE = 'xml_origins';
  const DB_COMPARER = 'xml_origins_column_xref';
  const DB_TEMP_PRODUCTS = 'xml_temp_products';

  protected $db = null;
  protected $content_type = false;
  protected $support_res_types = array( 'text/csv', 'application/xml' );
  protected $csv_settings = array(
    'separator' => ';',
    'hasheader' => true,
    'headerAsKey' => true
  );

	public function __construct( $arg = array() )
	{
    if (isset($arg['db'])) {
      $this->db = $arg['db'];
    } else {
      die('Hiányzik az adatbázis hivatkozás a '.__CLASS__.' osztály meghívásánál. Pótolja!');
    }

    if ( !$this->db instanceof \DatabaseManager\Database ) {
      die(__CLASS__.' @ '.__LINE__.': Az adatbázis linkelés nem a megfelelő osztályra hivatkozik.');
    }

		return $this;
	}

  public function __destruct()
  {
    $this->db = null;
    $this->memo();
  }

  public function delSource()
  {
    extract($_POST);

    if ((int)$delSource == 0) {
      throw new \Exception("Hiányzik a forrás ID-ja. Próbálja újra!");
    }

    $this->db->query("DELETE FROM ".self::DB_SOURCE." WHERE ".sprintf("ID = %d", (int)$delSource));
    $this->db->query("DELETE FROM ".self::DB_COMPARER." WHERE ".sprintf("origin_id = %d", (int)$delSource));
    $this->db->query("DELETE FROM ".self::DB_TEMP_PRODUCTS." WHERE ".sprintf("origin_id = %d", (int)$delSource));
  }

  public function creatingSource()
  {
    extract($_POST);

    if ($createSource != 0) {
      // Edit
      $this->db->update(
        self::DB_SOURCE,
        $xml_origins,
        sprintf("ID = %d", $createSource)
      );

      $cq = "SELECT ID FROM ".self::DB_COMPARER." WHERE origin_id = {$createSource}";
      $check = $this->db->query($cq)->fetchColumn();

      if (empty($check)) {
        $this->db->insert(
          self::DB_COMPARER,
          array(
            'origin_id' => $createSource
          )
        );
      }
      $this->db->update(
        self::DB_COMPARER,
        $xml_origins_column_xref,
        sprintf("origin_id = %d", $createSource)
      );
    } else {
      // Create
      $this->db->insert(
        self::DB_SOURCE,
        $xml_origins
      );
      $new_origin_id = (int)$this->db->lastInsertId();

      $ref_arr = array(
        'origin_id' => $new_origin_id
      );

      $xml_origins_column_xref = array_merge($xml_origins_column_xref, $ref_arr);

      $this->db->update(
        self::DB_COMPARER,
        $xml_origins_column_xref
      );
    }
  }

  public function getSources( $arg = array() )
  {
    $q = "SELECT * FROM ".self::DB_SOURCE." WHERE 1=1";
    if (isset($arg['ID'])) {
      $q .= " and ID = ".$arg['ID'];
    }

    $back = array();

    $db = $this->db->query($q);

    if ($db->rowCount() != 0) {
      $data = $db->fetchAll(\PDO::FETCH_ASSOC);

      foreach ((array)$data as $d) {
        $d['allapot_text'] = '<strong style="color:red;">Letöltetlen</strong>';
        $d['allapot'] = -1;

        if (is_null($d['last_download'])) {
          $d['last_download'] = 'N/A';
        } else {
          $d['allapot_text'] = '<strong style="color:green;">Letöltve</strong>';
          $d['allapot'] = 1;
        }

        if ($d['download_progress'] == 1) {
          $d['allapot_text'] = '<strong style="color:orange;">Letöltés folyamatban</strong>';
          $d['allapot'] = 0;
        }

        $d['temp_items'] = $this->getTempItemCounts((int)$d['ID']);
        $d['inserted_items'] = $this->getInsertedItemCounts((int)$d['ID']);
        $d['inserted_done_items'] = $this->getInsertedItemCounts((int)$d['ID'], true);
        $d['image_states'] = $this->getImageCounts((int)$d['ID'], true);
        $d['column_ref'] = $this->getColumnComparerKeys((int)$d['ID']);


        $back[] = $d;
      }

      unset($data);
    } else return $back;

    return $back;
  }

  public function getImageCounts( $origin = 0 )
  {
    $total = "SELECT
    (SELECT count(ID)  FROM `nagyker_downloadable_images` WHERE `nagyker` = {$origin}) as total,
    (SELECT count(ID)  FROM `nagyker_downloadable_images` WHERE `nagyker` = {$origin} and (downloaded = 1 or (downloaded = 0 and cannot_be_downloaded = 1))) as done";

    $stat = $this->db->query( $total )->fetch(\PDO::FETCH_ASSOC);

    if ($stat['total'] == 0) {
      return array('-', '-');
    }

    return array($stat['total'], $stat['done']);
  }

  public function getTempRecord( $record_id = 0 )
  {
    $back = array();

    $q = "SELECT * FROM ".self::DB_TEMP_PRODUCTS." WHERE 1=1 and ".sprintf("ID = %d", $record_id);

    $db = $this->db->query($q);

    if ($db->rowCount() != 0) {
      $back = $db->fetch(\PDO::FETCH_ASSOC);
    }

    return $back;
  }

  public function getTempItemCounts( $id = 0 )
  {
    return (int)$this->db->query("SELECT count(ID) FROM ".self::DB_TEMP_PRODUCTS." WHERE 1=1 and ".sprintf("origin_id = %d", $id))->fetchColumn();
  }

  public function getInsertedItemCounts( $id = 0, $only_done = false )
  {
    if ($only_done) {
      $done = ' and xml_import_done = 1 ';
    }
    $q = "SELECT count(ID) FROM shop_termekek WHERE 1=1 {$done} and ".sprintf("xml_import_origin = %d", $id);

    return (int)$this->db->query($q)->fetchColumn();
  }

  public function getSource( $id = 0 )
  {
    $data = $this->getSources(array(
      'ID' => $id
    ));

    if (!empty($data)) {
      return $data[0];
    } return false;
  }

  public function getColumnComparerKeys( $originid = 0 )
  {
    $back = array();
    $q = "SELECT * FROM ".self::DB_COMPARER." WHERE 1=1 and origin_id = ".$originid;
    $db = $this->db->query($q);

    if ($db->rowCount() != 0) {
      $data = $db->fetch(\PDO::FETCH_ASSOC);
      $back = $data;
      return $back;
    } else return false;

    return false;
  }

  public function loadResource( $originid = 0 )
  {
    $origin = $this->getSource( $originid );
    $url = $origin['url'];
    $finfo = get_headers( $url, 1 );
    $content_type = $finfo['Content-Type'];
    unset($finfo);
    $this->content_type = $content_type;
    $content = false;

    if ( !in_array($content_type, $this->support_res_types)) {
      die('Nem támogatott forrás formátum: '.$content_type);
    }

    switch ( $content_type )
    {
      case 'text/csv':
        $content = $this->loadCSV( $url );
      break;
      case 'application/xml':
        $content = $this->loadXML( $url );
      break;

      default:
        return false;
      break;
    }

    return $content;
  }

  public function loadCSV( $url )
  {
    if (!$url) {
      return false;
    }

    $content = array();
    $header = false;

    $row = 1;
    if (($handle = fopen($url, "r")) !== FALSE) {
        while (($data = fgetcsv($handle, 1000, ";")) !== FALSE)
        {
          $data = array_map("utf8_encode", $data); //added

          if($row == 1 && $this->csv_settings['hasheader']) {
            $header = $data;
          } else {
            if ($this->csv_settings['headerAsKey']) {
              $bdata = array();
              foreach ($data as $i => $d) {
                $bdata[$header[$i]] = $d;
              }
              unset($data);
              $content[] = $bdata;
            } else {
              $content[] = $data;
            }
          }

          $row++;
        }
        fclose($handle);
    }
    unset($data);
    unset($header);

    return $content;
  }

  public function loadXML( $url = false )
  {
    if (!$url) {
      return false;
    }

    $xml = simplexml_load_file($url, 'SimpleXMLElement', \LIBXML_NOCDATA );

    return $xml;
  }

  public function checkDataColumn( $originid = 0, $raw_xml = false )
  {
    if ($originid == 0 || !$raw_xml) {
      return false;
    }

    $rkey = rand(0,1);

    $sample = array();
    $comparer = $this->getColumnComparerKeys($originid);

    if ( !$comparer ) {
      return false;
    }

    switch ($originid) {
      // Eurostar
      case 1:
        $row = $raw_xml->{$comparer['rootkey']}[$rkey];

        $sample['compare'] = array(
          $comparer['prod_id'] =>  array(
            'text' => 'Termék nagyker ID',
            'key' => 'nagyker_termek_id'
          ),
          $comparer['termek_nev'] => array(
            'text' => 'Termék elnevezése',
            'key' => 'nev'
          ),
          $comparer['nagyker_ar_netto'] => array(
            'text' => 'Termék nagyker nettó ára',
            'key' => 'netto_ar'
          ),
          $comparer['termek_leiras'] => array(
            'text' => 'Termék leírás',
            'key' => 'leiras'
          ),
          $comparer['termek_leiras2'] => array(
            'text' => 'Termék kiegészítő leírás',
            'key' => 'leiras_addon'
          ),
          $comparer['termek_keszlet'] => array(
            'text' => 'Termék készlet',
            'key' => 'keszlet'
          ),
          $comparer['ean_code'] => array(
            'text' => 'EAN kód',
            'key' => 'ean'
          ),
          $comparer['marka_nev'] => array(
            'text' => 'Márka név',
            'key' => 'marka_nev'
          )
        );

        if (!is_null($comparer['kisker_ar_netto'])) {
          $sample['compare'][$comparer['kisker_ar_netto']] = array(
            'text' => 'Termék kisker nettó ár',
            'key' => 'kisker_netto_ar'
          );
        }

        if (!is_null($comparer['termek_kep_urls'])) {
          $sample['compare'][$comparer['termek_kep_urls']] = array(
            'text' => 'Termék képek',
            'key' => 'kepek'
          );
        }

        $sample['data']['nagyker_termek_id'] = $this->preparedGetXMlObject($row, $comparer['prod_id']);
        $sample['data']['nev'] = $this->preparedGetXMlObject($row, $comparer['termek_nev']);
        $sample['data']['netto_ar'] = $this->preparedGetXMlObject($row, $comparer['nagyker_ar_netto']);
        $sample['data']['kisker_netto_ar'] = $this->preparedGetXMlObject($row, $comparer['kisker_ar_netto']);
        $sample['data']['leiras'] = $this->preparedGetXMlObject($row, $comparer['termek_leiras']);
        $sample['data']['leiras_addon'] = $this->preparedGetXMlObject($row, $comparer['termek_leiras2']);
        $sample['data']['keszlet'] = $this->preparedGetXMlObject($row, $comparer['termek_keszlet']);
        $sample['data']['ean'] = $this->preparedGetXMlObject($row, $comparer['ean_code']);
        $sample['data']['marka_nev'] = $this->preparedGetXMlObject($row, $comparer['marka_nev']);
        $sample['data']['kepek'] = $this->preparedGetXMlObject($row, $comparer['termek_kep_urls']);

        return $sample;
      break;

      // Saenger
      case 2:
        $row = $raw_xml->{$comparer['rootkey']}[$rkey];

        $sample['compare'] = array(
          $comparer['prod_id'] =>  array(
            'text' => 'Termék nagyker ID',
            'key' => 'nagyker_termek_id'
          ),
          $comparer['termek_nev'] => array(
            'text' => 'Termék elnevezése',
            'key' => 'nev'
          ),
          $comparer['nagyker_ar_netto'] => array(
            'text' => 'Termék nagyker nettó ára',
            'key' => 'netto_ar'
          ),
          $comparer['termek_leiras'] => array(
            'text' => 'Termék leírás',
            'key' => 'leiras'
          ),
          $comparer['termek_leiras2'] => array(
            'text' => 'Termék kiegészítő leírás',
            'key' => 'leiras_addon'
          ),
          $comparer['termek_keszlet'] => array(
            'text' => 'Termék készlet',
            'key' => 'keszlet'
          ),
          $comparer['ean_code'] => array(
            'text' => 'EAN kód',
            'key' => 'ean'
          ),
          $comparer['marka_nev'] => array(
            'text' => 'Márka név',
            'key' => 'marka_nev'
          )
        );

        if (!is_null($comparer['kisker_ar_netto'])) {
          $sample['compare'][$comparer['kisker_ar_netto']] = array(
            'text' => 'Termék kisker nettó ár',
            'key' => 'kisker_netto_ar'
          );
        }

        if (!is_null($comparer['termek_kep_urls'])) {
          $sample['compare'][$comparer['termek_kep_urls']] = array(
            'text' => 'Termék képek',
            'key' => 'kepek'
          );
        }

        $sample['data']['nagyker_termek_id'] = $this->preparedGetXMlObject($row, $comparer['prod_id']);
        $sample['data']['nev'] = $this->preparedGetXMlObject($row, $comparer['termek_nev']);
        $sample['data']['netto_ar'] = $this->preparedGetXMlObject($row, $comparer['nagyker_ar_netto']);
        $sample['data']['kisker_netto_ar'] = $this->preparedGetXMlObject($row, $comparer['kisker_ar_netto']);
        $sample['data']['leiras'] = $this->preparedGetXMlObject($row, $comparer['termek_leiras']);
        $sample['data']['leiras_addon'] = $this->preparedGetXMlObject($row, $comparer['termek_leiras2']);
        $sample['data']['keszlet'] = $this->preparedGetXMlObject($row, $comparer['termek_keszlet']);
        $sample['data']['ean'] = $this->preparedGetXMlObject($row, $comparer['ean_code']);
        $sample['data']['marka_nev'] = $this->preparedGetXMlObject($row, $comparer['marka_nev']);
        $sample['data']['kepek'] = $this->preparedGetXMlObject($row, $comparer['termek_kep_urls']);

        return $sample;
      break;

      // Moss.sk
      case 3:
        $row = $raw_xml->{$comparer['rootkey']}[$rkey];

        $sample['compare'] = array(
          $comparer['prod_id'] =>  array(
            'text' => 'Termék nagyker ID',
            'key' => 'nagyker_termek_id'
          ),
          $comparer['termek_nev'] => array(
            'text' => 'Termék elnevezése',
            'key' => 'nev'
          ),
          $comparer['nagyker_ar_netto'] => array(
            'text' => 'Termék nagyker nettó ára',
            'key' => 'netto_ar'
          ),
          $comparer['termek_leiras'] => array(
            'text' => 'Termék leírás',
            'key' => 'leiras'
          ),
          $comparer['termek_leiras2'] => array(
            'text' => 'Termék kiegészítő leírás',
            'key' => 'leiras_addon'
          ),
          $comparer['termek_keszlet'] => array(
            'text' => 'Termék készlet',
            'key' => 'keszlet'
          ),
          $comparer['ean_code'] => array(
            'text' => 'EAN kód',
            'key' => 'ean'
          ),
          $comparer['marka_nev'] => array(
            'text' => 'Márka név',
            'key' => 'marka_nev'
          )
        );

        if (!is_null($comparer['kisker_ar_netto'])) {
          $sample['compare'][$comparer['kisker_ar_netto']] = array(
            'text' => 'Termék kisker nettó ár',
            'key' => 'kisker_netto_ar'
          );
        }

        if (!is_null($comparer['termek_kep_urls'])) {
          $sample['compare'][$comparer['termek_kep_urls']] = array(
            'text' => 'Termék képek',
            'key' => 'kepek'
          );
        }

        $sample['data']['nagyker_termek_id'] = $this->preparedGetXMlObject($row, $comparer['prod_id']);
        $sample['data']['nev'] = $this->preparedGetXMlObject($row, $comparer['termek_nev']);
        $sample['data']['netto_ar'] = $this->preparedGetXMlObject($row, $comparer['nagyker_ar_netto']);
        $sample['data']['kisker_netto_ar'] = $this->preparedGetXMlObject($row, $comparer['kisker_ar_netto']);
        $sample['data']['leiras'] = $this->preparedGetXMlObject($row, $comparer['termek_leiras']);
        $sample['data']['leiras_addon'] = $this->preparedGetXMlObject($row, $comparer['termek_leiras2']);
        $sample['data']['keszlet'] = $this->preparedGetXMlObject($row, $comparer['termek_keszlet']);
        $sample['data']['ean'] = $this->preparedGetXMlObject($row, $comparer['ean_code']);
        $sample['data']['marka_nev'] = $this->preparedGetXMlObject($row, $comparer['marka_nev']);
        $sample['data']['kepek'] = $this->preparedGetXMlObject($row, $comparer['termek_kep_urls']);

        return $sample;
      break;

      // Energo
      case 10:
        $row = $raw_xml->{$comparer['rootkey']}[0];

        $sample['compare'] = array(
          $comparer['prod_id'] =>  array(
            'text' => 'Termék nagyker ID',
            'key' => 'nagyker_termek_id'
          ),
          $comparer['termek_nev'] => array(
            'text' => 'Termék elnevezése',
            'key' => 'nev'
          ),
          $comparer['termek_leiras'] => array(
            'text' => 'Termék leírás',
            'key' => 'leiras'
          ),
          $comparer['termek_leiras2'] => array(
            'text' => 'Termék kiegészítő leírás',
            'key' => 'leiras_addon'
          ),
          $comparer['termek_keszlet'] => array(
            'text' => 'Termék készlet',
            'key' => 'keszlet'
          ),
          $comparer['ean_code'] => array(
            'text' => 'EAN kód',
            'key' => 'ean'
          ),
          $comparer['marka_nev'] => array(
            'text' => 'Márka név',
            'key' => 'marka_nev'
          )
        );

        // Nagyker nettó ár
        $sample['compare'][$comparer['nagyker_ar_netto']] = array(
          'text' => 'Termék nagyker nettó ára',
          'key' => 'netto_ar'
        );

        // Kisker nettó ár
        if (!is_null($comparer['kisker_ar_netto'])) {
          $sample['compare'][$comparer['kisker_ar_netto']] = array(
            'text' => 'Termék kisker nettó ár',
            'key' => 'kisker_netto_ar'
          );
        }

        // Kisker nettó akciós ár
        if (!is_null($comparer['kisker_ar_netto_akcios'])) {
          $sample['compare'][$comparer['kisker_ar_netto_akcios']] = array(
            'text' => 'Termék kisker AKCIÓS nettó ár',
            'key' => 'kisker_ar_netto_akcios'
          );
        }

        // Nagyker nettó akciós ár
        if (!is_null($comparer['nagyker_ar_netto_akcios'])) {
          $sample['compare'][$comparer['nagyker_ar_netto_akcios']] = array(
            'text' => 'Termék nagyker AKCIÓS nettó ár',
            'key' => 'nagyker_ar_netto_akcios'
          );
        }

        if (!is_null($comparer['termek_kep_urls'])) {
          $sample['compare'][$comparer['termek_kep_urls']] = array(
            'text' => 'Termék képek',
            'key' => 'kepek'
          );
        }

        $sample['data']['nagyker_termek_id'] = $this->preparedGetXMlObject($row, $comparer['prod_id']);
        $sample['data']['nev'] = $this->preparedGetXMlObject($row, $comparer['termek_nev']);
        $sample['data']['netto_ar'] = $this->preparedGetXMlObject($row, $comparer['nagyker_ar_netto']);
        $sample['data']['kisker_netto_ar'] = round($this->preparedGetXMlObject($row, $comparer['kisker_ar_netto']) / 1.27);
        $sample['data']['kisker_ar_netto_akcios'] = round($this->preparedGetXMlObject($row, $comparer['kisker_ar_netto_akcios']) / 1.27);
        $sample['data']['nagyker_ar_netto_akcios'] = $this->preparedGetXMlObject($row, $comparer['nagyker_ar_netto_akcios']);
        $sample['data']['leiras'] = $this->preparedGetXMlObject($row, $comparer['termek_leiras']);
        $sample['data']['leiras_addon'] = $this->energoParamPreparer($row, 'Parameterek.Parameter');
        $sample['data']['keszlet'] = $this->preparedGetXMlObject($row, $comparer['termek_keszlet']);
        $sample['data']['ean'] = $this->preparedGetXMlObject($row, $comparer['ean_code']);
        $sample['data']['marka_nev'] = $this->preparedGetXMlObject($row, $comparer['marka_nev']);
        $sample['data']['kepek'] = $this->preparedGetXMlObject($row, $comparer['termek_kep_urls']);

        return $sample;
      break;

      default:
      break;
    }
  }

  public function pushToTermekek( $originid )
  {
    $q = "SELECT
      tp.*
    FROM `xml_temp_products` as tp
    WHERE 1=1 and
    tp.origin_id = {$originid}";

    if (true) {
      $q .= " and
      (SELECT count(ID) FROM shop_termekek WHERE xml_import_origin = {$originid} and xml_import_res_id = tp.ID ) = 0 and
      (SELECT count(ID) FROM shop_termekek WHERE nagyker_kod = tp.prod_id and xml_import_origin != {$originid}) = 0 and
      (SELECT count(ID) FROM shop_termekek WHERE nagyker_kod = tp.prod_id) = 0";
    }

    $data = $this->db->query($q);

    if ($data->rowCount() != 0)
    {
      $data = $data->fetchAll(\PDO::FETCH_ASSOC);

      $insert_header = array('cikkszam', 'nagyker_kod', 'nev', 'leiras', 'keszletID', 'szallitasID', 'beszerzes_netto', 'netto_ar', 'brutto_ar', 'xml_import_origin', 'xml_import_res_id', 'xml_import_done', 'lathato', 'garancia_honap', 'raktar_keszlet');
      $this->prePushInsertHeaderModifier($originid, $insert_header);
      $insert_row = array();

      foreach ( (array)$data as $d )
      {
        list($keszlet_id, $szallitas_id) = $this->pushedProductKeszletSzallitasID($originid, (int)$d['termek_keszlet']);

        $irow = array(
          $d['prod_id'], $d['prod_id'], addslashes($d['termek_nev']), addslashes($d['termek_leiras']), $keszlet_id, $szallitas_id, $d['beszerzes_netto'], 0, 0, $originid, $d['ID'], 0, 0, 0, (int)$d['termek_keszlet']
        );

        $insert_row[] = $irow;
      }
      unset($data);
      unset($irow);
      //print_r($insert_header);
      //print_r($insert_row);

      if (!empty($insert_row)) {
        /* */
        $debug = $this->db->multi_insert(
          'shop_termekek',
          $insert_header,
          $insert_row,
          array(
            'debug' => false
          )
        );
        unset($insert_header);
        unset($insert_row);
        //echo $debug;
        /* */
      }

      //return count($insert_row);

    }
  }

  public function _old__pushToTermekek( $originid )
  {
    $q = "SELECT
      tp.*
    FROM `xml_temp_products` as tp
    WHERE 1=1 and
    tp.origin_id = {$originid}";

    if (true) {
      $q .= " and
      (SELECT count(ID) FROM shop_termekek WHERE xml_import_origin = {$originid} and xml_import_res_id = tp.ID ) = 0 and
      (SELECT count(ID) FROM shop_termekek WHERE nagyker_kod = tp.prod_id and xml_import_origin != {$originid}) = 0 and
      (SELECT count(ID) FROM shop_termekek WHERE nagyker_kod = tp.prod_id) = 0";
    }

    $data = $this->db->query($q);

    if ($data->rowCount() != 0) {
      $data = $data->fetchAll(\PDO::FETCH_ASSOC);

      $insert_header = array('nagyker_kod', 'nev', 'leiras', 'keszletID', 'szallitasID', 'netto_ar', 'brutto_ar', 'xml_import_origin', 'xml_import_res_id', 'xml_import_done', 'lathato', 'garancia_honap');
      $this->prePushInsertHeaderModifier($originid, $insert_header);
      $insert_row = array();

      foreach ( (array)$data as $d ) {
        if ($originid == 1) {
          $d['termek_nev'] = str_replace(array(
            'Ó', 'Ü', 'Ö', 'Ú', 'Ő', 'Ű', 'Á', 'É', 'Í'
          ),array(
            'ó', 'ü', 'ö', 'ú', 'ő', 'ű', 'á', 'é', 'í'
          ), ucfirst(strtolower($d['termek_nev'])));
        }

        list($keszlet_id, $szallitas_id) = $this->pushedProductKeszletSzallitasID($originid, (int)$d['termek_keszlet']);

        $irow = array(
          $d['prod_id'], addslashes($d['termek_nev']), addslashes($d['termek_leiras']), $keszlet_id, $szallitas_id, $d['nagyker_ar_netto'], ($d['nagyker_ar_netto'] * 1.27), $originid, $d['ID'], 0, 0, 12
        );

        $this->preInsertRowModifier($originid, $irow, $d);

        $insert_row[] = $irow;
      }

      //print_r($insert_header);
      //print_r($insert_row);

      if (!empty($insert_row)) {
        /* */
        $debug = $this->db->multi_insert(
          'shop_termekek',
          $insert_header,
          $insert_row,
          array(
            'debug' => false
          )
        );
        //echo $debug;
        /* */
      }

      //return count($insert_row);

    }

    // Updater
    $this->autoUpdater( $originid );

    return true;
  }

  public function autoUpdater( $originid = 0, $debug = false )
  {
    $update = array();

    $q = "SELECT
      tp.*
    FROM `xml_temp_products` as tp
    WHERE 1=1 and
    tp.origin_id = {$originid}";

    if (true) {
      $q .= " and
      (SELECT count(ID) FROM shop_termekek WHERE xml_import_origin = {$originid} and xml_import_res_id = tp.ID ) != 0";
    }

    $data = $this->db->query($q);

    if ($data->rowCount() != 0)
    {
      $data = $data->fetchAll(\PDO::FETCH_ASSOC);
      $upsetbench = array();

      $i = 0;
      foreach ((array)$data as $d)
      {
        //echo $d['prod_id'] . '<br>';
        //if($d['prod_id'] != '10000250') continue;
        //$i++; if ($i >= 100) { break; }

        $current_data = $this->db->query("SELECT
          ID,
          keszletID,
          szallitasID,
          netto_ar,
          brutto_ar,
          egyedi_ar,
          raktar_keszlet,
          akcios,
          akcios_netto_ar,
          akcios_brutto_ar,
          akcios_egyedi_brutto_ar
        FROM shop_termekek
        WHERE 1=1 and
        xml_import_origin = {$originid} and
        xml_import_res_id = {$d[ID]}")->fetch(\PDO::FETCH_ASSOC);
        //if($current_data['ID'] != 2421) continue;

        $d['will_update'] = $this->whatWantUpdate($originid, $d, $current_data);
        $d['current_data'] = $current_data;

        $update[] = $d;
      }


      foreach ((array)$update as $up) {
        if(empty($up['will_update'])) continue;

        $upset = '';

        foreach ((array)$up['will_update']['what'] as $upkey) {
          if (is_null($up['will_update']['field'][$upkey]['new'])) {
            $upset .= $upkey." = NULL, ";
          } else {
            $upset .= $upkey." = '".$up['will_update']['field'][$upkey]['new']."', ";
          }
        }

        if ($upset != '') {
          $upset = rtrim($upset, ', ');
          $upsetbench[$upset][] = $up['current_data']['ID'];
        }

      }

      $xcutr = 0;
      foreach ((array)$upsetbench as $whr => $ids) {
        $upq = "UPDATE shop_termekek SET $whr WHERE xml_import_origin = {$originid} and ID IN (".implode(",", $ids).")";

        $this->db->query($upq);
        //echo $upq."<br>";
      }

      unset($upsetbench);

      //print_r($upsetbench);
    }

    $this->autoIOProducts( $originid );

    if ($debug) {
      return $update;
    } else {
      unset($update);
      unset($data);
      return true;
    }
  }

  public function autoIOProducts( $orignid = 0 )
  {
    if ($orignid == 0) {
      return false;
    }

    $q = "SELECT
    ID, nagyker_kod, lathato
    FROM `shop_termekek`
    WHERE
      `xml_import_origin` = $orignid and
      xml_import_done = 1 and
      lathato = 1 and
      (SELECT t.io FROM xml_temp_products as t WHERE t.prod_id != '' and t.prod_id = nagyker_kod and t.origin_id = xml_import_origin) != lathato";

    $data = $this->db->query( $q );

    if ($data->rowCount() != 0) {
      $data = $data->fetchAll(\PDO::FETCH_ASSOC);
      foreach ($data as $d) {
        $io = ($d['lathato'] == 1) ? 0 : 1;
        $u = "UPDATE shop_termekek SET lathato = $io WHERE xml_import_origin = $orignid and ID = ".$d['ID'];
        //echo $u."<br>";
        $this->db->query( $u );
      }
    }
  }

  public function whatWantUpdate( $originid = 0, $tempdata = array(), $current_data = array() )
  {
    $wupdate = array();

    $netto_ar = $tempdata['nagyker_ar_netto'];
    $egyedi_ar = false;

    switch ($originid) {
      // Moss.sk
      case 3:
        $egyedi_ar = $tempdata['nagyker_ar_netto'];
        $netto_ar = round($egyedi_ar * 0.625 * 0.7874);
      break;
      // Energo
      case 10:
        if ( isset($tempdata['nagyker_ar_netto_akcios']) && (float)$tempdata['nagyker_ar_netto_akcios'] == 0 )
        {
          $egyedi_ar = round($tempdata['kisker_ar_netto'] * 1.27);
        }
      break;
    }

    $brutto_ar = round($netto_ar * 1.27);

    list($keszletID, $szallitasID) = $this->pushedProductKeszletSzallitasID($originid, $tempdata['termek_keszlet']);

    if ($current_data['netto_ar'] != $netto_ar && $current_data['akcios'] == 0) {
      $wupdate['what'][] = 'netto_ar';
      $wupdate['field']['netto_ar'] = array(
        'new' => $netto_ar,
        'old' => $current_data['netto_ar']
      );
    }
    if ($current_data['netto_ar'] != $netto_ar && $current_data['akcios_netto_ar'] != $netto_ar && $current_data['akcios'] == 1) {
      $wupdate['what'][] = 'akcios_netto_ar';
      $wupdate['field']['akcios_netto_ar'] = array(
        'new' => $netto_ar,
        'old' => $current_data['akcios_netto_ar']
      );
    }

    if ($current_data['brutto_ar'] != $brutto_ar && $current_data['akcios'] == 0) {
      $wupdate['what'][] = 'brutto_ar';
      $wupdate['field']['brutto_ar'] = array(
        'new' => $brutto_ar,
        'old' => $current_data['brutto_ar']
      );
    }

    if ($current_data['brutto_ar'] != $brutto_ar && $current_data['akcios_brutto_ar'] != $brutto_ar && $current_data['akcios'] == 1) {
      $wupdate['what'][] = 'akcios_brutto_ar';
      $wupdate['field']['akcios_brutto_ar'] = array(
        'new' => $brutto_ar,
        'old' => $current_data['akcios_brutto_ar']
      );
    }

    if ($egyedi_ar !== false && !empty($egyedi_ar) && $current_data['egyedi_ar'] != $egyedi_ar) {
      $wupdate['what'][] = 'egyedi_ar';
      $wupdate['field']['egyedi_ar'] = array(
        'new' => $egyedi_ar,
        'old' => $current_data['egyedi_ar']
      );
    }

    if ($tempdata['termek_keszlet'] != $current_data['raktar_keszlet']) {
      $wupdate['what'][] = 'raktar_keszlet';
      $wupdate['field']['raktar_keszlet'] = array(
        'new' => $tempdata['termek_keszlet'],
        'old' => $current_data['raktar_keszlet']
      );
    }

    if ($keszletID != $current_data['keszletID']) {
      $wupdate['what'][] = 'keszletID';
      $wupdate['field']['keszletID'] = array(
        'new' => $keszletID,
        'old' => $current_data['keszletID']
      );
    }

    if ($szallitasID != $current_data['szallitasID']) {
      $wupdate['what'][] = 'szallitasID';
      $wupdate['field']['szallitasID'] = array(
        'new' => $szallitasID,
        'old' => $current_data['szallitasID']
      );
    }

    // Akciózás
    if ( isset($tempdata['nagyker_ar_netto_akcios']) && (float)$tempdata['nagyker_ar_netto_akcios'] != 0 )
    {
      $nagyker_netto_akcios = (float)$tempdata['nagyker_ar_netto_akcios'];
      $nagyker_brutto_akcios = $nagyker_netto_akcios * 1.27;
      $kisker_netto = (float)$tempdata['kisker_ar_netto'];
      $kisker_netto_akcios = (float)$tempdata['kisker_ar_netto_akcios'];
      $kisker_brutto_akcios = round($kisker_netto_akcios * 1.27);
      $kisker_brutto = round($kisker_netto * 1.27);

      // 0-5 kerekítés
      $nagyker_netto_akcios = round( $nagyker_netto_akcios / 5 ) * 5;
      $nagyker_brutto_akcios = round( $nagyker_brutto_akcios / 5 ) * 5;
      $kisker_netto = round( $kisker_netto / 5 ) * 5;
      $kisker_netto_akcios = round( $kisker_netto_akcios /  5) * 5;
      $kisker_brutto_akcios = round( $kisker_brutto_akcios /  5) * 5;
      $kisker_brutto = round( $kisker_brutto /  5) * 5;

      // Akciós állapot
      if( $current_data['akcios'] != 1 ) {
        $wupdate['what'][] = 'akcios';
        $wupdate['field']['akcios'] = array(
          'new' => 1,
          'old' => $current_data['akcios']
        );
      }

      // Egyedi ár
      if ( $current_data['egyedi_ar'] != $kisker_brutto_akcios) {
        $wupdate['what'][] = 'egyedi_ar';
        $wupdate['field']['egyedi_ar'] = array(
          'new' => $kisker_brutto_akcios,
          'old' => $current_data['egyedi_ar']
        );
      }

      // Akciós nettó
      if ( $current_data['akcios_netto_ar'] != $nagyker_netto_akcios ) {
        $wupdate['what'][] = 'akcios_netto_ar';
        $wupdate['field']['akcios_netto_ar'] = array(
          'new' => $nagyker_netto_akcios,
          'old' => $current_data['akcios_netto_ar']
        );
      }

      // Akciós bruttó
      if ( $current_data['akcios_brutto_ar'] != $nagyker_brutto_akcios ) {
        $wupdate['what'][] = 'akcios_brutto_ar';
        $wupdate['field']['akcios_brutto_ar'] = array(
          'new' => $nagyker_brutto_akcios,
          'old' => $current_data['akcios_brutto_ar']
        );
      }

      // Egyedi ár akciós bruttó
      if ( $current_data['akcios_egyedi_brutto_ar'] != $kisker_brutto ) {
        $wupdate['what'][] = 'akcios_egyedi_brutto_ar';
        $wupdate['field']['akcios_egyedi_brutto_ar'] = array(
          'new' => $kisker_brutto,
          'old' => $current_data['akcios_egyedi_brutto_ar']
        );
      }
    }
    // Akció levétele
    else if( isset($tempdata['nagyker_ar_netto_akcios']) && (float)$tempdata['nagyker_ar_netto_akcios'] == 0 && $current_data['akcios'] == 1 )
    {
      // Akciós állapot
      if( $current_data['akcios'] == 1 ) {
        $wupdate['what'][] = 'akcios';
        $wupdate['field']['akcios'] = array(
          'new' => 0,
          'old' => $current_data['akcios']
        );
      }

      // Egyedi ár
      if ( $current_data['egyedi_ar'] != 0) {
        $wupdate['what'][] = 'egyedi_ar';
        $wupdate['field']['egyedi_ar'] = array(
          'new' => $egyedi_ar,
          'old' => $current_data['egyedi_ar']
        );
      }

      // Akciós nettó
      if ( !is_null($current_data['akcios_netto_ar']) ) {
        $wupdate['what'][] = 'akcios_netto_ar';
        $wupdate['field']['akcios_netto_ar'] = array(
          'new' => null,
          'old' => $current_data['akcios_netto_ar']
        );
      }

      // Akciós bruttó
      if ( !is_null($current_data['akcios_brutto_ar']) ) {
        $wupdate['what'][] = 'akcios_brutto_ar';
        $wupdate['field']['akcios_brutto_ar'] = array(
          'new' => null,
          'old' => $current_data['akcios_brutto_ar']
        );
      }

      // Egyedi ár akciós bruttó
      if ( !is_null($current_data['akcios_egyedi_brutto_ar']) ) {
        $wupdate['what'][] = 'akcios_egyedi_brutto_ar';
        $wupdate['field']['akcios_egyedi_brutto_ar'] = array(
          'new' => null,
          'old' => $current_data['akcios_egyedi_brutto_ar']
        );
      }
    }

    //print_r($tempdata);

    return $wupdate;
  }

  public function prePushInsertHeaderModifier($originid = 0, &$header )
  {
    return $header;
  }

  public function preInsertRowModifier($originid = 0, &$row, $data )
  {
    switch ($originid) {
      /**
      * MOSS.SK
      * nagyker_ar_netto = ajánlot fogy. ár közölve
      **/
      case 3:
        // nagyker Nettó ár kiszámolása a közölt fogy. árból
        $row[5] = round($data['nagyker_ar_netto'] * 0.625 * 0.7874);
        // nagyker Bruttó ár kiszámolása a közölt fogy. árból
        $row[6] = round($data['nagyker_ar_netto'] * 0.625);
        // fogy. ár az egyedi árba, mint értékesítési ár
        $row[] = $data['nagyker_ar_netto'];
      break;

      // Eurostar
      case 10:
        // fogy. ár az egyedi árba, mint értékesítési ár
        $row[] = round($data['kisker_ar_netto'] * 1.27);
      break;

    }

    return $row;
  }

  public function pushedProductKeszletSzallitasID( $originid = 0, $keszlet = 0 )
  {
    switch ($originid) {
      default:
        if ((int)$keszlet <=0 ) {
          return array(4, 10);
        } else {
          return array(2, 9);
        }
      break;
    }

    return false;
  }

  public function importToTemp( $originid, $context = false, $comparer = false )
  {
    if ( !$comparer ) {
      return false;
    }

    $this->memo();

    // Reset IO
    $this->db->update(
      self::DB_TEMP_PRODUCTS,
      array(
        'io' => 0
      ),
      sprintf("origin_id = %d", $originid)
    );

    $prepare = array();

    foreach ( (array)$context as $row )
    {
      $each = array();

      if ( $this->content_type == 'text/csv' )
      {
        $each['prod_id'] = $row[$comparer['prod_id']];
        $each['termek_nev'] = $row[$comparer['termek_nev']];
        $each['termek_keszlet'] = $row[$comparer['termek_keszlet']];
        $each['beszerzes_netto'] = $row[$comparer['beszerzes_netto']];
        $each['ean_code'] = $row[$comparer['ean_code']];
        $each['ar1'] = $row[$comparer['ar1']];
        $each['ar2'] = $row[$comparer['ar2']];
        $each['ar3'] = $row[$comparer['ar3']];
        $each['ar4'] = $row[$comparer['ar4']];
        $each['ar5'] = $row[$comparer['ar5']];
        $each['ar6'] = $row[$comparer['ar6']];
        $each['ar7'] = $row[$comparer['ar7']];
        $each['ar8'] = $row[$comparer['ar8']];
        $each['ar9'] = $row[$comparer['ar9']];
        $each['ar10'] = $row[$comparer['ar10']];
      }
      else if($this->content_type == 'application/xml')
      {
        $each['prod_id'] = $this->preparedGetXMlObject($row, $comparer['prod_id']);
        $each['termek_nev'] = $this->preparedGetXMlObject($row, $comparer['termek_nev']);
        $each['termek_keszlet'] = $this->preparedGetXMlObject($row, $comparer['termek_keszlet']);
        $each['beszerzes_netto'] = $this->preparedGetXMlObject($row, $comparer['beszerzes_netto']);
        $each['ean_code'] = $this->preparedGetXMlObject($row, $comparer['ean_code']);
        $each['ar1'] = $this->preparedGetXMlObject($row, $comparer['ar1']);
        $each['ar2'] = $this->preparedGetXMlObject($row, $comparer['ar2']);
        $each['ar3'] = $this->preparedGetXMlObject($row, $comparer['ar3']);
        $each['ar4'] = $this->preparedGetXMlObject($row, $comparer['ar4']);
        $each['ar5'] = $this->preparedGetXMlObject($row, $comparer['ar5']);
        $each['ar6'] = $this->preparedGetXMlObject($row, $comparer['ar6']);
        $each['ar7'] = $this->preparedGetXMlObject($row, $comparer['ar7']);
        $each['ar8'] = $this->preparedGetXMlObject($row, $comparer['ar8']);
        $each['ar9'] = $this->preparedGetXMlObject($row, $comparer['ar9']);
        $each['ar10'] = $this->preparedGetXMlObject($row, $comparer['ar10']);
      }
      unset($row);

      $prepare[] = $each;
    }
    unset($each);
    unset($context);

    $insert_row = array();
    $img_row = array();
    $insert_header = array('hashkey', 'origin_id', 'prod_id', 'last_updated', 'termek_nev', 'termek_leiras', 'termek_leiras2', 'beszerzes_netto', 'nagyker_ar_netto', 'kisker_ar_netto', 'termek_keszlet', 'termek_kep_urls', 'ean_code', 'marka_nev', 'kisker_ar_netto_akcios', 'nagyker_ar_netto_akcios', 'ar1','ar2','ar3','ar4','ar5','ar6','ar7','ar8','ar9','ar10', 'io');
    $img_header = array('hashkey', 'nagyker', 'nagyker_id', 'gyarto_id', 'kep');

    foreach ( (array)$prepare as $r ) {
      $hashkey = md5($originid.'_'.$r['prod_id']);
      $kepek = NULL;

      $insert_row[] = array(
        $hashkey, $originid, (string)$r['prod_id'].'', NOW, $r['termek_nev'], addslashes($r['termek_leiras']), addslashes($r['termek_leiras2']), $r['beszerzes_netto'], $r['nagyker_ar_netto'], $r['kisker_ar_netto'], $r['termek_keszlet'], $kepek, (string)$r['ean_code'].'', addslashes($r['marka_nev']),$r['kisker_ar_netto_akcios'], $r['nagyker_ar_netto_akcios'], $r['ar1'],$r['ar2'],$r['ar3'],$r['ar4'],$r['ar5'],$r['ar6'],$r['ar7'],$r['ar8'],$r['ar9'],$r['ar10'], 1
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
    unset($prepare);
    unset($r);

    // 90 mb usage
    //echo '--->'; $this->memo();

    /* * /
    echo '<pre>';
    print_r($img_row);
    echo '</pre>';
    return true;
    /* */


    //DB_TEMP_PRODUCTS

    /* */
    if (!empty($insert_row)) {
      $dbx = $this->db->multi_insert(
        self::DB_TEMP_PRODUCTS,
        $insert_header,
        $insert_row,
        array(
          'debug' => $debug,
          'duplicate_keys' => array('hashkey', 'prod_id', 'termek_nev', 'last_updated', 'termek_leiras', 'termek_leiras2', 'beszerzes_netto', 'nagyker_ar_netto', 'kisker_ar_netto', 'termek_keszlet', 'termek_kep_urls', 'ean_code', 'marka_nev', 'kisker_ar_netto_akcios', 'nagyker_ar_netto_akcios','ar1','ar2','ar3','ar4','ar5','ar6','ar7','ar8','ar9','ar10', 'io' )
        )
      );
    }
    //echo '--->'; $this->memo();
    unset($insert_row);

    if (!empty($img_row)) {
      $dbx = $this->db->multi_insert(
        'nagyker_downloadable_images',
        $img_header,
        $img_row,
        array(
          'debug' => $debug,
          'duplicate_keys' => array('hashkey', 'nagyker_id', 'gyarto_id', 'kep'),
          'row_format' => array( 'string', 'number', 'string', 'string', 'string' )
        )
      );
      //echo $dbx;
      if ($debug) {
        echo $dbx;
      }
    }
    unset($img_row);
    /* */

    $this->db->update(
      self::DB_SOURCE,
      array(
        'download_progress' => 0,
        'last_download' => NOW
      ),
      sprintf("ID = %d", $originid)
    );

    $this->memo();
  }

  public function _old_importToTemp( $originid )
  {
    $debug = false;


    $comparer = $this->getColumnComparerKeys($originid);

    /* * /
    echo '<pre>';
    print_r($comparer);
    echo '</pre>';
    return true;
    /* */

    if ( !$comparer ) {
      return false;
    }

    /* * /
    $this->db->update(
      self::DB_SOURCE,
      array(
        'download_progress' => 1
      ),
      sprintf("ID = %d", $originid)
    );
    /* */
    $this->db->update(
      self::DB_TEMP_PRODUCTS,
      array(
        'io' => 0
      ),
      sprintf("origin_id = %d", $originid)
    );

    $root = $xml->{$comparer['rootkey']};

    $prepared = array();

    $ii = 0;
    foreach ($root as $row) {
      $ii++;

      //if($ii > 10) break;

      $each = array();
      $each['nagyker_termek_id'] = $this->preparedGetXMlObject($row, $comparer['prod_id']);
      $each['nev'] = $this->preparedGetXMlObject($row, $comparer['termek_nev']);
      $each['leiras'] = $this->preparedGetXMlObject($row, $comparer['termek_leiras']);
      $each['leiras_addon'] = $this->preparedGetXMlObject($row, $comparer['termek_leiras2']);
      $each['keszlet'] = $this->preparedGetXMlObject($row, $comparer['termek_keszlet']);
      $each['ean'] = $this->preparedGetXMlObject($row, $comparer['ean_code']);
      $each['marka_nev'] = $this->preparedGetXMlObject($row, $comparer['marka_nev']);

      // Ár modulációk és kompenzációk
      switch ( $originid )
      {
        // Eurostar
        case 1:
          $kisker_netto_akcios = $this->preparedGetXMlObject($row, $comparer['kisker_ar_netto_akcios']);
          $akcios = ( $kisker_netto_akcios == '' || $kisker_netto_akcios == 0 ) ? false : true;

          if ( $akcios ) {
            $each['netto_ar'] = $this->preparedGetXMlObject($row, $comparer['nagyker_ar_netto_akcios']);
            $each['nagyker_ar_netto_akcios'] = $this->preparedGetXMlObject($row, $comparer['nagyker_ar_netto']);
            $each['kisker_netto_ar'] = $this->preparedGetXMlObject($row, $comparer['kisker_ar_netto_akcios']);
            $each['kisker_ar_netto_akcios'] =$this->preparedGetXMlObject($row, $comparer['kisker_ar_netto']);
          } else {
            $each['netto_ar'] = $this->preparedGetXMlObject($row, $comparer['nagyker_ar_netto']);
            $each['nagyker_ar_netto_akcios'] = 0;
            $each['kisker_netto_ar'] = $this->preparedGetXMlObject($row, $comparer['kisker_ar_netto']);
            $each['kisker_ar_netto_akcios'] = 0;
          }
        break;
        // Energo
        case 10:
          $each['netto_ar'] = $this->preparedGetXMlObject($row, $comparer['nagyker_ar_netto']);
          $each['kisker_netto_ar'] = round( $this->preparedGetXMlObject($row, $comparer['kisker_ar_netto']) / 1.27 );
          $each['kisker_ar_netto_akcios'] = round( $this->preparedGetXMlObject($row, $comparer['kisker_ar_netto_akcios']) / 1.27 );
          $each['nagyker_ar_netto_akcios'] = $this->preparedGetXMlObject($row, $comparer['nagyker_ar_netto_akcios']);
          $each['leiras_addon'] = $this->energoParamPreparer($row, 'Parameterek.Parameter');
        break;

        // Moss.sk
        case 3:
          $each['keszlet'] = ($each['keszlet'] == 'TRUE') ? 1 : 0;
          $each['netto_ar'] = $this->preparedGetXMlObject($row, $comparer['nagyker_ar_netto']);
        break;

        default:
          $each['netto_ar'] = $this->preparedGetXMlObject($row, $comparer['nagyker_ar_netto']);
          $each['kisker_netto_ar'] = $this->preparedGetXMlObject($row, $comparer['kisker_ar_netto']);
          $each['kisker_ar_netto_akcios'] = $this->preparedGetXMlObject($row, $comparer['kisker_ar_netto_akcios']);
          $each['nagyker_ar_netto_akcios'] = $this->preparedGetXMlObject($row, $comparer['nagyker_ar_netto_akcios']);
        break;
      }

      // Moss.sk
      if ( $originid == 3 ) {
        $each['kepek'] = $this->preparedImageSetPrefixer($row, $comparer['termek_kep_urls'], 'IMGURL_NO_WATER');
      } else {
        $each['kepek'] = $this->preparedGetXMlObject($row, $comparer['termek_kep_urls'], true);
      }

      // Eurostar névről levétel a kód
      if ( $originid == 1 ) {
        $each['nev'] = trim(str_replace($each['nagyker_termek_id'],'',$each['nev']));
        $each['nev'] = str_replace(array(
          'Ó', 'Ü', 'Ö', 'Ú', 'Ő', 'Ű', 'Á', 'É', 'Í'
        ),array(
          'ó', 'ü', 'ö', 'ú', 'ő', 'ű', 'á', 'é', 'í'
        ), ucfirst(strtolower($each['nev'])));
      }

      //if( $each['nagyker_termek_id'] != '500664210') continue;

      $prepared[] = $each;
    }
    unset($root);
    unset($xml);

    /* * /
    echo '<pre>';
    print_r($prepared);
    echo '</pre>';
    return true;
    /* */

    $insert_row = array();
    $img_row = array();
    $insert_header = array('hashkey', 'origin_id', 'prod_id', 'last_updated', 'termek_nev', 'termek_leiras', 'termek_leiras2', 'nagyker_ar_netto', 'kisker_ar_netto', 'termek_keszlet', 'termek_kep_urls', 'ean_code', 'marka_nev', 'kisker_ar_netto_akcios', 'nagyker_ar_netto_akcios','io');
    $img_header = array('hashkey', 'nagyker', 'nagyker_id', 'gyarto_id', 'kep');

    foreach ( $prepared as $r ) {
      $hashkey = md5($originid.'_'.$r['nagyker_termek_id']);
      $kepek = implode(',', (array)$r['kepek']);
      $insert_row[] = array(
        $hashkey, $originid, (string)$r['nagyker_termek_id'].'', NOW, $r['nev'], addslashes($r['leiras']), addslashes($r['leiras_addon']), $r['netto_ar'], $r['kisker_netto_ar'], $r['keszlet'], $kepek, (string)$r['ean'].'', addslashes($r['marka_nev']),$r['kisker_ar_netto_akcios'], $r['nagyker_ar_netto_akcios'], 1
      );

      if (!is_array($r['kepek'])) {
        $r['kepek'] = explode(",", $r['kepek']);
      }

      foreach ((array)$r['kepek'] as $k) {
        if($k == '') continue;
        $kephash = md5($originid.'_'.$r['nagyker_termek_id'].'_'.$k);
        $img_row[] = array($kephash, $originid, (string)$r['nagyker_termek_id'].'', (string)$r['nagyker_termek_id'].'', $k);
      }
    }
    /* * /
    echo '<pre>';
    print_r($img_row);
    echo '</pre>';
    return true;
    /* */
    unset($prepared);

    //DB_TEMP_PRODUCTS

    /* */
    if (!empty($insert_row)) {
      $dbx = $this->db->multi_insert(
        self::DB_TEMP_PRODUCTS,
        $insert_header,
        $insert_row,
        array(
          'debug' => $debug,
          'duplicate_keys' => array('hashkey', 'prod_id', 'termek_nev', 'last_updated', 'termek_leiras', 'termek_leiras2', 'nagyker_ar_netto', 'kisker_ar_netto', 'termek_keszlet', 'termek_kep_urls', 'ean_code', 'marka_nev', 'kisker_ar_netto_akcios', 'nagyker_ar_netto_akcios', 'io' )
        )
      );

      if ($debug) {
        //echo $dbx;
      }
    }

    if (!empty($img_row)) {
      $dbx = $this->db->multi_insert(
        'nagyker_downloadable_images',
        $img_header,
        $img_row,
        array(
          'debug' => $debug,
          'duplicate_keys' => array('hashkey', 'nagyker_id', 'gyarto_id', 'kep'),
          'row_format' => array( 'string', 'number', 'string', 'string', 'string' )
        )
      );
      //echo $dbx;
      if ($debug) {
        echo $dbx;
      }
    }
    /* */

    $this->db->update(
      self::DB_SOURCE,
      array(
        'download_progress' => 0,
        'last_download' => NOW
      ),
      sprintf("ID = %d", $originid)
    );

    return (int)count($insert_row);
  }

  // Energo nagyker paraméterek előkészítése addon leírásba
  public function energoParamPreparer( $row, $key )
  {
    $table = '';
    if (strpos($key,'.') !== false) {
      $xkey = explode('.', $key);
      $cur = $row;
      foreach ((array)$xkey as $ckey) {
        $cur = $cur->{$ckey};
        foreach ($cur as $c) {
          if(trim((string)$c) != '') {
            $table .= $c['name'].': '.trim((string)$c)."<br>";
          }
        }
      }
    }

    return $table;
  }

  public function preparedImageSetPrefixer($row, $key, $prefix)
  {
    $arr = (array)$row;
    $imgs = array();

    foreach ($arr as $key => $value) {
      if (strpos($key, $prefix) !== false) {
        $imgs[] = $value;
      } else continue;
    }

    return $imgs;
  }

  function preparedGetXMlObject($row, $key)
  {
    $value = false;

    if (strpos($key,'.') !== false) {
      $xkey = explode('.', $key);
      $cur = $row;
      foreach ((array)$xkey as $ckey) {
        $cur = $cur->{$ckey};
      }

      if (is_object($cur)) {
        $strtempcur = '';
        foreach ($cur as $c) {
          if($c != ''){
            $strtempcur .= trim($c).',';
          }
        }
        $strtempcur = rtrim($strtempcur, ',');
        $value = $strtempcur;
      } else {
        $value = (string)$cur;
      }
    } else {
      $value = (string)$row->{$key};
    }

    return $value;
  }

  function memo() {
      $mem_usage = memory_get_usage(true);

      if ($mem_usage < 1024)
          echo $mem_usage." bytes";
      elseif ($mem_usage < 1048576)
          echo round($mem_usage/1024,2)." kilobytes";
      else
          echo round($mem_usage/1048576,2)." megabytes";

      echo "<br/>";
  }
}
?>
