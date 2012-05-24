<?php

namespace JawHare;

class Authentication
{
	// The user currently using the system
	protected $user = null;
	protected $settings = null;
	protected $cookiename = 'JawHareAuth';

	public function __construct($settings)
	{
		$this->settings = $settings;
		$this->cookiename = $settings['cookie'];
		$this->user = new User();
	}

	public function is_logged_in($id = null)
	{
	}

	public function validate($password, $id = null)
	{
		if ($id !== null && ((is_numeric($id) && $id != $this->user->id()) || $id != $this->user->username()))
		{
			$this->user->load($id);
		}

		return $this->user->validatepw($password);
	}

	public function load_user_from_cookie()
	{
		if (!isset($_COOKIE[$this->cookiename]))
			return false;

		$data = unserialize($_COOKIE[$this->cookiename]);

		if ($data === false)
			return false;

		list ($id, $pw) = $data;

		try
		{
			$user = new User($id);

			if ($user->hashpw($user->password(), $user->salt()) == $pw)
			{
				$this->user = $user;
				return true;
			}
			return false;
		}
		catch (\Exception $e)
		{
			return false;
		}
	}

	public function user()
	{
		return $this->user;
	}

	public function login($id = null)
	{
		if ($id !== null && ((is_numeric($id) && $id != $this->user->id()) || $id != $this->user->username()))
		{
			$this->user->load($id);
		}

		$session = Session();

		$password_salt = $this->user->salt();

		if (empty($password_hash))
			$this->user->salt($password_salt = $this->user->password_salt())->save();

		$cookiepw = $this->user->hashpw($this->user->password(), $password_salt);



		setcookie($this->cookiename, serialize(array($this->user->id(), $cookiepw)), 0, '/', $this->settings['domain'], false, true);
	}

	public function logout($id = null)
	{
	}
}