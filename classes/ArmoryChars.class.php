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

require('armory.class.php');

class ArmoryChars extends PHPArmory
{
	
	/**
	* Download Character Information
	* 
	* @param $user Character Name
	* @param $realm Realm Name
	* @param $loc Server Location (us/eu)
	* @param $lang Language of output (en_us/de_de/fr_fr) 
	* @param $parse Parsed Output (true) or XML (false)
	* @return bol
	*/
	public function character($user, $realm, $lang='en_us', $force=false){
		$realm = $this->cleanServername($realm);
		$wowurl = $this->getServerLink().'/api/wow/character/'.$this->ConvertInput($realm).'/'.$this->ConvertInput($user).'?fields=reputation,primary skills, secondary skills,talents';
		if(!$json	= $this->get_CachedJSON($user.$realm, $force)){
			$json	= $this->read_url($wowurl, $this->serverlang);
			$this->CacheJSON($json, $user.$realm);
		}
		$chardata	= json_decode($json, true);
		$errorchk	= $this->CheckIfChar($chardata);
		if(!$errorchk){
			return $chardata;
		}else{
			return $errorchk;
		}
	}

	public function charicon($thumb){
		return $this->getServerLink().'/static-render/eu/'.$thumb;
	}

	/**
	* Check if a char exists, if not --> error Code
	* 
	* @param $chardata		XML Data of Char
	* @return error code
	*/
	protected function CheckIfChar($chardata){
		$status	= (isset($chardata['status'])) ? $chardata['status'] : false;
		$reason	= (isset($chardata['reason'])) ? $chardata['reason'] : false;
		$error = '';
		if($status){
			return array('status'=>$status,'reason'=>$reason);
		}else{
			return false;
		}
	}

	/**
	* Download Achievement Information
	* 
	* @param $category Category name
	* @param $user Character Name
	* @param $realm Realm Name
	* @param $loc Server Location (us/eu)
	* @param $lang Language of output (en_us/de_de/fr_fr) 
	* @param $parse Parsed Output (true) or XML (false)
	* @return bol
	*/
	/*public function GetAchievementData($category, $user, $realm, $loc='us', $lang='en_us', $parse=false, $asarray=false){
		$realm = $this->cleanServername($realm);
		$wowirl	= $this->links[$loc].'/api/wow/character/'.$this->ConvertInput($realm).'/'.$this->ConvertInput($user).'?fields=achievements';

		if(!$xmldata = $this->get_CachedJSON('achievement_'.$user.$realm, $force)){
			$xmldata	= $this->read_url($wowurl, $lang);
			$this->CacheJSON($xmldata, 'achievement_'.$user.$realm);
		}
		if($parse == true){
			$xml = simplexml_load_string($xmldata);
			if($asarray == true){
				$data = $this->xmlTools->simplexml2array($xml);
				if(is_array($data)){
					return $data;
				}else{
					return 0;
				}
			}else{
				if(is_object($xml)){
					return $xml;
				}else{
					return 0;
				}
			}
		}else{
			return $xmldata;
		}
	}*/

	/**
	* Download BossKill Information
	* 
	* @param $category Category name
	* @param $user Character Name
	* @param $realm Realm Name
	* @param $loc Server Location (us/eu)
	* @param $lang Language of output (en_us/de_de/fr_fr) 
	* @param $parse Parsed Output (true) or XML (false)
	* @return bol
	*/
	/*public function GetBossKillData($category, $user, $realm, $loc='us', $lang='en_us', $parse=false, $asarray=false){
		$realm = $this->cleanServername($realm);
		$wowurl = $this->links[$loc].'character-statistics.xml?r='.$this->ConvertInput($realm).'&cn='.$this->ConvertInput($user).'&c='.$category;
		if(!$xmldata = $this->get_CachedXML('bosskills_'.$user.$realm, $force)){
			$xmldata	= $this->read_url($wowurl, $lang);
			$this->CacheXML($xmldata, 'bosskills_'.$user.$realm);
		}
		if($parse == true){
			$xml = simplexml_load_string($xmldata);
			if($asarray == true){
				$data = $this->xmlTools->simplexml2array($xml);
				if(is_array($data)){
					return $data;
				}else{
					return 0;    
				}
			}else{
				if(is_object($xml)){   
					return $xml;
				}else{
					return 0;    
				}
			}
		}else{
			return $xmldata;
		}
	}*/

	
	/**
	* Download the Guild List of Armory
	* 
	* @param $guild Guildname on Realm
	* @param $realm Name of Realm
	* @param $loc Server Location (us/eu)
	* @param $minLevel Minimut Character Level to fetch
	* @param $clsFilter Classname, if set --> List users of that class only
	* @param $lang Language of output (en_us/de_de/fr_fr)
	* @param $parse Parsed Output (true) or XML (false)  
	* @param $asarray Parsed Output (true) or XML (false)  
	* @return bool
	*/
	/*public function GetGuildMembers($guild, $realm, $loc='us', $minLevel, $clsFilter, $rnkFilter, $lang='en_us', $force=false, $parse=true, $asarray=false) {
		$realm = $this->cleanServername($realm);
		$wowurl = $this->links[$loc]."guild-info.xml?r=".$this->ConvertInput($realm)."&n=".$this->ConvertInput($guild)."&p=1";
		if(!$xmldata = $this->get_CachedXML($guild.$realm, $force)){
			$xmldata	= $this->read_url($wowurl, $lang);
			$this->CacheXML($xmldata, $guild.$realm);
		}
		if($parse == true){
			$xml = simplexml_load_string($xmldata);
			return $this->getCharacterList($xml, $minLevel, $clsFilter, $rnkFilter);
		}else{
			if($asarray == true)
			{
				$xml = simplexml_load_string($xmldata);
				return $this->xmlTools->simplexml2array($xml);
			}else{				
				return $xmldata;
			}	
		}
	}*/
	
