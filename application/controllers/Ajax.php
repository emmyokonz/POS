<?php

defined('BASEPATH') or exit('No diirect access allowed!');



class Ajax extends MY_AdminController
{	
	
	function __construct()
	{
		parent::__construct();
		if (!class_exists('product_model')) {
			$this->load->model('product_model');
		}
			
	}
	function index()
	{
		if (!$this->input->is_ajax_request()) {
			$this->output->set_header('HTTP/1.1 404 NOT FOUND');
			$this->template->build('errors/error_404');//loading in custom error view
		}
	}
	
	function new_category_ajax()
	{
		if (!$this->input->is_ajax_request()) {
			$this->output->set_header('HTTP/1.1 404 NOT FOUND');
			show_404();
		}

		$this->output->set_content_type('application/json','utf-8');
		$data['name'] = $this->input->post('name',TRUE);
		$data['description'] = $this->input->post('description',TRUE);
		$data['created_date'] = time();
		$data['staff'] = $this->session->userdata('user_id');
		if (!class_exists('product_model')) {
			$this->load->model('product_model');
		}
		$save = $this->product_model->save($data,2);
		
		if ($save!==FALSE) {
			$data['message'] = lang('product_category_add_success');
			$data['response'] = '200';
			$data['new_cat'] = $save;
			
			echo json_encode($data);
			exit;
		}
		$data['message'] = ($this->product_model->errors()=='')?lang('product_add_error'):$this->product_model->errors();
		$data['response'] = 'faild';

		echo json_encode($data);
		exit;
	}
	
	function pages($page)
	{
		if (!$this->input->is_ajax_request()) {
			$this->output->set_header('HTTP/1.1 404 NOT FOUND');
			$this->template->build('errors/error_404');//loading in custom error view
		}
		$this->output->set_content_type('application/json','utf-8');
		if (file_exists($this->template->theme_path('partials/'.$page.'.php'))) {
			$this->data['page'] = $this->template->partial($page);
		}else{
			$this->data['page'] = lang('page_not_found').'ds';
		}
		echo json_encode($this->data);
	}

	function get_categories_ajax()
	{
		if (!$this->input->is_ajax_request()) {
			$this->output->set_header('HTTP/1.1 404 NOT FOUND');
			$this->template->build('errors/error_404');//loading in custom error view
		}
		if (!class_exists('product_model')) {
			$this->load->model('product_model');
		}
		$categories = $this->product_model->select('name,id')->where('item_type',2)->get_products()->result();
		echo( json_encode($categories));
	}
	
	function item_search()
	{
		if (!$this->input->is_ajax_request()) {
			$this->output->set_header('HTTPS/1.1 404 NOT FOUND');
			$this->template->build('errors/error_404');//loading in custom error view
		}
		if (!class_exists('product_model')) {
			$this->load->model('product_model');
		}
		$items = $this->product_model->get_item_search_suggestions($this->input->post('term'),$this->input->post('limit'));
//		print_r($items);exit;
		$suggestions = array();
		foreach ($items as $item) {
			$renderItem = array();
			$qty = $this->product_model->where('item_id',$item->id)->get_product_metadata()->row()->qty;
			$renderItem["label"] = $item->name . " | " . $item->suk. " (".$qty.")";
			$renderItem["value"] = strtoupper($item->name);
			$renderItem["qty"] = $qty;
			$suggestions[] = $renderItem;
		}
//		exit;
		//$suggestions = array_merge($suggestions, $this->Item_kit->get_item_kit_search_suggestions($this->input->post('q'),$this->input->post('limit')));
		echo json_encode($suggestions);
	}
	
	function people_search()
	{
		if (!$this->input->is_ajax_request()) {
			$this->output->set_header('HTTPS/1.1 404 NOT FOUND');
			$this->template->build('errors/error_404');
		}
		if (!class_exists('people_model')) {
			$this->load->model('people_model');
		}
//		print_r($people);exit;
		$people = $this->people_model->get_people_search_suggestions($this->input->post('term'),$this->input->post('people'),$this->input->post('limit'));
		$suggestions = array();
		foreach ($people as $person) {
			$renderperson = array();
			$balance = $this->people_model->where('people_id',$person->id)->get_person_metadata()->row()->balance;
			$renderperson["label"] = $person->name . " | " . $person->company. " (".my_number_format($balance,1).")";
			$renderperson["value"] = strtoupper($person->name);
			$renderperson["balance"] = my_number_format($balance,1);
			$suggestions[] = $renderperson;
		}
//		exit;
		//$suggestions = array_merge($suggestions, $this->person_kit->get_person_kit_search_suggestions($this->input->post('q'),$this->input->post('limit')));
		echo json_encode($suggestions);
	}
	
