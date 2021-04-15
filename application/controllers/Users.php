<?php
defined("BASEPATH")or exit('No direct access to script allowed.');

class Users extends MY_AdminController
{
	function __construct()
	{
		parent::__construct();
		$this->lang->load('auth');
		$this->lang->load('ion_auth');
	}

	function index()
	{
		//this will be present in all methods that will need top tabs.
		$this->data['active_tab'] = 'all users';
		// set the flash data error message if there is one
		$this->data['info'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('info');

		//list the users
		$this->data['users'] = $this->ion_auth_model->users()->result();
		foreach ($this->data['users'] as $k => $user) {
			$this->data['users'][$k]->groups = $this->ion_auth->get_users_groups($user->id)->result();
		}
		$this->template->build('users/index',$this->data);
	}

	function add_user()
	{
		//check if the user can create an account;
		if(!has_action('create')){
			$this->session->set_flashdata('error',$this->lang->line('action_no_access'));
			redirect(ADMIN.'users');
		}
		
		//this will be present in all methods that will need top tabs.
		$this->data['active_tab'] = 'all users';

		if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_admin()) 
		{
			redirect('auth', 'refresh');
		}

		$tables          = $this->config->item('tables', 'ion_auth');
		$identity_column = $this->config->item('identity', 'ion_auth');
		$this->data['identity_column'] = $identity_column;

		// validate form input
		$this->form_validation->set_rules('first_name', $this->lang->line('create_user_validation_fname_label'), 'trim|required');
		$this->form_validation->set_rules('last_name', $this->lang->line('create_user_validation_lname_label'), 'trim|required');
		if ($identity_column !== 'email') {
			$this->form_validation->set_rules('identity', $this->lang->line('create_user_validation_identity_label'), 'trim|required|is_unique[' . $tables['users'] . '.' . $identity_column . ']');
			$this->form_validation->set_rules('email', $this->lang->line('create_user_validation_email_label'), 'trim|required|valid_email');
		}
		else
		{
			$this->form_validation->set_rules('email', $this->lang->line('create_user_validation_email_label'), 'trim|required|valid_email|is_unique[' . $tables['users'] . '.email]');
		}
		$this->form_validation->set_rules('phone', $this->lang->line('create_user_validation_phone_label'), 'trim|callback_validate_phone');
		$this->form_validation->set_rules('password', $this->lang->line('create_user_validation_password_label'), 'required|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']|matches[password_confirm]');
		$this->form_validation->set_rules('password_confirm', $this->lang->line('create_user_validation_password_confirm_label'), 'required');
		
		//set message for callbacks
		$this->form_validation->set_message('validate_phone','Provide a valide phone number');
		
		if ($this->form_validation->run() === TRUE) {
			$email           = strtolower($this->input->post('email'));
			$identity        = ($identity_column === 'email') ? $email : $this->input->post('identity');
			$password        = $this->input->post('password');

			$additional_data = array(
				'first_name'=> $this->input->post('first_name'),
				'last_name' => $this->input->post('last_name'),
				'phone'     => $this->input->post('phone'),
			);
		}
		if ($this->form_validation->run() === TRUE && $id = $this->ion_auth->register($identity, $password, $email, $additional_data)) {
			
			log_activity($this->session->userdata('user_id'), 'Added a new user #'.$id);
			// check to see if we are creating the user
			// redirect them back to the admin page
			$this->session->set_flashdata('success', $this->ion_auth->messages());
			redirect(ADMIN."users", 'refresh');
		}
		else
		{
			// display the create user form
			// set the flash data error message if there is one
			if(validation_errors() || $this->ion_auth->errors() || $this->session->flashdata('message'))
			{
				$this->session->set_flashdata($this->input->post());
				$this->session->set_flashdata('error',(validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message'))));
				redirect(ADMIN."users",'refresh');
			}
			
			
			$this->data['first_name'] = array(
				'name' => 'first_name',
				'id'   => 'first_name',
				'type' => 'text',
				'class'   => 'form-control',
//				'value'=> $this->form_validation->set_value('first_name'),
				'value'=> $this->session->flashdata('first_name'),
			);
			$this->data['last_name'] = array(
				'name' => 'last_name',
				'id'   => 'last_name',
				'type' => 'text',
				'class'   => 'form-control',
//				'value'=> $this->form_validation->set_value('last_name'),
				'value'=> $this->session->flashdata('last_name'),
			);
			$this->data['identity'] = array(
				'name' => 'identity',
				'id'   => 'identity',
				'type' => 'text',
				'class'   => 'form-control',
//				'value'=> $this->form_validation->set_value('identity'),
				'value'=> $this->session->flashdata('identity'),
			);
			$this->data['email'] = array(
				'name' => 'email',
				'id'   => 'email',
				'type' => 'text',
				'class'   => 'form-control',
//				'value'=> $this->form_validation->set_value('email'),
				'value'=> $this->session->flashdata('email'),
			);
			$this->data['phone'] = array(
				'name' => 'phone',
				'id'   => 'phone',
				'type' => 'text',
				'class'   => 'form-control',
//				'value'=> $this->form_validation->set_value('phone'),
				'value'=> $this->session->flashdata('phone'),
			);
			$this->data['password'] = array(
				'name' => 'password',
				'id'   => 'password',
				'type' => 'password',
				'class'   => 'form-control',
//				'value'=> $this->form_validation->set_value('password'),
				'value'=> $this->session->flashdata('password'),
			);
			$this->data['password_confirm'] = array(
				'name' => 'password_confirm',
				'id'   => 'password_confirm',
				'class'   => 'form-control',
				'type' => 'password',
//				'value'=> $this->form_validation->set_value('password_confirm'),
				'value'=> $this->session->flashdata('password_confirm'),
			);

			$this->template->build('users/new_user', $this->data);
		}

	}
	
	function validate_phone($phone)
	{
		return validate_phone_number($phone);
	}

	function edit_user($id = NULL)
	{
		//this will be present in all methods that will need top tabs.
		$this->data['active_tab'] = 'all users';

		$user          = $this->ion_auth->user($id)->row();
		$currentGroups = $this->ion_auth->get_users_groups($id)->result();
		$groups        = $this->ion_auth->groups()->result_array();

		if ($this->ion_auth->is_admin()) {
			foreach ($groups as $k=>$group)
			{

				$this->data['groups'][$k]['gID'] = $group['id'];
				$this->data['groups'][$k]['name'] = $group['name'];
				$this->data['groups'][$k]['checked'] = null;
				$this->data['groups'][$k]['item'] = null;
				foreach ($currentGroups as $grp) {
					if ($this->data['groups'][$k]['gID'] == $grp->id) {
						$this->data['groups'][$k]['checked'] = ' checked="checked"';
						break;
					}
				}
			}
		}

		$this->data['permissions'] = ($this->permissions->get_user_perms_actions($id,$currentGroups));

		// validate form input
		$this->form_validation->set_rules('first_name', $this->lang->line('edit_user_validation_fname_label'), 'trim|required');
		$this->form_validation->set_rules('last_name', $this->lang->line('edit_user_validation_lname_label'), 'trim|required');
		$this->form_validation->set_rules('phone', $this->lang->line('edit_user_validation_phone_label'), 'trim|required|callback_validate_phone');
		
		//set message for callbacks
		$this->form_validation->set_message('validate_phone','Provide a valide phone number');
		
		if (isset($_POST) && !empty($_POST)) {

			// update the password if it was posted
			if ($this->input->post('password')) {
				$this->form_validation->set_rules('password', $this->lang->line('edit_user_validation_password_label'), 'required|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']|max_length[' . $this->config->item('max_password_length', 'ion_auth') . ']|matches[password_confirm]');
				$this->form_validation->set_rules('password_confirm', $this->lang->line('edit_user_validation_password_confirm_label'), 'required');
			}

			if ($this->form_validation->run() === TRUE) {
				//check if the user can update an account;
				if(!has_action('update')){
					$this->session->set_flashdata('error',$this->lang->line('action_no_access'));
					redirect(ADMIN.'users');
				}
				$data = array(
					'first_name'=> $this->input->post('first_name'),
					'last_name' => $this->input->post('last_name'),
					'phone'     => $this->input->post('phone'),
				);

				// update the password if it was posted
				if ($this->input->post('password')) {
					$data['password'] = $this->input->post('password');
				}
				$this->db->trans_start();
				// Only allow updating groups if user is admin
				if ($this->ion_auth->is_admin()) {
					// Update the groups user belongs to
					$groupData = $this->input->post('groups');

					if (isset($groupData) && !empty($groupData)) {

						$this->ion_auth->remove_from_group('', $id);

						foreach ($groupData as $grp) {
							$this->ion_auth->add_to_group($grp, $id);
						}

					}
					$perms = $this->input->post('permissions');
					if (isset($perms) && !empty($perms))
					{
						$this->permissions->add_user_actions($perms,$id);
					}
				}
				// check to see if we are updating the user
				$update = $this->ion_auth->update($user->id, $data);
				//				echo " < pre > ";print_r($this->input->post());exit;

				// check if the update was successful before commitin to database
				if ($this->db->trans_status() && $update) 
				{
					log_activity($this->session->userdata('user_id'), 'Updated user account #'.$id);
					
					$this->db->trans_commit();

					// redirect them back to the admin page if admin, or to the base url if non admin
					$this->session->set_flashdata('success', $this->ion_auth->messages());
					redirect(ADMIN.'users');

				}
				else
				{
					$this->db->trans_rollback();

					// redirect them back to the admin page if admin, or to the base url if non admin
					$this->session->set_flashdata('error', $this->ion_auth->errors());
				}

			}
		}


		// display the create user form
		// set the flash data error message if there is one
		if(validation_errors() || $this->ion_auth->errors() || $this->session->flashdata('message'))
		{
			$this->session->set_flashdata($this->input->post());
			$this->session->set_flashdata('error',(validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message'))));
			redirect(ADMIN."users",'refresh');
		}
			
		// pass the user to the view
		$this->data['user'] = $user;
		//		$this->data['groups'] = $groups;
		$this->data['currentGroups'] = $currentGroups;

		$this->data['first_name'] = array(
			'name' => 'first_name',
			'id'   => 'first_name',
			'class'=> 'form-control',
			'type' => 'text',
			'value'=> $this->form_validation->set_value('first_name', $user->first_name),
		);
		$this->data['last_name'] = array(
			'name' => 'last_name',
			'id'   => 'last_name',
			'class'=> 'form-control',
			'type' => 'text',
			'value'=> $this->form_validation->set_value('last_name', $user->last_name),
		);
		$this->data['email'] = array(
			'name'    => 'email',
			'id'      => 'email',
			'class'   => 'form-control',
			'type'    => 'text',
			'readonly'=>TRUE,
			'value'   => $this->form_validation->set_value('email', $user->email),
		);
		$this->data['phone'] = array(
			'name' => 'phone',
			'id'   => 'phone',
			'class'=> 'form-control',
			'type' => 'text',
			'value'=> $this->form_validation->set_value('phone', $user->phone),
		);

		//only the user can change password
		//admins can only send password reset request to any user.
		if ($id == $this->session->userdata('user_id'))
		{

			$this->data['password'] = array(
				'name' => 'password',
				'id'   => 'password',
				'class'=> 'form-control',
				'type' => 'password'
			);
			$this->data['password_confirm'] = array(
				'name' => 'password_confirm',
				'id'   => 'password_confirm',
				'class'=> 'form-control',
				'type' => 'password'
			);
		}

		$this->template->build('users/edit_user',$this->data);
	}
	
	/**
	 * Activate the user
	 *
	 * @param int         $id   The user ID
	 * @param string|bool $code The activation code
	 */
	public function activate($id)
	{
		//check if the user can update an account;
		if(!has_action('update')){
			$this->session->set_flashdata('error',$this->lang->line('action_no_access'));
			redirect(ADMIN."users");
		}
		if ($this->ion_auth->is_admin())
		{
			$activation = $this->ion_auth->activate($id);
		}

		if ($activation)
		{
			log_activity($this->session->userdata('user_id'), 'Activated a user #'.$id);
			// redirect them to the auth page
			$this->session->set_flashdata('success', $this->ion_auth->messages());
			redirect(ADMIN."users", 'refresh');
		}
		else
		{
			// redirect them to the forgot password page
			$this->session->set_flashdata('error', $this->ion_auth->errors());
			redirect(ADMIN."forgot_password", 'refresh');
		}
	}
	
	/**
	 * Deactivate the user
	 *
	 * @param int|string|null $id The user ID
	 */
	public function deactivate($id = NULL)
	{
		//check if the user can update an account;
		if(!has_action('update')){
			$this->session->set_flashdata('error',$this->lang->line('action_no_access'));
			redirect(ADMIN."users");
		}
		$id = (int)$id;
		// do we really want to deactivate?
		if ($id)
		{
			
			// do we have the right userlevel?
			if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin())
			{
				$this->ion_auth->deactivate($id);
			}
		}
		log_activity($this->session->userdata('user_id'), 'Deactivated a user #'.$id);
		// redirect them back to the auth page
		$this->session->set_flashdata('success', $this->ion_auth->messages());
		redirect(ADMIN.'users', 'refresh');
		
	}
	
	public function groups(){
		//check if the user can update an account;
			if(!has_action('read')){
				$this->session->set_flashdata('error',$this->lang->line('action_no_access'));
				redirect(ADMIN."users");
			}
		//this will be present in all methods that will need top tabs.
		$this->data['active_tab'] = 'groups';
		// set the flash data error message if there is one
		$this->data['info'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('info');

		//list the groups
		$this->data['groups'] = $this->ion_auth_model->groups()->result();
		foreach ($this->data['groups'] as $k => $group) {
			$this->data['groups'][$k]->permissions = $this->permissions->get_group_permissions($group->id)->num_rows();
			$this->data['groups'][$k]->users = $this->ion_auth_model->users($group->id)->num_rows();
		}
		$this->template->build('users/groups',$this->data);
	}
	/**
	 * Create a new group
	 */
	public function add_group()
	{
		//check if the user can update an account;
		if(!has_action('create')){
			$this->session->set_flashdata('error',$this->lang->line('action_no_access'));
			redirect(ADMIN.'users/add_group');
		}
		$this->data['active_tab'] = 'groups';

		// validate form input
		$this->form_validation->set_rules('group_name', $this->lang->line('create_group_validation_name_label'), 'trim|required|alpha_dash');

		if ($this->form_validation->run() === TRUE)
		{
			$new_group_id = $this->ion_auth->create_group($this->input->post('group_name'), $this->input->post('description'));
			if ($new_group_id)
			{
				log_activity($this->session->userdata('user_id'), 'Added a new group #'.$new_group_id);
				// check to see if we are creating the group
				// redirect them back to the admin page
				$this->session->set_flashdata('success', $this->ion_auth->messages());
				redirect(ADMIN."users/groups", 'refresh');
			}
		}
		else
		{
			// display the create group form
			// set the flash data error message if there is one
			$this->data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));

			$this->data['group_name'] = array(
				'name'  => 'group_name',
				'id'    => 'group_name',
				'type'  => 'text',
				'class' => 'form-control',
				'value' => $this->form_validation->set_value('group_name'),
			);
			$this->data['description'] = array(
				'name'  => 'description',
				'id'    => 'description',
				'type'  => 'text',
				'class' => 'form-control',
				'value' => $this->form_validation->set_value('description'),
			);

			$this->template->build('users/new_group', $this->data);
		}
	}

	/**
	 * Edit a group
	 *
	 * @param int|string $id
	 */
	public function edit_group($id)
	{
		// bail if no group id given
		if (!$id || empty($id))
		{
			redirect(ADMIN.'users/groups', 'refresh');
		}

		$this->data['active_tab'] = 'groups';

		$group = $this->ion_auth->group($id)->row();
		
		if ($this->ion_auth->is_admin()) {
			$current_perm = $this->permissions->get_group_permissions($id)->result();
			$permissions        = $this->permissions->_permissions();
			foreach ($permissions as $k=>$prem)
			{
				$disabled = "";
				if($prem->name == config_item('default_group_permission_name')){
					$disabled='disabled';
				}
				$this->data['permissions'][$k]['disabled'] = $disabled;
				$this->data['permissions'][$k]['pid'] = $prem->id;
				$this->data['permissions'][$k]['name'] = $prem->description;
				$this->data['permissions'][$k]['checked'] = null;
				foreach ($current_perm as $grp) {
					if ($this->data['permissions'][$k]['pid'] == $grp->id) {
						$this->data['permissions'][$k]['checked'] = ' checked="checked"';
						break;
					}
					
				}
			}
//			
		}

		// validate form input
		$this->form_validation->set_rules('group_name', $this->lang->line('edit_group_validation_name_label'), 'required|alpha_dash');

		if (isset($_POST) && !empty($_POST))
		{
			//check if the user can update an account;
			if(!has_action('update')){
				$this->session->set_flashdata('error',$this->lang->line('action_no_access'));
				redirect(ADMIN."users");
			}
			if ($this->form_validation->run() === TRUE)
			{	
				
				$this->db->trans_start();
				
				$this->permissions->add_to_permission($id,$this->input->post('permissions'));
				$group_update = $this->ion_auth->update_group($id, $this->input->post('group_name'), $this->input->post('group_description'));
				
				
				if ($this->db->trans_status() && $group_update) {
					
					
					log_activity($this->session->userdata('user_id'), 'Updated group #'.$group_update);
				
					$this->db->trans_commit();
					$this->session->set_flashdata('success', $this->lang->line('edit_group_saved'));
				}
				else
				{
					$this->db->trans_rollback();
					$this->session->set_flashdata('error', $this->ion_auth->errors());
				}
				redirect(ADMIN."users/edit_group/".$id);
			}
		}

		// set the flash data error message if there is one
		$this->data['error'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('info')));

		// pass the user to the view
		$this->data['group'] = $group;

		$readonly = $this->config->item('admin_group', 'ion_auth') === $group->name ? 'readonly' : '';

		$this->data['group_name'] = array(
			'name'    => 'group_name',
			'id'      => 'group_name',
			'type'    => 'text',
			'class'   => 'form-control',
			'value'   => $this->form_validation->set_value('group_name', $group->name),
			$readonly => $readonly,
		);
		$this->data['description'] = array(
			'name'  => 'group_description',
			'id'    => 'group_description',
			'class' => 'form-control',
			'type'  => 'text',
			'value' => $this->form_validation->set_value('group_description', $group->description),
		);

		$this->template->build('users/edit_group', $this->data);
	}
	
	function settings(){
		
	}
}