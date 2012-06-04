<?php
/**
 * @package JawHare
 * @license BSD
 * @link https://github.com/mikemill/JawHare 
 */

namespace JawHare;

/**
 * Event manager.  Allows for registering event listeners and triggering them. 
 * Singleton object
 * 
 * @method int on(string $events, callback $callback, boolean $check_duplicates)
 * @method static int on(string $events, callback $callback, boolean $check_duplicates)
 * @method null off (string $events, int|callback $callback, boolean $cleanup)
 * @method static null off (string $events, int|callback $callback, boolean $cleanup)
 * @method mixed trigger(string $event, ...)
 * @method static mixed trigger(string $event, ...)
 */
class EventManager
{
	/**
	 * The singleton object
	 * @var \JawHare\EventManager
	 */
	static protected $instance = null;

	/**
	 * The callbacks that have been registered
	 * @var array 
	 */
	protected $callbacks = array();
	
	/**
	 * The index that the next callback will use.  Used to make registering 
	 * @var int 
	 */
	protected $nextcallbackindex = 0;
	
	/**
	 * The events that are being listened to with pointers to the respective callbacks
	 * @var type 
	 */
	protected $events = array();

	protected function __construct()
	{
	}

	/**
	 * Registers an event listener
	 * @param string $events A space separated list of events to listen to
	 * @param callback $callback The callback to execute for the given event
	 * @param type $check_duplicates Check for callback duplicates.  If a duplicate is found then silently ignore but don't add
	 * @return null|index Returns the index for the callback or null if the callback isn't callable
	 * @todo Should thrown an exception on error instead of returning null
	 */
	protected function _on ($events, $callback, $check_duplicates = true)
	{
		if (!is_callable($callback))
			return null; // Should make this an exception;

		if (!$check_duplicates || ($index = array_search($callback, $this->callbacks)) === false)
		{
			$this->callbacks[$this->nextcallbackindex] = $callback;
			$index = $this->nextcallbackindex++;
		}

		$events = explode(' ', $events);
		foreach ($events AS $event)
		{
			if (empty($this->events[$event]))
			{
				$this->events[$event] = array(
					$index => &$this->callbacks[$index],
				);
			}
			elseif (!isset($this->events[$event][$index]))
			{
				$this->events[$event][$index] = &$this->callbacks[$index];
			}
		}

		return $index;
	}

	/**
	 * Deregisters an event listener
	 * @param string $events A space separated list of events to stop listening to
	 * @param int|callback $callback If an int deregisters the corresponding listener.  Otherwise it will attempt to find the listener.
	 * @param boolean $cleanup If true will go through and see if this listener is still listening to any events and if not remove it.
	 */
	protected function _off ($events, $callback, $cleanup = false)
	{
		if (is_int($callback))
			$index = $callback;
		else
		{
			$index = array_search($callback, $this->callbacks);
			if ($index === false)
				return;
		}

		$events = explode(' ', $events);
		foreach ($events AS $event)
		{
			if (isset($this->events[$event][$index]))
				unset($this->events[$event][$index]);
		}

		if ($cleanup)
		{
			foreach ($this->events AS $event)
				if (isset($event[$index]))
					return;

			unset($this->callbacks[$index]);
		}
	}

	/**
	 * Triggers the event listeners
	 * @param type $event The event to trigger
	 * @parms mixed ... Optional set of parameters to pass to the listeners
	 * @return mixed|null
	 */
	protected function _trigger($event)
	{
		if (!empty($this->events[$event]))
		{
			$arguments = array_slice(func_get_args(), 1);
			$eventobj = new Event($event);
			array_unshift($arguments, $eventobj);

			foreach ($this->events[$event] AS $handler)
			{
				$ret = call_user_func_array($handler, $arguments);

				if ($ret !== null)
					$eventobj->returndata($ret);

				if (!$eventobj->propagate())
					break;
			}

			return $eventobj->returndata();
		}

		return null;
	}

	/**
	 * Static interfaces to allow for convience
	 * @see _on() 
	 */
	static protected function _son($events, $callback, $check_duplicates = true)
	{
		$obj = self::instance();
		$obj->on($events, $callback, $check_duplicates);
	}

	/**
	 * Static interface to allow for convience
	 * @see EventManager::_off()
	 */
	static protected function _soff($events, $callback, $cleanup = false)
	{
		$obj = self::instance();
		$obj->off($events, $callback, $cleanup);
	}

	/**
	 * Static interfaces to allow for convience
	 * @see _trigger()
	 */
	static protected function _strigger($event)
	{
		$obj = self::instance();
		$obj->trigger($event);
	}

	/**
	* Since PHP doesn't allow for regular and static methods to have the same name we have to use a bit of magic to fake it.
	* @param string $name Name of the method to call.  Must be 'on', 'off', or 'trigger'
	* @param array $arguments Arguments for the method
	* @return mixed Return value from the method
	*/
	static public function __callStatic($name, $arguments)
	{
		if (in_array($name, array('on', 'off', 'trigger')))
		{
			$class = get_called_class();
			$name = '_s' . $name;
			return call_user_func_array(array($class, $name), $arguments);
		}
	}

	/**
	* Since PHP doesn't allow for regular and static methods to have the same name we have to use a bit of magic to fake it.
	* @param string $name Name of the method to call.  Must be 'on', 'off', or 'trigger'
	* @param array $arguments Arguments for the method
	* @return mixed Return value from the method
	*/
	public function __call($name, $arguments)
	{
		if (in_array($name, array('on', 'off', 'trigger')))
		{
			$class = get_called_class();
			$name = '_' . $name;
			return call_user_func_array(array($class, $name), $arguments);
		}
	}

	/**
	 * Get the instance of the EventManager object.  Creates the object if needed.
	 * @return \JawHare\EventManager
	 */
	static public function instance()
	{
		if (self::$instance === null)
		{

			$class = get_called_class();

			self::$instance = new $class();
		}

		return self::$instance;
	}
}