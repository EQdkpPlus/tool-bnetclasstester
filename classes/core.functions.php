<?php
 /*
 * Project:     eqdkpPLUS Core: Functions
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		    http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       2010
 * Date:        $Date: 2010-05-30 13:16:28 +0200 (Sun, 30 May 2010) $
 * -----------------------------------------------------------------------
 * @author      $Author: wallenium $
 * @copyright   2008 Simon (Wallenium) Wallmann
 * @link        http://eqdkp-plus.com
 * @package     libraries:PluginUpdCheck
 * @version     $Rev: 7938 $
 *
 * $Id: core.functions.php 7938 2010-05-30 11:16:28Z wallenium $
 */

if ( !defined('EQDKP_INC') ){
	header('HTTP/1.0 404 Not Found');exit;
}

// -----------------------------------------
// Version Data
// -----------------------------------------
define('EQDKPPLUS_VERSION', '0.7.0.1');
define('REQUIRED_PHP_VERSION', '5.2.0');
define('EQDKPPLUS_VERSION_BETA', TRUE);

if (isset($svn_rev)) {
	define('SVN_REV', $svn_rev);
}

/**
 * Determines if a folder path is valid. Ignores .svn, CVS, cache, etc.
 *
 * @param     string     $path             Path to check
 * @return    boolean
 */
function valid_folder($path){
	$ignore = array('.', '..', '.svn', 'CVS', 'cache', 'install', 'index.html', '.htaccess', '_images');
	if (isset($path)){
		if (!is_file($path) && !is_link($path) && !in_array(basename($path), $ignore)){
			return true;
		}
	}
	return false;
}

/**
* Strip multiple slashes
*
* @param $string	String input
*/
function stripmultslashes($string){
	$string = preg_replace("#(\\\){1,}(\"|\&quot;)#", '"', $string);
	$string = preg_replace("#(\\\){1,}(\'|\&\#039)#", "'", $string);
	return $string;
}

/**
* Undo Sanatize Tags
*
* @param $data	Data
*/
function undo_sanitize_tags($data){
	if ( is_array($data) ){
		foreach ( $data as $k => $v ){
			$data[$k] = undo_sanitize_tags($v);
		}
	}else{
		$data = str_replace('&lt;', '<', $data);
		$data = str_replace('&gt;', '>', $data);
	}
	return $data;
}

/**
* Applies htmlspecialchars to an array of data
*
* @deprec sanitize_tags
* @param $data
* @return array
*/
function htmlspecialchars_array($data){
	if ( is_array($data) ){
		foreach ( $data as $k => $v ){
			$data[$k] = ( is_array($v) ) ? htmlspecialchars_array($v) : htmlspecialchars($v);
		}
	}
	return $data;
}

/**
 * Personal ob_start function which uses gzip if enabled
 * This prevents the whitepage bugs/ wrong encodings if gzip is enabled
 */
function My_ob_start(){
  global $eqdkp;
  if ( $eqdkp->config['enable_gzip'] == '1' ){
    if ( (extension_loaded('zlib')) && (!headers_sent()) ){
      @ob_start('ob_gzhandler');
    }
  }else{
    @ob_start();
  }
}

/**
 * Rundet je nach Einstellungen im Eqdkp Plus Admin Menu die DKP Werte
 *
 * @param float $value
 * @return float
 */
function runden($value){
	global $eqdkp;
	$ret_val		= $value;
	$precision	= $eqdkp->config['pk_round_precision'];

	if (($precision < 0) or ($precision > 5) ){
		$precision = 2;
	}

	if ($eqdkp->config['pk_round_activate'] == "1"){
		$ret_val = round($value,$precision)	;
	}
	return $ret_val;
}

/**
 * var_dump array
 *
 * @param array $array
 */
function da_($array){
	echo "<pre>";
	var_dump($array);
	echo "</pre>";
}

/**
* Debug Function
* wenn inhalt ein array ist, wird da() aufgerufen
*
* @param mixed $content
* @return mixed
*/
function d($content="-" ){
	if(is_array($content)){
		return da($content);
	}
	if (is_object($content)) {
		echo "<pre>";
		var_dump($content);
		echo "</pre>";
	}

	if (is_bool($content)) {
		if($content == true){
			$content = "Bool - True";
		}else{
			$content = "Bool - false";
		}
	}

	if (strlen($content) ==0) {
		$content = "String Lenght=0";
	}

	echo "<table border=0>\n";
	echo "<tr>\n";
	echo "<td bgcolor='#0080C0'>";
	echo "<B>" . $content . "</B>";
	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";
}

/**
 * Debug Function
 * gibt ein Array in Tabbelarischer Form aus.
 *
 * @param Array $TheArray
 * @return mixed
 */
