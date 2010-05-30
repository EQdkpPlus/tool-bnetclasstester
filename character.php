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
require_once('classes/plus/xmlTools.class.php');
require_once('classes/plus/core.functions.php');
$urlreader    = new urlreader();
$xmltools     = new xmlTools();

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

if($_GET['array'] == 'true'){
	d($testdata);die();
}

if($testdata != 'no_char'){
	$output .= '<table width="800">';
	
	$output .= '<tr><td width="220">Connection Method</td><td width="580"><span style="color:red;">'.$get_method.'</span></td></tr>';
	$output .= '<tr><td width="220">Icon</td><td width="580"><img src="'.$testdata['ac_charicon'].'" alt="charicon" /></td></tr>';
	
	$character_data = $testdata['character']['@attributes'];
	$output .= '<tr><td width="220">Name</td><td width="580"><a href="'.$armory->Link($tmp_loc, $tmp_charname, $tmp_servername, 'char').'">'.$character_data['name'].'</a></td></tr>';
	$output .= '<tr><td width="220">Titel</td><td width="580">'.$character_data['suffix'].'</td></tr>';
	$output .= '<tr><td width="220">Klasse</td><td width="580">'.$character_data['class'].' [eqdkp-classid: '.$testdata['class_eqdkp'].']</td></tr>';
	$output .= '<tr><td width="220">Rasse</td><td width="580">'.$character_data['race'].' [eqdkp-raceid: '.$testdata['race_eqdkp'].']</td></tr>';
	$output .= '<tr><td width="220">Level</td><td width="580">'.$character_data['level'].'</td></tr>';
	$output .= '<tr><td width="220">Geschlecht</td><td width="580">'.$character_data['gender'].' [gender-id: '.$character_data['genderId'].']</td></tr>';
	$output .= '<tr><td width="220">Gilde</td><td width="580">'.$character_data['guildName'].'</td></tr>';
	$output .= '<tr><td width="220">Faction</td><td width="580">'.$character_data['faction'].' [faction-id: '.$character_data['factionId'].']</td></tr>';
	$output .= '<tr><td width="220">Last Update</td><td width="580">'.$character_data['lastModified'].'</td></tr>';
	$ats = $armory->Date2Timestamp($character_data['lastModified']);
	$output .= '<tr><td width="220">LU Timestamp</td><td width="580">'.$ats.' ('.date('d.m.Y',$ats).')</td></tr>';
	
	$chartab_data = $testdata['characterTab'];
	/**** basestats *****/
	// INFO: not all for every basestat available
	// AVAILABLE: armor, attack, base, critHitPercent, effective, petBonus, percent, healthRegen, manaRegen, mana
	$output .= '<tr><td width="220">Strength</td><td width="580">'.$chartab_data['baseStats']['strength']['@attributes']['base'].'</td></tr>';
	$output .= '<tr><td width="220">agility</td><td width="580">'.$chartab_data['baseStats']['agility']['@attributes']['base'].'</td></tr>';
	$output .= '<tr><td width="220">stamina</td><td width="580">'.$chartab_data['baseStats']['stamina']['@attributes']['base'].'</td></tr>';
	$output .= '<tr><td width="220">intellect</td><td width="580">'.$chartab_data['baseStats']['intellect']['@attributes']['base'].'</td></tr>';
	$output .= '<tr><td width="220">spirit</td><td width="580">'.$chartab_data['baseStats']['spirit']['@attributes']['base'].'</td></tr>';
	$output .= '<tr><td width="220">armor</td><td width="580">'.$chartab_data['baseStats']['armor']['@attributes']['base'].'</td></tr>';
	
	/**** resistences *****/
	// AVAILABLE: value, petBonus
	$output .= '<tr><td width="220">Arcane</td><td width="580">'.$chartab_data['resistances']['arcane']['@attributes']['value'].'</td></tr>';
	$output .= '<tr><td width="220">Fire</td><td width="580">'.$chartab_data['resistances']['fire']['@attributes']['value'].'</td></tr>';
	$output .= '<tr><td width="220">Frost</td><td width="580">'.$chartab_data['resistances']['frost']['@attributes']['value'].'</td></tr>';
	$output .= '<tr><td width="220">Holy</td><td width="580">'.$chartab_data['resistances']['holy']['@attributes']['value'].'</td></tr>';
	$output .= '<tr><td width="220">Nature</td><td width="580">'.$chartab_data['resistances']['nature']['@attributes']['value'].'</td></tr>';
	$output .= '<tr><td width="220">Shadow</td><td width="580">'.$chartab_data['resistances']['shadow']['@attributes']['value'].'</td></tr>';
	
	/**** Character Bars *****/
	// AVAILABLE: *health* effective; *secondbar* casting, notCasting, type, effective
	$output .= '<tr><td width="220">Health</td><td width="580">'.$chartab_data['characterBars']['health']['@attributes']['effective'].'</td></tr>';
	$output .= '<tr><td width="220">2nd</td><td width="580">'.$chartab_data['characterBars']['secondBar']['@attributes']['effective'].'</td></tr>';
	$output .= '<tr><td width="220">2nd_2</td><td width="580">'.$chartab_data['characterBars']['secondBar']['@attributes']['type'].'</td></tr>';
	
	
	$talents = $chartab_data['talentSpecs']['talentSpec'];
	/**** Spec Tree One & Two *****/
	// AVAILABLE: active, group, icon, prim, treeOne, treeThree, treeTwo
	$output .= '<tr><td width="220">Skilltree 1</td><td width="580">'.$talents[0]['@attributes']['treeOne'].'-'.$talents[0]['@attributes']['treeTwo'].'-'.$talents[0]['@attributes']['treeThree'].' ('.$talents[0]['@attributes']['prim'].') '.(($talents[0]['@attributes']['active']) ? '[Aktiv]' : '').'</td></tr>';
	$output .= (@$talents[1]['@attributes']['prim']) ? '<tr><td width="220">Skilltree 2</td><td width="580">'.$talents[1]['@attributes']['treeOne'].'-'.$talents[1]['@attributes']['treeTwo'].'-'.$talents[1]['@attributes']['treeThree'].' ('.$talents[1]['@attributes']['prim'].') '.(($talents[1]['@attributes']['active']) ? '[Aktiv]' : '').'</td></tr>' : '';
	
	/**** Professions *****/
	// AVAILABLE: id, key, max, name, value
	foreach($chartab_data['professions']['skill'] as $professions){
	$output .= '<tr><td width="220">'.$professions['@attributes']['name'].'</td><td width="580">'.$professions['@attributes']['value'].'</td></tr>';
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
							
	foreach($chartab_data['items']['item'] as $myitems){
		$gemarray = array($myitems['@attributes']['gem0Id'],$myitems['@attributes']['gem1Id'],$myitems['@attributes']['gem2Id']);
		$output .= '<tr><td>'.$myitems['@attributes']['name'].'</td><td>'.$myitems['@attributes']['id'].'</td><td>'.$myitems['@attributes']['permanentenchant'].'</td><td>'.implode(', ', $gemarray).'</td><td>'.$myitems['@attributes']['level'].'</td><td>'.$myitems['@attributes']['slot'].'</td></tr>';
	}
	$output .='</table></td></tr>';
	
	$achvmnt_data = $testdata['summary'];
	/**** Achievements *****/
	$output .= '<tr><td colspan=2><b><a href="'.$armory->Link($tmp_loc, $tmp_charname, $tmp_servername, 'achievements').'">Achievements</a></b></td></tr>';
	$output .='<tr><td width="220"> Points</td><td width="580">'.$achvmnt_data['c']['@attributes']['points'].'</td></tr>';
	$output .='<tr><td width="220">Progress</td><td width="580">'.$achvmnt_data['c']['@attributes']['earned'].'/'.$achvmnt_data['c']['@attributes']['total'].'</td></tr>';
	
	foreach($achvmnt_data['category'] as $myAchievements){
		if($myAchievements['@attributes']['id'] != '81'){
			$output .='<tr><td width="220">'.$myAchievements['c']['@attributes']['name'].'</td>
		<td width="580">'.$myAchievements['c']['@attributes']['earned'].'/'.$myAchievements['c']['@attributes']['total'].'</td></tr>';
		}
	}
	
	$output .= '</table>';
}else{
	$output .= '<b>WARNING: </b> No Chardata available on armory. Try other input!';
}

echo $output;
?>