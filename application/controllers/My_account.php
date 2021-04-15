<?php
defined('BASEPATH') or exit('No direct access to script is allowed');

class My_account extends MY_AdminController {
	function __construct() {
		parent::__construct();
		$this->user_details = $this->ion_auth_model->user($this->session->userdata('user_id'))->row();
	}
	
	public function index()
	{
		$user = $this->data['user']=$this->user_details;
		
		$group = null;
		foreach($this->ion_auth_model->get_users_groups()->result() as $_group){
			$group .= $_group->description .', ';
		}
		
		$this->data['user']->{'full_name'} = $this->data['user']->first_name.' '.$this->data['user']->last_name;
		$this->data['user']->{'groups'} = rtrim($group,', ');
		
		// validate form input
		$this->form_validation->set_rules('first_name', $this->lang->line('edit_user_validation_fname_label'), 'trim|required');
		$this->form_validation->set_rules('last_name', $this->lang->line('edit_user_validation_lname_label'), 'trim|required');
		$this->form_validation->set_rules('phone', $this->lang->line('edit_user_validation_phone_label'), 'trim|required');
		$this->form_validation->set_rules('address','Address', 'trim|required');

		if (isset($_POST) && !empty($_POST)) {

			// update the password if it was posted
			if ($this->input->post('password')) {
				$this->form_validation->set_rules('password', $this->lang->line('edit_user_validation_password_label'), 'required|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']|max_length[' . $this->config->item('max_password_length', 'ion_auth') . ']|matches[password_confirm]');
				$this->form_validation->set_rules('password_confirm', $this->lang->line('edit_user_validation_password_confirm_label'), 'required');
			}

			if ($this->form_validation->run() === TRUE) {
				
				$data = array(
					'first_name'=> $this->input->post('first_name'),
					'last_name' => $this->input->post('last_name'),
					'phone'     => $this->input->post('phone'),
					'address'     => $this->input->post('address'),
				);

				// update the password if it was posted
				if ($this->input->post('password')) {
					$data['password'] = $this->input->post('password');
				}
				$this->db->trans_start();
				
				// check to see if we are updating the user
				$update = $this->ion_auth->update($user->id, $data);

				// check if the update was successful before commitin to database
				if ($this->db->trans_status() && $update) {

					$this->db->trans_commit();

					// redirect them back to the admin page if admin, or to the base url if non admin
					$this->session->set_flashdata('success', $this->ion_auth->messages());
					redirect(current_url(),'reload');

				}
				else
				{
					$this->db->trans_rollback();

					// redirect them back to the admin page if admin, or to the base url if non admin
					$this->session->set_flashdata('error', $this->ion_auth->errors());
				}

			}
		}


		// set the flash data error message if there is one
		$this->data['info'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('info')));
//echo config_item('identity');exit;
		$this->data['first_name'] = array(
				'name' => 'first_name',
				'id'   => 'first_name',
				'type' => 'text',
				'class'   => 'form-control',
				'value'=> $this->form_validation->set_value('first_name',$user->first_name),
			);
			$this->data['last_name'] = array(
				'name' => 'last_name',
				'id'   => 'last_name',
				'type' => 'text',
				'class'   => 'form-control',
				'value'=> $this->form_validation->set_value('last_name',$user->last_name),
			);
			$this->data['address'] = array(
				'name' => 'address',
				'id'   => 'address',
				'type' => 'textarea',
				'class'   => 'form-control',
				'value'=> $this->form_validation->set_value('address',$user->address),
			);
			$this->data['phone'] = array(
				'name' => 'phone',
				'id'   => 'phone',
				'type' => 'text',
				'class'   => 'form-control',
				'value'=> $this->form_validation->set_value('phone',$user->phone),
			);
			$this->data['password'] = array(
				'name' => 'password',
				'id'   => 'password',
				'type' => 'password',
				'class'   => 'form-control',
				'value'=> $this->form_validation->set_value('password'),
			);
			$this->data['password_confirm'] = array(
				'name' => 'password_confirm',
				'id'   => 'password_confirm',
				'class'   => 'form-control',
				'type' => 'password',
				'value'=> $this->form_validation->set_value('password_confirm'),
			);
		
		$activities = $this->activities->get_many('user_id', $this->session->userdata('user_id'), config_item('activity_limit'));
			
			// Loop through activities to complete data.
		if ($activities)
		{
			foreach ($activities as &$activity)
			{
				$action = ($this->session->userdata('user_id')== $activity->user_id
							? 'You'
							: $this->ion_auth->user($activity->user_id)->username).' '. $activity->action;
							
				$activity->action = html_tag('p',
					'class="text-left m-b-0"',
					$action.html_tag('br')
					);
				// IP location link.
				$activity->ip_address = anchor(
					'https://www.iptolocation.net/trace-'.$activity->ip_address,
					$activity->ip_address,
					'target="_blank"'
				);
			}
		}
		$this->data['activities'] = $activities;
		
		$this->template->build('account/index',$this->data);
		
	}
	
	/**
	 * Log the user out
	 */
	public function logout()
	{
		$this->data['title'] = "Logout";

		// log the user out
		$logout = $this->ion_auth->logout();

		// redirect them to the login page
		$this->session->set_flashdata('info', $this->ion_auth->messages());
		redirect(ADMIN.'login', 'refresh');
	}
	
	public function settings(){
		
	}
}