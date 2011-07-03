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

if ( !defined('EQDKP_INC') ){
	header('HTTP/1.0 404 Not Found');exit;
}

class PHPArmory
{
	public $version			= '5.a1';
	public $build			= '03072011a';
	private $caching		= true;
	private $cachingtime	= 12;	// in hours
	private $xml_timeout	= 20;	// seconds to pass for timeout
	private $user_agent		= 'Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.8.1.2) Gecko/20070220 Firefox/2.0.0.2';
	private $serverloc		= 'us';
	private $serverlang		= 'en_EN';
	
	protected $convert		= array(
		'classes' => array(
			1		=> '10',	// warrior
			2		=> '5',		// paladin
			3		=> '3',		// hunter
			4		=> '7',		// rogue
			5		=> '6',		// priest
			6		=> '1',		// DK
			7		=> '8',		// shaman
			8		=> '4',		// mage
			9		=> '9',		// warlock
			11		=> '2',		// druid
		),
		'races' => array(
			'1'		=> 2,		// human
			'2'		=> 7,		// orc
			'3'		=> 3,		// dwarf
			'4'		=> 4,		// night elf
			'5'		=> 6,		// undead
			'6'		=> 8,		// tauren
			'7'		=> 1,		// gnome
			'8'		=> 5,		// troll
			'9'		=> 12,		// Goblin
			'10'	=> 10,		// blood elf
			'11'	=> 9,		// draenei
			'22'	=> 11,		// Worgen
		),
		'gender' => array(
			'0'		=> 'Male',
			'1'		=> 'Female',
		),
	);
	
	protected $links		= array(
		'eu'	=> 'http://eu.battle.net',
		'us'	=> 'http://www.battle.net',
		'kr'	=> 'http://kr.battle.net',
		'cn'	=> 'http://cn.battle.net',
		'tw'	=> 'http://tw.battle.net',
	);
	private $serverlocs  = array(
		'eu'	=> 'EU',
		'us'	=> 'US',
		'cn'	=> 'CN',
		'kr'	=> 'KR',
		'tw'	=> 'TW',
	);
	private $armlanguages  = array(
		'de_de'	=> 'Deutsch',
		'en_gb'	=> 'English (EU)',
		'en_us'	=> 'English (US)',
		'es_mx'	=> 'Español (AL)',
		'es_es'	=> 'Español (EU)',
		'fr_fr'	=> 'Français',
		'ru_ru'	=> 'Russian',
		'ko_kr'	=> 'Korean',
	);
	private $converts = array();

	/**
	* Initialize the Class
	* 
	* @param $loadme	Which modules to load: items, chars
	* @param $lang		Which language to import
	* @return bool
	*/
	public function __construct($lang='en_en'){
		global $pcache, $user;
		$this->armoryLang	= $lang;
		$this->pcache		= $pcache;
	}

	public function setSettings($setting){
		if(isset($setting['loc'])){
			$this->serverloc	= $setting['loc'];
		}
		if(isset($setting['lang'])){
			$this->serverlang	= $setting['lang'];
		}
	}
	
	public function getServerLink(){
		return $this->links[$this->serverloc];
	}

	public function language($string){
		global $user;
		return $user->lang($string);
	}

	/**
	* Get the Server Location
	*
	* @return string output
	*/
	public function GetLocs(){
		return $this->serverlocs;
	}

	/**
	* Get the Server Location
	* 
	* @param $loc	  		Location of Server
	* @return string output
	*/
	public function GetLink($loc=''){
		if($loc){
			return $this->links[$loc];
		}else{
			return $this->links;
		}
	}

	/**
	* Generate Link to Armory
	* 
	* @param $loc			Location of Server
	* @param $user			Name of the User
	* @param $server		Name of the WoW Server
	* @param $mode			Which page to open? (char, talent1, talent2, statistics, reputation, guild, achievements)
	* @param $guild			Name of the guild
	* @return string		output
	*/
	public function Link($user, $server, $mode='char', $guild=''){
		// init the variables
		$myGuild 	= $this->ConvertInput($guild);
		$myServer	= $this->ConvertInput($server, true);
		$myUser		= $this->ConvertInput($user);
		$linkprfx	= 'http://'.$this->serverloc.'.battle.net/wow/en/';

		// Generate the Output
		switch ($mode) {
			case 'char':
				return $linkprfx.'character/'.$myServer.'/'.$myUser.'/simple';break;
			case 'talent1':
				return $linkprfx.'character/'.$myServer.'/'.$myUser."/talent/primary";break;
			case 'talent2':
				return $linkprfx.'character/'.$myServer.'/'.$myUser."/talent/secondary";break;
			case 'statistics':
				return $linkprfx.'character/'.$myServer.'/'.$myUser."/statistic";break;
			case 'reputation':
				return $linkprfx.'character/'.$myServer.'/'.$myUser."/reputation/";break;
			case 'achievements':
				return $linkprfx.'character/'.$myServer.'/'.$myUser."/achievement";break;
			case 'guild':
				return $linkprfx."guild/".$myServer."/".$myGuild."/roster";break;
			case 'character-feed':
				return $linkprfx.'character/'.$myServer.'/'.$myUser."/feed";break;
			case 'character-feed-atom':
				return $this->links[$this->serverloc].'character-feed.atom??'.$myServer.'&'.$myUser;break;
		}
	}

