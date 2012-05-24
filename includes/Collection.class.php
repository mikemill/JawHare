<?php

namespace JawHare;

class Collection
{
	protected $result = null;
	protected $classtype = null;
	protected $maxindex = 0;

	public function __construct($result, $classtype)
	{
		$this->result = $result;
		$this->classtype = $classtype;
		$this->maxindex = $this->result->numrows();
	}

	public function num()
	{
		return $this->maxindex;
	}

	public function next()
	{
		$array = $this->result->assoc();

		if ($array === false)
			return false;

		return new $this->classtype($array);
	}

	public function get($index)
	{
		if ($index < 0 || $index >= $this->maxindex)
			return false;

		$ret = $this->result->seek($index);

		if ($ret === false)
			return false;

		return $this->next();
	}
}