<?php

namespace JawHare\Storage;

class UserStorageMySQL extends UserStorage
{
	public function load_user($id)
	{
		 return $this->db->select('
			SELECT {array_identifiers:cols}
			FROM users
			WHERE id_user = ' . $this->colSQLID('id_user'),
			array(
				'cols' => array_keys($this->columns),
				'id_user' => $id,
			)
		);
	}

	public function load_user_by_username($name)
	{
		 return $this->db->select('
			SELECT {array_identifiers:cols}
			FROM users
			WHERE username = ' . $this->colSQLID('username'),
			array(
				'cols' => array_keys($this->columns),
				'username' => $name,
			)
		);
	}

	public function create_user($data)
	{
		$cols = $this->columns;
		unset($cols['id_user']);

		$colnames = array_keys($cols);
		$coltypes = array();
		foreach ($cols AS $name => $type)
			$coltypes[] = $this->colSQLID($name);

		$data['cols'] = $colnames;

		return $this->db->query('
			INSERT INTO users ({array_identifiers:cols})
			VALUES (' . implode(', ', $coltypes) . ')'
			, $data, 'write');
	}

	public function update_user($data)
	{
		$columns = array();

		foreach ($data AS $col => $val)
		{
			if ($col == 'id_user')
				continue;
			$columns[] = $col . ' = ' . $this->colSQLID($col);
		}
			
		return $this->db->query('
			UPDATE users SET
			' . implode(', ', $columns) . '
			WHERE id_user = ' . $this->colSQLID('id_user'),
			$data,
			'write');		
	}

	public function get_admins()
	{
		return $this->db->query('
			SELECT {array_identifiers:cols}
			FROM users
			WHERE admin = ' . $this->colSQLID('admin'),
			array(
				'cols' => array_keys($this->columns),
				'admin' => true,
			)
		);
	}

	public function load_settings($id)
	{
		$settings = array();
		$results = $this->db->query('
			SELECT field, value
			FROM usersettings
			WHERE id_user = {int:id_user}', array('id_user' => $id));

		while ($row = $results->assoc())
			$settings[$row['field']] = $row['value'];

		return $settings;
	}

	public function delete_settings($id, $fields)
	{
		return $this->db->query('
			DELETE FROM usersettings
			WHERE id_user = {int:id_user}
				AND field IN ({array_string:fields})',
			array(
				'id_user' => $id,
				'fields' => $fields,
			),
			'write'
		);
	}

	public function save_settings($id, $data)
	{
		$rows = array();

		foreach ($data AS $field => $value)
			$rows[] = array($id, $field, $value);

		return $this->db->insert('usersettings', array('id_user' => 'int', 'field' => 'string', 'value' => 'string'), $rows, 'replace');
	}

}
