<?php
 /*
 * Project:     eqdkpPLUS Libraries: Armory
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		    http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       2007
 * Date:        $Date: 2009-08-14 01:48:55 +0200 (Fri, 14 Aug 2009) $
 * -----------------------------------------------------------------------
 * @author      $Author: wallenium $
 * @copyright   2008 Simon (Wallenium) Wallmann
 * @link        http://eqdkp-plus.com
 * @package     libraries:armory
 * @version     $Rev: 5645 $
 * 
 * $Id: armory.class.php 5645 2009-08-13 23:48:55Z wallenium $
 */

class PHPArmory
{
	var $version 	= '3.2.0';
	var $build		= '10082009a';
	
	private $xml_timeout = 20;  // seconds to pass for timeout
	private $user_agent  = 'Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.8.1.2) Gecko/20070220 Firefox/2.0.0.2';
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
  * @param $utf8test  Some Specialchars to test the Codeset
  * @param $loadme    Whcih modules to load: items, chars
  * @param $lang      Which language to import
  * @return bool
  */
	function __construct($utf8test, $lang='en_en'){
	 global $ac_trans;
		$this->stringIsUTF8 = ($this->isUTF8($utf8test) == 1) ? true : false;
		$this->armoryLang   = $lang;
		require('armory.convert.php');
		$this->convert      = $ac_trans;
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
    if($type == 'int'){
    	return ($input) ? $input : 0;
    }else{
    	return ($input) ? $input : '';
    }
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
    global $user;
	 	//$user 	= ($this->isUTF8) ? stripslashes(rawurlencode($user)) : stripslashes(rawurlencode(utf8_encode($user)));
		$out	= ($this->stringIsUTF8) ? stripslashes(rawurlencode($input)) : stripslashes(rawurlencode(mb_convert_encoding($input,"UTF-8",$user->lang['ENCODING'])));
    return $out;
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
	 global $ac_trans;
		$tmpdate = explode(" ", trim($armdate));

    $transmonthname = ($this->armoryLang && $this->armoryLang != 'en_en') ? $ac_trans['months'][$this->armoryLang][$this->UTF8tify($tmpdate[1])] : '';
    $tmpdate[1] = ($transmonthname) ? $transmonthname : $tmpdate[1];
    return strtotime($tmpdate[0].' '.$tmpdate[1].' '.$tmpdate[2]);
  }
	
	/**
  * Build Character Detail Array
  * 
  * @param $url URL to Download
  * @return xml
  */
	 protected function read_url($url, $lang=''){
	   if($lang){
      $this->armoryLang = $lang;
     }
	 // Try cURL first. If that isnt available, check if we're allowed to
	 // use fopen on URLs.  If that doesn't work, just die.
  	if (function_exists('curl_init')){
  		$curl = @curl_init($url);
  		$cookie = "cookieLangId=".$this->armoryLang.";";
  
  		@curl_setopt($curl, CURLOPT_COOKIE, $cookie);
  		@curl_setopt($curl, CURLOPT_USERAGENT, $this->user_agent);
  		@curl_setopt($curl, CURLOPT_TIMEOUT, $this->xml_timeout);
  		if (!(@ini_get("safe_mode") || @ini_get("open_basedir"))) {
      	@curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
      }
  		@curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);

  		$xml_data = @curl_exec($curl);
  		curl_close($curl);
  	}elseif (ini_get('allow_url_fopen') == 1 && function_exists('ini_set')){
  	   @ini_set('user_agent', $this->user_agent);  // set the useragent first. if not, you'll get the source....
  	   // its a bit tricky to get the cookie to work: http://www.testticker.de/tipps/article20060414003.aspx
       $cheader   = array("http" => array ("header" => "Cookie: cookieLangId=".$this->armoryLang.";\r\n"));
       $context   = @stream_context_create($cheader);
  	   $xml_data  = @file_get_contents($url, false, $context);
  	}else{
      // Thanks to Aki Uusitalo
  			$url_array = parse_url($url);
  			$fp = fsockopen($url_array['host'], 80, $errno, $errstr, 30); 
  			stream_set_timeout($fp, $this->xml_timeout);
  			$cookie = "cookieLangId=".$this->armoryLang.";";
  			if (!$fp){
  				die("cURL isn't installed, 'allow_url_fopen' isn't set and socket opening failed. Socket failed because: <br /><br /> $errstr ($errno)");
  			}else{
  				$out  = "GET " .$url_array['path']."?".$url_array['query']." HTTP/1.0\r\n";
  				$out .= "Host: ".$url_array['host']." \r\n";
  				$out .= "User-Agent: ".$this->user_agent;
  				$out .= "Connection: Close\r\n";
  				$out .= "Cookie: ".$cookie."\r\n";
  				$out .= "\r\n";
  
  				fwrite($fp, $out);
  
  				// Get rid of the HTTP headers
  				while ($fp && !feof($fp)){
  					$headerbuffer = fgets($fp, 1024);
  					if (urlencode($headerbuffer) == "%0D%0A"){
                      // We've reached the end of the headers
  						break;
  					}
  				}
  
  				$xml_data = '';
  				// Read the raw data from the socket in 1kb chunks
  				while (!feof($fp)){
  					$xml_data .= fgets($fp, 1024);
  				}
  				fclose($fp);
  			}        
  	}
	return $xml_data;
	}
	
	/**
  * Generate a hidden input field
  * 
  * @param	$name		name of the field
  * @param	$input	Value of the field
  * @return bool UTF8 encoded string
  */
	public function genHiddenInput($name, $input){
		return "<input name='".$name."' value='".$input."' type='hidden'>\n";
	}
	
	/**
  * Returns <kbd>true</kbd> if the string or array of string is encoded in UTF8.
  *
  * Example of use. If you want to know if a file is saved in UTF8 format :
  * <code> $array = file('one file.txt');
  * $isUTF8 = isUTF8($array);
  * if (!$isUTF8) --> we need to apply utf8_encode() to be in UTF8
  * else --> we are in UTF8 :)
  * @param mixed A string, or an array from a file() function.
  * @return boolean
  */
	 protected function isUTF8($string){
    if (is_array($string)){
    	$enc = implode('', $string);
    	return @!((ord($enc[0]) != 239) && (ord($enc[1]) != 187) && (ord($enc[2]) != 191));
    }else{
    	return (utf8_encode(utf8_decode($string)) == $string);
    }   
	}
	
	/**
  * Check if the String is UTF8 or not
  * 
  * @return bool
  */
	public function CheckUTF8(){
		return $this->stringIsUTF8;
	}
	
	/**
  * Convert the String to UTF8 if needed
  * 
  * @param $string Input
  * @return bool UTF8 encoded string
  */
	public function UTF8tify($string){
    global $user;
		if($this->stringIsUTF8 || !$this->XMLIsUTF8){
			return $string;
		}else{
			return mb_convert_encoding($string,$user->lang['ENCODING'],"UTF-8");
		}
	}
	
	public function utf8_array_encode($input){
    $return = array();
    foreach ($input as $key => $val){
      if( is_array($val) ){
        $return[$key] = mb_convert_variables("UTF-8", $user->lang['ENCODING'], $val);
      }else{
        $return[$key] = mb_convert_encoding($val,"UTF-8",$user->lang['ENCODING']);
      }
    }
    return $return;          
  }
  
  public function utf8_array_decode($input){
    $return = array();
    foreach ($input as $key => $val){
      $k = mb_convert_encoding($key,$user->lang['ENCODING'],"UTF-8");
      $return[$k] = mb_convert_encoding($val,$user->lang['ENCODING'],"UTF-8");
    }
    return $return;          
  }
}
?>
