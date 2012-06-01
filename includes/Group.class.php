<?php

namespace JawHare;

class Group
{
	protected $id = null;
	protected $name = null;

	protected $dirty = false;
	protected $storage = null;

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

	public function load_from_array($array)
	{
		$this->id = isset($array['id_group']) ? $array['id_group'] : $array['id'];
		$this->name = isset($array['groupname']) ? $array['groupname'] : $array['name'];

		return $this;
	}

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

	public function get_users()
	{
		return new UserCollection($this->storage->get_users($this->id));
	}

	public function add_user($user, $primary = false)
	{
		if (is_object($user))
			$userid = $user->id();
		else
			$userid = $user;

		return $this->storage->add_user($this->id, $userid, $primary);
	}
}