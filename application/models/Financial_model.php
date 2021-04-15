<?php
defined('BASEPATH') or exit('No direct access to script is allowed');

class Financial_model extends CI_Model
{
	private $_errors =[];
	private $_messages = [];
	private $_output = '';
	private $_tables = [];
	
	function __construct()
	{
		$this->_tables = config_item('tables');
	}
	
	function get_DCS_value(){
		$select = 'SUM('.$this->_tables['people_metadata'].'.balance) as D,';

		$where= [
			$this->_tables['people_header'].'.people_type'=>1,
		];

		$join = $this->_tables['people'].".id = ".$this->_tables['people_metadata'].".people_id";
		$D_query = $this->db->select($select)
		->where($where)
		->join($this->_tables['people_metadata'] , $join);
		$query_D = $D_query->get($this->_tables['people_header']);
		
		$D = $query_D->row()->D;


		$select_C = 'SUM('.$this->_tables['people_metadata'].'.balance) as C,';

		$where_C= [
			$this->_tables['people_header'].'.people_type'=>2,
		];

		$join_C = $this->_tables['people'].".id = ".$this->_tables['people_metadata'].".people_id";
		$C_query = $this->db->select($select_C)
		->where($where_C)
		->join($this->_tables['people_metadata'] , $join_C);
		$query_C = $C_query->get($this->_tables['people_header']);
		
		$C = $query_C->row()->C;

		$S = $this->db->select('SUM(qty*COST_price) as S')->from($this->_tables['product_metadata'])->get()->row()->S;
		
		if ($C < 0) {
			$C = html_tag('span',['class'=>'text-danger'],my_number_format($C,1,1));
		}
		if ($D < 0) {
			$D = html_tag('span',['class'=>'text-danger'],my_number_format($D,1,1));
		}
		if ($S < 0) {
			$S = html_tag('span',['class'=>'text-danger'],my_number_format($S,1,1));
		}
		return ['D'=>my_number_format($D,1,1),'C'=>my_number_format($C,1,1),'S'=>my_number_format($S,1,1)];
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
