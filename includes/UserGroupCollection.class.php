<?php
/**
 * @package JawHare
 * @license BSD
 * @link https://github.com/mikemill/JawHare 
 */

namespace JawHare;

/**
 * Collection for User Groups 
 */
class UserGroupCollection extends Collection
{
	protected $user;
	
	public function __construct($user, $results)
	{
		parent::__construct($results, null);
		$this->user = $user;
	}

	/**
	 * Gets the next User Group.  Returns false if there are no more.
	 * @return boolean|\JawHare\UserGroup 
	 */
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