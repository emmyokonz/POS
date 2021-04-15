<?php
defined('BASEPATH') or exit('No direct access to script is allowed');

class Product_model extends CI_Model {
	

	private $_tables = [];
	private $_where = [];
	private $_or_where = [];
	private $_select =[];
	private $_like =[];
	private $_or_like =[];
	private $_limit;
	private $_offset;
	private $_order;
	private $_order_by;
	private $_query_result=[];
	private $_errors =[];
	private $_messages = [];
	private $_output = '';

	function __construct()
	{
		$this->_tables = config_item('tables');
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

	function get($table = 'item_header')
	{
		return $this->db->get($table);
	}

/**
* @param string $by
* @param string $order
*
* @return static
*/
	public function order_by($by, $order='desc')
	{

		$this->_order_by = $by;
		$this->_order    = $order;

		return $this;
	}

/**
* @param int $limit
*
* @return static
*/
	public function limit($limit)
	{
		$this->_limit = $limit;

		return $this;
	}

/**
* @param string      $like
* @param string|null $value
* @param string      $position
*
* @return static
*/
	public function like($like, $value = NULL, $position = 'both')
	{

		array_push($this->_like, [
			'like'     => $like,
			'value'    => $value,
			'position' => $position
		]);

		return $this;
	}

/**
* @param string      $like
* @param string|null $value
* @param string      $position
*
* @return static
*/
	public function or_like($like, $value = NULL, $position = 'both')
	{

		array_push($this->_or_like, [
			'like'     => $like,
			'value'    => $value,
			'position' => $position
		]);

		return $this;
	}

/**
* @param int $offset
*
* @return static
*/

	public function offset($offset)
	{
		$this->_offset = $offset;

		return $this;
	}

/**
* @param array|string $select
*
* @return static
*/
	public function select($select)
	{
		$this->_select[] = $select;

		return $this;
	}


	public function where($where , $value = NULL)
	{
		if (!is_array($where)) {
			$where = [$where => $value];
		}

		array_push($this->_where , $where);

		return $this;
	}

	public function or_where($or_where , $value = NULL)
	{
		if (!is_array($or_where)) {
			$or_where = [$or_where => $value];
		}

		array_push($this->_or_where , $or_where);

		return $this;
	}

/**
* @return object|mixed
*/
	public function row()
	{

		$row = $this->_query_result->row();

		return $row;
	}

	function result()
	{
		$result = $this->_query_result->result();
		return($result);
	}

	function result_array()
	{
		$result = $this->_query_result->result_array();
		return($result);
	}

	function num_rows()
	{
		$num_rows = $this->_query_result->num_rows();
		return $num_rows;
	}

	function get_product($id = NULL)
	{
		if ($id == null) {
			$this->set_error('error_product_not_found');
			return FALSE;
		}

		$this->limit(1);
		$this->order_by($this->_tables['products'].'.id','DESC');
		$this->where([$this->_tables['products'].'.id'=>$id]);

		$this->get_products();

		return $this;
	}

	function product($id = NULL)
	{

		$this->limit(1);

		if (isset($this->_select) && !empty($this->_select)) {
			foreach ($this->_select as $select) {
				$this->db->select($select);
			}

			$this->_select = [];
		}

		// run each where that was passed
		if (isset($this->_where) && !empty($this->_where)) {
			foreach ($this->_where as $where) {
				$this->db->where($where);
			}

			$this->_where = [];
		}

		// set the order
		if (isset($this->_order_by) && isset($this->_order)) {
			$this->db->order_by($this->_order_by, $this->_order);

			$this->_order    = NULL;
			$this->_order_by = NULL;
		}

		$this->get_products();

		return $this;
	}

	function get_products()
	{
		if (isset($this->_select) && !empty($this->_select)) {
			foreach ($this->_select as $select) {
				$this->db->select($select);
			}

			$this->_select = [];
		}

		// run each where that was passed
		if (isset($this->_where) && !empty($this->_where)) {
			//			if(!is_null($product_type)||!$product_type)array_merge($this->_where , ['product_type'=>$product_type]);

			foreach ($this->_where as $where) {
				$this->db->where($where);
			}

			$this->_where = [];
		}

		if (isset($this->_like) && !empty($this->_like)) {
			foreach ($this->_like as $like) {
				$this->db->like($like['like'], $like['value'], $like['position']);
			}

			$this->_like = [];
		}

		if (isset($this->_or_like) && !empty($this->_or_like)) {
			foreach ($this->_or_like as $like) {
				$this->db->or_like($like['like'], $like['value'], $like['position']);
			}

			$this->_or_like = [];
		}

		// set the order
		if (isset($this->_order_by) && isset($this->_order)) {
			$this->db->order_by($this->_order_by, $this->_order);

			$this->_order    = NULL;
			$this->_order_by = NULL;
		}


		if (isset($this->_limit) && isset($this->_offset)) {
			$this->db->limit($this->_limit, $this->_offset);

			$this->_limit  = NULL;
			$this->_offset = NULL;
		} /*else if (isset($this->_limit)) {
	$this->db->limit($this->_limit);

	$this->_limit  = NULL;
	}*/

		$this->_query_result = $this->get();

		return $this;
	}

	function get_all()
	{
		$this->get_products();

		return $this;
	}
	
	function get_product_metadata()
	{
		$this->limit(1);

		if (isset($this->_select) && !empty($this->_select)) {
			foreach ($this->_select as $select) {
				$this->db->select($select);
			}

			$this->_select = [];
		}

		// run each where that was passed
		if (isset($this->_where) && !empty($this->_where)) {

			foreach ($this->_where as $where) {
				$this->db->where($where);
			}

			$this->_where = [];
		}

		$this->_query_result = $this->db->get($this->_tables['product_metadata']);

		return $this;
	}
	
	function get_item_search_suggestions($search, $limit)
	{
		$limit = isset($limit)? $limit : 25;
		$suggestions = array();

		$this->db->from($this->_tables['products']);
		$this->db->where("(name LIKE '%".$this->db->escape_like_str($search)."%' or
		suk LIKE '%".$this->db->escape_like_str($search)."%') and status=1 and item_type = 1");
		$this->db->order_by("name", "asc");
		$this->db->limit($limit);
		$by_name = $this->db->get();
		foreach ($by_name->result() as $row) {
			$suggestions[]=$row;
		}
		return $suggestions;
	}
	

	function product_exits($product_id)
	{
		$query = $this->select("1",FALSE)
		->where('id',$product_id)
		->where('item_type' , '1')
		->get_products($product_id);
		return ($query->num_rows() == 1);
	}

	/*
	Get an item id given an item number
	*/
	function get_item_id($product_suk)
	{
		$query = $this->select("id")
		->where('suk',$product_suk)
		->where('item_type' , '1')
		->get_products();

		if ($query->num_rows()==1) {
			return $query->row()->id;
		}

		return false;
	}


	/*
	Gets information about a particular item
	*/
	function get_info($item_id)
	{
		$this->where('id',$item_id)->limit(1);
		$select = $this->_tables['products'].'.name as name,'
		.$this->_tables['products'].'.suk as product_no,'
		.$this->_tables['products'].'.category_id as category,'
		.$this->_tables['products'].'.status as status,'
		.$this->_tables['products'].'.metadata as metadata,'
		.$this->_tables['products'].'.description as description,'
		.$this->_tables['product_metadata'].'.qty as qty,'
		.$this->_tables['product_metadata'].'.cost_price as cost_price,'
		.$this->_tables['product_metadata'].'.selling_price as selling_price,';
		
		$where= [
		$this->_tables['products'].'.id'=>$item_id,
		$this->_tables['products'].'.item_type'=>1,
		];
		
		$join = $this->_tables['products'].".id = ".$this->_tables['product_metadata'].".item_id";

		$sql_query = $this->db->select($select)
		->where($where)
		->join($this->_tables['products'] , $join);
		
		$query = $sql_query->get($this->_tables['product_metadata']);

		if ($query->num_rows()==1) {
			return $query->row();
		} else {
			//Get empty base parent object, as $item_id is NOT an item
			$item_obj=new stdClass();

			//Get all the fields from items table
			$fields = $sql_query->list_fields($this->_tables['products']);

			foreach ($fields as $field) {
				$item_obj->$field='';
			}

			return $item_obj;
		}
	}
	function stock_list()
	{
		$select = $this->_tables['products'].'.name as name,'.$this->_tables['products'].'.id as id,'
		.$this->_tables['product_metadata'].'.qty as qty,';
		
		$where= [
		$this->_tables['products'].'.item_type'=>1,
		];
		
		$join = $this->_tables['products'].".id = ".$this->_tables['product_metadata'].".item_id";

		$sql_query = $this->db->select($select)
		->where($where)
		->join($this->_tables['product_metadata'] , $join)
		->order_by($this->_tables['product_metadata'].'.qty','ASC')
		->limit(8);
		
		$query = $sql_query->get($this->_tables['products']);

		if ($query->num_rows()>=1) {
			$stock_list = $query->result();
			
			foreach ($stock_list as $result) {
				$result->name = humanize($result->name,'-');
				if ($result->qty <= config_item('reordering_point')) {
					$result->qty = html_tag('i',['class'=>'text-danger'],$result->qty);
					$result->name =html_tag('i',['class'=>'text-danger'],$result->name);
				}
			}
		}else{
			$stock_list = NULL;
		}
		return $stock_list;
	}
	
	function report_per_product($product, $start_start, $start_end)
	{
		if (!class_exists('sales_model')) {
			$this->load->model('sales_model');
		}
		if (!class_exists('people_model')) {
			$this->load->model('people_model');
		}

		$select = $this->_tables['people'].'.name as customer,'.
		$this->_tables['transaction_header'].'.sales_no as transaction_no,'.
		$this->_tables['product_metadata'].'.qty as qty_at_hand,'.
		$this->_tables['transaction_header'].'.id as transaction_id,'.
		$this->_tables['transaction_header'].'.created_date as date,'.
		$this->_tables['transaction_header'].'.transaction_type as type,'.
		$this->_tables['transaction_details'].'.price as price,'.
		$this->_tables['transaction_details'].'.qty as quantity,'.
		$this->_tables['transaction_details'].'.profit as profit,';
		
		$where = [
			$this->_tables['transaction_header'].'.created_date >='=>$start_start,
			$this->_tables['transaction_header'].'.created_date <='=>$start_end,
			$this->_tables['transaction_details'].'.product_id'=>$product,
		];

		$join2 = $this->_tables['product_metadata'].'.item_id = '.$this->_tables['transaction_details'].'.product_id';
		$join1 = $this->_tables['people'].'.id = '.$this->_tables['transaction_header'].'.people_id';
		$join3 = $this->_tables['transaction_header'].'.id = '.$this->_tables['transaction_details'].'.transaction_id';

		$result = $this->db->select($select)
		->group_start()
		->where($where)
		->group_end()
		->join($this->_tables['transaction_header'] , $join3)
		->join($this->_tables['product_metadata'] , $join2)
		->join($this->_tables['people'] , $join1)
		->order_by($this->_tables['transaction_header'].'.created_date','ASC')
		->get($this->_tables['transaction_details']. ' ');

		$product_sales = $result->result();


		if (count($product_sales) > 0) {
			$balance = 0;
			$sum_qty_in_transaction = 0;
			$qty_at_hand = 0;
			$amount = 0;
			foreach ($product_sales as $sales) {
//				$product_transaction_details = $this->sales_model->get_a_sales_transaction($sales->transaction_id);
				
				if ($sales->type == 1 || $sales->type == 4) {
					if ($sales->type == 1) {
						$amount = $amount + ($sales->price * $sales->quantity);
					}
					$sales->quantity = '-'.$sales->quantity;
				}
				$sales->amount = $amount;
				$sales->invoice_no = anchor_popup('preview/transaction/'.$sales->transaction_id,$sales->transaction_no);
				$sales->customer = humanize($sales->customer,'-');
				$sales->date = my_full_time_span($sales->date);
				$balance = $sales->qty_at_hand - ($sales->quantity + $balance);
				if ($sales->quantity > 0) {
					$sales->quantity_in = $sales->quantity;
					$sales->quantity_out = "0";
				}else{
					$sales->quantity_in = "0";
					$sales->quantity_out = $sales->quantity;
				}
				$sales->type = config_item('transaction_key')[$sales->type];
				$sum_qty_in_transaction = $sum_qty_in_transaction + $sales->quantity_in + $sales->quantity_out;
				$qty_at_hand = $sales->qty_at_hand;

			}
			
			return(['product_details'=>$product_sales,'qty_at_hand'=>$qty_at_hand,'sold_balance'=>$sum_qty_in_transaction]);
		} else {
			return( NULL);
		}
	}
	
	function save($form_data, $product_type = 1)
	{
//		purify data
		foreach ($form_data as $key => $dirty_dirty) {
			if ($key == 'name') {
				$form_data[$key] = html_entity_decode(cleanString($dirty_dirty,TRUE,"-"));
			}else{
				$form_data[$key] = html_entity_decode(cleanString($dirty_dirty));
			}
			
		}

		if ($this->_title_check($form_data['name'] ,$product_type)) {
			$this->set_error('product_creation_duplicate_title');
			return FALSE;
		}
		
		if (!class_exists('App'))
			$this->load->library('App');

		$form_data_filtered_hrd = $this->app->_filter_data($this->_tables['products'] , $form_data);

		$data_hrd =[
			'created_date' 	=> time(),
			'status' 		=> 1,
			'staff' 		=> $this->session->userdata('user_id'),
			'item_type' 	=> $product_type,

		];
		
		//create suk for only Products
		if ($product_type == 1) {
			$data_hrd['suk'] = generate_unique_product_code($this->_tables['products'],'suk',6);
		}
		
		$this->db->trans_begin();
		
		$data_hrd = array_merge($form_data_filtered_hrd , $data_hrd);
		$this->db->insert($this->_tables['products'],$data_hrd);

		$id = $this->db->insert_id($this->_tables['products']);

		if ($product_type == 1) {
		
			$data_metadata['item_id'] = $id;
			
			//filter off the fileds that ar not in the table columbs
			$form_data_filtered_metadata = $this->app->_filter_data($this->_tables['product_metadata'] , $form_data);
			
			$data_metadata = array_merge($form_data_filtered_metadata , $data_metadata);
			
			$this->db->insert($this->_tables['product_metadata'],$data_metadata);
			
			//lets insert into Transaction_model for opening qty.
			/*
			if ($form_data['qty'] !== '0' || $form_data['qty'] !== '') {
				$t_details_data=[
					"product_id" => $data_metadata['item_id'],
					"transaction_id" => NULL,
					"qty" => $form_data['qty'],
					"price" => $form_data['selling_price'],
					"cost_price" => $form_data['cost_price'],
					"staff" => $data_hrd['staff'],
				];
				$this->db->insert($this->_tables['transaction_details'],$data);
			}*/
		}

		if ($this->db->trans_status() == TRUE) {

			$this->db->trans_commit();
			return $id;
		}
		
		$this->db->trans_rollback();
		return FALSE;
	}

	function update($id, $form_data , $product_type)
	{
		$product = $this->get_product($id)->row();
		//updating product title?
		if (array_key_exists('name', $form_data) && $this->_title_check($form_data['name'],$product->item_type) && $product->name !== $form_data['name']) {
			$this->set_error('product_creation_duplicate_title');

			$this->set_error('product_update_unsuccessful');

			return FALSE;
		}

		//		purify data
		foreach ($form_data as $key => $dirty_dirty) {
			if ($key == 'name') {
				
				$form_data[$key] = html_entity_decode(cleanString($dirty_dirty,TRUE,"-"));
			} else {
				$form_data[$key] = html_entity_decode(cleanString($dirty_dirty));
			}

		}
		
		// Filter the data passed
		$form_data_filtered_hrd = $this->app->_filter_data($this->_tables['products'] , $form_data);
//		print_r($form_data);exit;
		

		$this->db->trans_begin();
		
		$this->save_product_changes($id,$form_data,$product_type);

		if ($product_type == 1) {
			
			//lets update the selling price and the cost price.
			//filter off the fileds that ar not in the table columbs
			$form_data_filtered_metadata = $this->app->_filter_data($this->_tables['product_metadata'] , $form_data);

			$this->db->where('item_id',$id)->update($this->_tables['product_metadata'],$form_data_filtered_metadata);
		}
		
		$this->db->where('id',$id)->update($this->_tables['products'], $form_data_filtered_hrd);
		
		if ($this->db->trans_status() === FALSE) {
			exit;
			$this->db->trans_rollback();

			$this->set_error('product_update_unsuccessful');
			return FALSE;
		}

		$this->db->trans_commit();
		
		$this->set_message('product_update_successful');
		log_activity($this->session->userdata('user_id'),'updated product ##'.$id);

		return TRUE;
	}
	
	function update_product_metadata($id = NULL, $form_data )
	{
		if ($id == NULL) {
			return FALSE;
		}
		
		//		purify data
		foreach ($form_data as $key => $dirty_dirty) {
			$form_data[$key] = my_number_format(html_entity_decode(cleanString($dirty_dirty)),0);
		}
		
		// Filter the data passed
		$form_data_filtered = $this->app->_filter_data($this->_tables['product_metadata'] , $form_data);
		

		$this->db->where(['item_id'=>$id]);
		
		$update = $this->db->update($this->_tables['product_metadata'], $form_data_filtered);

		if (!$update) {
			log_activity($this->session->userdata('user_id'),'updated product metadata error##'.$id);
			return FALSE;
		}
		
		log_activity($this->session->userdata('user_id'),'updated product metadata ##'.$id);
		return TRUE;
	}
	
	public function update_product_cost_price($id=NULL,$cost_price,$price){
		
		if ($id == NULL) {
			return FALSE;
		}

		//		purify data
		$cost_price = my_number_format(html_entity_decode(cleanString($cost_price)),0);
		$price = my_number_format(html_entity_decode(cleanString($price)),0);
		
		if ($price !== $cost_price) {
			$update = $this->db->where('item_id',$id)->update($this->_tables['product_metadata'] , ['cost_price'=>$price]);
		}else{
			return(TRUE);
		}
		
		if (isset($update)) {

			log_activity($this->session->userdata('user_id'),'updated product cost price ##'.$id);
			return TRUE;}

		return FALSE;
	}

	public function delete($id =NULL)
	{
		if ($id == NULL) {
			$this->set_error('product_delect_no_id');
			return FALSE;
		}
		$found = FALSE;
		
		// check if its a category
		$isCategory = $this->select('1',FALSE)->where(['item_type'=>2,'id'=>$id])->product()->num_rows();
		$metadata = $this->get_product($id)->row()->metadata;
		
		if ($isCategory == TRUE) {
			//check if it is assigned to a product
			$found = $this->select('1',false)->where(['category_id'=>$id])->product()->num_rows();
		}else{
//			exit;
			//its a product lets check if its has been transacted on.
			$found = $this->db->select('1',false)->where(['product_id'=>$id,'transaction_id <>'=>NULL])->get($this->_tables['transaction_details'])->num_rows();

		}
		
		$this->db->trans_begin();
		
		if (!$found) {

			$this->db->where(['id'=>$id])->delete($this->_tables['products']);
			$this->db->where(['item_id'=>$id])->delete($this->_tables['product_metadata']);
			$this->db->where(['item_id'=>$id])->delete($this->_tables['transaction_details']);
			$this->set_message('product_delete_successful');
			log_activity($this->session->userdata('user_id'),'deleted a product #'.$id);
		}else{

			
			$data_hrd['status'] = 0;
			//updating the metadata for the item for delete
			$data_hrd['metadata']=[
				'action_by'=>$this->session->userdata('user_id'),
				'action_time'=>time(),
				'action_ip'=>$this->input->ip_address()];

			$data_hrd['metadata']['action']="Deactivation";
			$data_hrd['metadata']['action_details']=[];
			$data_hrd['metadata'] = json_encode($data_hrd['metadata']);

			if ($metadata !== NULL) {
				$data_hrd['metadata'] = $metadata.','.$data_hrd['metadata'];
			}

			$this->db->where(['id'=>$id])->update($this->_tables['products'],['status'=>0]);
			$this->set_message('product_deactivated_successful');
			
		}

		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			$this->set_error('product_delete_unsuccessful');
			return FALSE;
		}

		$this->db->trans_commit();

		return TRUE;
	}
	
