<?php
namespace ResourceImporter;

use ResourceImporter\ResourceImportInterface;

/**
 *
 */
class CashmanAPI extends ResourceImportBase
{
  function __construct( $arg = array() )
  {
    return parent::__construct( $arg );
  }

  public function updateStock( $data = array() )
  {

  }

  public function __destruct()
  {
    parent::__destruct();
  }
}
