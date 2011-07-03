<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2007
 * Date:		$Date: 2011-03-18 20:10:20 +0100 (Fri, 18 Mar 2011) $
 * -----------------------------------------------------------------------
 * @author		$Author: wallenium $
 * @copyright	2006-2011 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev: 10112 $
 * 
 * $Id: armory.class.php 10112 2011-03-18 19:10:20Z wallenium $
 */

define('EQDKP_INC', true);
ini_set( 'display_errors', true );
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
// Loas phpinfo()
if($_GET['info'] == 'true'){
	phpinfo();die();
}

// fallback config
$tmp_charname	= (@$_GET['character'])	? urldecode($_GET['character'])				: "corgan";
$tmp_servername	= (@$_GET['server'])	? urldecode(stripslashes($_GET['server']))	: "Antonidas";
$tmp_loc		= (@$_GET['loc'])		? urldecode($_GET['loc'])					: "eu";
$tmp_language	= (@$_GET['language'])	? urldecode($_GET['language'])				: "de_de";
$tmp_force		= (@$_GET['force'])		? true										: false;

$output = '';

// init the required plus functions
$eqdkp_root_path = '';

require_once('classes/plus/urlreader.class.php');
$urlreader	= new urlreader();

// load the armory class
include_once('classes/ArmoryChars.class.php');

$output .= '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de" lang="de">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
';

$armory		= new ArmoryChars();
$armory->setSettings(array('loc'=>$tmp_loc, 'lang'=> $tmp_language));
$testdata	= $armory->character($tmp_charname, $tmp_servername, $tmp_force);
$get_method	= ($urlreader->get_method()) ? $urlreader->get_method() : 'Cached';

$output .= "<b>Armory Download Class Tester</b> ( armory.class.php [".$armory->version."#".$armory->build."])<br/>";
$output .= "<br>";

if($_GET['array'] == 'true'){
	d($testdata);die();
}

if(!isset($testdata['status'])){
	$output .= '<table width="800">';
	
	$output .= '<tr><td width="220">Connection Method</td><td width="580"><span style="color:red;">'.$get_method.'</span></td></tr>';
	$output .= '<tr><td width="220">Icon</td><td width="580"><img src="'.$armory->charicon($testdata['thumbnail']).'" alt="charicon" /></td></tr>';

	$output .= '<tr><td width="220">Name</td><td width="580"><a href="'.$armory->Link($tmp_charname, $tmp_servername, 'char').'">'.$testdata['name'].'</a></td></tr>';
	//$output .= '<tr><td width="220">Titel</td><td width="580">'.$character_data['suffix'].'</td></tr>';
	$output .= '<tr><td width="220">Klasse</td><td width="580">'.$testdata['class'].' [eqdkp-classid: '.$armory->ConvertID($testdata['class'], 'int', 'classes').']</td></tr>';
	$output .= '<tr><td width="220">Rasse</td><td width="580">'.$testdata['race'].' [eqdkp-raceid: '.$armory->ConvertID($testdata['race'], 'int', 'races').']</td></tr>';
	$output .= '<tr><td width="220">Level</td><td width="580">'.$testdata['level'].'</td></tr>';
	$output .= '<tr><td width="220">Geschlecht</td><td width="580">'.$armory->ConvertID($testdata['gender'], 'int', 'gender').' [gender-id: '.$testdata['gender'].']</td></tr>';
	//$output .= '<tr><td width="220">Gilde</td><td width="580">'.$character_data['guildName'].'</td></tr>';
	//$output .= '<tr><td width="220">Faction</td><td width="580">'.$character_data['faction'].' [faction-id: '.$character_data['factionId'].']</td></tr>';
	$output .= '<tr><td width="220">Last Update</td><td width="580">'.$testdata['lastModified'].'</td></tr>';
	//$ats = $armory->Date2Timestamp($character_data['lastModified']);
	//$output .= '<tr><td width="220">LU Timestamp</td><td width="580">'.$ats.' ('.date('d.m.Y',$ats).')</td></tr>';
	
	$output .= '</table>';
}else{
	$output .= '<b>WARNING: </b> '.$testdata['reason'];
}

echo $output;
?>