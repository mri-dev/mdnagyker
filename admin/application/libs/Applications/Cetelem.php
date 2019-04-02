<?php
namespace Applications;

class Cetelem 
{
	const STATUS_UNSTARTED 		= 0;
	const STATUS_WAITINGFORDOC 	= 1;
	const STATUS_INPROGRESS 	= 2; 	// narancs állapot
	const STATUS_CANCEL 		= 100; 	// piros állapot
	const STATUS_DONE 			= 200; 	// zöld állapot

	private $sandbox 	= false;
	private $db 		= null;
	private $shopcode 	= 0;
	private $society 	= 0;
	private $barem 		= 0;
	private $statuses 	= array(
		0 => array(
			'text' => 'Hiteligénylés megkezdésére várakozik.',
			'color' => 'orange',
			'class' => 'pre'
		),
		1 => array(
			'text' => 'Hiteligénylés megkezdve, dokumentumokra várakozik.',
			'color' => '#42A7E4',
			'class' => 'inprogress'
		),
		2 => array(
			'text' => 'Elbírálás folyamatban.',
			'color' => 'orange',
			'class' => 'processing'
		),
		100 => array(
			'text' => 'Sikertelen hiteligénylés. Kérelem elutasítva.',
			'color' => '#E05A25',
			'class' => 'cancel'
		),
		200 => array(
			'text' => 'Sikeres hiteligénylés, pozitív elbírálat.',
			'color' => '#31CE31',
			'class' => 'done'
		)
	);
	
	private $status_code = array(
		'start' => self::STATUS_WAITINGFORDOC,
		'narancs' => self::STATUS_INPROGRESS,
		'piros' => self::STATUS_CANCEL,
		'zold' => self::STATUS_DONE
	);

	public function __construct( $shopCode, $society, $barem, $arg = array() )
	{
		$this->shopcode = $shopCode;
		$this->society 	= $society;
		$this->barem 	= $barem;
		$this->db 		= $arg[db];

		return $this;
	}

	private function beforeSOAP()
	{
		if ($this->sandbox) 
		{
			ini_set('soap.wsdl_cache', '0');	
			define('ECOMMERCE_START_URL', 'https://ecomdemo.cetelem.hu/cetelem_aruhitel/hitelbiralat');
			define('ECOMMERCE_SOAP_URL', 'https://ecomdemo.cetelem.hu/ecommerce/EcommerceService?wsdl');
		} 
		else 
		{			
			ini_set('soap.wsdl_cache_enable', '1');
			ini_set('soap.wsdl_cache', '1');
			define('ECOMMERCE_START_URL', 'https://ecom.cetelem.hu/cetelem_aruhitel/hitelbiralat');
			define('ECOMMERCE_SOAP_URL', 'https://ecom.cetelem.hu/ecommerce/EcommerceService?wsdl');
		}
	}

	public function prepareDataJSON( $sessionkey, $values_array = array() )
	{
		$back = false;
		$default = array(
			'lastName' => false,
			'firstName' => false,
			'pcode' => false,
			'city' => false,
			'address' => false,
			'additional' => false,
			'phone' => false,
			'mobile' => false,
			'email' => false,
			'barem' => $this->barem,
			'articleId' => false,
			'cbZold' => DOMAIN.'gateway/cetelem/zold/'.$sessionkey,
			'cbPiros' => DOMAIN.'gateway/cetelem/piros/'.$sessionkey,
			'cbNarancs' => DOMAIN.'gateway/cetelem/narancs/'.$sessionkey,
			'cbTimeout' => DOMAIN.'gateway/cetelem/timeout/'.$sessionkey,
			'cbMainpage' => DOMAIN
		); 

		$back = array_replace($default, $values_array);

		return $back;
	}

