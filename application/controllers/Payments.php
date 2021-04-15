<?php
defined('BASEPATH')or exit('No direct script allowed!');
/**
* Developed by Techcoderr Developers
*/
class Payments extends MY_AdminController
{
	function __construct()
	{
		parent::__construct();
		$this->load->model('people_model');
		$this->load->model('accounts_model');
		add_script('assets/plugins/jquery-ui-1.12.1/jquery-ui.min');
		add_script('assets/js/NumberFormat');
		add_script('assets/js/ajax_queue');
		add_style('assets/plugins/jquery-ui-1.12.1/jquery-ui.min');
	}

	function index()
	{
		show_404();
		parse_str($_SERVER['QUERY_STRING'], $get);

		$this->load->library('pagination');
		$config['base_url'] = ('payments');
		$config['per_page'] = config_item('results_per_page');

		//lets count all active records
		$total_rows = $config['total_rows'] =  $this->people_model->total_payments();
		
		$offset = (isset($get['page'])) ? config_item('results_per_page') * ($get['page'] - 1) : 0;

		$payments = $this->people_model->all_payments_transaction(config_item('results_per_page') , $offset);
		//		print_r($payments);exit;

		$this->pagination->initialize($config);

		$this->data['pagination'] = $this->pagination->create_links();
		$this->data['count_records'] = $total_rows;
		

		if (!empty($payments) && is_array($payments)) {
			foreach ($payments as $payment) {
				$payment->account = html_tag(
				'a',
				['class'=>"text-left m-b-0",'href'=>admin_url('payments/edit/'.$payment->id)],
				$payment->account
				);
				$customer = $this->people_model->get_a_person($payment->people_id)->row()->name;

				if ($customer !== NULL) {
					$payment->customer =html_tag('span', ['class'=>'text-danger'], ($customer));
				} else {
					$payment->customer = 'UNKNOWN';
				}

				$payment->amount = html_tag(
				'span',
				'',
				my_number_format($payment->amount,TRUE)
				);

				$payment->date = html_tag(
				'span',
				'',
				my_full_time_span($payment->t_date)
				);

				$update=NULL;
				if (has_action('update')) {
					$update = admin_anchor(
					'payments/edit/'.$payment->id,
					fa_icon('edit' , true).' '."<span class='hidden-xs hidden-sm'>Edit</span>",
					['class'=>'btn btn-sm btn-info']
					);
				}
				$delete=NULL;
				$delete = admin_anchor(
				'payments/delete/'.$payment->id,
				fa_icon('trash-o', TRUE).' '."<span class='hidden-xs hidden-sm'>Delete</span>",
				[
					'class'=>'btn btn-sm btn-danger',
					'onclick'=>"return confirm('".lang('confirm_delete')."')"
				]
				);

				$preview=NULL;

				$preview = admin_anchor(
				'preview/payment/'.$payment->id,
				fa_icon('eye', TRUE).' '."<span class='hidden-xs hidden-sm'>Preview</span>",
				[
					'class'=>'btn btn-sm btn-warning',
				]
				);

				$payment->actions = $update. ' '.$preview.' '.$delete;
			}
		}

		$receive_payment = NULL;
		$make_payment = NULL;

		if (has_action('create')) {
			$make_payment = html_tag(
			'a',
			[
				'href'=>site_url(ADMIN.'payments/make_payment'),
				'class'=>'btn btn-danger btn-sm',
			],
			fa_icon('reply',TRUE). ' '.html_tag('span',['class'=>'hidden-xs hidden-sm'],lang('btn_make_payment'))
			);

			$receive_payment = html_tag(
			'a',
			[
				'href'=>site_url(ADMIN.'payments/recevie_payment'),
				'class'=>'btn btn-danger btn-sm',
			],
			fa_icon('forward',TRUE). ' '.html_tag('span',['class'=>'hidden-xs hidden-sm'],lang('btn_receive_payment'))
			);
		}

		$this->data['top_actions'] =$make_payment.' '.$receive_payment;
		$this->data['payments'] = $payments;
		$this->data['page_title'] = lang('payment_list_title');
		$this->template->build('payments/index',$this->data);
	}

