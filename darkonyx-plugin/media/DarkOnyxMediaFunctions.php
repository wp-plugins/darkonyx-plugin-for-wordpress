<?php
// Filter hook for specifying additional fields to appear when editing
// attachments.
add_filter("attachment_fields_to_edit", "darkonyx_attachment_fields", 10, 2);

/**
 * Handler function for displaying custom fields.
 * @param array $form_fields The fields to appear on the attachment form.
 * @param array $post Object representing the post we are saving to.
 * @return array Updated $form_fields with the new fields.
 */
function darkonyx_attachment_fields($form_fields, $post) {
  $image_args = array(
    "post_type" => "attachment",
    "numberposts" => 50,
    "post_status" => null,
    "post_mime_type" => "image",
    "post_parent" => null
  );
  $image_attachments = get_posts($image_args);
  $video_args = array(
    "post_type" => "attachment",
    "numberposts" => 25,
    "post_status" => null,
    "post_mime_type" => "video",
    "post_parent" => null
  );
  $video_attachments = get_posts($video_args);
  $mime_type = substr($post->post_mime_type, 0, 5);
  switch($mime_type) {
    case "image":
      break;
    case "audio":
    case "video":
	 $form_fields[DARKONYX_KEY . "html5_url"] = array(
        "label" => __("HTML5 file URL"),
        "input" => "html",
        "value" => get_post_meta($post->ID, DARKONYX_KEY . "html5_url", true)
      );
	  $form_fields[DARKONYX_KEY . "html5"] = array(
        "label" => __("HTML5 file"),
        "input" => "html",
        "html" => genFileSelectorHTML($post->ID, $video_attachments, 'html5')
      );
	  $form_fields[DARKONYX_KEY . "poster_url"] = array(
        "label" => __("Poster URL"),
        "input" => "html",
        "value" => get_post_meta($post->ID, DARKONYX_KEY . "poster_url", true)
      );
	  $form_fields[DARKONYX_KEY . "poster"] = array(
        "label" => __("Poster"),
        "input" => "html",
        "html" => genImageSelectorHTML($post->ID, $image_attachments, 'poster')
      );
      $form_fields[DARKONYX_KEY . "creator"] = array(
        "label" => __("Creator"),
        "input" => "text",
        "value" => get_post_meta($post->ID, DARKONYX_KEY . "creator", true)
      );
      $form_fields[DARKONYX_KEY . "duration"] = array(
        "label" => __("Duration (seconds)"),
        "input" => "text",
        "value" => get_post_meta($post->ID, DARKONYX_KEY . "duration", true)
      );
	  $form_fields[DARKONYX_KEY . "minage"] = array(
        "label" => __("Minimum Age (years)"),
        "input" => "text",
        "value" => get_post_meta($post->ID, DARKONYX_KEY . "minage", true)
      );
	  $form_fields[DARKONYX_KEY . "captions"] = array(
        "label" => __("Captions URL (.srt)"),
        "input" => "text",
        "value" => get_post_meta($post->ID, DARKONYX_KEY . "captions", true)
      );
      break;
  }
  $rtmp = get_post_meta($post->ID, DARKONYX_KEY . "rtmp");
  if ($mime_type == "video" && isset($rtmp) && $rtmp) {
    unset($form_fields["url"]);
    $form_fields[DARKONYX_KEY . "streamer"] = array(
        "label" => __("Streamer"),
        "input" => "text",
        "value" => get_post_meta($post->ID, DARKONYX_KEY . "streamer", true)
    );
    $form_fields[DARKONYX_KEY . "file"] = array(
        "label" => __("File"),
        "input" => "text",
        "value" => get_post_meta($post->ID, DARKONYX_KEY . "file", true)
    );
    $form_fields[DARKONYX_KEY . "provider"] = array(
        "label" => __("Provider"),
        "input" => "text",
        "value" => get_post_meta($post->ID, DARKONYX_KEY . "provider", true)
    );
  }

  if ($mime_type == "video" || $mime_type == "audio") {
    $insert = "<input type='submit' class='button-primary' name='send[$post->ID]' value='" . esc_attr__( 'Insert DarkOnyx Player' ) . "' />";
	$form_fields[DARKONYX_KEY . "player_style"] = array(
      "label" => __("Player Style"),
      "input" => "html",
      "html" => genStyleSelectorHTML($post->ID)
    );
	$form_fields[DARKONYX_KEY . "player_campaign"] = array(
      "label" => __("Player Ad-Campaign"),
      "input" => "html",
      "html" => genCampaignSelectorHTML($post->ID)
    );
    $form_fields["DarkOnyx"] = array("tr" => "\t\t<tr class='submit'><td></td><td class='savesend'>$insert</td></tr>\n");
  }
  return $form_fields;
}

