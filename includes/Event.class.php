<?php
/**
 * @package JawHare
 * @license BSD
 * @link https://github.com/mikemill/JawHare 
 */

namespace JawHare;

/**
 * Event object that is created and passed to event listeners.
 */
class Event
{
	/**
	 * The name of the event that was triggered
	 * @var string 
	 */
	protected $event;
	
	/**
	 * Keep propagating the event
	 * @var boolean
	 */
	protected $propagate = true;
	
	/**
	 * Data to return
	 * @var mixed
	 */
	protected $returndata = null;

	/**
	 *
	 * @param string $event Name of the event
	 */
	public function __construct($event)
	{
		$this->event = $event;
	}

	/**
	 * Name of the event
	 * @return string
	 */
	public function event() { return $this->event; }
	
	/**
	 * Getter or stting for event propagation.
	 * @param null|boolean $bool If null will return the current setting.
	 * @return boolean|\JawHare\Event 
	 */
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

	/**
	 * Getter or setting for the data to return to the location that triggered the event.
	 * If propagate isn't false then listeners down the line can inspect this data AND overwrite it.
	 * @param null|mixed $data If null will return the data
	 * @return \JawHare\Event 
	 */
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