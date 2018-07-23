<?php
namespace ResourceImporter;

/* 6.10 */

class CashmanFxApi {

	private $serv;
	private $szla;
	private $szlapdf;
	private $sztmp;
	private $etmp;

	public $szamla;
	public $szamla_hely;
	public $szamlaid;
	public $szamla_id;
	public $link;
	public $tmppdf;
	public $szamlatetelekTomb;
	public $szamlalistaTomb;
	public $keszlet_listaTomb;
	public $termekkeszletTomb;
	public $partner_id;
	public $partnerTomb;
	public $termekTomb;
	public $uzenet;
	public $hiba;


	function __construct($serv, $szla, $szlapdf, $sztmp, $etmp) {
		$this->serv = $serv;
		$this->szla = $szla;
		$this->szlapdf = $szlapdf;
		$this->sztmp = $sztmp;
		$this->etmp = $etmp;
		$this->hiba = '';
	}

	//új számla
	public function uj_szamla($tomb) {
		$this->szamla_id = '';
		$this->szamla = '';
		$this->szamla_hely = '';
		$this->szamlaid =  '';
		$this->link = '';

		$pn = substr(md5(rand()),0,7);
		$ch = curl_init ($this->serv);
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-type: multipart/form-data"));
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

		$tomb[0]['muveletkod']='1';
		if(!isset($tomb[0]['peldany']) || $tomb[0]['peldany']=='0') {
			$tomb[0]['peldany'] = '1';
		}
		curl_setopt($ch, CURLOPT_POSTFIELDS,  array("post" => serialize($tomb)));
		if ($res = curl_exec ($ch)) {
			if(substr($res,0,5)!="HIBA:") {
				$this->hiba = '';
				$slices = explode('|', $res);
				$pn =  str_replace('/', '-', $slices[2]) . "-" . $pn;
				if(isset($tomb[0]['noflash']) && $tomb[0]['noflash']=='1') {
					if(isset($tomb[0]['nodisplay']) && $tomb[0]['nodisplay']=='1') {
						$nodisplay = '1';
					} else {
						$nodisplay = '0';
					}
					if(isset($tomb[0]['email_cim']) && $tomb[0]['email_cim']!='') {
						$this->link = $this->szlapdf . "szamfej_id=" . $slices[0] . "&lang=" . $slices[4] . "&db=" . $slices[1] . "&csakeredeti=" . $slices[3] . "&nodisplay=" . $nodisplay . "&email=" . $tomb[0]['email_cim'] . "&peldany=" . $tomb[0]['peldany'] . "&szdesign=" . $slices[8] . "&strparam=" . $slices[9] . "&api=1" . "&pn=" . $pn  . "&eszamla=" . $slices[10];
					} else {
						$this->link = $this->szlapdf . "szamfej_id=" . $slices[0] . "&lang=" . $slices[4] . "&db=" . $slices[1] . "&csakeredeti=" . $slices[3] . "&nodisplay=" . $nodisplay . "&peldany=" . $tomb[0]['peldany'] . "&szdesign=" . $slices[8] . "&api=1" . "&pn=" . $pn . "&eszamla=" . $slices[10];
					}
					$this->szamla_hely = $this->szlapdf . "szamfej_id=" . $slices[0] . "&lang=" . $slices[4] . "&db=" . $slices[1] . "&csakeredeti=" . $slices[3] . "&nodisplay=" . '0' . "&peldany=" . $tomb[0]['peldany'] . "&szdesign=" . $slices[8] . "&api=1" . "&pn=" . $pn . "&eszamla=" . $slices[10];

					if($slices[10]!='1') {
						$this->tmppdf = $this->sztmp . "/" . $tomb[0]['Csoport'] . "/" . $pn . ".pdf";
					} else {
						$this->tmppdf = $this->etmp . "/" . $tomb[0]['Csoport'] . "/E-" . $pn . ".pdf";
					}
				} else {
					$this->link = $this->szla . "szamfej_id=" . $slices[0] . "&lang=" . $slices[4] . "&db=" . $slices[1] . "&csakeredeti=" . $slices[3];
					$this->szamla_hely = "";
				}
				if($slices[7]!='1') {
					$this->szamla_id = $slices[0];
					$this->szamla = $slices[2];
					$this->szamla_hely = '';
					$this->szamlaid =  $slices[6];
					//echo $this->szamla;
				} else {
					$this->szamla_id = $slices[0];
					$this->szamla = $slices[2];
					$this->szamlaid =  $slices[6];
					//echo "<a href=" . $this->link . "target='_blank'>" . $this->szamla . "</a>";
				}

				return true;
			} else {
				$this->hiba = $res;
				//echo $this->hiba;
				return false;
			}
			curl_close($ch);
		} else {
			$this->hiba = 'Curl hiba: ' . curl_error($ch);
			//echo $this->hiba;
			return false;
		}
	}

