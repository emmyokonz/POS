<?php
defined('BASEPATH') or exit('No direct access to script is allowed');

class Accounts_model extends CI_Model
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

	function get($table = 'account_header')
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

	function get_account()
	{
		if (isset($this->_select) && !empty($this->_select)) {
			foreach ($this->_select as $select) {
				$this->db->select($select);
			}

			$this->_select = [];
		}

		// run each where that was passed
		if (isset($this->_where) && !empty($this->_where)) {
			//			if(!is_null($account_type)||!$account_type)array_merge($this->_where , ['account_type'=>$account_type]);

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

	function account()
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

		$this->get_account();

		return $this;
	}

	function get_all()
	{
		$this->get_account();

		return $this;
	}
	
	function total_accounts()
	{
		return $this->where(['account_type<>'=>'3'])->get_all()->num_rows();
	}
	
	function get_balance($id = NULL){
		
		if ($id ==NULL) {
			return $id;
		}
		$query = $this->db->select('balance')->where('account_id',$id)->get($this->_tables['account_metadata']);

		if ($query->num_rows() !== 0) {
			return $query->row()->balance;
		}
		
		return NULL;
	}
	
	function all_accounts_transaction($limit,$offset){
		
		$select = '';
		$select .= $this->_tables['account_header'].'.id as id, ';
		$select .= $this->_tables['account_header'].'.name as name, ';
		$select .= $this->_tables['account_header'].'.status as status, ';
		$select .= $this->_tables['account_header'].'.account_type as _account_type, ';
		$select .= $this->_tables['account_types'].'.name as account_type, ';
		
		$where = [/*$this->_tables['account_header'].'.id ' => '41',*/$this->_tables['account_header'].'.account_type <>' => '3'];
		
		$join = $this->_tables['account_header'].'.account_type = '.$this->_tables['account_types'].'.id';
		
		$accounts_query = $this->db->select($select)->where($where)->join($this->_tables['account_types'], $join)->order_by('account_type','DESC')->limit($limit)->offset($offset)->get($this->_tables['account_header']);
		
		$accounts = $accounts_query->result();
		
		if(count($accounts) !== 0){
			//lets fetch the balances from the metadat table.
			foreach ($accounts as $account) {
				$account->balance = $this->get_balance($account->id);
				
				if ($account->account_type == 'payables') {
					$account->balance = $this->people_model->sum_people_balance(2);
				}
				if ($account->account_type == 'receiveables') {
					$account->balance = $this->people_model->sum_people_balance(1);
				}
			}
				
		}/*else{
		
			$accounts = new stdClass();
			
			//Get all the fields from items table
			$fields = $accounts_query->list_fields();

			foreach ($fields as $field) {
				$accounts->$field='';
			}
			
			$accounts->balance='';
			
		}*/
		return $accounts;
	}
	
	function save($form_data){
		
		if ($form_data['account_type'] =='' or is_null($form_data['account_type'])) {
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
		
		if ($this->_name_check($form_data['name'] ,$form_data['account_type'])) {
			$this->set_error('account_creation_duplicate_name');
			return FALSE;
		}
		
		//check if its the right account that is created
		if (in_array($form_data['account_type'], ['4','5'])) {
			$this->set_error('account_creation_wrong_type');
			return FALSE;
		}
		
		//formate numbers
		$form_data['opening_balance'] = my_number_format($form_data['opening_balance'],0);
		
		if (!class_exists('App'))
			$this->load->library('App');
		
		$additional_data =[
			'created_date' 		=> time(),
			'last_update_time' 	=> time(),
			'status' 			=> 1,
			'account_id' 		=> NULL,
			'staff' 			=> $this->session->userdata('user_id'),
		];
		$form_data = $additional_data + $form_data;
		
		//filter off field no the the database
		$form_data_filtered_hrd = $this->app->_filter_data($this->_tables['account_header'] , $form_data);
		
		$this->db->trans_begin();
		
		$this->db->insert($this->_tables['account_header'],$form_data_filtered_hrd);
		$id = $this->db->insert_id($this->_tables['products']);
		$form_data['account_id'] = $id;	
		
		//check if the account already exists in the account metadata and delete them.
		if ($this->account_has_metadata($id)) {
			log_activity($this->session->userdata('user_id'),sprintf(lang('account_files_found'),$id).' ##'.$id);
			if (!$this->trash_account_has_metadata($id)) {
				$this->set_error('account_creation_trashing_error');
				$this->db->trans_rollback();
				return FALSE;
			}
		}
		
		//check if it's a bank befor creating its metadata and activities.
		if ($form_data['account_type'] == $this->get_account_type_id_by_name('banks')) {
		
			$metadata_data = ['balance' => $form_data['opening_balance']]+$form_data;
			//filter off field no the the database
			$form_data_filtered_metadata = $this->app->_filter_data($this->_tables['account_metadata'] , $metadata_data);

			//insert into account metadata.
			$this->db->insert($this->_tables['account_metadata'],$form_data_filtered_metadata);

			$additional_activity_data = [
				'bank_id' 	=> $form_data['account_id'],
				'amount'	=> $form_data['opening_balance'],
				't_date'	=> $form_data['created_date'],
				'a_date'	=> $form_data['created_date'],
				'staff' 	=> $this->session->userdata('user_id'),
				'transaction'		=> 'deposit',
				'memo'		=> lang('account_opening'),
				'account_id'		=> $this->get_account_type_id_by_name('equity'),
				'account_type'		=> $this->get_account_type_id_by_name('banks'),
			];

			//filter off field no the the database
			$additional_activity_data_filtered = $this->app->_filter_data($this->_tables['account_activity'] , $additional_activity_data);

			//insert into account Activities
			$this->db->insert($this->_tables['account_activity'],$additional_activity_data_filtered);
		}
		
		if ($this->db->trans_status()) {
			$this->db->trans_commit();
			$this->set_message('account_created_successful');
			return $id;
		}
		
		$this->set_error('account_created_unsuccessful');
		$this->db->trans_rollback();
		return FALSE;
	}
	
	function update($id,$form_data,$account){
		
		if ($form_data['account_type'] =='' or is_null($form_data['account_type'])) {
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
		
		//updating account name?
		if (array_key_exists('name', $form_data) && $this->_name_check($form_data['name'],$account->account_type) && strtolower($account->name) !== strtolower($form_data['name'])) {
			$this->set_error('account_creation_duplicate_name');

			$this->set_error('account_update_unsuccessful');

			return FALSE;
		}
		
		//check if its the right account that is updated
		if (in_array($form_data['account_type'], ['4','5'])) {
			$this->set_error('account_creation_wrong_type');
			return FALSE;
		}
		
		if (!class_exists('App'))
			$this->load->library('App');
		
		//filter off field no the the database
		$form_data_filtered_hrd = $this->app->_filter_data($this->_tables['account_header'] , $form_data);
		
		$this->db->trans_begin();
		
		$this->save_account_changes($id,$form_data);
		
		//formate numbers
		$form_data['opening_balance'] = my_number_format($form_data['opening_balance'],0);

		$additional_data =[
			'last_update_time' 	=> time(),
			'status' 			=> $form_data['status'],
			'staff' 			=> $this->session->userdata('user_id'),
		];
		$form_data = $additional_data + $form_data;
		
		$this->db->where(['id'=>$id])->update($this->_tables['account_header'],$form_data_filtered_hrd);
		$form_data['account_id'] = $id;	
		
		//check if it's a bank befor creating its metadata and activities.
		if ($form_data['account_type'] == $this->get_account_type_id_by_name('banks') && $form_data['opening_balance']>0) {
			
//			get the balance before updating the metadata balance.
			$old_balance = $this->get_balance($form_data['account_id']);
			$metadata_data = ['balance' => $form_data['opening_balance']]+$form_data;
			//filter off field no the the database
			$form_data_filtered_metadata = $this->app->_filter_data($this->_tables['account_metadata'] , $metadata_data);

			//insert into account metadata.
			$this->db->where(['account_id'=>$form_data['account_id']])->update($this->_tables['account_metadata'],$form_data_filtered_metadata);

			$additional_activity_data = [
				'bank_id' 	=> $form_data['account_id'],
				'amount'	=> floatval($metadata_data['opening_balance'] - $old_balance),
				't_date'	=> $form_data['last_update_time'],
				'a_date'	=> $form_data['last_update_time'],
				'staff' 	=> $this->session->userdata('user_id'),
				'memo'		=> lang('account_adjustment'),
				'transaction'		=> 'deposit',
				'account_id'		=> $this->get_account_type_id_by_name('equity'),
				'account_type'		=> $this->get_account_type_id_by_name('banks'),
			];
			
			//filter off field no the the database
			$additional_activity_data_filtered = $this->app->_filter_data($this->_tables['account_activity'] , $additional_activity_data);

			//insert into account Activities
			$this->db->insert($this->_tables['account_activity'],$additional_activity_data_filtered);
		}
		
		if ($this->db->trans_status()) {
			$this->db->trans_commit();
			$this->set_message('account_update_successful');
			return $id;
		}
		
		$this->set_error('account_created_unsuccessful');
		$this->db->trans_rollback();
		return FALSE;
	}

	function get_account_id_by_name($name = NULL){
		if ($name == NULL) {
			return $name;
		}
		
		return($this->select('id')->where(['name'=>$name])->limit(1)->account()->row()->id);
	
	}
	
	function get_account_name_by_id($id = NULL){
		if ($id == NULL) {
			return $id;
		}
		
		return($this->select('name')->where(['id'=>$id])->limit(1)->account()->row()->name);
	
	}
	
	function get_account_type_by_id($id = NULL){
		if ($id == NULL) {
			return $id;
		}
		
		return($this->select('account_type')->where(['id'=>$id])->limit(1)->account()->row()->account_type);
	
	}
	
	function get_account_type_id_by_name($name = NULL){
		if ($name == NULL) {
			return $name;
		}
		
		return($this->db->select('id')->where(['name'=>$name])->limit(1)->get($this->_tables['account_types'])->row()->id);
	
	}
	
	function get_account_type_name_by_id($id = NULL){
		if ($id == NULL) {
			return $id;
		}
		
		return($this->db->select('name')->where(['id'=>$id])->limit(1)->get($this->_tables['account_types'])->row()->name);
	
	}
	
	function account_has_activities($account_id = NULL)
	{
		if ($account_id == NULL) {
			return FALSE;
		}
		
		$found = $this->db->select('1',FALSE)->where(['account_id'=>$account_id])->or_where(['bank_id'=>$account_id])->get($this->_tables['account_activity'])->num_rows();
//			echo(boolval($found));exit;
		return (boolval($found));
	}
	
	function get_cash_balance(){
		$select = $this->_tables['account_header'].'.name as name,'
		.$this->_tables['account_metadata'].'.balance as balance,';

		$where= [
			$this->_tables['account_header'].'.account_type'=>1,
		];
		$join = $this->_tables['account_metadata'].".account_id = ".$this->_tables['account_header'].".id";

		$sql_query = $this->db->select($select)
		->where($where)
		->join($this->_tables['account_metadata'] , $join)
		->order_by($this->_tables['account_metadata'].'.balance','DESC')
		->limit(4);
		$query = $sql_query->get($this->_tables['account_header']);
		
		$total = 0;
		
		if ($query->num_rows()>=1) {
			$banks = $query->result();

			foreach ($banks as $result) {
				$total = $result->balance + $total;
				$result->name = humanize($result->name,'-');
				$result->balance = my_number_format($result->balance,1,1);
				if ($result->balance < 0) {
					$result->balance = html_tag('i',['class'=>'text-danger'],$result->balance);
					$result->name =html_tag('i',['class'=>'text-danger'],$result->name);
				}
			}
		} else {
			$banks = NULL;
		}
		$return = ['banks'=>$banks,'total'=>my_number_format($total,1,1)];
		return $return;
	}
	
	function account_has_metadata($account_id = NULL){
		if ($account_id == NULL) {
			return FALSE;
		}
		
		$found = $this->db->select('1',FALSE)->where(['account_id'=>$account_id])->limit(1)->get($this->_tables['account_metadata'])->num_rows();
//		exit($found);
		return (boolval($found));
	}
	
	function trash_account_has_metadata($account_id = NULL){
		if ($account_id == NULL) {
			return FALSE;
		}
		
		if ($this->account_has_metadata($account_id)) {
			$trashed = $this->db->where(['account_id'=>$account_id])->delete($this->_tables['account_metadata']);
		}else{
			$trashed = TRUE;
		}
		
		return (boolval($trashed));
	}
	
	function trash_account_in_activity($account_id = NULL)
	{
		if ($account_id == NULL) {
			return FALSE;
		}
		
		if ($this->account_has_activities($account_id)) {
			$trashed = $this->db->where(['account_id'=>$account_id])->or_where(['bank_id'=>$account_id])->delete($this->_tables['account_activity']);
		}else{
			$trashed = TRUE;
		}
		
		return (boolval($trashed));
	}
	
	function delete($id){
		if ($id == NULL) {
			$this->set_error(sprintf(lang('record_not_exist'),lang('account')));
			return FALSE;
		}
		
		$account = $this->where(['id'=>$id])->account()->row();
		
		//check if ensure its not equity,reveiveables,or payable
		if (!in_array($account->account_type,[1,2])) {
			$this->set_error('error_unable_to_perform_delete');
			return FALSE;
		}

		// check if it has a transaction already
		if ($this->account_has_transaction($id)) {
			$this->set_error('error_unable_to_perform_delete');
			return FALSE;
		}
		
		$this->db->trans_begin();
		//the account is free to be deleted it appears to be a new account or unsued account.
		if ($this->trash_account_has_metadata($id) && $this->trash_account_in_activity($id)) {

			$this->db->where('id',$id)->delete($this->_tables['account_header']);
			
		}
		
		if (!$this->db->trans_status()) {
			$this->db->trans_rollback();
			$this->set_error('account_delete_unsuccessful');
			return FALSE;
		}
		$this->db->trans_commit();
		$this->set_error('account_delete_successful');
		return TRUE;
	}
	
	function account_status($id, $activation = 'activation')
	{
		if ($id == NULL) {
			$this->set_error(sprintf(lang('record_not_exist'),lang('account')));
			return FALSE;
		}
		
		$account = $this->where(['id'=>$id])->account()->row();
		
		//check if ensure its not equity,reveiveables,or payable
		if (!in_array($account->account_type,[1,2])) {
			$this->set_error('error_unable_to_perform_delete');
			return FALSE;
		}
		
		$metadata = $this->where('id',$id)->select('metadata')->account()->row()->metadata;
		
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
		
		$this->db->where('id',$id)->update($this->_tables['account_header'],$data);
		
		if (!$this->db->trans_status()) {
			$this->db->trans_rollback();
			$this->set_error(sprintf(lang('account_status_unsuccessful'),$activation));
			return FALSE;
		}
		$this->db->trans_commit();
		$this->set_error(sprintf(lang('account_status_unsuccessful'),$activation));
		return TRUE;
	}
	
	function account_has_transaction($id = NULL){
		if (NULL == $id) {
			return TRUE;
		}
		
		if ($this->account_has_activities($id) || $this->account_has_metadata($id)) {
			return TRUE;
		}
		
		return FALSE;
	}

	function save_payment($form_data, $people_id,$payment_type)
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
		
		if ($form_data["t_date"] == NULL) {
			$form_data['t_date'] = $form_data['a_date']= time();
		}else{
			$form_data['t_date'] = strtotime($form_data['t_date'].date("H:i"));
		}
		
		$form_data['amount'] = my_number_format($form_data['amount']);


		if (!class_exists('App'))
			$this->load->library('App');

		if ($form_data['t_date'] <= strtotime('today 11:59:00 pm',time())) {
			$form_data['a_date'] = $form_data['t_date'];
		} else {
			$form_data['a_date'] = time();
		}

		$people_type = $this->people_model->get_a_person($people_id)->row()->people_type;

		$form_data['staff'] 		= $this->session->userdata('user_id');
		$form_data['people_id'] 	= $people_id;
		$form_data['bank_id'] 	= $form_data['account'];
		$form_data['memo'] 	= $form_data['description'];
		$form_data['account_type'] 	= $this->get_account_type_id_by_name('banks');

		if ($payment_type == "receive") {
			$form_data['transaction'] 		= 'deposit';
		}
		if ($payment_type == "make") {
			$form_data['transaction'] 		= 'cheque';
		}

		
		if ($payment_type =='make' ) {
			if ($people_type ==1 ) {
				$people_ = ['transaction_type'=>5];
				$form_data['account_id'] = $this->get_account_id_by_name('receiveables');
			} else {
				$people_ = ['transaction_type'=>6];
				$form_data['account_id'] = $this->get_account_id_by_name('payables');
			}
		}elseif($payment_type == "receive"){
			if ($people_type ==1 ) {
				$people_ = ['transaction_type'=>6];
				$form_data['account_id'] = $this->get_account_id_by_name('receiveables');
			} else {
				$people_ = ['transaction_type'=>5];
				$form_data['account_id'] = $this->get_account_id_by_name('payables');
			}
		}
		
		$form_data_filtered_hrd = $this->app->_filter_data($this->_tables['account_activity'] , $form_data);
		
		$this->db->trans_begin();
		$people_['amount'] = $form_data_filtered_hrd['amount'];
		$update_people_metatdat = $this->people_model->update_people_metadata($people_id,$people_);
		
		$account_ =[
			'last_update_time'=>$form_data_filtered_hrd['t_date'],
			'amount'=>$form_data_filtered_hrd['amount']
			
		];
		
		$update_account_metadata = $this->update_account_metadata($form_data['account'],$account_,$form_data['transaction'] );
//				exit('make'.$form_data['account_id']);
//		echo "<pre>"; print_r($form_data_filtered_hrd);exit;
		if ($update_people_metatdat == TRUE && $update_account_metadata==TRUE) {
			$this->db->insert($this->_tables['account_activity'],$form_data_filtered_hrd);
		}
		if (!$this->db->trans_status()) {
			$this->db->trans_rollback();
			return FALSE;
		}

		$this->db->trans_commit();
		return TRUE;
	}
	
	function save_expenses($form_data, $people_id)
	{
		if ($form_data['account_id'] =='' or is_null($form_data['account_id'])) {
			$this->set_error('account_choose_type');
			return FALSE;
		}
		if ($form_data['bank_id'] =='' or is_null($form_data['bank_id'])) {
			$this->set_error('account_choose_bank');
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
		
		if ($form_data["t_date"] == NULL) {
			$form_data['t_date'] = $form_data['a_date']= time();
		}else{
			$form_data['t_date'] = strtotime($form_data['t_date'].date("H:i"));
		}
		
		$form_data['amount'] = my_number_format($form_data['amount']);


		if (!class_exists('App'))
			$this->load->library('App');

		if ($form_data['t_date'] <= strtotime('today 11:59:59 pm',time())) {
			$form_data['a_date'] = $form_data['t_date'];
		} else {
			$form_data['a_date'] = time();
		}

		$people_type_query = $this->people_model->get_a_person($people_id);
		if ($people_type_query !== FALSE ) {
			$people_type = $people_type_query->row()->people_type;
		}else{
			$people_type = 3;
		}

		$form_data['staff'] 		= $this->session->userdata('user_id');
		$form_data['people_id'] 	= $people_id;
		$form_data['memo'] 	= $form_data['description'];
		$form_data['account_type'] 	= $this->get_account_type_id_by_name('expenses');
		$form_data['transaction'] 		= 'cheque';
		
		$form_data_filtered_hrd = $this->app->_filter_data($this->_tables['account_activity'] , $form_data);
		if ($people_type ==1 ) {
			$people_ = ['transaction_type'=>5,'amount'=>$form_data_filtered_hrd['amount']];
		} else {
			$people_ = ['transaction_type'=>6,'amount'=>$form_data_filtered_hrd['amount']];
		}
		
		$this->db->trans_begin();
		$update_people_metatdat = $this->people_model->update_people_metadata($people_id,$people_);
		
		$account_ =[
			'last_update_time'=>$form_data_filtered_hrd['t_date'],
			'amount'=>$form_data_filtered_hrd['amount']
			
		];
		
		$update_account_metadata = $this->update_account_metadata($form_data['bank_id'],$account_,$form_data['transaction'] );

		if ($update_people_metatdat == TRUE && $update_account_metadata==TRUE) {
			$this->db->insert($this->_tables['account_activity'],$form_data_filtered_hrd);
		}
//		exit;
		if (!$this->db->trans_status()) {
			$this->db->trans_rollback();
			return FALSE;
		}

		$this->db->trans_commit();
		return TRUE;
	}
	
	function save_transfer($form_data)
	{
		if ($form_data['account_id'] =='' or is_null($form_data['account_id'])) {
			$this->set_error('account_choose_type');
			return FALSE;
		}
		if ($form_data['bank_id'] =='' or is_null($form_data['bank_id'])) {
			$this->set_error('account_choose_bank');
			return FALSE;
		}
		if ($form_data['bank_id'] == $form_data['account_id']) {
			$this->set_error('account_choose_same_bank');
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
		
		if ($form_data["t_date"] == NULL) {
			$form_data['t_date'] = $form_data['a_date']= time();
		}else{
			$form_data['t_date'] = strtotime($form_data['t_date'].date("H:i"));
		}
		
		$form_data['amount'] = my_number_format($form_data['amount']);


		if (!class_exists('App'))
			$this->load->library('App');

		if ($form_data['t_date'] <= strtotime('today 11:59:00 pm',time())) {
			$form_data['a_date'] = $form_data['t_date'];
		} else {
			$form_data['a_date'] = time();
		}

		$form_data['staff'] 		= $this->session->userdata('user_id');
		$form_data['people_id'] 	= NULL;
		$form_data['memo'] 	= 'Fund transfer';
		$form_data['account_type'] 	= $this->get_account_type_id_by_name('banks');
		$form_data['transaction'] 		= 'transfer';
		
		$form_data_filtered_hrd = $this->app->_filter_data($this->_tables['account_activity'] , $form_data);
		
		$this->db->trans_begin();
		$account_ =[
			'last_update_time'=>$form_data_filtered_hrd['t_date'],
			'amount'=>$form_data_filtered_hrd['amount']
			
		];
		
		$update_bank_metadata = $this->update_account_metadata($form_data['bank_id'],$account_,'cheque' );
		$update_account_metadata = $this->update_account_metadata($form_data['account_id'],$account_,'deposit' );

		if ($update_bank_metadata == TRUE && $update_account_metadata==TRUE) {
			$this->db->insert($this->_tables['account_activity'],$form_data_filtered_hrd);
		}
//		exit;
		if (!$this->db->trans_status()) {
			$this->db->trans_rollback();
			return FALSE;
		}

		$this->db->trans_commit();
		return TRUE;
	}
	
	function update_account_metadata($id, $form_data, $type="deposit")
	{

		if ($type == 'deposit') {
			$form_data['balance'] = ($this->get_balance($id) + my_number_format($form_data['amount'] , 0));
		} else {
			$form_data['balance'] = ($this->get_balance($id)  - my_number_format($form_data['amount'] , 0));
		}
		
		$form_data_filtered = $this->app->_filter_data($this->_tables['account_metadata'] , $form_data);
		
		if ($this->db->where(['account_id'=>$id])->update($this->_tables['account_metadata'], $form_data_filtered)) {

			return TRUE;
		}
		return FALSE;
	}

	public function set_message($message)
	{
		$this->_messages[] = $message;

		return $message;
	}
	
	function get_account_transaction($account_type_name,$account_id,$account_type_id){
		
		if ($account_type_id == 1) {
			return $this->get_bank_details($account_id);
		}
		if ($account_type_id == 2) {
			return $this->get_expenses_details($account_id);
		}
		if (in_array($account_type_id,[3,4])) {
			return $this->get_accountPR_details($account_id);
		}
	}
	
	function get_bank_details($id){
		$this->db->select('t_date as date,people_id,memo,amount,bank_id,account_id,transaction,id');
		$this->db->where('bank_id',$id);
		$this->db->or_where('bank_id',$id);
		$this->db->from($this->_tables['account_activity']);
		$query1 = $this->db->get_compiled_select();

		$this->db->select('t_date as date,people_id,memo,amount,bank_id,account_id,transaction,id');
		$this->db->where('account_id',$id);
		$this->db->from($this->_tables['account_activity']);
		$query2 = $this->db->get_compiled_select();
		
		$query = $this->db->query($query1. ' UNION '. $query2.' order by date ASC');

		if ($query->num_rows() >0) {
			$query_result = $query->result();
		} else {
			$return['total_amount']=my_number_format(0,1,1);
			$return['details']=NULL;
			return($return);
		}
		if (!class_exists('people_model')) {
			$this->load->model('people_model');
		}
		$amount = 0;
		foreach ($query_result as $result) {
			if ($result->transaction =='deposit') {
				$amount = $amount + $result->amount;
				$result->balance = my_number_format($amount,1);
				$result->credit = my_number_format($result->amount,1);
				$result->debit = '0.00';
			}elseif($result->transaction =='transfer'){
				if ($id == $result->bank_id) {
					$amount = $amount - $result->amount;
					$result->balance = my_number_format($amount,1);
					$result->debit = '-'.my_number_format($result->amount,1);
					$result->credit = '0.00';
				}elseif ($id == $result->account_id){
					$amount = $amount + $result->amount;
					$result->balance = my_number_format($amount,1);
					$result->credit = my_number_format($result->amount,1);
					$result->debit = '0.00';
				}
			}else{
				$amount = $amount - $result->amount;
				$result->balance = my_number_format($amount,1);
				$result->debit = '-'.my_number_format($result->amount,1);
				$result->credit = '0.00';
			}
			
			if (!is_null($result->people_id)) {
				$name_q = $this->people_model->get_a_person($result->people_id);
				if ($name_q !== FALSE) {
					$result->name = humanize($name_q->row()->name,'-');
				} else {
					$result->name =ucwords($result->people_id);
				}
			}else{
				$result->name = NULL;
			}
			
			$result->account = humanize($this->get_account_name_by_id($result->account_id),'-');
			$result->memo_reduce = word_limiter($result->memo,5,'...');
			$result->amount = my_number_format($result->amount,1,1);
			
			$view ='';
			$view = admin_anchor(
			'preview/payment/'.$result->id,
			fa_icon('eye', TRUE).' '."<span class='hidden-xs hidden-sm'>View</span>",
			[
				'class'=>'btn btn-sm btn-warning',
			]
			);
			$result->action = $view;
		}
//		echo "<pre>";print_r($query_result);exit;
		$return['total_amount']=my_number_format($amount,1,1);
		$return['details']=$query_result;
		return $return;
		
	}
	
	function get_expenses_details($id){
		$query = $this->db
		->select('t_date as date,people_id,memo,amount,bank_id')
		->where('account_id',$id)
//		->where('account_type',$id)
		->order_by('t_date',"ASC")
		->get($this->_tables['account_activity']);
		
		if ($query->num_rows() >0) {
			$query_result = $query->result();
		}else{
			$return['total_amount']=my_number_format(0,1,1);
			$return['details'] = NULL;
			return($return);
		}
		if (!class_exists('people_model')) {
			$this->load->model('people_model');
		}
		$amount = 0;
		foreach ($query_result as $result) {
			$amount = $amount + $result->amount;
			$name_q = $this->people_model->get_a_person($result->people_id);
			if ($name_q !== false) {
				$result->name = humanize($name_q->row()->name,'-');
			}else{
				$result->name =ucwords($result->people_id);
			}
			$result->bank = humanize($this->get_account_name_by_id($result->bank_id),'-');
			$result->amount = my_number_format($result->amount,1,1);
		}
		$return['total_amount']=my_number_format($amount,1,1);
		$return['details']=$query_result;
		
		return $return;
	}

	function get_a_transaction_detail($tansaction_id=NULL){
		if ($tansaction_id == NULL) {
			return NULL;
		}
		$this->db->select('t_date as date,people_id,memo,amount,staff,bank_id,account_id,transaction,id');
		$this->db->where('id',$tansaction_id);
		$details_query = $this->db->get($this->_tables['account_activity']);
		
		if ($details_query->num_rows() > 0) {
			$details = $details_query->row();
		}else{
			return NULL;
		}
		
		$details->date = invoice_date($details->date);
		$people_query = $this->people_model->get_a_person($details->people_id);
		if($people_query !== FALSE ){
			if ($people_query->num_rows() > 0) {
				$company = $people_query->row()->company;
				$name = humanize($people_query->row()->name,'-');
				$details->name = $company. ' ('.$name.')';
			}else{
				$details->name = NULL;
			}
			
		}else{
			$details->name = NULL;
		}
		$details->amount = my_number_format($details->amount,1,1);
		$details->staff = get_display_name($details->staff);
		
		if ($details->transaction == 'cheque') {
			$details->name_title = 'Paid to';
			$details->from_account = $this->get_account_name_by_id($details->bank_id);
			$details->to_account = NULL;
		}else{
			$details->name_title = 'Payment from';
			$details->from_account = $this->get_account_name_by_id($details->bank_id);
			$details->to_account = $this->get_account_name_by_id($details->account_id);
			if ($details->transaction == 'deposit') {
				$details->from_account = NULL;
				$details->to_account = $this->get_account_name_by_id($details->bank_id);
			}
		}
		
		return $details;
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
	function save_account_changes($id, $form_data=[])
	{
		$db_details = $this->accounts_model->where(['id'=>$id,'account_type<>'=>'3'])->account()->row();

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

			$this->db->where(['id'=>$id])->update($this->_tables['account_header'],$data_hrd);
			return(TRUE);
		}
	}

/**
* title check
*
* @param $account_title string
*
* @return bool
* @author Mathew
*/
	protected function _name_check($account_title = '', $account_type)
	{

		if (empty($account_title)) {
			return FALSE;
		}

		return $this->db->where(['name'=> $account_title,'account_type'=>$account_type])
		->limit(1)
		->count_all_results($this->_tables['account_header']) > 0;
	}
}
