<?php
/**
 * @package JawHare
 * @license BSD
 * @link https://github.com/mikemill/JawHare 
 */

namespace JawHare;

/**
 * Base authentication class. 
 */
class Authentication
{
	/**
	 * The currently logged in user.  Initally set to an empty user.
	 * @var \JawHare\User 
	 */
	protected $user = null;
	
	/**
	 * The settings passed to the object.
	 * @var array
	 */
	protected $settings = null;
	
	/**
	 * The name of the cookie to use.  Initially set to the name passed by the settings.
	 * @var string
	 */
	protected $cookiename = 'JawHareAuth';

	/**
	 *
	 * @param array $settings 
	 */
	public function __construct($settings)
	{
		$this->settings = $settings;
		$this->cookiename = $settings['cookie'];
		$this->user = new User();
	}

	/**
	 * Checks to see if the current user is logged in.  If passed an ID it will check to see if the current user matches that id.
	 * @param null|int|string $id
	 * @return bool 
	 */
	public function is_logged_in($id = null)
	{
		if ($id === null)
		{
			$id = $this->user->id();
			return !empty($id);
		}

		if (is_numeric($id))
			return $id == $this->user->id();
		else
			return $id == $this->user->username();

	}

	/**
	 * Validates the supplied password.  If given an ID it will attempt to load that user before validating.
	 * @param string $password
	 * @param null|int|string $id
	 * @return bool 
	 */
	public function validate($password, $id = null)
	{
		if ($id !== null && ((is_numeric($id) && $id != $this->user->id()) || $id != $this->user->username()))
		{
			$this->user->load($id);
		}

		return $this->user->validatepw($password);
	}

	/**
	 * Load the current user from the authentication cookie.
	 * @return boolean 
	 */
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
		catch (NoUserException $e)
		{
			return false;
		}
	}

	/**
	 * The current user.
	 * @return \JawHare\User
	 */
	public function user()
	{
		return $this->user;
	}

	/**
	 * Logs the user in and sets the cookie.
	 * If given an id will load the user beforehand.
	 * @param null|int|string $id 
	 */
	public function login($id = null)
	{
		if ($id !== null && ((is_numeric($id) && $id != $this->user->id()) || $id != $this->user->username()))
		{
			$this->user->load($id);
		}

		$password_salt = $this->user->salt();

		if (empty($password_salt))
			$this->user->salt($password_salt = $this->user->password_salt())->save();

		$cookiepw = $this->user->hashpw($this->user->password(), $password_salt);

		setcookie($this->cookiename, serialize(array($this->user->id(), $cookiepw)), 0, '/', $this->settings['domain'], false, true);
	}

	/**
	 * Log the user out 
	 */
	public function logout()
	{
		setcookie($this->cookiename, null, time() - 3600, '/', $this->settings['domain'], false, true);
	}
}