	//számla rögzítés
	public function uj_szamla_szamlaszam_nelkul($tomb) {
		$ch = curl_init($this->serv);
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-type: multipart/form-data"));
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		$tomb[0]['muveletkod']='4';
		curl_setopt($ch, CURLOPT_POSTFIELDS,  array("post" => serialize($tomb)));
		if ($res = curl_exec ($ch)) {
			if(substr($res,0,5)!="HIBA:") {
				$this->hiba = '';
				return true;
			} else {
				$this->hiba = $res;
				//echo $this->hiba;
				return false;
			}
			curl_close($ch);
		} else {
			$this->hiba = 'Curl hiba: ' . curl_error($ch);
			//echo $this->hiba;
			return false;
		}
	}

	//számla másolat
	public function szamla_ujranyomtatas($tomb) {
		$pn = substr(md5(rand()),0,7);
		$ch = curl_init ($this->serv);
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-type: multipart/form-data"));
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		$tomb[0]['muveletkod']='2';
		if(!isset($tomb[0]['peldany']) || $tomb[0]['peldany']=='0') {
			$tomb[0]['peldany'] = '1';
		}
		curl_setopt($ch, CURLOPT_POSTFIELDS,  array("post" => serialize($tomb)));
		if ($res = curl_exec ($ch)) {
			if(substr($res,0,5)!="HIBA:") {
				$this->hiba = '';
				$slices = explode('|', $res);
				$pn =  str_replace('/', '-', $slices[2]) . "-" . $pn;
				if(isset($tomb[0]['noflash']) && $tomb[0]['noflash']=='1') {
					if(isset($tomb[0]['nodisplay']) && $tomb[0]['nodisplay']=='1') {
						$nodisplay = '1';
					} else {
						$nodisplay = '0';
					}

					if(isset($tomb[0]['email_cim']) && $tomb[0]['email_cim']!='') {
						$this->link = $this->szlapdf . "szamfej_id=" . $slices[0] . "&lang=" . $slices[4] . "&db=" . $slices[1] . "&csakeredeti=" . $slices[3] . "&nodisplay=" . $nodisplay . "&email=" . $tomb[0]['email_cim'] . "&peldany=" . $tomb[0]['peldany'] . "&szdesign=" . $slices[6] . "&strparam=" . $slices[7] . "&pn=" . $pn;
					} else {
						$this->link = $this->szlapdf . "szamfej_id=" . $slices[0] . "&lang=" . $slices[4] . "&db=" . $slices[1] . "&csakeredeti=" . $slices[3] . "&nodisplay=" . $nodisplay . "&peldany=" . $tomb[0]['peldany'] . "&szdesign=" . $slices[6] . "&pn=" . $pn;
					}
					$this->szamla_hely = $this->szlapdf . "szamfej_id=" . $slices[0] . "&lang=" . $slices[4] . "&db=" . $slices[1] . "&csakeredeti=" . $slices[3] . "&nodisplay=" . '0' . "&peldany=" . $tomb[0]['peldany'] . "&szdesign=" . $slices[6] . "&pn=" . $pn;

					if($slices[10]!='1') {
						$this->tmppdf = $this->sztmp . "/" . $tomb[0]['Csoport'] . "/" . $pn . ".pdf";
					} else {
						$this->tmppdf = $this->etmp . "/" . $tomb[0]['Csoport'] . "/E-" . $pn . ".pdf";
					}
				} else {
					$this->link = $this->szla . "szamfej_id=" . $slices[0] . "&lang=" . $slices[4] . "&db=" . $slices[1] . "&csakeredeti=" . $slices[3];
					$this->szamla_hely = '';
				}

				$this->szamla = $slices[2];
				//echo "<a href=$this->link target='_blank'>$this->szamla</a>";


				return true;
			} else {
				$this->hiba = $res;
				//echo $this->hiba;
				return false;
			}
			curl_close($ch);
		} else {
			$this->hiba = 'Curl hiba: ' . curl_error($ch);
			//echo $this->hiba;
			return false;
		}
	}

