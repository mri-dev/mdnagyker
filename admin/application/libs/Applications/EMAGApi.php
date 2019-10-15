<?php
namespace Applications;

class EMAGApi
{
  const API_URI = 'https://marketplace.emag.hu/api-3';
  const USER_NAME = 'Mobildata';
  const USER_PW = 'mobildata123';
  private $db = null;
  private $requestData = array();
  private $hash = null;
  private $action = '/read';
  private $endpoint = '/';

	public function __construct( $auth = array(), $arg = array() )
	{
		$this->db = $arg[db];

    if ( empty($auth) || ( empty($auth['username']) || empty($auth['password']) ) )
    {
      throw new \Exception("Az EMAG Auth-hoz szükséges a felhasználónév és a jelszó.");
    }

    $this->hash = base64_encode( $auth['username'] . ':' . $auth['password'] );

		return $this;
	}

  public function filter( $arg = array() )
  {

    $this->requestData = array_merge($this->requestData, $arg);

    return $this;
  }

  public function setAction( $v )
  {
    $this->action = '/'.$v;
    return $this;
  }

  public function setEndpoint( $v )
  {
    $this->endpoint = '/'.$v;
    return $this;
  }

  public function run()
  {
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, self::API_URI . $this->endpoint . $this->action);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Basic ' . $this->hash]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($this->requestData));
    $result = json_decode(curl_exec($ch), true);
    curl_close($ch);
    return $result;
  }

	public function __destruct()
	{
		$this->db = null;
	}
}
