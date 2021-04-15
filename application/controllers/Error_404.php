<?php

class Error_404 extends MY_AdminController {
	
	function __construct(){
		parent::__construct();
	}
	function index(){
		$this->output->set_status_header('404');
    	$this->template->build('errors/error_404');//loading in custom error view 
	}
}