	//e-számla
	public function e_szamla_keszites($tomb) {
		$ch = curl_init ($this->serv);
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-type: multipart/form-data"));
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

		$tomb[0]['muveletkod']='16';

		curl_setopt($ch, CURLOPT_POSTFIELDS,  array("post" => serialize($tomb)));
		if ($res = curl_exec ($ch)) {
			if(substr($res,0,5)!="HIBA:") {
				$this->hiba = '';
				$this->uzenet = $res;
				//echo $res;
				return true;
			} else {
				$this->hiba = $res;
				//echo $this->hiba;
				return false;
			}
			curl_close($ch);
		} else {
			$this->hiba = 'Curl hiba: ' . curl_error($ch);
			//echo $this->hiba;
			return false;
		}
	}

	//számla stornó
	public function szamla_storno($tomb) {
		$pn = substr(md5(rand()),0,7);
		$ch = curl_init ($this->serv);
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-type: multipart/form-data"));
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

		$tomb[0]['muveletkod']='3';
		if(!isset($tomb[0]['peldany']) || $tomb[0]['peldany']=='0') {
			$tomb[0]['peldany'] = '1';
		}

		curl_setopt($ch, CURLOPT_POSTFIELDS,  array("post" => serialize($tomb)));
		if ($res = curl_exec ($ch)) {
			if(substr($res,0,5)!="HIBA:") {
				$this->hiba = '';
				$slices = explode('|', $res);
				$pn =  str_replace('/', '-', $slices[2]) . "-" . $pn;
				if(isset($tomb[0]['noflash']) && $tomb[0]['noflash']=='1') {
					if(isset($tomb[0]['nodisplay']) && $tomb[0]['nodisplay']=='1') {
						$nodisplay = '1';
					} else {
						$nodisplay = '0';
					}

					if(isset($tomb[0]['email_cim']) && $tomb[0]['email_cim']!='') {
						$this->link = $this->szlapdf . "szamfej_id=" . $slices[0] . "&lang=" . $slices[4] . "&db=" . $slices[1] . "&csakeredeti=" . $slices[3] . "&nodisplay=" . $nodisplay . "&email=" . $tomb[0]['email_cim'] . "&peldany=" . $tomb[0]['peldany'] . "&szdesign=" . $slices[7] . "&strparam=" . $slices[8] . "&api=1" . "&pn=" . $pn;
					} else {
						$this->link = $this->szlapdf . "szamfej_id=" . $slices[0] . "&lang=" . $slices[4] . "&db=" . $slices[1] . "&csakeredeti=" . $slices[3] . "&nodisplay=" . $nodisplay . "&peldany=" . $tomb[0]['peldany'] . "&szdesign=" . $slices[7] . "&api=1" . "&pn=" . $pn;
					}
					$this->szamla_hely = $this->szlapdf . "szamfej_id=" . $slices[0] . "&lang=" . $slices[4] . "&db=" . $slices[1] . "&csakeredeti=" . $slices[3] . "&nodisplay=" . '0' . "&peldany=" . $tomb[0]['peldany'] . "&szdesign=" . $slices[6] . "&pn=" . $pn;

					if($slices[10]!='1') {
						$this->tmppdf = $this->sztmp . "/" . $tomb[0]['Csoport'] . "/" . $pn . ".pdf";
					} else {
						$this->tmppdf = $this->etmp . "/" . $tomb[0]['Csoport'] . "/E-" . $pn . ".pdf";
					}

				} else {
					$this->link = $this->szla . "szamfej_id=" . $slices[0] . "&lang=" . $slices[4] . "&db=" . $slices[1] . "&csakeredeti=" . $slices[3];
				}

				$this->szamla = $slices[2];
				//echo "<a href=$this->link target='_blank'>$this->szamla</a>";


				return true;
			} else {
				$this->hiba = $res;
				//echo $this->hiba;
				return false;
			}
			curl_close($ch);
		} else {
			$this->hiba = 'Curl hiba: ' . curl_error($ch);
			//echo $this->hiba;
			return false;
		}
	}

