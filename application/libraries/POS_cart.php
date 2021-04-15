<?php
defined('BASEPATH') or exit('No direct access to script is allowed');

class POS_cart {
	
	private $_tables = [];
	private $_where = [];
	private $_or_where = [];
	private $_select =[];
	private $_like =[];
	private $_limit;
	private $_offset;
	private $_order;
	private $_order_by;
	private $_query_result=[];
	private $_errors =[];
	private $_messages = [];
	private $_output = '';
	private $cart_content = [];
	
	function __construct() 
	{
		$this->_tables = config_item('tables');
		$this->load->model(['product_model','people_model','purchases_model']);
		
	}

	/**
	* __call
	*
	* Acts as a simple way to call model methods without loads of stupid alias'
	*
	* @param string $method
	* @param array  $arguments
	*
	* @return mixed
	* @throws Exception
	*/
	public function __call($method, $arguments)
	{
		if (method_exists( $this->product_model, $method) ) {
			return call_user_func_array( array($this->product_model,$method), $arguments);
		}
		throw new Exception('Undefined method Product models::' . $method . '() called');
	}

	/**
	* __get
	*
	* Enables the use of CI super-global without having to define an extra variable.
	*
	* I can't remember where I first saw this, so thank you if you are the original author. -Militis
	*
	* @param    string $var
	*
	* @return    mixed
	*/
	public function __get($var)
	{
		return get_instance()->$var;
	}
	
	function get_cart($cart)
	{
		if (!$this->session->userdata($cart)) {$this->set_cart($cart);}
			

		return $this->session->userdata($cart);
	}

	function set_cart($cart, $cart_data=[])
	{
		if (!is_array($cart_data)) {
			$cart_data = array($cart_data);
		}
		if (empty($cart_data)) {
			$cart_data = [
			'items'=>[],
			'people' =>['name'=>'','id'=>''],
			'subtotal'=>0,
			'payment'=> '0',
			'total'=>0,
			];
		}
		$this->session->set_userdata($cart,$cart_data);
	}
	
	function delete_item($cart,$line,$empty_cart=FALSE)
	{
		if ($empty_cart) {
			$this->_empty_cart($cart);
			return TRUE;
		}else{
			$items=$this->get_cart($cart)['items'];
			unset($items[$line]);
			$this->update_cart($cart,'items',$items);
			return TRUE;
		}
		return FALSE;	
	}
	
	private function _empty_cart($cart)
	{
		$this->session->unset_userdata($cart);
	}
	
	function update_cart($cart,$key,$data){
		/*if (!is_array($data)) {
			$data = array($data);
		}*/
		
		$cart_details = $this->get_cart($cart);
		
		foreach ($cart_details as $cart_key => $val) {
			if ($key==$cart_key) {
				$cart_details[$cart_key] = $data;
			}
		}
		$this->session->set_userdata($cart,$cart_details);
//		echo json_encode($cart_details);
		
		return TRUE;
	}

	function update_cart_item($cart,$form_data)
	{
		$items = $this->get_cart($cart)['items'];
		if (isset($items[$form_data['line']])) {
			if (my_number_format($form_data['qty'])) {
				$items[$form_data['line']]['quantity'] = $form_data['qty'];
			}
			//Can be set to zero.
			if (is_numeric($form_data['price'])) {
				$items[$form_data['line']]['price'] = $form_data['price'];
			}
			$this->update_cart($cart,'items',$items);
			
			$subtotal = 0;
			foreach ($items as $k) {
//				echo json_encode($items);
				$subtotal = ($subtotal + ($k['quantity'] * $k['price']));
			}
			$this->set_subtotal($cart,$subtotal);
			$this->set_total($cart,$subtotal - floatval($form_data['paid']));

			return true;
		}

		return false;
	}
	
