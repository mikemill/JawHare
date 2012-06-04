<?php
/**
 * @package JawHare
 * @license BSD
 * @link https://github.com/mikemill/JawHare 
 */

namespace JawHare;

// Pull in the configuration file
require_once 'conf/config.php';

// Create and autolaoder for the framework.
spl_autoload_register(function($class_name)
{
	global $ini;

	if (strpos($class_name, 'JawHare\\') === 0)
	{
		$class = str_replace('JawHare\\', '', $class_name);
		$filename = $ini['class_dir'] . '/' . str_replace('\\', '/', $class) . '.class.php';
		if (file_exists($filename))
			include_once $filename;
	}
});

/**
 * Retrieve or create the Cache object
 * @param array|null $config
 * @return \JawHare\Cache\Cache
 */
function Cache($config = null)
{
	static $instance = null;

	if ($instance !== null)
		return $instance;

	if ($config === null || !isset($config['class']) || !class_exists($config['class']))
	{
		$instance = new Cache\NullCache();
		return $instance;
	}
	else
	{
		$instance = new $config['class']($config);
		return $instance;
	}
}

/**
 * Retrieve or create the Database object
 * @param array|null $config
 * @return \JawHare\Database\Database
 * @throws \Exception 
 */
function Database($config = null)
{
	static $instance = null;

	if ($instance !== null)
		return $instance;

	if ($config === null || !isset($config['class']) || !class_exists($config['class']))
	{
		throw new \Exception('No database class available');
	}
	else
	{
		$instance = new $config['class']($config);
		return $instance;
	}
}

/**
 * Retrieve or create the Session object
 * @param array|null $config
 * @return \JawHare\Database\Database
 * @throws \Exception 
 */
function Session($config = null)
{
	static $instance = null;

	if ($instance !== null)
		return $instance;

	if ($config === null || !isset($config['class']) || !class_exists($config['class']))
	{
		throw new \Exception('No session class available');
	}
	else
	{
		$instance = new $config['class'](!empty($config['autostart']));
		return $instance;
	}
}

/**
 * Retrieve or create the Settings object
 * @param array|null $config
 * @return \JawHare\Settings 
 */
function Settings($config = array())
{
	static $instance = null;

	if ($instance !== null)
		return $instance;

	$instance = new Settings($config);

	return $instance;
}

/**
 * Retrieve or create the Authentication object
 * @param array|null $config
 * @return \JawHare\Authentication
 * @throws \Exception 
 */
function Authentication($config = null)
{
	static $instance = null;

	if ($instance !== null)
		return $instance;

	if ($config === null || !isset($config['class']) || !class_exists($config['class']))
	{
		throw new \Exception('No authentication class available');
	}
	else
	{
		$instance = new $config['class']($config);
		return $instance;
	}
}