	//számla tételek
	public function szamla_tetel($tomb) {
		$ch = curl_init ($this->serv);
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-type: multipart/form-data"));
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

		$tomb[0]['muveletkod']='17';
		curl_setopt($ch, CURLOPT_POSTFIELDS,  array("post" => serialize($tomb)));
		if ($res = curl_exec ($ch)) {
			if(substr($res,0,5)!="HIBA:") {
				$this->hiba = '';
				$this->szamlatetelekTomb = unserialize($res);
				return true;
			} else {
				$this->hiba = $res;
				//echo $this->hiba;
				return false;
			}
			curl_close($ch);
		} else {
			$this->hiba = 'Curl hiba: ' . curl_error($ch);
			//echo $this->hiba;
			return false;
		}
	}

	//számlalista
	public function szamla_lista($tomb) {
		$ch = curl_init ($this->serv);
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-type: multipart/form-data"));
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

		$tomb[0]['muveletkod']='18';
		curl_setopt($ch, CURLOPT_POSTFIELDS,  array("post" => serialize($tomb)));
		if ($res = curl_exec ($ch)) {
			if(substr($res,0,5)!="HIBA:") {
				$this->hiba = '';
				$this->szamlalistaTomb = unserialize($res);
				return true;
			} else {
				$this->hiba = $res;
				//echo $this->hiba;
				return false;
			}
			curl_close($ch);
		} else {
			$this->hiba = 'Curl hiba: ' . curl_error($ch);
			//echo $this->hiba;
			return false;
		}
	}

	//pénztár
	function penztar_rogzit($tomb) {
		$ch = curl_init ($this->serv);
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-type: multipart/form-data"));
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

		$tomb[0]['muveletkod']='15';
		curl_setopt($ch, CURLOPT_POSTFIELDS,  array("post" => serialize($tomb)));
		if ($res = curl_exec ($ch)) {
			if(substr($res,0,5)!="HIBA:") {
				$this->hiba = '';
				$this->uzenet = $res;
				//echo $res;
				return true;
			} else {
				$this->hiba = $res;
				//echo $this->hiba;
				return false;
			}
			curl_close($ch);
		} else {
			$this->hiba = 'Curl hiba: ' . curl_error($ch);
			//echo $this->hiba;
			return false;
		}
	}


	public function termekrogzites($tomb) {
		$ch = curl_init ($this->serv);
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-type: multipart/form-data"));
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

		$tomb[0]['muveletkod']='8';
		curl_setopt($ch, CURLOPT_POSTFIELDS,  array("post" => serialize($tomb)));
		if ($res = curl_exec ($ch)) {
			if(substr($res,0,5)!="HIBA:") {
				$this->hiba = '';
				$slices = explode('|', $res);
				$this->uzenet = $res;
				//echo $res;
				return true;
			} else {
				$this->hiba = $res;
				//echo $this->hiba;
				return false;
			}
			curl_close($ch);
		} else {
			$this->hiba = 'Curl hiba: ' . curl_error($ch);
			//echo $this->hiba;
			return false;
		}
	}

