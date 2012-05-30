<?php

namespace JawHare\Storage;

abstract class GroupStorage extends DatabaseStorage
{
	protected $columns = array(
		'id_group' => 'int',
		'groupname' => 'string',
	);

	protected $table = 'groups';

	abstract public function load_group($id);
	abstract public function create_group($name);
	abstract public function update_group($id, $name);
}