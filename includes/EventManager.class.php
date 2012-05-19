<?php

namespace JawHare;

class EventManager
{
	static protected $instance = null;

	protected $callbacks = array();
	protected $nextcallbackindex = 0;
	protected $events = array();

	protected function __construct()
	{
	}

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

	// Static interfaces to allow for convience
	static protected function _son($events, $callback, $check_duplicates = true)
	{
		$obj = self::instance();
		$obj->on($events, $callback, $check_duplicates);
	}

	static protected function _soff($events, $callback, $cleanup = false)
	{
		$obj = self::instance();
		$obj->off($events, $callback, $cleanup);
	}

	static protected function _strigger($event)
	{
		$obj = self::instance();
		$obj->trigger($event);
	}

	// Since PHP doesn't allow for regular and static methods to have the same name we have to use a bit of magic to fake it.
	static public function __callStatic($name, $arguments)
	{
		if (in_array($name, array('on', 'off', 'trigger')))
		{
			$class = get_called_class();
			$name = '_s' . $name;
			return call_user_func_array(array($class, $name), $arguments);
		}
	}

	public function __call($name, $arguments)
	{
		if (in_array($name, array('on', 'off', 'trigger')))
		{
			$class = get_called_class();
			$name = '_' . $name;
			return call_user_func_array(array($class, $name), $arguments);
		}
	}

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