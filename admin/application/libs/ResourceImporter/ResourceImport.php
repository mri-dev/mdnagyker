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

  public function __destruct()
  {
    parent::__destruct();
  }
}
