<?php
namespace JawHare\session;

// The default class doesn't actually do anything but use the default PHP session handler.  It is mainly here to allow for extending it.
class Session
{
	protected $started = false;
	protected $data = null;

	public function __construct()
	{
		// Did they pass a session cookie?
		if (isset($_COOKIE['PHPSESSID']))
			$this->start();
	}

	// PHP session handler functions
	protected function close(){}
	protected function destroy ($session_id){}
	protected function gc ($maxlifetime){}
	protected function open ($save_path, $session_id){}
	protected function read ($session_id){}
	protected function write ($session_id , $session_data){}

	public function started()
	{
		return $this->started;
	}

	public function start()
	{
		if (!$this->started)
		{
			session_start();

			if (strtolower(session_id()) == 'deleted')
				session_regenerate_id(true);

			$this->data = &$_SESSION;

			$this->started = true;
		}
	}

	// Getters and settings
	public function &__get($var)
	{
		if (!$this->started)
			$this->start();

		if (isset($this->data[$var]))
			return $this->data[$var];
	}

	public function __set($var, $val)
	{
		if (!$this->started)
			$this->start();

		$this->data[$var] = $val;
	}

	public function __isset($var)
	{
		if (!$this->started)
			$this->start();

		return isset($this->data[$var]);
	}

	public function __unset($var)
	{
		if (!$this->started)
			$this->start();

		unset($this->data[$var]);
	}
}
