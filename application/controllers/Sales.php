<?php
defined('BASEPATH')or exit('No direct script allowed!');
/**
* Developed by Techcoderr Developers
*/
class Sales extends MY_AdminController
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
	}

	function index()
	{
		parse_str($_SERVER['QUERY_STRING'], $get);

		$this->load->library('pagination');
		$config['base_url'] = ('sales');
		$config['per_page'] = config_item('results_per_page');
		
		//lets count all active records
		$total_rows = $config['total_rows'] =  $this->sales_model->total_rows();

		$offset = (isset($get['page'])) ? config_item('results_per_page') * ($get['page'] - 1) : 0;

		$sales = $this->sales_model->all_sales_transaction(config_item('results_per_page') , $offset);
//		print_r($sales);exit;

		$this->pagination->initialize($config);

		$this->data['pagination'] = $this->pagination->create_links();
		$this->data['count_records'] = $total_rows;

		if (!empty($sales) && is_array($sales)) {
			foreach ($sales as $sale) {
				$sale->sales_no = html_tag(
				'a',
				['class'=>"text-left m-b-0",'href'=>admin_url('sales/edit/'.$sale->id)],
				$sale->sales_no
				);
				$customer = $this->people_model->get_a_person($sale->people_id)->row()->name;

				if ($customer !== NULL) {
					$sale->customer =html_tag('span', ['class'=>'text-danger'], ($customer));
				} else {
					$sale->customer = 'UNKNOWN';
				}

				$sale->total_items = html_tag(
				'span',
				'',
				my_number_format($sale->total_items)
				);

				$sale->amount = html_tag(
				'span',
				'',
				my_number_format($sale->amount,TRUE)
				);

				$sale->date = html_tag(
				'span',
				'',
				my_full_time_span($sale->created_date)
				);

				$update=NULL;
				$activate=NULL;
				if (has_action('update')) {
					$update = admin_anchor(
					'sales/edit/'.$sale->id,
					fa_icon('edit' , true).' '."<span class='hidden-xs hidden-sm'>Edit</span>",
					['class'=>'btn btn-sm btn-info']
					);
					if ($sale->status == '0') {
						$activate = admin_anchor(
						'sales/activate/'.$sale->id,
						fa_icon('check' , true).' '."<span class='hidden-xs hidden-sm'>Activate</span>",
						['class'=>'btn btn-sm btn-success']
						);
					}
				}
				$delete=NULL;
				if (has_action('delete') && $sale->status == 1) {
					$delete = admin_anchor(
					'sales/delete/'.$sale->id,
					fa_icon('trash-o', TRUE).' '."<span class='hidden-xs hidden-sm'>Delete</span>",
					[
						'class'=>'btn btn-sm btn-danger',
						'onclick'=>"return confirm('".lang('confirm_delete')."')"
					]
					);
				}
				$preview=NULL;

				$preview = admin_anchor(
				'preview/transaction/'.$sale->id,
				fa_icon('eye', TRUE).' '."<span class='hidden-xs hidden-sm'>Preview</span>",
				[
					'class'=>'btn btn-sm btn-warning',
				]
				);

				$sale->actions = $update. ' '.$preview.' '.$delete;
			}
		}
		
		$new_sales = NULL;
		$new_credit_memo = NULL;

		if (has_action('create')) {
			$new_sales = html_tag(
			'a',
			[
				'href'=>site_url(ADMIN.'sales/new'),
				'class'=>'btn btn-danger btn-sm',
			],
			fa_icon('plus',TRUE). ' '.html_tag('span',['class'=>'hidden-xs hidden-sm'],lang('btn_new_sales'))
			);

			$new_sale = html_tag(
			'a',
			[
				'href'=>site_url(ADMIN.'sales/new_credit_memo'),
				'class'=>'btn btn-danger btn-sm',
			],
			fa_icon('plus-circle',TRUE). ' '.html_tag('span',['class'=>'hidden-xs hidden-sm'],lang('btn_new_credit_memo'))
			);
		}
		
		$this->data['top_actions'] =$new_credit_memo.' '.$new_sales;
		$this->data['sales'] = $sales;
		$this->data['page_title'] = lang('sales_list_title');
		$this->template->build('Sales/index',$this->data);
	}

	function preview()
	{
		$this->template->build('Sales/preview',$this->data);
	}

	function new()
	{
//		$cart = $this->pos_cart->delete_item('credit_memo',0,1);
		$this->session->set_userdata('cart__', array_flip(config_item('transaction_type'))[1]);
		/*$this->session->set_userdata('__back', current_url());*/
		$this->data['cart_content'] = $this->pos_cart->prepare_cart('sales_cart');
		$this->data['page_title'] = lang('sales_new_title');
		$this->data['cart'] = $this->template->partial('sales_cart',$this->data);
		$this->template->build('Sales/new_sales',$this->data);
	}
	
	function new_credit_memo()
	{
		//		$cart = $this->pos_cart->delete_item('credit_memo',0,1);
		$this->session->set_userdata('cart__', array_flip(config_item('transaction_type'))[2]);
		/*$this->session->set_userdata('__back', current_url());*/
		$this->data['page_title'] = lang('sales_new_credit_memo_title');
		$this->data['cart_content'] = $this->pos_cart->prepare_cart('credit_memo');
		$this->data['cart'] = $this->template->partial('credit_memo',$this->data);
		$this->template->build('Sales/new_sales',$this->data);
	}

	function edit($sales_id=NULL)
	{
		if (!has_action('update'))
			show_404('welcome',TRUE);

		$sales_id = xss_clean($sales_id);
		
		$transaction = $this->sales_model->get_a_sales_transaction($sales_id);

		$transaction_items_obj = $this->sales_model->get_a_sales_transaction_items($sales_id);

//		print_r($items_array);exit;
		//lets build the cart items
		
		$transaction_type = array_flip(config_item('transaction_type'))[$transaction->transaction_type];
		$this->session->set_userdata('cart__', $transaction_type);
		/*$this->session->set_userdata('__back', current_url());*/
	
		//first empty the cart
		$this->pos_cart->delete_item($transaction_type,0,1);

		$items_array =[];
		$line = 0;
		foreach ($transaction_items_obj as $key =>$item) {
			$insertkey = ++$line;
			$item_info = $this->product_model->get_info($item->product_id);
			$c_item = [
				'item_id'=>$item->product_id,
				'line'=>$insertkey,
				'name'=>$item_info->name,
				'item_number'=>$item_info->product_no,
				'description'=>$item_info->description,
				'quantity'=>$item->qty,
				'qty_in_stock'=>$item_info->qty,
				'price'=>$item->price
			];
			//lets convert object to array.
			$items_array[$insertkey] = $c_item;
		}

		//lets generate all cart content;
		$cart_data = [
			'items'=>$items_array,
			'people' =>['name'=>$this->people_model->get_a_person($transaction->people_id)->row()->name,'id'=>$transaction->people_id],
			'subtotal'=>$transaction->amount,
			'total'=>$transaction->amount,
			'payment'=>'',
			'created_date' => $transaction->created_date,
			'sales_id'=>$sales_id,
		];
		$this->pos_cart->set_cart($transaction_type , $cart_data);	
		
		//create a new cart
		$this->data['page_title'] = lang('sales_edit_title');
		$this->data['cart_content'] = $this->pos_cart->prepare_cart($transaction_type);
		$this->data['cart'] = $this->template->partial($transaction_type,$this->data);
		$this->template->build('Sales/edit_sales',$this->data);
	}
	
	function delete($sales_id){
	
		if (!has_action('delete'))
			show_404('welcome',TRUE);

		$sales_id = xss_clean($sales_id);
		
		if ($this->sales_model->delete_sales($sales_id)) {
			$this->session->set_flashdata('success',lang('sales_deleted_successfully'));
			redirect('sales');
		}
		$this->session->set_flashdata('success',lang('sales_deleted_not_successfully'));
		redirect('sales');
		
	}
	
	function remove_sales_item($item_number , $cart = 'sales_cart')
	{
		$this->pos_cart->delete_item($cart,$item_number);
		redirect('sales/new');
	}
}

//end of line.
