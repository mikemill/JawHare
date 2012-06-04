<?php

namespace JawHare\Storage;

/**
 * Storage for user data 
 */
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

	/**
	 * Loads a user's base data
	 * @param int $id ID of the user
	 * @return \JawHare\Database\DatabaseResult (Likely to change in the future)
	 */
	abstract public function load_user($id);

	/**
	 * Loads a user's base data using the username instead of the id
	 * @param string $name Username of the user
	 * @return \JawHare\Database\DatabaseResult (Likely to change in the future) 
	 */
	abstract public function load_user_by_username($name);

	/**
	 * Creates a new user
	 * @param array $data Assocative array of $data=>$value pairs
	 * @return \JawHare\Database\DatabaseResult (Likely to change in the future) 
	 */
	abstract public function create_user($data);
	
	/**
	 * Update's the user's base data
	 * Can not update the user's ID
	 * @param array $data Assocative array of $data=>$value pairs to update.
	 * @return \JawHare\Database\DatabaseResult (Likely to change in the future) 
	 */
	abstract public function update_user($data);
	
	/**
	 * Get the users who have the admin flag enabled.
	 * @return \JawHare\Database\DatabaseResult (Likely to change in the future) 
	 */
	abstract public function get_admins();
	
	/**
	 * Load the user's settings
	 * @param int $id ID of the user
	 * @return array Assocative array of $setting=>$value pairs
	 */
	abstract public function load_settings($id);
	
	/**
	 * Delete settings for a particular user
	 * @param int $id ID of the user
	 * @param array $fields Numeric array of the settings to delete for this user
	 * @return \JawHare\Database\DatabaseResult (Likely to change in the future) 
	 */
	abstract public function delete_settings($id, $fields);
	
	/**
	 * Save the settings for a user.
	 * @param int $id ID of the user
	 * @param array $data Assocative array of $setting=>$value pairs
	 * @return \JawHare\Database\DatabaseResult (Likely to change in the future) 
	 */
	abstract public function save_settings($id, $data);
}