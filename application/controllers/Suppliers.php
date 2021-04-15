<?php
defined('BASEPATH')or exit('No direct script allowed!');
/**
* Developed by Techcoderr Developers
*/
class suppliers extends MY_AdminController
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

		$where =['people_type'=>2,'status => 1'];

		$this->load->library('pagination');
		$config['base_url'] = ('suppliers');
		$config['per_page'] = config_item('results_per_page');

		$config['total_rows'] =  $this->people_model
		->where($where)
		->get_all(1)
		->num_rows();

		$offset = (isset($get['page'])) ? config_item('results_per_page') * ($get['page'] - 1) : 0;

		$suppliers = $this->people_model->where($where)->limit(config_item('results_per_page'))->offset($offset)->order_by('created_date','DESC')->get_all(1)->result();
		//lets count all active records
		$count = $this->people_model->select("1",false)->where($where)->get_all(1)->num_rows();

		$this->pagination->initialize($config);

		$this->data['pagination'] = $this->pagination->create_links();
		$this->data['count_records'] = $count;


		if (!empty($suppliers) && is_array($suppliers)) {
			foreach ($suppliers as $supplier) {
				$supplier->supplier_name = html_tag(
				'a',
				['class'=>"text-left m-b-0",'href'=>admin_url('supplier/edit/'.$supplier->id)],
				$supplier->name
				);
				$balance = $this->people_model->select('balance')->where('people_id',$supplier->id)->get_person_metadata()->row()->balance;

				if ($balance <0) {
					$supplier->balance =html_tag('span', ['class'=>'text-danger'], my_number_format($balance,TRUE));
				} else {
					$supplier->balance = my_number_format($balance,TRUE);
				}

				$supplier->date = html_tag(
				'span',
				'',
				my_full_time_span($supplier->created_date)
				);

				$update=NULL;
				$activate=NULL;
				if (has_action('update')) {
					$update = admin_anchor(
					'suppliers/edit/'.$supplier->id,
					fa_icon('edit' , true).' '."<span class='hidden-xs hidden-sm'>Edit</span>",
					['class'=>'btn btn-sm btn-info']
					);
					if ($supplier->status == '0') {
						$activate = admin_anchor(
						'suppliers/activate/'.$supplier->id,
						fa_icon('check' , true).' '."<span class='hidden-xs hidden-sm'>Activate</span>",
						['class'=>'btn btn-sm btn-success']
						);
					}
				}
				$delete=NULL;
				if (has_action('delete') && $supplier->status == 1) {
					$delete = admin_anchor(
					'suppliers/delete/'.$supplier->id,
					fa_icon('trash-o', TRUE).' '."<span class='hidden-xs hidden-sm'>Delete</span>",
					[
						'class'=>'btn btn-sm btn-danger',
						'onclick'=>"return confirm('".lang('confirm_delete')."')"
					]
					);
				}
				$supplier->actions = $update. ' '.$delete;
			}
		}



		$new_supplier = NULL;

		if (has_action('create')) {
			$new_supplier = html_tag(
			'a',
			[
				'href'=>site_url(ADMIN.'suppliers/new'),
				'class'=>'btn btn-danger btn-sm',
			],
			fa_icon('plus',TRUE). ' '.html_tag('span',['class'=>'hidden-xs hidden-sm'],lang('btn_new_supplier'))
			);

		}


		$this->data['top_actions'] =  $new_supplier;
		$this->data['suppliers'] = $suppliers;
		$this->data['page_title'] = lang('supplier_list_title');
		$this->template->build('suppliers/index',$this->data);
	}

	function new()
	{
		if (!has_action('create'))
			show_404();

		$this->form_validation->set_rules('name',lang('supplier_name_label'),'trim|required');
		$this->form_validation->set_rules('company',lang('supplier_company_name_label'),'trim|required');
		$this->form_validation->set_rules('opening_bal',lang('supplier_opening_bal_label'),'trim');
		$this->form_validation->set_rules('billing_address',lang('supplier_billing_address_label'),'trim');
		$this->form_validation->set_rules('contact_address',lang('supplier_address_label'),'trim|xss_clean');
		$this->form_validation->set_rules('phone',lang('supplier_phone_label'),'trim|xss_clean');
		$this->form_validation->set_rules('email',lang('supplier_email_label'),'trim|xss_clean');

		if (!$this->form_validation->run()) {

			//pass the supplier data to session so as to retain it after redirect
			if (validation_errors() || $this->ion_auth->errors() || $this->session->flashdata('message')) {
				$this->session->set_flashdata($this->input->post());
				$this->session->set_flashdata('error',(validation_errors() ? validation_errors() : ($this->people_model->errors() ? $this->people_model->errors() : $this->session->flashdata('message'))));
				redirect(ADMIN."suppliers/new",'refresh');
			}
			//			print_r($this->session->flashdata());exit;
			//prepare the form
			$this->data['supplier_name']=[
				'name' => 'name',
				'type' => 'text',
				'placeholder' => lang('supplier_name_label'),
				'required'=>'required',
				'class'   => 'form-control',
				'value'=> $this->session->flashdata('name'),
			];

			$this->data['supplier_company']=[
				'name' => 'company',
				'type' => 'text',
				'placeholder' => lang('supplier_company_label'),
				'class'   => 'form-control',
				'value'=> $this->session->flashdata('company'),
			];

			$this->data['supplier_billing_address']=[
				'name' => 'billing_address',
				'type' => 'text',
				'placeholder' => lang('supplier_billing_address_label'),
				'class'   => 'form-control',
				'value'=> $this->session->flashdata('billing_address'),
			];

			$this->data['supplier_opening_bal']=[
				'name' => 'opening_balance',
				'type' => 'text',
				'placeholder' => lang('supplier_opening_bal_label'),
				'class'   => 'form-control',
				'value'=> $this->session->flashdata('opening_balance'),
			];

			$this->data['supplier_contact_address']=[
				'name' => 'address',
				'type' => 'textarea',
				'placeholder' => lang('supplier_address_label'),
				'rows' => '3',
				'class'   => 'form-control textarea',
				'value'=> $this->session->flashdata('address'),
			];


			$this->data['supplier_phone']=[
				'name' => 'phone',
				'type' => 'tel',
				'placeholder' => lang('supplier_phone'),
				'rows' => '3',
				'class'   => 'form-control textarea',
				'value'=> $this->session->flashdata('phone'),
			];


			$this->data['supplier_email']=[
				'name' => 'email',
				'type' => 'email',
				'placeholder' => lang('supplier_email'),
				'rows' => '3',
				'class'   => 'form-control textarea',
				'value'=> $this->session->flashdata('email'),
			];

			$this->data['page_title'] = lang('supplier_new_title');
			$this->template->build('suppliers/new_supplier',$this->data);
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
			//						print_r($data);exit;

			if (($supplier_id=$this->people_model->save($data,'2')) == TRUE) {
				$this->session->set_flashdata('success',lang('supplier_add_success'));
				log_activity($this->session->userdata('user_id'),'added a new supplier ##'.$supplier_id);
				if ($this->input->post('submit', TRUE) == "saveandclose") {
					redirect(ADMIN.'suppliers', 'refresh');
					exit;
				} else {
					redirect(ADMIN.'suppliers/new', 'refresh');
					exit;
				}

			} else {
				$this->session->set_flashdata('error',($this->people_model->errors()=='')?lang('supplier_add_error'):$this->people_model->errors());
				redirect(ADMIN.'suppliers/new', 'refresh');
				exit;
			}
		}
	}

	function edit($supplier_id = NULL)
	{
		if (!has_action('update'))
			show_404('welcome',TRUE);

		$supplier_id = xss_clean($supplier_id);
		$supplier = $this->people_model->where('people_type',2)->get_a_person($supplier_id)->row();

		if (!class_exists('transaction_model'))
			$this->load->model('transaction_model');
		$supplier_transactions = $this->transaction_model->transaction_per_person($supplier_id);
		$this->data['count_records'] = count($supplier_transactions);
		
		$this->form_validation->set_rules('name',lang('supplier_name_label'),'trim|required');
		$this->form_validation->set_rules('company',lang('supplier_company_name_label'),'trim|required');
		$this->form_validation->set_rules('billing_address',lang('supplier_billing_address_label'),'trim');
		$this->form_validation->set_rules('contact_address',lang('supplier_address_label'),'trim|xss_clean');
		$this->form_validation->set_rules('phone',lang('supplier_phone_label'),'trim|xss_clean');
		$this->form_validation->set_rules('email',lang('supplier_email_label'),'trim|xss_clean');

		if (!$this->form_validation->run()) {

			//pass the supplier data to session so as to retain it after redirect
			if (validation_errors() || $this->ion_auth->errors() || $this->session->flashdata('message')) {
				$this->session->set_flashdata($this->input->post());
				$this->session->set_flashdata('error',(validation_errors() ? validation_errors() : ($this->people_model->errors() ? $this->people_model->errors() : $this->session->flashdata('message'))));
				redirect(ADMIN."suppliers/edit",'refresh');
			}
			
			if ($supplier==NULL) {
				$this->session->set_flashdata('error',sprintf(lang('record_not_exist'),'supplier'));
				$this->redirect('suppliers');
			}
			$this->data['name']=humanize($supplier->name,'-');
			
			//prepare the form
			$this->data['supplier_name']=[
				'name' => 'name',
				'type' => 'text',
				'placeholder' => lang('supplier_name_label'),
				'required'=>'required',
				'class'   => 'form-control',
				'value'=> $this->form_validation->set_value($this->session->flashdata('name'),$supplier->name)
			];

			$this->data['supplier_company']=[
				'name' => 'company',
				'type' => 'text',
				'placeholder' => lang('supplier_company_label'),
				'class'   => 'form-control',
				'value'=> $this->form_validation->set_value($this->session->flashdata('company'),$supplier->company)
			];

			$this->data['supplier_billing_address']=[
				'name' => 'billing_address',
				'type' => 'text',
				'placeholder' => lang('supplier_billing_address_label'),
				'class'   => 'form-control',
				'value'=> $this->form_validation->set_value($this->session->flashdata('billing_address'),$supplier->billing_address)
			];

			$this->data['supplier_contact_address']=[
				'name' => 'address',
				'type' => 'textarea',
				'placeholder' => lang('supplier_address_label'),
				'rows' => '3',
				'class'   => 'form-control textarea',
				'value'=> $this->form_validation->set_value($this->session->flashdata('address'),$supplier->address)
			];


			$this->data['supplier_phone']=[
				'name' => 'phone',
				'type' => 'tel',
				'placeholder' => lang('supplier_phone'),
				'rows' => '3',
				'class'   => 'form-control textarea',
				'value'=> $this->form_validation->set_value($this->session->flashdata('phone'),$supplier->phone)
			];


			$this->data['supplier_email']=[
				'name' => 'email',
				'type' => 'email',
				'placeholder' => lang('supplier_email'),
				'rows' => '3',
				'class'   => 'form-control textarea',
				'value'=> $this->form_validation->set_value($this->session->flashdata('email'),$supplier->email)
			];

			$balance = $this->people_model->select('balance')->where('people_id',$supplier->id)->get_person_metadata()->row()->balance;

			if ($balance < 0) {
				$this->data['balance'] = html_tag('span', ['class'=>'text-danger'], my_number_format($balance,TRUE));
			} else {
				$this->data['balance'] = my_number_format($balance,TRUE);
			}

			if (has_action('create')) {
				$new_category = html_tag(
				'a',
				[
					'href'=>site_url(ADMIN.'suppliers/new_category'),
					'class'=>'btn btn-danger btn-sm',
				],
				fa_icon('plus',TRUE).' '.html_tag('span',['class'=>'hidden-xs hidden-sm'],lang('btn_new_category'))
				);

			}
			//uncomment this if the transaction modal has been created. this is to enable the load of supplier transactions
			if (!empty($supplier_transactions) && is_array($supplier_transactions)) {
				foreach ($supplier_transactions as $transaction) {

					$view=NULL;
					if (has_action('update')) {
						$view = admin_anchor(
						'preview/'.$transaction->method.'/'.$transaction->id,
						fa_icon('eye' , true).' '."<span class='hidden-xs hidden-sm'>View</span>",
						['class'=>'btn btn-sm btn-info']
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

			$this->data['supplier_transactions'] = $supplier_transactions;
			$this->data['page_title'] = lang('supplier_edit_title');
			$this->template->build('suppliers/edit_supplier',$this->data);
		} else {

			$data = $this->input->post(array(
			'name',
			'company',
			'billing_address',
			'address',
			'phone',
			'email',
			), true);

			if (($category_update=$this->people_model->update($supplier->id,$data,'2')) == TRUE) {
				$this->session->set_flashdata('success',$this->people_model->messages());
				log_activity($this->session->userdata('user_id'),'updated a supplier ##'.$supplier->id);
				if ($this->input->post('submit', TRUE) == "saveandclose") {
					redirect(ADMIN.'suppliers', 'refresh');
					exit;
				} else {
					redirect(current_url(), 'refresh');
					exit;
				}

			} else {
				$this->session->set_flashdata('error',($this->people_model->errors()=='')?lang('supplier_category_update_error'):$this->people_model->errors());
				redirect(ADMIN.'suppliers/new', 'refresh');
				exit;
			}
		}
	}

	function delete($person = NULL)
	{

		$person = my_number_format(cleanString($person,true,''));
		if ($this->people_model->delete($person) == TRUE) {
			$this->session->set_flashdata('success',($this->people_model->messages()));
			$this->redirect(previous_url('dashboard',true));
		}
		$this->session->set_flashdata('error',($this->people_model->errors()));
		$this->redirect(previous_url('dashboard',true));
	}

	function activate($person = NULL)
	{
		$person = my_number_format(cleanString($person,true,''));
		if ($this->people_model->update($person, ['id'=>$person], $this->people_model->person_type($person)) == TRUE) {
			$this->session->set_flashdata('success',($this->people_model->messages()));
			$this->redirect(previous_url('dashboard',true));
		}
		$this->session->set_flashdata('error',($this->people_model->errors()));
		$this->redirect(previous_url('dashboard',true));
	}
}

//end of line.
