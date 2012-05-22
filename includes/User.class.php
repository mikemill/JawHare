<?php

namespace JawHare;
class User
{
	protected $id;
	protected $hashed_pw = '';
	protected $data = array();

	public function __construct($id = null)
	{
		$this->id = $id;

		if ($this->id !== null)
			$this->load();
	}

	public function hashpw($pw)
	{
		// This function uses the Blowfish algorithm with a random salt.
		// The cost value is a balance of security and speed.  The time component increases exponentially with the cost paramaeter.
		$salt = '$2a$06$';
		$characters = './0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
		$len = strlen($characters) - 1;
		for ($i = 0; $i <= 22; $i++)
			$salt .= $characters[mt_rand(0, $len)];

		$salt .= '$';

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

		$db = Database();

		$ret = $db->select('JawHare:User:load_user', array('id' => $id));

		foreach ($ret->assoc() AS $data => $value)
		{
			$this->data[$data] = $value;
		}

		$this->hashed_pw = $this->data['passwd'];
		$this->id = $this->data['id_user'];
	}
}