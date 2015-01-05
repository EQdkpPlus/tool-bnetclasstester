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

// Loas phpinfo()
if($_GET['info'] == 'true'){
	phpinfo();die();
}

// fallback config
$tmp_servername	= (@$_GET['realm'])		? urldecode(stripslashes($_GET['realm']))	: "Antonidas,Mal'Ganis";
$tmp_force		= (@$_GET['force'])		? true										: false;
$tmp_loc		= (@$_GET['loc'])		? urldecode($_GET['loc'])					: $default_loc;
$tmp_language	= (@$_GET['lang'])		? urldecode($_GET['lang'])					: $default_language;
$api_version	= (@$_GET['apiversion'])? urldecode($_GET['apiversion'])			: $api_version;

$output = '';

// init the required plus functions
$eqdkp_root_path = '';

require_once('classes/plus_url_fetcher.class.php');
require_once('classes/core.functions.php');
$puf	= new urlFetcher();

// load the armory class
if($api_version < '2'){
	include_once('objects/bnet_armory_old.class.php');
}else{
	include_once('objects/bnet_armory.class.php');
}

$armory		= new bnet_armory($tmp_loc, $tmp_language, $api_key);
$servernames = explode(",", $tmp_servername);
$testdata	= $armory->realm($servernames, $tmp_force);
$get_method	= ($puf->get_method()) ? $puf->get_method() : 'Cached';

$output .= "<b>battle.net Armory Class Tester - REALM</b> ( bnetArmory [".$armory->getVersion()."]), connection method: <span style='color:red;'>".$get_method."</span><br/><br/>";

$output .= 'GET Parameters: realm [Antonidas], loc [eu], lang [de_DE], force [false]<br/><br/>';


if($_GET['array'] == 'true'){
	d($testdata);die();
}

$output .= '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de" lang="de">
		<head>
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
			<title>battle.net Armory Class Tester '.$armory->getVersion().' - REALM</title>
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

if(!isset($testdata['status'])){
	// Header
	$output .= '<div class="ui-grid ui-widget ui-widget-content ui-corner-all">
					<div class="ui-grid-header ui-widget-header ui-corner-top">Realm Status</div>';
	$output .= '<table class="ui-grid-content ui-widget-content">';
	$output .= '<tr><td class="ui-state-default" >Name</th>';
	$output .= '<th class="ui-state-default" width="50">Type</th>';
	$output .= '<th class="ui-state-default" width="50">Queue</th>';
	$output .= '<th class="ui-state-default" width="150">Population</th>';
	$output .= '<th class="ui-state-default" width="60">Status</th>';
	$output .= '</tr>';
	
	foreach($testdata['realms'] as $realmdata){
		$output .= '<tr>';
		$output .= '<td class="ui-widget-content left">'.$realmdata['name'].'</td>';
		$output .= '<td class="ui-widget-content">'.$realmdata['type'].'</td>';
		$output .= '<td class="ui-widget-content">'.(($realmdata['queue']) ? $realmdata['queue'] : '--').'</td>';
		$output .= '<td class="ui-widget-content">'.$realmdata['population'].'</td>';
		$output .= '<td class="ui-widget-content">'.(($realmdata['status'] == 1) ? '<span style="color:green;">online</span' : '<span style="color:red;">offline</span>').'</td>';
		$output .= '</tr>';
	}
	$output .= "</table></div><br/><br/>";
}else{
	$output .= '<b>WARNING: </b> '.$testdata['reason'];
}

$output .= '</body></html>';
echo $output;
?>