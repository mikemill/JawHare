<?php
/**
 * @package JawHare
 * @license BSD
 * @link https://github.com/mikemill/JawHare 
 */

namespace JawHare;

/**
 * Object that holds both the config from the files and the settings from storage 
 */
class Settings
{
	/**
	 * The settings from storage
	 * @var array 
	 */
	protected $settings;
	
	/**
	 * The configuration from files
	 * @var array
	 */
	protected $config;

	/**
	 * The settings that have been changed since the last save
	 * @var array
	 */
	protected $dirty_settings = array();
	
	/**
	 * The settings that have been deleted since the last save
	 * @var type 
	 */
	protected $delete_settings = array();

	/**
	 *
	 * @param array $config The configuration from the files.
	 */
	public function __construct($config)
	{
		$this->config = $config;

		$this->settings = Database()->load_storage('Settings')->load_settings();
	}

	/**
	 * Gets the specified configuration.
	 * @param string $var The configuration variable to retrieve
	 * @param null|string $subindex If not null the sub variable to retrieve
	 * @return mixed
	 */
	public function config($var, $subindex = null)
	{
		if ($subindex !== null)
			return isset($this->config[$var], $this->config[$var][$subindex]) ? $this->config[$var][$subindex] : null;
		return isset($this->config[$var]) ? $this->config[$var] : null;
	}

	/**
	 * Sets or gets the specified setting.
	 * Allows settings to follow the $obj->name() or $obj->name('value') format.
	 * @param string $name Name of the setting
	 * @param null|string $arguments Value to assign to the variable.
	 * @return string|\JawHare\Settings 
	 */
	public function __call($name, $arguments)
	{
		if (empty($arguments))
			return isset($this->settings[$name]) ? $this->settings[$name] : null;
		else
		{
			$this->settings[$name] = $arguments[0];
			$this->dirty_settings[$name] = null;
			unset($this->delete_settings[$name]);
			return $this;
		}
	}

	/**
	 * Whether or not a given setting is set
	 * @param string $name Setting name
	 * @return boolean
	 */
	public function __isset($name)
	{
		return isset($this->settings[$name]);
	}

	/**
	 * Deletes a setting
	 * @param string $name Setting name
	 */
	public function __unset($name)
	{
		if (isset($this->settings[$name]))
		{
			$this->delete_settings[$name] = null;
			unset($this->settings[$name]);
			unset($this->dirty_settings[$name]);
		}
	}

	/**
	 * Save changes to settings.  Configuration changes are not saved.
	 * @return \JawHare\Settings 
	 */
	public function save()
	{
		$store = $db = Database()->load_storage('Settings');

		if (!empty($this->dirty_settings))
		{

			$data = array_intersect_key($this->settings, $this->dirty_settings);
			$store->save_settings($data);
		}

		if (!empty($this->delete_settings))
		{
			$store->delete_settings(array_keys($this->delete_settings));
		}

		$this->delete_settings = $this->dirty_settings = array();

		return $this;
	}
}