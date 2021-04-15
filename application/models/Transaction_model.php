<?php
defined('BASEPATH') or exit('No direct access to script is allowed');

class Transaction_model extends CI_Model
{


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

	function get($table = 'people_header')
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
		$this->get_people();

		return $this;
	}
	
	public function transaction_per_person($person ,$order=NULL){
		if (!class_exists('sales_model')) {
			$this->load->model('sales_model');
		}
		if (!class_exists('people_model')) {
			$this->load->model('people_model');
		}
		$people_type = $this->people_model->get_a_person($person)->row()->people_type;
		$this->db->select('created_date,transaction_type,amount,id,1 as account,1 as memo');
		$this->db->where('people_id',$person);
		$this->db->from($this->_tables['transaction_header']);
		$query1 = $this->db->get_compiled_select();
		/*
		$this->db->select('t_date as created_date,account,amount,id,1 as account');
		$this->db->where(['people_id'=>$person,'account <>'=>array_flip(config_item('transaction_type'))[2]]);
		$this->db->from($this->_tables['people_activity']);
		$query2 = $this->db->get_compiled_select();
		*/
		$this->db->select('t_date as created_date,transaction,amount,id,account_id as account,memo');
		$this->db->where(['people_id'=>$person]);
		$this->db->from($this->_tables['account_activity']);
		$query3 = $this->db->get_compiled_select();
		
		if($order == NULL){
			$sales_transaction = $this->db->query($query1. /*' UNION '. $query2.*/ ' UNION '.$query3.' order by created_date DESC')->result();
		}else{
			$sales_transaction = $this->db->query($query1. /*' UNION '. $query2.*/ ' UNION '.$query3.' order by created_date '.$order)->result();
		}
//		echo "<pre>";
//		print_r($sales_transaction);exit;
		foreach ($sales_transaction as $tran) {
			//lets check if the account ia equity the change the transaction to memo
			if ($tran->account == 3) {
				$tran->transaction_type = $tran->memo;
			}

			if ($tran->transaction_type == 2 || $tran->transaction_type == 4) {
				$tran->amount = '-'.$tran->amount;
			}
			if (($tran->transaction_type == 'deposit' || $tran->transaction_type== 6) && $people_type == 1) {
				$tran->amount = '-'.$tran->amount;
			}
			if (($tran->transaction_type == 'cheque' || $tran->transaction_type== 5) && $people_type == 2) {
				$tran->amount = '-'.$tran->amount;
			}
//			$tran->created_date = ($tran->created_date);
			if (in_array($tran->transaction_type , (config_item('transaction_type')))) {
				if (in_array($tran->transaction_type,[1,2,3,4])) {
					$tran->method = 'transaction';
				}else{
					$tran->method = 'payments';
				}
				$tran->transaction_type = humanize(array_flip(config_item('transaction_type'))[$tran->transaction_type], '_');
				if ($tran->transaction_type == 'Sales Cart') {
					$tran->transaction_type = "Invoice";
				}
			}else{
				$tran->method = 'payment';
				
			}
			
		}
//		echo '<pre>';
//		print_r($sales_transaction);exit;
		return $sales_transaction;
	}
	
	public function report_per_person($person,$start_start,$start_end){
		if (!class_exists('sales_model')) {
			$this->load->model('sales_model');
		}
		if (!class_exists('people_model')) {
			$this->load->model('people_model');
		}
		
		$people_type = $this->people_model->get_a_person($person)->row()->people_type;
		$this->db->select('created_date,transaction_type,amount,id,1 as account,1 as memo');
		$this->db->where('people_id',$person)->where('created_date >=',$start_start)
		->where('created_date <=',$start_end);
		$this->db->from($this->_tables['transaction_header']);
		$query1 = $this->db->get_compiled_select();
		
		$this->db->select('t_date as created_date,transaction,amount,id,account_id as account,memo');
		$this->db->where(['people_id'=>$person])->where('t_date >=',$start_start)
		->where('t_date <=',$start_end);
		$this->db->from($this->_tables['account_activity']);
		$query3 = $this->db->get_compiled_select();

		$sales_transaction = $this->db->query($query1.' UNION '.$query3.' order by created_date ASC')->result();
		
		foreach ($sales_transaction as $tran) {
			//lets check if the account ia equity the change the transaction to memo
			if ($tran->account == 3) {
				$tran->transaction_type = $tran->memo;
			}

			if ($tran->transaction_type == 2 || $tran->transaction_type == 4) {
				$tran->amount = '-'.$tran->amount;
			}
			if (($tran->transaction_type == 'deposit' || $tran->transaction_type== 6) && $people_type == 1) {
				$tran->amount = '-'.$tran->amount;
			}
			if (($tran->transaction_type == 'cheque' || $tran->transaction_type== 5) && $people_type == 2) {
				$tran->amount = '-'.$tran->amount;
			}
//			$tran->created_date = ($tran->created_date);
			if (in_array($tran->transaction_type , (config_item('transaction_type')))) {
				if (in_array($tran->transaction_type,[1,2,3,4])) {
					$tran->method = 'transaction';
				}else{
					$tran->method = 'payments';
				}
				$tran->transaction_type = humanize(array_flip(config_item('transaction_type'))[$tran->transaction_type], '_');
				if ($tran->transaction_type == 'Sales Cart') {
					$tran->transaction_type = "Invoice";
				}
			}else{
				$tran->method = 'payments';
				
			}
			
		}
//		echo '<pre>';print_r($sales_transaction);exit;
		return $sales_transaction;
	}
	
	public function product_transactions_list($product){
		if (!class_exists('sales_model')) {
			$this->load->model('sales_model');
		}
		if (!class_exists('people_model')) {
			$this->load->model('people_model');
		}
		
		$product_sales = $this->sales_model->get_a_product_sales_transactions($product);
		
//		print_r($product_sales);exit;
		
		if(count($product_sales) > 0){
			foreach ($product_sales as $sales) {
				$product_transaction_details = $this->sales_model->get_a_sales_transaction($sales->transaction_id);
				
				if ($product_transaction_details->transaction_type == 1 || $product_transaction_details->transaction_type == 4) {
					$sales->qty = '-'.$sales->qty;
				}
				$sales->invoice_no = anchor_popup('preview/transaction/'.$product_transaction_details->id,$product_transaction_details->sales_no);
				$sales->name = $this->people_model->get_a_person($product_transaction_details->people_id)->row()->name;
				$sales->date = my_full_time_span($product_transaction_details->created_date);
				$sales->amount = my_number_format(floatval($sales->price * $sales->qty),1);
				
			}
			return($product_sales);
		}else{
			return( NULL);
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


}
