<?php
 /*
 * Project:     eqdkpPLUS Libraries: Armory
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		    http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       2007
 * Date:        $Date: 2009-06-21 21:22:18 +0200 (Sun, 21 Jun 2009) $
 * -----------------------------------------------------------------------
 * @author      $Author: wallenium $
 * @copyright   2008 Simon (Wallenium) Wallmann
 * @link        http://eqdkp-plus.com
 * @package     libraries:armory
 * @version     $Rev: 5091 $
 * 
 * $Id: ArmoryChars.class.php 5091 2009-06-21 19:22:18Z wallenium $
 */


// this class is PHP5 only... testing OOP
if (version_compare(phpversion(), "5.0.0", "<")){
  die('You need PHP5 or higher to use this class');
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
	public function GetCharacterData($user, $realm, $loc='us', $lang='en_us', $parse=true){
	  $wowurl = $this->links[$loc].'character-sheet.xml?r='.$this->ConvertInput($realm).'&n='.$this->ConvertInput($user);
		if($parse == true){
			$xml = simplexml_load_string($this->read_url($wowurl, $lang));
			if(is_object($xml)){
				return $xml->xpath("/page/characterInfo");
			}else{
				return 0;	
			}
		}else{
			return $this->read_url($wowurl, $lang);
		}
	}
	
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
  * @return bool
  */
	public function GetGuildMembers($guild, $realm, $loc='us', $minLevel, $clsFilter, $lang='en_us', $parse=true) {
    $wowurl = $this->links[$loc]."guild-info.xml?r=".$this->ConvertInput($realm)."&n=".$this->ConvertInput($guild)."&p=1";
		$xmldata = $this->read_url($wowurl, $lang);
		if($parse == true){
			$xml = simplexml_load_string($xmldata);
			return $this->getCharacterList($xml, $minLevel, $clsFilter);
		}else{
			return $xmldata;
		}
	}
	
	/**
  * Build Character List with some details out of Guild List
  * 
  * @param $xml XML Input of Armory
  * @param $minLevel List users higher than this Level only
  * @param $class List users of that Class only
  * @return bol List Array
  */
	private function getCharacterList($xml, $minLevel, $class) {
		$cList = array();
		if(is_object($xml)){
			$characters = $xml->xpath("/page/guildInfo/guild/members/character");
			if(sizeof($characters) == 0) {
				echo "<font style='color: #f00; font-weight: bold;'>Warning! No characters found!</font><p />";
			}else{
	  		foreach($characters as $character) {
	  			$attribs = $character->attributes();
	        // end of encoding problem fix
	  			if((int)($attribs["level"]) >= $minLevel) {
	  				if (!$class || $class == $attribs["classId"]) {
	  					$cList[] = array(
	  						'name'						=> $attribs['name'],
	  						'level'						=> $attribs['level'],
	  						'rank'						=> $attribs['rank'],
	  						'gender'					=> $attribs['genderId'],
	  						'raceid'					=> $attribs['raceId'],
	  						'classid'					=> $attribs['classId'],
	  						'eqdkp_classid'		=> $this->ConvertID($attribs['classId'], 'int', 'classes'),
	  						'eqdkp_raceid'		=> $this->ConvertID($attribs['raceId'], 'int', 'races'),
	  					);
	  				}
	  			}
	  		} // end of foreach
	  	} // end of check
			return $cList;
		}else{
			return 'no_membeinfo';
		} // end of is_object
	}
	
	/**
  * Check if a char exists, if not --> error Code
  * 
  * @param $chardata		XML Data of Char
  * @return error code
  */
  protected function CheckIfChar($chardata){
    $error = '';
    if(!is_object($chardata)){
    	$error = 'old_char';
    }elseif($chardata->attributes()){
      foreach($chardata->attributes() as $a=>$b){
        if($a === "errCode"){
          $error = 'no_char';
        }
      }
    }else{
      if(empty($chardata->characterTab)){
        $error = 'old_char';
      }
    }
    return $error;
  }
	
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
    return $this->links[$loc]."images/portraits/$dir/{$genderid}-{$raceid}-{$classid}.gif";
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
		$genderid = $this->ConvertID($gender, 'string', 'gender', true);
		$raceid		= $this->ConvertID($race, 'int', 'races', true);
		$classid	= $this->ConvertID($class, 'int', 'classes', true);
		return $this->getCharacterIcon($loc, $level, $genderid, $raceid, $classid);
	}
	
	/**
  * Build Character Detail Array
  * 
  * @param $xml XML Input of Armory
  * @return bol List Array
  */
	public function BuildMemberArray($chardata, $loc='us'){
		$dataarray = $memberarray = array();
		$myerror = $this->CheckIfChar($chardata);
		
		if(!$myerror){
  		foreach($chardata->character->attributes() as $a => $b) {
  		// This is an ugly workaround for an encoding error in the armory
  		  if ( substr($b ,0,1) == 'J' && substr($b ,-3) == 'ger' ) {
          $b = utf8_encode('Jäger');
        }
        if ( substr($b ,0,1) == 'M' && substr($b ,-6) == 'nnlich' ) {
          $b = utf8_encode('Männlich');
        }
        // end of encoding problem fix
      	$dataarray[strtolower($a)] = $b;

      	// Add the enflish ones:
      	if($a == 'classId'){
          $dataarray['class_eqdkp'] = $this->ConvertID($b, 'int', 'classes');
        }
        if($a == 'raceId'){
          $dataarray['race_eqdkp'] = $this->ConvertID($b, 'int', 'races');
        }
  		}
  		
  		// Specs
  		$talentspecs = array();
  		foreach($chardata->characterTab->talentSpecs as $a => $b) {
      	$talentspecs[strtolower($a)] = $b;
  		}
  		foreach($talentspecs['talentspecs']->children() as $myTalents){
  		  $dataarray['spec'.$myTalents['group']] = $myTalents;
      }
      $dataarray['dualspec'] = (count($dataarray) > 1) ? true : false;
      
      // Professions
  		foreach($chardata->characterTab->professions as $a => $b) {
      	$dataarray[strtolower($a)] = $b;
  		}
  		
  		// Resistances
  		foreach($chardata->characterTab->resistances as $a => $b) {
      	$dataarray[strtolower($a)] = $b;
  		}
  		
  		// Base Stats
  		foreach($chardata->characterTab->baseStats as $a => $b) {
      	$dataarray[strtolower($a)] = $b;
  		}
  		
  		// Character Bars
  		foreach($chardata->characterTab->characterBars as $a => $b) {
      	$dataarray[strtolower($a)] = $b;
  		}
  		
  		// Glyphs
  		foreach($chardata->characterTab->glyphs as $a => $b) {
      	$dataarray[strtolower($a)] = $b;
  		}
  		
  		// Items
  		foreach($chardata->characterTab->items as $a => $b) {
      	$dataarray[strtolower($a)] = $b;
  		}
  		
  		// Achievements: Overview ($dataarray['achievements']['main']['totalpoints'];)
  		foreach($chardata->summary->c->attributes() as $a=>$b) {
  			$dataarray['achievements']['main'][strtolower($a)] = $b;
  		}
  		
  		// Achievements:
  		$idd = 1;
  		foreach($chardata->summary as $b) {
  			foreach($b as $c=>$d){
  				if($c!='c'){
  					// We will build an array...
  					$dataarray['achievements']['detail'][$idd]['main']	= $d->attributes();
  					$dataarray['achievements']['detail'][$idd]['child'] = $d->c->attributes();
  					$idd++;
  				}
  			}
  		}
  		
  		// Char Icon
  		if($dataarray['level'] && $dataarray['classid'] && $dataarray['genderid'] && $dataarray['raceid']){
  			$dataarray['ac_charicon'] = $this->getCharacterIcon($loc, $dataarray['level'], $dataarray['genderid'], $dataarray['raceid'], $dataarray['classid']);
  		}
  		
  		// Honored kills
  		$dataarray['honoredkills'] = $chardata->characterTab->pvp->lifetimehonorablekills['value'];
  		return $dataarray;
		}else{
      return $myerror;
    }
	}
}
