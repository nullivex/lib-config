openlss/lib-config
==========

Configuration access helper and environment manager

Usage
----

```php

//build config
$config = array();
$config['test1'] = 'test';
$config['test2']['sec'] = 'test';
$config['test3']['sec'] = 'test2';

//load config
Config::setConfig($config); unset($config);

//set an additional
Config::set('test2.newsec',null,'testvalue');

//get options
$test = Config::get('test1');
$test2 = Config::get('test2.sec');
$test3 = Config::get('test2.newsec');

//get merged
$db = Config::getMerged('sec'); //will return test2
```

Singleton
----
Config operates with an internal singleton even though all the methods are static.

To retrieve the singleton use the following

```php
//use the clone keyword to actually copy the object (this is optional)
$inst = clone Config::_get();

//load up a new temporary config
Config::setConfig($newconfig);

//restore the old config
Config::$inst = clone $inst; unset($inst);
```

Reference
----

### (object) _get()
Returns the current singleton (will create one if it doesnt exist)

### (void) setConfig($config)
Will set the passed array to the main config

### (mixed) set($sec,$name,$value)
Sets a value in the config
  * $sec		config section (can be an MDA key) NULL for none
  * $name		config name (can be an MDA key) NULL for none
  * $value		Value to be set
Returns the value to be set

### (mixed) get($sec=null,$name=null)
Get a value from the confi structure
  * $sec		config section (can be an MDA key) NULL for none
  * $name		config name (can be an MDA key) NULL for none

### (mixed) getMerged($sec,$name=null)
Get a merged value from the config tree
Consider the following config structure
```php
//full tree
$config['db']['driver'] = 'mysql';
$config['admin']['db']['driver'] = 'sqlite3';

Config::setConfig($config);
$val = Config::getMerged('admin','db.driver'); //returns 'sqllite'

//shorthand tree
$config['db']['driver'] = 'mysql';

Config::setConfig($config);
$val = Config::getMerged('admin','db.driver'); //returns 'mysql'

```
The idea is to retrieve a section from a subroot and gracefully look upstream.
  * $sec		config section (can be an MDA key) NULL for none
  * $name		config name (can be an MDA key) NULL for none


