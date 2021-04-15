<?php
defined('BASEPATH')or exit('No direct script allowed!');
/**
* Developed by Techcoderr Developers
*/
class Customers extends MY_AdminController
{
	function __construct()
	{
		parent::__construct();
		$this->load->model('people_model');
		$this->data['top_actions'] = '';
	}
	
	function index()
	{
		parse_str($_SERVER['QUERY_STRING'], $get);
		
		$where =['people_type'=>1,'status => 1'];

		$this->load->library('pagination');
		$config['base_url'] = ('customers');
		$config['per_page'] = config_item('results_per_page');

		$config['total_rows'] =  $this->people_model
		->where($where)
		->get_all(1)
		->num_rows();

		$offset = (isset($get['page'])) ? config_item('results_per_page') * ($get['page'] - 1) : 0;

		$customers = $this->people_model->where($where)->limit(config_item('results_per_page'))->offset($offset)->order_by('created_date','DESC')->get_all(1)->result();
		//lets count all active records
		$count = $this->people_model->select("1",false)->where($where)->get_all(1)->num_rows();

		$this->pagination->initialize($config);

		$this->data['pagination'] = $this->pagination->create_links();
		$this->data['count_records'] = $count;


		if (!empty($customers) && is_array($customers)) {
			foreach ($customers as $customer) {
				$customer->customer_name = html_tag(
				'a',
				['class'=>"text-left m-b-0",'href'=>admin_url('customer/edit/'.$customer->id)],
				$customer->name
				);
				$balance = $this->people_model->select('balance')->where('people_id',$customer->id)->get_person_metadata()->row()->balance;
				
				if($balance <0){
					$customer->balance =html_tag('span', ['class'=>'text-danger'], my_number_format($balance,TRUE));
				}else{
					$customer->balance = my_number_format($balance,TRUE);
				}
					
				$customer->date = html_tag(
				'span',
				'',
				my_full_time_span($customer->created_date)
				);

				$update=NULL;
				$activate=NULL;
				if (has_action('update')) {
					$update = admin_anchor(
					'customers/edit/'.$customer->id,
					fa_icon('edit' , true).' '."<span class='hidden-xs hidden-sm'>Edit</span>",
					['class'=>'btn btn-sm btn-info']
					);
					if ($customer->status == '0') {
						$activate = admin_anchor(
						'customers/activate/'.$customer->id,
						fa_icon('check' , true).' '."<span class='hidden-xs hidden-sm'>Activate</span>",
						['class'=>'btn btn-sm btn-success']
						);
					}
				}
				$delete=NULL;
				if (has_action('delete') && $customer->status == 1) {
					$delete = admin_anchor(
					'customers/delete/'.$customer->id,
					fa_icon('trash-o', TRUE).' '."<span class='hidden-xs hidden-sm'>Delete</span>",
					[
						'class'=>'btn btn-sm btn-danger',
						'onclick'=>"return confirm('".lang('confirm_delete')."')"
					]
					);
				}
				$customer->actions = $update. ' '.$activate.' '.$delete;
			}
		}


		
		$new_customer = NULL;

		if (has_action('create')) {
			$new_customer = html_tag(
			'a',
			[
				'href'=>site_url(ADMIN.'customers/new'),
				'class'=>'btn btn-danger btn-sm',
			],
			fa_icon('plus',TRUE). ' '.html_tag('span',['class'=>'hidden-xs hidden-sm'],lang('btn_new_customer'))
			);

		}


		$this->data['top_actions'] =  $new_customer;
		$this->data['customers'] = $customers;
		$this->data['page_title'] = lang('customer_list_title');
		$this->template->build('customers/index',$this->data);
	}
	
