<?php
/**
 * @package JawHare
 * @license BSD
 * @link https://github.com/mikemill/JawHare 
 */

namespace JawHare;

/**
 * The base group object.  Note that this is for the group itself and doesn't directly contain user information. 
 */
class Group
{
	/**
	 * The ID of the group.  If null then a new group will be created when saved.
	 * @var id
	 */
	protected $id = null;
	
	/**
	 * The name of the group.
	 * @var string
	 */
	protected $name = null;

	/**
	 * Whether or not the group data has been modified since the last save.
	 * @var boolean
	 */
	protected $dirty = false;
	
	/**
	 * Pointer to the storage object
	 * @var \JawHare\Storage\GroupStorage
	 */
	protected $storage = null;

	/**
	 *
	 * @param null|int|array $id ID of the group to load.  If an array will load the data from the array.
	 */
	public function __construct($id = null)
	{
		$this->storage = Database()->load_storage('Group');

		if (is_array($id))
		{
			$this->load_from_array($id);
		}
		else
		{
			$this->id = $id;

			if ($this->id !== null)
				$this->load();
		}

	}

	/**
	 * Loads the group data from the given array.
	 * @param array $array The array of group data
	 * @return \JawHare\Group 
	 */
	public function load_from_array($array)
	{
		$this->id = isset($array['id_group']) ? $array['id_group'] : $array['id'];
		$this->name = isset($array['groupname']) ? $array['groupname'] : $array['name'];

		return $this;
	}

	/**
	 * Loads the group data
	 * @param null|int $id If not null will load the given group's data
	 * @return \JawHare\Group
	 * @throws NoGroupException 
	 */
	public function load($id = null)
	{
		if ($id === null)
			$id = $this->id;

		$ret = $this->storage->load_group($id);

		if ($ret->numrows() == 0)
			throw new NoGroupException;

		$this->load_from_array($ret->assoc());

		return $this;
	}

	/**
	 * Gets or sets the group's id
	 * @param null|int $id
	 * @return \JawHare\Group 
	 */
	public function id($id = null)
	{
		if ($id === null)
			return $this->id;
		elseif ($this->id != $id)
		{
			$this->id = $id;
			$this->dirty = true;
		}
		return $this;
	}
	
	/**
	 * Gets or sets the group's name
	 * @param null|string $name
	 * @return \JawHare\Group 
	 */
	public function name($name = null)
	{
		if ($name === null)
			return $this->name;
		elseif ($this->name != $name)
		{
			$this->name = $name;
			$this->dirty = true;
		}
		return $this;
	}

	/**
	 * Save the group's data.  If the id is null or zero then create the group.
	 * @return \JawHare\Group 
	 */
	public function save()
	{
		if (empty($this->id))
		{
			$ret = $this->storage->create_group($this->name);
			$this->id($ret->insert_id());
		}
		elseif ($this->dirty)
		{
			$this->storage->update_group($this->id, $this->name);
		}

		$this->dirty = false;

		return $this;
	}

	/**
	 * Get the users in this group
	 * @return \JawHare\UserCollection 
	 */
	public function get_users()
	{
		return new UserCollection($this->storage->get_users($this->id));
	}

	/**
	 * Add a user to this group
	 * @param int|object $user The user to add
	 * @param boolean $primary Whether or not to make this group the user's primary group
	 * @return \JawHare\Database\DatabaseResult
	 */
	public function add_user($user, $primary = false)
	{
		if (is_object($user))
			$userid = $user->id();
		else
			$userid = $user;

		return $this->storage->add_user($this->id, $userid, $primary);
	}
}