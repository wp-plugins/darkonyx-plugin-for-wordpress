<?php
define("DARKONYX_KEY", "darkonyxmodule_");

class DarkOnyxFramework
{
	
	private static $dir = DARKONYX_PLUGIN_DIR;
	private static $url = DARKONYX_PLUGIN_URL;
	private static $current_config = "";
	private static $current_config_values;
	private static $div_id = 1;
	private static $loaded_flash_vars;
	private static $loaded_additional_flash_vars;
	private static $isGoodPatch = null;
	
	private static $player_version = '';
	private static $min_plugin_version = '';
  
	/**
	  * Finds DarkOnyx player path
	  * @return true if given Path is ok.
	  */
	public static function isGoodPath($path = ''){
		if(DarkOnyxFramework::$isGoodPatch != null)
			return DarkOnyxFramework::$isGoodPatch;

		if($path == '')
			$path = get_option(DARKONYX_KEY . "player_location");

		$foundEinterface = false;
		$foundConfig = false;
		if($handle = @opendir($path)) { 
			while (false !== ($file = readdir($handle))) {
				if ($file == "einterface.php") {
					$foundEinterface = true;
				}else if($file == "config.php"){
					$foundConfig = true;
				}
			}
			closedir($handle);
		}
		
		DarkOnyxFramework::$isGoodPatch = ($foundEinterface && $foundConfig);

		return ($foundEinterface && $foundConfig);
	}
	/**
	  * Chcecks installation status of DarkOnyx Player
	  * @return true if player is installed properly.
	  */
	public static function isPlayerInstalled(){
		return class_exists(MAIN_CONFIG);
	}
	
	public static function isGoodDarkOnyxVersion(){
		$myFile = get_option(DARKONYX_KEY . "player_location")."/version.txt";
		$fh = fopen($myFile, 'r');
		$theData = fread($fh, filesize($myFile));
		fclose($fh);
		$lines = explode("\n",$theData);
		DarkOnyxFramework::$player_version = $lines[0];
		if(isset($lines[1]))
			DarkOnyxFramework::$min_plugin_version = $lines[1];
		return DarkOnyxFramework::versionCompare(DarkOnyxFramework::$player_version, WA_MIN_DARKONYX_VERSION);
	}
	
	public static function isGoodPluginVersion(){
		if(DarkOnyxFramework::$min_plugin_version == '')
			return true;
		return DarkOnyxFramework::versionCompare(WA_CURR_PLUGIN_VERSION, DarkOnyxFramework::$min_plugin_version);
	}
	
	public static function logInToDarkOnyxCP(){
		require_once(get_option(DARKONYX_KEY . "player_location").'/panel/system/classes/Auth.class.php');
		$oAuth = new Auth();
		$oAuth->Login(MAIN_CONFIG::$admin_login, MAIN_CONFIG::$admin_pass, MAIN_CONFIG::$admin_pass);
	}

	public static function getControlPanelURL(){
		return 'http://'.MAIN_CONFIG::$domain.'/'.MAIN_CONFIG::$player_folder;
	}
	
	/**
	  * Comparison of versions
	  * @return true v1 is higher than v2.
	  */
	private static function versionCompare($v1, $v2){
		$da = explode('.',$v1);
		$pl = explode('.',$v2);
		if($pl[0] < $da[0]) 
			return true;
		else if($pl[0] > $da[0])
			return false;
		if($pl[1] < $da[1]) 
			return true;
		else if($pl[1] > $da[1])
			return false;
		if($pl[2] <= $da[2]) 
			return true;
		return false;
	}
	
}
?>