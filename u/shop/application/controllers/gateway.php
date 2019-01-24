<? 
use Applications\Simple;
use Applications\Cetelem;
use MailManager\Mailer;
use PortalManager\Template;
use PortalManager\Request;
use PortalManager\Admin;
use PortalManager\Traffic;

class gateway extends Controller
{
		function __construct(){	
			parent::__construct();
			parent::$pageTitle = '';
	
			
			// SEO Információk
			$SEO = null;
			// Site info
			$SEO .= $this->view->addMeta('description','');
			$SEO .= $this->view->addMeta('keywords','');
			$SEO .= $this->view->addMeta('revisit-after','3 days');
			
			// FB info
			$SEO .= $this->view->addOG('type','website');
			$SEO .= $this->view->addOG('url',DOMAIN);
			$SEO .= $this->view->addOG('image',DOMAIN.substr(IMG,1).'noimg.jpg');
			$SEO .= $this->view->addOG('site_name',TITLE);
			
			$this->view->SEOSERVICE = $SEO;
		}
		
		function test()
		{
			$this->hidePatern = true;

			$this->Admin = new Admin( false, array( 'db' => $this->db ));
			switch ( $this->view->gets[2] ) {
				case 'img':
					//echo realpath(__FILE__);
					$this->Admin->autoProductImageConnecter( array( 'image_path' => '../../admin/src/products/all' ));	
				break;
			}
		}

		/**
		 * WEBSHOP API
		 */
		function api() {
			$this->hidePatern = true;
			$error = false;
			$result = array(
				"error" => 0,
				"msg" => ""
			);
						
			$valid_commands = array( 'articleUpdate', 'saleReport', 'webshopSale', 'inventory' );
			
			$postjson 	= file_get_contents('php://input');						
			$api 		= json_decode( urldecode($postjson) );
						
			if( !$error )
				if ( !$api ) {
					$error = "Hibás JSON kérés. Kérjük, hogy ellenőrízze a struktúrát!";
				} else {
					$error = false;
				}

			if( !$error )
				if ( $api->command == "" ) {
					$error = "Ismeretlen művelet nem végrehajtható!";
				} else {
					$error = false;
				}

			if( !$error )
				if ( !in_array( $api->command, $valid_commands ) ) {
					$error = "command=".$api->command . ": művelet nem engedélyezett!";
				} else {
					$error = false;
				}

			if ( !$error ) {	
				switch ( $api->command ) {
					// Megrendelés értesítő visszaigazolás
					case 'saleReport':
					
					break;
					// Termék raktárkészlet frissítés
					case 'inventory':
						
						

					break;
					// Termék frissítés
					case 'articleUpdate':
						/**
						 * Termék fő adatok
						 * - articleid
						 * - name
						 * - number
						 * - description
						 * */
						$prod_data = $api->parameters->article;

						/**
						 * Variációk a termékeknek
						 * - variantid
						 * - color_number
						 * - color_name
						 * - size
						 * - netprice
						 * */
						$variants = $api->parameters->variants;

						/**
						 * Hibaüzenet frissítés során
						 * */
						$error = $api->parameters->error;

						$inserted = 0;

						if( count($variants) > 0 ) {
							foreach ( $variants as $va ) {
								$netto 	= 0;
								$brutto = 0;

								$netto = (int)$va->netprice;

								if ( $netto <= 0 || !$netto ) {
									$netto = 0;
								}

								$brutto = $netto * 1.27;

								if ( $netto != 0 ) {

									$update_data = array();

									$update_data['netto_ar'] = $netto;
									$update_data['brutto_ar'] = $brutto;

									if ( $prod_data->name ) 		$update_data['nev'] = $prod_data->name;
									if ( $prod_data->description ) 	$update_data['rovid_leiras'] = $prod_data->description;
									if ( $va->color_number) 		$update_data['szin_kod'] = $va->color_number;
									if ( $va->color_name ) 			$update_data['szin'] = $va->color_name;
									if ( $va->size ) 				$update_data['meret'] = $va->size;


									$check_usage = $this->db->query( sprintf("SELECT 1 FROM shop_termekek WHERE raktar_articleid = %d and raktar_variantid = %d;", $prod_data->articleid, $va->variantid))->rowCount();

									try {

										if ( $check_usage !== 0 ) {
											$this->db->update( 
												'shop_termekek', 
												$update_data, 
												sprintf("raktar_articleid = %d and raktar_variantid = %d", $prod_data->articleid, $va->variantid ) 
											);
										} else {
											$inserted++;
											$update_data['raktar_articleid'] 	= $prod_data->articleid;
											$update_data['raktar_variantid'] 	= $va->variantid;
											$update_data['raktar_number'] 		= $prod_data->number;
											$update_data['cikkszam']			= $prod_data->articleid.'-'.$va->variantid;
											$update_data['kulcsszavak'] 		= $prod_data->name . ' '. str_replace(array( ' / ', ', ', ',' ), ' ', $va->color_name ) . ' ' . $va->size;
											// Alapértelmezett márka
											$update_data['marka'] = $this->view->settings['alapertelmezett_marka'];
											// Alapértelmezett termék állapot
											$update_data['keszletID'] = $this->view->settings['alapertelmezett_termek_allapot'];
											// Alapértelmezett szállítási idő
											$update_data['szallitasID'] = $this->view->settings['alapertelmezett_termek_szallitas'];
											$update_data['lathato'] = 0;

											$ins_data = array();
											foreach ( $update_data as $d ) {
												$ins_data[] = $d;
											}

											$this->db->insert(
												'shop_termekek',
												array_combine(
													array_keys($update_data),
													$ins_data
												)
											);											
										}
										
									} catch (Exception $e) {
										$error = $e->getMessage();
									}
									
								}
							}

							if( $inserted > 0 ){
								// Értesítő e-mail új termékek létrehozásáról
								$mail = new Mailer( 
									$this->view->settings['page_title'], 
									$this->view->settings['email_noreply_address'], 
									$this->view->settings['mail_sender_mode'] 
								);	
								$mail->add( $this->view->settings['alert_email'] );
								$arg = array(
									'settings' 		=> $this->view->settings,
									'infoMsg' 		=> 'Ezt az üzenetet a rendszer küldte. Kérjük, hogy ne válaszoljon rá!',
									'new_items' 	=> $inserted,
									'source_str_json' => $postjson
								);
								$mail->setSubject( 'API értesítő: új termékek kerültek a webáruházba' );
								$mail->setMsg( (new Template( VIEW . 'templates/mail/' ))->get( 'admin_api_newproducts', $arg ) );			
								$re = $mail->sendMail();
							}
						}

					break;
				}
			}

			/**
			 * RESPONSE
			 */
			if ( $error ) {
				$result['sended_json'] = $postjson;
				$result['error'] = 1;
				$result['msg'] = $error;


			}

			$result_json = json_encode( $result, JSON_UNESCAPED_UNICODE );

			try {
				$this->db->insert( 
					"api_request",
					array_combine(
						array( "command","referencia","datum","parancs_json","valasz_json", "post_json", "get_json" ),
						array( $api->command, $_SERVER['HTTP_REFERER'], date('Y-m-d'), urldecode($postjson), $result_json, json_encode($_POST, JSON_UNESCAPED_UNICODE), json_encode($_GET, JSON_UNESCAPED_UNICODE) )
					)
				);
			}catch(\Exception $e){
				echo $e->getMessage();
			}
		
			
			header('Contant-Type: application/json');

			echo $result_json;

		}

