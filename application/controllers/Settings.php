<?php
defined('BASEPATH') OR exit('No direct access to script allowed.');

class Settings extends MY_AdminController {
	private $_config = array();
	function __construct()
	{
		parent::__construct();
		$this->lang->load('auth');
		$this->lang->load('ion_auth');
	}
	
	public function index(){
		
		$tab = xss_clean($this->uri->segment(2));
		$tab = trim($tab);
		$tab = htmlspecialchars(strip_tags($tab));
		
		if($tab == NULL || $tab =='') $tab = 'general';
			
		if($this->input->post()){
			$_post_data = [];
			foreach($this->input->post() as $key => $post_data){
				
				$this->form_validation->set_rules($key,ucfirst(humanize($key)),'trim|xss_clean');
				
				$_post_data[$key] = $this->input->post($key);
			}
			
			if($this->form_validation->run()){
				
				//lets update our settings
				
				$update = $this->app->update_settings($_post_data,$tab);
				
				if($update == TRUE){
					$this->session->set_flashdata('success',lang('settings_updated'));
					redirect(current_url(), "reload");
				}else{
					$this->session->set_flashdata('error',lang('settings_update_error'));
				}
				
			}
			redirect(current_url(), "refresh");
			
		}
		
		$this->data['tabs'] = $this->app->settings_tab();
		$this->data['active_tab'] = $tab;
		$this->data['settings'] = $this->app->get_settings_list($tab);
		
		$this->template->build('settings/index', $this->data);
	}
	
	public function utility(){
		$this->data['active_tab'] = 'utility';
	}
}