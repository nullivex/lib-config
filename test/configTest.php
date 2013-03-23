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

require_once('vendor/autoload.php');
require('boot.php');
ld('config');

class ConfigTest extends \PHPUNIT_Framework_TestCase {

	static $test_config = array(
		'main'=>array(
			  'test'=>'test setting'
			 ,'test2'=>false
		)
		,'subgroup'=>array(
			'subset'=>array(
				 'a'=>true
				,'b'=>false
			)
		)
		,'subset'=>array(
				 'a'=>false
				,'c'=>true
		)
	);
	static $orig_config = null;

	protected $config = null;

	protected function setUp(){
		self::$orig_config = Config::$inst;
		$this->config = new Config();
		$this->config->setConfig(self::$test_config);
		Config::$inst = $this->config;
	}

	protected function tearDown(){
		$this->config = null;
		Config::$inst = self::$orig_config;
	}

	//Config::setConfig() test
	public function testSetConfig(){
		$test['test']['test1'] = true;
		$this->config->setConfig($test);
		$this->assertTrue(Config::get('test','test1'));
	}

	//Config::get*() tests
	public function testGet(){
		$this->assertFalse(Config::get('main','test2'));
	}

	public function testGetPath(){
		$this->assertFalse(Config::get('subgroup','subset.b'));
	}

	public function testGetMergedOverlayBothSet(){
		$this->assertTrue(Config::getMerged('subgroup','subset.a'));
	}

	public function testGetMergedOverlayUnsetParent(){
		$this->assertFalse(Config::getMerged('subgroup','subset.b'));
	}

	public function testGetMergedOverlayUnsetChild(){
		$this->assertTrue(Config::getMerged('subgroup','subset.c'));
	}

	//Config::set() tests
	public function testSetSimple(){
		Config::set('main','test3',true);
		$this->assertTrue(Config::get('main','test3'));
	}

	public function testSetExistingPath(){
		Config::set('subgroup','subset.b',true);
		$this->assertTrue(Config::get('subgroup','subset.b'));
	}

	public function testSetNewPath(){
		Config::set('does','not.exist',true);
		$this->assertTrue(Config::get('does','not.exist'));
	}

	public function testSetLongPath(){
		Config::set('does','this.extra.long.dotted.path.work.properly.wonk.wonk.wonk',true);
		$this->assertTrue(Config::get('does','this.extra.long.dotted.path.work.properly.wonk.wonk.wonk'));
	}

}
