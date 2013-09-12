<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2010
 * Date:		$Date$
 * -----------------------------------------------------------------------
 * @author		$Author$
 * @copyright	2006-2011 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev$
 * 
 * $Id$
 */

define('EQDKP_INC', true);
ini_set( 'display_errors', true );
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);

if($_GET['info'] == 'true'){
	phpinfo();die();
}

// fallback config
$tmp_teamname	= (@$_GET['teamname'])	? urldecode($_GET['teamname'])				: "the Voodooclub";
$tmp_teamsize	= (@$_GET['teamsize'])	? urldecode($_GET['teamsize'])				: "2v2";
$tmp_servername	= (@$_GET['realm'])		? urldecode(stripslashes($_GET['realm']))	: "Antonidas";
$tmp_loc		= (@$_GET['loc'])		? urldecode($_GET['loc'])					: "eu";
$tmp_language	= (@$_GET['lang'])		? urldecode($_GET['lang'])					: "de_DE";
$tmp_force		= (@$_GET['force'])		? true										: false;

$output = '';

// init the required plus functions
$eqdkp_root_path = '';

require_once('classes/plus/plus_url_fetcher.class.php');
require_once('classes/plus/core.functions.php');
$puf	= new urlFetcher();

// load the armory class
include_once('objects/bnet_armory.class.php');

$armory		= new bnet_armory($tmp_loc, $tmp_language);
$testdata	= $armory->pvpteam($tmp_servername, $tmp_teamname, $tmp_teamsize, $tmp_force);
$get_method	= ($puf->get_method()) ? $puf->get_method() : 'Cached';

$output .= '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de" lang="de">
		<head>
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
			<title>battle.net Armory Class Tester '.$armory->getVersion().' - CHARACTER</title>
			<link rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.14/themes/base/jquery-ui.css" type="text/css" media="all" />
			<style type="text/css">
				.ui-grid { width: 900px; padding: .4em;  background-image: none; }
				.ui-grid .ui-grid-content { width: 100%; border-collapse: collapse; }
				.ui-grid table tbody td, .ui-grid .ui-grid-header, .ui-grid table thead a { padding: .4em;  }
				.ui-grid table tbody td {  text-align: center; font-weight: normal;  }
				.ui-grid table tbody td.left {  text-align: left; font-weight: normal;  }
				.ui-grid .ui-grid-header, .ui-grid .ui-grid-footer { padding: .8em .4em; text-align: center; }
				.ui-grid .ui-grid-footer { background-image: none; font-weight: normal; text-align: left; }
				.ui-grid table thead a { display: block;  }
				.ui-grid .ui-icon { float: right; }
				.ui-grid .ui-grid-paging { float: right; }
				.ui-grid .ui-grid-paging-prev { float: left; width: 16px; height: 16px; }
				.ui-grid .ui-grid-paging-next { float: right; width: 16px; height: 16px; }
				.ui-grid .ui-grid-results {  }
			</style> 
		</head>
		<body>
';
#$_GET['array']=true;
if($_GET['array'] == 'true'){
	d($testdata);die();
}

if($_GET['achievementdata'] == 'true'){
	d($armory->getdata_achievements('character', $tmp_force));die();
}

$output .= "<b>battle.net Armory Class Tester - CHARACTER</b> ( bnetArmory [".$armory->getVersion()."]), connection method: <span style='color:red;'>".$get_method."</span><br/><br/>";

if(!isset($testdata['status'])){
	$output .= '<div class="ui-grid ui-widget ui-widget-content ui-corner-all">
				<div class="ui-grid-header ui-widget-header ui-corner-top">Profile information of "'.$tmp_charname.'" on realm "'.$tmp_servername.'"</div>';
	$output .= '<table class="ui-grid-content ui-widget-content">';
	
	$output .= '<tr>
					<th class="ui-state-default" width="220">Field name</th>
					<th class="ui-state-default" width="580">Data</th>
				</tr>';
	$output .= '<tr>
					<td width="220" class="ui-widget-content left">GET Parameters</td>
					<td width="580" class="ui-widget-content left">teamname [the Voodooclub], teamsize [2v2], realm [Antonidas], loc [eu], lang [de_DE], force [false]</td>
				</tr>';
	$output .= '<tr>
					<td width="220" class="ui-widget-content left">Team name</td>
					<td width="580" class="ui-widget-content left">'.$testdata['name'].'</td>
				</tr>';
	$output .= '<tr>
					<td width="220" class="ui-widget-content left">Teamsize</td>
					<td width="580" class="ui-widget-content left">'.$testdata['teamsize'].'</td>
				</tr>';
	$output .= '<tr>
					<td width="220" class="ui-widget-content left">Ranking</td>
					<td width="580" class="ui-widget-content left">'.$testdata['ranking'].'</td>
				</tr>';
	$output .= '<tr>
					<td width="220" class="ui-widget-content left">Rating</td>
					<td width="580" class="ui-widget-content left">'.$testdata['rating'].'</td>
				</tr>';
	$output .= '<tr>
					<td width="220" class="ui-widget-content left">Games played</td>
					<td width="580" class="ui-widget-content left">'.$testdata['gamesPlayed'].'</td>
				</tr>';
	$output .= '<tr>
					<td width="220" class="ui-widget-content left">Games won</td>
					<td width="580" class="ui-widget-content left">'.$testdata['gamesWon'].'</td>
				</tr>';
	$output .= '<tr>
					<td width="220" class="ui-widget-content left">Games lost</td>
					<td width="580" class="ui-widget-content left">'.$testdata['gamesLost'].'</td>
				</tr>';
	$output .= '<tr>
					<td width="220" class="ui-widget-content left">Session games played</td>
					<td width="580" class="ui-widget-content left">'.$testdata['sessionGamesPlayed'].'</td>
				</tr>';
	$output .= '<tr>
					<td width="220" class="ui-widget-content left">Session games won</td>
					<td width="580" class="ui-widget-content left">'.$testdata['sessionGamesWon'].'</td>
				</tr>';
	$output .= '<tr>
					<td width="220" class="ui-widget-content left">Session games lost</td>
					<td width="580" class="ui-widget-content left">'.$testdata['sessionGamesLost'].'</td>
				</tr>';
	$output .= '<tr>
					<td width="220" class="ui-widget-content left">Last Session ranking</td>
					<td width="580" class="ui-widget-content left">'.$testdata['lastSessionRanking'].'</td>
				</tr>';
	$output .= '<tr>
					<td width="220" class="ui-widget-content left">Current week ranking</td>
					<td width="580" class="ui-widget-content left">'.$testdata['currentWeekRanking'].'</td>
				</tr>';
	$output .= '<tr>
					<td width="220" class="ui-widget-content left">side</td>
					<td width="580" class="ui-widget-content left">'.$testdata['side'].'</td>
				</tr>';
	$output .= '<tr>
					<td width="220" class="ui-widget-content left">Members</td>
					<td width="580" class="ui-widget-content left">';
	foreach($testdata['members'] as $mmembers){
		$output .= ''.$mmembers['character']['name'].'</br>';
	}
	$output .= '</td></tr>';

	$output .= '</table></div>';
}else{
	$output .= '<b>WARNING: </b> '.$testdata['reason'];
}


$output .= '</body></html>';
echo $output;
?>