<?php

namespace JawHare;
class BaseTheme
{
	protected $view = null;
	protected $view_vars = array();
	protected $viewdir = null;

	protected $themedir = 'Base';

	protected $head_views = array();
	protected $foot_views = array();

	protected $title = '';

	protected $jsfiles = array();
	protected $rawjs = array();

	protected $cssfiles = array();
	protected $rawcss = array();

	protected $lang = 'english';


	public function __construct($viewdir)
	{
		$this->viewdir = $viewdir;
	}

	public function title($title = null)
	{
		if ($title === null)
		{
			return $this->title;
		}
		else
		{
			$this->title = $title;
		}
	}

	public function lang($lang = null)
	{
		if ($lang === null)
		{
			return $this->lang;
		}
		else
		{
			$this->lang = $lang;
		}
	}

	public function cssfiles($cssfiles = null, $append = true)
	{
		if ($cssfiles === null)
		{
			return $this->cssfiles;
		}
		elseif ($append)
		{
			if (!is_array($cssfiles))
				$cssfiles = array($cssfiles);

			$this->cssfiles = array_merge($this->cssfiles, $cssfiles);
		}
		else
		{
			$this->cssfiles = $cssfiles;
		}
	}

	public function rawcss($rawcss = null, $append = true)
	{
		if ($rawcss === null)
		{
			return $this->rawcss;
		}
		elseif ($append)
		{
			if (!is_array($rawcss))
				$rawcss = array($rawcss);

			$this->rawcss = array_merge($this->rawcss, $rawcss);
		}
		else
		{
			$this->rawcss = $rawcss;
		}
	}

	public function jsfiles($jsfiles = null, $append = true)
	{
		if ($jsfiles === null)
		{
			return $this->jsfiles;
		}
		elseif ($append)
		{
			if (!is_array($jsfiles))
				$jsfiles = array($jsfiles);

			$this->jsfiles = array_merge($this->jsfiles, $jsfiles);
		}
		else
		{
			$this->jsfiles = $jsfiles;
		}
	}

	public function rawjs($rawjs = null, $append = true)
	{
		if ($rawjs === null)
		{
			return $this->rawjs;
		}
		elseif ($append)
		{
			if (!is_array($rawjs))
				$rawjs = array($rawjs);

			$this->rawjs = array_merge($this->rawjs, $rawjs);
		}
		else
		{
			$this->rawjs = $rawjs;
		}
	}

	public function view($view = null, $view_vars = null)
	{
		if ($view === null)
		{
			return $this->view;
		}
		else
		{
			$this->view = $view;
			if (is_array($view_vars))
				$this->view_vars = array_merge($this->view_vars, $view_vars);
		}
	}

	public function addview($view, $view_vars = null, $part = 'head')
	{
		if ($part == 'head')
			$this->head_views[] = $view;
		else
			$this->foot_views[] = $view;

		if (is_array($view_vars))
			$this->view_vars = array_merge($this->view_vars, $view_vars);

	}

	public function add_view_var($var, $val)
	{
		$this->view_vars[$var] = $val;
	}

	protected function view_filename($view, $dir)
	{
		return $this->viewdir . "/$dir/{$view}.tpl";
	}

	protected function lang_filename($view, $dir, $lang)
	{
		return $this->viewdir . "/{$dir}_languages/$view.$lang.php";
	}

	protected function view_exists($view)
	{
		$dirs = array(
			$this->themedir,
		);

		if ($this->themedir != 'Base')
			$dirs[] = 'Base';

		foreach ($dirs AS $dir)
		{
			if (file_exists($this->view_filename($view, $dir)))
				return $dir;			
		}
		return false;
	}

	protected function load_view($view, $dir = null, $variables = null, $load_lang = true)
	{
		if ($view == '')
			return;

		// For ease, we are going to make a $theme variable that is set to this object.
		$theme = $this;

		if ($dir === null)
		{
			$dir = $this->view_exists($view);
			if ($dir === false)
				throw new ViewException('404', $view);
		}

		if ($variables === null)
			$variables = $this->view_vars;

		extract($variables);

		// Check for languages
		if ($load_lang)
		{
			if (file_exists($this->lang_filename($view, $dir, $this->lang)))
				require_once $this->lang_filename($view, $dir, $this->lang);
		}
		
		require_once $this->view_filename($view, $dir);
	}

	public function show()
	{
		$views = array_merge($this->head_views, array($this->view), $this->foot_views);

		// First check to see if we have any initalization files
		foreach ($views AS $view)
		{
			$view .= '-init';
			if ($dir = $this->view_exists($view))
			{
				$this->load_view($view, $dir, array());
			}
		}

		try
		{
			ob_start();
			// Now load the actual views
			foreach ($views AS $view)
				$this->load_view($view);

			ob_end_flush();
		}
		catch(ViewException $e)
		{
			ob_end_clean();
			die($e->getMessage());
		}
	}
}