<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * TRAKO_Controller
 * 
 * TRAKO_Controller Controller Class
 *
 * @access  public
 * @author  Enliven Applications
 * @version 3.0
 * 
*/
class MY_Controller extends CI_Controller
{

	/**
     * Construct
     *
     * @access  public
     * @author  Enliven Applications
     * @version 3.0
     * 
     * @return  null
     */
	public function __construct()
	{
		parent::__construct();
		
		// start benchmarking...
		$this->benchmark->mark('admin_controller_start');
		$this->load->config('app_config');

		
		// make sure the db is up to date
		// using automated migrations. This
		// is ONLY done in admin as it's a
		// potential security risk, so we'll
		// at least try to keep all that behind
		// a login.
		$this->load->library(['migration','activities','user_agent']);
		/*
        if ($this->migration->latest() === FALSE)
        {
            show_error($this->migration->error_string());
        }*/
		// load up the core library for APP
		$this->load->library('app');
		// get admin theme info
		$theme = $this->app->get_active_theme();

		// get all the settings from the db
		$settings = $this->app->get_db_config();
		

		// end benchmarking
		$this->benchmark->mark('admin_controller_end');
	}

}


/**
 * TRAKO_AdminController
 * 
 * TRAKO_AdminController Controller Class
 *
 * @access  public
 * @author  Enliven Applications
 * @version 3.0
 * 
*/
class MY_AdminController extends CI_Controller
{
	
	protected $data;
	
	/**
     * Construct
     *
     * @access  public
     * @author  Enliven Applications
     * @version 3.0
     * 
     * @return  null
     */
	public function __construct()
	{
		
		parent::__construct();
		
		// start benchmarking...
		$this->benchmark->mark('admin_controller_start');
		$this->load->config('app_config');

//		$this->output->enable_profiler(TRUE);
		
		// make sure the db is up to date
		// using automated migrations. This
		// is ONLY done in admin as it's a
		// potential security risk, so we'll
		// at least try to keep all that behind
		// a login.
		$this->load->library(['migration','activities','ion_auth','permissions','user_agent','form_validation']);
		/*
        if ($this->migration->latest() === FALSE)
        {
            show_error($this->migration->error_string());
        }*/
		// load up the core library for APP
		$this->load->library('app');

		// we're always using this in the admin area so we'll eventually autoload
		$this->load->library('ion_auth');

		//allways check if logged in.
		if (!$this->ion_auth->logged_in())
		{
			
			$this->ion_auth->logout();
			//set the current url to redirect to after login
			$uri = (!empty($_SERVER['QUERY_STRING'])) ? uri_string() ."?".$_SERVER['QUERY_STRING'] : uri_string();
			$this->session->set_userdata('previous_uri',$uri);
			$this->session->set_flashdata('message','Please login to continue');
			redirect(ADMIN.'login');
		}
		
		//lets always check if user has permission to execut any controller.
		if (!$this->permissions->has_permission($this->router->class))
		{
			show_error('You don\'t have permission to execut this action. </br> Please contact <a href="mailto:'.config_item('system_help_email').'">Admin</a> for Help');
		}

		// get admin theme info
		$theme = $this->app->get_active_theme('1');
		//		$this->session->set_userdata('__back', $this->agent->referrer());

		// get all the settings from the db
		$this->app->get_db_config();
		
		$this->template->set_theme($theme->path);
		
		$this->load->model('ion_auth_model');
		
		$this->template->set_breadcrumb('Dashboard',admin_url('dashboard'));
		//global variables
		$_user_details = $this->ion_auth->select('first_name,last_name,email,username')->user()->row();
		$this->template
				->set('page_title' , '')
				->set('site_name' , config_item('app_name'))
				->set('_user_full_name' , $_user_details->first_name. ' '. $_user_details->last_name)
				->set('_user_details', $_user_details)
				->set('sidebars', $this->permissions->build_admin_links());

		// set some partials
		$this->template
				->set_partial('flashdata', 'flashdata')
				->set_partial('sidebar', 'sidebar')
				->set_partial('header', 'header')
				->set_partial('titlebar', 'titlebar')
				->set_partial('footer', 'footer');
//				echo "<pre>"; print_r($this->template->data);exit;
		// end benchmarking
		add_script('assets/plugins/jquery-ui-1.12.1/jquery-ui.min');
		add_script('assets/plugins/datatables/DataTables-1.10.20/js/jquery.dataTables.min');
		add_script('assets/plugins/datatables/DataTables-1.10.20/js/dataTables.bootstrap.min');
		add_script('assets/plugins/datatables/Buttons-1.6.1/js/dataTables.buttons.min');
		add_script('assets/plugins/datatables/Buttons-1.6.1/js/buttons.html5.min');
		add_script('assets/plugins/datatables/Buttons-1.6.1/js/buttons.print.min');
		add_script('assets/plugins/datatables/pdfmake-0.1.36/pdfmake.min');
		add_script('assets/plugins/datatables/pdfmake-0.1.36/vfs_fonts');
		add_script('assets/js/NumberFormat');
		add_script('assets/js/ajax_queue');
		add_style('assets/plugins/datatables/Buttons-1.6.1/css/buttons.bootstrap.min');
		add_style('assets/plugins/datatables/DataTables-1.10.20/css/datatables.bootstrap.min');
		add_style('assets/plugins/jquery-ui-1.12.1/jquery-ui.min');
		$this->benchmark->mark('admin_controller_end');

		// and we're off.....
	}

