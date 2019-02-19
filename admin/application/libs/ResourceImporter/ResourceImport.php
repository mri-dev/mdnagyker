<?php
namespace ResourceImporter;

use ResourceImporter\ResourceImportInterface;

/**
 *
 */
class ResourceImport extends ResourceImportBase implements ResourceImportInterface
{
  public $crm = null;
  function __construct( $arg = array() )
  {
    if (isset($arg['crm'])) {
      $this->crm = $arg['crm'];
    }

    return parent::__construct( $arg );
  }

  public function getRaktarXML()
  {
    $raktar = $this->loadResource( 2 );
    return $raktar;
  }

  public function syncTempProducts()
  {
    $this->pushToTermekek( 1 );

    // xml_import_res_id fixálás, ha eltérő
    $this->db->query("UPDATE `shop_termekek` as s SET s.xml_import_res_id = (SELECT x.ID FROM xml_temp_products as x WHERE x.origin_id = s.xml_import_origin and s.cikkszam = x.cikkszam)  WHERE (SELECT x.ID FROM xml_temp_products as x WHERE x.origin_id = s.xml_import_origin and s.cikkszam = x.cikkszam) != s.xml_import_res_id");
  }

  public function groupCat( $content = false )
  {
    $group = array();

   if ( count($content) == 0 ) {
     return false;
   }

   foreach ( (array)$content as $data ) {
     if (!in_array($data['CSOPORT'], $group[$data['FAJTA']])) {
       $group[$data['FAJTA']][] = $data['CSOPORT'];
     }
   }
   unset($content);
   return $group;
  }

  public function prepareContext( $context = false )
  {
    $prepared = array();
    $prepared = $context;
    return $prepared;
  }

  public function findOldWebshopProducts( $products, $old_cats, $new_cats, $onlynews = false )
  {
    $data = array();
    $li = 0;
    foreach ($products as $p) {
      $li++;
      //if ($li > 5 ) { break; }
      $prod = $this->findProductBySKU($p['sku']);

      if ($onlynews && $prod) { continue; }
      //if ($p['sku'] != '381320-30-1') {continue;}

      $p['pushtocats'] = $this->connectSyncCategories($p['cats'], $old_cats, $new_cats);
      $p['dbdata'] = $prod;
      $p['prices'] = $this->preparePricesFromOldVirtuemart( $p['prices'] );
      $p['medias'] = $this->prepareMediasFromOldVirtuemart( $p['medias'] );
      $data[] = $p;
    }

    return $data;
  }

  private function prepareMediasFromOldVirtuemart( $list )
  {
    $medias = array();

    foreach ( (array)$list as $p )
    {
      $medias[] = array(
        'hashkey' => md5($p['id']),
        'ordering' => (int)$p['ordering'],
        'path' => 'src/products/import/'.str_replace('images/stories/virtuemart/product/', '', $p['image'])
      );
    }

    return $medias;
  }

  private function preparePricesFromOldVirtuemart( $list )
  {
    $prices = array();

    foreach ((array)$list as $p) {
      if ($p['name'] == 'Kiskeredkedelmi') {
        $prices['ar1'] = $p['net_price'];
      }
      if ($p['name'] == 'Viszonteladó') {
        $prices['ar2'] = $p['net_price'];
      }
      if ($p['name'] == 'Nagykereskedő') {
        $prices['ar3'] = $p['net_price'];
      }
    }

    return $prices;
  }

  public function connectSyncCategories($catarr = array(), $old, $new)
  {
    $cats = array();

    foreach ((array)$catarr as $c) {
      $find = $new[$old[trim($c['name'])]];
      if ($find) {
        if (!in_array($find['ID'],(array)$cats['ids'])) {
          $cats['ids'][] = $find['ID'];
          $cats['list'][] = array(
            'search' => $c,
            'keyid' => $old[trim($c['name'])],
            'obj' => $find,
            'connectDBID' => $find['ID']
          );
        }
      }

    }
    return $cats;
  }