function da( $TheArray ){ // Note: the function is recursive
	if(!is_array($TheArray)){
		return "no array";
	}
	echo "<table border=0>\n";
	$Keys = array_keys( $TheArray );
	foreach( $Keys as $OneKey ){
		echo "<tr>\n";
		echo "<td bgcolor='#727450'>";
		echo "<B>" . $OneKey . "</B>";
		echo "</td>\n";
		echo "<td bgcolor='#C4C2A6'>";
		if ( is_array($TheArray[$OneKey]) ){
			da($TheArray[$OneKey]);
		}else{
			echo $TheArray[$OneKey];
		}
		echo "</td>\n";
		echo "</tr>\n";
	}
	echo "</table>\n";
}

/**
* Sanatize Tags
*
* @param $data	Data
*/
function sanitize_tags($data){
	if ( is_array($data) ){
		foreach ( $data as $k => $v ){
			$data[$k] = sanitize_tags($v);
		}
	}else{
		$data = str_replace('<', '&lt;', $data);
		$data = str_replace('>', '&gt;', $data);
	}
	return $data;
}

function join_array($glue, $pieces, $dimension = 0){
	$rtn = array();
	foreach($pieces as $key => $value){
		if(isset($value[$dimension])){
			$rtn[] = $value;
		}
	}
	return join($glue, $rtn);
}

/**
 * Returns <kbd>true</kbd> if the string or array of string is encoded in UTF8.
 *
 * Example of use. If you want to know if a file is saved in UTF8 format :
 * <code> $array = file('one file.txt');
 * $isUTF8 = isUTF8($array);
 * if (!$isUTF8) --> we need to apply utf8_encode() to be in UTF8
 * else --> we are in UTF8 :)
 * @param mixed A string, or an array from a file() function.
 * @return boolean
 */
function isUTF8($string){
	if (is_array($string)){
		$enc = implode('', $string);
		return @!((ord($enc[0]) != 239) && (ord($enc[1]) != 187) && (ord($enc[2]) != 191));
	}else{
		return (utf8_encode(utf8_decode($string)) == $string);
	}
}

/**
 * Own utf8 encode function
 *
 * @param string
 * @return string
 */
function utf8encode($string){
	if(function_exists('mb_convert_encoding')){
		 return mb_convert_encoding($string, "UTF-8", mb_detect_encoding($string));
	}else{
		return utf8_encode($string);
	}
}

/**
 * Own utf8 decode function 
 *
 * SHOULD NOT BE USED IN CODE -
 * ONLY FOR SERVER-SERVER COMMUNICATIONS
 *
 * @param string
 * @return string
 */
function utf8decode($string){
	if(function_exists('mb_convert_encoding')){
		 return mb_convert_encoding($string, "iso-8859-1", "UTF-8");
	}else{
		return utf8_decode($string);
	}
}

function validate(){
	global $eqdkp_root_path ;

	$keyfile_dat = $eqdkp_root_path.'/key.dat' ;
	$keyfile_php = $eqdkp_root_path.'/key.php' ;
	$return = true;

	if(file_exists($keyfile_dat) ){
		$handle = @fopen($keyfile_dat,"r");
		$keystring = @fread($handle, filesize($keyfile_dat));
	}elseif (file_exists($keyfile_php) ){
		include_once($keyfile_php);
	}

	if (strlen($keystring) > 1){
		$keystring = @base64_decode($keystring) ;
		$keystring = @gzuncompress($keystring) ;
		$keystring = @unserialize($keystring);
		$_data = $keystring ;
	}

	if (is_array($_data)){
		$_info = " | Type:".$_data['type']." | User:".$_data['kndNr'];

		switch ($_data['type']){
			case 0: $return = (substr(EQDKPPLUS_VERSION,0,3) > $_data['version_allowed']) ? true : false ;	 break;	 //check server & version - 10
			case 1: $return = false ; break;	 //>50
			case 2: $return = false ; break;	 //>100
			case 3: $return = false ; break;	 //>100
			case 4: $return = false ; break;	 //>dev
			case 5: $return = false ; break;	 //>beta
		}
	}
	return $return;
}

/**
 * returns coloured names
 * @param array $neg
 * @param array $norm
 * @param array $pos
 * @return string
 */
function get_coloured_member_names($norm, $pos=array(), $neg=array()){
	global $pdh;
	$mems = array();
	if(is_array($neg)){
		foreach($neg as $member_id){
    	$mems[] = "<span class='negative'>".$pdh->get('member', 'name', array($member_id))."</span>";
		}
	}
	if(is_array($norm)){
		foreach($norm as $member_id){
			$mems[] = $pdh->get('member', 'name', array($member_id));
		}
	}
	if(is_array($pos)){
		foreach($pos as $member_id){
    	$mems[] = "<span class='positive'>".$pdh->get('member', 'name', array($member_id))."</span>";
		}
	}
	asort($mems);
	return implode(', ', $mems);
}

