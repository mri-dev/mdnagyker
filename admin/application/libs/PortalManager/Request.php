<?
namespace PortalManager;

/**
* class Request
* @package PortalManager
*/
class Request
{	
	private $curl = null;
	private $data = null;
	private $type = 'http';
	private $result = null;
	private $use_port = 80;
	private $post_setted = false;
	private $debug_mode = false;
	private $timeout = 30;
	private $JSONPrefix = '';
	private $url = null;

	public function post( $url, $datas = array() , $type = 'http' )
	{
		$this->post_setted = true;
		$this->url 	= $url;
		$this->type = $type;
		$this->data = $datas;

		switch ( $this->type ) {
			case 'json':
				$this->data = json_encode( $this->data, JSON_UNESCAPED_UNICODE );
			break;
			case 'http':
				$this->data = http_build_query( $this->data );
			break;			
		}

		return $this;
	}

	public function setPort( $port )
	{
		$this->use_port = $port;

		return $this;
	}

	public function setJSONPrefix( $prefix )
	{
		$this->JSONPrefix = $prefix;

		return $this;
	}

	public function setDebug( $flag )
	{
		$this->debug_mode = $flag;

		return $this;
	}

	public function setTimeout( $sec = 30 )
	{
		$this->timeout = $sec;

		return $this;
	}

	public function send()
	{

		if( !$this->post_setted ) return false;

		$this->curl = curl_init();

		curl_setopt($this->curl, CURLOPT_URL, $this->url );

		curl_setopt($this->curl, CURLOPT_POST, 1);

		curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true );

		curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, false );

		curl_setopt($this->curl, CURLOPT_SSL_VERIFYHOST, false);

		curl_setopt($this->curl, CURLOPT_PORT, $this->use_port );

		curl_setopt($this->curl, CURLOPT_TIMEOUT, $this->timeout );

		curl_setopt($this->curl, CURLOPT_CONNECTTIMEOUT, $this->timeout );

		//curl_setopt($this->curl, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.0.3705; .NET CLR 1.1.4322)');

		curl_setopt($this->curl, CURLOPT_HEADER, false);

		// Debug log
		if( $this->debug_mode ) {

			curl_setopt($this->curl, CURLOPT_VERBOSE, true);

			$verbose = fopen('php://temp', 'w+');

			curl_setopt($this->curl, CURLOPT_STDERR, $verbose);

		}

		switch ( $this->type ) {
			case 'json':
				$this->data = $this->JSONPrefix.urlencode($this->data);
				curl_setopt($this->curl, CURLOPT_HTTPHEADER, array( 
					'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
					'Accept: application/json',
					'Content-Length: ' . strlen($this->data)
				)); 
			break;
			case 'http':
				curl_setopt($this->curl, CURLOPT_HTTPHEADER, array(
					'Content-Type: application/x-www-form-urlencoded',
					'Content-Length: ' . strlen($this->data)
				)); 
			break;			
		}

		curl_setopt($this->curl, CURLOPT_POSTFIELDS, $this->data );

		if( $this->result = curl_exec( $this->curl ) ) { } else {
			throw new \Exception( curl_error( $this->curl ) );
		}

		if( $this->debug_mode ) {

			rewind($verbose);

			$verboseLog = stream_get_contents($verbose);

			$info = curl_getinfo( $this->curl );

			echo "Request info:\n<pre>", print_r($info), "</pre>\n";

			echo "Verbose info:\n<pre>", htmlspecialchars($verboseLog), "</pre>\n";

		}



		$this->post_setted = false;

		return $this;
	}

	public function getResult()
	{
		if ( !$this->result ) { return false; }

		return $this->result;
	}

	public function decodeJSON( $json )
	{
		return json_decode( $json, true);
	}

	public function __destruct()
	{	
		curl_close( $this->curl );
	}
}
?>