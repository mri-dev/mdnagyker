<?
namespace Applications;
/**
* Borgun fizetési kapu - SecurePay 2.0
* https://www.b-payment.hu/docs/securepay
**/
class Borgun
{
  public $dev = true;
  public $merchantID = '';
  public $paymentGatewayID = '';
  public $secretKey = '';
  public $currency = 'ISK';
  public $lang = 'HU';
  public $order_id = '';
  public $orderdata = false;
  public $db = null;

	function __construct( $db = null ) {
    $this->website = 'https://'.$_SERVER['HTTP_HOST'];
    $this->db = $db;
		return $this;
	}

  public function setMerchant( $v )
  {
    $this->merchantID = $v;
    return $this;
  }

  public function setSecretKey( $v )
  {
    $this->secretKey = $v;
    return $this;
  }

  public function setGatewayID( $v )
  {
    $this->paymentGatewayID = $v;
    return $this;
  }

  public function setCurrency( $v )
  {
    $this->currency  = $v;
    return $this;
  }

  public function setOrderId( $v )
  {
    $this->order_id  = $v;
    return $this;
  }

  public function setData( $v )
  {
    $this->orderdata  = $v;
    return $this;
  }

  // OrderId|Amount|Currency
  public function orderHMAC( $orderID, $amount = 0)
  {
    $message = utf8_encode(trim($orderID).'|'.trim($amount).'|'.$this->currency);
    $hash = hash_hmac('sha256', $message, $this->secretKey);
    return $hash;
  }

  // MerchantId|ReturnUrlSuccess|ReturnUrlSuccessServer|OrderId|Amount|Currency
  private function checkhashHMAC( $orderID, $amount = 0, $currency)
  {
    $message = trim(utf8_encode(trim($this->merchantID).'|'.trim($this->website).'/gateway/borgun/success|'.trim($this->website).'|'.trim($orderID).'|'.trim($amount).'|'.trim($currency)));
    $hash = hash_hmac('sha256', $message, $this->secretKey);
    return $hash;
  }

