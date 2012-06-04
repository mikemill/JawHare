<?php
/**
 * @package JawHare
 * @license BSD
 * @link https://github.com/mikemill/JawHare 
 */

namespace JawHare;

/**
 * The base theme class.  Should be extended in the majority of real use 
 */
class BaseTheme
{
	/**
	 * The main view
	 * @var null|string 
	 */
	protected $view = null;
	
	/**
	 * The variables to pass to the templates
	 * @var array
	 */
	protected $view_vars = array();
	
	/**
	 * The directory where the view templates can be found
	 * @var string
	 */
	protected $viewdir = null;

	/**
	 * The directory name of this theme.  Also serves as the theme name
	 * @var string
	 */
	protected $themedir = 'Base';

	/**
	 * The views to load before the main view.
	 * @var array
	 */
	protected $head_views = array();
	
	/**
	 * The views to load after the main view
	 * @var array
	 */
	protected $foot_views = array();

	/**
	 * The HTML page title
	 * @var string
	 */
	protected $title = '';

	/**
	 * Javascript files to load
	 * @var array
	 */
	protected $jsfiles = array();
	
	/**
	 * Raw Javascript to include.
	 * Entries should not include <script> tags.
	 * Entries should include $(function(){}) blocks if they need them.
	 * @var array
	 */
	protected $rawjs = array();

	/**
	 * CSS files to load
	 * @var array
	 */
	protected $cssfiles = array();
	
	/**
	 * Raw CSS in include.  Entries should not include <style> tags
	 * @var type 
	 */
	protected $rawcss = array();

	/**
	 * The language to load
	 * @var string
	 */
	protected $lang = 'english';


	/**
	 *
	 * @param string $viewdir The directory where the views can be found.
	 */
	public function __construct($viewdir)
	{
		$this->viewdir = $viewdir;
	}

	/**
	 * Sets or gets the title
	 * @param null|string $title
	 * @return string|\JawHare\BaseTheme 
	 */
	public function title($title = null)
	{
		if ($title === null)
		{
			return $this->title;
		}
		else
		{
			$this->title = $title;
			return $this;
		}
	}

	/**
	 * Sets or gets the language
	 * @param null|string $lang
	 * @return string|\JawHare\BaseTheme 
	 */
	public function lang($lang = null)
	{
		if ($lang === null)
		{
			return $this->lang;
		}
		else
		{
			$this->lang = $lang;
			return $this;
		}
	}

	/**
	 * Sets or gets the CSS files.  If appending it adds to the list of CSS files otherwise it overwrites them.
	 * @param null|string|array $cssfiles CSS file(s) to set.  If null acts as a getter
	 * @param bool $append
	 * @return array|\JawHare\BaseTheme 
	 */
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
		return $this;
	}

	/**
	 * Sets or gets the raw CSS.
	 * @param null|string|array $rawcss Raw CSS to add.  If null acts as a getter.
	 * @param bool $append If true adds otherwise overwrites.
	 * @return array|\JawHare\BaseTheme 
	 */
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
		return $this;
	}

	/**
	 * Sets or gets the Javascript Files.
	 * @param null|string|array $jsfiles JS files to add.  If null acts as a getter.
	 * @param bool $append If true adds otherwise overwrites.
	 * @return array|\JawHare\BaseTheme 
	 */
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
		
		return $this;
	}

	/**
	 * Sets or gets the raw JS.
	 * @param null|string|array $rawjs Raw JS to add.  If null acts as a getter.
	 * @param bool $append If true adds otherwise overwrites.
	 * @return array|\JawHare\BaseTheme 
	 */
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
		
		return $this;
	}

	/**
	 * Gets or sets the main view
	 * @param null|string $view The main view.  If null acts as a getter
	 * @param null|array $view_vars Variables for this view
	 * @return string|\JawHare\BaseTheme 
	 */
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

			return $this;
		}
	}

	/**
	 * Adds a non-main view
	 * @param string $view View to add
	 * @param null|array $view_vars Variables to add for this view
	 * @param string $part Defaults to head.  Whether to add to the head or foot section.
	 * @return \JawHare\BaseTheme 
	 */
	public function addview($view, $view_vars = null, $part = 'head')
	{
		if ($part == 'head')
			$this->head_views[] = $view;
		else
			$this->foot_views[] = $view;

		if (is_array($view_vars))
			$this->view_vars = array_merge($this->view_vars, $view_vars);

		return $this;
	}

	/**
	 * Adds a variable for the views
	 * @param mixed $var Variable name
	 * @param mixed $val Value
	 * @return \JawHare\BaseTheme 
	 */
	public function add_view_var($var, $val)
	{
		$this->view_vars[$var] = $val;
		return $this;
	}

	/**
	 * The filename for the given view
	 * @param string $view Name of the view
	 * @param string $dir Theme directory
	 * @return string 
	 */
	protected function view_filename($view, $dir)
	{
		return $this->viewdir . "/$dir/{$view}.tpl";
	}

	/**
	 * Name of the language file for the given view
	 * @param string $view Name of the view
	 * @param string $dir Theme directory
	 * @param string $lang Language
	 * @return string 
	 */
	protected function lang_filename($view, $dir, $lang)
	{
		return $this->viewdir . "/{$dir}_languages/$view.$lang.php";
	}

	/**
	 * Determines if the given view exists.
	 * @param type $view Name of the view
	 * @return string|boolean Directory where the view was found or false if not found
	 */
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

	/**
	 * Load a view and execute it
	 * @param string $view Name of the view
	 * @param null|string $dir Directory for the view.  If null will attempt to find it
	 * @param null|array $variables Variables to extract to the view.  If null uses the object's view_vars
	 * @param bool $load_lang Whether to load the language file for this view (if it exists)
	 * @return mixed Return fom the view itself
	 * @throws ViewException 
	 */
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

	/**
	 * Display the output. 
	 */
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