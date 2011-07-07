<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2007
 * Date:		$Date$
 * -----------------------------------------------------------------------
 * @author		$Author$
 * @copyright	2006-2011 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev$
 * 
 * $Id$
 *
 * Based on the new battlenet API, see documentation: http://blizzard.github.com/api-wow-docs/
 */

if ( !defined('EQDKP_INC') ){
	header('HTTP/1.0 404 Not Found');exit;
}

class bnetArmory
{
	private $version		= '5.0';
	private $build			= '$Rev$';
	private $caching		= true;
	private $cachingtime	= 12;	// in hours
	private $serverloc		= 'us';
	
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

	private $serverlocs		= array(
		'eu'	=> 'EU',
		'us'	=> 'US',
		'kr'	=> 'KR',
		'tw'	=> 'TW',
	);
	private $converts		= array();

	/**
	* Initialize the Class
	* 
	* @param $lang		Which language to import
	* @return bool
	*/
	public function __construct($lang='en_en'){
		global $pcache, $user;
		$this->pcache		= $pcache;
	}

	public function setSettings($setting){
		if(isset($setting['loc'])){
			$this->serverloc	= $setting['loc'];
		}
	}

	public function getVersion(){
		return $this->version.((preg_match('/\d+/', $this->build, $match))? '#'.$match[0] : '');
	}

	public function getServerLink(){
		return 'http://'.$this->serverloc.'.battle.net';
	}

	public function language($string){
		global $user;
		return $user->lang($string);
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
	public function bnlink($user, $server, $mode='char', $guild=''){
		// init the variables
		$myGuild 	= $this->ConvertInput($guild);
		$myServer	= $this->ConvertInput($server, true);
		$myUser		= $this->ConvertInput($user);
		$linkprfx	= $this->getServerLink().'/wow/en/';

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
				return $this->getServerLink().'character-feed.atom??'.$myServer.'&'.$myUser;break;
		}
	}

	/**
	* Fetch character information
	* 
	* @param $user		Character Name
	* @param $realm		Realm Name
	* @param $force		Force the cache to update?
	* @return bol
	*/
	public function character($user, $realm, $force=false){
		$realm = $this->cleanServername($realm);
		$wowurl = $this->getServerLink().'/api/wow/character/'.$this->ConvertInput($realm).'/'.$this->ConvertInput($user).'?fields=guild,stats,talents,items,reputation,titles,professions,appearance,companions,mounts,pets,achievements,progression';
		if(!$json	= $this->get_CachedJSON('chardata_'.$user.$realm, $force)){
			$json	= $this->read_url($wowurl);
			$this->CacheJSON($json, 'chardata_'.$user.$realm);
		}
		$chardata	= json_decode($json, true);
		$errorchk	= $this->CheckIfError($chardata);
		return (!$errorchk) ? $chardata: $errorchk;
	}

	/**
	* Create full character Icon Link
	* 
	* @param $thumb		Thumbinformation returned by battlenet JSON feed
	* @return string
	*/
	public function characterIcon($thumb){
		return $this->getServerLink().'/static-render/'.$this->serverloc.'/'.$thumb;
	}

	/**
	* Fetch guild information
	* 
	* @param $user		Character Name
	* @param $realm		Realm Name
	* @param $force		Force the cache to update?
	* @return bol
	*/
	public function guild($guild, $realm, $force=false){
		$realm = $this->cleanServername($realm);
		$wowurl = $this->getServerLink().'/api/wow/guild/'.$this->ConvertInput($realm).'/'.$this->ConvertInput($guild).'?fields=members,achievements';
		if(!$json	= $this->get_CachedJSON('guilddata_'.$guild.$realm, $force)){
			$json	= $this->read_url($wowurl);
			$this->CacheJSON($json, 'guilddata_'.$guild.$realm);
		}
		$chardata	= json_decode($json, true);
		$errorchk	= $this->CheckIfError($chardata);
		return (!$errorchk) ? $chardata: $errorchk;
	}

	/**
	* Fetch realm information
	* 
	* @param $realm		Realm Name
	* @param $force		Force the cache to update?
	* @return bol
	*/
	public function realm($realms, $force=false){
		$realms = (is_array($realms)) ? $realms : array();
		$wowurl = $this->getServerLink().'/api/wow/realm/status?realms='.implode(",",$realms);
		if(!$json	= $this->get_CachedJSON('realmdata_'.implode("",$realms), $force)){
			$json	= $this->read_url($wowurl);
			$this->CacheJSON($json, 'realmdata_'.implode("",$realms));
		}
		$realmdata	= json_decode($json, true);
		$errorchk	= $this->CheckIfError($realmdata);
		return (!$errorchk) ? $realmdata: $errorchk;
	}

	/**
	* Fetch item information
	* 
	* @param $itemid	battlenet Item ID
	* @param $force		Force the cache to update?
	* @return bol
	*/
	public function item($itemid, $force=false){
		$wowurl = $this->getServerLink().'/api/wow/data/item/'.$itemid;
		if(!$json	= $this->get_CachedJSON('itemdata_'.$itemid, $force)){
			$json	= $this->read_url($wowurl);
			$this->CacheJSON($json, 'itemdata_'.$itemid);
		}
		$itemdata	= json_decode($json, true);
		$errorchk	= $this->CheckIfError($itemdata);
		return (!$errorchk) ? $itemdata: $errorchk;
	}
	

	/**
	* Check if the JSON is an error result
	* 
	* @param $data		XML Data of Char
	* @return error code
	*/
	protected function CheckIfError($data){
		$status	= (isset($data['status'])) ? $data['status'] : false;
		$reason	= (isset($data['reason'])) ? $data['reason'] : false;
		$error = '';
		if($status){
			return array('status'=>$status,'reason'=>$reason);
		}else{
			return false;
		}
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
	* Convert from Armory ID to EQDKP Id or reverse
	* 
	* @param $name			name/id to convert
	* @param $type			int/string?
	* @param $cat			category (classes, races, months)
	* @param $ssw			if set, convert from eqdkp id to armory id
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
	* Write JSON to Cache
	* 
	* @param	$json		XML string
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
	protected function read_url($url){
		global $urlreader, $eqdkp_root_path;
		if(!is_object($urlreader)){
			$mpath = ($eqdkp_root_path) ? $eqdkp_root_path.'core/': '';
			include($mpath.'urlreader.class.php');
			$urlreader	= new urlreader();
		}
		return $urlreader->GetURL($url, '');
	}

	/**
	* Check if an error occured
	* 
	* @return error
	*/
	public function CheckError(){
		return ($this->error) ? $this->error : false;
	}
}
?>