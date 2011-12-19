<?php
/*
Plugin Name: DarkOnyx Plugin for WordPress
Plugin URI: http://darkonyx.web-anatomy.com/en
Description: Embed your video content with DarkOnyx hybrid Flash/HTML5 player into your WordPress articles.
Version: 1.0.0
Author: Web-Anatomy s.c.
Author URI: http://darkonyx.web-anatomy.com/en

Copyright 2011  Web-Anatomy s.c.  (email : contact@web-anatomy.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
 
global $wp_version;

define("WA_CUR_PLUGIN_VERSION", "1.0.0");
define("WA_MIN_DARKONYX_VERSION", "2.1.0");
define("WA_PLAYER_GA_VARS", "?utm_source=WordPress&utm_medium=Product&utm_campaign=WordPress");
define("WA_NOT_INSTALLED", 'DarkOnyx Video Plugin for Wordpress has not been properly installed.');
define("WA_FILE_PERMISSIONS", 'For tips on how to make sure this folder is writable please refer to <a href="http://codex.wordpress.org/Changing_File_Permissions">http://codex.wordpress.org/Changing_File_Permissions</a>.');

// Check for WP2.7 installation
if (!defined ('IS_WP27')) {
  define('IS_WP27', version_compare($wp_version, '2.7', '>=') );
}

// This works only in WP2.7 or higher
if (IS_WP27 == FALSE) {
  add_action('admin_notices', create_function('', 'echo \'<div id="message" class="error fade"><p><strong>' . __('Sorry, the DarkOnyx Plugin for WordPress works only under WordPress 2.7 or higher.') . '</strong></p></div>\';'));
  return;
}

// The plugin is only compatible with PHP 5.0 or higher
if (version_compare(phpversion(), "5.0", '<')) {
  add_action('admin_notices', create_function('', 'echo \'<div id="message" class="error fade"><p><strong>' . __('Sorry, the DarkOnyx Plugin for WordPress only works with PHP Version 5 or higher.') . '</strong></p></div>\';'));
  return;
}
//Define the plugin directory and url for file access.
$uploads = wp_upload_dir();
if (isset($uploads["error"]) && !empty($uploads["error"])) {
  add_action('admin_notices', create_function('', 'echo \'<div id="message" class="fade updated"><p><strong>There was a problem completing activation of the DarkOnyx Player Plugin for WordPress.  Please note that the DarkOnyx Plugin for WordPress requires that the WordPress uploads directory exists and is writable.  ' . WA_FILE_PERMISSIONS . '</strong></p></div>\';'));
  return;
}
$pluginURL = $isHttps ? str_replace("http://", "https://", WP_PLUGIN_URL) : WP_PLUGIN_URL;
$uploadsURL = $isHttps ? str_replace("http://", "https://", $uploads["baseurl"]) : $uploads["baseurl"];
define("DARKONYX_PLUGIN_DIR", WP_PLUGIN_DIR . "/" . plugin_basename(dirname(__FILE__)));
define("DARKONYX_PLUGIN_URL", $pluginURL . "/" . plugin_basename(dirname(__FILE__)));


//Include core plugin files.
include_once (dirname (__FILE__) . "/framework/DarkOnyxFramework.php");
if(DarkOnyxFramework::isGoodPath())
	include_once(get_option(DARKONYX_KEY . "player_location").'/config.php');
include_once (dirname (__FILE__) . "/media/DarkOnyxMediaFunctions.php");
include_once (dirname (__FILE__) . "/media/DarkOnyxShortcode.php");

add_filter("the_content", "darkonyx_tag_callback", 11);


register_deactivation_hook(__FILE__, "darkonyx_deactivation");
add_action('init', 'darkonyx_init');

function darkonyx_deactivation() {
  delete_option(DARKONYX_KEY . "uninstalled");
}

function darkonyx_init() {
	clearstatcache();

	//searching DarkOnyx Player dir
	if(!get_option(DARKONYX_KEY . "player_location")){
		add_action('admin_notices', create_function('', 'echo \'<div id="message" class="fade updated"><p><strong>' . __('No DarkOnyx Root Directory Path has been assigned with this plugin. Please check DarkOnyx Plugin Settings!') . '</strong></p></div>\';'));
	}else if(!DarkOnyxFramework::isGoodPath()){
		add_action('admin_notices', create_function('', 'echo \'<div id="message" class="fade updated"><p><strong>' . __('DarkOnyx Player has not been found in the following directory: "'.get_option(DARKONYX_KEY . "player_location").'", or it has not been fully installed yet!') . '</strong></p></div>\';'));
	}else if(!DarkOnyxFramework::isPlayerInstalled()){
		add_action('admin_notices', create_function('', 'echo \'<div id="message" class="fade updated"><p><strong>' . __('DarkOnyx Player in the following directory'.get_option(DARKONYX_KEY . "player_location").' has not been properly installed!') . '</strong></p></div>\';'));
	}else if(!DarkOnyxFramework::isGoodDarkOnyxVersion()){
		add_action('admin_notices', create_function('', 'echo \'<div id="message" class="fade updated"><p><strong>' . __('Minimum DarkOnyx Player version compatibile with this plugin is: '.WA_MIN_DARKONYX_VERSION.'') . '</strong></p></div>\';'));
	}else if(!DarkOnyxFramework::isGoodPluginVersion()){
		add_action('admin_notices', create_function('', 'echo \'<div id="message" class="fade updated"><p><strong>' . __('A newer version of this plugin is required to work with your DarkOnyx player') . '</strong></p></div>\';'));
	}
  
}

//ADMIN
//Player configuration and Media Management, limited to administrators.
if(is_admin()){
	if(DarkOnyxFramework::isPlayerInstalled())
		DarkOnyxFramework::logInToDarkOnyxCP();
	add_action('admin_menu', 'darkonyx_plugin_menu');
}


function darkonyx_plugin_menu(){
	$admin = add_menu_page("Web-Anatomy DarkOnyx player", "DarkOnyx", "administrator", "darkonyx", "darkonyx_plugin_pages", DARKONYX_PLUGIN_URL . "/darkonyxlogo.png");
}

 
// Entry point to the Player configuration wizard.
function darkonyx_plugin_pages() {
  switch ($_GET["page"]) {
    case "darkonyx":
		require_once (dirname(__FILE__) . "/admin/SettingsPage.php");
		break;
	default:
		break;
  }
}

?>