		/**
		 * OTP SIMPLE FIZETÉSI RENDSZER 
		 * Feldolgozó egységek
		 * */
		function simple()
		{					
			$this->simple = (new Simple())
				->setMerchant( 	'HUF', $this->view->settings['payu_merchant'])
				->setSecretKey( 'HUF', $this->view->settings['payu_secret'] )
				->setCurrency( 	'HUF' );
				
			switch ( $this->view->gets[2] ) {
				case 'ipn':
					$this->hidePatern = true;
					$ipn = $this->simple->getIPN();
										
					if ( $ipn->validateReceived() ) {
						$this->db->insert( 
							"gateway_payu_ipn",
							array_combine(
								array( "megrendeles","statusz","datastr" ),
								array( $_POST['REFNOEXT'], $_POST['ORDERSTATUS'], json_encode($_POST) )
							)
						);

						switch ( $_POST['ORDERSTATUS'] ) {
							case 'PAYMENT_AUTHORIZED':
								$this->db->update(
									"orders",
									array( 
										'payu_fizetve' => 1,
										'payu_teljesitve' => 1
									),
									sprintf("azonosito = '%s'", $_POST['REFNOEXT'])
								);
							break;
							case 'COMPLETE':
								$this->db->update(
									"orders",
									array( 
										'payu_teljesitve' => 1
									),
									sprintf("azonosito = '%s'", $_POST['REFNOEXT'])
								);
							break;
							default:
								# code...
								break;
						}
						
						$this->out( 'ipn_data', $ipn->confirmReceived() );
					}					
					break;
				case 'idn':

					exit;
					$order = $this->shop->getOrderData( $this->view->gets[3] );

					/**
					 * Get IDN
					 */
					$idn = $this->simple->getIDN();
					/**
					 * Set needed fields
					 */	
					$data = array();
					$data['MERCHANT'] 		= $this->config['MERCHANT'];
					$data['ORDER_REF'] 		= $order['azonostio'];
					$data['ORDER_AMOUNT'] 	= (isset($_REQUEST['ORDER_AMOUNT'])) ? $_REQUEST['ORDER_AMOUNT'] : 'N/A';	
					$data['ORDER_CURRENCY'] = $this->simple->getCurrency();
					$data['IDN_DATE'] 		= date("Y-m-d H:i:s");
					
					$response = $idn->requestIdn( $data );
					/**
					 * Check response
					 */	
					if (isset($response['RESPONSE_CODE'])) {	
						if($idn->checkResponseHash($response)){
							/*
							* your code here
							*/
																
							print "<pre>";	
							print_r($response);
							print "</pre>";
								
						}
						//print list of missing fields
						//print_r($idn->getMissing()); 
					}

					break;
				case 'backref':
					/**
					 * Start backref
					 */		
					$backref = $this->simple->getBackref();						
					/**
					 * Add order reference number from merchant system (ORDER_REF)
					 */			
					$backref->order_ref = (isset($_REQUEST['order_ref'])) ? $_REQUEST['order_ref'] : 'N/A';
					/**
					 * Check backref
					 */		
					$message = '<div class="simple-information simple-back simple-back-'.( ($backref->checkResponse()) ? 'success' : 'error' ).'">';
					$order = $this->shop->getOrderData( $_REQUEST['order_ref'], 'azonosito' );

					if($backref->checkResponse()){						
						/**
						 * SUCCESSFUL card authorizing
						 * Notify user and wait for IPN
						 * Need to notify user
						 * 
						 */
						$backStatus = $backref->backStatusArray;									

						// Üzenet a fizetésről
						if( $order['payu_fizetve'] == 0 ){
							
							$this->db->update(
								'orders',
								array(
									'payu_fizetve' => 1
								),
								"ID = ".$order[ID]
							);
						}


						$message .= '<div class="head success">';
						
						// Notification by payment method						
						//CCVISAMC
						if ($backStatus['PAYMETHOD']=='Visa/MasterCard/Eurocard') {
							$message .= __('Sikeres kártya ellenőrzés. ');
							if ($backStatus['ORDER_STATUS']=='IN_PROGRESS') {
								$message .= __('Tranzakció megerősítésre vár.');
							} elseif ($backStatus['ORDER_STATUS']=='PAYMENT_AUTHORIZED' || $backStatus['ORDER_STATUS']=='COMPLETED') {								
								$message .= __('Sikeres tranzakció!');
							} 
						}
						//WIRE
						elseif ($backStatus['PAYMETHOD']=='Bank/Wire transfer') {
							$message .= __('Átutalás elfogadva. ');
							if ($backStatus['ORDER_STATUS']=='PAYMENT_AUTHORIZED' || $backStatus['ORDER_STATUS']=='COMPLETED') {
								$message .= __('Sikeres átutalás.');
							} 			
						}
						//CASH
						elseif ($backStatus['PAYMETHOD']=='Cash on delivery') {
							$message .= __('Megrendelés elfogadva. ');
						}

						$message .= '</div>';
							
					} else {	

						/**
						 * UNSUCCESSFUL card authorizing
						 * END of transaction
						 * Need to notify user
						 * 
						 */
						$message .= '<div class="head error">'.__('Tranzakció sikertelen').'</div>';
						$backStatus = $backref->backStatusArray;	
						
						/**
						 * Your code here
						 */	
						$message .= '<div class="ft" style="color:red;">'.__('Kérjük, ellenőrizze a tranzakció során megadott adatok helyességét. Amennyiben minden adatot helyesen adott meg, a visszautasítás okának kivizsgálása kapcsán kérjük, szíveskedjen kapcsolatba lépni kártyakibocsátó bankjával.').'</div>';
					}

					/**
					 * Notification
					 */	
					$message .= '<div class="cth">Tranzakció információk</div>';  
					$message .= '<div class="in">';  
					$message .= '<div class="ft">'.__('Simple tranzakció azonosító').': <b class="d">'.$backStatus['PAYREFNO'].'</b></div>'; 
					$message .= '<div class="ft">'.__('Időpont').': <b class="d">'.$backStatus['BACKREF_DATE'].'</b></div>';
					$message .= '<div class="ft">'.__('Megrendelés azonosító').': <b class="d">'.$backStatus['REFNOEXT'].'</b></div>';
					if( false ):
					$message .= '<b><font color="red">Fejlesztési segítség, éles oldalon ne jelenjen meg!</font></b><br/>'; 
					$message .= 'STATUS: <b class="d">'.$backStatus['ORDER_STATUS'].'</b><br/>';	
					endif;
					$message .= '<a href="/order/'.$order['accessKey'].'" class="btn btn-sm btn-default" style="color:black;"><i class="fa fa-angle-left"></i> '.__('vissza a megrendelés összesítőhöz').'</a>';
					$message .= '</div>'; 
					
					$message .= '<div class="simple-info-footer"><a href="https://www.simple.hu/" target="_blank">Biztonságos fizetés Simple-lel.</a> <div style="float:right;"><img src="'.IMG.'simple_vedd_online_140.png"></div><div class="clr"></div></div></div>';
					/**
					 * Print generated message
					 */			
					$this->view->pay_msg = $message;
				break;
				case 'timeout':

					$order = $this->shop->getOrderData( $_REQUEST['order_ref'], 'azonosito' );
					$message = '<div class="simple-information simple-timout">';

					if (@$_REQUEST['redirect']==1) {
						$message .= '<div class="head"><span style="color:red;">'.__('Tranzakciót megszakította.').'</span></div>';
					} else {
						$message .= '<div class="head"><span style="color:red;">'.__('Tranzakciót időkorlátja lejárt.').'</span></div>';
					}

					$message .= '<div class="cth">'.__('Tranzakció adatok').':</div>';
					$message .= '<div class="in">';
					$message .= '<div>'.__('Időpont').': '.date('Y-m-d H:i:s', time()).'</div>';
					$message .= '<div>'.__('Megrendelés azonosító').': '.$_REQUEST['order_ref'].'</div>';
					$message .= '<div><br><a style="color:black;" href="/order/'.$order['accessKey'].'" class="btn btn-sm btn-default"><i class="fa fa-angle-left"></i> vissza a megrendelés adatlapjára</a></div>';
					
					$message .= '</div>';

					$message .= '<div class="simple-info-footer"><a href="https://www.simple.hu/" target="_blank">Biztonságos fizetés Simple-lel.</a> <div style="float:right;"><img src="'.IMG.'simple_vedd_online_140.png"></div><div class="clr"></div></div></div>';

					$this->view->pay_msg = $message;
				break;
				default:
					# code...
					break;
			}
		}