/**
 * gens the HTML for HTML5 file selector.
 * @param int $id The id of the current attachment.
 * @return string The HTML to render the image selector.
 */
function genFileSelectorHTML($id, $attachments, $inputname) {
$output = "";
  $sel = false;
  if ($attachments) {
    $output .= "<select name='attachments[$id][" . DARKONYX_KEY . "$inputname]' id='imageselector$id' width='200' style='width:200px;'>\n";
    $output .= "<option value='-1' title='None'>None</option>\n";
    $file_id = get_post_meta($id, DARKONYX_KEY . $inputname, true);
    $file_url = get_post_meta($id, DARKONYX_KEY . $inputname, true);
    foreach($attachments as $post) {
      if (substr($post->post_mime_type, 0, 5) == "video") {
        if ($post->ID == $file_id) {
          $selected = "selected='selected'";
          $sel = true;
        } else {
          $selected = "";
        }
        $output .= "<option value='" . $post->ID . "' title='" . $post->guid . "' " . $selected . ">" . $post->post_title . "</option>\n";
      }
    }
    if (!$sel && isset($image_post) && isset($file_id) && $file_id != -1 && isset($file_url) && !$file_url) {
      $image_post = get_post($file_id);
      $output .= "<option value='" . $image_post->ID . "' title='" . $image_post->guid . "' selected=selected >" . $image_post->post_title . "</option>\n";
    }
    $output .= "</select>\n";
  }

  return $output;
}

/**
 * gens the HTML for rendering the thumbnail image selector.
 * @param int $id The id of the current attachment.
 * @return string The HTML to render the image selector.
 */
function genImageSelectorHTML($id, $attachments, $inputname) {
$output = "";
  $sel = false;
  if ($attachments) {
    $output .= "<select name='attachments[$id][" . DARKONYX_KEY . "$inputname]' id='imageselector$id' width='200' style='width:200px;'>\n";
    $output .= "<option value='-1' title='None'>None</option>\n";
    $image_id = get_post_meta($id, DARKONYX_KEY . $inputname, true);
    $thumbnail_url = get_post_meta($id, DARKONYX_KEY . $inputname, true);
    foreach($attachments as $post) {
      if (substr($post->post_mime_type, 0, 5) == "image") {
        if ($post->ID == $image_id) {
          $selected = "selected='selected'";
          $sel = true;
        } else {
          $selected = "";
        }
        $output .= "<option value='" . $post->ID . "' title='" . $post->guid . "' " . $selected . ">" . $post->post_title . "</option>\n";
      }
    }
    if (!$sel && isset($image_post) && isset($image_id) && $image_id != -1 && isset($thumbnail_url) && !$thumbnail_url) {
      $image_post = get_post($image_id);
      $output .= "<option value='" . $image_post->ID . "' title='" . $image_post->guid . "' selected=selected >" . $image_post->post_title . "</option>\n";
    }
    $output .= "</select>\n";
  }

  return $output;
}

/**
 * gens the combobox of available players.
 * @param int $id The attachment id.
 * @return string The HTML to render the player selector.
 */
function genStyleSelectorHTML($id, $name = "", $selectedValue = "") {
  if($name == "")
	$name = "attachments[$id][" . DARKONYX_KEY . "player_style]";

  $style_select = "<select name='".$name."' style=\"width:200px;\" id='" . DARKONYX_KEY . "player_style_" . $id . "'>\n";
  $style_select .= "<option value='Default'>Default</option>\n";
  $styleList = array();
  $dir = opendir(get_option(DARKONYX_KEY . "player_location").'/players/interfaces');
  while($file = readdir($dir)){
    if($file != '.' && $file != '..'){ 
		$f = str_replace('.xml','',$file);
		$f = str_replace('darkonyx_','',$f); 
		$sel = "";
		if($selectedValue == $f)
			$sel = "SELECTED";
		$style_select .= "<option ".$sel.">".$f."</option>";
	}
  }		
  $style_select .= "</select> <a href=\"".DarkOnyxFramework::getControlPanelURL()."/?p=settings&p2=styles\" class=\"button-secondary\" target=\"_blank\">DarkOnyx Style Management</a>\n";
  return $style_select;
}

