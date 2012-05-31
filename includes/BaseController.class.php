<?php
namespace JawHare;
class BaseController
{
	protected $post = array();
	protected $get = array();
	protected $request = array();

	protected $cache = null;
	protected $db = null;
	protected $settings = null;
	protected $auth = null;
	protected $sess = null;

	protected $theme = null;

	protected $default_parameters = array(
		'_GET' => array(
			'action' => 'string',
		),
		'_POST' => array(),
	);

	static protected $load_db = true;
	static protected $load_cache = true;
	static protected $load_session = true;

	public function __construct($objects = array())
	{
		foreach ($objects AS $obj => $val)
			$this->$obj = $val;
	}

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

	protected function show_view($view, $vars = array())
	{
		$vars['controller'] = $this;

		if (file_exists($filename = $this->settings->config('views_dir') . '/' . $view . '.tpl'))
		{
			extract($vars);
			require_once $filename;
		}
		else
			throw new ViewException('404', $view);
	}

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

	protected function is_valid_action($action)
	{
		return method_exists($this, 'action_' . $action);
	}


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

	static public function redirect_exit($url, $status = '302', $cache = true)
	{
		header('Location: ' . $url, true, $status);

		if ($cache)
		{
			// TODO: Add cache headers
		}
		die;
	}

	static public function json_response($obj, $encode = true)
	{
		header('Content-type: text/json');
		echo $encode ? json_encode($obj) : $obj;
		die;
	}
}