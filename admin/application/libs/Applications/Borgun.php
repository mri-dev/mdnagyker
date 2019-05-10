<?
namespace Applications;
/**
* Borgun fizetési kapu - SecurePay 2.0
* https://www.b-payment.hu/docs/securepay
**/
class Borgun
{
  public $dev = true;
  public $merchantID = 9275444;
  public $paymentGatewayID = 16;
  public $secretKey = 99887766;
  public $returnURI = $_SERVER['HTTP_HOST'];

	function __construct() {
		return $this;
	}


  function makeCheckHash()
  {
    // code...
  }

  // MerchantId|ReturnUrlSuccess|ReturnUrlSuccessServer|OrderId|Amount|Currency
  private function makeHMAC( $orderID, $amount = 0, $currency = 'HUF')
  {
    $message = utf8_encode($this->secretKey.'|https://borgun.is|https://borgun.is/success|'.$orderID.'|'.$amount.'|'.$currency);
    $checkhash = hash_hmac('sha256', $message, $this->secretKey);
    return $checkhash;
  }

  function getServiceURI()
  {
    $prefix = ( $this->dev ) ? 'test' : 'securepay' ;
    return 'https://'.$prefix.'.borgun.is/securepay/default.aspx';
  }
}
?>