	function add_item($cart, $item_id, $quantity=1, $discount=0, $price=0, $description=null)
	{
		//make sure item exists
		if (!$this->product_model->product_exits($item_id)) {
		
			//try to get item id given an item_number
			$item_id = $this->product_model->get_item_id($item_id);

			if (!$item_id)
				return false;
		}


		//Alain Serialization and Description

		//Get all items in the cart so far...
		$items = $this->get_cart($cart)['items'];
		
		//We need to loop through all items in the cart.
		//If the item is already there, get it's key($updatekey).
		//We also need to get the next key that we are going to use in case we need to add the
		//item to the cart. Since items can be deleted, we can't use a count. we use the highest key + 1.

		$maxkey=0;                       //Highest key so far
//		$itemalreadyinsale=FALSE;        //We did not find the item yet.
		$insertkey=0;                    //Key to use for new entry.
		$updatekey=0;                    //Key to use to update(quantity)
//		if($items)
		foreach ($items as $item) {
			//We primed the loop so maxkey is 0 the first time.
			//Also, we have stored the key in the element itself so we can compare.

			if ($maxkey <= $item['line']) {
				$maxkey = $item['line'];
			}

			/*if ($item['item_id']==$item_id) {
				$itemalreadyinsale=TRUE;
				$updatekey=$item['line'];
			}*/
		}

		$insertkey=$maxkey+1;
		//array/cart records are identified by $insertkey and item_id is just another field.
		$item_info = $this->product_model->get_info($item_id);
		$item = [
		($insertkey)=>[
				'item_id'=>$item_id,
				'line'=>$insertkey,
				'name'=>$item_info->name,
				'item_number'=>$item_info->product_no,
				'description'=>$description!=null ? $description: $item_info->description,
				'quantity'=>$quantity,
				'qty_in_stock'=>$item_info->qty,
				'price'=>$price!==0 ? $price: ($cart=='sales_cart' || $cart=="credit_memo"?$item_info->selling_price:$item_info->cost_price)
			]
		];

		//Item already exists  add to quantity
		/*if ($itemalreadyinsale ) {
			$items[$updatekey]['quantity']+=$quantity;
		} else {
			
		}*/
		
		//add to existing array
		$items+=$item;
		$this->update_cart($cart,'items',$items);
//		print_r($items);
		return true;
	}
	
	function prepare_cart_items($cart_name = 'sales_cart'){
		//	this is for ajax result;
//		print_r($this->get_cart($cart_name));exit;
		$cart = ($this->get_cart($cart_name));
		$cart_items = $this->get_cart($cart_name)['items'];
		$subtotal = 0;
		$amount = 0;
		$cart_str='';
		if (!empty($cart_items)) {
			$n='';
			foreach ($cart_items as $line => $each_item) {
				$cart_str .= '<tr class="cart-item">';
				$cart_str .='<td>'.++$n.'</td>';
				$cart_str .='<td>'.$each_item['description'].'</td>';
				$cart_str .='<td><input type="text" data-line="'.$each_item['line'].'" id="qty_'.$each_item['line'].'" class="qty" value="'.my_number_format(set_value("qty_".$each_item['line'],$each_item['quantity'])) .'" style="width:40px" name="qty_'.$each_item['line'].'"/></td>';
				$cart_str .='<td>'.((config_item('allow_manual_price')==TRUE)?
				'<input type="text" data-line="'.$each_item['line'].'" class="price" value="'.my_number_format(set_value("price_".$each_item['line'],$each_item['price'])).'" style="width:70px" name="price_'.$each_item['line'].'" id="price_'.$each_item['line'].'"/>'
				:
				'<input type="text" data-line="'.$each_item['line'].'" value="'.my_number_format(set_value("price_".$each_item['line'],$each_item['price'])).'" style="width:70px" name="price_'.$each_item['line'].'" id="price_'.$each_item['line'].'"  readonly="readonly"/>')
				.'</td>';
				$amount = $each_item['price']*$each_item['quantity'];
				$cart_str .='<td class="amount" data-amount="'.$amount.'" id="amount_'.$each_item['line'].'">'.my_number_format(($amount),TRUE).'</td>';
				$cart_str .='<td align="right">'.html_tag('a',["href"=>site_url(ADMIN.'sales/remove_sales_item/'.$each_item['line']), "class"=>"btn-danger btn btn-xs remove_cart_item",'data-line'=>$each_item['line']],fa_icon('close',false).' '.lang('btn_remove')).'</td>';
				$cart_str .= '</tr>';
				$subtotal = $subtotal + $amount;
			}
			$this->set_subtotal($cart_name,$subtotal);
			$this->set_total($cart_name,($subtotal - floatval($cart['payment'])));
			$cart_str .= '<tr>';
			$cart_str .='<td colspan="8" style="text-align:right;">'.html_tag('a',["href"=>site_url(ADMIN.'sales/remove_sales_item/'.$each_item['line']), "class"=>"btn-danger btn btn-xs remove_all_cart_item",'data-line'=>$each_item['line']],fa_icon('close',false).' '.lang('btn_clear_cart')).'</td>';
			$cart_str .= '</tr>';
		}else{
			$this->set_subtotal($cart_name,0);
			$this->set_total($cart_name,(0 - floatval($cart['payment'])));
			$cart_str .='<tr class="warning">';
			$cart_str .='<td colspan="8" style="text-align:center;">';
			$cart_str .='<b>'.$this->lang->line('sales_no_items_in_cart').'</b>';
			$cart_str .='</td>';
			$cart_str .= '</tr>';
		}
		
		return $cart_str;
	}
	
