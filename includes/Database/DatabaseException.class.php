<?php

namespace JawHare\Database;

class DatabaseException extends \Exception
{
	protected $sql;
	public function __construct($sql, $errormsg)
	{
		parent::__construct($errormsg);

		$this->sql = $sql;
	}
}