	function receive_payment()
	{
		if (!has_action('create'))
			show_404();

		$this->form_validation->set_rules('name',lang('name'),'trim|required');
		$this->form_validation->set_rules('amount',lang('sales_amount_label'),'trim|required');
		$this->form_validation->set_rules('account',lang('payment_account_label'),'trim|required');
		$this->form_validation->set_rules('t_date',lang('date'),'trim');
		$this->form_validation->set_rules('description',lang('payment_description_label'),'trim');

		if (!$this->form_validation->run()) {

			//pass the product data to session so as to retain it after redirect
			if (validation_errors() || $this->ion_auth->errors() || $this->session->flashdata('message')) {
				$this->session->set_flashdata($this->input->post());
				$this->session->set_flashdata('error',(validation_errors() ? validation_errors() : ($this->accounts_model->errors() ? $this->accounts_model->errors() : $this->session->flashdata('message'))));
				redirect(ADMIN."payments/receive_payment",'refresh');
			}

			$this->data['name']=[
				'name' => 'name',
				'type' => 'text',
				'placeholder' => lang('name'),
				'required'=>'required',
				'class'   => 'form-control',
				"data-people" => "0",
				"id" => "peoplepayment",
				'value'=> $this->session->flashdata('name'),
			];

			$banks = $this->accounts_model->select('id,name')->where(['account_type'=>'1','status'=>1])->get_account()->result();
			foreach ($banks as $bank) {
				$option[$bank->id] = $bank->name;
			}
			$option = [''=>lang('account_choose_account')]+$option;
			$this->data['account']=[
				'name' => 'account',
				'type' => 'text',
				'placeholder' => lang('payment_account_label'),
				'required'=>'required',
				'class'   => 'form-control',
				'options' => $option,
				'selected'=> ($this->session->flashdata('account')),
			];
			$this->data['amount']=[
				'name' => 'amount',
				'type' => 'text',
				'placeholder' => lang('sales_amount_label'),
				'required'=>'required',
				'class'   => 'form-control',
				'value'=> $this->session->flashdata('amount'),
			];
			$this->data['date']=[
				'name' => 't_date',
				'type' => 'text',
				'placeholder' => lang('date'),
				'id'   => 'datepicker',
				'class'   => 'form-control',
				'value'=> $this->session->flashdata('t_date'),
			];
			$this->data['description']=[
				'name' => 'description',
				'type' => 'text',
				'placeholder' => lang('payment_description_label'),
				'class'   => 'form-control',
				'rowspan'   => '3',
				'value'=> $this->session->flashdata('description'),
			];
			
			$this->data['page_title'] = lang('payment_receive_title');
			$this->data['payment'] = $this->template->partial("receive_payment",$this->data);
			$this->template->build('payments/new_payment',$this->data);

		} else {

			$people_id = $this->people_model->get_a_person_by_name($this->input->post('name'))->row()->id;

			$data = $this->input->post(array(
			'name',
			'amount',
			'account',
			't_date',
			'description',
			), true);

			if (($this->accounts_model->save_payment($data,$people_id,'receive')) == TRUE) {
				$this->session->set_flashdata('success',lang('payment_applied_success'));
				log_activity($this->session->userdata('user_id'),'applied a new payment to'.$people_id);
				if ($this->input->post('submit', TRUE) == "saveandclose") {
					redirect(ADMIN.'accounts', 'refresh');
					exit;
				} else {
					redirect(ADMIN.'payments/receive_payment', 'refresh');
					exit;
				}

			} else {
				$this->session->set_flashdata('error',($this->accounts_model->errors()=='')?lang('product_add_error'):$this->accounts_model->errors());
				redirect(ADMIN.'payments/receive_payment', 'refresh');
				exit;
			}
		}

	}
	