	function add_to_cart(){
		if (!$this->input->is_ajax_request()) {
			$this->output->set_header('HTTPS/1.1 404 NOT FOUND');
			$this->template->build('errors/error_404');//loading in custom error view
		}
		if (!class_exists('POS_cart')) {
			$this->load->library('POS_cart');}
		$data=array();
		
		$out_of_stock = 0;
		if (!$this->input->post("item")) {
			$data['responds']['status'] = ERROR;
			$data['responds']['error'] = lang('sales_provide_item');
			echo json_encode($data['responds']);
			exit;
		}
		$item_id = $this->product_model->where('name',$this->input->post("item"))->product()->row()->id;
		
		
		//check if item is out of stock
		if ($this->product_model->where('item_id',$item_id)->get_product_metadata()->row()->qty <= config_item('reordering_point')) {

			$data['responds']['out_of_stock'] = 1;
			$data['responds']['message'] = $this->lang->line('sales_quantity_less_than_zero');

		}
		
		if (!$out_of_stock) {
			$this->pos_cart->add_item($this->input->post("cart"),$item_id);
		}
		$data['responds']['status'] = SUCCESS;
		$data['responds']['content'] = $this->pos_cart->prepare_cart($this->input->post("cart"));
		echo json_encode($data['responds']);
		exit;
	}
	
	function add_cart_people()
	{
		if (!$this->input->is_ajax_request()) {
			$this->output->set_header('HTTPS/1.1 404 NOT FOUND');
			$this->template->build('errors/error_404');//loading in custom error view
		}
		if (!class_exists('POS_cart')) {
			$this->load->library('POS_cart');}
		if (!class_exists('people_model')) {
			$this->load->model('people_model');}
		$data=array();
		
		if (!$this->input->post("name")) {
			$data['responds']['status'] = ERROR;
			$data['responds']['error'] = lang('sales_provide_name');
			echo json_encode($data['responds']);
			exit;
		}
		$people_id = $this->people_model->where(['name'=>($this->input->post("name")),'status'=>1])->people()->row()->id;
		$people_bal = $this->people_model->where(['people_id'=>$people_id])->get_person_metadata()->row()->balance;

		$this->pos_cart->set_people($this->input->post("cart"),$this->input->post("name"));
		$data['responds']['status'] = SUCCESS;
		$data['responds']['people'] = $this->input->post("name");
		$data['responds']['balance'] = $people_bal;
		
		echo json_encode($data['responds']);
		exit;
	}
	
	function update_cart(){
		if (!$this->input->is_ajax_request()) {
			$this->output->set_header('HTTPS/1.1 404 NOT FOUND');
			$this->template->build('errors/error_404');//loading in custom error view
		}
		if (!class_exists('POS_cart')) {
			$this->load->library('POS_cart');}
			
		$form_data['line'] = $this->input->post('line');
		$form_data['qty'] = $this->input->post('qty');
		$form_data['price'] = $this->input->post('price');
		$form_data['paid'] = $this->input->post('paid');
		
		if ($this->pos_cart->update_cart_item($this->input->post('cart'),$form_data) == TRUE) {
			$data['responds']['status'] = SUCCESS;
		}else{
			$data['responds']['status'] = ERROR;
			$data['responds']['error'] = "Error occured while updating cart";
		}
		echo json_encode($data['responds']);
		exit();
	}
	
	function update_cart_payment()
	{
		if (!$this->input->is_ajax_request()) {
			$this->output->set_header('HTTPS/1.1 404 NOT FOUND');
			$this->template->build('errors/error_404');//loading in custom error view
		}
		if (!class_exists('POS_cart')) {
			$this->load->library('POS_cart');}
		
		if ($this->pos_cart->update_cart($this->input->post('cart'),'payment',$this->input->post('total')) == TRUE) {
			$data['responds']['status'] = SUCCESS;
		}else{
			$data['responds']['status'] = ERROR;
			$data['responds']['error'] = "Error occured while updating cart";
		}
		echo json_encode($data['responds']);
		exit();
	}
	
	function remove_cart_item()
	{
		if (!$this->input->is_ajax_request()) {
			$this->output->set_header('HTTPS/1.1 404 NOT FOUND');
			$this->template->build('errors/error_404');//loading in custom error view
		}
		if (!class_exists('POS_cart')) {
			$this->load->library('POS_cart');}
		
		if ($this->pos_cart->delete_item($this->input->post('cart'),$this->input->post('line'),$this->input->post('close_cart')) == TRUE) {
			$data['responds']['status'] = SUCCESS;
			$data['responds']['message'] = "Item(s) removed successfully";
			$data['responds']['content'] = $this->pos_cart->prepare_cart($this->input->post("cart"));
			echo json_encode($data['responds']);
			exit();
		}else{
			$data['responds']['status'] = ERROR;
			$data['responds']['error'] = "Error occured while removing item(s) from cart";
			echo json_encode($data['responds']);
			exit();
		}
		
	}
	