		/**
		 * CETELEM ÁRUHITEL API
		 * */
		public function cetelem()
		{
			// Cetelem API 
			$cetelem = (new Cetelem( $this->view->settings['cetelem_shopcode'], $this->view->settings['cetelem_society'], $this->view->settings['cetelem_barem'], array( 'db' => $this->db ) ))->sandboxMode( CETELEM_SANDBOX_MODE );

			// Log
			$this->db->insert(
				'gateway_cetelem_ipn',
				array(
					'megrendeles' => $this->view->gets[3],
					'statusz' => $this->view->gets[2],
					'datastr' => json_encode($_REQUEST)
				)
			);

			// Megrendelés adatok
			$this->view->order 		= $this->shop->getOrderData($this->view->gets[3]);

			switch ($this->view->gets[2]) 
			{
				// Tranzakció elkezdése
				case 'start':

					$this->view->szamlazas	= json_decode($this->view->order['szamlazasi_keys'], true);

					$name = explode(" ", $this->view->szamlazas['nev']);

					$data = array(
						'firstName' => $name[0],
						'lastName' 	=> $name[1],
						'pcode' 	=> $this->view->szamlazas[irsz],
						'city' 		=> $this->view->szamlazas[city],
						'address' 	=> $this->view->szamlazas[uhsz],
						'email'  	=> $this->view->order[email],
						'articleId' => 335,
					);

					$datalist = $cetelem->prepareDataJSON($this->view->gets[3], $data);

					$total = 0;

					if(is_array($this->view->order[items]))
					foreach ( $this->view->order[items] as $i ) 
					{
						$total += $i[subAr];
					}
					
					if($this->view->order['kedvezmeny'] > 0) {
						$total -= $this->view->order['kedvezmeny'];
					}

					$cetelem->startTransaction($total, $datalist);

				break;
			}
		
		}
		
		function __destruct(){
			// RENDER OUTPUT
				parent::bodyHead();					# HEADER
				$this->view->render(__CLASS__);		# CONTENT
				parent::__destruct();				# FOOTER
		}
	}

?>