/**
 * returns comparison result
 * @param string $version1
 * @param string $version2
 * @return int
 */
function compareVersion($version1, $version2){
  $result = 0;
  $match1 = explode('.', $version1);
  $match2 = explode('.', $version2);
  $int1 = sprintf( '%d%02d%02d%02d', $match1[0], $match1[1], intval($match1[2]),intval($match1[3]));
  $int2 = sprintf( '%d%02d%02d%02d', $match2[0], $match2[1], intval($match2[2]), intval($match2[3]) );

  if($int1 < $int2){ $result = -1;}
  if($int1 > $int2){ $result = 1;}
  return $result;
}

function RunGlobalsFix(){
	if( (bool)@ini_get('register_globals') ){
		$superglobals = array($_ENV, $_GET, $_POST, $_COOKIE, $_FILES, $_SERVER);
		if( isset($_SESSION) ){
			array_unshift($superglobals, $_SESSION);
		}
		$knownglobals = array(
			// Known PHP Reserved globals and superglobals:
			'_ENV',       'HTTP_ENV_VARS',
			'_GET',       'HTTP_GET_VARS',
			'_POST',    	'HTTP_POST_VARS',
			'_COOKIE',    'HTTP_COOKIE_VARS',
			'_FILES',    	'HTTP_FILES_VARS',
			'_SERVER',    'HTTP_SERVER_VARS',
			'_SESSION',   'HTTP_SESSION_VARS',
			'_REQUEST',

			// Global variables used by this code snippet:
			'superglobals',
			'knownglobals',
			'superglobal',
			'global',
			'void'
		);
		foreach( $superglobals as $superglobal ){
			foreach( $superglobal as $global => $void ){
				if( !in_array($global, $knownglobals) ){
					unset($GLOBALS[$global]);
				}
			}
		} // end forach
	} // end if register_globals = on
}

/**
 * returns sorted ids			example: sorting by name
 * @param array $tosort				array($id => array('name' => name))
 * @param array $order				array(0, 0)
 * @param array $sort_order			array(0 => 'name')
 * @return array
 */
function get_sortedids($tosort, $order, $sort_order){
	$sorts = array();
	foreach($tosort as $id => $detail){
		$sorts[$id] = $detail[$sort_order[$order[0]]];
	}
	if($order[1]){
		arsort($sorts);
	}else{
		asort($sorts);
	}
	foreach($sorts as $id => $detail){
		$sortids[] = $id;
	}
	return $sortids;
}

/**
 * Redirects the user to another page and exits cleanly
 *
 * @param     string     $url          URL to redirect to
 * @param     bool       $return       Whether to return the generated redirect url (true) or just redirect to the page (false)
 * @param			bool			 $extern			 Is it an external link (other server) or an internal link?
 * @return    mixed                    null, else the parsed redirect url if return is true.
 */
function redirect($url, $return = false, $extern=false){
	global $eqdkp;
	$out = ((!$extern) ? $eqdkp->BuildLink(). str_replace('&amp;', '&', $url) : $url);
	if ($return){
		return $out;
	}else{
		header('Location: ' . $out);exit;
	}
}

/**
 * Keep a consistent page title across the entire application
 *
 * @param     string     $title            The dynamic part of the page title, appears before " - Guild Name DKP"
 * @return    string
 */
function page_title($title = ''){
	global $eqdkp, $user;
	$pt_prefix		= (defined('IN_ADMIN')) ? $user->lang['admin_title_prefix'] : $user->lang['title_prefix'];
	$main_title		= sprintf($pt_prefix, $eqdkp->config['guildtag'], $eqdkp->config['dkp_name']);
	return sanitize((( $title != '' ) ? $title.' - ' : '').$main_title, TAG);
}

/**
 * Returns the appropriate CSS class to use based on a number's range
 *
 * @param     string     $item             The number
 * @param     boolean    $percentage       Treat the number like a percentage?
 * @return    mixed                        CSS Class / false
*/
function color_item($item, $percentage = false){
	global $eqdkp;
	if (!is_numeric($item)){
		return false;
	}
	$class		= 'neutral';
	$max_val	= ($eqdkp->config['pk_max_percvalue']) ? $eqdkp->config['pk_max_percvalue'] : 67;
	$min_val	= ($eqdkp->config['pk_min_percvalue']) ? $eqdkp->config['pk_min_percvalue'] : 34;

	if (!$percentage){
		if($item < 0){
			$class = 'negative';
		}elseif($item > 0){
			$class = 'positive';
		}
	}else{
		if($item >= 0 && $item <= $min_val){
			$class = 'negative';
		}elseif ($item >= $max_val && $item <= 100){
			$class = 'positive';
		}
	}
	return $class;
}

/**
 * Resolve the User Browser
 *
 * @param string $member
 * @return string
 */