	public function startTransaction( $total_price = 0, $prepareDataJSON = array() )
	{
		$this->beforeSOAP();
		/////////////////////////////////////
		$shopCode 		= $this->shopcode;
		$society 		= $this->society;
		$purchaseAmount = $total_price;

		$soap_client = new \SOAPCLient( ECOMMERCE_SOAP_URL );
		
		try 
		{
			$res 	= $soap_client->initProcess($society, $shopCode, json_encode($prepareDataJSON));	
			$result = json_decode($res, true);
		} 
		catch (\Exception $e) 
		{
			echo "SOAP Exception: " . $e->getMessage();
			exit;
		}

		if ( $result['success'] ) 
		{
			$url = ECOMMERCE_START_URL . '?shopCode=' . $shopCode . '&purchaseAmount='.$total_price.'&customerKey='. $result['customerKey'];
			header('Location: '. $url);
			exit;
		} 
		elseif($result['errorCode'] == 101) 
		{
			foreach ($results['errors'] as $fields => $msg) 
			{
				echo $field . ": ". $msg . "\n";
			}
		}

		return false;	
	}

	public function getTransactionStatus( $orderid, $formated = false )
	{
		$status = self::STATUS_UNSTARTED;
		$started = false;
		$success = false;

		if ( empty($orderid) ) { return false; }

		$start_status = $this->db->squery( "SELECT 1 FROM gateway_cetelem_ipn WHERE statusz = 'start' and megrendeles = :id ORDER BY idopont DESC", array( 'id' => $orderid ) )->rowCount();

		if ($start_status != 0) {
			$started = true;
		}

		if ($started) {
			$status = self::STATUS_WAITINGFORDOC;
		}

		$narancs_status = $this->db->squery( "SELECT 1 FROM gateway_cetelem_ipn WHERE statusz = 'narancs' and megrendeles = :id ORDER BY idopont DESC", array( 'id' => $orderid ) )->rowCount();

		if ($narancs_status != 0) {
			$status = self::STATUS_INPROGRESS;
		}

		$piros_status = $this->db->squery( "SELECT 1 FROM gateway_cetelem_ipn WHERE statusz = 'piros' and megrendeles = :id ORDER BY idopont DESC", array( 'id' => $orderid ) )->rowCount();

		if ($piros_status != 0) {
			$status = self::STATUS_CANCEL;
		}

		$zold_status = $this->db->squery( "SELECT 1 FROM gateway_cetelem_ipn WHERE statusz = 'zold' and megrendeles = :id ORDER BY idopont DESC", array( 'id' => $orderid ) )->rowCount();

		if ($zold_status != 0) {
			$success = true;
		}

		if ($success) {
			$status = self::STATUS_DONE;
		}


		if (!$formated) {
			return $status;
		}

		$stat = $this->statuses[$status];

		return '<span class="status status-'.$stat['class'].'" style="background: '.$stat['color'].';">'.$stat['text'].'</span>';
	}

	public function getIPNList( $orderid )
	{
		$q = $this->db->query("SELECT statusz, idopont FROM gateway_cetelem_ipn WHERE megrendeles = '{$orderid}' GROUP BY statusz  ORDER BY idopont ASC");

		if ($q->rowCount() == 0) {
			return array();
		}

		$list = $q->fetchAll(\PDO::FETCH_ASSOC);

		$sl = array();

		foreach ($list as $l) 
		{
			$l[statusz_text] 	= $l[statusz];
			$l[statusz] 		= $this->statuses[$this->status_code[$l[statusz]]];

			$sl[] = $l;
		}

		return $sl;
	}

	public function calc( $price = 0, $ownShare = 0 )
	{
		$back = array();
				

		$this->beforeSOAP();
		/////////////////////////////////////
		$shopCode 		= $this->shopcode;
		$society 		= $this->society;
		$barem 			= $this->barem;
		
		$soap_client = new \SOAPClient( ECOMMERCE_SOAP_URL, array( 'trace' => 1) );
	
	
		$cfg = array(
			'barem' 			=> $barem,
			'purchaseAmount' 	=> $price, 
			'ownShare' 			=> $ownShare
		);
		
		$response = $soap_client->getCalculation( (int)$society, (int)$shopCode, json_encode($cfg) );
		
		

		$result = json_decode($response, true);
			

		if ( !$result['success'] ) 
		{
			return $result['errorMessage'];
		}

		foreach ( $result['calculationData']['durations'] as $duration ) 
		{
			$back[$duration] = $result['calculationData']['results']['d_'.$duration];
		}
	
		return $back;
	}

	public function sandboxMode( $flag = true )
	{
		$this->sandbox = $flag;

		return $this;
	}

	public function __destruct()
	{
		$this->db = null;
	}
}