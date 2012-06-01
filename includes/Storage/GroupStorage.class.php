<?php

namespace JawHare\Storage;

abstract class GroupStorage extends DatabaseStorage
{
	abstract public function load_group($id);
	abstract public function create_group($name);
	abstract public function update_group($id, $name);
	abstract public function get_users($groupid);
	abstract public function add_user($groupid, $userid, $primary);
	abstract public function remove_user($groupid, $userid);
	abstract public function get_users_groups($userid);
	abstract public function set_user_primary($userid, $groupid, $primary, $addnew = true);
	
}