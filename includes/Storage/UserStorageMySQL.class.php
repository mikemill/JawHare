<?php

namespace JawHare\Storage;

class UserStorageMySQL extends UserStorage
{
	public function load_user($id)
	{
		 return $this->db->select('
			SELECT {array_identifiers:cols}
			FROM users
			WHERE id_user = ' . $this->colSQLID('username'),
			array(
				'cols' => array_keys($this->columns),
				'id' => $id,
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
}
