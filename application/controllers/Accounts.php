<?php
defined('BASEPATH') or exit('No direct access to script is allowed');

class Accounts extends MY_AdminController
{
	function __construct()
	{
		parent::__construct();
		$this->load->model('accounts_model');
		$this->load->model('people_model');
	}

	public function index()
	{
		parse_str($_SERVER['QUERY_STRING'], $get);

		$this->load->library('pagination');
		$config['base_url'] = ('accounts');
		$config['per_page'] = config_item('results_per_page');

		//lets count all active records
		$this->data['count_records'] = $config['total_rows'] =  $this->accounts_model->total_accounts();

		$offset = (isset($get['page'])) ? config_item('results_per_page') * ($get['page'] - 1) : 0;

		$accounts = $this->accounts_model->all_accounts_transaction(config_item('results_per_page') , $offset);

		$this->pagination->initialize($config);

		$this->data['pagination'] = $this->pagination->create_links();
		
		$account = ksort($accounts);
		
		if (!empty($accounts) && is_array($accounts)) {
		
			$n ='';	
			foreach ($accounts as $account) {

				$account->name = html_tag(
				'a',
				['class'=>"text-left m-b-0",'href'=>admin_url('accounts/edit/'.$account->id)],
				humanize(ucfirst($account->name),'-')
				);
				
				$account->balance = html_tag(
				'span',
				'',
				is_null($account->balance) ? NULL : (($account->balance > 0) ? my_number_format($account->balance,TRUE) : html_tag('span',['class'=>'text-danger'],my_number_format($account->balance,TRUE)))
				);

				$account->account_type = html_tag(
				'span',
				'',
				ucfirst($account->account_type)
				);
				
				$account->sn = html_tag(
				'span',
				'',
				++$n
				);

				$update=NULL;
				if (has_action('update')) {
					$update = admin_anchor(
					'accounts/edit/'.$account->id,
					fa_icon('edit' , true).' '."<span class='hidden-xs hidden-sm'>Edit</span>",
					['class'=>'btn btn-sm btn-info']
					);
				}
				$delete=NULL;
				$_delete = admin_anchor(
				'accounts/delete/'.$account->id,
				fa_icon('trash-o', TRUE).' '."<span class='hidden-xs hidden-sm'>Delete</span>",
				[
					'class'=>'btn btn-sm btn-danger',
					'onclick'=>"return confirm('".lang('confirm_delete')."')"
				]
				);
				$_activate = admin_anchor(
				'accounts/activate/'.$account->id,
				fa_icon('check', TRUE).' '."<span class='hidden-xs hidden-sm'>activate</span>",
				[
					'class'=>'btn btn-sm btn-default',
//					'onclick'=>"return  confirm('".lang('confirm_delete')."')"
				]
				);
				$_deactivate = admin_anchor(
				'accounts/deactivate/'.$account->id,
				fa_icon('ban', TRUE).' '."<span class='hidden-xs hidden-sm'>Hide</span>",
				[
					'class'=>'btn btn-sm btn-success',
//					'onclick'=>"return confirm('".lang('confirm_delete')."')"
				]
				);
				
				if($account->status == 1){
					if ($this->accounts_model->account_has_transaction($account->id) || !in_array($account->_account_type,[1,2])) {
						$delete = $_deactivate;
					}else{
						$delete = $_delete;
					}
				}else{
					$delete = $_activate;
				}
					
				$preview=NULL;
				$preview = admin_anchor(
				'accounts/view/'.$account->id,
				fa_icon('eye', TRUE).' '."<span class='hidden-xs hidden-sm'>View</span>",
				[
					'class'=>'btn btn-sm btn-warning',
				]
				);

				$account->actions = $update. ' '.$preview.' '.$delete;
				
				if (!in_array($account->_account_type,[1,2])) {
					$account->actions = '';
				}
			}
		}

		$receive_account = NULL;
		$make_account = NULL;

		if (has_action('create')) {
			$make_account = html_tag(
			'a',
			[
				'href'=>site_url(ADMIN.'accounts/make_account'),
				'class'=>'btn btn-danger btn-sm',
			],
			fa_icon('reply',TRUE). ' '.html_tag('span',['class'=>'hidden-xs hidden-sm'],lang('btn_make_account'))
			);

			$receive_account = html_tag(
			'a',
			[
				'href'=>site_url(ADMIN.'accounts/recevie_account'),
				'class'=>'btn btn-danger btn-sm',
			],
			fa_icon('forward',TRUE). ' '.html_tag('span',['class'=>'hidden-xs hidden-sm'],lang('btn_receive_account'))
			);
		}
		
		$this->data['accounts'] = $accounts;
		$this->data['page_title'] = lang('account_list_title');
		$this->template->build('accounts/index',$this->data);
	}
	