	function transfer()
	{
		if (!has_action('create'))
			show_404();

		$this->form_validation->set_rules('bank_id',lang('payment_transfer_from'),'trim|required');
		$this->form_validation->set_rules('amount',lang('sales_amount_label'),'trim|required');
		$this->form_validation->set_rules('account_id',lang('payment_transfer_to'),'trim|required');
		$this->form_validation->set_rules('t_date',lang('date'),'trim');

		if (!$this->form_validation->run()) {

			//pass the product data to session so as to retain it after redirect
			if (validation_errors() || $this->ion_auth->errors() || $this->session->flashdata('message')) {
				$this->session->set_flashdata($this->input->post());
				$this->session->set_flashdata('error',(validation_errors() ? validation_errors() : ($this->accounts_model->errors() ? $this->accounts_model->errors() : $this->session->flashdata('message'))));
				redirect(ADMIN."payments/transfer",'refresh');
			}
			

			$banks = $this->accounts_model->select('id,name')->where(['account_type'=>'1','status'=>1])->get_account()->result();
			$option=[];
			foreach ($banks as $bank) {
				$option[$bank->id] = $bank->name;
			}
			$option = [''=>lang('account_choose_account')]+$option;
			$this->data['bank']=[
				'name' => 'bank_id',
				'type' => 'text',
				'placeholder' => lang('payment_account_label'),
				'required'=>'required',
				'class'   => 'form-control',
				'options' => $option,
				'selected'=> ($this->session->flashdata('bank_id')),
			];
			$account = $this->accounts_model->select('id,name')->where(['account_type'=>'1','status'=>1])->get_account()->result();
			$account_option=[];
			foreach ($account as $bank) {
				$account_option[$bank->id] = $bank->name;
			}
			$account_option = [''=>lang('account_choose_account')]+$account_option;
			$this->data['account']=[
				'name' => 'account_id',
				'type' => 'text',
				'placeholder' => lang('payment_account_label'),
				'required'=>'required',
				'class'   => 'form-control',
				'options' => $account_option,
				'selected'=> ($this->session->flashdata('account_id')),
			];
			$this->data['amount']=[
				'name' => 'amount',
				'type' => 'text',
				'placeholder' => lang('sales_amount_label'),
				'required'=>'required',
				'autocomplete'=>'off',
				'class'   => 'form-control digitsonly',
				'value'=> $this->session->flashdata('amount'),
			];
			$this->data['date']=[
				'name' => 't_date',
				'type' => 'text',
				'placeholder' => lang('date'),
				'id'   => 'datepicker',
				'class'   => 'form-control',
				'value'=> $this->session->flashdata('t_date'),
			];
			
			$this->data['page_title'] = lang('payment_transfer_title');
			$this->data['payment'] = $this->template->partial("transfer",$this->data);
			$this->template->build('payments/new_payment',$this->data);
		} else {

			$data = $this->input->post(array(
			'bank_id',
			'amount',
			'account_id',
			't_date',
			), true);

			if (($this->accounts_model->save_transfer($data)) == TRUE) {
				$this->session->set_flashdata('success',lang('payment_applied_success'));
				log_activity($this->session->userdata('user_id'),'applied a transfer from'.$data['bank_id'].' to'.$data['account_id']);
				if ($this->input->post('submit', TRUE) == "saveandclose") {
					redirect(ADMIN.'accounts', 'refresh');
					exit;
				} else {
					redirect(ADMIN.'payments/transfer', 'refresh');
					exit;
				}

			} else {
				$this->session->set_flashdata('error',($this->accounts_model->errors()=='')?lang('product_add_error'):$this->accounts_model->errors());
				redirect(ADMIN.'payments/transfer', 'refresh');
				exit;
			}
		}
	}
	
