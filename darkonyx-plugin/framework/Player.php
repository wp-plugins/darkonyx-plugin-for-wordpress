<?php
/**
 * A convenience object for storing information about a Player.
 * @file Class definition for Player
 */
 
class Player{

	private $width = 640;
	private $height = 360;
	private $campaign = "";
	private $style = "";
	private $mediaID = "";
	private $darkonyxID = "";
	private $path;
	
	public function __construct(){
		if(DarkOnyxFramework::isGoodPath())
			$this->path = 'http://'.MAIN_CONFIG::$domain.'/'.MAIN_CONFIG::$player_folder;
	}
	
	public function setWidth($width){
		$this->width = str_replace('px','',$width);
	}
	
	public function setHeight($height){
		$this->height = str_replace('px','',$height);
	}
	
	public function setStyle($style){
		if($style != "Default")
			$this->style = $style;
	}
	
	public function setCampaign($campaign){
		if($campaign != "Default")
			$this->campaign = $campaign;
	}
	
	public function setMediaID($id){
		$this->mediaID = $id;
	}
	
	public function setDarkOnyxID($id){
		$this->darkonyxID = $id;
	}
	
	private function getVideoObjCode(){
		$post = get_post($this->mediaID);

		$post_date = explode(" ",$post->post_date);
		$date = explode('-',$post_date[0]);
		$time = explode(':',$post_date[1]);
		$unixdate = mktime($time[0], $time[1], $time[2], $date[1], $date[2], $date[0]);

		$thumbnail_url = get_post_meta($this->mediaID, DARKONYX_KEY . "poster_url", true);
		$thumbnail_id = get_post_meta($this->mediaID, DARKONYX_KEY . "poster", true);
		$html5_url = get_post_meta($this->mediaID, DARKONYX_KEY . "html5_url", true);
		$html5_id = get_post_meta($this->mediaID, DARKONYX_KEY . "html5", true);
	
		
		$output = '<script language="javascript">';
				$output .= 'var playerWidth = '.$this->width.';'."\r\n";
				$output .= 'var playerHeight = '.$this->height.';'."\r\n";
				if($this->campaign != "")
					$output .= 'var campaignID = '.$this->campaign.';'."\r\n";
				if($this->style != "")
					$output .= 'var styleName = "'.$this->style.'";'."\r\n";
				$output .= 'var videoObject = {'."\r\n";
				$output .= 'title: "'.$this->escapeString($post->post_title).'",'."\r\n"; 
				$output .= 'description: "'.$this->escapeString($post->post_content).'",'."\r\n"; 
				$output .= 'author: "'.get_post_meta($this->mediaID, DARKONYX_KEY . "creator", true).'",'."\r\n"; 
				$output .= 'added: "'.$unixdate.'",'."\r\n";  
				if(get_post_meta($this->mediaID, DARKONYX_KEY . "duration", true) != "")
					$output .= 'duration: '.get_post_meta($this->mediaID, DARKONYX_KEY . "duration", true).','."\r\n"; 
				if(get_post_meta($this->mediaID, DARKONYX_KEY . "minage", true) != "")
					$output .= 'minAge:'.get_post_meta($this->mediaID, DARKONYX_KEY . "minage", true).','."\r\n"; 
				if($thumbnail_url != "" && $thumbnail_url  != -1){
					$output .= 'poster: "'.$thumbnail_url.'",'."\r\n";
					$output .= 'thumbnail: "'.$thumbnail_url.'",'."\r\n";
				}else if($thumbnail_id != "" && $thumbnail_id  != -1){
					$image_attachment = get_post($thumbnail_id);
					$image = isset($image_attachment) ? $image_attachment->guid : "";
					$output .= 'poster: "'.$image.'",'."\r\n";
					$output .= 'thumbnail: "'.$image.'",'."\r\n";
				}
				$output .= 'link: "'.get_bloginfo('url').'",'."\r\n"; 
				if(get_post_meta($this->mediaID, DARKONYX_KEY . "captions", true) != ""){
					$output .= 'captions:['."\r\n"; 
						$output .= '{lang: "English",url: "'.get_post_meta($this->mediaID, DARKONYX_KEY . "captions", true).'"}'."\r\n"; 
					$output .= '],'."\r\n"; 
				}
				$output .= 'sources:['."\r\n"; 
					$output .= '{type: "sd", method:"'.$this->getVideoMethod(get_post_meta($this->mediaID, "_wp_attached_file", true)).'",url: "'.$this->getCheckURL(get_post_meta($this->mediaID, "_wp_attached_file", true)).'"},'."\r\n"; 
					if($html5_url != "" && $html5_url != -1){
						$output .= '{type: "sd", method:"'.$this->getVideoMethod($html5_url).'",url: "'.$this->getCheckURL($html5_url).'"},'."\r\n"; 
					}
					if($html5_id != "" && $html5_id != -1){
						$html5_attachment = get_post($html5_id);
						$html5 = $html5_attachment->guid;
						$output .= '{type: "sd", method:"'.$this->getVideoMethod($html5).'",url: "'.$this->getCheckURL($html5).'"},'."\r\n"; 
					}
				$output .= ']'."\r\n"; 
				$output .= '}'."\r\n"; 
			$output .= '</script><script type="text/javascript" src="'.$this->path.'/einterface.php"></script>';
		return $output;
	} 
	
	private function escapeString($string){
		$string = str_replace("\n", " ", $string);
		$string = str_replace("\r", " ", $string);
		$string = str_replace('"', '\"', $string);
		return $string;
	}
	
	private function getDarkOnyxDBCode(){
		$output = '<script language="javascript">'."\r\n";
		$output .= 'var videoID = '.$this->darkonyxID.';'."\r\n";
		$output .= 'var playerWidth = '.$this->width.';'."\r\n";
		$output .= 'var playerHeight = '.$this->height.';'."\r\n";
		if($this->campaign != "")
			$output .= 'var campaignID = '.$this->campaign.';'."\r\n";
		if($this->style != "")
			$output .= 'var styleName = "'.$this->style.'";'."\r\n";
		$output .= '</script><script type="text/javascript" src="'.$this->path.'/einterface.php"></script>';
		return $output;
	}
	
	private function getVideoMethod($url){
		if(strpos($url, 'youtube.com') !== false)
			return 'YOUTUBE';
		if(strpos($url, 'dailymotion.com') !== false)
			return 'DAILYMOTION';
		if(strpos($url, 'rtmp:') !== false)
			return 'RTMP';
		return 'HTTP';
	}
	
	private function getCheckURL($url){
		if(strpos($url, 'http') !== false || strpos($url, 'rtmp') !== false)
			return $url;
		$upload = wp_upload_dir();
			return $upload['baseurl'].'/'.$url;
	}
	
	public function getCode(){
		if(!DarkOnyxFramework::isGoodPath())
			return WA_NOT_INSTALLED;
		if($this->darkonyxID != "")
			return $this->getDarkOnyxDBCode();
		if($this->mediaID != "")
			return $this->getVideoObjCode();
		return "DarkOnyx - Video ID Error";
	}
	
}
?>