function genCampaignSelectorHTML($id, $name = "", $selectedValue = "") { 
  if($name == "")
	$name = "attachments[$id][" . DARKONYX_KEY . "player_campaign]";

  $campaign_select = "<select name='".$name."' style=\"width:200px;\" id='" . DARKONYX_KEY . "player_campaign_" . $id . "'>\n";
  $campaign_select .= "<option value='Default'>Default</option>\n";
  $sql = "SELECT id, name FROM ".MAIN_CONFIG::$prefix."campaigns WHERE active=1 ORDER BY id DESC";
  $result = mysql_query($sql);
  while($rows = mysql_fetch_array($result)){
	$sel = "";
	if($selectedValue == $rows['id'])
		$sel = "SELECTED";
	$campaign_select .= "<option value='" . $rows['id'] . "' ".$sel.">" . $rows['name'] . "</option>\n";				
    
  }
  $campaign_select .= "</select>  <a href=\"".DarkOnyxFramework::getControlPanelURL()."/?p=campaigns\" class=\"button-secondary\" target=\"_blank\">DarkOnyx Campaign Management</a>\n";
  return $campaign_select;
}


// Filter hook for modifying the text inserted into the post body.
add_filter("media_send_to_editor", "darkonyx_tag_to_editor", 11, 3);

function darkonyx_tag_to_editor($html, $send_id, $attachment) {
  if ($_POST["send"][$send_id] == "Insert DarkOnyx Player") {
    $output = "[DarkOnyx ";
	
    if ($attachment[DARKONYX_KEY . "player_style"] != "Default") {
		$output .= " style=\"" . $attachment[DARKONYX_KEY . "player_style"] . "\"";
	}
	if ($attachment[DARKONYX_KEY . "player_campaign"] != "Default") {
		$output .= " campaign=\"" . $attachment[DARKONYX_KEY . "player_campaign"] . "\"";
	}
	$output .= " width=\"640px\" ";
	$output .= " height=\"360px\" ";
    $output .= "mediaid=\"" . $send_id . "\"]";
    update_post_meta($_GET["post_id"], DARKONYX_KEY . "fb_headers_id", $send_id);
    return $output;
  }
  return $html;
}

// Filter hook for specifying which custom fields are save.
add_filter("attachment_fields_to_save", "darkonyx_attachment_fields_to_save", 10, 2);

/**
 * Handler function for saving custom fields.
 * @param array $post Array representing the post we are saving.
 * @param array $attachment Array representing the attachment fields being
 * saved.
 * @return array $post updated with the attachment fields to be saved.
 */
function darkonyx_attachment_fields_to_save($post, $attachment) {
  $mime_type = substr($post["post_mime_type"], 0, 5);
  $rtmp = get_post_meta($post["ID"], DARKONYX_KEY . "rtmp");
  if ($mime_type == "video" && isset($rtmp)) {
    update_post_meta($post["ID"], DARKONYX_KEY . "streamer", isset($attachment[DARKONYX_KEY . "streamer"]) ? $attachment[DARKONYX_KEY . "streamer"] : "");
    update_post_meta($post["ID"], DARKONYX_KEY . "file", isset($attachment[DARKONYX_KEY . "file"]) ? $attachment[DARKONYX_KEY . "file"] : "");
    update_post_meta($post["ID"], DARKONYX_KEY . "provider", isset($attachment[DARKONYX_KEY . "provider"]) ? $attachment[DARKONYX_KEY . "provider"] : "");
  }
  if ($mime_type == "video" || $mime_type == "audio") {
	update_post_meta($post["ID"], DARKONYX_KEY . "poster_url", $attachment[DARKONYX_KEY . "poster_url"]);
	update_post_meta($post["ID"], DARKONYX_KEY . "poster", $attachment[DARKONYX_KEY . "poster"]);
	update_post_meta($post["ID"], DARKONYX_KEY . "html5_url", $attachment[DARKONYX_KEY . "html5_url"]);
	update_post_meta($post["ID"], DARKONYX_KEY . "html5", $attachment[DARKONYX_KEY . "html5"]);
    update_post_meta($post["ID"], DARKONYX_KEY . "creator", $attachment[DARKONYX_KEY . "creator"]);
    update_post_meta($post["ID"], DARKONYX_KEY . "duration", $attachment[DARKONYX_KEY . "duration"]);
	update_post_meta($post["ID"], DARKONYX_KEY . "captions", $attachment[DARKONYX_KEY . "captions"]);
	update_post_meta($post["ID"], DARKONYX_KEY . "minage", $attachment[DARKONYX_KEY . "minage"]);
  }
  return $post;

}

