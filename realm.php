<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2011
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
// Loas phpinfo()
if($_GET['info'] == 'true'){
	phpinfo();die();
}

// fallback config
$tmp_servername	= (@$_GET['realm'])		? urldecode(stripslashes($_GET['realm']))	: "Antonidas,Mal'Ganis";
$tmp_force		= (@$_GET['force'])		? true										: false;

$output = '';

// init the required plus functions
$eqdkp_root_path = '';

require_once('classes/plus/urlreader.class.php');
require_once('classes/plus/core.functions.php');
$urlreader	= new urlreader();

// load the armory class
include_once('classes/armory.class.php');

$armory		= new bnetArmory();
$armory->setSettings(array('loc'=>$tmp_loc, 'lang'=> $tmp_language));
$servernames = explode(",", $tmp_servername);
$testdata	= $armory->realm($servernames, $tmp_force);
$get_method	= ($urlreader->get_method()) ? $urlreader->get_method() : 'Cached';

$output .= "<b>battle.net Armory Class Tester - REALM</b> ( bnetArmory [".$armory->getVersion()."]), connection method: <span style='color:red;'>".$get_method."</span><br/><br/>";


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