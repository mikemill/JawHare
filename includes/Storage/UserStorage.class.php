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
		'salt' => 'string',
	);

	protected $table = 'users';

	abstract public function load_user($id);
	abstract public function load_user_by_username($name);
	abstract public function create_user($data);
	abstract public function update_user($data);
	abstract public function get_admins();
	abstract public function load_profile($id);
	abstract public function delete_profile($id, $fields);
	abstract public function save_profile($id, $data);
}