<?php
/**
 *  OpenLSS - Lighter Smarter Simpler
 *
 *	This file is part of OpenLSS.
 *
 *	OpenLSS is free software: you can redistribute it and/or modify
 *	it under the terms of the GNU Lesser General Public License as
 *	published by the Free Software Foundation, either version 3 of
 *	the License, or (at your option) any later version.
 *
 *	OpenLSS is distributed in the hope that it will be useful,
 *	but WITHOUT ANY WARRANTY; without even the implied warranty of
 *	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *	GNU Lesser General Public License for more details.
 *
 *	You should have received a copy of the 
 *	GNU Lesser General Public License along with OpenLSS.
 *	If not, see <http://www.gnu.org/licenses/>.
 */
namespace LSS;

use \Exception;

class Config {

	static $inst = false;
	
	public $config = array();
	public $debug = false;

	public static function _get(){
		if(self::$inst == false) self::$inst = new Config();
		return self::$inst;
	}

	public function setConfig($config){
		$this->config = $config;
	}

	public static function set($sec,$name,$value=null){
		if(self::_get()->debug) printf("Config::set(%s,%s,%s)\n",$sec,$name,$value);
		return mda_set(self::_get()->config,$sec,$name,$value);
	}

	public static function get($sec=null,$name=null){
		if(is_null($sec)) return self::_get()->config;
		if(self::_get()->debug) printf("Config::get(%s%s)\n",$sec,is_null($name)?'':sprintf(',%s',$name));
		if(!mda_exists(self::_get()->config,$sec,$name))
			throw new Exception('Config var doesnt exist: '.$sec.'.'.$name);
		return mda_get(self::_get()->config,$sec,$name);
	}

	public static function getMerged($sec,$name=null){
		if(self::_get()->debug) printf("Config::getMerged(%s%s)\n",$sec,is_null($name)?'':sprintf(',%s',$name));
		try{ $s = self::get($sec,$name); } catch(Exception $e){ $s = null; }
		try{ $n = self::get($name); } catch(Exception $e){ $n = null; }
		$rv = $s;
		if(!is_null($n)){
			if(is_array($n) && is_array($s)){
				$rv = array_merge($n,$s);
			} else if(is_null($s)){
				$rv = $n;
			}
		}
		if(is_null($rv)) throw new Exception("config: mergeable var not found: $sec.$name");
		if(self::_get()->debug) printf("Config::getMerged() complete\n");
		return $rv;
	}

}