	/**
	* Get the Server Languages
	* 
	* @return string output
	*/
	public function GetLanguages(){
		return $this->armlanguages;
	}

	/**
	* Clean the Servername if taken from Database
	* 
	* @return string output
	*/
	public function cleanServername($server){
		return html_entity_decode($server,ENT_QUOTES,"UTF-8");
	}

	/**
	* Output a value or 0 for int, value or '' for string
	* 
	* @param $input 
	* @param $type		int/string
	* @return string/int output
	*/
	public function ValueOrNull($input, $type='int'){
		return ($input) ? $input : (($type == 'int') ? 0 : '');
	}

	/**
	* Convert from Armory ID to EQDKP Id or reverse
	* 
	* @param $name			name/id to convert
	* @param $type			int/string?
	* @param $cat				category (classes, races, months)
	* @param $ssw				if set, convert from eqdkp id to armory id
	* @return string/int output
	*/
	public function ConvertID($name, $type, $cat, $ssw=''){
		if($ssw){
			if(!is_array($this->converts[$cat])){
				$this->converts[$cat] = array_flip($this->convert[$cat]);
			}
			return ($type == 'int') ? $this->converts[$cat][(int) $name] : $this->converts[$cat][$name];
		}else{
			return ($type == 'int') ? $this->convert[$cat][(int) $name] : $this->convert[$cat][$name];
		}
	}

	/**
	* Prepare a string for beeing sent to armory
	* 
	* @param $input 
	* @return string output
	*/
	public function ConvertInput($input, $removeslash=false){
		if($removeslash){
			// new servername convention: mal'ganis = malganis
			return stripslashes(str_replace("'", "", $input));
		}else{
			return stripslashes(rawurlencode($input));
		}
	}

	/**
	* Check if Armory is online or not
	* 
	* @param $url URL to check if online 
	* @return true/array with error information
	*/
	public function CheckOnlineStatus($url='wowarmory.com'){
		if (@fsockopen($url, 80, $errno, $errstr, 30)){
			return array($errstr,$errno);
		}else{
			return true;
		}
	}

	/**
	* Convert Armory Date in Timestamp
	* 
	* @param $armdate Input Date
	* @return Timestamp
	*/
	public function Date2Timestamp($armdate){
		$tmpdate		= explode(" ", trim($armdate));
		$datenames		= array_flip($this->language('time_monthnames'));
		$datename		= ($datenames[$tmpdate[1]]) ? ($datenames[$tmpdate[1]]+1) : $tmpdate[1];
		return strtotime(substr($tmpdate[2].'-'.$datename.'-'.$tmpdate[0], 0, -1));
	}

	/**
	* Write JSON to Cache
	* 
	* @param	$json			XML string
	* @param	$filename	filename of the cache file
	* @return --
	*/
	protected function CacheJSON($json, $filename){
		if($this->caching){
			if(is_object($this->pcache)){
				$this->pcache->putContent($this->pcache->FolderPath('armory', 'cache', false).md5($filename), $json);
			}else{
				file_put_contents('data/'.md5($filename), $json);
			}
		}
	}

	/**
	* get the cached JSON if not outdated & available
	* 
	* @param	$filename	filename of the cache file
	* @param	$force		force an update of the cached json file
	* @return --
	*/
	protected function get_CachedJSON($filename, $force=false){
		if(!$this->caching){return '';}
		$data_ctrl = false;
		$rfilename	= (is_object($this->pcache)) ? $this->pcache->FolderPath('armory', 'cache').md5($filename) : 'data/'.md5($filename);
		if(is_file($rfilename)){
			$data_ctrl	= (!$force && (filectime($rfilename)+(3600*$this->cachingtime)) > time()) ? true : false;
		}
		return ($data_ctrl) ? @file_get_contents($rfilename) : false;
	}

	/**
	* Fetch the Data from URL
	* 
	* @param $url URL to Download
	* @return json
	*/
	protected function read_url($url, $lang=''){
	 	global $urlreader, $eqdkp_root_path;
		if($lang){
			$this->armoryLang = $lang;
		}
		if(!is_object($urlreader)){
			$mpath = ($eqdkp_root_path) ? $eqdkp_root_path.'core/': '';
			include($mpath.'urlreader.class.php');
			$urlreader	= new urlreader();
		}
		return $urlreader->GetURL($url, $this->armoryLang);
	}

	/**
	* Generate a hidden input field
	* 
	* @param	$name		name of the field
	* @param	$input	Value of the field
	* @return input field
	*/
	public function genHiddenInput($name, $input){
		return "<input name='".$name."' value=\"".$input."\" type='hidden' />\n";
	}
}
?>