	function activate($id=NULL)
	{
	
		if ($id == NULL) {
			$this->set_error(sprintf(lang('record_not_exist'),'item'));
			return FALSE;
		}

		$metadata = $this->get_product($id)->row()->metadata;

		$this->db->trans_begin();
		$data['status'] = 1;
		$data['metadata']=[
			'action_by'=>$this->session->userdata('user_id'),
			'action_time'=>time(),
			'action_ip'=>$this->input->ip_address(),
			'action'=>'activation',
			'action_deatils'=>[]
		];
		$data['metadata'] = json_encode($data['metadata']);
		if ($metadata !== NULL) {
			$data['metadata'] = $metadata.','.$data['metadata'];
		}
			
		$this->db->where('id',$id)->update($this->_tables['products'], $data);

		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			$this->set_error('product_publish_unsuccessful');
			return FALSE;
		}

		$this->db->trans_commit();
		log_activity($this->session->userdata('user_id'),'published product #'.$id);
		$this->set_message('product_activated_successful');
		return TRUE;
	}
	
	function item_type($id = NULL)
	{
		return $this->get_product($id)->row()->item_type;
	}

	/*
	Determines if a given item_id is an item
	*/
	function exists($item_id)
	{
		
		$this->db->from($this->_tables['products']);
		$this->db->where('id',$item_id);
		$query = $this->db->get();

		return ($query->num_rows()==1);

	}
	
	function generate_physical_stock(){
		
		$select = $this->_tables['products'].'.name as name,'.
					$this->_tables['products'].'.suk as product_number,'.
					$this->_tables['products'].'.status as status,'.
					$this->_tables['products'].'.id as product_id,'.
					$this->_tables['product_metadata'].'.qty as quantity';
		$where = ['item_type'=>1];
		$join = $this->_tables['product_metadata'].'.item_id = '.$this->_tables['products'].'.id';
		
		$result = $this->db->select($select)
		->where($where)
		->join($this->_tables['product_metadata'] , $join)
		->order_by($this->_tables['products'].'.name','ASC')
		->get($this->_tables['products']);
		
		if ($result->num_rows() > 0) {
			return $result->result();
		}
		
		return(NULL);
	}
	
	function generate_inventory_report()
	{
		$date = $this->session->userdata('range');
		$date = explode('_',$date);
		$start_start = $date[0];
		$start_end = $date[1];
		
		$select = $this->_tables['products'].'.name as name,'.
					$this->_tables['products'].'.id as id,'.
					$this->_tables['product_metadata'].'.qty as quantity,'.
					'SUM('.$this->_tables['transaction_details'].'.qty) as qty_sold,'.
					'AVG('.$this->_tables['transaction_details'].'.price) as a_price,'.
					'AVG('.$this->_tables['product_metadata'].'.selling_price) as price,'.
					'SUM('.$this->_tables['transaction_details'].'.profit) as profit,'.
					'SUM('.$this->_tables['transaction_details'].'.qty * '.$this->_tables['transaction_details'].'.price) as amount,';
					
					
		$where = [
			$this->_tables['products'].'.item_type'=>1,
			$this->_tables['transaction_header'].'.transaction_type'=>1,
			$this->_tables['transaction_header'].'.created_date >='=>$start_start,
			$this->_tables['transaction_header'].'.created_date <='=>$start_end
		];
					
		$join1 = $this->_tables['product_metadata'].'.item_id = '.$this->_tables['products'].'.id';
		$join2 = $this->_tables['transaction_details'].'.product_id = '.$this->_tables['products'].'.id';
		$join3 = $this->_tables['transaction_header'].'.id = '.$this->_tables['transaction_details'].'.transaction_id';
		
		$result = $this->db->select($select)
		->where($where)
		->join($this->_tables['product_metadata'] , $join1)
		->join($this->_tables['transaction_details'] , $join2)
		->join($this->_tables['transaction_header'] , $join3)
		->order_by($this->_tables['products'].'.name','ASC')
		->group_by($this->_tables['products'].'.name')
		->get($this->_tables['products']. ' ');
		
		if ($result->num_rows() > 0) {
//			echo "<pre>";print_r($result->result());exit;
			return $result->result();
		}
		
		return(NULL);
	}
	
	/**
	* check if their is changes befor updating.
	* 
	* @return boolean
	*/
	function save_product_changes($id, $form_data=[],$type)
	{
		if ($this->exists($id) && !empty($form_data)) {
			if ($type == 1) {
				$db_details = $this->get_info($id);
			}else{
				$db_details = $this->get_product($id)->row();
			}
			
			if (isset($db_details->category)) {
				$db_details->category_id=$db_details->category;
			}
			
			//get the key/value intersection of the form_data and the db data
			$qq = array_intersect_key((array)$db_details,(array)$form_data);
			/*print_r($qq);
			print_r($form_data);
			exit;*/
			$data_hrd['metadata']=[
				'action_by'=>$this->session->userdata('user_id'),
				'action_time'=>time(),
				'action_ip'=>$this->input->ip_address()];
				
				$data_hrd['metadata']['action']="Update";
				$data_hrd['metadata']['action_details']=['old_data'=>(array)$qq,'new_data'=>(array)$form_data];
				$data_hrd['metadata'] = json_encode($data_hrd['metadata']);
			
				$metadata = $db_details->metadata;
			if ($qq == $form_data) {
				return FALSE;
			} else {
				if ($metadata !== NULL) {
					$data_hrd['metadata'] = $metadata.','.$data_hrd['metadata'];
				}
				
				$this->db->where(['id'=>$id])->update($this->_tables['items'],$data_hrd);
				return(TRUE);
			}
			
		}

	}
	public function set_message($message)
	{
		$this->_messages[] = $message;

		return $message;
	}

	public function messages()
	{
		$_output = '';
		foreach ($this->_messages as $message) {
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
		foreach ($this->_errors as $error) {
			$errorLang = $this->lang->line($error) ? $this->lang->line($error) : '##' . $error . '##';
			$_output .=  $errorLang;
		}

		return $_output;
	}

/**
* title check
*
* @param $product_title string
*
* @return bool
* @author Mathew
*/
	protected function _title_check($product_title = '', $product_type)
	{

		if (empty($product_title)) {
			return FALSE;
		}

		return $this->db->where(['name'=> $product_title,'item_type'=>$product_type])
		->limit(1)
		->count_all_results($this->_tables['products']) > 0;
	}

}