	function new(){
		if (!has_action('create'))
			show_404();

		$this->form_validation->set_rules('name',lang('customer_name_label'),'trim|required');
		$this->form_validation->set_rules('company',lang('customer_company_name_label'),'trim|required');
		$this->form_validation->set_rules('opening_bal',lang('customer_opening_bal_label'),'trim');
		$this->form_validation->set_rules('billing_address',lang('customer_billing_address_label'),'trim');
		$this->form_validation->set_rules('contact_address',lang('customer_address_label'),'trim|xss_clean');
		$this->form_validation->set_rules('phone',lang('customer_phone_label'),'trim|xss_clean');
		$this->form_validation->set_rules('email',lang('customer_email_label'),'trim|xss_clean');

		if (!$this->form_validation->run()) {

			//pass the customer data to session so as to retain it after redirect
			if (validation_errors() || $this->ion_auth->errors() || $this->session->flashdata('message')) {
				$this->session->set_flashdata($this->input->post());
				$this->session->set_flashdata('error',(validation_errors() ? validation_errors() : ($this->people_model->errors() ? $this->people_model->errors() : $this->session->flashdata('message'))));
				redirect(ADMIN."customers/new",'refresh');
			}
			//			print_r($this->session->flashdata());exit;
			//prepare the form
			$this->data['customer_name']=[
				'name' => 'name',
				'type' => 'text',
				'placeholder' => lang('customer_name_label'),
				'required'=>'required',
				'autocomplete' => 'off',
				'class'   => 'form-control',
				'value'=> $this->session->flashdata('name'),
			];
			
			$this->data['customer_company']=[
				'name' => 'company',
				'type' => 'text',
				'autocomplete' => 'off',
				'placeholder' => lang('customer_company_label'),
				'class'   => 'form-control',
				'value'=> $this->session->flashdata('company'),
			];

			$this->data['customer_billing_address']=[
				'name' => 'billing_address',
				'type' => 'text',
				'autocomplete' => 'off',
				'placeholder' => lang('customer_billing_address_label'),
				'class'   => 'form-control',
				'value'=> $this->session->flashdata('billing_address'),
			];

			$this->data['customer_opening_bal']=[
				'name' => 'opening_balance',
				'type' => 'text',
				'autocomplete' => 'off',
				'placeholder' => lang('customer_opening_bal_label'),
				'class'   => 'form-control digitsminusonly',
				'value'=> $this->session->flashdata('opening_balance'),
			];

			$this->data['customer_contact_address']=[
			'name' => 'address',
				'type' => 'textarea',
				'placeholder' => lang('customer_address_label'),
				'rows' => '3',
				'class'   => 'form-control textarea',
				'value'=> $this->session->flashdata('address'),
			];
			

			$this->data['customer_phone']=[
			'name' => 'phone',
			'type' => 'tel',
			'autocomplete' => 'off',
				'placeholder' => lang('customer_phone'),
				'rows' => '3',
				'class'   => 'form-control textarea',
				'value'=> $this->session->flashdata('phone'),
			];
			

			$this->data['customer_email']=[
			'name' => 'email',
			'type' => 'email',
			'autocomplete' => 'off',
				'placeholder' => lang('customer_email'),
				'rows' => '3',
				'class'   => 'form-control textarea',
				'value'=> $this->session->flashdata('email'),
			];
			
			$this->data['page_title'] = lang('customer_new_title');
			$this->template->build('customers/new_customer',$this->data);
		} else {

			$data = $this->input->post(array(
			'name',
			'company',
			'opening_balance',
			'billing_address',
			'address',
			'phone',
			'email',
			), true);

			$data['opening_balance'] = ((strlen($data['opening_balance']) < 1)?0:my_number_format($data['opening_balance'],false));

			if (($customer_id=$this->people_model->save($data,'1')) == TRUE) {
				$this->session->set_flashdata('success',lang('customer_add_success'));
				log_activity($this->session->userdata('user_id'),'added a new customer ##'.$customer_id);
				if ($this->input->post('submit', TRUE) == "saveandclose") {
					redirect(ADMIN.'customers', 'refresh');
					exit;
				} else {
					redirect(ADMIN.'customers/new', 'refresh');
					exit;
				}

			} else {
				$this->session->set_flashdata('error',($this->people_model->errors()=='')?lang('customer_add_error'):$this->people_model->errors());
				redirect(ADMIN.'customers/new', 'refresh');
				exit;
			}
		}

	}
	