	function redirect($uri , $method=null)
	{//exit($this->session->userdata('previous_uri'));
		if ($this->session->userdata('previous_uri')) {
			redirect($this->session->userdata('previous_uri'),$method);
		} else {
			redirect(ADMIN.$uri, $method);
		}
	}
	
	// ------------------------------------------------------------------------

	/**
	 * check_nonce
	 *
	 * Method for checking forms with added security nonce.
	 *
	 * @author 	Kader Bouyakoub
	 * @link 	https://goo.gl/wGXHO9
	 * @since 	1.4.0
	 *
	 * @access 	public
	 * @param 	string 	$action 	The action attached (Optional).
	 * @param 	bool 	$referrer	Whether to check referrer.
	 * @param 	string 	$name 		The name of the field used as nonce.
	 * @return 	bool
	 */
	public function check_nonce($action = null, $referrer = true, $name = '_csknonce')
	{
		// If the action is not provided, get if from the request.
		$real_action = (null !== $req = $this->input->request('action')) ? $req : -1;
		(null === $action) && $action = $real_action;

		// Initial status.
		$status = verify_nonce($this->input->request($name), $action);

		// We check referrer only if set and nonce passed test.
		if (true === $status && true === $referrer)
		{
			/**
			 * because till this line, the $status is set to TRUE,
			 * its value is changed according the referrer check status.
			 */
			$status = $this->check_referrer();
		}

		// Otherwise, return only nonce status.
		return $status;
	}

	// ------------------------------------------------------------------------

	/**
	 * check_referrer
	 *
	 * Method for comparing the request referrer to the hidden referrer field.
	 *
	 * @author 	Kader Bouyakoub
	 * @link 	https://goo.gl/wGXHO9
	 * @since 	1.4.0
	 *
	 * @uses 	CI_User_agent
	 *
	 * @access 	public
	 * @param 	string 	$referrer 	The hidden field value (optional).
	 * @param 	string 	$name 		The name of the referrer field.
	 * @return 	bool
	 */
	public function check_referrer($referrer = null, $name = '_csk_http_referrer')
	{
		(class_exists('CI_User_agent', false)) OR $this->load->library('user_agent');

		$real_referrer = $this->agent->referrer();
		(null === $referrer) && $referrer = $this->input->request($name, true);

		return (1 === preg_match("#{$referrer}$#", $real_referrer));
	}

}

class MY_AuthController extends CI_Controller {
	
	function __construct() {
		
		parent::__construct();
		
		// start benchmarking...
		$this->benchmark->mark('admin_controller_start');
		
		$this->load->config('app_config');
		
		// make sure the db is up to date
		// using automated migrations. This
		// is ONLY done in admin as it's a
		// potential security risk, so we'll
		// at least try to keep all that behind
		// a login.
		$this->load->library(['form_validation','app','activities']);
		
		// we're always using this in the admin area so we'll eventually autoload
		$this->load->library('ion_auth');
		
		// get admin theme info
		$theme = $this->app->get_active_theme('1');

		// get all the settings from the db
		$settings = $this->app->get_db_config();

		$this->template->set_theme($theme->path);
		
		$this->template->set_layout(str_replace('_admin','',$this->template->get_theme())."_auth");
		
		// set some partials
		$this->template->set_partial('flashdata', 'flashdata');
		
		//set some global vars
		$this->template->set('allow_remember',config_item('remember_users'));
		
		// end benchmarking
		$this->benchmark->mark('admin_controller_end');

		// and we're off.....
	}
	
}

function active_nav($menu_name){

	if($menu_name == get_instance()->router->fetch_class()){
		echo 'active';
	}
}