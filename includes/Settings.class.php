<?php

namespace JawHare;

class Settings
{
	protected $settings;
	protected $config;

	protected $dirty_settings = array();
	protected $delete_settings = array();

	public function __construct($config)
	{
		$this->config = $config;

		$this->settings = Database()->loadStorage('Settings')->load_settings();
	}

	public function config($var, $subindex = null)
	{
		if ($subindex !== null)
			return isset($this->config[$var], $this->config[$var][$subindex]) ? $this->config[$var][$subindex] : null;
		return isset($this->config[$var]) ? $this->config[$var] : null;
	}

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

	public function __isset($name)
	{
		return isset($this->settings[$name]);
	}

	public function __unset($name)
	{
		if (isset($this->settings[$name]))
		{
			$this->delete_settings[$name] = null;
			unset($this->settings[$name]);
			unset($this->dirty_settings[$name]);
		}
	}

	public function save()
	{
		$store = $db = Database()->loadStorage('Settings');

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