// Action hook for defining what the URL tab should use to render itself.
add_action("media_upload_darkonyx_database", "darkonyx_database_render");

/**
 * Handler for rendering the DarkOnyx External URL tab.
 * @return string The HTML to render the tab.
 */
function darkonyx_database_render() {
  $errors = null;

  require_once (dirname(__FILE__) . "/DarkOnyxDatabase.php");
   if(!DarkOnyxFramework::isPlayerInstalled()) {
	return installErrorMessage();
  }
  return wp_iframe("media_darkonyx_db_insert_form", $errors);
}

// Action hook for defining what the URL tab should use to render itself.
add_action("media_upload_darkonyx_external", "darkonyx_external_render");

/**
 * Handler for rendering the DarkOnyx External URL tab.
 * @return string The HTML to render the tab.
 */
function darkonyx_external_render() {
  $errors = null;
  if (!empty($_POST)) {
    $return = media_upload_form_handler();

    if (is_string($return)) {
      return $return;
    }
    if (is_array($return)) {
      $errors = $return;
    }
  }
  require_once (dirname(__FILE__) . "/ExternalMediaTab.php");
  if(!DarkOnyxFramework::isGoodPath()) {
	return installErrorMessage();
  }
  return wp_iframe("media_darkonyx_url_insert_form", $errors);
}

// Filter hook for modifying the URL that displays for URL attachments.
add_filter("wp_get_attachment_url", "darkonyx_url_attachment_filter", 10, 2);

/**
 * Handler for modifying the attachment url.
 * @param string $url The current URL.
 * @param <type> $id The id of the post.
 * @return string The modified URL.
 */
function darkonyx_url_attachment_filter($url, $id) {
  preg_match_all("/http:\/\/|rtmp:\/\//", $url, $matches);
  if (count($matches[0]) > 1) {
    $upload_dir = wp_upload_dir();
    return str_replace($upload_dir["baseurl"] . "/", "", $url);
  }
  return $url;
}

// Filter hook for modifying the file value that appears in the Media Library.
add_filter("get_attached_file", "darkonyx_url_attached_file", 10, 2);

/**
 * Handler for modifying the path to the attached file.
 * @param string $file The current file path.
 * @param int $attachment_id The id of the attachmenet.
 * @return string The modified file path.
 */
function darkonyx_url_attached_file($file, $attachment_id) {
  global $post;

  $external = get_post_meta($attachment_id, DARKONYX_KEY . "external", true);
  if ((isset($post) && substr($post->post_mime_type, 0, 5) == "video") || $external) {
    $upload_dir = wp_upload_dir();

    return str_replace($upload_dir["basedir"]."/", "", $file);
  }
  return $file;
}


// Filter hook for adding additional tabs.
add_filter("media_upload_tabs", "darkonyx_tab");

/**
 * Handler for adding additional tabs.
 * @param array $_default_tabs The array of tabs.
 * @return array $_default_tabs with the new tabs added.
 */
function darkonyx_tab($_default_tabs) {
  $_default_tabs["darkonyx_external"] = "External Media URL";
  $_default_tabs["darkonyx_database"] = "DarkOnyx Videos";
  return $_default_tabs;
}

/**
 * Displays message if plugin is not installed properly.
 */
function installErrorMessage(){
	echo WA_NOT_INSTALLED;
}
?>