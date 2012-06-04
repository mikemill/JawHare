<?php
/**
 * @package JawHare
 * @license BSD
 * @link https://github.com/mikemill/JawHare 
 */

namespace JawHare;

/**
 * Base controller.  The controller is the initial point of entry and the main work horse. 
 */
class BaseController
{
	/**
	 * A cleaned version of $_POST
	 * @var array 
	 */
	protected $post = array();

	/**
	 * A cleaned version of $_GET
	 * @var array 
	 */
	protected $get = array();

	/**
	 * A cleaned version of $_REQUEST.  Created by GET + POST
	 * @var array 
	 */
	protected $request = array();

	/**
	 * Cache object
	 * @var \JawHare\Cache\Cache 
	 */
	protected $cache = null;
	
	/**
	 * Database object
	 * @var \JawHare\Database\Database
	 */
	protected $db = null;
	
	/**
	 * Settings object
	 * @var \JawHare\Settings
	 */
	protected $settings = null;
	
	/**
	 * Authentication object
	 * @var \JawHare\Authentication
	 */
	protected $auth = null;
	
	/**
	 * Session object
	 * @var \JawHare\Session\Session
	 */
	protected $sess = null;

	/**
	 * Theme object
	 * @var null|\JawHare\BaseTheme
	 */
	protected $theme = null;

	/**
	 * The set of parameters that will be accepted on every request.
	 * @var array
	 */
	protected $default_parameters = array(
		'_GET' => array(
			'action' => 'string',
		),
		'_POST' => array(),
	);
	
	/**
	 * The parameters to accept for this controller.
	 * @var array
	 */
	protected $parameters = array(
		'_GET' => array(
		),
		'_POST' => array(
		),
	);

	/**
	 * Whether or not to load the database object
	 * @var bool
	 */
	static protected $load_db = true;
	
	/**
	 * Whether or not to load the cache object
	 * @var bool
	 */
	static protected $load_cache = true;
	
	/**
	 * Whether or not to load the session object
	 * @var type 
	 */
	static protected $load_session = true;

	/**
	 *
	 * @param array $objects An array of database, session, cache, etc objects loaded by the boot function.
	 */
	public function __construct($objects = array())
	{
		foreach ($objects AS $obj => $val)
			$this->$obj = $val;
	}

	/**
	 * Santizes $_GET and $_POST 
	 */
	public function sanitize()
	{
		$santizer = function($input, $allowed)
		{
			$ret = array();

			foreach ($allowed AS $name => $type)
			{
				$valid = false;

				if (!isset($input[$name]) && $type != 'exists')
					continue;

				if (is_callable($type))
				{
					$valid = call_user_func($type, $input[$name]);
				}
				elseif (is_bool($type))
				{
					$valid = $type;
				}
				elseif (substr($type, 0, 1) == '~' || substr($type, 0, 1) == '/')
				{
					$valid = preg_match($type, $input[$name]) > 0;
				}
				else
				{
					switch ($type)
					{
						case 'string':
							$valid = true;
							$input[$name] = (string) $input[$name];
							break;

						case 'int':
							if (is_numeric($input[$name]))
							{
								$input[$name] = (int) $input[$name];
								$valid = true;
							}
							break;

						case 'hexstring':
							$valid = preg_match('~^[a-fA-F0-9]*$~', $input[$name]) > 0;
							break;

						case 'exists':
							$valid = true;
							$input[$name] = isset($input[$name]);
							break;
					}
				}

				if ($valid)
				{
					$ret[$name] = $input[$name];
				}
			}

			return $ret;
		};

		foreach (array('_GET', '_POST') AS $var)
		{
			$allowed = $this->default_parameters[$var] + (isset($this->parameters[$var]) ? $this->parameters[$var] : array());

			$prop = substr(strtolower($var), 1);

			$this->$prop = $santizer($GLOBALS[$var], $allowed);
		}

		$this->request = $this->get + $this->post;
	}

	/**
	 * Routes control flow to the specified action.
	 * Action methods need to be prefixed with action_
	 * @param string $action Name of the action to call
	 * @throws BadActionException 
	 */
	protected function route($action)
	{
		$func = 'action_' . $action;
		if (is_callable(array($this, $func)))
		{
			$this->$func();
		}
		else
		{
			throw new BadActionException($action);
		}
	}

	/**
	 * Loads the specified theme
	 * @param string $theme Theme to load
	 * @return \JawHare\BaseTheme
	 */
	protected function load_theme($theme = null)
	{
		$theme = ($theme === null || !class_exists($theme . 'Theme') ? $this->settings->config('theme') : $theme) . 'Theme';

		if (is_object($this->theme))
		{
			if ($this->theme instanceof $theme)
				return;
		}
		$this->theme = new $theme($this->settings->config('views_dir'));

		$this->theme->add_view_var('auth', $this->auth);

		return $this->theme;
	}

	/**
	 * If $theme is specified acts as a setter and returns the controller.
	 * If $theme is null acts as a getter and returns the theme.
	 * @param null|\JawHare\BaseTheme $theme
	 * @return \JawHare\BaseController|\JawHare\BaseTheme
	 */
	protected function theme($theme = null)
	{
		if ($theme === null)
			return $this->theme;
		else
		{
			$this->theme = $theme;
			return $this;
		}
	}

	/**
	 * Checks to see if there is a method for the specified action
	 * @param string $action
	 * @return bool
	 */
	protected function is_valid_action($action)
	{
		return method_exists($this, 'action_' . $action);
	}
	
	/**
	 * Entry point for the controller.
	 * Creates the various objects needed by the controller, creates the controller object, loads the current user, santizes the input, and routes the action.
	 * @global type $ini
	 * @param null|string $action The action to route to.  Defaults to 'index' if null
	 * @param array $passed_objects Additional objects to pass to the controller.
	 * @return \JawHare\BaseController
	 */
	public static function boot($action = null, $passed_objects = array())
	{
		global $ini;

		if (self::$load_cache)
			$cache = Cache($ini['cache']);
		else
			$cache = null;

		if (self::$load_cache)
			$db = Database($ini['database']);
		else
			$db = null;

		if (self::$load_session)
			$sess = Session($ini['session']);
		else
			$sess = null;

		$objects = array(
			'cache' => $cache,
			'db' => $db,
			'settings' => new Settings($ini),
			'sess' => $sess,
			'auth' => Authentication($ini['authentication']),
		);

		$objects['auth']->load_user_from_cookie();

		foreach ($passed_objects AS $obj => $val)
		{
			$objects[$obj] = $val;
		}


		$classname = get_called_class();

		$obj = new $classname($objects);

		$obj->sanitize();

		if ($action == null)
		{
			$action = !empty($obj->_GET['action']) && $obj->is_valid_action($obj->_GET['action']) ? $obj->_GET['action'] : 'index';
		}

		$obj->route($action);

		return $obj;
	}

	/**
	 * Redirects to user to the given URL and stops execution
	 * @param string $url URL to redirect the user to
	 * @param string $status The HTTP status.  Defaults to 302
	 * @param bool $cache Whether to set the cache headers
	 */
	static public function redirect_exit($url, $status = '302', $cache = true)
	{
		header('Location: ' . $url, true, $status);

		if ($cache)
		{
			// TODO: Add cache headers
		}
		die;
	}

	/**
	 * Outputs a JSON object and stops execution.
	 * @param mixed $obj The object to output
	 * @param bool $encode Whether ot not to json_encode the $obj
	 */
	static public function json_response($obj, $encode = true)
	{
		header('Content-type: text/json');
		echo $encode ? json_encode($obj) : $obj;
		die;
	}
}