<?php
/*
 * LSS Core
 * OpenLSS - Light, sturdy, stupid simple
 * 2010 Nullivex LLC, All Rights Reserved.
 * Bryan Tong <contact@nullivex.com>
 *
 *   OpenLSS is free software: you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation, either version 3 of the License, or
 *   (at your option) any later version.
 *
 *   OpenLSS is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License
 *   along with OpenLSS.  If not, see <http://www.gnu.org/licenses/>.
 */

//this is not a config file
ld('/func/mda');

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
		list($sec,$name) = self::_path(array($sec,$name));
		if(!isset(self::_get()->config[$sec]))
			self::_get()->config[$sec]=array();
		if(strpos($name,'.') !== false){
			$p = explode('.',$name);
			switch(count($p)){
				case 2:
					if(!isset(self::_get()->config[$sec][$p[0]]))
						self::_get()->config[$sec][$p[0]]=array();
					self::_get()->config[$sec][$p[0]][$p[1]] = $value;
					break;
				case 3:
					if(!isset(self::_get()->config[$sec][$p[0]]))
						self::_get()->config[$sec][$p[0]]=array();
					if(!isset(self::_get()->config[$sec][$p[0]][$p[1]]))
						self::_get()->config[$sec][$p[0]][$p[1]]=array();
					self::_get()->config[$sec][$p[0]][$p[1]][$p[2]] = $value;
					break;
				case 4:
					if(!isset(self::_get()->config[$sec][$p[0]]))
						self::_get()->config[$sec][$p[0]]=array();
					if(!isset(self::_get()->config[$sec][$p[0]][$p[1]]))
						self::_get()->config[$sec][$p[0]][$p[1]]=array();
					if(!isset(self::_get()->config[$sec][$p[0]][$p[1]][$p[2]]))
						self::_get()->config[$sec][$p[0]][$p[1]][$p[2]]=array();
					self::_get()->config[$sec][$p[0]][$p[1]][$p[2]][$p[3]] = $value;
					break;
				default:
					//set up to maxdepth array levels, then put raw dotted leftovers there as a var
					if(!isset(self::_get()->config[$sec][$p[0]]))
						self::_get()->config[$sec][$p[0]]=array();
					if(!isset(self::_get()->config[$sec][$p[0]][$p[1]]))
						self::_get()->config[$sec][$p[0]][$p[1]]=array();
					if(!isset(self::_get()->config[$sec][$p[0]][$p[1]][$p[2]]))
						self::_get()->config[$sec][$p[0]][$p[1]][$p[2]]=array();
					self::_get()->config[$sec][$p[0]][$p[1]][$p[2]][implode('.',array_slice($p,3))] = $value;
					break;
			}
			unset($p);
		} else
			self::_get()->config[$sec][$name] = $value;
	}

	public static function get($sec,$name=null){
		if(self::_get()->debug) printf("Config::get(%s%s)\n",$sec,is_null($name)?'':sprintf(',%s',$name));
		list($sec,$name) = self::_path(array($sec,$name));
		if(!isset(self::_get()->config[$sec])) throw new Exception("config: sec does not exist: $sec");
		if(is_null($name)){
			return self::_get()->config[$sec];
		} else {
			$rv = null;
			if(!isset(self::_get()->config[$sec][$name]) && (strpos($name,'.') !== false)){
				$p = explode('.',$name);
				$rv = null;
				switch(count($p)){
					case 4:
					default:
						if(is_null($rv) && (isset(self::_get()->config[$sec][$p[0]][$p[1]][$p[2]][implode('.',array_slice($p,3))])))
							$rv = self::_get()->config[$sec][$p[0]][$p[1]][$p[2]][implode('.',array_slice($p,3))];
					case 3:
						if(is_null($rv) && (isset(self::_get()->config[$sec][$p[0]][$p[1]][implode('.',array_slice($p,2))])))
							$rv = self::_get()->config[$sec][$p[0]][$p[1]][implode('.',array_slice($p,2))];
					case 2:
						if(is_null($rv) && (isset(self::_get()->config[$sec][$p[0]][implode('.',array_slice($p,1))])))
							$rv = self::_get()->config[$sec][$p[0]][implode('.',array_slice($p,1))];
						break;
				}
				unset($p);
			} else {
				$rv = mda_get(self::_get()->config[$sec],$name);
			}
			if(is_null($rv)) throw new Exception("config: var not found: $sec,$name");
			return $rv;
		}
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
		if(is_null($rv)) throw new Exception("config: mergeable var not found: $sec,$name");
		if(self::_get()->debug) printf("Config::getMerged() complete\n");
		return $rv;
	}

	private static function _path($path=''){
		if(is_array($path)) $path = implode('.',$path);
		$path = trim($path,'.');
		$rv = array(0=>$path,1=>null);
		if(strpos($path,'.') !== false){
			$p = explode('.',$path);
			$rv[0] = array_shift($p);
			$rv[1] = implode('.',$p);
			unset($p);
			if(strlen($rv[1]) === 0) $rv[1] = null;
		}
		if(self::_get()->debug) printf("Config::_path(%s,%s)\n",$rv[0],$rv[1]);
		return $rv;
	}

}
