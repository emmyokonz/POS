<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * CodeIgniter Template Class
 *
 * Build your CodeIgniter pages much easier with partials, breadcrumbs, layouts and themes
 *
 * @package			CodeIgniter
 * @subpackage		Libraries
 * @category		Libraries
 * @author			Philip Sturgeon
 * @license			http://philsturgeon.co.uk/code/dbad-license
 * @link			http://getsparks.org/packages/template/show
 */
class Template
{
	private $_module = '';
	private $_controller = '';
	private $_method = '';

	private $_theme = NULL;
	private $_theme_path = NULL;
	private $_layout = FALSE; // By default, dont wrap the view with anything
	private $_layout_subdir = ''; // Layouts and partials will exist in views/layouts
	// but can be set to views/foo/layouts with a subdirectory

	private $_title = '';
	private $_metadata = array();
	private $_js_files = array();
	private $_css_files = array();

	private $_partials = array();

	private $_breadcrumbs = array();

	private $_title_separator = ' | ';

	private $_parser_enabled = TRUE;
	private $_parser_body_enabled = TRUE;

	private $_theme_locations = array();

	private $_is_mobile = FALSE;

	// Minutes that cache will be alive for
	private $cache_lifetime = 0;

	private $_ci;

	private $data = array();

	/**
	 * Constructor - Sets Preferences
	 *
	 * The constructor can be passed an array of config values
	 */
	function __construct($config = array())
	{
		$this->_ci =& get_instance();

		if ( ! empty($config))
		{
			$this->initialize($config);
		}
		
		// Make sure URL helper is load then we load our helper
		(function_exists('base_url')) or $this->CI->load->helper('url');
		
		// Load events library.
		$this->_ci->load->library('events');

		log_message('debug', 'Template Class Initialized');
	}

	// --------------------------------------------------------------------

	/**
	 * Initialize preferences
	 *
	 * @access	public
	 * @param	array
	 * @return	void
	 */
	function initialize($config = array())
	{
		foreach ($config as $key => $val)
		{
			if ($key == 'theme' AND $val != '')
			{
				$this->set_theme($val);
				continue;
			}

			$this->{'_'.$key} = $val;
		}

		// No locations set in config?
		if ($this->_theme_locations === array())
		{
			// Let's use this obvious default
			$this->_theme_locations = array(APPPATH . 'themes/');
		}
		
		// Theme was set
		if ($this->_theme)
		{
			$this->set_theme($this->_theme);
		}

		// If the parse is going to be used, best make sure it's loaded
		if ($this->_parser_enabled === TRUE)
		{
			$this->_ci->load->library('parser');
		}

		// Modular Separation / Modular Extensions has been detected
		if (method_exists( $this->_ci->router, 'fetch_module' ))
		{
			$this->_module 	= $this->_ci->router->fetch_module();
		}

		// What controllers or methods are in use
		$this->_controller	= $this->_ci->router->fetch_class();
		$this->_method 		= $this->_ci->router->fetch_method();

		// Load user agent library if not loaded
		$this->_ci->load->library('user_agent');

		// We'll want to know this later
		$this->_is_mobile	= $this->_ci->agent->is_mobile();
	}

	// --------------------------------------------------------------------

	/**
	 * Magic Get function to get data
	 *
	 * @access	public
	 * @param	  string
	 * @return	mixed
	 */
	public function __get($name)
	{
		return isset($this->data[$name]) ? $this->data[$name] : NULL;
	}

	// --------------------------------------------------------------------

	/**
	 * Magic Set function to set data
	 *
	 * @access	public
	 * @param	  string
	 * @return	mixed
	 */
	public function __set($name, $value)
	{
		$this->data[$name] = $value;
	}

	// --------------------------------------------------------------------