  public function updateJoomlaPreparedProductContent( $products = array() )
  {
    $new_insert = array();
    $ins_header = array('hashkey', 'origin_id', 'cikkszam', 'gyarto_kod', 'prod_id', 'last_sync_up', 'termek_nev', 'termek_leiras', 'beszerzes_netto','ean_code', 'marka_nev', 'io', 'mennyisegegyseg', 'kisker_ar_netto');

    foreach ( (array)$products as $p )
    {
      // SAVE
      if ($p['dbdata']) {
        // termekid
        $pid = $p['dbdata']['ID'];

        // kategória sync
        if (!empty($p['pushtocats']['ids'])) {
          //$this->reconnectProductCategories($pid, $p['pushtocats']['ids'], true);
        }

        // create manufacturer
        $this->manufacturerAdder((int)$p['gyarto_id'], trim($p['gyarto']));

        $update = array(
          'rovid_leiras' => addslashes($p['rovid_leiras']),
          'leiras' => addslashes($p['leiras']),
          'meta_title' => ($p['meta_title'] == '') ? NULL : addslashes($p['meta_title']),
          'meta_desc' => ($p['meta_desc'] == '') ? NULL : addslashes($p['meta_desc']),
          'marka' => (int)$p['gyarto_id']
        );

        // XML adatok frissítése adatbázisban
        if ( $p['prices'] )
        {
          foreach ( (array)$p['prices'] as $pricekey => $price )
          {
            $xmlupdateparams = array();
            $xmlupdateparams['cikkszam'] = trim($p['dbdata']['cikkszam']);
            $xmlupdateparams['originid'] = 1;
            $xmlupdateparams['priceval'] = (float)($price);

            $arqry = "UPDATE xml_temp_products SET {$pricekey} = :priceval WHERE origin_id = :originid and cikkszam = :cikkszam and {$pricekey} != :priceval";

            $this->db->squery($arqry, $xmlupdateparams);
          }
        }

        $image_insert = array();
        if ( $p['medias'] ) {
          $this->db->squery("DELETE FROM shop_termek_kepek WHERE byimport = 1 and termekID = :id", array('id' => $pid));
          foreach ( (array)$p['medias'] as $media )
          {
            $image_insert[] = array(
              'hashkey' => $media['hashkey'],
              'termekID' => $pid,
              'kep' => $media['path'],
              'sorrend' => $media['ordering'],
              'byimport' => 1
            );

            if ( $media['ordering'] == 1 )
            {
              $q = "UPDATE shop_termekek SET profil_kep = '{$media['path']}' WHERE xml_import_origin = 1 and ID = {$pid}";
              $this->db->query( $q );
            }
          }
        }

        if ($image_insert) {
          $this->db->multi_insert_v2(
            'shop_termek_kepek',
            array('hashkey', 'termekID', 'kep', 'sorrend', 'byimport'),
            $image_insert
          );
        }

        $this->db->update(
          'shop_termekek',
          $update,
          sprintf("ID = %d", $pid)
        );
      }
      else
      {
        // Létrehozás
        if ( $this->crm )
        {
          $items = array();
          $item = array(
						'termek_id' => 0,
						'cikkszam' => trim($p['sku']),
						'gyarto_cikkszam' => trim($p['sku']),
						'gyartocikkszam' => trim($p['sku']),
						'megnevezes' => trim($p['name']),
						//'vonalkod' => '1000001000004',
						'megjegyzes' => trim($p['rovid_leiras']),
						'afa' => 27,
						'netto_egysegar' => (float)$p['prices']['ar1'],
						'termekcsoport_id' => 1,
						'koltseghely_id' => 2,
						'termek' => 1,
						'mennyisegiegyseg' => trim($p['unit'])
					);

          if ($p['prices']) {
            foreach ((array)$p['prices'] as $priceid => $price ) {
              if ($priceid == 'ar1') {
                continue;
              }
              $priceid = str_replace("ar", "", $priceid);

              $item['afa'.$priceid] = 27;
              $item['netto_egysegar'.$priceid] = (float)$price;
            }
          }

					$items[] = $item;

					//$ins = $this->crm->addProduct( $items );

          if ($ins && $ins['error'] == 0)
          {
            // Ha rögzítésre került a termék a cashmanban és nincs hiba
            $hashkey = md5('1_'.$ins['uzenet']);
            $pin = array(
              'hashkey' => $hashkey,
              'origin_id' => 1,
              'prod_id' => $ins['uzenet'],
              'cikkszam' => trim($p['sku']),
              'gyarto_kod' => trim($p['sku']),
              'last_sync_up' => NOW,
              'termek_nev' => trim($p['name']),
              'termek_leiras' => addslashes($p['leiras']),
              'beszerzes_netto' => 0,
              'ean_code' => $ins['uzenet'],
              'marka_nev' => $p['gyarto'],
              'io' => 1,
              'mennyisegegyseg' => $p['unit'],
              'kisker_ar_netto' => (float)$p['prices']['ar1']
            );

            if ($p['prices']) {
              foreach ((array)$p['prices'] as $priceid => $price ) {
                $pin[$priceid] = (float)$price;
                $ins_header[] = $priceid;
              }
            }

            $new_insert[] = $pin;
          } else {
            echo 'CashmanFX API - Rögzítés: ['.$p['sku'].'] ' . $ins['hiba'] . "<br>";
          }
        }

      }
    }

    // Új termékek importálása a temp mappába
    if ($new_insert && false)
    {
      $this->db->multi_insert_v2(
        'xml_temp_products',
        $ins_header,
        $new_insert
      );
    }
  }