	function make_payment()
	{
		if (!has_action('create'))
			show_404();

		$this->form_validation->set_rules('name',lang('name'),'trim|required');
		$this->form_validation->set_rules('amount',lang('sales_amount_label'),'trim|required');
		$this->form_validation->set_rules('account',lang('payment_account_label'),'trim|required');
		$this->form_validation->set_rules('t_date',lang('date'),'trim');
		$this->form_validation->set_rules('description',lang('payment_description_label'),'trim');
		
		if (!$this->form_validation->run()) {

			//pass the product data to session so as to retain it after redirect
			if (validation_errors() || $this->ion_auth->errors() || $this->session->flashdata('message')) {
				$this->session->set_flashdata($this->input->post());
				$this->session->set_flashdata('error',(validation_errors() ? validation_errors() : ($this->accounts_model->errors() ? $this->accounts_model->errors() : $this->session->flashdata('message'))));
				redirect(ADMIN."payments/make_payment",'refresh');
			}
			
			$this->data['name']=[
				'name' => 'name',
				'type' => 'text',
				'placeholder' => lang('name'),
				'required'=>'required',
				'class'   => 'form-control',
				"data-people" => "0",
				"id" => "peoplepayment",
				'value'=> $this->session->flashdata('name'),
			];
			
			$banks = $this->accounts_model->select('id,name')->where(['account_type'=>'1','status'=>1])->get_account()->result();
			$option=[];
			foreach ($banks as $bank) {
				$option[$bank->id] = $bank->name;
			}
			$option = [''=>lang('account_choose_account')]+$option;
			$this->data['account']=[
				'name' => 'account',
				'type' => 'text',
				'placeholder' => lang('payment_account_label'),
				'required'=>'required',
				'class'   => 'form-control',
				'options' => $option,
				'selected'=> ($this->session->flashdata('account')),
			];
			$this->data['amount']=[
				'name' => 'amount',
				'type' => 'text',
				'placeholder' => lang('sales_amount_label'),
				'required'=>'required',
				'class'   => 'form-control digitsonly',
				'value'=> $this->session->flashdata('amount'),
			];
			$this->data['date']=[
				'name' => 't_date',
				'type' => 'text',
				'placeholder' => lang('date'),
				'id'   => 'datepicker',
				'class'   => 'form-control',
				'value'=> $this->session->flashdata('t_date'),
			];
			$this->data['description']=[
				'name' => 'description',
				'type' => 'text',
				'placeholder' => lang('payment_description_label'),
				'class'   => 'form-control',
				'rowspan'   => '3',
				'value'=> $this->session->flashdata('description'),
			];
				
			
			$this->data['page_title'] = lang('payment_make_title');
			$this->data['payment'] = $this->template->partial("make_payment",$this->data,'make');
			$this->template->build('payments/new_payment',$this->data);
		} else {

			$people_id = $this->people_model->get_a_person_by_name($this->input->post('name'))->row()->id;
			
			$data = $this->input->post(array(
			'name',
			'amount',
			'account',
			't_date',
			'description',
			), true);

			if (($this->accounts_model->save_payment($data,$people_id,'make')) == TRUE) {
				$this->session->set_flashdata('success',lang('payment_applied_success'));
				log_activity($this->session->userdata('user_id'),'applied a new payment to'.$people_id);
				if ($this->input->post('submit', TRUE) == "saveandclose") {
					redirect(ADMIN.'accounts', 'refresh');
					exit;
				} else {
					redirect(ADMIN.'payments/make_payment', 'refresh');
					exit;
				}

			} else {
				$this->session->set_flashdata('error',($this->accounts_model->errors()=='')?lang('product_add_error'):$this->accounts_model->errors());
				redirect(ADMIN.'payments/make_payment', 'refresh');
				exit;
			}
		}
		
	}
	
	function edit($id=NULL){
		if ($id == NULL) {
			$this->session->set_flashdata('error',sprintf(lang('record_not_exist'),lang('payment')));
			redirect(previous_url('accounts'));
		}
		$this->template->build('edit_payment');
	}
}

//end of line.
