<?php

class Sales_model {

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

	function get($table = 'transaction_header')
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
	
	function get_all()
	{
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
	
	function all_sales_transaction($limit,$offset){
		return $this->db
		->group_start()
		->where('transaction_type',1)
		->or_where('transaction_type',2)
		->group_end()
		->where('status', '1')
		->offset($offset)
		->limit($limit)
		->order_by('created_date',"DESC")
		->get($this->_tables['transaction_header'])->result();
	}
	
	function get_sales_range($start, $end =NULL)
	{
		
		$daily_sales_invoice = $this->db
		->select('COUNT(id) as invoice_count, SUM(amount) as sales_amount')
		->group_start()
		->where('created_date >= ',$start)
		->where('created_date <= ' , $end)
		->group_end()
		->where('transaction_type',1)
		->get($this->_tables['transaction_header'])->row();
		
		return($daily_sales_invoice);
	}
	
	function recent_sales(){
		$recent_sales_invoice = $this->db
		->select('sales_no,people_id,created_date,amount,id')
		->where('transaction_type',1)
		->limit(5)
		->order_by('created_date','DESC')
		->get($this->_tables['transaction_header'])->result();
		$this->load->model('people_model');
		$sales_amount=0;
		if (count($recent_sales_invoice)>0) {
			foreach ($recent_sales_invoice as $sales) {
				$sales->name = $this->people_model->get_a_person($sales->people_id)->row()->name;
				$sales->name = humanize($sales->name,'-');
				$sales->date = my_full_time_span($sales->created_date);
				$sales_amount = $sales->amount + $sales_amount;
				$sales->amount = my_number_format($sales->amount,1,1);
			}
			
			$return['recent_sales_invoice'] = $recent_sales_invoice;
		}else{
			$return['recent_sales_invoice'] = NULL;
		}
		
		$return['sales_amount'] = my_number_format($sales_amount,1,1);
		$return['month'] = date('F Y',time());
		
		return $return;
	}
	
	function get_a_sales_transaction_by_slaes_no($id= NULL)
	{
	
		if ($id == null) {
			$this->set_error('error_transaction_not_found');
			return FALSE;
		}

		$this->db->where(['sales_no'=>$id]);

		$this->db->get($this->_tables['transaction_header']);
		
		return $this;
	}
	
	public function get_a_product_sales_transactions($product_id=NULL){
		if ($product_id == NULL) {
			return [];
		}
		
		$query_transaction = $this->db->where(['product_id'=>$product_id])->get($this->_tables['transaction_details']);
//		exit('ddd');
		
		if ($query_transaction->num_rows() >0) {
			return $query_transaction->result();
		}else{
			return [];
		}
	}
	
	function get_a_sales_transaction($id= NULL){
	
		if ($id == null) {
			$this->set_error('error_transaction_not_found');
			return FALSE;
		}

//		$this->db->limit(1);
//		$this->db->order_by('ids','DESC');
		$this->db->where(['id'=>$id]);

		return $this->db->get($this->_tables['transaction_header'])->row();
	}
	
	function get_a_sales_transaction_items($transaction_id= NULL){
	
		if ($transaction_id == null) {
			$this->set_error('error_transaction_not_found');
			return FALSE;
		}

		$this->db->order_by('id','DESC');
		$this->db->where(['transaction_id'=>$transaction_id]);

		$items = $this->db->get($this->_tables['transaction_details'])->result();
		
		
//		print_r($items);exit;
		return $items;
	}
	
	function total_rows(){
		return $this->db->select('1',FALSE)
		->group_start()
		->where('transaction_type',2)
		->or_where('transaction_type',3)
		->group_end()
		->where('status', '1')
		->count_all($this->_tables['transaction_header']);
	}
	
	
	public function save_sales($cart,$form_data=[]){
		$success_1 =false;
		$success_2 = false;
		$success_3 = false;
		$success_4 = false;
 		$success_5 =false;
		
		if (empty($form_data)) {
			return FALSE;
		}
		
		foreach ($form_data as $key => $dirty_dirty) {
			if (!is_array($dirty_dirty)) {
				$form_data[$key] = html_entity_decode(cleanString($dirty_dirty));
			}
		}
		
		if (!class_exists('App'))
			$this->load->library('App');

		$form_data_filtered_hrd = $this->app->_filter_data($this->_tables['transaction_header'] , $form_data);
		
		//set payment to 0 if empty.
//		$form_data['payment'] = ($form_data['payment']!='')?$form_data['payment']:0;
		if ($form_data['payment'] == '') {
			$form_data['payment'] = 0;
			$success_3 = 1;
		}
		$this->db->trans_begin();
		
		$this->db->insert($this->_tables['transaction_header'],$form_data_filtered_hrd);
		$id = $this->db->insert_id($this->_tables['transaction_header']);
		
		
//		update people activity
//		update people metadata
		
//		insert into transaction details
		$form_data_filtered_trans_details =[];
		
		$trans_details = $form_data['items'];
		
		foreach ($trans_details as $key => $vals) {
			$trans_details[$key]['transaction_id'] = $id;
			
			//filter off the fileds that are not in the table columbs
			$form_data_filtered_trans_details[$key] = $this->app->_filter_data($this->_tables['transaction_details'] ,$trans_details[$key]);
		}

		
		
		foreach ($form_data_filtered_trans_details as $trans_detail) {
			if ($form_data['transaction_type'] == 1) {
			$qty = ['qty'=>$this->product_model->get_info($trans_detail['product_id'])->qty - $trans_detail['qty']];
			
		}elseif($form_data['transaction_type'] == 2){
			$qty = ['qty'=>$this->product_model->get_info($trans_detail['product_id'])->qty + $trans_detail['qty']];
					
		}
			//update product metadata
			$success_2=$this->product_model->update_product_metadata($trans_detail['product_id'],$qty);
			
			
		}
		
		//add Payments
		if ($form_data['transaction_type'] == '2') {
			$form_data['payment'] = $form_data['amount'];
		}
		if ($form_data['payment'] !== '0' ) {
			$activity['created_date'] = $form_data['created_date'];
			$activity['staff'] = $form_data['staff'];
			$activity['payment'] = $form_data['payment'];
			$activity['transaction_type'] = $form_data['transaction_type'];
			$activity['transaction_id'] = $id;
			$success_3 = $this->people_model->update_people_activity($form_data['people_id'] , $activity);
		}
		$success_4 = $this->people_model->update_people_metadata($form_data['people_id'] , $form_data);
		
		$success_5 = $this->db->insert_batch($this->_tables['transaction_details'],$form_data_filtered_trans_details);
		if (!$success_2 || ($form_data['payment'] !== '0' && !$success_3) || !$success_4 || !$success_5) {
			
			$this->db->trans_rollback();
			$this->set_error('transaction_error');
			return FALSE;
		}
		if (!$this->db->trans_status()) {
			$this->db->trans_rollback();
			return FALSE;
		}
		
		$this->db->trans_commit();
		return TRUE;
	}
	
	public function update_sales($cart,$form_data=[]){

		$success_2 = false;
		$success_3 = false;
		$success_4 = false;
 		$success_5 =false;
		
		if (empty($form_data)) {
			return FALSE;
		}
		
		foreach ($form_data as $key => $dirty_dirty) {
			if (!is_array($dirty_dirty)) {
				$form_data[$key] = html_entity_decode(cleanString($dirty_dirty));
			}
		}
//		print_r($form_data);exit;
		$prev_trans = $this->get_a_sales_transaction($form_data['sales_id']);
		
		if (!class_exists('App'))
			$this->load->library('App');

		$form_data_filtered_hrd = $this->app->_filter_data($this->_tables['transaction_header'] , $form_data);
		
		
		$this->db->trans_begin();
//		print_r($form_data);exit;
		//update the transaction header first;
		$this->db->where('id',$form_data['sales_id'])->update($this->_tables['transaction_header'],$form_data_filtered_hrd);
		$id = $form_data['sales_id'];
		
		$form_data_filtered_trans_details =[];
		
		$trans_details = $form_data['items'];
		
		foreach ($trans_details as $key => $vals) {
			$trans_details[$key]['transaction_id'] = $id;
			
			//filter off the fileds that ar not in the table columbs
			$form_data_filtered_trans_details[$key] = $this->app->_filter_data($this->_tables['transaction_details'] ,$trans_details[$key]);
		}
		
		//update the product Qty before removeing products from the transaction details table
		foreach ($this->get_a_sales_transaction_items($form_data['sales_id']) as $old_trans_detail) {

			if ($form_data['transaction_type'] == 2) {
				$qty = ['qty'=>$this->product_model->get_info($old_trans_detail->product_id)->qty - $old_trans_detail->qty];
			} else {
				$qty = ['qty'=>$this->product_model->get_info($old_trans_detail->product_id)->qty + $old_trans_detail->qty];
			}
			$success_2=$this->product_model->update_product_metadata($old_trans_detail->product_id,$qty);
			
		}
		
		//remove products from the transaction detail table.
		$this->db->where('transaction_id',$id)->delete($this->_tables['transaction_details']);
		
		//update the people metadata for the previous sales amount recorded
		$bal = $this->people_model->where(['people_id'=>$prev_trans->people_id])->get_person_metadata()->row()->balance;
		if ($form_data['transaction_type'] == 2) {
			$this->db->where('people_id',$prev_trans->people_id)->update($this->_tables['people_metadata'],['last_update_date'=>time(),'balance'=>($bal + $prev_trans->amount)]);
		} else {
			$this->db->where('people_id',$prev_trans->people_id)->update($this->_tables['people_metadata'],['last_update_date'=>time(),'balance'=>($bal - $prev_trans->amount)]);
		}
		
		
		//delete the payment from people activity if the transaction type is credit Memo
		$this->db->where('transaction_id',$form_data['sales_id'])->delete($this->_tables['people_activity']);
		foreach ($form_data_filtered_trans_details as $trans_detail) {
			$qty = ['qty'=>$this->product_model->get_info($trans_detail['product_id'])->qty - $trans_detail['qty']];

			$success_2=$this->product_model->update_product_metadata($trans_detail['product_id'],$qty);

		}

		//add Payments if it is credit memo
		if ($form_data['transaction_type'] == '2') {
			$form_data['payment'] = $form_data['amount'];
			$activity['created_date'] = $form_data['created_date'];
			$activity['staff'] = $form_data['staff'];
			$activity['payment'] = $form_data['payment'];
			$activity['transaction_type'] = $form_data['transaction_type'];
			$activity['transaction_id'] = $form_data['sales_id'];
			$success_3 = $this->people_model->update_people_activity($form_data['people']['id'] , $activity);
		} else {
			$success_3 =1;
		}
		
		$success_4 = $this->people_model->update_people_metadata($form_data['people']['id'] , $form_data);

		$success_5 = $this->db->insert_batch($this->_tables['transaction_details'],$form_data_filtered_trans_details);
		if (!$success_2 || !$success_3 || !$success_4 || !$success_5) {

			$this->db->trans_rollback();
			$this->set_error('transaction_error');
			return FALSE;
		}
		
		
		if (!$this->db->trans_status()) {
			$this->db->trans_rollback();
			return FALSE;
		}
		
		$this->db->trans_commit();
		return TRUE;
	}
	
	public function delete_sales($invoice_id){
		
		$invoice_details = $this->get_a_sales_transaction_items($invoice_id);
		$invoice_header = $this->get_a_sales_transaction($invoice_id);
		$invoice_amount = $invoice_header->amount;
		$invoice_people = $invoice_header->people_id;
//		print_r($invoice_people);exit;
		
		$this->db->trans_begin();
		$this->db->where('id',$invoice_id)->delete($this->_tables['transaction_header']);
		
		//update the product Qty before removeing products from the transaction details table
		foreach ($this->get_a_sales_transaction_items($invoice_id) as $trans_detail) {
			if ($invoice_header->transaction_type == 2) {
				$qty = ['qty'=>$this->product_model->get_info($trans_detail->product_id)->qty - $trans_detail->qty];
			}else{
				$qty = ['qty'=>$this->product_model->get_info($trans_detail->product_id)->qty + $trans_detail->qty];
			}
			$success_2=$this->product_model->update_product_metadata($trans_detail->product_id,$qty);

		}
		
		//update the people metadata for the previous sales amount recorded
		$bal = $this->people_model->where(['people_id'=>$invoice_people])->get_person_metadata()->row()->balance;
		if ($invoice_header->transaction_type == 2) {
			$this->db->where('people_id',$invoice_people)->update($this->_tables['people_metadata'],['last_update_date'=>time(),'balance'=>($bal + $invoice_amount)]);
		}else{
			$this->db->where('people_id',$invoice_people)->update($this->_tables['people_metadata'],['last_update_date'=>time(),'balance'=>($bal - $invoice_amount)]);
		}
		if($invoice_header->transaction_type == 2){
			//delete the payment from people activity if the transaction type is credit Memo
			$this->db->where('transaction_id',$invoice_id)->delete($this->_tables['people_activity']);	
		}
		//remove products from the transaction detail table.
		$this->db->where('transaction_id',$invoice_id)->delete($this->_tables['transaction_details']);

		if (!$this->db->trans_status()) {
			$this->db->trans_rollback();
			return FALSE;
		}

		$this->db->trans_commit();
		return TRUE;
	}
	
	function generate_sales_report(){
		$date = $this->session->userdata('range');
		$date = explode('_',$date);
		$start_start = $date[0];
		$start_end = $date[1];
		
		$this->db->select('sales_no,people_id,total_items,staff,created_date,profit,amount,transaction_type,id');
		$this->db->where(['status'=>1,'transaction_type'=>1]);
		$this->db->group_start();
		$this->db->where('created_date >=',$start_start);
		$this->db->where('created_date <=',$start_end);
		$this->db->group_end();
		$query = $this->db->get($this->_tables['transaction_header']);
		
		if ($query->num_rows()!==0) {
			$results = $query->result(); 
		}else{
			$results = NULL;
		}
		
		return($results);
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

}