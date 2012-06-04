<?php

namespace JawHare\Storage;

class GroupStorageMySQL extends DatabaseStorage
{
	public function load_group($id)
	{
		return $this->db->select('
			SELECT id_group, groupname
			FROM groups
			WHERE id_group = {int:id_group}',
			array(
				'id_group' => $id,
			)
		);
	}

	public function create_group($name)
	{
		return $this->db->query('
			INSERT INTO groups (groupname)
			VALUES ({string:groupname})',
			array(
				'groupname' => $name,
			), 'write');
	}

	public function update_group($id, $name)
	{
		return $this->db->query('
			UPDATE groups
			SET groupname = {string:groupname}
			WHERE id_group = {int:id_group}',
			array(
				'id_group' => $id,
				'groupname' => $name,
			), 'write');
	}

	public function get_users($groupid)
	{
		return $this->db->query('
			SELECT id_user
			FROM usergroups
			WHERE id_group = ' . $this->colSQLID('id_group'),
			array(
				'id_group' => $groupid,
			));
	}

	public function add_user($groupid, $userid, $primary)
	{
		return $this->db->query('
			INSERT INTO usergroups (`id_group`, `id_user`, `primary`)
			VALUES ({int:group}, {int:user}, {bool:primary})',
			array(
				'group' => $groupid,
				'user' => $userid,
				'primary' => $primary,
			), 'write');
	}

	public function remove_user($groupid, $userid)
	{
		return $this->db->query('
			DELETE FROM usergroups
			WHERE id_group = {int:id_group}
				AND id_user = {int:id_user}',
			array(
				'id_group' => $groupid,
				'id_user' => $userid,
			), 'write');
	}

	public function get_users_groups($userid)
	{
		return $this->db->query('
			SELECT `id_group`, `groupname`, `primary`
			FROM usergroups
			LEFT JOIN groups USING (id_group)
			WHERE id_user = {int:id}',
			array(
				'id' => $userid,
			)
		);
	}

	public function set_user_primary($userid, $groupid, $primary, $addnew = true)
	{
		if ($addnew)
		{
			$sql = '
				INSERT INTO usergroups (id_group, groupname, primary)
				VALUES ({int:id_group}, {int:id_user}, {bool:primary})
				ON DUPLICATE KEY UPDATE primary = {bool:primary}';
		}
		else
		{
			$sql = '
				UPDATE usergroups SET primary = {bool:primary}
				WHERE id_group = {int:id_group}
					AND id_user = {int:id_user}';
		}

		return $this->db->query($query,
			array(
				'id_group' => $groupid,
				'id_user' => $userid,
				'primary' => $primary,
			), 'write');
	}
}