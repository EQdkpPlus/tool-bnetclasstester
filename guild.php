<?php
/******************************
 * Armory Import Class Tester
 * (c) 2007 by Simon Wallmann
 * ---------------------------
 * $Id: guild.php 5209 2009-07-09 19:58:31Z wallenium $
 ******************************/

// Loas phpinfo()
if($_GET['info'] == 'true'){
	phpinfo();die();
}

// fallback config
$wow_servername = "Malygos";
$wow_guild			= "Die Legendären";
//$wow_servername = "Mal'Ganis";
//$wow_guild			= " Der Gerechte Tod ";
$wow_loc				= "eu";
$wow_language		= "de_de";
$min_level			= 20;

error_reporting(E_ALL);
$output = '';
include_once('classes/ArmoryChars.class.php');

if(@$_GET['debug'] == 'true'){
	$output .= $_SERVER['HTTP_USER_AGENT'];
}

$armory = new ArmoryChars("âîâ");
$output .= "<b>Armory Download Class Tester</b> ( armory.class.php [".$armory->version."#".$armory->build."])<br/>";
$output .= "<br>";

	$tmp_guild 			= (@$_GET['guild']) ? urldecode($_GET['guild']) : $wow_guild;
	$tmp_servername = (@$_GET['server']) ? urldecode(stripslashes($_GET['server'])) : $wow_servername;
	$tmp_loc 				= (@$_GET['loc']) ? urldecode($_GET['loc']) : $wow_loc;
	$tmp_language 	= (@$_GET['language']) ? urldecode($_GET['language']) : $wow_language;
	$min_level			= (@$_GET['level']) ? urldecode($_GET['level']) : $min_level;
	$cclass					= (@$_GET['class']) ? urldecode($_GET['class']) : '';
	
	//Load the armory stuff
	$dataarry 			= $armory->GetGuildMembers($tmp_guild,$tmp_servername,$tmp_loc, $min_level, $cclass, $tmp_language);

	$output .= '<table width="400">';
	foreach($dataarry as $chars){
		$output .= "<tr>";
		$output .= '<td width="200">'.utf8_encode($chars['name']).'</td>';
		$output .= '<td width="50">'.$chars['classid'].'</td>';
		$output .= '<td width="150">'.$chars['level'].'</td>';
		$output .= "</tr>";
	}
	$output .= "</table>";
	echo $output;
?>