	public function save_cart(){
		if (!$this->input->is_ajax_request()) {
			$this->output->set_header('HTTPS/1.1 404 NOT FOUND');
			$this->template->build('errors/error_404');//loading in custom error view
		}
		if (!class_exists('POS_cart')) {
			$this->load->library('POS_cart');}
			
		$transaction = $this->pos_cart->save_transaction($this->input->post('cart'),$this->input->post('save'));
		
		if ($transaction) {
			$data['responds']['status'] = SUCCESS;
			$data['responds']['message'] = $this->pos_cart->messages()/*['message']*/;
			$this->pos_cart->delete_item($this->input->post('cart'),'1',TRUE);
			if ($this->input->post("save") == 'savenew') {
				if ($this->input->post("cart") == 'sales_cart') {
					$data['responds']['href'] = site_url('sales/new');
				} elseif($this->input->post("cart") == 'credit_memo') {
					$data['responds']['href'] = site_url('sales/new_credit_memo');
				}elseif($this->input->post("cart") == 'purchase') {
					$data['responds']['href'] = site_url('purchases/new');
				}elseif($this->input->post("cart") == 'return') {
					$data['responds']['href'] = site_url('purchases/new_return');
				}
			} else {
				if ($this->input->post("cart") == 'sales_cart') {
					$data['responds']['href'] = site_url('sales');
				} elseif($this->input->post("cart") == 'credit_memo') {
					$data['responds']['href'] = site_url('sales');
				}elseif($this->input->post("cart") == 'purchase') {
					$data['responds']['href'] = site_url('purchases');
				}elseif($this->input->post("cart") == 'return') {
					$data['responds']['href'] = site_url('purchases');
				}
			}
			echo json_encode($data['responds']);
			exit();
		}
			
		$data['responds']['status'] = ERROR;
		$data['responds']['error'] = $this->pos_cart->errors();
		echo json_encode($data['responds']);
		exit();
	}
	
	public function edit_cart(){
		if (!$this->input->is_ajax_request()) {
			$this->output->set_header('HTTPS/1.1 404 NOT FOUND');
			$this->template->build('errors/error_404');//loading in custom error view
		}
		if (!class_exists('POS_cart')) {
			$this->load->library('POS_cart');}
		
		if ($transaction = $this->pos_cart->update_transaction($this->input->post('cart'),$this->input->post('save'))) {
			$data['responds']['status'] = SUCCESS;
			$data['responds']['message'] = $this->pos_cart->messages()/*['message']*/;
			$this->pos_cart->delete_item($this->input->post('cart'),'1',TRUE);
			if ($this->input->post("save") == 'savenew') {
				if ($this->input->post("cart") == 'sales_cart') {
					$data['responds']['href'] = site_url('sales/new');
				} elseif($this->input->post("cart") == 'credit_memo') {
					$data['responds']['href'] = site_url('sales/new_credit_memo');
				}elseif($this->input->post("cart") == 'purchase') {
					$data['responds']['href'] = site_url('purchases/new');
				}elseif($this->input->post("cart") == 'return') {
					$data['responds']['href'] = site_url('purchases/new_return');
				}
			} else {
				if ($this->input->post("cart") == 'sales_cart') {
					$data['responds']['href'] = site_url('sales');
				} elseif($this->input->post("cart") == 'credit_memo') {
					$data['responds']['href'] = site_url('sales');
				}elseif($this->input->post("cart") == 'purchase') {
					$data['responds']['href'] = site_url('purchases');
				}elseif($this->input->post("cart") == 'return') {
					$data['responds']['href'] = site_url('purchases');
				}
			}
			echo json_encode($data['responds']);
			exit();
		}
			
		$data['responds']['status'] = ERROR;
		$data['responds']['error'] = $this->pos_cart->errors();
		echo json_encode($data['responds']);
		exit();
	}
	
	public function cart_empty()
	{
		if (!$this->input->is_ajax_request()) {
			$this->output->set_header('HTTPS/1.1 404 NOT FOUND');
			$this->template->build('errors/error_404');//loading in custom error view
		}
		if (!class_exists('POS_cart')) {
			$this->load->library('POS_cart');}
			
		if ($this->pos_cart->cart_is_empty($this->input->post('cart'))) {
			$data['responds']['status'] = SUCCESS;
			echo json_encode($data['responds']);
			exit();
		}
		$data['responds']['status'] = ERROR;
		echo json_encode($data['responds']);
		exit();
	}
}

function bs_alert($message = '', $type = 'info')
	{
		if (empty($message))
		{
			return;
		}
		// Turn 'error' into 'danger' because it does not exist on bootstrap.
		$type == 'error' && $type = 'danger';

		$alert =<<<END
<div class="alert alert-{type}" style="display: block;">
	<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
	{message}
</div>
END;
		return str_replace(
			array('{type}', '{message}'),
			array($type, $message),
			$alert
		);
	}
