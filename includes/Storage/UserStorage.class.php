<?php

namespace JawHare\Storage;

abstract class UserStorage extends DatabaseStorage
{
	protected $columns = array(
		'id_user' => 'int',
		'username' => 'string',
		'fullname' => 'string',
		'passwd' => 'string',
		'email' => 'string',
		'admin' => 'bool',
	);
	abstract public function load_user($id);
	abstract public function load_user_by_username($name);
	abstract public function create_user($data);
	abstract public function update_user($data);
}