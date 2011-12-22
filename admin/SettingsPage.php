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
						<h3 class="hndle"><span>Dependency Diagram (What is what...)</span></h3>
						<div class="inside" >
							<?php echo '<img src="'.WP_PLUGIN_URL.'/'.str_replace("admin","",plugin_basename(dirname(__FILE__))).'/diagram.png" width="871" height="434" />'; ?>
						</div>
					</div>
				</div>
			</div>
		</div>
		
		<div id="poststuff">
			<div id="post-body">
				<div id="post-body-content">
					<div class="stuffbox">
						<h3 class="hndle"><span>Installation Guide</span></h3>
						<div class="inside">
							<!-- BOX1 -->
							<div style="boxInfo">
								<div class="roundedBox">
									<div class="imgHolder"><?php echo '<img src="'.WP_PLUGIN_URL.'/'.str_replace("admin","",plugin_basename(dirname(__FILE__))).'/darkonyx_logo.png" width="105" height="74" />'; ?></div>
									<div class="guideContainer">
										<span class="guideTitle">1.Download DarkOnyx Player</span>
										<span class="guideDesc">
											<a href="http://web-anatomy.com/en/Register" target="_blank">Register</a> an account on web-anatomy.com and grab free DarkOnyx Player from 
											<a href="http://darkonyx.web-anatomy.com/en/Download" target="_blank">Download</a> page. Once you're ready, you will be able to download
											the player from <a href="http://web-anatomy.com/en/my_products" target="_blank">My Products - Download</a> page. Don't forget to register 
											domain for your player in the input box next to the download button (same as wordpress)
										</span>
									</div>
								</div>
								<div class="subMenu">
									<span class="subContext">
										Watch <a href="http://darkonyx.web-anatomy.com/en/Videos?vid=12" target="_blank">Video Tutorial</a> on how to download  and install the player or check this 
										<a href="http://web-anatomy.com/docs/darkonyx/darkonyx_guide_1_installation.pdf" target="_blank">PDF Guide!</a>
									</span>
								</div>
							</div>
							<div style="clear:both"></div><br/>
							<!-- BOX2 -->
							<div style="boxInfo">
								<div class="roundedBox">
									<div class="imgHolder"><?php echo '<img src="'.WP_PLUGIN_URL.'/'.str_replace("admin","",plugin_basename(dirname(__FILE__))).'/darkonyx_logo.png" width="105" height="74" />'; ?></div>
									<div class="guideContainer">
										<span class="guideTitle">2.Install DarkOnyx Player</span>
										<span class="guideDesc">
											Create new folder (e.g. darkonyx) on your ftp/domain and upload DarkOnyx files there. Then navigate to that directory with your
											browser. Player will create its database. The hole process looks very similar to how you install your Wordpress.
										</span>
									</div>
								</div>
								<div class="subMenu">
									<span class="subContext">
										Watch <a href="http://darkonyx.web-anatomy.com/en/Videos?vid=12" target="_blank">Video Tutorial</a> on how to download  and install the player or check this 
										<a href="http://web-anatomy.com/docs/darkonyx/darkonyx_guide_1_installation.pdf" target="_blank">PDF Guide!</a>
									</span>
								</div>
							</div>
							
							<div style="clear:both"></div><br/>
							<!-- BOX3 -->
							<div style="boxInfo">
								<div class="roundedBox">
									<div class="imgHolder"><?php echo '<img src="'.WP_PLUGIN_URL.'/'.str_replace("admin","",plugin_basename(dirname(__FILE__))).'/wp_logo.png" width="105" height="74" />'; ?></div>
									<div class="guideContainer">
										<span class="guideTitle">3. Configure DarkOnyx Plugin for Wordpress</span>
										<span class="guideDesc">
											Grab DarkOnyx Player Root Directory Path from DarkOnyx CMS (you will find it in settings tab) and then paste it to the input 
											above on this page. From now your Wordpress will be connected to your DarkOnyx Player
										</span>
									</div>
								</div>
								<div class="subMenu">
									<span class="subContext">
										Watch <a href="http://darkonyx.web-anatomy.com/en/Videos?vid=16" target="_blank">Video Tutorial</a> on how to configure DarkOnyx Plugin for Wordpress
									</span>
								</div>
							</div>
							
							<div style="clear:both"></div><br/>
							<!-- BOX4 -->
							<div style="boxInfo">
								<div class="roundedBox">
									<div class="imgHolder"><?php echo '<img src="'.WP_PLUGIN_URL.'/'.str_replace("admin","",plugin_basename(dirname(__FILE__))).'/plugin_logo.png" width="105" height="74" />'; ?></div>
									<div class="guideContainer">
										<span class="guideTitle">4. Start deploying videos to your website</span>
										<span class="guideDesc">
											Depending on your preferences you can either use Wordpress Media Library for managing your videos or DarkOnyx Video Database
											thanks to DarkOnyx Plugin
										</span>
									</div>
								</div>
								<div class="subMenu">
									<span class="subContext">
										Watch <a href="http://darkonyx.web-anatomy.com/en/Videos?vid=17" target="_blank">Video Tutorial</a> on how to manage video content with DarkOnyx Plugin for Wordpress
									</span>
								</div>
							</div>
							
							<div style="clear:both"></div>
						</div>
					
					</div>
				</div>
			</div>
		</div>	
		
		

	 <div id="poststuff">
              <div id="post-body">
                <div id="post-body-content">
                 <div class="stuffbox" style="float:left">
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