	public function cart_is_empty($cart_name)
	{	
		if (!class_exists('Sales_model')) {
			$this->load->model('sales_model');
		}
		$cart = $this->get_cart($cart_name);
//		print_r($cart);
		if (empty($cart['items']) && $cart['people']['id']=='') {
			return TRUE;
		//if we sre editing a sales transaction we close it and start a new transaction
		}elseif (isset($cart['sales_id'])) { // if the cart has a sales id then we are trying to edit a transaction
			$this->delete_item($cart_name,1,1);
			return TRUE;
		} else {
			return FALSE;
		}
		return FALSE;
	}
	public function prepare_cart($cart_name){
		$this->cart_content['cart_items'] = $this->prepare_cart_items($cart_name);
		$this->cart_content['subtotal'] = my_number_format($this->get_cart($cart_name)['subtotal']);
		$this->cart_content['people'] = ($this->get_cart($cart_name)['people']);
		$this->cart_content['payment'] =($this->get_cart($cart_name)['payment']);
		$this->cart_content['total'] = my_number_format($this->get_cart($cart_name)['total'],TRUE);
		return $this->cart_content;
	}
	
	function set_subtotal($cart,$subtotal=0){
		$this->update_cart($cart,'subtotal',$subtotal);
	}
	
	function set_total($cart,$total=0){
		$this->update_cart($cart,'total',$total);
	}
	
	function set_people($cart,$people=NULL){
		
		if (!class_exists('people_model')) {
			$this->load->model('people_model');}
		if (NULL == $people) {
			$people = ['name'=>"walk-in-customer",'id'=>0];
		}else{
			if ($cart == 'purchase' || $cart=='return') {
				$people_type = 2;
			}else{
				$people_type = 1;
			}
			$people_id = $this->people_model->where(['name'=>$people, 'people_type'=>$people_type,'status'=>1])->people()->row()->id;
	//		echo($people_id);
			$people = ['id'=>$people_id, 'name' => $people];
				
		}
		
			
		$this->update_cart($cart,'people',$people);
		
	}
	
	function get_people($cart){
		return $this->get_cart($cart)['people']['name'];
	}
	
	function get_subtotal($cart){
		return $this->get_cart($cart)['subtotal'];
	}
	
