<?php

namespace JawHare;

class UserGroup
{
	protected $user = null;
	protected $group = null;
	protected $primary = false;

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

	public function primary($value = null)
	{
		if ($value === null)
			return $this->primary;
		else
		{
			$this->primary = $value;
			Database()->loadStorage('Group')->set_user_primary($this->user->id(), $this->group->id(), $this->primary);
			return $this;
		}
	}

	public function user()
	{
		return $this->user;
	}

	public function group()
	{
		return $this->group;
	}

	public function remove()
	{
		Database()->loadStorage('Group')->remove_user($this->group->id(), $this->user->id());
		return $this;
	}
}