function resolve_browser($string){
	global $eqdkp_root_path, $jquery, $html;

	if( preg_match("/opera/i",$string))
	{
	return "<span ".$html->HTMLTooltip($string, '')."><img src=\"".$eqdkp_root_path."images/glyphs/browser/opera_icon.png\"></span>";
	}
	else if( preg_match("/msie/i",$string) )
	{
	return"<span ".$html->HTMLTooltip($string, '')."><img src=\"".$eqdkp_root_path."images/glyphs/browser/ie_icon.png\"></span>";
	}
	else if( preg_match("/chrome/i", $string) )
	{
	return"<span ".$html->HTMLTooltip($string, '')."><img src=\"".$eqdkp_root_path."images/glyphs/browser/chrome_icon.png\"></span>";
	}
	else if( preg_match("/konqueror/i",$string) )
	{
	return"<span ".$html->HTMLTooltip($string, '')."><img src=\"".$eqdkp_root_path."images/glyphs/browser/konqueror_icon.png\"></span>";
	}
	else if( preg_match("/safari/i",$string) )
	{
	return"<span ".$html->HTMLTooltip($string, '')."><img src=\"".$eqdkp_root_path."images/glyphs/browser/safari_icon.png\"></span>";
	}
	else if( preg_match("/lynx/i",$string) )
	{
	return "<span ".$html->HTMLTooltip($string, '').">Lynx</span>";
	}
	else if( preg_match("/netscape6/i",$string) )
	{
	return "<span ".$html->HTMLTooltip($string, '')."><img src=\"".$eqdkp_root_path."images/glyphs/browser/netscape_icon.png\"></span>";
	}
	else if( preg_match("/mozilla/i",$string) )
	{
	return "<span ".$html->HTMLTooltip($string, '')."><img src=\"".$eqdkp_root_path."images/glyphs/browser/firefox_icon.png\"></span>";
	}
	else if( preg_match("/w3m/i",$string) )
	{
	return "<span ".$html->HTMLTooltip($string, '').">w3m</span>";
	}
	else
	{
	return "<span ".$html->HTMLTooltip($string, '')."><img src=\"".$eqdkp_root_path."images/glyphs/help_off.png\"></span>";
	}
}

/**
 * Resolve the EQDKP Page the user is surfing on..
 *
 * @param string $member
 * @return string
 */
