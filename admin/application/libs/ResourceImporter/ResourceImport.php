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
    $li = 0;
    foreach ($products as $p) {
      $li++;
      //if ($li > 5 ) { break; }
      $prod = $this->findProductBySKU($p['sku']);
      if(!$prod) continue;
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
        $prices['ar2'] = $p['net_price'];
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

        // XML adatok frissítése adatbázisban
        if ( $p['prices'] )
        {
          foreach ( (array)$p['prices'] as $pricekey => $price )
          {
            $xmlupdateparams = array();
            $xmlupdateparams['cikkszam'] = trim($p['dbdata']['cikkszam']);
            $xmlupdateparams['originid'] = (int)($p['dbdata']['xml_import_origin']);
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
              $this->db->query("UPDATE shop_termekek SET profil_kep = '{$media['path']}' WHERE xml_import_origin = {$p['dbdata']['xml_import_origin']} and ID = {$pid}");
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
    }
  }

  public function __destruct()
  {
    parent::__destruct();
  }
}
