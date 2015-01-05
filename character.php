<?php
/*	Project:	EQdkp-Plus
 *	Package:	Battle.net class tester
 *	Link:		http://eqdkp-plus.eu
 *
 *	Copyright (C) 2006-2015 EQdkp-Plus Developer Team
 *
 *	This program is free software: you can redistribute it and/or modify
 *	it under the terms of the GNU Affero General Public License as published
 *	by the Free Software Foundation, either version 3 of the License, or
 *	(at your option) any later version.
 *
 *	This program is distributed in the hope that it will be useful,
 *	but WITHOUT ANY WARRANTY; without even the implied warranty of
 *	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *	GNU Affero General Public License for more details.
 *
 *	You should have received a copy of the GNU Affero General Public License
 *	along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

define('EQDKP_INC', true);
ini_set( 'display_errors', true );
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
require_once('config.php');

if($_GET['info'] == 'true'){
	phpinfo();die();
}

// fallback config
$tmp_charname	= (@$_GET['character'])	? urldecode($_GET['character'])				: $default_character;
$tmp_servername	= (@$_GET['realm'])		? urldecode(stripslashes($_GET['realm']))	: $default_realm;
$tmp_loc		= (@$_GET['loc'])		? urldecode($_GET['loc'])					: $default_loc;
$tmp_language	= (@$_GET['lang'])		? urldecode($_GET['lang'])					: $default_language;
$tmp_force		= (@$_GET['force'])		? true										: false;

$output = '';

// init the required plus functions
$eqdkp_root_path = '';

require_once('classes/plus/plus_url_fetcher.class.php');
require_once('classes/plus/core.functions.php');
$puf	= new urlFetcher();

// load the armory class
include_once('objects/bnet_armory.class.php');

$armory		= new bnet_armory($tmp_loc, $tmp_language, $api_key);
$testdata	= $armory->character($tmp_charname, $tmp_servername, $tmp_force);
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

function convert($size){
	$unit=array('b','kb','mb','gb','tb','pb');
	return @round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i];
}

if($_GET['array'] == 'true'){
	d($testdata);die();
}
#['achievements']
if($_GET['achievementdata'] == 'true'){
	d($armory->getdata_achievements('character', $tmp_force));die();
}

$output .= "<b>battle.net Armory Class Tester - CHARACTER</b> ( bnetArmory [".$armory->getVersion()."]), connection method: <span style='color:red;'>".$get_method."</span> <span>Memory: ".convert(memory_get_usage(true))." [".convert(memory_get_peak_usage())."]</span><br/><br/>";

if(!isset($testdata['status'])){
	$output .= '<div class="ui-grid ui-widget ui-widget-content ui-corner-all">
				<div class="ui-grid-header ui-widget-header ui-corner-top">Profile information of "'.$tmp_charname.'" on realm "'.$tmp_servername.'"</div>';
	$output .= '<table class="ui-grid-content ui-widget-content">';
	
	$output .= '<tr>
					<th class="ui-state-default" width="220">Field name</th>
					<th class="ui-state-default" width="580">Data</th>
				</tr>';
	$output .= '<tr>
					<td width="220" class="ui-widget-content left">Icon</td>
					<td width="580" class="ui-widget-content left"><img src="'.$armory->characterIcon($testdata).'" alt="charicon" /></td>
				</tr>';
	$output .= '<tr>
					<td width="220" class="ui-widget-content left">GET Parameters</td>
					<td width="580" class="ui-widget-content left">character [corgan], realm [Antonidas], loc [eu], lang [de_DE], force [false]</td>
				</tr>';
	$output .= '<tr>
					<td width="220" class="ui-widget-content left">Image</td>
					<td width="580" class="ui-widget-content left"><img src="'.$armory-> characterImage($testdata).'" alt="charimage" /></td>
				</tr>';
	$output .= '<tr>
					<td width="220" class="ui-widget-content left">Name</td>
					<td width="580" class="ui-widget-content left"><a href="'.$armory->bnlink($tmp_charname, $tmp_servername, 'char').'">'.$testdata['name'].'</a></td>
				</tr>';
	$output .= '<tr>
					<td width="220" class="ui-widget-content left">Class</td>
					<td width="580" class="ui-widget-content left">'.$testdata['class'].' [eqdkp-classid: '.$armory->ConvertID($testdata['class'], 'int', 'classes').']</td>
				</tr>';
	$output .= '<tr>
					<td width="220" class="ui-widget-content left">Race</td>
					<td width="580" class="ui-widget-content left">'.$testdata['race'].' [eqdkp-raceid: '.$armory->ConvertID($testdata['race'], 'int', 'races').']</td>
				</tr>';
	$output .= '<tr>
					<td width="220" class="ui-widget-content left">Level</td>
					<td width="580" class="ui-widget-content left">'.$testdata['level'].'</td>
				</tr>';
	$output .= '<tr>
					<td width="220" class="ui-widget-content left">Gender</td>
					<td width="580" class="ui-widget-content left">'.$armory->ConvertID($testdata['gender'], 'int', 'gender').' [gender-id: '.$testdata['gender'].']</td>
				</tr>';
	$output .= '<tr>
					<td width="220" class="ui-widget-content left">PVP Arena Teams</td>
					<td width="580" class="ui-widget-content left">'.$testdata['pvp']['arenaTeams'][0]['name'].'</td>
				</tr>';
	$output .= '<tr>
					<td width="220" class="ui-widget-content left">Gilde</td>
					<td width="580" class="ui-widget-content left">'.$testdata['guild']['name'].'</td>
				</tr>';
	$output .= '<tr>
					<td width="220" class="ui-widget-content left">Last Update</td>
					<td width="580" class="ui-widget-content left">'.date('d.m.Y',($testdata['lastModified']/ 1000)).'</td>
				</tr>';
	$output .= '<tr>
					<td width="220" class="ui-widget-content left">LU Timestamp</td>
					<td width="580" class="ui-widget-content left">'.$testdata['lastModified'].'</td>
				</tr>';
	
	$output .= '</table></div>';
}else{
	$output .= '<b>WARNING: </b> '.$testdata['reason'];
}


$output .= '</body></html>';
echo $output;
?>