<?php
/******************************
 * Armory Import Class Tester
 * (c) 2007 by Simon Wallmann
 * ---------------------------
 * $Id: $
 ******************************/

define('EQDKP_INC', true);

// Loas phpinfo()
if($_GET['info'] == 'true'){
	phpinfo();die();
}

// fallback config
$wow_servername = "Antonidas";
$wow_guild			= "Die Freien";
$wow_loc				= "eu";
$wow_language		= "de_de";
$min_level			= 20;

$output = '';
require_once('classes/plus/urlreader.class.php');
$urlreader    = new urlreader();
include_once('classes/ArmoryChars.class.php');

if(@$_GET['debug'] == 'true'){
	$output .= $_SERVER['HTTP_USER_AGENT'];
}

$tmp_guild 			= (@$_GET['guild']) ? urldecode($_GET['guild']) : $wow_guild;
$tmp_servername = (@$_GET['server']) ? urldecode(stripslashes($_GET['server'])) : $wow_servername;
$tmp_loc 				= (@$_GET['loc']) ? urldecode($_GET['loc']) : $wow_loc;
$tmp_language 	= (@$_GET['language']) ? urldecode($_GET['language']) : $wow_language;
$min_level			= (@$_GET['level']) ? urldecode($_GET['level']) : $min_level;
$cclass					= (@$_GET['class']) ? urldecode($_GET['class']) : '';
$rrank['value']	= (@$_GET['rank']) ? urldecode($_GET['rank']) : '';

$output .= '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de" lang="de">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
';

$armory = new ArmoryChars();
$output .= "<b>Armory Download Class Tester</b> ( armory.class.php [".$armory->version."#".$armory->build."])<br/>";
$output .= "<a href='".$armory->Link($tmp_loc, '', $tmp_servername, 'guild', $tmp_guild)."' target='top'>Link to Armory</a><br>";
$output .= "<br>";
	
	//Load the armory stuff
	$dataarry 			= $armory->GetGuildMembers($tmp_guild,$tmp_servername,$tmp_loc, $min_level, $cclass, $rrank ,$tmp_language);

	$output .= '<table width="400">';
		$output .= '<tr><td width="200">Name</td>';
		$output .= '<td width="50">Class ID</td>';
		$output .= '<td width="150">Level</td>';
		$output .= '<td width="150">Rank</td>';
		$output .= '</tr>';
	foreach($dataarry as $chars){
		$output .= "<tr>";
		$output .= '<td width="200">'.$chars['name'].'</td>';
		$output .= '<td width="50">'.$chars['classid'].'</td>';
		$output .= '<td width="150">'.$chars['level'].'</td>';
		$output .= '<td width="150">'.$chars['rank'].'</td>';
		$output .= "</tr>";
	}
	$output .= "</table>";
	echo $output;
?>
