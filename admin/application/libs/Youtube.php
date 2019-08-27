<? 
class Youtube{
	static $video_width = 725;
	static $video_height = 408;
	
	static function videoData($yid){
		$url = "http://gdata.youtube.com/feeds/api/videos/" . $yid;
		if($this->video = @simplexml_load_file($url)){}else{ echo 'HIBA: Nincs ilyen video!';}
	}
	
	static function getVideoId($url){
		$pos = strpos($url,'v=');
		return substr($url,$pos+2,11);
	}
	static function emberCode($str){
		$vw = (is_numeric(self::$video_width)) ? self::$video_width : 725;
		$vh = (is_numeric(self::$video_height)) ? self::$video_height : 408;
		
		preg_match_all("#((http://|https://)?(www.)?youtube\.com/watch\?[=a-z0-9&_;]+)#i",$str,$m);
		
		$vURL = $m[0];
				
		$vid = array();
		foreach($vURL as $link){
			$str = str_replace(
			"[yt]".$link."[/yt]",
			'<div style="padding:10px;"><center><iframe width="'.$vw.'" height="'.$vh.'" src="http://www.youtube.com/embed/'.self::getVideoId($link).'?rel=0" frameborder="0" allowfullscreen></iframe></center></div>',
			$str);
		}
		
		return $str;
	}
	
	static function ember($txt){
		$txt = self::emberCode($txt);
		return $txt;
	}
	
	static function pushVar($var,$val){
		self::$var = $val;
	}
}
?>