	public function new(){
		if (!has_action('create'))
			show_404();

		$this->form_validation->set_rules('name',lang('account_name_label'),'trim|required');
		$this->form_validation->set_rules('description',lang('account_description_label'),'trim');
		$this->form_validation->set_rules('account_type',lang('account_type_label'),'trim');
		$this->form_validation->set_rules('opening_balance',lang('customer_opening_bal_label'),'trim');
		
		if (!$this->form_validation->run()) {

			//pass the product data to session so as to retain it after redirect
			if (validation_errors() || $this->ion_auth->errors() || $this->session->flashdata('message')) {
				$this->session->set_flashdata($this->input->post());
				$this->session->set_flashdata('error',(validation_errors() ? validation_errors() : ($this->accounts_model->errors() ? $this->accounts_model->errors() : $this->session->flashdata('message'))));
				redirect(ADMIN."products/new",'refresh');
			}
			

			$accounts = $this->db->select('id,name,description')->where(['id'=>1])->or_where(['id'=>2])->order_by('name','DESC')->get(config_item('tables')['account_types'])->result();

			foreach ($accounts as $account) {
				$option[$account->id] = $account->name." [".$account->description."]";
			}
			$option =[''=>lang('account_choose_type')]+$option;
			$this->data['account_type']=[
				'name' => 'account_type',
				'required'=>'required',
				'class'   => 'form-control',
				'id'   => 'account_type',
				'data-container'=>"body",
				'options' => $option,
				'selected'=> $this->session->flashdata('account_type'),
			];
			
			$this->data['description']=[
				'name' => 'description',
				'type' => 'text',
				'placeholder' => lang('account_description_label'),
				'class'   => 'form-control',
				'autocomplete' => 'off',
				'value'=> $this->session->flashdata('description'),
			];
			
			$this->data['name']=[
				'name' => 'name',
				'type' => 'text',
				'placeholder' => lang('account_name_label'),
				'required'=>'required',
				'autocomplete' => 'off',
				'class'   => 'form-control',
				'value'=> $this->session->flashdata('name'),
			];
			$this->data['opening_balance_label']=lang('customer_opening_bal_label');
			$this->data['opening_balance']=[
				'name' => 'opening_balance',
				'type' => 'text',
				'autocomplete' => 'off',
				'placeholder' => lang('customer_opening_bal_label'),
				'class'   => 'form-control digitsonly',
				'value'=> $this->session->flashdata('opening_balance'),
			];
			
			$this->data['page_title'] = lang('account_new_title');
			$this->data['account_form'] = $this->template->partial('account_form',$this->data);
			$this->template->build('accounts/new_account',$this->data);
		}else{
			$data = $this->input->post(array(
			'name',
			'account_type',
			'opening_balance',
			'description',
			), true);
			$data['opening_balance'] = floatval($this->input->post('opening_balance'));
//			print_r($this->input->post());exit;
			$account = $this->accounts_model->save($data,);
			
			if ($account) {
				$this->session->set_flashdata('success',$this->accounts_model->messages());
				log_activity($this->session->userdata('user_id'),'created a new account ##'.$account);
				if ($this->input->post('submit', TRUE) == "saveandclose") {
					redirect(ADMIN.'accounts', 'refresh');
					exit;
				} else {
					redirect(current_url(), 'refresh');
					exit;
				}
			} else {
				$this->session->set_flashdata('error',($this->accounts_model->errors()=='')?lang('product_category_update_error'):$this->accounts_model->errors());
				redirect(ADMIN.'accounts/new', 'refresh');
				exit;
			}
		}
		
	}
	