	function edit($customer_id = NULL)
	{
		if (!has_action('update'))
			show_404('welcome',TRUE);

		$customer_id = xss_clean($customer_id);
		$customer = $this->people_model->where('people_type',1)->get_a_person($customer_id)->row();
		
		if (!class_exists('transaction_model'))
			$this->load->model('transaction_model');
		$customer_transactions = $this->transaction_model->transaction_per_person($customer_id);
		$this->data['count_records'] = count($customer_transactions);

		$this->form_validation->set_rules('name',lang('customer_name_label'),'trim|required');
		$this->form_validation->set_rules('company',lang('customer_company_name_label'),'trim|required');
		$this->form_validation->set_rules('billing_address',lang('customer_billing_address_label'),'trim');
		$this->form_validation->set_rules('contact_address',lang('customer_address_label'),'trim|xss_clean');
		$this->form_validation->set_rules('phone',lang('customer_phone_label'),'trim|xss_clean');
		$this->form_validation->set_rules('email',lang('customer_email_label'),'trim|xss_clean');

		if (!$this->form_validation->run()) {

			//pass the customer data to session so as to retain it after redirect
			if (validation_errors() || $this->ion_auth->errors() || $this->session->flashdata('message')) {
				$this->session->set_flashdata($this->input->post());
				$this->session->set_flashdata('error',(validation_errors() ? validation_errors() : ($this->people_model->errors() ? $this->people_model->errors() : $this->session->flashdata('message'))));
				redirect(ADMIN."customers/edit",'refresh');
			}

			if ($customer==NULL) {
				$this->session->set_flashdata('error',sprintf(lang('record_not_exist'),'customer'));
				redirect('customers');
			}
			
			$this->data['name'] = humanize($customer->name,'-');
			//prepare the form
			$this->data['customer_name']=[
				'name' => 'name',
				'type' => 'text',
				'placeholder' => lang('customer_name_label'),
				'required'=>'required',
				'autocomplete' => 'off',
				'class'   => 'form-control',
				'value'=> $this->form_validation->set_value($this->session->flashdata('name'),$customer->name)
			];

			$this->data['customer_company']=[
				'name' => 'company',
				'type' => 'text',
				'autocomplete' => 'off',
				'placeholder' => lang('customer_company_label'),
				'class'   => 'form-control',
				'value'=> $this->form_validation->set_value($this->session->flashdata('company'),$customer->company)
			];

			$this->data['customer_billing_address']=[
				'name' => 'billing_address',
				'type' => 'text',
				'autocomplete' => 'off',
				'placeholder' => lang('customer_billing_address_label'),
				'class'   => 'form-control',
				'value'=> $this->form_validation->set_value($this->session->flashdata('billing_address'),$customer->billing_address)
			];

			$this->data['customer_contact_address']=[
				'name' => 'address',
				'type' => 'textarea',
				'autocomplete' => 'off',
				'placeholder' => lang('customer_address_label'),
				'rows' => '3',
				'class'   => 'form-control textarea',
				'value'=> $this->form_validation->set_value($this->session->flashdata('address'),$customer->address)
			];


			$this->data['customer_phone']=[
				'name' => 'phone',
				'type' => 'tel',
				'autocomplete' => 'off',
				'placeholder' => lang('customer_phone'),
				'rows' => '3',
				'class'   => 'form-control textarea',
				'value'=> $this->form_validation->set_value($this->session->flashdata('phone'),$customer->phone)
			];


			$this->data['customer_email']=[
				'name' => 'email',
				'type' => 'email',
				'autocomplete' => 'off',
				'placeholder' => lang('customer_email'),
				'rows' => '3',
				'class'   => 'form-control textarea',
				'value'=> $this->form_validation->set_value($this->session->flashdata('email'),$customer->email)
			];
			
			$balance = $this->people_model->select('balance')->where('people_id',$customer->id)->get_person_metadata()->row()->balance;

			if ($balance < 0) {
				$this->data['balance'] = html_tag('span', ['class'=>'text-danger'], my_number_format($balance,TRUE));
			} else {
				$this->data['balance'] = my_number_format($balance,TRUE);
			}

			if (has_action('create')) {
				$new_category = html_tag(
				'a',
				[
					'href'=>site_url(ADMIN.'customers/new_category'),
					'class'=>'btn btn-danger',
				],
				fa_icon('plus',TRUE).' '.html_tag('span',['class'=>'hidden-xs hidden-sm'],lang('btn_new_category'))
				);

			}
			//uncomment this if the transaction modal has been created. this is to enabble the load of customer transactions
			if (!empty($customer_transactions) && is_array($customer_transactions)) {
				foreach ($customer_transactions as $transaction) {
					
					$view=NULL;
					if (has_action('update')) {
						$view = admin_anchor(
						'preview/'.$transaction->method.'/'.$transaction->id,
						fa_icon('eye' , true).' '."<span class='hidden-xs hidden-sm'>View</span>",
						['class'=>'btn btn-xs btn-info']
						);
					}
					$transaction->actions = $view;
//					$transaction->transaction_type = config_item('transaction_key')[$transaction->transaction_type];
					$transaction->date = html_tag(
					'span',
					'',
					my_full_time_span($transaction->created_date)
					);

					$transaction->amount = html_tag(
					'span',
					'',
					my_number_format($transaction->amount,1)
					);

				}
			}
			
			$this->data['customer_transactions'] = $customer_transactions;
			$this->data['page_title'] = lang('customer_edit_title');
			$this->template->build('customers/edit_customer',$this->data);
		} else {

			$data = $this->input->post(array(
			'name',
			'company',
			'billing_address',
			'address',
			'phone',
			'email',
			), true);

			if (($category_update=$this->people_model->update($customer->id,$data,'1')) == TRUE) {
				$this->session->set_flashdata('success',lang('people_update_successful'));
				log_activity($this->session->userdata('user_id'),'updated a category ##'.$customer->id);
				if ($this->input->post('submit', TRUE) == "saveandclose") {
					redirect(ADMIN.'customers', 'refresh');
					exit;
				} else {
					redirect(current_url(), 'refresh');
					exit;
				}

			} else {
				$this->session->set_flashdata('error',($this->people_model->errors()=='')?lang('people_update_unsuccessful'):$this->people_model->errors());
				redirect(current_url(), 'refresh');
				exit;
			}
		}
	}
	
	function delete($person = NULL)
	{

		$person = my_number_format(cleanString($person,true,''));
		if ($this->people_model->delete($person) == TRUE) {
			$this->session->set_flashdata('success',($this->people_model->messages()));
			redirect(ADMIN.'customers', 'refresh');
		}
		$this->session->set_flashdata('error',($this->people_model->errors()));
		redirect(ADMIN.'customers', 'refresh');
	}
	
	function activate($person = NULL)
	{
		$person = my_number_format(cleanString($person,true,''));
		
		if ($this->people_model->people_status($person, 'activation') == TRUE) {
//		if ($this->people_model->activate($person) == TRUE) {
			$this->session->set_flashdata('success',($this->people_model->messages()));
			redirect(ADMIN.'customers', 'refresh');
		}
		$this->session->set_flashdata('error',($this->people_model->errors()));
		redirect(ADMIN.'customers', 'refresh');
	}
}

//end of line.
