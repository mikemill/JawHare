<?php

namespace JawHare\Storage;

/**
 * Storage for groups 
 */
abstract class GroupStorage extends DatabaseStorage
{

	/**
	 * Load a group's data
	 * @param int $id ID of the group
	 * @return \JawHare\Database\DatabaseResult (Likely to change in the future)
	 */
	abstract public function load_group($id);

	/**
	 * Create a new group
	 * @param string $name Name of the group
	 * @return \JawHare\Database\DatabaseResult (Likely to change in the future)
	 */
	abstract public function create_group($name);

	/**
	 * Change a group's data
	 * @param int $id Id of the group
	 * @param name $name New name of the group
	 * @return \JawHare\Database\DatabaseResult (Likely to change in the future)
	 */
	abstract public function update_group($id, $name);

	/**
	 * Get the users who are in this group.
	 * @param int $groupid ID of the group
	 * @return \JawHare\Database\DatabaseResult (Likely to change in the future)
	 */
	abstract public function get_users($groupid);

	/**
	 * Add a user to this group
	 * @param int $groupid ID of the gruop
	 * @param int $userid ID of the user
	 * @param int $primary Whether or not to make this group the user's primary group
	 * @return \JawHare\Database\DatabaseResult (Likely to change in the future)
	 */
	abstract public function add_user($groupid, $userid, $primary);

	/**
	 * Remove a user from the gruop
	 * @param int $groupid ID of the group
	 * @param int $userid ID of the user
	 * @return \JawHare\Database\DatabaseResult (Likely to change in the future)
	 */
	abstract public function remove_user($groupid, $userid);

	/**
	 * Get the groups that a user belongs to
	 * @param int $userid ID of the user
	 * @return \JawHare\Database\DatabaseResult (Likely to change in the future)
	 */
	abstract public function get_users_groups($userid);
	
	/**
	 * Sets a user's primary group.
	 * @param int $userid ID of the user
	 * @param int $groupid ID of the group
	 * @param boolean $primary Whether or not to set this group as the primary
	 * @param boolean $addnew If the user isn't already a member of the group whether or not to add them to the group.
	 * @return \JawHare\Database\DatabaseResult (Likely to change in the future)
	 */
	abstract public function set_user_primary($userid, $groupid, $primary, $addnew = true);
	
}