	public function edit($id = NULL){
		if (!has_action('create'))
			show_404();
		if ($id == NULL) {
			$this->session->set_flashdata('error',sprintf(lang('record_not_exist'),lang('account')));
			redirect(previous_url('accounts',1));
		}
		$account_query = $this->accounts_model->where(['id'=>$id])->account();
		if ($account_query->num_rows()>0) {
			$account_details = $account_query->row();
		}else {
			$this->session->set_flashdata('error',sprintf(lang('record_not_exist'),lang('account')));
			redirect(previous_url('accounts',1));
		}
		
		$this->form_validation->set_rules('name',lang('account_name_label'),'trim|required');
		$this->form_validation->set_rules('description',lang('account_description_label'),'trim');
		$this->form_validation->set_rules('account_type',lang('account_type_label'),'trim');
		$this->form_validation->set_rules('adjust_balance',lang('customer_opening_bal_label'),'trim');
		
		if (!$this->form_validation->run()) {

			//pass the product data to session so as to retain it after redirect
			if (validation_errors() || $this->ion_auth->errors() || $this->session->flashdata('message')) {
				$this->session->set_flashdata($this->input->post());
				$this->session->set_flashdata('error',(validation_errors() ? validation_errors() : ($this->accounts_model->errors() ? $this->accounts_model->errors() : $this->session->flashdata('message'))));
				redirect(ADMIN."products/new",'refresh');
			}

			$this->data['account_type_edit']=[
				'name' => 'account_type',
				'required'=>'required',
				'class'   => 'form-control',
				'id'   => 'account_type',
				'readonly'   => 'readonly',
				'value'	=>$this->accounts_model->get_account_type_name_by_id($account_details->account_type),
			];

			$this->data['description']=[
				'name' => 'description',
				'type' => 'text',
				'placeholder' => lang('account_description_label'),
				'class'   => 'form-control',
				'autocomplete' => 'off',
				'value'=> $this->form_validation->set_value($this->session->flashdata('description'),$account_details->description),
			];

			$this->data['name']=[
				'name' => 'name',
				'type' => 'text',
				'placeholder' => lang('account_name_label'),
				'required'=>'required',
				'autocomplete' => 'off',
				'class'   => 'form-control',
				'value'=> $this->form_validation->set_value($this->session->flashdata('name'),humanize($account_details->name,'-')),
			];
			$this->data['opening_balance_label']=lang('customer_adjust_bal_label');
			$this->data['opening_balance']=[
				'name' => 'adjust_balance',
				'type' => 'text',
				'autocomplete' => 'off',
				'placeholder' => lang('customer_adjust_bal_label'),
				'class'   => 'form-control digitsonly',
				'value'=> $this->session->flashdata('adjust_balance'),
			];

			$this->data['page_title'] = lang('account_new_title');
			$this->data['account_form'] = $this->template->partial('account_form',$this->data);
			$this->template->build('accounts/edit_account',$this->data);
		} else {
			$data = $this->input->post(array(
			'name',
			'account_type',
			'description',
			), true);
			$data['account_type'] = $this->accounts_model->get_account_type_id_by_name($data['account_type']);
			$data['opening_balance'] = floatval($this->input->post('adjust_balance'));
			$data['status'] = 1;
//			print_r($this->input->post());exit;;
			$account = $this->accounts_model->update($id,$data,$account_details);

			if ($account) {
				$this->session->set_flashdata('success',$this->accounts_model->messages());
				log_activity($this->session->userdata('user_id'),'Updated account ##'.$account);
				if ($this->input->post('submit', TRUE) == "saveandclose") {
					redirect(ADMIN.'accounts', 'refresh');
					exit;
				} else {
					redirect(current_url(), 'refresh');
					exit;
				}
			} else {
				$this->session->set_flashdata('error',($this->accounts_model->errors()=='')?lang('product_category_update_error'):$this->accounts_model->errors());
				redirect(current_url(), 'refresh');
				exit;
			}
		}
	}
	
	public function view($id=NULL){
		
		if ($id == NULL) {
			$this->session->set_flashdata('error',sprintf(lang('record_not_exist'),lang('account')));
			redirect(previous_url('accounts',1));
		}
		
		$account_type_id = $this->accounts_model->get_account_type_by_id($id);
		$account_view_name = $this->accounts_model->get_account_type_name_by_id($account_type_id);
		$account_amount = 0;
		$account_details = $this->accounts_model->get_account_transaction($account_view_name,$id,$account_type_id);
		
		
		$this->data['account_details'] = $account_details;
		$this->data['amount'] = $account_amount;
		$this->data['page_title'] = sprintf(lang('account_bank_view_title'),lang($account_view_name));
		$this->data['view'] = $this->template->partial('view_types/'.($account_view_name),$this->data);
		$this->template->build('accounts/view_account',$this->data);	
	}
	public function delete($id = NULL){
		if ($id == NULL) {
			$this->session->set_flashdata('error',sprintf(lang('record_not_exist'),lang('account')));
			redirect(previous_url('accounts',1));
		}
		
		if ($this->accounts_model->delete($id)) {
			$this->session->set_flashdata('success',$this->accounts_model->messages());
			redirect(ADMIN.'accounts');
		}
		$this->session->set_flashdata('error',$this->accounts_model->errors());
		redirect(ADMIN.'accounts');
	}
	public function activate($id = NULL){
		if ($id == NULL) {
			$this->session->set_flashdata('error',sprintf(lang('record_not_exist'),lang('account')));
			redirect(previous_url('accounts',1));
		}
		
		if ($this->accounts_model->account_status($id)) {
			$this->session->set_flashdata('success',$this->accounts_model->messages());
			redirect(ADMIN.'accounts');
		}
		$this->session->set_flashdata('error',$this->accounts_model->errors());
		redirect(ADMIN.'accounts');
	}
	public function deactivate($id = NULL){
		if ($id == NULL) {
			$this->session->set_flashdata('error',sprintf(lang('record_not_exist'),lang('account')));
			redirect(previous_url('accounts',1));
		}
		
		if ($this->accounts_model->account_status($id , 'deactivation')) {
			$this->session->set_flashdata('success',$this->accounts_model->messages());
			redirect(ADMIN.'accounts');
		}
		$this->session->set_flashdata('error',$this->accounts_model->errors());
		redirect(ADMIN.'accounts');
	}
}