<?php

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); }

function error_message($message) { 
    return '<div class="error fade" id="message">
      <p><strong>'.$message.'</strong></p>
    </div>';
}

if (isset($_POST['Uninstall'])) {
  uninstall();
} else {
	if(isset($_POST["main_folder_location"])){
		update_option(DARKONYX_KEY . "player_location", $_POST["main_folder_location"]);
		if(!DarkOnyxFramework::isGoodPath($_POST["main_folder_location"]))
			echo error_message('DarkOnyx Root Directory Path appears to be invalid, or the player has not been properly installed!');
		else
			echo '<script type="text/javascript">window.location.reload();</script>';
	}
}

function uninstall() {
  global $wpdb;

  $option_query = "DELETE FROM $wpdb->options WHERE option_name LIKE '" . DARKONYX_KEY . "%';";

  $wpdb->query($option_query);

  update_option(DARKONYX_KEY . "uninstalled", true);
  feedback_message(__('Tables and settings deleted, deactivate the plugin now'));
}

function feedback_message ($message, $timeout = 0) { ?>
  <div class="fade updated" id="message" onclick="this.parentNode.removeChild (this)">
    <p><strong><?php echo $message ?></strong></p>
  </div> <?php
}

echo '<link rel="stylesheet" href="'. WP_PLUGIN_URL . '/' . str_replace("admin","",plugin_basename(dirname(__FILE__))) . 'css/settings.css" type="text/css"/>'."\n"; 
?>
<div class="wrap">
      <h2>DarkOnyx Player Setup</h2>
        <form name="<?php echo DARKONYX_KEY . "setLocationForm" ?>" method="post" action="">
            <div id="poststuff">
              <div id="post-body">
                <div id="post-body-content">
                  <div class="stuffbox">
                    <h3 class="hndle"><span>DarkOnyx Player Location</span></h3>
                    <div class="inside" style="margin: 15px;">
                    <label for="main_folder_location">DarkOnyx Root Directory:</label>
                    <input id="main_folder_location" type="text" style="width:300px;<?php if(!DarkOnyxFramework::isGoodPath()) echo 'border:2px solid red;';?>" name="main_folder_location" value="<?php if(get_option(DARKONYX_KEY . "player_location")) echo get_option(DARKONYX_KEY . "player_location"); else echo $_SERVER['DOCUMENT_ROOT'].'/'?>" />
					<input class="button-secondary action" type="submit" name="save" value="Save changes"/>
					<br/>
					<?php if(!DarkOnyxFramework::isGoodPath()){?>
						<span class="description">Player must be installed on the same web-server as Wordpress, example: "<?php echo $_SERVER['DOCUMENT_ROOT'];?>/darkonyx"</span>
						<br><br>
						<p>Don't have a player yet? Download DarkOnyx from <a href="http://darkonyx.web-anatomy.com/en/Download" target="_blank">here!</a></p>
					<?php }else{ ?>
						<br>
						<a href="<?php echo DarkOnyxFramework::getControlPanelURL();?>" class="button-primary" target="_blank">Move to DarkOnyx - Control Panel</a>
					<?php } ?>
					</div>
                </div>
              </div>
            </div>
          </div>
        </form>
     <div id="poststuff">
              <div id="post-body">
                <div id="post-body-content">
                  <div class="stuffbox">
                    <h3 class="hndle"><span>Setup instruction</span></h3>
					<div id="steps">
					<div>
						<a href="http://darkonyx.web-anatomy.com/en/Download" target="_blank">
							<div class="step">
								<div class="number">1</div>
								<div class="text">Download Player<br><span style="color:#ffcf91;font-size:12px;">From here!</span></div>
							</div>
						</a>
							<div class="separator"></div>
					</div>
					<div>
						<a href="http://darkonyx.web-anatomy.com/en/Videos?vid=12" target="_blank">
							<div class="step">
								<div class="number">2</div>
								<div class="text">Install Player<br><span style="color:#ffcf91;font-size:12px;">Watch Video Tutorial!</span></div>
							</div>
						</a>
							<div class="separator"></div>
					</div>
					<div>
					<a href="http://darkonyx.web-anatomy.com/en/Videos?vid=16" target="_blank">
						<div class="step">
							<div class="number">3</div>
							<div class="text">Configure Plugin<br><span style="color:#ffcf91;font-size:12px;">Watch Video Tutorial!</span></div>
						</div>
					</a>
						<div class="separator"></div>
					</div>
					<a href="http://darkonyx.web-anatomy.com/en/Videos?vid=17" target="_blank">
						<div class="step">
							<div class="number">4</div>
							<div class="text">How To Use?<br><span style="color:#ffcf91;font-size:12px;">Watch Video Tutorial!</span></div>
						</div>
					</a>
					</div>
                    <div class="inside" style="margin: 15px;">
					 <p>In short:</p>
					 <p>
					 1. Download and install DarkOnyx Web Video Player on your web-server (<a href="http://darkonyx.web-anatomy.com/en/Download" target="_blank">you can grab player here</a>).
					 <br>2. Grab DarkOnyx absolute directory path from Main Settings (DarkOnyx Control Panel) 
					 <br>3. Paste DarkOnyx Directory Path to the input above (DarkOnyx Player Location)
					 </p>
		  
					</div>
                </div>
              </div>
            </div>
	</div>	
	 <div id="poststuff">
              <div id="post-body">
                <div id="post-body-content">
                  <div class="stuffbox">
                    <h3 class="hndle"><span>Licensing</span></h3>
                    <div class="inside" style="margin: 15px;">
                     <p>This Wordpress Plugin was designed to work with all commercial and non-commercial editions of DarkOnyx Web Video Player. Feel free to grab your player from our site.</p> <p>If you are running a <strong>commercial site</strong> (displaying ads, selling stuff), please make sure to grab a proper license. With a commercial edition you will be able to modify watermark, and site info in the context menu. Web-Anatomy also provides free tech support to all its clients.</p><p>Schools and other non-profit organizations can use the Free Edition without these restrictions. DarkOnyx Free Edition is also a great opportunity to check the product before buying it first.</p>
		  <a href="http://darkonyx.web-anatomy.com/en/Download" class="button-primary" target="_blank">Purchase a License</a>
					</div>
                </div>
              </div>
            </div>
	</div>	  

</div>