	//készletlista
	public function keszlet_lista($tomb) {
		$ch = curl_init ($this->serv);
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-type: multipart/form-data"));
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

		$tomb[0]['muveletkod']='19';
		curl_setopt($ch, CURLOPT_POSTFIELDS,  array("post" => serialize($tomb)));
		if ($res = curl_exec ($ch)) {
			if(substr($res,0,5)!="HIBA:") {
				$this->hiba = '';
				$this->keszlet_listaTomb = unserialize($res);
				return true;
			} else {
				$this->hiba = $res;
				//echo $this->hiba;
				return false;
			}
			curl_close($ch);
		} else {
			$this->hiba = 'Curl hiba: ' . curl_error($ch);
			//echo $this->hiba;
			return false;
		}
	}

	//termék készlet
	public function termek_keszlet($tomb) {
		$ch = curl_init ($this->serv);
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-type: multipart/form-data"));
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

		$tomb[0]['muveletkod']='5';
		curl_setopt($ch, CURLOPT_POSTFIELDS,  array("post" => serialize($tomb)));
		if ($res = curl_exec ($ch)) {
			if(substr($res,0,5)!="HIBA:") {
				$this->hiba = '';
				$this->termekkeszletTomb = explode('|', $res);
				return true;
			} else {
				$this->hiba = $res;
				//echo $this->hiba;
				return false;
			}
			curl_close($ch);
		} else {
			$this->hiba = 'Curl hiba: ' . curl_error($ch);
			//echo $this->hiba;
			return false;
		}
	}

	//termék készlet összes, PRO+
	public function termek_keszlet_osszes($tomb) {
		$ch = curl_init ($this->serv);
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-type: multipart/form-data"));
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

		$tomb[0]['muveletkod']='14';
		curl_setopt($ch, CURLOPT_POSTFIELDS,  array("post" => serialize($tomb)));
		if ($res = curl_exec ($ch)) {
			if(substr($res,0,5)!="HIBA:") {
				$this->hiba = '';
				$this->termekkeszletTomb = explode('|', $res);
				return true;
			} else {
				$this->hiba = $res;
				//echo $this->hiba;
				return false;
			}
			curl_close($ch);
		} else {
			$this->hiba = 'Curl hiba: ' . curl_error($ch);
			//echo $this->hiba;
			return false;
		}
	}


	//számla kiegyenlítés
	public function szamla_kiegyenlites($tomb) {
		$ch = curl_init ($this->serv);
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-type: multipart/form-data"));
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

		$tomb[0]['muveletkod']='6';
		curl_setopt($ch, CURLOPT_POSTFIELDS,  array("post" => serialize($tomb)));
		if ($res = curl_exec ($ch)) {
			if(substr($res,0,5)!="HIBA:") {
				$this->hiba = '';
				$this->uzenet = "Számla kiegyenlítve";
				$slices = explode('|', $res);
				return true;
			} else {
				$this->hiba = $res;
				//echo $this->hiba;
				return false;
			}
			curl_close($ch);
		} else {
			$this->hiba = 'Curl hiba: ' . curl_error($ch);
			//echo $this->hiba;
			return false;
		}
	}


	//partner kedvezmény
	function partner_kedvezmeny($tomb) {
		$ch = curl_init ($this->serv);
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-type: multipart/form-data"));
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

		$tomb[0]['muveletkod']='7';
		curl_setopt($ch, CURLOPT_POSTFIELDS,  array("post" => serialize($tomb)));

		if ($res = curl_exec ($ch)) {
			if(substr($res,0,5)!="HIBA:") {
				$this->hiba = '';
				$this->uzenet = $res;
				$slices = explode('|', $res);
				//echo $res;
				return true;
			} else {
				$this->hiba = $res;
				//echo $this->hiba;
				return false;
			}
			curl_close($ch);
		} else {
			$this->hiba = 'Curl hiba: ' . curl_error($ch);
			//echo $this->hiba;
			return false;
		}
	}


	//partner rögzítés
	public function partnerrogzites($tomb) {
		$ch = curl_init ($this->serv);
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-type: multipart/form-data"));
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

		$tomb[0]['muveletkod']='9';
		curl_setopt($ch, CURLOPT_POSTFIELDS,  array("post" => serialize($tomb)));
		if ($res = curl_exec ($ch)) {
			if(substr($res,0,5)!="HIBA:") {
				$this->hiba = '';
				$this->partner_id = explode('|', $res);
				$slices = explode('|', $res);
				return true;
			} else {
				$this->hiba = $res;
				//echo $this->hiba;
				return false;
			}
			curl_close($ch);
		} else {
			$this->hiba = 'Curl hiba: ' . curl_error($ch);
			//echo $this->hiba;
			return false;
		}
	}


