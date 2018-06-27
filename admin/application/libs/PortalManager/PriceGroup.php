<?php
namespace PortalManager;

/*************************
* class PriceGroup
* @package PortalManager
* @version 1.0
**************************/
class PriceGroup
{
	private $db = null;
	private $id = false;
	private $item_data = false;

	function __construct( $elem_id, $arg = array() )
	{
		$this->db = $arg[db];
		$this->id = $elem_id;

		$this->get();

		return $this;
	}

	/**
	 * Kategória adatainak lekérése
	 * @return void
	 */
	private function get()
	{
		$cat_qry 	= $this->db->query( sprintf("
			SELECT *
			FROM ".\PortalManager\PriceGroups::DBTABLE."
			WHERE ID = %d;", $this->id));
		$item_data = $cat_qry->fetch(\PDO::FETCH_ASSOC);
		$this->item_data = $item_data;
	}

	/**
	 * Aktuális kategória adatainak szerkesztése / mentése
	 * @param  array $db_fields új kategória adatok
	 * @return void
	 */
	public function edit( $db_fields )
	{
		$this->db->update(
			\PortalManager\PriceGroups::DBTABLE,
			$db_fields,
			"ID = ".$this->id
		);
	}

	/**
	 * Aktuális kategória törlése
	 * @return void
	 */
	public function delete()
	{
		$this->db->query(sprintf("DELETE FROM ".\PortalManager\PriceGroups::DBTABLE." WHERE ID = %d",$this->id));
	}

	/*===============================
	=            GETTERS            =
	===============================*/
	public function getTitle()
	{
		return $this->item_data['title'];
	}
	public function getKey()
	{
		return $this->item_data['groupkey'];
	}
	public function getId()
	{
		return $this->item_data['ID'];
	}
	/*-----  End of GETTERS  ------*/

	public function __destruct()
	{
		$this->db = null;
		$this->item_data = false;
	}

}

?>
