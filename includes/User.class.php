<?php
/**
 * @package JawHare
 * @license BSD
 * @link https://github.com/mikemill/JawHare 
 */

namespace JawHare;

/**
 * Base User class 
 */
class User
{
	/**
	 * ID for this user.  If null then it is treated as a new user when saved.
	 * @var int|null
	 */
	protected $id = null;
	
	/**
	 * The hashed version of the password.  The plain text version is never saved.
	 * @var string
	 */
	protected $hashed_pw = '';
	
	/**
	 * Key-Value pairs of user data
	 * @var array
	 */
	protected $data = array();
	
	/**
	 * Key-value pairs ofuser settings.  Settings are not guaranteed to exist
	 * @var array
	 */
	protected $settings = array();
	
	/**
	 * Settings that have been changed since the last save
	 * @var array
	 */
	protected $dirty_settings = array();
	
	/**
	 * Settings that have been deleted since the last save
	 * @var array
	 */
	protected $delete_settings = array();
	
	/**
	 * Whether or not the user data is dirty
	 * @var boolean
	 */
	protected $dirty = false;
	
	/**
	 * Pointer to user storage
	 * @var \JawHare\Storage\UserStorage
	 */
	protected $storage = null;

	/**
	 *
	 * @param null|array|int|string $id The ID or username of the user to load.  Or an array with the user data from which to build the user object
	 */
	public function __construct($id = null)
	{
		$this->storage = Database()->load_storage('User');

		if (is_array($id))
		{
			$this->load_from_array($id);
		}
		else
		{
			$this->id = $id;

			if ($this->id !== null)
				$this->load();
		}
	}

	/**
	 * Load the user data from the given array of data
	 * @param array $array 
	 */
	protected function load_from_array($array)
	{
		foreach ($array AS $data => $value)
		{
			$this->data[$data] = $value;
		}

		$this->hashed_pw = $this->data['passwd'];
		$this->id = $this->data['id_user'];
	}

	/**
	 * Load the user's settings from storage 
	 */
	protected function load_settings()
	{
		$this->settings = $this->storage->load_settings($this->id);
	}

	/**
	 * Create a random password salt.
	 * @return string 
	 */
	public function password_salt()
	{
		// This function uses the Blowfish algorithm with a random salt.
		// The cost value is a balance of security and speed.  The time component increases exponentially with the cost paramaeter.
		$salt = '$2a$06$';
		$characters = './0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
		$len = strlen($characters) - 1;
		for ($i = 0; $i <= 22; $i++)
			$salt .= $characters[mt_rand(0, $len)];

		$salt .= '$';

		return $salt;
	}

	/**
	 * Hash the password.
	 * Note: This does not store the password.
	 * @param string $pw The password to hash
	 * @param null|string $salt The salt to use.  If null creates a random salt.
	 * @return string 
	 */
	public function hashpw($pw, $salt = null)
	{
		if ($salt === null)
			$salt = $this->password_salt();
		return crypt($pw, $salt);
	}
	
	/**
	 * Validate the given password against this user's password.
	 * @param string $input The plain text password to validate.
	 * @return boolean
	 */
	public function validatepw($input)
	{
		return crypt($input, $this->hashed_pw) == $this->hashed_pw;
	}

	/**
	 * Gets or sets the user's password.  If a password is given it will immediately hash it.
	 * @param null|string $pw If null acts as a getter.  Otherwise the plain text password.
	 * @return string|\JawHare\User 
	 */
	public function password($pw = null)
	{
		if ($pw === null)
			return $this->hashed_pw;
		else
		{
			$this->hashed_pw = $this->hashpw($pw);
			return $this;
		}
	}

	/**
	 * Load the user's data.
	 * @param null|int|string $id If not null load the given id instead of this user.
	 * @return \JawHare\User
	 * @throws NoUserException 
	 */
	public function load($id = null)
	{
		if ($id === null)
			$id = $this->id;

		if (is_numeric($id))
			$ret = $this->storage->load_user($id);
		elseif (is_string($id))
			$ret = $this->storage->load_user_by_username($id);
		else
			return $this;

		if ($ret->numrows() == 0)
			throw new NoUserException;

		$this->load_from_array($ret->assoc());
		$this->load_settings();

		return $this;
	}

	/**
	 * Saves the user's data and settings.  If there is no ID it will create a new user.
	 * @return \JawHare\User 
	 */
	public function save()
	{
		if (empty($this->id))
		{
			$ret = $this->storage->create_user($this->data);
			$this->id($ret->insert_id());
			$this->storage->save_settings($this->id, $this->settings);
		}
		else
		{
			if ($this->dirty)
				$this->storage->update_user($this->data);

			if (!empty($this->dirty_settings))
			{
				$data = array();

				foreach (array_unique($this->dirty_settings) AS $field)
					if (isset($this->settings[$field]))
						$data[$field] = $this->settings[$field];

				if (!empty($data))
					$this->storage->save_settings($this->id, $data);
			}
			
			if (!empty($this->delete_settings))
			{
				$this->storage->delete_settings($this->id, array_keys($this->delete_settings));
			}
		}

		$this->dirty = false;
		$this->dirty_settings = array();
		$this->delete_settings = array();

		return $this;
	}

