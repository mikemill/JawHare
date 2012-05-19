<?php

namespace JawHare;

class Event
{
	protected $event;
	protected $propagate = true;
	protected $returndata = null;

	public function __construct($event)
	{
		$this->event = $event;
	}

	public function event() { return $this->event; }
	
	public function propagate($bool = null)
	{
		if (!is_bool($bool))
			return $this->propagate;
		else
		{
			$this->propagate = $bool;
			return $this;
		}
	}

	public function returndata($data = null)
	{
		if ($data === null)
			return $this->returndata;
		else
		{
			$this->returndata = $data;
			return $this;
		}
	}
}