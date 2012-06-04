<?php
/**
 * @package JawHare
 * @license BSD
 * @link https://github.com/mikemill/JawHare 
 */

namespace JawHare;

/**
 * A user in a group. 
 */
class UserGroup
{
	/**
	 * The user
	 * @var \JawHare\User 
	 */
	protected $user = null;
	
	/**
	 * The group
	 * @var \JawHare\Group 
	 */
	protected $group = null;
	
	/**
	 * Whether or not this group is the user's primary group
	 * @var boolean
	 */
	protected $primary = false;

	/**
	 *
	 * @param \JawHare\User|id $user The user
	 * @param \JawHare\User|id $group The group
	 * @param boolean $primary Whether or not this group is the user's primary group
	 */
	public function __construct($user, $group, $primary = false)
	{
		if (is_object($user))
			$this->user = $user;
		else
			$this->user = new User($user);

		if (is_object($group))
			$this->group = $group;
		else
			$this->group = new Group($group);

		$this->primary = (bool) $primary;
	}

	/**
	 * Gets or sets whether or not this group is the user's primary group
	 * @param null|boolean $value If null acts as a getter otherwise sets the primary group status.
	 * @return \JawHare\UserGroup 
	 */
	public function primary($value = null)
	{
		if ($value === null)
			return $this->primary;
		else
		{
			$this->primary = $value;
			Database()->load_storage('Group')->set_user_primary($this->user->id(), $this->group->id(), $this->primary);
			return $this;
		}
	}

	/**
	 * Get the user object
	 * @return \JawHare\User
	 */
	public function user()
	{
		return $this->user;
	}

	/**
	 * Get the group object
	 * @return \JawHare\Group
	 */
	public function group()
	{
		return $this->group;
	}

	/**
	 * Removes the user from the group
	 * @return \JawHare\UserGroup 
	 */
	public function remove()
	{
		Database()->load_storage('Group')->remove_user($this->group->id(), $this->user->id());
		return $this;
	}
}