	//partner adatok
	public function partneradatok($tomb) {
		$ch = curl_init ($this->serv);
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-type: multipart/form-data"));
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

		$tomb[0]['muveletkod']='13';
		curl_setopt($ch, CURLOPT_POSTFIELDS,  array("post" => serialize($tomb)));
		if ($res = curl_exec ($ch)) {
			if(substr($res,0,5)!="HIBA:") {
				$this->hiba = '';
				$this->partnerTomb = explode('|', $res);
				return true;
			} else {
				$this->hiba = $res;
				//echo $this->hiba;
				return false;
			}
			curl_close($ch);
		} else {
			$this->hiba = 'Curl hiba: ' . curl_error($ch);
			//echo $this->hiba;
			return false;
		}
	}


	//partnerkártya
	public function partner_kartya($tomb) {
		$ch = curl_init ($this->serv);
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-type: multipart/form-data"));
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

		$tomb[0]['muveletkod']='10';
		curl_setopt($ch, CURLOPT_POSTFIELDS,  array("post" => serialize($tomb)));

		if ($res = curl_exec ($ch)) {
			if(substr($res,0,5)!="HIBA:") {
				$this->hiba = '';
				$this->uzenet = $res;
				$slices = explode('|', $res);
				//echo $res;
				return true;
			} else {
				$this->hiba = $res;
				//echo $this->hiba;
				return false;
			}
			curl_close($ch);
		} else {
			$this->hiba = 'Curl hiba: ' . curl_error($ch);
			//echo $this->hiba;
			return false;
		}
	}


	//proformából számla
	public function proforma($tomb) {
		$pn = substr(md5(rand()),0,7);
		$ch = curl_init ($this->serv);
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-type: multipart/form-data"));
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

		$tomb[0]['muveletkod']='11';
		if(!isset($tomb[0]['peldany']) || $tomb[0]['peldany']=='0') {
			$tomb[0]['peldany'] = '1';
		}
		curl_setopt($ch, CURLOPT_POSTFIELDS,  array("post" => serialize($tomb)));
		if ($res = curl_exec ($ch)) {
			if(substr($res,0,5)!="HIBA:") {
				$this->hiba = '';
				$slices = explode('|', $res);
				$pn =  str_replace('/', '-', $slices[2]) . "-" . $pn;
				if(isset($tomb[0]['noflash']) && $tomb[0]['noflash']=='1') {
					if(isset($tomb[0]['nodisplay']) && $tomb[0]['nodisplay']=='1') {
						$nodisplay = '1';
					} else {
						$nodisplay = '0';
					}

					if(isset($tomb[0]['email_cim']) && $tomb[0]['email_cim']!='') {
						$this->link = $this->szlapdf . "szamfej_id=" . $slices[0] . "&lang=" . $slices[4] . "&db=" . $slices[1] . "&csakeredeti=" . $slices[3] . "&nodisplay=" . $nodisplay . "&email=" . $tomb[0]['email_cim'] . "&peldany=" . $tomb[0]['peldany'] . "&szdesign=" . $slices[6] . "&strparam=" . $slices[7] . "&api=1" . "&pn=" . $pn;
					} else {
						$this->link = $this->szlapdf . "szamfej_id=" . $slices[0] . "&lang=" . $slices[4] . "&db=" . $slices[1] . "&csakeredeti=" . $slices[3] . "&nodisplay=" . $nodisplay . "&peldany=" . $tomb[0]['peldany'] . "&szdesign=" . $slices[6] . "&api=1" . "&pn=" . $pn;
					}
					$this->szamla_hely = $this->szlapdf . "szamfej_id=" . $slices[0] . "&lang=" . $slices[4] . "&db=" . $slices[1] . "&csakeredeti=" . $slices[3] . "&nodisplay=" . '0' . "&peldany=" . $tomb[0]['peldany'] . "&szdesign=" . $slices[6] . "&api=1" . "&pn=" . $pn;

					if($slices[10]!='1') {
						$this->tmppdf = $this->sztmp . "/" . $tomb[0]['Csoport'] . "/" . $pn . ".pdf";
					} else {
						$this->tmppdf = $this->etmp . "/" . $tomb[0]['Csoport'] . "/E-" . $pn . ".pdf";
					}
				} else {
					$this->link = $this->szla . "szamfej_id=" . $slices[0] . "&lang=" . $slices[4] . "&db=" . $slices[1] . "&csakeredeti=" . $slices[3];
					$this->szamla_hely = '';
				}

				$this->szamla = $slices[2];
				//echo "<a href=$this->link target='_blank'>$this->szamla</a>";
				return true;
			} else {
				$this->hiba = $res;
				//echo $this->hiba;
				return false;
			}
			curl_close($ch);
		} else {
			$this->hiba = 'Curl hiba: ' . curl_error($ch);
			//echo $this->hiba;
			return false;
		}
	}