function resolve_eqdkp_page($page){
	global $db, $eqdkp, $user, $SID, $pdh;

	$matches = explode('&', $page);
	if (!empty($matches[0])){
		switch ($matches[0]){
			/***************** Admin *****************/
			case 'addadj':
				$page = $user->lang['adding_groupadj'];
				if ( (!empty($matches[1])) && (preg_match('/^' . URI_ADJUSTMENT . '=([0-9]{1,})/', $matches[1], $adjustment_id)) ){
					$page  = $user->lang['editing_groupadj'] . ': ';
					$page .= '<a href="addadj.php' . $SID . '&amp;' . URI_ADJUSTMENT . '=' . $adjustment_id[1] . '">' . $adjustment_id[1] . '</a>';
				}
			break;
			case 'addiadj':
				$page = $user->lang['adding_indivadj'];
				if ( (!empty($matches[1])) && (preg_match('/^' . URI_ADJUSTMENT . '=([0-9]{1,})/', $matches[1], $adjustment_id)) ){
					$page  = $user->lang['editing_indivadj'] . ': ';
					$page .= '<a href="addiadj.php' . $SID . '&amp;' . URI_ADJUSTMENT . '=' . $adjustment_id[1] . '">' . $adjustment_id[1] . '</a>';
				}
			break;
			case 'additem':
				$page = $user->lang['adding_item'];
				if ( (!empty($matches[1])) && (preg_match('/^' . URI_ITEM . '=([0-9]{1,})/', $matches[1], $item_id)) ){
					$item_name = $pdh->get('item', 'name', array($item_id[1]));
					$page  = $user->lang['editing_item'] . ': ';
					$page .= '<a href="additem.php' . $SID . '&amp;' . URI_ITEM . '=' . $item_id[1] . '">' . $item_name . '</a>';
				}
			break;
			case 'addnews':
				$page = $user->lang['adding_news'];
				if ( (!empty($matches[1])) && (preg_match('/^' . URI_NEWS . '=([0-9]{1,})/', $matches[1], $news_id)) ){
					$news_name = get_news_name($news_id[1]);
					$page  = $user->lang['editing_item'] . ': ';
					$page .= '<a href="addnews.php' . $SID . '&amp;' . URI_NEWS . '=' . $news_id[1] . '">' . $news_name . '</a>';
				}
			break;
			case 'addraid':
				$page = $user->lang['adding_raid'];
				if ( (!empty($matches[1])) && (preg_match('/^' . URI_RAID . '=([0-9]{1,})/', $matches[1], $raid_id)) ){
					$raid_name = $pdh->get('raid', 'event_name', array($raid_id[1]));
					$page  = $user->lang['editing_raid'] . ': ';
					$page .= '<a href="addraid.php' . $SID . '&amp;' . URI_RAID . '=' . $raid_id[1] . '">' . $raid_name . '</a>';
				}
			break;
			case 'addturnin':
				$page = $user->lang['adding_turnin'];
			break;
			case 'config':
				$page = $user->lang['managing_config'];
			break;
			case 'index':
				$page = $user->lang['viewing_admin_index'];
			break;
			case 'logs':
				$page = $user->lang['viewing_logs'];
			break;
			case 'manage_members':
				$page = $user->lang['managing_members'];
			break;
			case 'manage_users':
				$page = $user->lang['managing_users'];
			break;
			case 'mysql_info':
				$page = $user->lang['viewing_mysql_info'];
			break;
			case 'plugins':
				$page = $user->lang['managing_plugins'];
			break;
			case 'styles':
				$page = $user->lang['managing_styles'];
			break;
			
			/***************** Listing *****************/
			case 'listadj':
				if ( (empty($matches[1])) || ($matches[1] == 'group') ){
				$page = $user->lang['listing_groupadj'];
				}else{
				$page = $user->lang['listing_indivadj'];
				}
			break;
			case 'listevents':
				$page = $user->lang['listing_events'];
			break;
			case 'listitems':
				if ( (empty($matches[1])) || ($matches[1] == 'values') ){
					$page = $user->lang['listing_itemvals'];
				}else{
					$page = $user->lang['listing_itemhist'];
				}
			break;
			case 'listmembers':
				$page = $user->lang['listing_members'];
			break;
			case 'listraids':
				$page = $user->lang['listing_raids'];
			break;
			
			/***************** Misc *****************/
			case 'parse_log':
				$page = $user->lang['parsing_log'];
			break;
			case 'stats':
				$page = $user->lang['viewing_stats'];
			break;
			case 'summary':
				$page = $user->lang['viewing_summary'];
			break;
                    
			/***************** Viewing *****************/
			case 'viewevent':
				$page = $user->lang['viewing_event'] . ': ';
				if (!empty($matches[1])){
					preg_match('/^' . URI_EVENT . '=([0-9]{1,})/', $matches[1], $event_id);
					$event_name = $pdh->get('event', 'name', array($event_id[1]));
					$page .= '<a href="../viewevent.php' . $SID . '&amp;' . URI_EVENT . '=' . $event_id[1] . '" target="_top">' . $event_name . '</a>';
				}
			break;
			case 'viewitem':
				$page = $user->lang['viewing_item'] . ': ';
				if (!empty($matches[1])){
					preg_match('/^' . URI_ITEM . '=([0-9]{1,})/', $matches[1], $item_id);
					$item_name = $pdh->get('item', 'name', array($item_id[1]));
					$page .= '<a href="../viewitem.php' . $SID . '&amp;' . URI_ITEM . '=' . $item_id[1] . '" target="_top">' . $item_name . '</a>';
				}
			break;
			case 'viewnews':
				$page = $user->lang['viewing_news'];
			break;
			case 'viewmember':
				$page = $user->lang['viewing_member'] . ': ';
				if (!empty($matches[1])){
					preg_match('/^' . URI_NAME . '=([A-Za-z]{1,})/', $matches[1], $member_name);
					$page .= '<a href="../viewmember.php' . $SID . '&amp;' . URI_NAME . '=' . $member_name[1] . '" target="_top">' . $member_name[1] . '</a>';
				}
			break;
			case 'viewraid':
				$page = $user->lang['viewing_raid'] . ': ';
				if (!empty($matches[1])){
					preg_match('/^' . URI_RAID . '=([0-9]{1,})/', $matches[1], $raid_id);
					$raid_name = $pdh->get('raid', 'name', array($raid_id[1]));
					$page .= '<a href="../viewraid.php' . $SID . '&amp;' . URI_RAID . '=' . $raid_id[1] . '" target="_top">' . $raid_name . '</a>';
				}
			break;
		}
	}
	return $page;
}

/**
 * Resolve the saved eqdkp log entries
 *
 * @param string $member
 * @return string
 */