	/**
	* Build Character List with some details out of Guild List
	* 
	* @param $xml XML Input of Armory
	* @param $minLevel List users higher than this Level only
	* @param $class List users of that Class only
	* @return bol List Array
	*/
	/*private function getCharacterList($xml, $minLevel, $class, $rank) {
		$cList = array();
		if(is_object($xml)){
			$characters = $xml->xpath("/page/guildInfo/guild/members/character");
			if(sizeof($characters) == 0) {
				echo "<font style='color: #f00; font-weight: bold;'>Warning! No characters found!</font><p />";
			}else{
				$rank['sort'] = ($rank['sort']) ? $rank['sort'] : 1;
				// Load char by char
	  		foreach($characters as $character) {
	  			$attribs = $character->attributes();
	        // end of encoding problem fix
	  			if((int)($attribs["level"]) >= $minLevel) {
	  				if (!$class || $class == $attribs["classId"]) {
	  					if(!$rank['value'] || ($rank['sort'] == '2' && $rank['value'] == $attribs['rank'])
	  							or ($rank['sort'] == '1' && $rank['value'] >= $attribs['rank'])){
		  					$cList[] = array(
		  						'name'						=> $attribs['name'],
		  						'level'						=> $attribs['level'],
		  						'rank'						=> $attribs['rank'],
		  						'gender'					=> $attribs['genderId'],
		  						'raceid'					=> $attribs['raceId'],
		  						'classid'					=> $attribs['classId'],
		  						'eqdkp_classid'				=> $this->ConvertID($attribs['classId'], 'int', 'classes'),
		  						'eqdkp_raceid'				=> $this->ConvertID($attribs['raceId'], 'int', 'races'),
		  					);
	  					} // end rank filter
	  				} // end class filter
	  			} // end level filter
	  		} // end of foreach
	  	} // end of check
			return $cList;
		}else{
			return 'no_membeinfo';
		} // end of is_object
	}*/

	/**
	* Check if an error occured
	* 
	* @return error
	*/
	public function CheckError(){
		return ($this->error) ? $this->error : false;
	}

	/**
	* Get the Character Icons with Armory ID
	* 
	* @param $loc			Localization
	* @param $level		Level of the Char
	*	@param $gender	Gender ID
	* @param $race		Race ID
	* @param $class		Class ID
	* @return bol List Array
	*/
	private function getCharacterIcon($loc, $level, $genderid, $raceid, $classid){
		$dir = 'wow'.($level < '60' ? '-default' : ($level < '80' ? '-70' : '-80'));
		return $this->links[$loc]."_images/portraits/$dir/{$genderid}-{$raceid}-{$classid}.gif";
	}

	/**
	* Get the Character Icons by EQDKP-PLUS Variables
	* 
	* @param $loc			Localization
	* @param $level		Level of the Char
	*	@param $gender	Gender Male/Female
	* @param $race		Race ID of EQDKP
	* @param $class		Class ID of EQDKP
	* @return bol List Array
	*/
	public function getCharacterIconPLUS($loc, $level, $gender, $race, $class){
		$genderid	= $this->ConvertID($gender, 'string', 'gender', true);
		$raceid		= $this->ConvertID($race, 'int', 'races', true);
		$classid	= $this->ConvertID($class, 'int', 'classes', true);
		return $this->getCharacterIcon($loc, $level, $genderid, $raceid, $classid);
	}
	
}
?>