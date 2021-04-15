<?php

class MY_Output extends CI_Output {
	
	private $last_set_status_code;
	
	function set_status_header($code='200',$text='') {
		set_status_header($code,$text);
		$this->last_set_status_code=$code;
		return $this;
	}
	
	function get_status_header(){
		return $this->last_set_status_code;
	}
}