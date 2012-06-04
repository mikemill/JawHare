<?php

namespace JawHare\Database;

/**
 * Exception for dealing with errors with regards to the database or queries.
 * These will almost always be fatal. 
 */
class DatabaseException extends \Exception
{
	protected $sql;
	public function __construct($sql, $errormsg)
	{
		parent::__construct($errormsg);

		$this->sql = $sql;
	}
}