<?php
defined('BASEPATH')or exit('No direct script allowed!');
/**
* Developed by Techcoderr Developers
*/
class Expenses extends MY_AdminController
{
	function __construct()
	{
		parent::__construct();
		$this->load->model('accounts_model');
		$this->load->model('people_model');
		
	}

	function index()
	{show_404();
		$this->data['page_title'] = lang('expenses_list_title');
		$this->data['expenses'] = $this->template->partial("new_expenses_widget");
		$this->template->build('expenses/index',$this->data);
	}

	function new()
	{
		if (!has_action('create'))
			show_404();
		
		$this->form_validation->set_rules('name',lang('name'),'trim');
		$this->form_validation->set_rules('bank_id',lang('account_bank_label'),'trim|required');
		$this->form_validation->set_rules('amount',lang('sales_amount_label'),'trim|required');
		$this->form_validation->set_rules('account_id',lang('payment_account_label'),'trim|required');
		$this->form_validation->set_rules('t_date',lang('date'),'trim');
		$this->form_validation->set_rules('description',lang('payment_description_label'),'trim|required');
		
		if (!$this->form_validation->run()) {

			//pass the product data to session so as to retain it after redirect
			if (validation_errors() || $this->ion_auth->errors() || $this->session->flashdata('message')) {
				$this->session->set_flashdata($this->input->post());
				$this->session->set_flashdata('error',(validation_errors() ? validation_errors() : ($this->accounts_model->errors() ? $this->accounts_model->errors() : $this->session->flashdata('message'))));
				redirect(ADMIN."expenses/new",'refresh');
			}

			$this->data['name']=[
				'name' => 'name',
				'type' => 'text',
				'placeholder' => lang('name'),
				'class'   => 'form-control',
				"data-people" => "0",
				'autocomplete'   => 'off',
				"id" => "peoplepayment",
				'value'=> $this->session->flashdata('name'),
			];

			$banks = $this->accounts_model->select('id,name')->where(['account_type'=>'1','status'=>1])->get_account()->result();
			$option=[];
			foreach ($banks as $bank) {
				$option[$bank->id] = $bank->name;
			}
			$option = [''=>lang('account_choose_bank_account')]+$option;
			$this->data['bank']=[
				'name' => 'bank_id',
				'type' => 'text',
				'placeholder' => lang('account_bank_label'),
				'required'=>'required',
				'class'   => 'form-control',
				'options' => $option,
				'selected'=> ($this->session->flashdata('bank_id')),
			];
			$accounts = $this->accounts_model->select('id,name')->where(['account_type'=>'2','status'=>1])->get_account()->result();
			$account_option=[];
			foreach ($accounts as $account) {
				$account_option[$account->id] = $account->name;
			}
			$account_option = [''=>lang('account_choose_account')]+$account_option;
			$this->data['account']=[
				'name' => 'account_id',
				'type' => 'text',
				'placeholder' => lang('payment_account_label'),
				'required'=>'required',
				'autocomplete'   => 'off',
				'class'   => 'form-control',
				'options' => $account_option,
				'selected'=> ($this->session->flashdata('account_id')),
			];
			$this->data['amount']=[
				'name' => 'amount',
				'type' => 'text',
				'placeholder' => lang('sales_amount_label'),
				'required'=>'required',
				'autocomplete' =>"off",
				'autocomplete'   => 'off',
				'class'   => 'form-control digitsonly',
				'value'=> $this->session->flashdata('amount'),
			];
			$this->data['date']=[
				'name' => 't_date',
				'type' => 'text',
				'placeholder' => lang('date'),
				'id'   => 'datepicker',
				'autocomplete'   => 'off',
				'class'   => 'form-control',
				'value'=> $this->session->flashdata('t_date'),
			];
			$this->data['description']=[
			'name' => 'description',
			'autocomplete' =>"off",
			'type' => 'text',
			'autocomplete'   => 'off',
				'placeholder' => lang('payment_description_label'),
				'class'   => 'form-control',
				'value'=> $this->session->flashdata('description'),
			];

			$this->data['page_title'] = lang('expenses_new_title');
			$this->data['expenses'] = $this->template->partial("expenses_form_widget",$this->data);
			$this->template->build('expenses/new_expenses',$this->data);

		}else{
		
			$people_id_query = $this->people_model->get_a_person_by_name($this->input->post('name'));
			if ($people_id_query !== FALSE) {
				$people_id = $people_id_query->row()->id;
			} else {
				$people_id = $this->input->post('name');
			}

			$data = $this->input->post(array(
			'name',
			'bank_id',
			'amount',
			'account_id',
			't_date',
			'description',
			), true);

			if (($this->accounts_model->save_expenses($data,$people_id)) == TRUE) {
				$this->session->set_flashdata('success',lang('expenses_applied_success'));
				log_activity($this->session->userdata('user_id'),'applied a new expenses to'.$people_id);
				if ($this->input->post('submit', TRUE) == "saveandclose") {
					redirect(ADMIN.'accounts', 'refresh');
					exit;
				} else {
					redirect(ADMIN.'expenses/new', 'refresh');
					exit;
				}

			} else {
				$this->session->set_flashdata('error',($this->people_model->errors()=='')?lang('product_add_error'):$this->people_model->errors());
				redirect(ADMIN.'payments/make_payment', 'refresh');
				exit;
			}
		}
		
	}

	function edit()
	{
		$this->data['page_title'] = lang('expenses_edit_title');
		$this->data['expenses'] = $this->template->partial("edit_expenses_widget",$this->data);
		$this->template->build('expenses/index',$this->data);
	}
}

//end of line.
