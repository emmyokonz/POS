<?php
defined('BASEPATH')or exit('No direct script allowed!');
/**
* Developed by Techcoderr Developers
*/
class Preview extends MY_AdminController
{
	function __construct()
	{
		parent::__construct();
		$this->load->model('sales_model');
		$this->load->library('POS_cart');
		$this->data['top_actions'] = '';
		add_script('assets/js/jquery-ui.min');
		add_script('assets/js/NumberFormat');
		add_script('assets/js/ajax_queue');
		$this->session->set_userdata('ajax_var','kjsh');
		$this->data['print_header'] = $this->template->partial('print_header');
	}

	function transaction($transaction_id = NULL)
	{
		if (!class_exists('people_model'))
			$this->load->model('people_model');
		if (!class_exists('product_model'))
			$this->load->model('product_model');
		$transaction = $this->sales_model->get_a_sales_transaction($transaction_id);
		if (!empty($transaction)) {
			$transaction->{'items'} = $this->sales_model->get_a_sales_transaction_items($transaction_id);
			foreach ($transaction->items as $item) {
				$item->description = $this->product_model->get_info($item->product_id)->description;
			}
			
			$people = $this->people_model->get_a_person($transaction->people_id)->row();
			$transaction->people_name = humanize($people->name,'-');
			$transaction->people_address = $people->address;
			$transaction->email = $people->email;
			$transaction->phone = $people->phone;
			$transaction->company = $people->company;
			$transaction->created_date = invoice_date($transaction->created_date);
			$transaction->balance = $this->people_model->where('people_id',$transaction->people_id)->get_person_metadata()->row()->balance;
			$transaction->transaction_type = config_item('invoice_preview_type')[$transaction->transaction_type];

			if ($transaction->transaction_type == 'INVOICE' || $transaction->transaction_type == "CREDIT MEMO") {
				$transaction->edit_link = site_url('sales/edit/'.$transaction_id);
			}else{
				$transaction->edit_link = site_url('purchases/edit/'.$transaction_id);
			}
		}else{
			$transaction = new stdClass();
			
			//Get all the fields from items table
			$fields = $this->db->where(['id'=>$transaction_id])->get(config_item('tables')['transaction_header'])->list_fields(config_item('tables')['products']);

			foreach ($fields as $field) {
				$transaction->$field='';
			}
			$transaction->items = [];
			$transaction->people_name ='';
			$transaction->people_address = '';
			$transaction->email ='';
			$transaction->phone = '';
			$transaction->company ='';
			$transaction->balance ='';
			$transaction->sales_no ='';
			$transaction->sales_date ='';

		}
//		print_r($transaction);exit;
		$transaction->link = my_previous_page();
		$this->data['transaction'] = $transaction;
		$this->template->build('preview/transaction',$this->data);
	}
	
	function invoice(){
		if (!class_exists('pos_cart')) {
			$this->load->library('pos_cart');
		}
		if (!class_exists('people_model'))
			$this->load->model('people_model');
		if (!class_exists('product_model'))
			$this->load->model('product_model');
		$cart = $this->session->userdata('cart__');
		$transaction = $this->pos_cart->get_cart($cart);
//		print_r($transaction);exit;
		if (!empty($transaction['items']) && !empty($transaction['people']['id'])) {

			foreach ($transaction['items'] as $item) {
				$item['description'] = $this->product_model->get_info($item['item_id'])->description;
			}

			$people = $this->people_model->get_a_person($transaction['people']['id'])->row();
			$transaction['people_name'] = humanize($people->name,'-');
			$transaction['people_address'] = $people->address;
			$transaction['email'] = $people->email;
			$transaction['phone'] = $people->phone;
			$transaction['company'] = $people->company;
			$transaction['sales_no'] = '';
			$transaction['created_date'] = invoice_date(time());
		} else {
			
			$transaction['items'] = [];
			$transaction['people_name'] = '';
			$transaction['people_address'] = '';
			$transaction['email'] = '';
			$transaction['phone'] = '';
			$transaction['company'] = '';
			$transaction['balance'] = '';
			$transaction['created_date'] = invoice_date(time());
			$transaction['sales_no'] = '';
		}
//		echo $cart;
//		echo config_item('invoice_preview_type')[config_item('transaction_type')[$cart]];exit;
		$transaction['transaction_type'] = config_item('invoice_preview_type')[config_item('transaction_type')[$cart]];
		$transaction['link'] = $this->agent->referrer();
//		$this->session->unset_userdata('__back');
		//		print_r($transaction);exit;
		$this->data['transaction'] = $transaction;
		$this->template->build('preview/invoice',$this->data);
	}
	
	function Payment($transaction_id=NULL)
	{
		if (!class_exists('people_model'))
			$this->load->model('people_model');
		if (!class_exists('accounts_model'))
			$this->load->model('accounts_model');
		if(is_null($transaction_id))
			$this->session->set_flashdata('error',sprintf(lang('record_not_exist'),lang('payment')));
		$transaction = $this->accounts_model->get_a_transaction_detail($transaction_id);
		$this->data['transaction'] = $transaction;
		$this->template->build('preview/payment',$this->data);
	}
}

//end of line.
