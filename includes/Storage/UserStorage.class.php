<?php

namespace JawHare\Storage;

abstract class UserStorage extends DatabaseStorage
{
	protected $column_defs = array(
		'id_user' => 'int',
		'username' => 'string',
		'fullname' => 'string',
		'passwd' => 'string',
		'email' => 'string',
		'admin' => 'bool',
		'salt' => 'string',
	);

	abstract public function load_user($id);
	abstract public function load_user_by_username($name);
	abstract public function create_user($data);
	abstract public function update_user($data);
	abstract public function get_admins();
	abstract public function load_settings($id);
	abstract public function delete_settings($id, $fields);
	abstract public function save_settings($id, $data);
}