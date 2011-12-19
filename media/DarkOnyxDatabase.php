<?php

function getChannels(){
	$channels = array();
	$sql = "SELECT id, name FROM ".MAIN_CONFIG::$prefix."channels ORDER by id DESC";
	$result = mysql_query($sql, MAIN_CONFIG::$connect);
	while($rows=mysql_fetch_array($result)){
		$id = $rows['id'];
		$channels[$id] = $rows['name'];
	}
	return $channels;
}

function form_encode($string)
{
	$string = str_replace("\\\"", "\"", $string);
    $string = str_replace("\\'", "'", $string);
    $string = str_replace("\\\\", "\\", $string);
	return stripslashes($string);
}

function my_editor_content( $content ) {
	$content = "If you like this post, then please consider retweeting it or sharing it on Facebook.";
	return $content;
}

/**
 * @file This file contains the functions for rendering the External Media tab.
 * This is a combination of the default WordPress From Computer and URL Import
 * tabs.
 */

function media_darkonyx_db_insert_form() {
	global $redir_tab, $type;
	  
	$redir_tab = 'darkonyx_database';
	media_upload_header();
	  
	$post_id = intval($_REQUEST['post_id']);
	  
	$form_action_url = admin_url("media-upload.php?type=$type&tab=$redir_tab&post_id=$post_id");
    $form_action_url = apply_filters('media_upload_form_url', $form_action_url, $type);

	$add_url  = '';
	$orderInput = mysql_real_escape_string($_POST[DARKONYX_KEY.'_order']);
	$searchInput = mysql_real_escape_string($_POST[DARKONYX_KEY.'_search']);
	$tagInput = mysql_real_escape_string($_POST[DARKONYX_KEY.'_tag']);
	$publishedInput = mysql_real_escape_string($_POST[DARKONYX_KEY.'_published']);
	$channelInput = (int) $_POST[DARKONYX_KEY.'_channel'];
	
	if(empty($orderInput) || $orderInput == 'DESC'){
		$sort = 'DESC';
	}else{
		$sort = 'ASC';
	}

	$search = '';
	if(isset($_POST[DARKONYX_KEY.'_search'])){
		if($searchInput == 'Search...'){ $searchInput = '';}
		$search .= "WHERE (title LIKE '%$searchInput%' OR description LIKE '%$searchInput%') ";
	}else{
		$search .= "WHERE title LIKE '%%' ";
	}
	
	if(!empty($tagInput)){
		if($tagInput== 'Tag...'){ $tagInput = ''; }
		$search .= "AND tags LIKE '%$tagInput%' ";
	}
	
	if(isset($_POST[DARKONYX_KEY.'_published']) && $publishedInput != ''){
		$search .= "AND published=$publishedInput ";
	}

	if(!empty($channelInput)){
		$vchannelstable = ", ".MAIN_CONFIG::$prefix."channels_videos vchannels";
		$vchannelscols = ", vchannels.channel_id, vchannels.video_id";
		$search .= "AND channels LIKE '%,$channelInput,%'";
	}

    if(isset($_POST[DARKONYX_KEY."_videoID"]) && $_POST[DARKONYX_KEY."_videoID"] != ""){
		$output = "[DarkOnyx ";
		if ($_POST[DARKONYX_KEY . "player_style"] != "Default") {
			$output .= " style=\"" . $_POST[DARKONYX_KEY . "player_style"] . "\" ";
		}
		if ($_POST[DARKONYX_KEY . "player_campaign"] != "Default") {
			$output .= " campaign=\"" . $_POST[DARKONYX_KEY . "player_campaign"] . "\" ";
		}
		$output .= " width=\"640px\" ";
		$output .= " height=\"360px\" ";
		$output .= "id=\"".(int)$_POST[DARKONYX_KEY."_videoID"]."\"";
		$output .= " ]";
		media_send_to_editor($output);
   }

 $channels = getChannels();
  ?>
  <?php wp_enqueue_script("jquery"); ?>
  <?php echo '<link rel="stylesheet" href="'. WP_PLUGIN_URL . '/' . str_replace("media","",plugin_basename(dirname(__FILE__))) . 'css/tabs.css" type="text/css"/>'."\n"; ?>
  <div class="db-container">
  <?php wp_nonce_field('media-form'); ?>

  <form enctype="multipart/form-data" method="post" action="<?php echo esc_attr($form_action_url); ?>" class="media-upload-form type-form validate" id="<?php echo $type; ?>-form">
	 <input type="hidden" id="videoID" name="<?php echo DARKONYX_KEY;?>_videoID" value=""/>
	 <input type="hidden" name="post_id" id="post_id" value="<?php echo (int) $post_id; ?>" />
    <h3 class="media-title">DarkOnyx Database Videos <?php echo "<a href=\"".DarkOnyxFramework::getControlPanelURL()."/?p=videos\" class=\"button-secondary\" target=\"_blank\">DarkOnyx Video Management</a>";?></h3>

	<table class="widefat" style="display:table;margin-bottom:10px;border-top-color:none;">
	<tr class="db-table-main">
		<td>Player Settings</td>
		<td style="text-align:right;" id="db-settings-show"><a href="javascript:void(0)" onClick="changeSttingsVisibility();">Show</a></td>
	</tr>
	<tr id="db-settings-1" style="display:none;">
		<th valign="top" class="label" scope="row"><label for="attachments[<?php echo $post_id;?>][darkonyxmodule_player_style]"><span class="alignleft">Player Style</span><br class="clear"></label></th>
		<td class="field">
		<?php 
		$selectedStyle = '';
		if(isset($_POST[DARKONYX_KEY . "player_style"]))
			$selectedStyle = $_POST[DARKONYX_KEY . "player_style"];
		echo genStyleSelectorHTML($post_id, DARKONYX_KEY . "player_style", $selectedStyle);
		?>
		</td>
	</tr>
	<tr id="db-settings-2" style="display:none;">
		<th valign="top" class="label" scope="row"><label for="attachments[<?php echo $post_id;?>][darkonyxmodule_player_campaign]"><span class="alignleft">Player Campaign</span><br class="clear"></label></th>
		<td class="field">
		<?php 
		$selectedCampaign = '';
		if(isset($_POST[DARKONYX_KEY . "player_campaign"]))
			$selectedCampaign = $_POST[DARKONYX_KEY . "player_campaign"];
		echo genCampaignSelectorHTML($post_id, DARKONYX_KEY . "player_campaign", $selectedCampaign);
		?>
		</td>
	</tr>
	</table>
	<table class="widefat" style="display:table;margin-bottom:30px;border-top-color:none;">
	<tr class="db-table-main">
		<td>Filters</td>
		<td style="text-align:right;" id="db-filters-show"><a href="javascript:void(0)" onClick="changeFiltersVisibility();">Show</a></td>
	</tr>
	<tr id="db-filters-1" style="display:none;">
		<th valign="top" class="label" scope="row"><span class="alignleft">Search</span><br class="clear"></th>
		<td class="field">
		<input type="text" id="search" onkeyup="setFilter(true)" value="" style="width:400px;">
		</td>
	</tr>
	<tr id="db-filters-2" style="display:none;">
		<th valign="top" class="label" scope="row"><span class="alignleft">Tag</span><br class="clear"></th>
		<td class="field">
		<input type="text" id="tag" onkeyup="setFilter(true)" value="" style="width:400px;">
		</td>
	</tr>
	<tr id="db-filters-3" style="display:none;">
		<th valign="top" class="label" scope="row"><span class="alignleft">Channel</span><br class="clear"></th>
		<td class="field">
		<select id="channel" onchange="setFilter()" style="width:400px;">
		<option value="">All</option>
		<?php
		foreach($channels as $key => $value){
			$sel = '';
			echo '<option value="'.$key.'" '.$sel.'>'.$value.'</option>';
		}
		?>
		</select>
		</td>
	</tr>
	<tr id="db-filters-4" style="display:none;">
		<th valign="top" class="label" scope="row"><span class="alignleft">Order</span><br class="clear"></th>
		<td class="field">
		<select id="order" onchange="setFilter()" style="width:400px;">
		<option value="DESC">Newest</option>
		<option value="ASC">Oldest</option>
		</select>
		</td>
	</tr>
	<tr id="db-filters-5" style="display:none;">
		<th valign="top" class="label" scope="row"><span class="alignleft">Status</span><br class="clear"></th>
		<td class="field">
		<select id="published" onchange="setFilter()" style="width:400px;">
		<option value="">All</option>
		<option value="1">Published</option>
		<option value="0">Non-published</option>
		</select>
		</td>
	</tr>
	<tr id="db-filters-6" style="display:none;">
		<th valign="top" class="label" scope="row"><span class="alignleft">Rows per page</span><br class="clear"></th>
		<td class="field">
		<select id="max" onchange="setFilter()" style="width:400px;">
		<option>5</option>
		<option SELECTED>10</option>
		<option>25</option>
		<option>50</option>
		<option>100</option>
		</select>
		</td>
	</tr>
	</table>

	 <table class="widefat" id="videosContainer">
		 <tr class="db-table-main">
			<td class="db-table-thumb">Thumbnail</td>
			<td class="db-table-title">Title & Description</td>
			<td class="db-table-channels">Channels</td>
		 </tr>
	 </table>	
	 
	<div id="pages"></div>
	</div>
	</form>
	<script src="<?php echo 'http://'.MAIN_CONFIG::$domain.'/'.MAIN_CONFIG::$player_folder;?>/panel/pageiterator.js" type="text/javascript"></script>
	<script type="text/javascript">

	function submitVideo(id){
		jQuery('#videoID').val(id);
		jQuery('#<?php echo $type; ?>-form').submit();
	}
					
	jQuery(document).ready(function() {
		setFilterAction();
	});

	//videos
	var videosList = new Array();
	var max; //max videos per page
	var search;
	var tags;
	var channel;
	var order;
	var published;

	var _pageIterator = new PageIterator();
	_pageIterator.positionClass = 'video_pages_pos';
	_pageIterator.container = 'pages';
	_pageIterator.setPageFunction = 'setPage';
	_pageIterator.setPageFunction = 'setPage';
	_pageIterator.maxSidePages = 10;

	function setFilterAction(){
		_pageIterator.currentPage = 1;
		
		max = jQuery("#max").val();
		search = jQuery("#search").val();
		tags = jQuery("#tag").val();
		channel = jQuery("#channel").val();
		order = jQuery("#order").val();
		published = jQuery("#published").val();

		getVideosList();
	}


	function setPage(p){
		_pageIterator.setPage(p);
		getVideosList();
	}

	function unSerializeVideosArray(xml){
		xml = parseXml(xml);
		var models = new Array();
		var modelsNum = 0;
		jQuery(xml).find("video").each(function()
		{
			models[modelsNum] = {id:jQuery(this).find("id").text(),title:jQuery(this).find("title").text(),description:jQuery(this).find("description").text(),thumbnail:jQuery(this).find("thumbnail").text(),published:jQuery(this).find("published").text()};
			modelsNum++;
		});
		var pagesCount = jQuery(xml).find('pages').attr('count');
		_pageIterator.setPages(pagesCount);
		return models; 
	}

	function parseXml(xml)
	{	
		if (jQuery.browser.msie)
		{
			var xmlDoc = new ActiveXObject("Microsoft.XMLDOM"); 
			xmlDoc.loadXML(xml);
			xml = xmlDoc;
		}
		
		return xml;
	}

	function getVideosList(){
		jQuery.post("<?php echo 'http://'.MAIN_CONFIG::$domain.'/'.MAIN_CONFIG::$player_folder;?>/panel/system/ajax/Videos.ajax.php", {action:"getVideosList", max: max, page:_pageIterator.currentPage, search: search, tags: tags, channel: channel, order: order, published: published},
			function(data) {
				renderVideosList(data);
			}
		);
	}

	function renderVideosList(data){
		jQuery(".vpos").remove();
		var videos = unSerializeVideosArray(data);
		videosList = videos;
		for(var i=0;i<videos.length;i++){
			var img = '';
			var notpubstyle = '';
			var notpubtext = '';
			if(videos[i].published == 0){
				notpubstyle = 'red"';
				notpubtext = ' - Not published';
			}

			if(videos[i].thumbnail != '' && videos[i].thumbnail != 'http://')
				img = '<img src="'+videos[i].thumbnail+'" width="160" height="90">';
				var pubstyle = '';
				var pubtext = '';
				if(videos[i].published == 0){
					pubstyle = 'color:red;';
					pubtext = '- Not published';
				}
				var code = '<tr class="vpos">';
						code += '<td class="db-table-thumb">'+img+'</td>';
						code += '<td class="db-table-title">';
							code += '<span style="font-weight:bold;'+pubstyle+'">'+videos[i].title+pubtext+'</span><br>';
							code += '<span style="color:#707070;">'+videos[i].description+'</span><br>';
							code += '<input type="button" onclick="submitVideo('+videos[i].id+');" class="button-primary" value="<?php echo esc_attr__( 'Insert DarkOnyx Player' ); ?>" />';
						code += '</td>';
						code += '<td class="db-table-channels" id="channels_'+videos[i].id+'">-</td>';
					 code += '</tr>';
				jQuery("#videosContainer").last().append(code);

			
		}
		downloadChannelData();
	};

	function downloadChannelData(){
		var vstring = '';
		for(var i=0;i<videosList.length;i++){
			vstring += videosList[i].id+',';
		}
		jQuery.post("<?php echo 'http://'.MAIN_CONFIG::$domain.'/'.MAIN_CONFIG::$player_folder;?>/panel/system/ajax/Videos.ajax.php", {action:"getChannelData", videosString:vstring},
			function(data) {
				renderChannelData(data);
			}
		);
	};

	function renderChannelData(xml){
		xml = parseXml(xml);
		jQuery(xml).find("video").each(function()
		{
			var obj = document.getElementById('channels_'+jQuery(this).attr('id'));
			if(typeof obj != "undefined"){
				obj.innerHTML = '';
				jQuery(this).find("channel").each(function()
				{
					obj.innerHTML += '<a href="<?php echo 'http://'.MAIN_CONFIG::$domain.'/'.MAIN_CONFIG::$player_folder;?>/index.php?p=editchannel&playlistID='+jQuery(this).attr('playlistID')+'&id='+jQuery(this).attr('id')+'" target="_blank">'+jQuery(this).attr('name')+'</a>, ';
				});
				if(obj.innerHTML == '') 
					obj.innerHTML = '-';
				else{
					var strLen = obj.innerHTML.length;
					obj.innerHTML = obj.innerHTML.slice(0,strLen-2);
				}
			}
		});
	};
	 
	var filterTimer = false;
	function setFilter(delay){
		if(filterTimer) clearTimeout(filterTimer);
		if(delay)
			filterTimer = setTimeout(function(){setFilterAction();},1000);
		else
			setFilterAction();
	}

	
	var dbSettingsBloocked = false;
	function changeSttingsVisibility(){
		var show = '<a href="javascript:void(0)" onClick="changeSttingsVisibility();">Show</a>';
		var hide = '<a href="javascript:void(0)" onClick="changeSttingsVisibility();">Hide</a>';
		if(!dbSettingsBloocked){
			jQuery('#db-settings-1').show("slow");
			jQuery('#db-settings-2').show("slow");
			jQuery('#db-settings-show').html(hide);
			dbSettingsBloocked = true;
		}else{
			jQuery('#db-settings-1').hide("slow");
			jQuery('#db-settings-2').hide("slow");
			jQuery('#db-settings-show').html(show);
			dbSettingsBloocked = false;
		}
	}
	
	var dbFiltersBloocked = false;
	function changeFiltersVisibility(){
		var show = '<a href="javascript:void(0)" onClick="changeFiltersVisibility();">Show</a>';
		var hide = '<a href="javascript:void(0)" onClick="changeFiltersVisibility();">Hide</a>';
		if(!dbFiltersBloocked){
			jQuery('#db-filters-1').show("slow");
			jQuery('#db-filters-2').show("slow");
			jQuery('#db-filters-3').show("slow");
			jQuery('#db-filters-4').show("slow");
			jQuery('#db-filters-5').show("slow");
			jQuery('#db-filters-6').show("slow");
			jQuery('#db-filters-show').html(hide);
			dbFiltersBloocked = true;
		}else{
			jQuery('#db-filters-1').hide("slow");
			jQuery('#db-filters-2').hide("slow");
			jQuery('#db-filters-3').hide("slow");
			jQuery('#db-filters-4').hide("slow");
			jQuery('#db-filters-5').hide("slow");
			jQuery('#db-filters-6').hide("slow");
			jQuery('#db-filters-show').html(show);
			dbFiltersBloocked = false;
		}
	}
	
	<?php
	if(isset($_POST['post-query-submit'])){
		echo 'jQuery(document).ready(changeFiltersVisibility);'."\r\n";
		
		if($_POST[DARKONYX_KEY . "player_style"] != "Default" || $_POST[DARKONYX_KEY . "player_campaign"] != "Default")
			echo 'jQuery(document).ready(changeSttingsVisibility);';
	}
	?>
	</script>
  <?php
}
?>
