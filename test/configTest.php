<?php
require_once(dirname(__DIR__).'/test_common.php');
lib('config');

class ConfigTest extends PHPUNIT_Framework_TestCase {

	static $test_config = array('main'=>array('test'=>'test setting','test2'=>false));
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

	public function testSetConfig(){
		$test['test']['test1'] = true;
		$this->config->setConfig($test);
		$this->assertTrue(Config::get('test','test1'));
	}

	public function testGet(){
		$this->assertFalse(Config::get('main','test2'));
	}

	public function testSet(){
		Config::set('main','test3',true);
		$this->assertTrue(Config::get('main','test3'));
	}

}
