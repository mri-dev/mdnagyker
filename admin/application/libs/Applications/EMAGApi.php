<?php
namespace Applications;

class EMAGApi
{
  const API_URI = 'https://marketplace-api.emag.hu/api-3/';
  const USER_NAME = 'Mobildata';
  const USER_PW = 'mobildata123';
  private $db = null;

	public function __construct( $arg = array() )
	{
		$this->db = $arg[db];
		return $this;
	}

	public function __destruct()
	{
		$this->db = null;
	}
}
