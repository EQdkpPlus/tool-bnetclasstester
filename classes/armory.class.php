<?php
 /*
 * Project:     eqdkpPLUS Libraries: Armory
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		    http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       2007
 * Date:        $Date: 2010-05-29 12:25:07 +0200 (Sat, 29 May 2010) $
 * -----------------------------------------------------------------------
 * @author      $Author: wallenium $
 * @copyright   2008 Simon (Wallenium) Wallmann
 * @link        http://eqdkp-plus.com
 * @package     libraries:armory
 * @version     $Rev: 7929 $
 * 
 * $Id: armory.class.php 7929 2010-05-29 10:25:07Z wallenium $
 */

if ( !defined('EQDKP_INC') ){
	header('HTTP/1.0 404 Not Found');exit;
}

class PHPArmory
{
	public $version				= '4.0.0';
	public $build					= '30052010a';
	private $caching			= true;
	private $cachingtime	= 12;		// in hours
	private $xml_timeout	= 20;  	// seconds to pass for timeout
	private $user_agent		= 'Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.8.1.2) Gecko/20070220 Firefox/2.0.0.2';
	
	protected $links		 = array(
        										'eu'		=> 'http://eu.wowarmory.com/',
        										'us'		=> 'http://www.wowarmory.com/',
        										'kr'    => 'http://kr.wowarmory.com/',
        										'cn'    => 'http://cn.wowarmory.com/',
        										'tw'    => 'http://tw.wowarmory.com/',
        									);
	private $serverlocs  = array(
                            'eu'    => 'EU',
                            'us'    => 'US',
                            'cn'    => 'CN',
                            'kr'    => 'KR',
                            'tw'    => 'TW',
                          );
   private $armlanguages  = array(
                            'de_de' => 'Deutsch',
                            'en_gb' => 'English (EU)',
                            'en_us' => 'English (US)',
                            'es_mx' => 'Español (AL)',
                            'es_es' => 'Español (EU)',
                            'fr_fr' => 'Français',
                            'ru_ru' => 'Russian',
                            'ko_kr' => 'Korean',
                          );
   private $converts = array();
	
	/**
  * Initialize the Class
  * 
  * @param $loadme    Whcih modules to load: items, chars
  * @param $lang      Which language to import
  * @return bool
  */
	function __construct($lang='en_en'){
	 global $ac_trans, $pcache, $xmltools;
		$this->armoryLang   = $lang;
		require('armory.convert.php');
		$this->convert      = $ac_trans;
		$this->pcache				= $pcache;
		$this->xmlTools			= $xmltools;
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
  * @param $loc	  		Location of Server
  * @param $user    	Name of the User
  * @param $server		Name of the WoW Server
  * @param $mode			Which page to open? (char, talent1, talent2, statistics, reputation, guild, achievements)
  * @param $guild			Name of the guild
  * @return string output
  */
  public function Link($loc, $user, $server, $mode='char', $guild=''){
		// init the variables
		$myGuild 	= ($guild) ? '&gn='.$this->ConvertInput($guild) : '';
		$myServer	= 'r='.$this->ConvertInput($server);
		$myUser		= 'cn='.$this->ConvertInput($user);
		
		// Generate the Output
		switch ($mode) {
	    case 'char':
	        $url = $this->links[$loc].'character-sheet.xml?'.$myServer.'&'.$myUser.$myGuild;
	        break;
	    case 'talent1':
	        $url = $this->links[$loc].'character-talents.xml?'.$myServer.'&'.$myUser.$myGuild.'&group=1';
	        break;
	    case 'talent2':
	        $url = $this->links[$loc].'character-talents.xml?'.$myServer.'&'.$myUser.$myGuild.'&group=2';
	        break;
	    case 'statistics':
	        $url = $this->links[$loc].'character-statistics.xml?'.$myServer.'&'.$myUser.$myGuild;
	        break;
	    case 'reputation':
	        $url = $this->links[$loc].'character-reputation.xml?'.$myServer.'&'.$myUser.$myGuild;
	        break;
	    case 'achievements':
	        $url = $this->links[$loc].'character-achievements.xml?'.$myServer.'&'.$myUser.$myGuild;
	        break;
	    case 'guild':
	        $url = $this->links[$loc].'guild-info.xml?'.$myServer.'&'.$myUser.$myGuild;
	        break;
		}
		return $url;
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
	 public function ConvertInput($input){
    return stripslashes(rawurlencode($input));
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
		$tmpdate = explode(" ", trim($armdate));
    return strtotime(substr($tmpdate[2].'-'.$tmpdate[1].'-'.$tmpdate[0], 0, -1));
  }
	
	/**
  * Write XML to Cache
  * 
  * @param	$xml			XML string
  * @param	$filename	filename of the cache file
  * @return --
  */
	protected function CacheXML($xml, $filename){
		if($this->caching){
			if(is_object($this->pcache)){
				$this->pcache->putContent($xml, $this->pcache->FolderPath('armory/', 'eqdkp').md5($filename));
			}else{
				file_put_contents('data/'.md5($filename), $xml);
			}
		}
	}
	
	/**
  * get the cached xml if not outdated & available
  * 
  * @param	$filename	filename of the cache file
  * @param	$force		force an update of the cached xml file
  * @return --
  */
	protected function get_CachedXML($filename, $force=false){
		if(!$this->caching){return '';}
		$rfilename	= (is_object($this->pcache)) ? $this->pcache->FolderPath('armory/', 'eqdkp').md5($filename) : 'data/'.md5($filename);
		$data_ctrl	= (!$force && (filectime($rfilename)+(3600*$this->cachingtime)) > time()) ? true : false;
		return ($data_ctrl) ? @file_get_contents($rfilename) : false;
	}
	
	/**
  * Fetch the Data from URL
  * 
  * @param $url URL to Download
  * @return xml
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
		return "<input name='".$name."' value=\"".$input."\" type='hidden'>\n";
	}
}
?>
