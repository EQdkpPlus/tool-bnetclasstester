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

define('EQDKP_INC', true);
ini_set( 'display_errors', true );
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
// Loas phpinfo()
if($_GET['info'] == 'true'){
	phpinfo();die();
}

// fallback config
$tmp_charname 	= (@$_GET['character'])	? urldecode($_GET['character'])							: "Jaga";
$tmp_servername = (@$_GET['server'])		? urldecode(stripslashes($_GET['server']))	: "Antonidas";
$tmp_loc 				= (@$_GET['loc'])				? urldecode($_GET['loc'])										: "eu";
$tmp_language 	= (@$_GET['language'])	? urldecode($_GET['language'])							: "de_de";

$output = '';

// init the required plus functions
$eqdkp_root_path = '';
require_once('classes/plus/urlreader.class.php');
$urlreader    = new urlreader();

// load the armory class
include_once('classes/ArmoryChars.class.php');

$output .= '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de" lang="de">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
';

$armory = new ArmoryChars();
$output .= "<b>Armory Download Class Tester</b> ( armory.class.php [".$armory->version."#".$armory->build."])<br/>";
$output .= "<br>";

$chardata 	= $armory->GetCharacterData($tmp_charname,$tmp_servername,$tmp_loc, $tmp_language);
$testdata 	= $armory->BuildMemberArray($chardata[0]);
$get_method	= ($urlreader->get_method()) ? $urlreader->get_method() : 'Cached';

