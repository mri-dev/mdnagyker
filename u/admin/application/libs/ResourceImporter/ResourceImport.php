<?php
namespace ResourceImporter;

use ResourceImporter\ResourceImportInterface;

/**
 *
 */
class ResourceImport extends ResourceImportBase implements ResourceImportInterface
{

  function __construct( $arg = array() )
  {
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

  public function findOldWebshopProducts( $products, $old_cats, $new_cats )
  {
    $data = array();

    foreach ($products as $p) {
      $prod = $this->findProductBySKU($p['sku']);
      if(!$prod) continue;
      $p['pushtocats'] = $this->connectSyncCategories($p['cats'], $old_cats, $new_cats);
      $p['dbdata'] = $prod;
      $data[] = $p;
    }

    return $data;
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
    foreach ( (array)$products as $p )
    {
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

        $this->db->update(
          'shop_termekek',
          $update,
          sprintf("ID = %d", $pid)
        );
      }
    }
  }

  public function __destruct()
  {
    parent::__destruct();
  }
}
