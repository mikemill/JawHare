<?php

namespace JawHare;
class User
{
	protected $id;
	protected $hashed_pw = '';
	protected $data = array();
	protected $storage = null;
	protected $dirty = false;

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
			throw new \Exception('Unable to load member');
		$this->load_from_array($ret->assoc());

		return $this;
	}

	public function save()
	{
		if (empty($this->id))
		{
			$ret = $this->storage->create_user($this->data);
			$this->id($ret->insert_id());
		}
		else
			$this->storage->update_user($this->data);
	}

	public function id ($val = null)
	{
		if ($val === null)
			return $this->id;
		else
		{
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
			$this->hashed_pw = $this->data['passwd'] = $this->hashpw($val);
			return $this;
		}
	}

	static public function get_admins()
	{
		$result = Database()->loadStorage('User')->get_admins();

		return new Collection($result, get_called_class());
	}
}