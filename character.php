<?php
 /*
 * Project:     eqdkpPLUS Libraries: Armory Class Construct
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		    http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       2008
 * Date:        $Date: 2009-06-21 21:21:41 +0200 (Sun, 21 Jun 2009) $
 * -----------------------------------------------------------------------
 * @author      $Author: wallenium $
 * @copyright   2008 Simon (Wallenium) Wallmann
 * @link        http://eqdkp-plus.com
 * @package     libraries: armory construct tester
 * @version     $Rev: 5090 $
 * 
 * $Id: character.php 5090 2009-06-21 19:21:41Z wallenium $
 */

// Loas phpinfo()
if($_GET['info'] == 'true'){
	phpinfo();die();
}

// fallback config
$tmp_charname 	= (@$_GET['character'])	? urldecode($_GET['character'])							: "Eladriel";
$tmp_servername = (@$_GET['server'])		? urldecode(stripslashes($_GET['server']))	: "Malygos";
$tmp_loc 				= (@$_GET['loc'])				? urldecode($_GET['loc'])										: "eu";
$tmp_language 	= (@$_GET['language'])	? urldecode($_GET['language'])							: "de_de";
echo 'irgendwas';
if(@$_GET['debug'] == 'true'){
	$output .= $_SERVER['HTTP_USER_AGENT'];
}

error_reporting(E_ALL);
$output = '';
include_once('classes/ArmoryChars.class.php');

$armory = new ArmoryChars("âîâ");
$output .= "<b>Armory Download Class Tester</b> ( armory.class.php [".$armory->version."#".$armory->build."])<br/>";
$output .= "<br>";

$chardata = $armory->GetCharacterData($tmp_charname,$tmp_servername,$tmp_loc, $tmp_language);
$testdata = $armory->BuildMemberArray($chardata[0]);

$output .= '<table width="500">';

$output .= '<tr><td width="220">Icon</td><td width="280"><img src="'.$testdata['ac_charicon'].'" alt="charicon" /></td></tr>';
$output .= '<tr><td width="220">Name</td><td width="280"><a href="'.$armory->Link($tmp_loc, $tmp_charname, $tmp_servername, 'char').'">'.$testdata['name'].'</a></td></tr>';
$output .= '<tr><td width="220">Titel</td><td width="280">'.$testdata['suffix'].'</td></tr>';
$output .= '<tr><td width="220">Klasse</td><td width="280">'.$testdata['class'].' [eqdkp-classid: '.$testdata['class_eqdkp'].']</td></tr>';
$output .= '<tr><td width="220">Rasse</td><td width="280">'.$testdata['race'].' [eqdkp-raceid: '.$testdata['race_eqdkp'].']</td></tr>';
$output .= '<tr><td width="220">Level</td><td width="280">'.$testdata['level'].'</td></tr>';
$output .= '<tr><td width="220">Geschlecht</td><td width="280">'.$testdata['gender'].' [gender-id: '.$testdata['genderid'].']</td></tr>';
$output .= '<tr><td width="220">Gilde</td><td width="280">'.$testdata['guildname'].'</td></tr>';
$output .= '<tr><td width="220">Faction</td><td width="280">'.$testdata['faction'].' [faction-id: '.$testdata['factionid'].']</td></tr>';
$output .= '<tr><td width="220">Honored Kills</td><td width="280">'.$testdata['honoredkills'].'</td></tr>';
$output .= '<tr><td width="220">Last Update</td><td width="280">'.$testdata['lastmodified'].'</td></tr>';
$ats = $armory->Date2Timestamp($testdata['lastmodified']);
$output .= '<tr><td width="220">LU Timestamp</td><td width="280">'.$ats.' ('.date('d.m.Y',$ats).')</td></tr>';

$output .= '<tr><td width="220">Arcane</td><td width="280">'.$testdata['resistances']->arcane['value'].'</td></tr>';
$output .= '<tr><td width="220">Fire</td><td width="280">'.$testdata['resistances']->fire['value'].'</td></tr>';
$output .= '<tr><td width="220">Frost</td><td width="280">'.$testdata['resistances']->frost['value'].'</td></tr>';
$output .= '<tr><td width="220">Holy</td><td width="280">'.$testdata['resistances']->holy['value'].'</td></tr>';
$output .= '<tr><td width="220">Nature</td><td width="280">'.$testdata['resistances']->nature['value'].'</td></tr>';
$output .= '<tr><td width="220">Shadow</td><td width="280">'.$testdata['resistances']->shadow['value'].'</td></tr>';

$output .= '<tr><td width="220">Health</td><td width="280">'.$testdata['characterbars']->health['effective'].'</td></tr>';
$output .= '<tr><td width="220">2nd</td><td width="280">'.$testdata['characterbars']->secondBar['effective'].'</td></tr>';
$output .= '<tr><td width="220">2nd_2</td><td width="280">'.$testdata['characterbars']->secondBar['type'].'</td></tr>';

$output .= '<tr><td width="220">Skilltree 1</td><td width="280">'.$testdata['spec1']['treeOne'].'-'.$testdata['spec1']['treeTwo'].'-'.$testdata['spec1']['treeThree'].' ('.$testdata['spec1']['prim'].')</td></tr>';
$output .= (@$testdata['spec2']) ? '<tr><td width="220">Skilltree 2</td><td width="280">'.$testdata['spec2']['treeOne'].'-'.$testdata['spec2']['treeTwo'].'-'.$testdata['spec2']['treeThree'].' ('.$testdata['spec2']['prim'].')</td></tr>' : '';


foreach($testdata['professions']->children() as $professions){
$output .= '<tr><td width="220">'.$professions['name'].'</td><td width="280">'.$professions['value'].'</td></tr>';
}

// Items
$output .='<tr><td width="220">Items</td><td width="280">';
foreach($testdata['items']->children() as $myitems){
$output .= $myitems['id'].', ';
}
$output .='</td></tr>';

// Achievements
$output .= '<tr><td colspan=2><b><a href="'.$armory->Link($tmp_loc, $tmp_charname, $tmp_servername, 'achievements').'">Achievements</a></b></td></tr>';
$output .='<tr><td width="220"> Points</td><td width="280">'.$testdata['achievements']['main']['points'].'</td></tr>';
$output .='<tr><td width="220">Progress</td><td width="280">'.$testdata['achievements']['main']['earned'].'/'.$testdata['achievements']['main']['total'].'</td></tr>';

foreach($testdata['achievements']['detail'] as $myAchievements){
	if($myAchievements['main']['id'] != '81'){
		$output .='<tr><td width="220">'.$myAchievements['main']['name'].'</td>
	<td width="280">'.$myAchievements['child']['earned'].'/'.$myAchievements['child']['total'].'</td></tr>';
	}
}

$output .= '</table>';

echo $output;
?>