function resolve_logs($row){
	global $user, $logs;
	eval($row['log_value']);

	switch ( $row['log_tag'] ){
		case '{L_ACTION_EVENT_ADDED}':
			$logline = sprintf($user->lang['vlog_event_added'],      $row['username'], $log_action['{L_NAME}'], $log_action['{L_VALUE}']);
		break;
		case '{L_ACTION_EVENT_UPDATED}':
			$logline = sprintf($user->lang['vlog_event_updated'],    $row['username'], $log_action['{L_NAME_BEFORE}']);
		break;
		case '{L_ACTION_EVENT_DELETED}':
			$logline = sprintf($user->lang['vlog_event_deleted'],    $row['username'], $log_action['{L_NAME}']);
		break;
		case '{L_ACTION_GROUPADJ_ADDED}':
			$logline = sprintf($user->lang['vlog_groupadj_added'],   $row['username'], $log_action['{L_ADJUSTMENT}']);
		break;
		case '{L_ACTION_GROUPADJ_UPDATED}':
			$logline = sprintf($user->lang['vlog_groupadj_updated'], $row['username'], $log_action['{L_ADJUSTMENT_BEFORE}']);
		break;
		case '{L_ACTION_GROUPADJ_DELETED}':
			$logline = sprintf($user->lang['vlog_groupadj_deleted'], $row['username'], $log_action['{L_ADJUSTMENT}']);
		break;
		case '{L_ACTION_HISTORY_TRANSFER}':
			$logline = sprintf($user->lang['vlog_history_transfer'], $row['username'], $log_action['{L_FROM}'], $log_action['{L_TO}']);
		break;
		case '{L_ACTION_INDIVADJ_ADDED}':
			$logline = sprintf($user->lang['vlog_indivadj_added'],   $row['username'], $log_action['{L_ADJUSTMENT}'], count(explode(', ', $log_action['{L_MEMBERS}'])));
		break;
		case '{L_ACTION_INDIVADJ_UPDATED}':
			$logline = sprintf($user->lang['vlog_indivadj_updated'], $row['username'], $log_action['{L_ADJUSTMENT_BEFORE}'], $log_action['{L_MEMBERS_BEFORE}']);
		break;
		case '{L_ACTION_INDIVADJ_DELETED}':
			$logline = sprintf($user->lang['vlog_indivadj_deleted'], $row['username'], $log_action['{L_ADJUSTMENT}'], $log_action['{L_MEMBERS}']);
		break;
		case '{L_ACTION_ITEM_ADDED}':
			$logline = sprintf($user->lang['vlog_item_added'],       $row['username'], $log_action['{L_NAME}'], count(explode(', ', $log_action['{L_BUYERS}'])), $log_action['{L_VALUE}']);
		break;
		case '{L_ACTION_ITEM_UPDATED}':
			$logline = sprintf($user->lang['vlog_item_updated'],     $row['username'], $log_action['{L_NAME_BEFORE}'], count(explode(', ', $log_action['{L_BUYERS_BEFORE}'])));
		break;
		case '{L_ACTION_ITEM_DELETED}':
			$logline = sprintf($user->lang['vlog_item_deleted'],     $row['username'], $log_action['{L_NAME}'], count(explode(', ', $log_action['{L_BUYERS}'])));
		break;
		case '{L_ACTION_MEMBER_ADDED}':
			$logline = sprintf($user->lang['vlog_member_added'],     $row['username'], $log_action['{L_NAME}']);
		break;
		case '{L_ACTION_MEMBER_UPDATED}':
			$logline = sprintf($user->lang['vlog_member_updated'],   $row['username'], $log_action['{L_NAME_BEFORE}']);
		break;
		case '{L_ACTION_MEMBER_DELETED}':
			$logline = sprintf($user->lang['vlog_member_deleted'],   $row['username'], $log_action['{L_NAME}']);
		break;
		case '{L_ACTION_NEWS_ADDED}':
			$logline = sprintf($user->lang['vlog_news_added'],       $row['username'], $log_action['{L_HEADLINE}']);
		break;
		case '{L_ACTION_NEWS_UPDATED}':
			$logline = sprintf($user->lang['vlog_news_updated'],     $row['username'], $log_action['{L_HEADLINE_BEFORE}']);
		break;
		case '{L_ACTION_NEWS_DELETED}':
			$logline = sprintf($user->lang['vlog_news_deleted'],     $row['username'], $log_action['{L_HEADLINE}']);
		break;
		case '{L_ACTION_RAID_ADDED}':
			$logline = sprintf($user->lang['vlog_raid_added'],       $row['username'], $log_action['{L_EVENT}']);
		break;
		case '{L_ACTION_RAID_UPDATED}':
			$logline = sprintf($user->lang['vlog_raid_updated'],     $row['username'], $log_action['{L_EVENT_BEFORE}']);
		break;
		case '{L_ACTION_RAID_DELETED}':
			$logline = sprintf($user->lang['vlog_raid_deleted'],     $row['username'], $log_action['{L_EVENT}']);
		break;
		case '{L_ACTION_TURNIN_ADDED}':
			$logline = sprintf($user->lang['vlog_turnin_added'],     $row['username'], $log_action['{L_FROM}'], $log_action['{L_TO}'], $log_action['{L_ITEM}']);
		case '{L_ACTION_LOGS_DELETED}':
			$logline = sprintf($user->lang['vlog_logs_deleted'],       $row['username'], $log_action['{L_HEADLINE}']);
		break;
		case '{L_ACTION_USER_ADDED}':
			$logline = sprintf($user->lang['vlog_user_added'],     $row['username'], $log_action['{L_USER}']);
		break;
		case '{L_ACTION_USER_UPDATED}':
			$logline = sprintf($user->lang['vlog_user_updated'],   $row['username'], $log_action['{L_USER}']);
		break;
		case '{L_ACTION_USER_DELETED}':
			$logline = sprintf($user->lang['vlog_user_deleted'],   $row['username'], $log_action['{L_USER}']);
		break;
		case '{L_ACTION_MULTIDKP_ADDED}':
			$logline = sprintf($user->lang['vlog_multidkp_added'],     $row['username'], $log_action['{L_MULTINAME}']);
		break;
		case '{L_ACTION_MULTIDKP_UPDATED}':
			$logline = sprintf($user->lang['vlog_multidkp_updated'],   $row['username'], $log_action['{L_MULTINAME}']);
		break;
		case '{L_ACTION_MULTIDKP_DELETED}':
			$logline = sprintf($user->lang['vlog_multidkp_deleted'],   $row['username'], $log_action['{L_MULTINAME}']);
		break;
		default: $logline = $logs->lang_replace($row['log_tag']).' ('.$row['username'].')';
	}
	unset($log_action);
	return $logline;
}

