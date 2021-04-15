<?php
defined('BASEPATH') or exit('No direct access to script is allowed');

class People_model extends CI_Model
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

	function get_people()
	{
		if (isset($this->_select) && !empty($this->_select)) {
			foreach ($this->_select as $select) {
				$this->db->select($select);
			}

			$this->_select = [];
		}

		// run each where that was passed
		if (isset($this->_where) && !empty($this->_where)) {
			//			if(!is_null($people_type)||!$people_type)array_merge($this->_where , ['people_type'=>$people_type]);

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
		} 

		$this->_query_result = $this->get();

		return $this;
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

	function get_a_person($id = NULL)
	{
		if ($id == null) {
			$this->set_error('error_people_not_found');
			return FALSE;
		}

		$this->limit(1);
		$this->order_by($this->_tables['people'].'.id','DESC');
		$this->where([$this->_tables['people'].'.id'=>$id]);

		$this->get_people();

		return $this;
	}

	function get_a_person_by_name($name = NULL)
	{
		if ($name == null) {
			$this->set_error('error_people_not_found');
			return FALSE;
		}

		$this->limit(1);
		$this->where([$this->_tables['people'].'.name'=>$name]);

		$this->get_people();

		return $this;
	}

	/*
	Gets information about a particular item
	*/
	function get_person_info($people_id)
	{
//		$this->where('id',$people_id)->limit(1);
		
		$select = $this->_tables['people'].'.name as name,'
		.$this->_tables['people'].'.email as email,'
		.$this->_tables['people'].'.company as company,'
		.$this->_tables['people'].'.status as status,'
		.$this->_tables['people_metadata'].'.balance as balance,';

		$where= [
			$this->_tables['people'].'.id'=>$people_id,
		];
		$or_where=[$this->_tables['people'].'.name'=>$people_id,];

		$join = $this->_tables['people'].".id = ".$this->_tables['people_metadata'].".people_id";

		$sql_query = $this->db->select($select)
		->where($where)
		->or_where($or_where)
		->join($this->_tables['people'] , $join);

		$query = $sql_query->get($this->_tables['product_metadata']);

		if ($query->num_rows()==1) {
			return $query->row();
		} else {
			//Get empty base parent object, as $people_id is NOT an item
			$item_obj=new stdClass();

			//Get all the fields from items table
			$fields = $sql_query->list_fields($this->_tables['people']);

			foreach ($fields as $field) {
				$item_obj->$field='';
			}

			return $item_obj;
		}
	}


	function people()
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

		$this->get_people();

		return $this;
	}

	function get_all()
	{
		$this->get_people();

		return $this;
	}
	
	function total_payments(){
		return $this->db->select('1',FALSE)
		->where('account',"recievables")
		->or_where('account',"payables")
		->get($this->_tables['people_activity'])->num_rows();
	}
	
	function all_payments_transaction($limit , $offset){
		return $this->db
		->where('account',"recievables")
		->or_where('account',"payables")
		->offset($offset)
		->limit($limit)
		->order_by('t_date',"DESC")
		->get($this->_tables['people_activity'])->result();
	}
	
	function save_payment($form_data,$people_id)
	{
		if ($form_data['account'] =='' or is_null($form_data['account'])) {
			$this->set_error('account_choose_type');
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
		
		$form_data['t_date'] = strtotime($form_data['t_date'].date("H:i"));
		$form_data['amount'] = my_number_format($form_data['amount']);
		
		
		if (!class_exists('App'))
			$this->load->library('App');

		if ($form_data['t_date'] <= strtotime('today 11:59:00 pm',time())) {
			$data_hrd['a_date'] = $form_data['t_date'];
		}else{
			$form_data['a_date'] = time();
		}
		
		$people_type = $this->get_a_person($people_id)->row()->people_type;
		
		$form_data['staff'] 		= $this->session->userdata('user_id');
		$form_data['people_id'] 	= $people_id;

		$form_data_filtered_hrd = $this->app->_filter_data($this->_tables['people_activity'] , $form_data);
		
		$this->db->trans_begin();
		$update_metatdat = $this->update_people_metadata($people_id,['transaction_type'=>2,'amount'=>$data_hrd['amount']]);
		
		if ($update_metatdat) {
			$this->db->insert($this->_tables['people_activity'],$data_hrd);
		}
		if (!$this->db->trans_status()) {
			$this->db->trans_rollback();
			return FALSE;
		}
		
		$this->db->trans_commit();
		return TRUE;
	}
	
	function sum_people_balance($people_type = NULL){
		
		if ($people_type == NULL) {
			return '0';
		}
		$balance = 0;
		$people = $this->select('id')->where('people_type',$people_type)->get_people()->result();
		foreach ($people as $person) {
			$balance =$balance + $this->db->select('balance')->where('people_id',$person->id)->get($this->_tables['people_metadata'])->row()->balance;
		}
		
		return $balance;
	}
	
	function get_person_metadata()
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

		$this->_query_result = $this->db->get($this->_tables['people_metadata']);

		return $this;
	}
	
	function get_people_search_suggestions($search,$people, $limit,$cart=1)
	{

		$limit = isset($limit)? $limit : 25;
		$suggestions = array();

		$this->db->from($this->_tables['people']);
		if ($people !== "0") {
			$this->db->where("(name LIKE '%".$this->db->escape_like_str($search)."%' or
			company LIKE '%".$this->db->escape_like_str($search)."%') and status=1 and people_type =".$people);
		}else{
			$this->db->where("(name LIKE '%".$this->db->escape_like_str($search)."%' or
			company LIKE '%".$this->db->escape_like_str($search)."%') and status=1");
		}
		$this->db->order_by("people_type", "asc");
		$this->db->order_by("name", "asc");
		$this->db->limit($limit);
		$by_name = $this->db->get();
		foreach ($by_name->result() as $row) {
			$suggestions[]=$row;
		}
		return $suggestions;
	}
	
	function save($form_data, $people_type = 1)
	{
		//		purify data
		foreach ($form_data as $key => $dirty_dirty) {
			if ($key == 'name') {
				$form_data[$key] = html_entity_decode(cleanString($dirty_dirty,TRUE,"-"));
			} else {
				$form_data[$key] = html_entity_decode(cleanString($dirty_dirty));
			}

		}

		if ($this->_title_check($form_data['name'] ,$people_type)) {
			$this->set_error('people_creation_duplicate_title');
			return FALSE;
		}

		if (!class_exists('App'))
			$this->load->library('App');

		$form_data_filtered_hrd = $this->app->_filter_data($this->_tables['people'] , $form_data);

		$data_hrd =[
			'created_date' 	=> time(),
			'status' 		=> 1,
			'staff' 		=> $this->session->userdata('user_id'),
			'people_type' 	=> $people_type,

		];

		$this->db->trans_begin();

		$data_hrd = array_merge($form_data_filtered_hrd , $data_hrd);
		$this->db->insert($this->_tables['people'],$data_hrd);

		$id = $this->db->insert_id($this->_tables['people']);

		//create person metadata
		$data_metadata['people_id'] = $id;

		//filter off the fileds that ar not in the table columbs
		$form_data_filtered_metadata = $this->app->_filter_data($this->_tables['people_metadata'] , $form_data);

		$data_metadata = array_merge($form_data_filtered_metadata , $data_metadata);
		$data_metadata['balance'] = $form_data['opening_balance'];
		$data_metadata['last_update_date'] = $data_hrd['created_date'];
		$data_metadata['staff'] = $data_hrd['staff'];

		$this->db->insert($this->_tables['people_metadata'],$data_metadata);
		//end of person metadata creation.
		
		/*//insert into the people activity
		if (floatval($form_data['opening_balance']) < 0) {
			$activity['current_bal'] = 0;
			$activity['amount'] = $form_data['opening_balance'];
		}else{
			$activity['amount'] = 0;
			$activity['current_bal'] = $form_data['opening_balance'];
		}
		$activity['account'] = 'opening balance';
		$activity['people_id'] = $id;
		$activity['t_date'] = time();
		$activity['staff'] = $this->session->userdata('user_id');
		
		$this->db->insert($this->_tables['people_activity'],$activity);*/
		
		if (floatval($form_data['opening_balance']) !== 0) {
			$activity['amount'] = $form_data['opening_balance'];
			$activity['account_id'] = '3';
			$activity['memo'] = 'opening balance';
			$activity['people_id'] = $id;
			$activity['t_date'] = $data_hrd['created_date'];
			$activity['a_date'] = $data_hrd['created_date'];
			$this->load->model('accounts_model');
			$activity['account_type'] = ($people_type == 1 ?$this->accounts_model->get_account_type_id_by_name('receiveables'):$this->accounts_model->get_account_type_id_by_name('payables'));
			$activity['staff'] = $this->session->userdata('user_id');
			$activity['transaction'] = ($form_data['opening_balance'] > 0?'deposit':'cheque');

			$this->db->insert($this->_tables['account_activity'],$activity);
		}
		
		if ($this->db->trans_status() == TRUE) {

			$this->db->trans_commit();
			return $id;
		}

		$this->db->trans_rollback();
		return FALSE;
	}

	function update($id, $form_data , $people_type)
	{
		$people = $this->where('id',$id)->get_people($id)->row();

		//updating people title?
		if (array_key_exists('name', $form_data) && $this->_title_check($form_data['name'],$people->people_type) && $people->name !== $form_data['name']) {
			$this->set_error('people_creation_duplicate_title');

			$this->set_error('people_update_unsuccessful');

			return FALSE;
		}

		//		purify form_data
		foreach ($form_data as $key => $dirty_dirty) {
			if ($key == 'name') {
				$form_data[$key] = html_entity_decode(cleanString($dirty_dirty,TRUE,"-"));
			} else {
				$form_data[$key] = html_entity_decode(cleanString($dirty_dirty));
			}

		}
		
		// Filter the data passed
		$form_data_filtered_hrd = $this->app->_filter_data($this->_tables['people'] , $form_data);


		$this->db->trans_begin();
		
		$this->save_people_changes($id,$form_data,$this->person_type($id));

		$data_hrd=[];

		
		$data_hrd = array_merge($form_data_filtered_hrd , $data_hrd);
		$this->db->where(['id'=>$id,'people_type'=>$people_type]);
		$this->db->update($this->_tables['people'], $data_hrd);

		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();

			log_activity($this->session->userdata('user_id'),'error updating people ##'.$id);
			return FALSE;
		}

		$this->db->trans_commit();

		log_activity($this->session->userdata('user_id'),'updated people ##'.$id);

		return TRUE;
	}
	
	function people_status($id, $activation = 'activation')
	{
		if ($id == NULL) {
			$this->set_error(sprintf(lang('record_not_exist'),'Client'));
			return FALSE;
		}

		$people = $this->where(['id'=>$id])->people()->row();

		$metadata = $this->where('id',$id)->select('metadata')->people()->row()->metadata;

		$data = [
			'metadata'	=> json_encode([
				'action_by'=>$this->session->userdata('user_id'),
				'action_time'=>time(),
				'action_ip'=>$this->input->ip_address(),
				'action'=>$activation,
				'action_details'=>[],
			]),
		];
		
		if ($metadata !== NULL) {
			$data['metadata'] = $metadata.','.$data['metadata'];
		}
		
		//update data
		if ($activation == 'activation') {
			$data['status'] = 1;
		} else {
			$data['status'] = 0;
		}

		$this->db->trans_begin();

		$this->db->where('id',$id)->update($this->_tables['people_header'],$data);

		if (!$this->db->trans_status()) {
			$this->set_message(sprintf(lang('people_activated_unsuccessful'),$activation));
			$this->db->trans_rollback();
			return FALSE;
		}
		$this->set_message(sprintf(lang('people_activated_successful'),$activation));
		$this->db->trans_commit();
		return TRUE;
	}
	
	function update_people_metadata($id, $form_data)
	{

		$data_hrd =[
			'last_update_date' 	=> time(),
			'staff' 			=> $this->session->userdata('user_id'),
		];
		if ($this->get_a_person($id) == FALSE) {
			return true;
		}
		if ($form_data['transaction_type']==1 || $form_data['transaction_type']==3) {
			$data_hrd['balance'] = ($this->where(['people_id'=>$id])->get_person_metadata()->row()->balance  + my_number_format($form_data['amount'] , 0)) - (my_number_format($form_data['payment'],0) );
		} else if ($form_data['transaction_type']==5 ) {
			$data_hrd['balance'] = ($this->where(['people_id'=>$id])->get_person_metadata()->row()->balance  + my_number_format($form_data['amount'] , 0));
		}else{
			$data_hrd['balance'] = ($this->where(['people_id'=>$id])->get_person_metadata()->row()->balance  - my_number_format($form_data['amount'] , 0));
		}
		$this->db->where(['people_id'=>$id]);

		if ($this->db->update($this->_tables['people_metadata'], $data_hrd)) {
		
			return TRUE;
		}
		return FALSE;
	}
	
	function update_people_activity($id, $form_data)
	{
//		print_r($form_data);exit;

		//		purify data
		foreach ($form_data as $key => $dirty_dirty) {
			$form_data[$key] = html_entity_decode(cleanString($dirty_dirty));
		}
		
//		$this->db->trans_begin();
		
		if (!isset($form_data['account'])) {
			if ($form_data['transaction_type'] == 2) {
				$form_data['account'] = "credit_memo";
			} else {
				$form_data['account'] = "recievables";
			}
			if ($this->get_a_person($id)->row()->people_type == '2') {
				$form_data['account'] = "payables";
			}
		}

		$current = $this->where(['people_id'=>$id])->get_person_metadata()->row()->balance;
		$data_hrd =[
			'current_bal' 		=> $current,
			'people_id' 		=> $id,
			't_date' 		=> $form_data['created_date'],
			'amount' 		=> $form_data['payment'],
			'account' 		=> $form_data['account'],
			'staff' 		=> $form_data['staff'],
			'transaction_id' 		=> $form_data['transaction_id'],
		];

		if ($this->db->insert($this->_tables['people_activity'], $data_hrd ,TRUE)) {
		
			return TRUE;
		}
		return FALSE;
	}

	public function delete($id =NULL)
	{
		if ($id == NULL) {
			$this->set_error('people_delect_no_id');
			return FALSE;
		}

//		check is this client was involved in any activity or transaction.
		$found_person_activities = $this->db->select('1',FALSE)->where('people_id',$id)->get($this->_tables['account_activity'])->num_rows();
		$found_person_transactions = $this->db->select('1',FALSE)->where('people_id',$id)->get($this->_tables['transaction_header'])->num_rows();
		$this->db->trans_begin();

		if (!$found_person_activities && !$found_person_transactions) {

			$this->db->where(['people_id'=>$id])->delete($this->_tables['people_metadata']); 	
			$this->db->where(['id'=>$id])->delete($this->_tables['people_header']);
			$this->set_message('people_delete_successful');
			log_activity($this->session->userdata('user_id'),'deleted a client #'.$id);
		} else {
			$this->people_status($id,'deactivation');
			log_activity($this->session->userdata('user_id'),'deleted a people #'.$id);
		}

		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			$this->set_message(sprintf(lang('people_activated_unsuccessful'),'deactivation'));
			return FALSE;
		}

		$this->db->trans_commit();

		return TRUE;
	}

	function person_type($id = NULL)
	{
		return $this->get_people($id)->row()->people_type;
	}
	
	function generate_people_report($people_type,$transaction_type){
		$date = $this->session->userdata('range');
		$date = explode('_',$date);
		$start_start = $date[0];
		$start_end = $date[1];
		
		$select = $this->_tables['people_header'].'.name as name,'.
				'COUNT('.$this->_tables['transaction_header'].'.id) as total_purchase,'.
				'SUM('.$this->_tables['transaction_header'].'.amount) as amount,'.
				'SUM('.$this->_tables['transaction_header'].'.profit) as profit,'.
				$this->_tables['people_header'].'.id as id,';
		
		$where = [
			$this->_tables['people_header'].'.people_type'=>$people_type,
			$this->_tables['transaction_header'].'.transaction_type'=>$transaction_type
		];
		$join = $this->_tables['people_header'].'.id = '.$this->_tables['transaction_header'].'.people_id';
		
		$result = $this->db
		->select($select)
		->where($where)
		->group_start()
		->where($this->_tables['transaction_header'].'.created_date >=',$start_start)
		->where($this->_tables['transaction_header'].'.created_date <=',$start_end)
		->group_end()
		->join($this->_tables['people_header'],$join)
		->order_by($this->_tables['people_header'].'.name','ASC')
		->group_by($this->_tables['people_header'].'.name')
		->get($this->_tables['transaction_header']);
		
		if ($result->num_rows() >0) {
			$return=$result->result();
		}else{
			$return = NULL;
		}
		return($return);
	}

	function unpublish($id)
	{
		$this->db->trans_begin();

		// run each where that was passed
		if (isset($this->_where) && !empty($this->_where)) {

			foreach ($this->_where as $k => $where) {
				$this->db->where($where);
			}

			$this->_where = [];
		} else {
			$this->db->where(['id'=>$id]);
		}

		// Filter the data passed
		$data['metadata'] = json_encode([
			'ip_address'=>$this->input->ip_address(),
			'last_updated_by'=>$this->session->userdata('user_id'),
			'last_update_time'=>time(),
		]);
		$data['publish_by']='';
		$data['publish_date']='';
		$data['published']=0;
		$this->db->update($this->_tables['people_metadata'], $data);

		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			$this->set_error('people_unpublish_unsuccessful');
			return FALSE;
		}

		$this->db->trans_commit();

		$this->set_message('people_unpublish_successful');
		log_activity($this->session->userdata('user_id'),'unpublished people #'.$id);
		return TRUE;
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
	* check if their is changes before updating.
	*
	* @return boolean
	*/
	function save_people_changes($id, $form_data=[], $type)
	{
		$db_details = $this->get_a_person($id)->row();

		if (isset($db_details->category)) {
			$db_details->category_id=$db_details->category;
		}

		//get the key/value intersection of the form_data and the db data
		
		$qq = array_intersect_key((array)$db_details,(array)$form_data);
		/*print_r($qq);
		print_r($form_data);
		exit;
		*/
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

			$this->db->where(['id'=>$id])->update($this->_tables['people'],$data_hrd);
			return(TRUE);
		}
	}

/**
* title check
*
* @param $people_title string
*
* @return bool
* @author Mathew
*/
	protected function _title_check($people_title = '', $people_type)
	{

		if (empty($people_title)) {
			return FALSE;
		}

		return $this->db->where(['name'=> $people_title,'people_type'=>$people_type])
		->limit(1)
		->count_all_results($this->_tables['people']) > 0;
	}
}
