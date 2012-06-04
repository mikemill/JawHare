<?php
namespace JawHare\session;

/**
 * Session handler class.
 * The default class doesn't actually do anything but use the default PHP session handler.  It is mainly here to allow for extending it.
 */
class Session
{
	/**
	 * Whether or not the session has been started
	 * @var boolean
	 */
	protected $started = false;
	
	/**
	 * Session data
	 * @var array
	 */
	protected $data = null;

	public function __construct()
	{
		// Did they pass a session cookie?
		if (isset($_COOKIE['PHPSESSID']))
			$this->start();
	}

	/**
	 * Close session.
	 * PHP session handler function.
	 */
	protected function close(){}
	
	/**
	 * Destroy the session
	 * PHP session handler function.
	 * @param string $session_id 
	 */
	protected function destroy ($session_id){}
	
	/**
	 * Session global collection
	 * PHP session handler function.
	 * @param int $maxlifetime 
	 */
	protected function gc ($maxlifetime){}
	
	/**
	 * Open session.
	 * PHP session handler function.
	 * @param string $save_path
	 * @param string $session_id 
	 */
	protected function open ($save_path, $session_id){}
	
	/**
	 * Read session.
	 * PHP session handler function.
	 * @param string $session_id 
	 */
	protected function read ($session_id){}
	
	/**
	 * Write the session data.
	 * PHP session handler function.
	 * @param string $session_id
	 * @param string $session_data 
	 */
	protected function write ($session_id , $session_data){}

	/**
	 * Whether or not the session has started
	 * @return boolean
	 */
	public function started()
	{
		return $this->started;
	}

	/**
	 *  Start the session.
	 */
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

	/**
	 * Get a value from the session data
	 * @param string $var
	 * @return mixed 
	 */
	public function &__get($var)
	{
		if (!$this->started)
			$this->start();

		if (isset($this->data[$var]))
			return $this->data[$var];
	}

	/**
	 * Set a session variable
	 * @param string $var
	 * @param mixed $val 
	 */
	public function __set($var, $val)
	{
		if (!$this->started)
			$this->start();

		$this->data[$var] = $val;
	}

	/**
	 * Checks to see if a variable is set
	 * @param string $var
	 * @return boolean
	 */
	public function __isset($var)
	{
		if (!$this->started)
			$this->start();

		return isset($this->data[$var]);
	}

	/**
	 * Removes a session variable
	 * @param string $var 
	 */
	public function __unset($var)
	{
		if (!$this->started)
			$this->start();

		unset($this->data[$var]);
	}
}
