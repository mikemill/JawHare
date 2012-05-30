<?php

namespace JawHare;

class UserGroupCollection extends Collection
{
	protected $user;
	
	public function __construct($user, $results)
	{
		parent::__construct($results, null);
		$this->user = $user;
	}

	public function next()
	{
		$array = $this->result->assoc();

		if ($array === false)
			return false;

		$primary = $array['primary'];
		unset($array['primary']);

		return new UserGroup($this, $array, $primary);
	}

}