  public function payingFORM()
  {
    $order = $this->orderdata;
    $total_price = 0;
    $showfiled = false;

    $itemform = '';
    $ii = 0;

    foreach ((array)$order['items'] as $item ) {
      $each = $item['egysegAr'];
      $each = 100;
      $itemform .= '<input type="'. (($showfiled) ? 'text' : 'hidden' ) .'"  name="itemdescription_'.$ii.'" value="Termek '.$ii.'" />';
      $itemform .= '<input type="'. (($showfiled) ? 'text' : 'hidden' ) .'"  name="itemcount_'.$ii.'" value="'.$item['me'].'" />';
      $itemform .= '<input type="'. (($showfiled) ? 'text' : 'hidden' ) .'"  name="itemunitamount_'.$ii.'" value="'.$each.'" />';
      $itemform .= '<input type="'. (($showfiled) ? 'text' : 'hidden' ) .'"  name="itemamount_'.$ii.'" value="'.($each * $item['me']).'" />';
      $total_price += ($each * $item['me']);
      $ii++;
    }

    // Szállítási költség
    if ( $order['szallitasi_koltseg'] > 0 ) {
      $itemform .= '<input type="'. (($showfiled) ? 'text' : 'hidden' ) .'"  name="itemdescription_'.$ii.'" value="Szállítási költség" />';
      $itemform .= '<input type="'. (($showfiled) ? 'text' : 'hidden' ) .'"  name="itemcount_'.$ii.'" value="'.$item['me'].'" />';
      $itemform .= '<input type="'. (($showfiled) ? 'text' : 'hidden' ) .'"  name="itemunitamount_'.$ii.'" value="'.$order['szallitasi_koltseg'].'" />';
      $itemform .= '<input type="'. (($showfiled) ? 'text' : 'hidden' ) .'"  name="itemamount_'.$ii.'" value="'.($order['szallitasi_koltseg']).'" />';
      $ii++;
      $total_price += $order['szallitasi_koltseg'];
    }

    //$total_price = number_format($total_price, 2, ".","");
    $checkhash = $this->checkhashHMAC( $order['azonosito'], $total_price, $this->currency);

    $form = '';
    $form .= '<form id="form1" action="'.$this->getServiceURI().'" method="post">';
    $form .= '<input type="'. (($showfiled) ? 'text' : 'hidden' ) .'" name="merchantid" value="'.trim($this->merchantID).'" />';
    $form .= '<input type="'. (($showfiled) ? 'text' : 'hidden' ) .'" name="paymentgatewayid" value="'.trim($this->paymentGatewayID).'" />';
    $form .= '<input type="'. (($showfiled) ? 'text' : 'hidden' ) .'" name="checkhash" value="'.$checkhash.'" />';
    $form .= '<input type="'. (($showfiled) ? 'text' : 'hidden' ) .'" name="orderid" value="'.$this->order_id.'" />';
    $form .= '<input type="'. (($showfiled) ? 'text' : 'hidden' ) .'" name="currency" value="'.$this->currency.'" />';
    //$form .= '<input type="'. (($showfiled) ? 'text' : 'hidden' ) .'" name="reference" value="'.$order["ID"].'" />';
    $form .= '<input type="'. (($showfiled) ? 'text' : 'hidden' ) .'" name="language" value="'.$this->lang.'" />';

    $form .= '<input type="'. (($showfiled) ? 'text' : 'hidden' ) .'" name="returnurlsuccess" value="'.$this->website.'/gateway/borgun/success/?order_id='.$this->order_id.'" />';
    $form .= '<input type="'. (($showfiled) ? 'text' : 'hidden' ) .'" name="returnurlcancel" value="'.$this->website.'/gateway/borgun/cancel/?order_id='.$this->order_id.'" />';
    $form .= '<input type="'. (($showfiled) ? 'text' : 'hidden' ) .'" name="returnurlerror" value="'.$this->website.'/gateway/borgun/error/?order_id='.$this->order_id.'" />';

    $form .= '<input type="'. (($showfiled) ? 'text' : 'hidden' ) .'" name="buyername" value="'.(trim($order['nev'])).'" />';
    $form .= '<input type="'. (($showfiled) ? 'text' : 'hidden' ) .'" name="buyeremail" value="'.(trim($order['email'])).'" />';

    $form .= $itemform;

    $form .= '<input type="'. (($showfiled) ? 'text' : 'hidden' ) .'" name="amount" value="'.$total_price.'"/>';
    //$form .= '<input type="'. (($showfiled) ? 'text' : 'hidden' ) .'" name="pagetype" value="0" />';
    //$form .= '<input type="'. (($showfiled) ? 'text' : 'hidden' ) .'" name="skipreceiptpage" value="0" />';
    //$form .= '<input type="'. (($showfiled) ? 'text' : 'hidden' ) .'" name="merchantlogo" value="https://www.b-payment.hu/docs/images/logo.jpg" />';
    //$form .= '<input type="'. (($showfiled) ? 'text' : 'hidden' ) .'" name="merchantemail" value="'.$order['email'].'" />';
    $form .= '<button type="submit" name="PostButton" class="pay-button-borgun">Bankkártyás fizetés indítása</button>';
    $form .= '<br><img src="'.IMG.'borgun-payment.jpg" style="height: 50px;" alt="Borgun B-Payment"/>';
    $form .= '</form>';

    /*
    echo '<pre>';
    print_r($order);
    echo '</pre>';
    */

    return $form;
  }

  public function logIPNTransaction( $valid_orderhash = false )
  {
    $post = $_POST;
    $orderid = $_GET['order_id'];

    $this->db->insert(
        "gateway_borgun_ipn",
        array(
          'megrendeles' => $orderid,
          'statusz' => $post['status'],
          'stepstatus' => ($post['step'] == '') ? NULL : $post['step'],
          'orderhash' => ($post['orderhash'] == '') ? NULL : $post['orderhash'],
          'datastr' => json_encode($post, \JSON_UNESCAPED_UNICODE),
          'orderhashvalid' => (!$valid_orderhash) ? NULL : (int)$valid_orderhash
        )
    );
  }

  function getServiceURI()
  {
    $prefix = ( $this->dev ) ? 'test' : 'securepay' ;
    return 'https://'.$prefix.'.borgun.is/SecurePay/default.aspx';
  }

  public function __destruct()
  {
    $this->db = null;
  }
}
?>
