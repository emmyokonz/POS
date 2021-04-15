<?php
defined('BASEPATH')or exit('No direct script allowed!');
/**
* Developed by Techcoderr Developers
*/
class Purchases extends MY_AdminController
{
	function __construct()
	{
		parent::__construct();
		$this->load->model('purchases_model');
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
		$config['base_url'] = ('purchases');
		$config['per_page'] = config_item('results_per_page');

		//lets count all active records
		$total_rows = $config['total_rows'] =  $this->purchases_model->total_rows();

		$offset = (isset($get['page'])) ? config_item('results_per_page') * ($get['page'] - 1) : 0;

		$purchases = $this->purchases_model->all_purchase_transaction(config_item('results_per_page') , $offset);
		//		print_r($purchases);exit;

		$this->pagination->initialize($config);

		$this->data['pagination'] = $this->pagination->create_links();
		$this->data['count_records'] = $total_rows;

		if (!empty($purchases) && is_array($purchases)) {
			foreach ($purchases as $purchase) {
				$purchase->purchases_no = html_tag(
				'a',
				['class'=>"text-left m-b-0",'href'=>admin_url('purchases/edit/'.$purchase->id)],
				$purchase->sales_no
				);
				$customer = $this->people_model->get_a_person($purchase->people_id)->row()->name;

				if ($customer !== NULL) {
					$purchase->customer =html_tag('span', ['class'=>'text-danger'], ($customer));
				} else {
					$purchase->customer = 'UNKNOWN';
				}

				$purchase->total_items = html_tag(
				'span',
				'',
				my_number_format($purchase->total_items)
				);

				$purchase->amount = html_tag(
				'span',
				'',
				my_number_format($purchase->amount,TRUE)
				);

				$purchase->date = html_tag(
				'span',
				'',
				my_full_time_span($purchase->created_date)
				);

				$update=NULL;
				$activate=NULL;
				if (has_action('update')) {
					$update = admin_anchor(
					'purchases/edit/'.$purchase->id,
					fa_icon('edit' , true).' '."<span class='hidden-xs hidden-sm'>Edit</span>",
					['class'=>'btn btn-sm btn-info']
					);
					if ($purchase->status == '0') {
						$activate = admin_anchor(
						'purchases/activate/'.$purchase->id,
						fa_icon('check' , true).' '."<span class='hidden-xs hidden-sm'>Activate</span>",
						['class'=>'btn btn-sm btn-success']
						);
					}
				}
				$delete=NULL;
				if (has_action('delete') && $purchase->status == 1) {
					$delete = admin_anchor(
					'purchases/delete/'.$purchase->id,
					fa_icon('trash-o', TRUE).' '."<span class='hidden-xs hidden-sm'>Delete</span>",
					[
						'class'=>'btn btn-sm btn-danger',
						'onclick'=>"return confirm('".lang('confirm_delete')."')"
					]
					);
				}
				$preview=NULL;

				$preview = admin_anchor(
				'preview/transaction/'.$purchase->id,
				fa_icon('eye', TRUE).' '."<span class='hidden-xs hidden-sm'>Preview</span>",
				[
					'class'=>'btn btn-sm btn-warning',
				]
				);

				$purchase->actions = $update. ' '.$preview.' '.$delete;
			}
		}

		$new_purchases = NULL;
		$new_credit_memo = NULL;

		if (has_action('create')) {
			$new_purchases = html_tag(
			'a',
			[
				'href'=>site_url(ADMIN.'purchases/new'),
				'class'=>'btn btn-danger btn-sm',
			],
			fa_icon('plus',TRUE). ' '.html_tag('span',['class'=>'hidden-xs hidden-sm'],lang('btn_new_purchases'))
			);

			$new_purchase = html_tag(
			'a',
			[
				'href'=>site_url(ADMIN.'purchases/new_credit_memo'),
				'class'=>'btn btn-danger btn-sm',
			],
			fa_icon('plus-circle',TRUE). ' '.html_tag('span',['class'=>'hidden-xs hidden-sm'],lang('btn_new_credit_memo'))
			);
		}

		$this->data['top_actions'] =$new_credit_memo.' '.$new_purchase;
		$this->data['purchases'] = $purchases;
		$this->data['page_title'] = lang('purchases_list_title');
		$this->template->build('purchases/index',$this->data);
	}

	function preview()
	{
		$this->template->build('purchases/preview',$this->data);
	}

	function new()
	{
		$this->session->set_userdata('cart__', array_flip(config_item('transaction_type'))[3]);
		$this->session->set_userdata('__back', current_url());
		$this->data['cart_content'] = $this->pos_cart->prepare_cart('purchase');
		
		$this->data['page_title'] = lang('purchases_new_title');
		$this->data['cart'] = $this->template->partial('purchase_cart',$this->data);
		$this->template->build('purchases/new_purchase',$this->data);
	}

	function new_return()
	{
		$this->session->set_userdata('cart__', array_flip(config_item('transaction_type'))[4]);
		$this->session->set_userdata('__back', current_url());
		$this->data['page_title'] = lang('purchases_new_return_title');
		$this->data['cart_content'] = $this->pos_cart->prepare_cart('return');
		$this->data['cart'] = $this->template->partial('return_cart',$this->data);
		$this->template->build('purchases/new_purchase',$this->data);
	}
	function edit($sales_id=NULL)
	{
		if (!has_action('update'))
			show_404('welcome',TRUE);

		$sales_id = xss_clean($sales_id);

		$transaction = $this->purchases_model->get_a_purchase_transaction($sales_id);

		$transaction_items_obj = $this->purchases_model->get_a_purchase_transaction_items($sales_id);
		

		//		print_r($items_array);exit;
		//lets build the cart items

		$transaction_type = array_flip(config_item('transaction_type'))[$transaction->transaction_type];
		$this->session->set_userdata('cart__', $transaction_type);
		$this->session->set_userdata('__back', current_url());

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
		$this->data['page_title'] = lang('purchases_edit_title');
		$this->data['cart_content'] = $this->pos_cart->prepare_cart($transaction_type);
		$this->data['cart'] = $this->template->partial($transaction_type.'_cart',$this->data);
		$this->template->build('purchases/edit_purchase',$this->data);
	}

	function delete($sales_id)
	{

		if (!has_action('delete'))
			show_404('welcome',TRUE);

		$sales_id = xss_clean($sales_id);

		if ($this->purchases_model->delete_purchase($sales_id)) {
			$this->session->set_flashdata('success',lang('purchases_deleted_successfully'));
			redirect('purchases');
		}
		$this->session->set_flashdata('success',lang('purchases_deleted_not_successfully'));
		redirect('purchases');
	}

}

//end of line.