	/**
	 * Set data using a chainable metod. Provide two strings or an array of data.
	 *
	 * @access	public
	 * @param	  string
	 * @return	mixed
	 */
	public function set($name, $value = NULL)
	{
		// Lots of things! Set them all
		if (is_array($name) OR is_object($name))
		{
			foreach ($name as $item => $value)
			{
				$this->data[$item] = $value;
			}
		}

		// Just one thing, set that
		else
		{
			$this->data[$name] = $value;
		}

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Build the entire HTML output combining partials, layouts and views.
	 *
	 * @access	public
	 * @param	string
	 * @return	void
	 */
	public function build($view, $data = array(), $return = FALSE)
	{
		// Set whatever values are given. These will be available to all view files
		is_array($data) OR $data = (array) $data;

		// Merge in what we already have with the specific data
		$this->data = array_merge($this->data, $data);

		// We don't need you any more buddy
		unset($data);
		
		

		// Does the theme have functions.php file?
		if (file_exists($this->theme_path('functions.php')))
		{
			include($this->theme_path('functions.php'));
		}
		
		$template = array();
		
		/*if (empty($this->_title))
		{
			$this->_title = $this->title();
		}*/
		
		// Always set page title
		empty($this->_title) && $this->title();

		// this is hack-y, but the only way I've found to 
		// make loading assets from the layout file...  :/
		
		$this->_body =  self::_load_view('layouts/'.$this->_layout, $this->data, $this->_parser_body_enabled, self::_find_view_folder());

		// Output template variables to the template
		$template['title']	= $this->_title;
		$template['breadcrumbs'] = $this->_breadcrumbs;
		$template['partials']	= array();
		$template['metadata']	= $this->_metadata;
		$template['css_files']	= $this->_output_css();
		$template['js_files']	= $this->_output_js();

		// Assign by reference, as all loaded views will need access to partials
		$this->data['template'] =& $template;

		foreach ($this->_partials as $name => $partial)
		{
			// We can only work with data arrays
			is_array($partial['data']) OR $partial['data'] = (array) $partial['data'];


			// If it uses a view, load it
			if (isset($partial['view']))
			{
				$template['partials'][$name] = $this->_find_view($partial['view'], $partial['data']);
			}

			// Otherwise the partial must be a string
			else
			{
				if ($this->_parser_enabled === TRUE)
				{
					$partial['string'] = $this->_ci->parser->parse_string($partial['string'], $this->data + $partial['data'], TRUE, TRUE);
				}

				$template['partials'][$name] = $partial['string'];
			}
		}
			
		// Disable sodding IE7's constant cacheing!!
		$this->_ci->output->set_header('Expires: Sat, 01 Jan 2000 00:00:01 GMT');
		$this->_ci->output->set_header('Cache-Control: no-store, no-cache, must-revalidate');
		$this->_ci->output->set_header('Cache-Control: post-check=0, pre-check=0, max-age=0');
		$this->_ci->output->set_header('Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' );
		$this->_ci->output->set_header('Pragma: no-cache');

		// Let CI do the caching instead of the browser
		$this->_ci->output->cache($this->cache_lifetime);

		// Test to see if this file
		$this->_body = $this->_find_view($view, array(), $this->_parser_body_enabled);

		// Want this file wrapped with a layout file?
		if ($this->_layout)
		{
			// Added to $this->data['template'] by refference
			$template['body'] = $this->_body;

			// Find the main body and 3rd param means parse if its a theme view (only if parser is enabled)
			$this->_body =  self::_load_view('layouts/'.$this->_layout, $this->data, $this->_parser_body_enabled, self::_find_view_folder());
		}

		// Want it returned or output to browser?
		if ( ! $return)
		{
			$this->_ci->output->set_output($this->_body);
		}

		return $this->_body;
	}

	/**
	 * Set the title of the page
	 *
	 * @access	public
	 * @param	string
	 * @return	void
	 */
	public function title()
	{
		// If we have some segments passed
		/*if (func_num_args() >= 1)
		{
			$title_segments = func_get_args();
			$this->_title = implode($this->_title_separator, $title_segments);
		}*/
		if ( ! empty($this->_title))
		{
			return $this;
		}

		if ( ! empty($args = func_get_args()))
		{
//			print_r($args);exit;
			is_array($args[0]) && $args = $args[0];
			$args[] = $this->_title;
			$this->_title = rtrim(implode($this->_title_separator, $args),' '.$this->_title_separator);
		}
		return $this;
	}


	/**
	 * Put extra javascipt, css, meta tags, etc before all other head data
	 *
	 * @access	public
	 * @param	 string	$line	The line being added to head
	 * @return	void
	 */
	public function prepend_metadata($line)
	{
		array_unshift($this->_metadata, $line);
		return $this;
	}


	/**
	 * Put extra javascipt, css, meta tags, etc after other head data
	 *
	 * @access	public
	 * @param	 string	$line	The line being added to head
	 * @return	void
	 */
	public function append_metadata($line)
	{
		$this->_metadata[] = $line;
		return $this;
	}


		/**
	 * Put extra javascipt, css, meta tags, etc after other head data
	 *
	 * @param	string	$line	The line being added to head
	 * @return	object	$this
	 */
	public function add_css(/*$files, $min_file = null, $group = 'extra'*/)
	{
//		Asset::css($files, $min_file, $group);
		if ( ! empty($css = func_get_args()))
		{
			is_array($css[0]) && $css = $css[0];
			$css = $this->_remove_extension($css, '.css');
			$this->_css_files = array_merge($this->_css_files, $css);
		}
		
		
		return $this;
	}
	
	public function prepend_css(/*$files, $min_file = null, $group = 'extra'*/)
	{
//		Asset::css($files, $min_file, $group);
		if ( ! empty($css = func_get_args()))
		{
			is_array($css[0]) && $css = $css[0];
			$css = $this->_remove_extension($css, '.css');
			$this->_css_files = array_merge($css , $this->_css_files);
		}
		
		
		return $this;
	}
	
	public function add_js(/*$files, $min_file = null, $group = 'extra'*/)
	{
//		Asset::js($files, $min_file, $group);
		
		if ( ! empty($js = func_get_args()))
		{
			is_array($js[0]) && $js = $js[0];
			$js = $this->_remove_extension($js, '.js');
			$this->_js_files = array_merge($this->_js_files, $js);
		}
		return $this;
	}

	public function prepend_js(/*$files, $min_file = null, $group = 'extra'*/)
	{
//		Asset::js($files, $min_file, $group);
		
		if ( ! empty($js = func_get_args()))
		{
			is_array($js[0]) && $js = $js[0];
			$js = $this->_remove_extension($js, '.js');
			$this->_js_files = array_merge($js,$this->_js_files);
		}
		return $this;
	}

	/**
	 * Set metadata for output later
	 *
	 * @access	public
	 * @param	  string	$name		keywords, description, etc
	 * @param	  string	$content	The content of meta data
	 * @param	  string	$type		Meta-data comes in a few types, links for example
	 * @return	void
	 */
	public function set_metadata($name, $content, $type = 'meta')
	{
		$name = htmlspecialchars(strip_tags($name));
		$content = htmlspecialchars(strip_tags($content));

		// Keywords with no comments? ARG! comment them
		if ($name == 'keywords' AND ! strpos($content, ','))
		{
			$content = preg_replace('/[\s]+/', ', ', trim($content));
		}

		switch($type)
		{
			case 'meta':
				$this->_metadata[$name] = '<meta name="'.$name.'" content="'.$content.'" />';
			break;

			case 'link':
				$this->_metadata[$content] = '<link rel="'.$name.'" href="'.$content.'" />';
			break;

			case 'og':
				$this->_metadata[$content] = '<meta property="og:'.$name.'" content="'.$content.'" />';
			break;
		}

		return $this;
	}


	/**
	 * Which theme are we using here?
	 *
	 * @access	public
	 * @param	string	$theme	Set a theme for the template library to use
	 * @return	void
	 */
	public function set_theme($theme = NULL)
	{
		$this->_theme = $theme;
		foreach ($this->_theme_locations as $location)
		{
			if ($this->_theme AND file_exists($location.$this->_theme))
			{
				$this->_theme_path = rtrim($location.$this->_theme.'/');
				break;
			}
		}

		return $this;
	}

	/**
	 * Get the current theme
	 *
	 * @access public
	 * @return string	The current theme
	 */
	 public function get_theme()
	 {
	 	return $this->_theme;
	 }

	/**
	 * Get the current theme path
	 *
	 * @access	public
	 * @return	string The current theme path
	 */
	public function get_theme_path()
	{
		return $this->_theme_path;
	}


	/**
	 * Which theme layout should we using here?
	 *
	 * @access	public
	 * @param	string	$view
	 * @return	void
	 */
	public function set_layout($view, $_layout_subdir = '')
	{
		$this->_layout = $view;

		$_layout_subdir AND $this->_layout_subdir = $_layout_subdir;

		return $this;
	}

	/**
	 * Set a view partial
	 *
	 * @access	public
	 * @param	string
	 * @param	string
	 * @param	boolean
	 * @return	void
	 */
	public function set_partial($name, $view, $data = array())
	{
		$this->_partials[$name] = array('view' => $view, 'data' => $data);
		return $this;
	}

	/**
	 * return a single view partial
	 *
	 * @access	public
	 * @param	string
	 * @param	array
	 * @return	string
	 */
	public function partial($view , $data = array())
	{
		return $this->_find_view($view,$data);
	}

	/**
	 * Set a view partial
	 *
	 * @access	public
	 * @param	string
	 * @param	string
	 * @param	array
	 * @return	void
	 */
	public function inject_partial($view , $name, $data = array())
	{
//		$this->_partials[$name] = array('string' => $string, 'data' => $data);
		$this->set($name,$this->_find_view($view,$data));
		return $this;
	}


	/**
	 * Helps build custom breadcrumb trails
	 *
	 * @access	public
	 * @param	string	$name		What will appear as the link text
	 * @param	string	$url_ref	The URL segment
	 * @return	void
	 */
	public function set_breadcrumb($name, $uri = '')
	{
		$this->_breadcrumbs[] = array('name' => $name, 'uri' => $uri );
		return $this;
	}

	/**
	 * Set a the cache lifetime
	 *
	 * @access	public
	 * @param	string
	 * @param	string
	 * @param	boolean
	 * @return	void
	 */
	public function set_cache($minutes = 0)
	{
		$this->cache_lifetime = $minutes;
		return $this;
	}


	/**
	 * enable_parser
	 * Should be parser be used or the view files just loaded normally?
	 *
	 * @access	public
	 * @param	 string	$view
	 * @return	void
	 */
	public function enable_parser($bool)
	{
		$this->_parser_enabled = $bool;
		return $this;
	}

	/**
	 * enable_parser_body
	 * Should be parser be used or the body view files just loaded normally?
	 *
	 * @access	public
	 * @param	 string	$view
	 * @return	void
	 */
	public function enable_parser_body($bool)
	{
		$this->_parser_body_enabled = $bool;
		return $this;
	}

	/**
	 * theme_locations
	 * List the locations where themes may be stored
	 *
	 * @access	public
	 * @param	 string	$view
	 * @return	array
	 */
	public function theme_locations()
	{
		return $this->_theme_locations;
	}

	/**
	 * add_theme_location
	 * Set another location for themes to be looked in
	 *
	 * @access	public
	 * @param	 string	$view
	 * @return	array
	 */
	public function add_theme_location($location)
	{
		$this->_theme_locations[] = $location;
	}

	/**
	 * theme_exists
	 * Check if a theme exists
	 *
	 * @access	public
	 * @param	 string	$view
	 * @return	array
	 */
	public function theme_exists($theme = NULL)
	{
		$theme OR $theme = $this->_theme;

		foreach ($this->_theme_locations as $location)
		{
			if (is_dir($location.$theme))
			{
				return TRUE;
			}
		}

		return FALSE;
	}

	/**
	 * get_layouts
	 * Get all current layouts (if using a theme you'll get a list of theme layouts)
	 *
	 * @access	public
	 * @param	 string	$view
	 * @return	array
	 */
	public function get_layouts()
	{
		$layouts = array();

		foreach(glob(self::_find_view_folder().'layouts/*.*') as $layout)
		{
			$layouts[] = pathinfo($layout, PATHINFO_BASENAME);
		}

		return $layouts;
	}


	/**
	 * Get Metadata
	 *
	 * @param 	string 	$place
	 * @return 	string
	 */
	public function get_metadata($place = 'header')
	{
		// We are going to set this to a blank array if this
		// does not exist in the right format, since we are going to
		// see if any overrides are in place that we can use as well.
		if ( ! isset($this->_metadata[$place]) or ! is_array($this->_metadata[$place])) {
			$this->_metadata[$place] = array();
		}

		// Go through any 'header' place overrides
		if (isset($this->_override_meta[$place])) {
			foreach ($this->_override_meta[$place] as $key => $meta) {

				// If this already exists, unset it.
				if (isset($this->_metadata[$place][$key])) {
					unset($this->_metadata[$place][$key]);
				}

				$this->_metadata[$place][$key] = $this->_override_meta[$place][$key];
			}
		}

		// Still nothing? Now we can return null.
		if ( ! $this->_metadata[$place]) {
			return null;
		}

		return implode("\n\t\t", $this->_metadata[$place]);
	}




	/**
	 * get_layouts
	 * Get all current layouts (if using a theme you'll get a list of theme layouts)
	 *
	 * @access	public
	 * @param	 string	$view
	 * @return	array
	 */
	public function get_theme_layouts($theme = NULL)
	{
		$theme OR $theme = $this->_theme;

		$layouts = array();

		foreach ($this->_theme_locations as $location)
		{
			// Get special web layouts
			if( is_dir($location.$theme.'/layouts/') )
			{
				foreach(glob($location.$theme . '/layouts/*.*') as $layout)
				{
					$layouts[] = pathinfo($layout, PATHINFO_BASENAME);
				}
				break;
			}

			// So there are no web layouts, assume all layouts are web layouts
			if(is_dir($location.$theme.'/layouts/'))
			{
				foreach(glob($location.$theme . '/layouts/*.*') as $layout)
				{
					$layouts[] = pathinfo($layout, PATHINFO_BASENAME);
				}
				break;
			}
		}

		return $layouts;
	}

	/**
	 * layout_exists
	 * Check if a theme layout exists
	 *
	 * @access	public
	 * @param	 string	$view
	 * @return	array
	 */
	public function layout_exists($layout)
	{
		// If there is a theme, check it exists in there
		if ( ! empty($this->_theme) AND in_array($layout, self::get_theme_layouts()))
		{
			return TRUE;
		}

		// Otherwise look in the normal places
		return file_exists(self::_find_view_folder().'layouts/' . $layout . self::_ext($layout));
	}

	/**
	 * load_view
	 * Load views from theme paths if they exist.
	 *
	 * @access	public
	 * @param	string	$view
	 * @param	mixed	$data
	 * @return	array
	 */
	public function load_view($view, $data = array())
	{
		return $this->_find_view($view, (array)$data);
	}

	// find layout files, they could be mobile or web
	private function _find_view_folder()
	{
		if ($this->_ci->load->get_var('template_views'))
		{
			return $this->_ci->load->get_var('template_views');
		}

		// Base view folder
		$view_folder = APPPATH.'views/';

		// Using a theme? Put the theme path in before the view folder
		if ( ! empty($this->_theme))
		{
			$view_folder = $this->_theme_path.'';
		}

		// Would they like the mobile version?
		//if ($this->_is_mobile === TRUE AND is_dir($view_folder.'mobile/'))
		//{
		//	// Use mobile as the base location for views
		//	$view_folder .= 'mobile/';
		//}
//
		//// Use the web version
		//else if (is_dir($view_folder.'web/'))
		//{
		//	$view_folder .= 'web/';
		//}
//
		// Things like views/admin/web/view admin = subdir
		if ($this->_layout_subdir)
		{
			$view_folder .= $this->_layout_subdir.'/';
		}

		
		// If using themes store this for later, available to all views
		$this->_ci->load->vars('template_views', $view_folder);
		

		return $view_folder;
	}

	// A module view file can be overriden in a theme
	private function _find_view($view, array $data, $parse_view = TRUE)
	{
		// Only bother looking in themes if there is a theme
		if ( ! empty($this->_theme))
		{
			foreach ($this->_theme_locations as $location)
			{
				$theme_views = array(
					$this->_theme . '/partials/' . $view,
					$this->_theme . '/views/' . $view
				);

				foreach ($theme_views as $theme_view)
				{
					if (file_exists($location . $theme_view . self::_ext($theme_view)))
					{
						return self::_load_view($theme_view, $this->data + $data, $parse_view, $location);
					}
				}
			}
		}

		// Not found it yet? Just load, its either in the module or root view
		return self::_load_view($view, $this->data + $data, $parse_view);
	}

	private function _load_view($view, array $data, $parse_view = TRUE, $override_view_path = NULL)
	{
		
		// Sevear hackery to load views from custom places AND maintain compatibility with Modular Extensions
		if ($override_view_path !== NULL)
		{
			if ($this->_parser_enabled === TRUE AND $parse_view === TRUE)
			{
				// Load content and pass through the parser
				$content = $this->_ci->parser->parse_string($this->_ci->load->file(
					$override_view_path.$view.self::_ext($view), 
					TRUE
					
				), $data, TRUE);
			}

			else
			{
//				echo '<pre>'; print_r($data);exit;
				$this->_ci->load->vars($data);
				
				// Load it directly, bypassing $this->load->view() as ME resets _ci_view
				$content = $this->_ci->load->file(
					$override_view_path.$view.self::_ext($view),
					TRUE
				);
//				echo '<pre>'; print_r($content);exit;
			}
		}

		// Can just run as usual
		else
		{
			// Grab the content of the view (parsed or loaded)
			$content = ($this->_parser_enabled === TRUE AND $parse_view === TRUE)

				// Parse that bad boy
				? $this->_ci->parser->parse($view, $data, TRUE)

				// None of that fancy stuff for me!
				: $this->_ci->load->view($view, $data, TRUE);
		}

		return $content;
	}

	private function _guess_title()
	{
		$this->_ci->load->helper('inflector');

		// Obviously no title, lets get making one
		$title_parts = array();

		// If the method is something other than index, use that
		if ($this->_method != 'index')
		{
			$title_parts[] = $this->_method;
		}

		// Make sure controller name is not the same as the method name
		if ( ! in_array($this->_controller, $title_parts))
		{
			$title_parts[] = $this->_controller;
		}

		// Is there a module? Make sure it is not named the same as the method or controller
		if ( ! empty($this->_module) AND ! in_array($this->_module, $title_parts))
		{
			$title_parts[] = $this->_module;
		}

		// Glue the title pieces together using the title separator setting
		$title = humanize(implode($this->_title_separator, $title_parts));

		return $title;
	}

	private function _ext($file)
	{
		return pathinfo($file, PATHINFO_EXTENSION) ? '' : '.php';
	}
	
	// ------------------------------------------------------------------------

    /**
     * Removes files extension
     * @access 	public
     * @param 	mixed 	string or array
     * @return 	mixed 	string or array
     */
    protected function _remove_extension($file, $ext = '.css')
    {
    	// In case of multiple items
    	if (is_array($file))
    	{
    		$file = array_map(function($f) use ($ext) {
    			$f = preg_replace('/'.$ext.'$/', '', $f);
    			return $f;
    		}, $file);
    	}
    	// In case of a single element
    	else
    	{
    		$file = preg_replace('/'.$ext.'$/', '', $file);
    	}

    	return $file;
    }


// ------------------------------------------------------------------------
	
	/**
	 * Returns the array of loaded CSS files
	 * @access 	public
	 * @param 	none
	 * @return 	array
	 */
	public function get_css()
	{
		return $this->_css_files;
	}

    /**
     * Returns the full url to css file
     * @param   string  $file   filename with or without .css extension
     * @return  string
     */
    public function css_url($file = null, $folder = null)
    {
    	// If a valid URL is passed, we simply return it
        if (filter_var($file, FILTER_VALIDATE_URL) !== false) 
        {

        	return $this->_remove_extension($file, '.css').'.css';
        }

        $ver = '';
        if (strpos($file, '?') !== false) 
        {
            $args = explode('?', $file);
            $file = $args[0];
            $ver  = '?'.$args[1];
        }
        $file = $this->_remove_extension($file, '.css').'.css';

        if ($folder !== null)
        {
        	$url = base_url("content/{$folder}");
        }
        else
        {
        	$url = $this->theme_url();
        }

        $url .= (strstr($file, '/')) ? "/{$file}{$ver}" : "/css/{$file}{$ver}";

		return preg_replace('/([^:])(\/{2,})/', '$1/', $url);
    }

    /**
     * Returns the full css <link> tag
     * 
     * @param   string  $file   filename to load
     * @param   string  $cdn    to use in case of CDN user
     * @param   mixed   $attr   attributes to append to string
     * 
     * @return  string
     */
    public function css($file, $cdn = null, $attrs = '', $folder = null)
    {
    	// Only if a $file a requested
        if ($file) 
        {
        	// Use the 2nd parameter if it's set & the CDN use is enabled.
            ($this->cdn_enabled && $cdn !== null) && $file = $cdn;

            // Return the full link tag
            return '<link rel="stylesheet" type="text/css" href="'.$this->css_url($file, $folder).'"'._stringify_attributes($attrs).'>'."\n";
        }

        return null;
    }

	
	/**
	 * Collect all additional CSS files and prepare them for output
	 * @access 	protected
	 * @param 	none
	 * @return 	string
	 */
	protected function _output_css()
	{
		$css = array();

		Events::trigger('enqueue_styles');
		
		foreach ($this->_css_files as $file) 
		{
			// In case of an array, the first element is the local file
			// while the second shoud be the CDN served file.
			if (is_array($file)) 
			{
				$css[] = $this->css($file[0], $file[1]);
			}
			else 
			{
				$css[] = $this->css($file);
			}
		}
		
		return implode("\t\t", $css);
	}
//--------------------------------------------------------------------------

	
	/**
	 * Returns the array of loaded JS files
	 * @access 	public
	 * @param 	none
	 * @return 	array
	 */
	public function get_js()
	{
		return $this->js_files;
	}

    /**
     * Returns the full url to js file
     * @param   string  $file   filename with or without .js extension
     * 
     * @return  string
     */
    public function js_url($file = null, $folder = null)
    {
    	// If a valid URL is passed, we simply return it
        if (filter_var($file, FILTER_VALIDATE_URL) !== false) 
        {

        	return $this->_remove_extension($file, '.js').'.js';
        }

        $ver = '';
        if (strpos($file, '?') !== false) 
        {
            $args = explode('?', $file);
            $file = $args[0];
            $ver  = '?'.$args[1];
        }
        $file = $this->_remove_extension($file, '.js').'.js';

        if ($folder !== null)
        {
        	$url = base_url("content/{$folder}");
        }
        else
        {
        	$url = $this->theme_url();
        }

        $url .= (strstr($file, '/')) ? "/{$file}{$ver}" : "/js/{$file}{$ver}";

		return preg_replace('/([^:])(\/{2,})/', '$1/', $url);
    }

    /**
     * Returns the full js <link> tag
     * 
     * @param   string  $file   filename to load
     * @param   string  $cdn    to use in case of CDN user
     * @param   mixed   $attr   attributes to append to string
     * 
     * @return  string
     */
    public function js($file, $cdn = null, $attrs = '', $folder = null)
    {
    	// Only if a $file a requested
        if ($file)
        {
        	// Use the 2nd parameter if it's set & the CDN use is enabled.
            ($this->cdn_enabled && $cdn !== null) && $file = $cdn;
            return '<script type="text/javascript" src="'.$this->js_url($file, $folder).'"'._stringify_attributes($attrs).'></script>'."\n";
        }
        return null;
    }

	
	/**
	 * Collect all additional JS files and prepare them for output
	 * @access 	protected
	 * @param 	none
	 * @return 	string
	 */
	protected function _output_js()
	{
		$js = array();

		Events::trigger('enqueue_scripts');
		
		foreach ($this->_js_files as $file) 
		{
			// In case of an array, the first element is the local file
			// while the second shoud be the CDN served file.
			if (is_array($file)) 
			{
				$js[] = $this->js($file[0], $file[1]);
			}
			else 
			{
				$js[] = $this->js($file);
			}
		}
		
		return implode("\t\t", $js);
	}

	/**
	 * Collectes all additional metadata and prepare them for output
	 * 
	 * @access 	protected
	 * @param 	none
	 * 
	 * @return 	string
	 */
	protected function _output_meta()
	{
		$output = '';

		Events::trigger('enqueue_metadata');

		if ( ! empty($this->metadata))
		{
			foreach($this->metadata as $key => $val)
			{
				list($type, $name) = explode('::', $key);
				$content = isset($val['content'])? $val['content']: null;
				$attrs = isset($val['attrs'])? $val['attrs']: null;
				$output .= $this->meta($name, $content, $type, $attrs);
			}
		}

		return $output;
	}
	
	
    // ------------------------------------------------------------------------
    // !URLs: Assets and Uploads
    // ------------------------------------------------------------------------

	/**
	 * Returns the URL to the theme's folder.
	 * @access 	public
	 * @param 	string 	$uri 	in case you want to link a file.
	 * @return 	string
	 */
	public function theme_url($uri = '')
	{
		(function_exists('base_url')) OR $this->CI->load->helper('url');

		$url = ($this->cdn_enabled == true && ! empty($this->cdn_server))
			? $this->cdn_server
			: base_url();
		return preg_replace(
			'/([^:])(\/{2,})/',
			'$1/',
			"{$url}/content/themes/{$this->_theme}/{$uri}"
		);
	}

	/**
	 * Returns the real path to the theme folder.
	 * @access 	public
	 * @param 	string 	$uri 	file name.
	 * @return 	string 	the path if found, else FALSE.
	 */
	public function theme_path($uri = '')
	{
		return realpath(FCPATH.'content/themes/'.$this->_theme.'/'.$uri);
	}

	/**
	 * Changes the folder to 'uploads' only
	 * @access 	public
	 * @param 	string 	$uri 	path to file
	 * @return 	string
	 */
	public function upload_url($uri = '')
	{
		(function_exists('base_url')) OR $this->CI->load->helper('url');
		return preg_replace(
			'/([^:])(\/{2,})/',
			'$1/',
			base_url("content/uploads/{$uri}")
		);
	}

	/**
	 * Returns the real path to the uploads folder.
	 * @access 	public
	 * @param 	string 	$uri 	file name.
	 * @return 	string 	the path if found, else FALSE.
	 */
	public function upload_path($uri = '')
	{
		return realpath(FCPATH."content/uploads/{$uri}");
	}

	/**
	 * Changes the folder to 'common' folder.
	 * @access 	public
	 * @param 	string 	$uri 	path to file
	 * @return 	string
	 */
	public function common_url($uri = '')
	{
		(function_exists('base_url')) OR $this->CI->load->helper('url');
		return preg_replace(
			'/([^:])(\/{2,})/',
			'$1/',
			base_url("content/common/{$uri}")
		);
	}

	/**
	 * Returns the real path to the common folder.
	 * @access 	public
	 * @param 	string 	$uri 	file name.
	 * @return 	string 	the path if found, else FALSE.
	 */
	public function common_path($uri = '')
	{
		return realpath(FCPATH."content/common/{$uri}");
	}

}
// END Template class

// ------------------------------------------------------------------------

if ( ! function_exists('get_theme_url'))
{
	/**
	 * Returns the URL to the theme folder.
	 * @param 	string 	$uri 	string to be appended.
	 * @return 	string.
	 */
	function get_theme_url($uri = '')
	{
		return get_instance()->template->theme_url($uri);
	}
}

if ( ! function_exists('theme_url'))
{
	/**
	 * Unlike the function above, this one echoes the URL.
	 * @param 	string 	$uri 	string to be appended.
	 */
	function theme_url($uri = '')
	{
		echo get_instance()->template->theme_url($uri);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('get_theme_path'))
{
	/**
	 * Returns the URL to the theme folder.
	 * @param 	string 	$uri 	string to be appended.
	 * @return 	string.
	 */
	function get_theme_path($uri = '')
	{
		return get_instance()->template->theme_path($uri);
	}
}

if ( ! function_exists('theme_path'))
{
	/**
	 * Unlike the function above, this one echoes the URL.
	 * @param 	string 	$uri 	string to be appended.
	 */
	function theme_path($uri = '')
	{
		echo get_instance()->template->theme_path($uri);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('get_upload_url'))
{
	/**
	 * Returns the URL to the uploads folder.
	 * @param 	string 	$uri 	string to be appended.
	 * @return 	string.
	 */
	function get_upload_url($uri = '')
	{
		return get_instance()->template->upload_url($uri);
	}
}

if ( ! function_exists('upload_url'))
{
	/**
	 * Unlike the function above, this one echoes the URL.
	 * @param 	string 	$uri 	string to be appended.
	 */
	function upload_url($uri = '')
	{
		echo get_instance()->template->upload_url($uri);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('get_upload_path'))
{
	/**
	 * Returns the URL to the uploads folder.
	 * @param 	string 	$uri 	string to be appended.
	 * @return 	string.
	 */
	function get_upload_path($uri = '')
	{
		return get_instance()->template->upload_path($uri);
	}
}

if ( ! function_exists('upload_path'))
{
	/**
	 * Unlike the function above, this one echoes the URL.
	 * @param 	string 	$uri 	string to be appended.
	 */
	function upload_path($uri = '')
	{
		echo get_instance()->template->upload_path($uri);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('get_common_url'))
{
	/**
	 * Returns the URL to the commons folder.
	 * @param 	string 	$uri 	string to be appended.
	 * @return 	string.
	 */
	function get_common_url($uri = '')
	{
		return get_instance()->template->common_url($uri);
	}
}

if ( ! function_exists('common_url'))
{
	/**
	 * Unlike the function above, this one echoes the URL.
	 * @param 	string 	$uri 	string to be appended.
	 */
	function common_url($uri = '')
	{
		echo get_instance()->template->common_url($uri);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('get_common_path'))
{
	/**
	 * Returns the URL to the commons folder.
	 * @param 	string 	$uri 	string to be appended.
	 * @return 	string.
	 */
	function get_common_path($uri = '')
	{
		return get_instance()->template->common_path($uri);
	}
}

if ( ! function_exists('common_path'))
{
	/**
	 * Unlike the function above, this one echoes the URL.
	 * @param 	string 	$uri 	string to be appended.
	 */
	function common_path($uri = '')
	{
		echo get_instance()->template->common_path($uri);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('css_url'))
{
    /**
     * Returns the full url to css file
     * @param   string  $file   filename with or without .css extension
     * @return  string
     */
    function css_url($file = null, $folder = null)
    {
        return get_instance()->template->css_url($file, $folder);
    }
}

if ( ! function_exists('css'))
{
    /**
     * Returns the full css <link> tag
     * @param   string  $file   filename to load
     * @param   string  $cdn    to use in case of CDN user
     * @param   mixed   $attr   attributes to append to string
     * @return  string
     */
    function css($file = null, $cdn = null, $attrs = '', $folder = null)
    {
        return get_instance()->template->css($file, $cdn, $attrs, $folder);
    }
}

if ( ! function_exists('add_style'))
{
	/**
	 * Enqueue a single or multiple style sheet.
	 */
	function add_style()
	{
		return call_user_func_array(
			array(get_instance()->template, 'add_css'),
			func_get_args()
		);
	}
}

if ( ! function_exists('prepend_style'))
{
	/**
	 *  Prepend StyleSheets
	 */
	function prepend_style()
	{
		return call_user_func_array(
			array(get_instance()->template, 'prepend_css'),
			func_get_args()
		);
	}
}

// ----------------------------------------------------------------------------

if ( ! function_exists('js_url'))
{
    /**
     * Returns the full url to js file
     * @param   string  $file   filename with or without .js extension
     * @return  string
     */
    function js_url($file, $folder = null)
    {
        return get_instance()->template->js_url($file, $folder);
    }
}

if ( ! function_exists('js'))
{
    /**
     * Returns the full JS <script> tag
     * @param   string  $file   filename to load
     * @param   string  $cdn    to use in case of CDN user
     * @param   mixed   $attr   attributes to append to string
     * @return  string
     */
    function js($file = null, $cdn = null, $attrs = '', $folder = null)
    {
        return get_instance()->template->js($file, $cdn, $attrs, $folder);
    }
}

if ( ! function_exists('add_script'))
{
	/**
	 * Enqueue a single or multiple script sheet.
	 */
	function add_script()
	{
		return call_user_func_array(
			array(get_instance()->template, 'add_js'),
			func_get_args()
		);
	}
}

if ( ! function_exists('prepend_script'))
{
	/**
	 *  Prepend Scripts
	 */
	function prepend_script()
	{
		return call_user_func_array(
			array(get_instance()->template, 'prepend_js'),
			func_get_args()
		);
	}
}

if ( ! function_exists('theme'))
{
	/**
	 *  get the current theme
	 */
	function theme()
	{
		return get_instance()->template->_theme;
	}
}

if ( ! function_exists('layout'))
{
	/**
	 *  get the current theme layout
	 */
	function layout()
	{
		echo get_instance()->template->_layout;
	}
}