if($testdata != 'no_char'){
	$output .= '<table width="800">';
	
	$output .= '<tr><td width="220">Connection Method</td><td width="580"><span style="color:red;">'.$get_method.'</span></td></tr>';
	$output .= '<tr><td width="220">Icon</td><td width="580"><img src="'.$testdata['ac_charicon'].'" alt="charicon" /></td></tr>';
	$output .= '<tr><td width="220">Name</td><td width="580"><a href="'.$armory->Link($tmp_loc, $tmp_charname, $tmp_servername, 'char').'">'.$testdata['name'].'</a></td></tr>';
	$output .= '<tr><td width="220">Titel</td><td width="580">'.$testdata['suffix'].'</td></tr>';
	$output .= '<tr><td width="220">Klasse</td><td width="580">'.$testdata['class'].' [eqdkp-classid: '.$testdata['class_eqdkp'].']</td></tr>';
	$output .= '<tr><td width="220">Rasse</td><td width="580">'.$testdata['race'].' [eqdkp-raceid: '.$testdata['race_eqdkp'].']</td></tr>';
	$output .= '<tr><td width="220">Level</td><td width="580">'.$testdata['level'].'</td></tr>';
	$output .= '<tr><td width="220">Geschlecht</td><td width="580">'.$testdata['gender'].' [gender-id: '.$testdata['genderid'].']</td></tr>';
	$output .= '<tr><td width="220">Gilde</td><td width="580">'.$testdata['guildname'].'</td></tr>';
	$output .= '<tr><td width="220">Faction</td><td width="580">'.$testdata['faction'].' [faction-id: '.$testdata['factionid'].']</td></tr>';
	$output .= '<tr><td width="220">Honored Kills</td><td width="580">'.$testdata['honoredkills'].'</td></tr>';
	$output .= '<tr><td width="220">Last Update</td><td width="580">'.$testdata['lastmodified'].'</td></tr>';
	$ats = $armory->Date2Timestamp($testdata['lastmodified']);
	$output .= '<tr><td width="220">LU Timestamp</td><td width="580">'.$ats.' ('.date('d.m.Y',$ats).')</td></tr>';
	
	/**** basestats *****/
	// INFO: not all for every basestat available
	// AVAILABLE: armor, attack, base, critHitPercent, effective, petBonus, percent, healthRegen, manaRegen, mana
	$output .= '<tr><td width="220">Strength</td><td width="580">'.$testdata['basestats']->strength['base'].'</td></tr>';
	$output .= '<tr><td width="220">agility</td><td width="580">'.$testdata['basestats']->agility['base'].'</td></tr>';
	$output .= '<tr><td width="220">stamina</td><td width="580">'.$testdata['basestats']->stamina['base'].'</td></tr>';
	$output .= '<tr><td width="220">intellect</td><td width="580">'.$testdata['basestats']->intellect['base'].'</td></tr>';
	$output .= '<tr><td width="220">spirit</td><td width="580">'.$testdata['basestats']->spirit['base'].'</td></tr>';
	$output .= '<tr><td width="220">armor</td><td width="580">'.$testdata['basestats']->armor['base'].'</td></tr>';
	
	/**** resistences *****/
	// AVAILABLE: value, petBonus
	$output .= '<tr><td width="220">Arcane</td><td width="580">'.$testdata['resistances']->arcane['value'].'</td></tr>';
	$output .= '<tr><td width="220">Fire</td><td width="580">'.$testdata['resistances']->fire['value'].'</td></tr>';
	$output .= '<tr><td width="220">Frost</td><td width="580">'.$testdata['resistances']->frost['value'].'</td></tr>';
	$output .= '<tr><td width="220">Holy</td><td width="580">'.$testdata['resistances']->holy['value'].'</td></tr>';
	$output .= '<tr><td width="220">Nature</td><td width="580">'.$testdata['resistances']->nature['value'].'</td></tr>';
	$output .= '<tr><td width="220">Shadow</td><td width="580">'.$testdata['resistances']->shadow['value'].'</td></tr>';
	
	/**** Character Bars *****/
	// AVAILABLE: *health* effective; *secondbar* casting, notCasting, type, effective
	$output .= '<tr><td width="220">Health</td><td width="580">'.$testdata['characterbars']->health['effective'].'</td></tr>';
	$output .= '<tr><td width="220">2nd</td><td width="580">'.$testdata['characterbars']->secondBar['effective'].'</td></tr>';
	$output .= '<tr><td width="220">2nd_2</td><td width="580">'.$testdata['characterbars']->secondBar['type'].'</td></tr>';
	
	/**** Spec Tree One & Two *****/
	// AVAILABLE: active, group, icon, prim, treeOne, treeThree, treeTwo
	$output .= '<tr><td width="220">Skilltree 1</td><td width="580">'.$testdata['spec1']['treeOne'].'-'.$testdata['spec1']['treeTwo'].'-'.$testdata['spec1']['treeThree'].' ('.$testdata['spec1']['prim'].') '.(($testdata['spec1']['active']) ? '[Aktiv]' : '').'</td></tr>';
	$output .= (@$testdata['spec2']) ? '<tr><td width="220">Skilltree 2</td><td width="580">'.$testdata['spec2']['treeOne'].'-'.$testdata['spec2']['treeTwo'].'-'.$testdata['spec2']['treeThree'].' ('.$testdata['spec2']['prim'].') '.(($testdata['spec2']['active']) ? '[Aktiv]' : '').'</td></tr>' : '';
	
	/**** Professions *****/
	// AVAILABLE: id, key, max, name, value
	foreach($testdata['professions']->children() as $professions){
	$output .= '<tr><td width="220">'.$professions['name'].'</td><td width="580">'.$professions['value'].'</td></tr>';
	}
	
	/**** Items *****/
	// AVAILABLE: displayInfoId, durability, gem0Id, gem1Id, gem2Id, gemIcon0, gemIcon1, gemIcon2, icon, id, level, maxDurability, name, permanentEnchantIcon
	//						permanentEnchantSpellDesc, permanentEnchantSpellName, permanentenchant, pickUp, putDown, randomPropertiesId, rarity, seed, slot
	$output .='<tr><td width="220">Items</td><td width="580">
							<table>
							<tr>
								<th width="250">Item Name</th>
								<th width="100">Item ID</th>
								<th width="50">Enchant</th>
								<th width="50">Gems</th>
								<th width="100">Item Level</th>
								<th width="30">Slot</th>
							</tr>';
							
	foreach($testdata['items'] as $myitems){
		$gemarray = array($myitems['gem0Id'],$myitems['gem1Id'],$myitems['gem2Id']);
		$output .= '<tr><td>'.$myitems['name'].'</td><td>'.$myitems['id'].'</td><td>'.$myitems['permanentenchant'].'</td><td>'.implode(', ', $gemarray).'</td><td>'.$myitems['level'].'</td><td>'.$myitems['slot'].'</td></tr>';
	}
	$output .='</table></td></tr>';
	
	/**** Achievements *****/
	$output .= '<tr><td colspan=2><b><a href="'.$armory->Link($tmp_loc, $tmp_charname, $tmp_servername, 'achievements').'">Achievements</a></b></td></tr>';
	$output .='<tr><td width="220"> Points</td><td width="580">'.$testdata['achievements']['main']['points'].'</td></tr>';
	$output .='<tr><td width="220">Progress</td><td width="580">'.$testdata['achievements']['main']['earned'].'/'.$testdata['achievements']['main']['total'].'</td></tr>';
	
	foreach($testdata['achievements']['detail'] as $myAchievements){
		if($myAchievements['main']['id'] != '81'){
			$output .='<tr><td width="220">'.$myAchievements['main']['name'].'</td>
		<td width="580">'.$myAchievements['child']['earned'].'/'.$myAchievements['child']['total'].'</td></tr>';
		}
	}
	
	$output .= '</table>';
}else{
	$output .= '<b>WARNING: </b> No Chardata available on armory. Try other input!';
}

echo $output;
?>