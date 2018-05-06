<?php
namespace Interfaces;

interface InstallModules{
  public function checkInstalled();
  public function installer( \PortalManager\Installer $installer );
}
?>
