<?php
include_once(DARKONYX_PLUGIN_DIR. "/framework/Player.php");

/**
 * Callback for locating [DarkOnyx] tag instances.
 * @param string $the_content The content to be parsed.
 * @return string The parsed and replaced [DarkOnyx] tag.
 */
function darkonyx_tag_callback($the_content = "") {
	$tag_regex = '/(.?)\[(DarkOnyx)\b(.*?)(?:(\/))?\](?:(.+?)\[\/\2\])?(.?)/s';
	$the_content = preg_replace_callback($tag_regex, "darkonyx_tag_parser", $the_content);
	return $the_content;
}

/**
 * Parses the attributes of the [DarkOnyx] tag.
 * @param array $matches The match array
 * @return string The code that should replace the tag.
 */
function darkonyx_tag_parser($matches) {
  if ($matches[1] == "[" && $matches[6] == "]") {
    return substr($matches[0], 1, -1);
  }
  $player = new Player();
  $param_regex = '/([\w.]+)\s*=\s*"([^"]*)"(?:\s|$)|([\w.]+)\s*=\s*\'([^\']*)\'(?:\s|$)|([\w.]+)\s*=\s*([^\s\'"]+)(?:\s|$)|"([^"]*)"(?:\s|$)|(\S+)(?:\s|$)/';
  $text = preg_replace("/[\x{00a0}\x{200b}]+/u", " ", $matches[3]);
  $atts = array();
  if (preg_match_all($param_regex, $text, $match, PREG_SET_ORDER)) {
    foreach ($match as $p_match) {
      if (!empty($p_match[1])){
			switch($p_match[1]){
				case "width":
					$player->setWidth($p_match[2]);
					break;
				case "height":
					$player->setHeight($p_match[2]);
					break;
				case "campaign":
					$player->setCampaign($p_match[2]);
					break;
				case "style":
					$player->setStyle($p_match[2]);
					break;
				case "id":
					$player->setDarkOnyxID($p_match[2]);
					break;
				case "mediaid":
					$player->setMediaID($p_match[2]);
					break;
				default:
					break;
			}
		}
    }
  } else {
    $atts = ltrim($text);
  }

  return $matches[1] . $player->getCode() . $matches[6];
}
?>