	function save_transaction($cart , $save='saveclose'){
		$cart_contents =  $this->get_cart($cart);
		if (empty($cart_contents['items'])) {
			$return['error'] = $this->set_error('empty_sales_not_allowed');
			return FALSE;
		}
		if ($cart_contents['people']['id'] == '') {
			$return['error'] = $this->set_error('empty_sales_people_not_allowed');
			return FALSE;
		}
		
		$form_data['people_id']=$cart_contents['people']['id'];
		$form_data['amount']=$cart_contents['subtotal'];
		$form_data['payment']=$cart_contents['payment'];
		$form_data['created_date']=time();
		$form_data['staff']=$this->session->userdata('user_id');
		$form_data['transaction_type']=config_item('transaction_type')[$cart];
		$form_data['status']=1;
		$form_data['sales_no']=generate_unique_invoice_code($cart,config_item('tables')['transaction_header'],'sales_no',10);
		$profit =0;
		$total_items =0;
		foreach ($cart_contents['items'] as $key=>$items) {
			$cp = $this->product_model->get_info($items['item_id'])->cost_price;
			$form_data['items'][$key]['product_id'] = $items['item_id'];
			$form_data['items'][$key]['qty'] = $items['quantity'];
			$form_data['items'][$key]['price'] = $items['price'];
			$form_data['items'][$key]['cost_price'] = $cp;
			$form_data['items'][$key]['staff'] = $this->session->userdata('user_id');
			$form_data['items'][$key]['profit'] = $items['quantity'] * (floatval($items['price']) - floatval($cp));
			$profit = $profit + ($items['quantity'] *(floatval($items['price']) - floatval($cp)));
			$total_items = $total_items + $items['quantity'];
		}
		$form_data['profit']=$profit;
		$form_data['total_items']=$total_items;
		
		if (!class_exists('Sales_model')) {
			$this->load->model('sales_model');
		}
		if ($cart == 'sales_cart' || $cart == 'credit_memo') {
			$sales = $this->sales_model->save_sales($cart,$form_data);
		}else{
			$sales = $this->purchases_model->save_purchase($cart,$form_data);
		}
		if ($sales) {
			$this->set_message('transaction_saved');
			return TRUE;
		}
		$this->set_error($this->sales_model->errors());
		return FALSE;
	}
	
	function update_transaction($cart , $save='saveclose'){
		$cart_contents =  $this->get_cart($cart);
		if (empty($cart_contents['items'])) {
			$return['error'] = $this->set_error('empty_sales_not_allowed');
			return FALSE;
		}
		if ($cart_contents['people']['id'] == '') {
			$return['error'] = $this->set_error('empty_sales_people_not_allowed');
			return FALSE;
		}
		
		$form_data = $cart_contents;
		$form_data['amount']=$cart_contents['subtotal'];
		$form_data['staff']=$this->session->userdata('user_id');
		$form_data['transaction_type']=config_item('transaction_type')[$cart];
		$profit =0;
		$total_items =0;
		foreach ($cart_contents['items'] as $key=>$items) {
			$cp = $this->product_model->get_info($items['item_id'])->cost_price;
			$form_data['items'][$key]['product_id'] = $items['item_id'];
			$form_data['items'][$key]['qty'] = $items['quantity'];
			$form_data['items'][$key]['price'] = $items['price'];
			$form_data['items'][$key]['cost_price'] = $cp;
			$form_data['items'][$key]['staff'] = $this->session->userdata('user_id');
			$form_data['items'][$key]['profit'] = $items['quantity'] * (floatval($items['price']) - floatval($cp));
			$profit = $profit + ($items['quantity'] *(floatval($items['price']) - floatval($cp)));
			$total_items = $total_items + $items['quantity'];
		}
		$form_data['profit']=$profit;
		$form_data['total_items']=$total_items;
		
		if (!class_exists('Sales_model')) {
			$this->load->model('sales_model');
		}
		if ($cart == 'sales_cart' || $cart == 'credit_memo') {
			$sales = $this->sales_model->update_sales($cart,$form_data);
		} else {
			$sales = $this->purchases_model->update_purchase($cart,$form_data);
		}
		if ($sales) {
			$this->set_message('transaction_saved');
			return TRUE;
		}
		$this->set_error($this->sales_model->errors());
		return FALSE;
	}
	public function set_message($message)
	{
		$this->_messages[] = $message;

		return $message;
	}
	
	public function messages()
	{
		$_output = '';
		foreach ($this->_messages as $message)
		{
			$messageLang = lang($message) ? lang($message) : '##' . $message . '##';
			$_output .=  $messageLang;
		}

		return $_output;
	}

	
	public function set_error($error)
	{
		$this->_errors[] = $error;

		return $error;
	}
	
	public function errors()
	{
		$_output = '';
		foreach ($this->_errors as $error)
		{
			$errorLang = $this->lang->line($error) ? $this->lang->line($error) : '##' . $error . '##';
			$_output .=  $errorLang;
		}

		return $_output;
	}
}