  public function registerUnregisteredProductToCashman( )
  {
    if (!$this->crm) {
      return false;
    }

    $q = $this->db->squery("SELECT r.* FROM xml_temp_products as r WHERE 1=1");

    if ($q->rowCount() != 0) {
      $data = $q->fetchAll(\PDO::FETCH_ASSOC);

      //echo '<pre>';
      /*print_r($data);
      echo '</pre>';
      exit;
      */

      foreach ($data as $d)
      {
        $cikkszam = trim($d['cikkszam']);
        //echo "<br>".$cikkszam ;
        $check_cm = $this->crm->getProduct($cikkszam);
        //var_dump($check_cm);
        $ins = false;
        //continue;

        if (true)
        {
          if ( $check_cm === false )
          {
            // Ha nem létezik a cashman-ben, akkor létrehozás
            $items = array();
            $item = array(
              'termek_id' => 0,
              'cikkszam' => $cikkszam,
              'gyarto_cikkszam' => $cikkszam,
              'gyartocikkszam' => $cikkszam,
              'megnevezes' => trim($d['termek_nev']),
              //'vonalkod' => '1000001000004',
              'megjegyzes' => '',
              'afa' => 27,
              'netto_egysegar' => (float)$d['ar1'],
              'termekcsoport_id' => 1,
              'koltseghely_id' => 2,
              'termek' => 1,
              'mennyisegiegyseg' => trim($d['mennyisegegyseg'])
            );

            for ($i=0; $i <= 10 ; $i++) {
              $priceid = $i;
              $price = (float)$d['ar'.$i];

              if ($price != 0) {
                $item['afa'.$priceid] = 27;
                $item['netto_egysegar'.$priceid] = $price;
              }
            }

            $items[] = $item;

            //$ins = $this->crm->addProduct( $items );
            //echo 'INSERT: ';
            //print_r($item);

            if ($ins && $ins['error'] == 0)
            {
              $this->db->update(
                'xml_temp_products',
                array(
                  'prod_id' => (int)trim($ins['uzenet'])
                ),
                sprintf("origin_id = %d and cikkszam = '%s'", 1, $cikkszam)
              );
            }
            $ins = false;
            $items = array();
            $item = array();

          } else {
            // Ha már létezik a cashmanban

            $items = array();
            $item = array(
              // Kötelezőek
              'termek_id' => $check_cm['id'],
              // Vonalkód a prod_id (cm ID) ha nincs vonalkód definiálva, ha van, akkor az ean_code frissül
              'vonalkod' => ($d['ean_code'] == '') ? $check_cm['id'] : trim($d['ean_code']),
              'megnevezes' => trim($d['termek_nev']),
              'termekcsoport_id' => 1,
              'afa' => 27,
              'mennyisegiegyseg' => trim($d['mennyisegegyseg']),
              'netto_egysegar' =>  (float)$d['ar1'],
              'termek' => 1,
              'cikkszam' => $cikkszam,
              // Kiegészítés:
              'minimum' => (int)$d['virtualis_keszlet']

            );
            for ($i=0; $i <= 10 ; $i++) {
              $priceid = $i;
              $price = (float)$d['ar'.$i];

              if ($price != 0) {
                $item['afa'.$priceid] = 27;
                $item['netto_egysegar'.$priceid] = $price;
              }
            }
            $items[] = $item;
            $ins = $this->crm->addProduct( $items );

            //echo 'UPDATE: ';
            //print_r($ins);

            if ($ins && $ins['error'] == 0)
            {
              // update prod_id
              $this->db->update(
                'xml_temp_products',
                array(
                  'prod_id' => (int)trim($check_cm['id'])
                ),
                sprintf("origin_id = %d and cikkszam = '%s'", 1, $cikkszam)
              );
            }

            $ins = false;
            $items = array();
            $item = array();
          }
        }
      }
    }

    return false;
  }

  public function __destruct()
  {
    parent::__destruct();
  }
}