	//proformából e-számla
	public function e_proforma($tomb) {
		$pn = substr(md5(rand()),0,7);
		$ch = curl_init ($this->serv);
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-type: multipart/form-data"));
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

		$tomb[0]['muveletkod']='20';
		if(!isset($tomb[0]['peldany']) || $tomb[0]['peldany']=='0') {
			$tomb[0]['peldany'] = '1';
		}
		curl_setopt($ch, CURLOPT_POSTFIELDS,  array("post" => serialize($tomb)));
		if ($res = curl_exec ($ch)) {
			if(substr($res,0,5)!="HIBA:") {
				$this->hiba = '';
				$slices = explode('|', $res);
				$pn =  str_replace('/', '-', $slices[2]) . "-" . $pn;

				if(isset($tomb[0]['nodisplay']) && $tomb[0]['nodisplay']=='1') {
					$nodisplay = '1';
				} else {
					$nodisplay = '0';
				}

				$this->link = $this->szlapdf . "szamfej_id=" . $slices[0] . "&lang=" . $slices[4] . "&db=" . $slices[1] . "&csakeredeti=" . $slices[3] . "&nodisplay=" . $nodisplay . "&email=" . $tomb[0]['email_cim'] . "&peldany=" . $tomb[0]['peldany'] . "&szdesign=" . $slices[6] . "&strparam=" . $slices[7] . "&api=1" . "&pn=" . $pn . "&eszamla=1";

				$this->szamla_hely = $this->szlapdf . "szamfej_id=" . $slices[0] . "&lang=" . $slices[4] . "&db=" . $slices[1] . "&csakeredeti=" . $slices[3] . "&nodisplay=" . '0' . "&peldany=" . $tomb[0]['peldany'] . "&szdesign=" . $slices[6] . "&api=1" . "&pn=" . $pn . "&eszamla=1";

				$this->tmppdf = $this->etmp . "/" . $tomb[0]['Csoport'] . "/E-" . $pn . ".pdf";

				$this->szamla = $slices[2];
				return true;
			} else {
				$this->hiba = $res;
				return false;
			}
			curl_close($ch);
		} else {
			$this->hiba = 'Curl hiba: ' . curl_error($ch);
			return false;
		}
	}

	//termék adatok
	public function termekadatok($tomb) {
		$ch = curl_init ($this->serv);
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-type: multipart/form-data"));
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

		$tomb[0]['muveletkod']='21';
		curl_setopt($ch, CURLOPT_POSTFIELDS,  array("post" => serialize($tomb)));
		if ($res = curl_exec ($ch)) {
			if(substr($res,0,5)!="HIBA:") {
				$this->hiba = '';
				$this->termekTomb = explode('|', $res);
				return true;
			} else {
				$this->hiba = $res;
				return false;
			}
			curl_close($ch);
		} else {
			$this->hiba = 'Curl hiba: ' . curl_error($ch);
			return false;
		}
	}

}
?>