/**
 * Checks if a POST or a database field value exists;
 * Use the POST field value or if not available use the DB field!
 *
 * @param    string  $post_field POST field name
 * @param    array   $db_row     Array of DB values
 * @param    string  $db_field   DB field name
 * @return   string
 */
function post_or_db($fieldname, $data_row = array(), $data_field = ''){
	global $in;
	// Check if there's a database row..
	if (@sizeof($data_row) > 0 ){
		if ( $data_field == '' ){
			$data_field = $fieldname;
		}
		$database_value = $data_row[$data_field];
	}else{
		$database_value = '';
	}
	return ($in->get($fieldname)) ? $in->get($fieldname) : $database_value;
}

/**
 * Sanitize an imput
 *
 * @param     string     $input            Input to sanitize
 * @return    string
 */
function sanitize($input){
	return filter_var($input, FILTER_SANITIZE_STRING);
}

/**
 * unsanatize the input
 *
 * @param     string     $input            Input to reverse
 * @return    string
 */
function unsanitize($input){
	return htmlspecialchars_decode($input, ENT_QUOTES);
}

/**
 * Returns a string with a list of available pages
 *
 * @param     string     $url         			The starting URL for each page link
 * @param     int        $items        			The number of items we're paging through
 * @param     int        $per_page         How many items to display per page
 * @param     int        $start_item       Which number are we starting on
 * @param     string     $start_variable   In case you need to call your _GET var something other than 'start'
 * @return    string
 */
function generate_pagination($url, $items, $per_page, $start, $start_variable='start'){
    global $eqdkp_root_path, $user;
		
    $uri_symbol = ( strpos($url, '?') ) ? '&amp;' : '?';
		//On what page we are?
		$recent_page = (int)floor($start / $per_page) + 1;
		//Calculate total pages
		$total_pages = ceil($items / $per_page);
		//Return if we don't have at least 2 Pages
		if (!$items || $total_pages  < 2){
        return '';
    }

		//First Page
    $pagination = '<div class="pagination">';
		if ($recent_page == 1){
			$pagination .= '<span class="pagination_activ">1</span>';
		} else {
			$pagination .= '<a href="'.$url.$uri_symbol . $start_variable.'='.( ($recent_page - 2) * $per_page).'" title="'.$user->lang['previous_page'].'"><img src="'.$eqdkp_root_path.'images/arrows/left_arrow.png" border="0"></a>&nbsp;&nbsp;<a href="'.$url.'" class="pagination">1</a>';
		}

		//If total-pages < 4 show all page-links
		if ($total_pages < 4){
				$pagination .= ' ';
				for ( $i = 2; $i < $total_pages; $i++ ){
					if ($i == $recent_page){
						$pagination .= '<span class="pagination_activ">'.$i.'</span> ';
					} else {
						$pagination .= '<a href="'.$url.$uri_symbol.$start_variable.'='.( ($i - 1) * $per_page).'" title="'.$user->lang['page'].' '.$i.'" class="pagination">'.$i.'</a> ';
					}
					$pagination .= ' ';
				}	
		//Don't show all page-links		
		} else {
			$start_count = min(max(1, $recent_page - 5), $total_pages - 4);
			$end_count = max(min($total_pages, $recent_page + 5), 4);
			
			$pagination .= ( $start_count > 1 ) ? ' ... ' : ' ';
			
			for ( $i = $start_count + 1; $i < $end_count; $i++ ){
				if ($i == $recent_page){
					$pagination .= '<span class="pagination_activ">'.$i.'</span> ';
				} else {
					$pagination .= '<a href="'.$url.$uri_symbol.$start_variable.'='.( ($i - 1) * $per_page).'" title="'.$user->lang['page'].' '.$i.'" class="pagination">'.$i.'</a> ';
				}
			}
			$pagination .= ($end_count < $total_pages ) ? '  ...  ' : ' ';		
		} //close else
		
		
		//Last Page
		if ($recent_page == $total_pages){
			$pagination .= '<span class="pagination_activ">'.$recent_page.'</span>';
		} else {
			$pagination .= '<a href="'.$url.$uri_symbol.$start_variable.'='.(($total_pages - 1) * $per_page) . '" class="pagination" title="'.$user->lang['page'].' '.$total_pages.'">'.$total_pages.'</a>&nbsp;&nbsp;<a href="'.$base. $uri_symbol .$start_variable.'='.($recent_page * $per_page).'" title="'.$user->lang['next_page'].'"><img src="'.$eqdkp_root_path.'images/arrows/right_arrow.png" border="0"></a>';
		}
	
	$pagination .= '</div>';
	return $pagination;
}
/*
 * Add the necessary Javascript for infotooltip to the template
 * @return true
 */