	/**
	 * Gets or sets the user's id
	 * @param null|int $val If null acts as a getter.  Otherwise sets the user's id
	 * @return int|\JawHare\User 
	 */
	public function id ($val = null)
	{
		if ($val === null)
			return $this->id;
		else
		{
			$this->dirty = true;
			$this->id = $this->data['id_user'] = $val;
			return $this;
		}
	}
	
	/**
	 * Gets or sets the user's username
	 * @param type $val If null acts as a getter.  Otherwise sets the user's username
	 * @return string|null|\JawHare\User 
	 */
	public function username ($val = null)
	{
		if ($val === null)
			return isset($this->data['username']) ? $this->data['username'] : null;
		else
		{
			$this->dirty = true;
			$this->data['username'] = $val;
			return $this;
		}
	}

	/**
	 * Gets or sets the user's fullname
	 * @param type $val If null acts as a getter.  Otherwise sets the user's fullname
	 * @return string|null|\JawHare\User Returns null if there isn't a fullname set
	 */
	public function fullname ($val = null)
	{
		if ($val === null)
			return isset($this->data['fullname']) ? $this->data['fullname'] : null;
		else
		{
			$this->dirty = true;
			$this->data['fullname'] = $val;
			return $this;
		}
	}

	/**
	 * Gets or sets the user's email address
	 * @param type $val If null acts as a getter.  Otherwise sets the user's email address
	 * @return string|null|\JawHare\User Returns null if there isn't an email address set
	 */
	public function email ($val = null)
	{
		if ($val === null)
			return isset($this->data['email']) ? $this->data['email'] : null;
		else
		{
			$this->dirty = true;
			$this->data['email'] = $val;
			return $this;
		}
	}

	/**
	 * Gets or sets the user's salt
	 * @param type $val If null acts as a getter.  Otherwise sets the user's salt
	 * @return string|null|\JawHare\User Returns null if there isn't a salt set
	 */
	public function salt ($val = null)
	{
		if ($val === null)
			return isset($this->data['salt']) ? $this->data['salt'] : null;
		else
		{
			$this->dirty = true;
			$this->data['salt'] = $val;
			return $this;
		}
	}

	/**
	 * Gets or sets the user's admin status
	 * @param type $val If null acts as a getter.  Otherwise sets the user's admin status
	 * @return bool|\JawHare\User 
	 */
	public function admin ($val = null)
	{
		if ($val === null)
			return isset($this->data['admin']) ? $this->data['admin'] : false;
		else
		{
			$this->dirty = true;
			$this->data['admin'] = (bool) $val;
			return $this;
		}
	}

	/**
	 * Gets or sets the user's password.  Automatically hashes the password if set.
	 * @param type $val If null acts as a getter.  Otherwise sets the user's password
	 * @return string|\JawHare\User 
	 */
	public function passwd ($val = null)
	{
		if ($val === null)
			return $this->hashed_pw;
		else
		{
			$this->dirty = true;
			$this->hashed_pw = $this->data['passwd'] = $this->hashpw($val);
			return $this;
		}
	}

	/**
	 * Sets or gets a user's setting
	 * @param null|string $var If null returns an array of user settings.  Otherwise operates on that one variable.
	 * @param type $val If null acts as a getter.  Otherwise sets the user's setting for $var
	 * @return mixed|array|\JawHare\User 
	 */
	public function settings ($var = null, $val = null)
	{
		if ($var === null)
			return $this->settings;
		elseif ($val === null)
			return isset($this->settings[$var]) ? $this->settings[$var] : null;
		else
		{
			if (!isset($this->settings[$var]) || $this->settings[$var] !== $val)
			{
				$this->settings[$var] = $val;
				$this->dirty_settings[] = $var;

				unset($this->delete_settings[$var]);
			}
			return $this;
		}
	}

	/**
	 * Deletes the specified setting
	 * @param string $var
	 * @return \JawHare\User 
	 */
	public function delete_settings($var)
	{
		unset($this->settings[$var]);
		$this->delete_settings[$var] = null;

		return $this;
	}

	/**
	 * Gets the groups this user is in
	 * @return \JawHare\UserGroupCollection 
	 */
	public function get_groups()
	{
		return new UserGroupCollection($this, Database()->load_storage('Group')->get_users_groups($this->id));
	}

	/**
	 * Adds this user to the specified group
	 * @param int|\JawHare\Group $group The group to add the user to
	 * @param boolean $primary Whether or not this group is the user's primary group.
	 * @return \JawHare\Database\DatabaseResult
	 */
	public function add_group($group, $primary = false)
	{
		if (is_object($group))
			$groupid = $group->id();
		else
			$groupid = $group;

		return Database()->load_storage('Group')->add_user($groupid, $this->id, $primary);
	}

	/**
	 * Get the users who are administrators.
	 * @return \JawHare\Collection 
	 */
	static public function get_admins()
	{
		$result = Database()->load_storage('User')->get_admins();

		return new Collection($result, get_called_class());
	}
}

/**
 * User exceptions 
 */
class UserException extends \Exception
{
}

/**
 * Exception for when trying to load a particular user but they don't exist. 
 */
class NoUserException extends UserException
{
}