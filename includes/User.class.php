<?php

namespace JawHare;
class User
{
	protected $id;
	protected $hashed_pw = '';
	protected $data = array();
	protected $settings = array();
	protected $dirty_settings = array();
	protected $delete_settings = array();
	protected $dirty = false;
	protected $storage = null;

	public function __construct($id = null)
	{
		$this->storage = Database()->loadStorage('User');

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

	protected function load_from_array($array)
	{
		foreach ($array AS $data => $value)
		{
			$this->data[$data] = $value;
		}

		$this->hashed_pw = $this->data['passwd'];
		$this->id = $this->data['id_user'];
	}

	protected function load_settings()
	{
		$this->settings = $this->storage->load_settings($this->id);
	}

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

	public function hashpw($pw, $salt = null)
	{
		if ($salt === null)
			$salt = $this->password_salt();
		return crypt($pw, $salt);
	}

	public function validatepw($input)
	{
		return crypt($input, $this->hashed_pw) == $this->hashed_pw;
	}

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
	}

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

	public function admin ($val = null)
	{
		if ($val === null)
			return isset($this->data['admin']) ? $this->data['admin'] : null;
		else
		{
			$this->dirty = true;
			$this->data['admin'] = (bool) $val;
			return $this;
		}
	}

	public function passwd ($val = null)
	{
		if ($val === null)
			return isset($this->data['passwd']) ? $this->data['passwd'] : null;
		else
		{
			$this->dirty = true;
			$this->hashed_pw = $this->data['passwd'] = $this->hashpw($val);
			return $this;
		}
	}

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

	public function delete_settings($var)
	{
		unset($this->settings[$var]);
		$this->delete_settings[$var] = null;

		return $this;
	}

	static public function get_admins()
	{
		$result = Database()->loadStorage('User')->get_admins();

		return new Collection($result, get_called_class());
	}
}

class UserException extends \Exception
{
}

class NoUserException extends UserException
{
}