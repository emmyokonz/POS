<?php

class Product_lib
{

	/*public $tables = array();

	function __construct()
	{

		// initialize db tables data
		$this->tables = $this->config->item('tables');
	}*/


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
	/*public function __call($method, $arguments)
	{
		if (method_exists( $this->product_model, $method) ) {
			return call_user_func_array( array($this->product_model,$method), $arguments);
		}
		throw new Exception('Undefined method Product Lib::' . $method . '() called');
	}*/

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
	/*public function __get($var)
	{
		return get_instance()->$var;
	}*/
	
	/*function product_exits($product_id){
		$query = $this->product_model->select("1",FALSE)
			->where('id',$product_id)
			->where('item_type' , '1')
			->get_product();
		return ($query->num_rows()==1);
	}*/
	
	/*
	Get an item id given an item number
	*/
	/*function get_item_id($product_suk)
	{
		$query = $this->product_model->select("id")
		->where('suk',$product_suk)
		->where('item_type' , '1')
		->get_products();

		if ($query->num_rows()==1) {
			return $query->row()->id;
		}

		return false;
	}*/
	

	/*
	Gets information about a particular item
	*/
	/*function get_info($item_id)
	{
		$this->product_model->where('id',$item_id)->limit(1);

		$query = $this->product_model->get_products();

		if ($query->num_rows()==1) {
			return $query->row();
		} else {
			//Get empty base parent object, as $item_id is NOT an item
			$item_obj=new stdClass();

			//Get all the fields from items table
			$fields = $this->db->list_fields($this->_tables['products']);

			foreach ($fields as $field) {
				$item_obj->$field='';
			}

			return $item_obj;
		}
	}*/

}