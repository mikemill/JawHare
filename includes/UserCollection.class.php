<?php
/**
 * @package JawHare
 * @license BSD
 * @link https://github.com/mikemill/JawHare 
 */

namespace JawHare;

/**
 *	User Collection 
 */
class UserCollection extends Collection
{
	public function __construct($results)
	{
		parent::__construct($results, null);
	}

	public function next()
	{
		$array = $this->result->assoc();

		if ($array === false)
			return false;

		$user = new User();

		if (!isset($array['passwd']))
			$user->load($array['id_user']);
		else
			$user->load_from_array($array);

		return $user;
	}

}