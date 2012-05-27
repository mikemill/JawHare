<?php

namespace JawHare;

// Pull in the configuration file
require_once 'conf/config.php';

// Create and autolaoder for the framework.
// Note that the order of these two matters so that the module can overwrite the framework objects if it wishes
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

function Cache($config = null)
{
	static $instance = null;

	if ($instance !== null)
		return $instance;

	if ($config === null || !isset($config['class']) || !class_exists($config['class']))
	{
		$instance = new NullCache();
		return $instance;
	}
	else
	{
		$instance = new $config['class']($config);
		return $instance;
	}
}

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

function Settings($config = array())
{
	static $instance = null;

	if ($instance !== null)
		return $instance;

	$instance = new Settings($config);

	return $instance;
}