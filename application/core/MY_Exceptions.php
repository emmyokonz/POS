<?php

class MY_Exceptions extends CI_Exceptions{
	function __construct() {
		parent::__construct();
		$this->CI =& get_instance();
	}
	
	function show_404($page='', $log_error = TRUE){
		header("HTTP/1.1 404 Not Found");
		// By default we log this, but allow a dev to skip it
		$heading = '404 Page Not Found This Time';
        if ($log_error)
        {
            log_message('error', $heading.': '.$page);
        }
        $this->CI->output->set_status_header('404');
		echo $this->CI->template->build(config_item('admin').'/errors/error_404');
		exit(4);
	}
}