function infotooltip_js($tooltip=true) {
	static $added = 0;
    global $tpl, $eqdkp_root_path, $eqdkp, $user;
	if(!$added AND $eqdkp->config['infotooltip_use']) {
		$tpl->js_file($eqdkp_root_path.'infotooltip/includes/jquery.infotooltip.js');
		$js = "$(document).ready(function(){
			$('.infotooltip').infotooltips();";
		if($tooltip) {
			$js .= "$('.infotooltip').tooltip({
						tooltipClass: 'ui-tt-transparent-tooltip',
						content: function(response) {
							$.get('".$eqdkp_root_path."infotooltip/infotooltip_feed.php?direct=1&name='+$(this).attr('title')+'&lang='+$(this).attr('lang')+'&game_id='+$(this).attr('game_id'), response);
							return '".$user->lang['lib_loading']."';
						},
						open: function() {
							var tooltip = $(this).tooltip('widget');
							$(document).mousemove(function(event) {
								tooltip.position({
									my: 'left center',
									at: 'right center',
									offset: '25 25',
									of: event
								});
							})
							// trigger once to override element-relative positioning
							.mousemove();
						},
						close: function() {
							$(document).unbind('mousemove');
						}
					});";
		}
		$tpl->add_js($js."});");
		$tpl->css_file($eqdkp_root_path.'infotooltip/includes/'.$eqdkp->config['default_game'].'.css');
	}
	$added = 1;
	return true;
}


//////////////////////////////////////////////////////////////////////
// TO BE REWORKED OR REMOVED
//////////////////////////////////////////////////////////////////////

/**
 * Return the gender of a given member
 * need the Plugin Charmanager installed
 *
 * @param string $member
 * @return string
 */
function get_Gender($member){
	global $table_prefix , $pm,$db;
	static $gender_member = array();

	$ret = '';
	if (isset($member)){
		if (empty($gender_member)){
			$sql =' SELECT m.member_id, m.member_name, ma.gender
							FROM __member_additions ma
			   			INNER JOIN __members m
			   			ON ma.member_id = m.member_id';
			$result = $db->query($sql);
			while($row = $db->fetch_record($result)){
				$gender_member[$row['member_name']] = $row['gender'];
			}
		}
		$ret = $gender_member[$member];
	}
	return $ret ;
}

function Raidcount(){
	global $db ;
	$sql = "SELECT count(raid_id) from __raids";
	$count = $db->query_first($sql);

	if ($count > 1 ){
		return	true ;
	}
}

function decode_unicode($string) {
	$string = str_replace('\u00df', 'ß', $string);
	$string = str_replace('\u00e4', 'ä', $string);
	$string = str_replace('\u00fc', 'ü', $string);
	$string = str_replace('\u00f6', 'ö', $string);
	$string = str_replace('\u00c4', 'Ä', $string);
	$string = str_replace('\u00dc', 'Ü', $string);
	$string = str_replace('\u00d6', 'Ö', $string);
	return $string;
}

function get_cookie($name){
		global $eqdkp;

		$cookie_name = $eqdkp->config['cookie_name'] . '_' . $name;
		return ( isset($_COOKIE[$cookie_name]) ) ? $_COOKIE[$cookie_name] : '';
}

function set_cookie($name, $cookie_data, $cookie_time){
		global $eqdkp;

		setcookie($eqdkp->config['cookie_name'] . '_' . $name, $cookie_data, $cookie_time, $eqdkp->config['cookie_path'], $eqdkp->config['cookie_domain']);
}
?>