<?php

class App
{

	function __construct()
	{

		// initialize db tables data
		$this->tables = $this->config->item('tables');

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

	public function update_settings($data = NULL, $tab)
	{

		if ($data == NULL)
		{
			return FALSE;
		}
//				echo " < pre > "; print_r($tab);exit;
		$this->db->trans_start();
		foreach ($data as $key => $settings_value)
		{
			$this->db
			->set('value',$settings_value)
			->where(['name'=>$key,'tab'=>$tab])
			->update($this->tables['settings']);
		}
		if ($this->db->trans_status())
		{
			$this->db->trans_commit();
			return TRUE;
		}
		else
		{
			$this->db->trans_rollback();
		}

		return FALSE;
	}
	
	public function get_db_config()
	{
		
		$settings = $this->get_settings_list('get_db_config');
		
		foreach($settings as $setting)
		{
			if($setting->value == 'true' || $setting->value == 'false')
			{
				// convert to true bool
				$bool_value = filter_var($setting->value, FILTER_VALIDATE_BOOLEAN);

				// set the value
				$this->config->set_item($setting->name, $bool_value);
				
//				$_settings[$setting->name] = $bool_value;
			}
			else
			{
				$this->config->set_item($setting->name, $setting->value);
//				$_settings[$setting->name] = $setting->value;
			}
		}
	}

	function get_settings_list($tabs = FALSE)
	{
		if($tabs == FALSE)
		{
			return $this->db->where('tab',config_item('default_settings_tab'))->get($this->tables['settings'])->result();
		}
		if ($tabs == 'get_db_config') {
			return $this->db->select('name,value')->get($this->tables['settings'])->result();
		}
		$tab_settings = $this->db->where('tab',$tabs)->get($this->tables['settings'])->result();

		return $tab_settings;
	}

	function settings_tab()
	{
		return $this->db->select("distinct(tab)")->get($this->tables['settings'])->result_array();
	}

	private function is_settings_options_dynamic($dynamic)
	{
		return $dynamic;
	}

	private function _radio($name,$current_val,$options = NULL,$dynamic = FALSE)
	{
		$radio = '';
		if (!empty($options))
		{
			if ($this->is_settings_options_dynamic($name)) {

				$options = $this->dynamic_config_options($name,$options);

			}
			$options_arr = explode("|", $options);

			foreach ($options_arr as $option)
			{
				$parts   = explode('=', $option);
				$checked = ($current_val == $parts[1]) ? TRUE : FALSE;
				$data = [
					'name' 		=> $name,
					'id'		=> $name,
					'value'		=> $parts[1],
					'class'		=> 'form-control',
					'checked'	=> $checked,
				];

				//Edit this to suit your template.
				$radio .= "<p class='radio radio-info p-r-20'>".form_radio($data). form_label($parts[0],$name).'</p>';
			}
			return $radio;
		}
	}

	private function _dropdown($name,$current_val,$options = NULL,$dynamic = FALSE)
	{
		$dropdown = '';
		// $options not empty?
		if (!empty($options)) {
			if ($this->is_settings_options_dynamic($dynamic)) {

				$options = $this->dynamic_config_options($name,$options);

			}
			// explode the first bit on the pipe 10 = 10 | 20 = 20 produces array([0] 10 = 10, [1] 20 = 20)
			$options_arr = explode("|", $options);

			// foreach of those exploded array items
			foreach ($options_arr as $option) {
				// explode again on the = sign 10 = 10 produces array([0] 10, [1] 10)
				$parts = explode('=', $option);

				$form_opts[$parts[1]] = $parts[0];

				// if they've tried to submit the new value but validation failed, we'll repopulate the value here.
				if ($this->input->post()) {
					// set the $current_val to the user's input
					$current_val = $this->input->post($name);
				}
			}
		}
		return form_dropdown($name, $form_opts, $current_val, 'class="form-control" id="' . $name . '"');
	}

	protected function dynamic_config_options($name,$options)
	{
		//lets get the options(class and method) given
		$options_arr = explode('|',$options);

		//lets check if the class exits or loaded.
		if (!$this->load->is_loaded($options_arr[0])) {
			$this->load->library($options_arr[0]);
		}

		//lets return the result as object.
		$options = $this->
		{
			$options_arr[0]
		}->
		{
			$options_arr[1]
		}();

		//lets force it to be array.
		//		if(!array($options)) $options = array($options);

		//lets get the fileds to fetch form the third perimeter
		$fields      = explode(',',$options_arr[2]);
		$options_str = '';
		foreach ($options as $result) {
			$options_str .= $result->
			{
				$fields[1]
			}.'='.$result->
			{
				$fields[0]
			}.'|';
		}

		//		print_r(rtrim($options_str,' | '));exit;
		return rtrim($options_str,'|');
	}

	public function build_form_fields($field_type,$name,$current_val,$options = NULL,$dynamic = FALSE)
	{

		if ($field_type == 'radio')
		{
			return $this->_radio($name,$current_val,$options );
		}
		elseif ($field_type == 'dropdown') {

			return $this->_dropdown($name,$current_val,$options );

		}
		elseif ($field_type == 'text') {
			// if they've tried to submit the new value but validation failed, we'll repopulate the value here.
			if ($this->input->post()) {
				// set the $current_val to the user's input
				$current_val = set_value($name);
			}
			return form_input($name, $current_val, 'class="form-control" id="' . $name . '"');
		}
		elseif ($field_type == 'textarea') {
			// if they've tried to submit the new value but validation failed, we'll repopulate the value here.
			if ($this->input->post()) {
				// set the $current_val to the user's input
				$current_val = set_value($name);
			}
			return form_textarea($name, $current_val, 'class="form-control" id="' . $name . '"');
		}
		// return default failure
		return false;
	}

	public function all_objects($type = FALSE)
	{
		$widgets = $this->db
						->where('type',$type)
						->get($this->tables['objs'])
						->result();
						
		return $widgets;
	}
	
	public function activate_widget($id)
	{
		if(!$id){
			return FALSE;
		}
		
		return $this->db->where('id',$id)->set('active',1)->update($this->tables['objs']);
	}
	
	public function deactivate_widget($id)
	{
		if(!$id){
			return FALSE;
		}
		
		return $this->db->where('id',$id)->set('active',0)->update($this->tables['objs']);
	}
	
	function widgets($position = FALSE)
	{
		return $this->widgets->get_widgets($position);
	}
	
	public function get_active_theme($is_admin='0')
	{
		return $this->db->where('is_active', 1)->where('is_admin', $is_admin)->limit(1)->get('templates')->row();
	}
	
	public function send_email($to,$reply_to, $subject, $message, $cc=false, $bcc=false)
	{
		$this->load->library('email');

		//set up the email config 
		$mail_protocol = $this->config->item('mail_protocol');

		// protocol
		$config['protocol'] = $mail_protocol;

		// we switch on $mail_protocol so we
		// can add additional config items 
		// as the protocol changes
		switch ($mail_protocol) {
			// the simple mail protocol
			case 'mail':
				// we don't need to do anything for mail...
				break;

			// smtp... 	
			case 'smtp':
				$config['smtp_host'] = $this->config->item('smtp_host');
				$config['smtp_user'] = $this->config->item('smtp_user');
				$config['smtp_pass'] = $this->config->item('smtp_pass');
				$config['smtp_port'] = $this->config->item('smtp_port');
				$config['smtp_crypto'] = $this->config->item('smtp_crypto');
				break;

			// lastly, sendmail
			case 'sendmail':
				//The server path to Sendmail. Usually '/usr/sbin/sendmail'
				$config['mailpath'] = $this->config->item('sendmail_path');
				break;

			// default is 'mail'
			default:
				// $mail_protocol ended up being something 
				// other than the 3 we check for, so we override
				// whatever it was and go with 'mail'
				$config['protocol'] = 'mail';
				break;
		}
		
		// the rest of the config items we don't
		// need to worry about which protocol the
		// site is using...
		$config['charset'] = 'iso-8859-1';
		$config['wordwrap'] = TRUE;
		$config['useragent'] = 'Trakonet v3';
		$config['mailtype'] = 'html';
		
		

		// init and let's send some email
		$this->email->initialize($config);

		// from db settings
		$this->email->from($this->config->item('server_email'), $this->config->item('site_name'));

		// set who it's going to...
		$this->email->to($to);
		
		$this->email->reply_to($reply_to);

		// if $cc
		if ($cc)
		{
			$this->email->cc($cc);
		}
		
		// if $bcc
		if ($bcc)
		{
			$this->email->bcc($bcc);
		}

		// set the subject
		$this->email->subject($subject);
		
		// set the message...
		$this->email->message($message);

		// and off we go
		/*if (!$this->email->send())
		{
			$this->email->print_debugger();
		}*/
		return true;

	}
	
	function contact_form_data($data){
		$form_data = $this->_filter_data($this->tables['contacts'] , $data);
		foreach($form_data as $dirty_dirty)
		{
			strip_tags(htmlspecialchars($dirty_dirty,ENT_QUOTES|ENT_HTML5));
			$this->purify($dirty_dirty);
		}
		print_r($form_data);exit;
		if(!class_exists('user_agent')){$this->load->library('user_agent');}
		$data =[
			'metadata' => json_encode([
				'ip_address'=>$this->input->ip_address(),
				'user_agent'=>$this->input->user_agent(),
//				'browser'=>$this->user_agent->browser(),
				'time'=>time(),
			])
		];
		
		$data = array_merge($form_data , $data);
		if($data['email']=='' && $data['message']==''){
			return FALSE;
		}
		$this->db->trans_begin();
		$send = $this->send_email(config_item('server_mail'),$data['email'],config_item('contact_form_subject'),config_item('cc'));
		
		if($send && $this->db->insert('contacts',$data) && $this->db->trans_status() == TRUE ){
			
			$this->db->trans_commit();
				
		}else
		{
			$this->db->trans_rollback();
			return FALSE;
		}
		
		return true;
	}
	
	/**
	 * @param string $table
	 * @param array  $data
	 *
	 * @return array
	 */
	function _filter_data($table, $data)
	{
		$filtered_data = [];
		$columns = $this->db->list_fields($table);

		if (is_array($data))
		{
			foreach ($columns as $column)
			{
				if (array_key_exists($column, $data))
					$filtered_data[$column] = $data[$column];
			}
		}

		return $filtered_data;
	}
	
	/**
	 * create_nonce
	 *
	 * Creates a cryptographic token tied to the selected action, user,
	 * user session id and window of time.
	 *
	 * @author 	Kader Bouyakoub
	 * @link 	https://goo.gl/wGXHO9
	 * @since 	1.4.0
	 *
	 * @access 	public
	 * @param 	mixed 	$action 	Scalar value to add context to the nonce.
	 * @return 	string 	The generated token.
	 */
	public function create_nonce($action = -1)
	{
		// Prepare an instance of CI object.
		$CI =& get_instance();

		// Get the current user's ID.
		/*$uid = (false !== $user = $CI->ion_auth_model->user()) 
			? $user->id
			: apply_filters('nonce_user_logged_out', 0, $action);*/
		$uid = $this->session->userdata('user_id');
		// Make sure to get the current user session's ID.
		(class_exists('CI_Session', false)) OR $CI->load->library('session');
		$token = session_id();
		$tick  = $this->nonce_tick();

		return substr($this->_nonce_hash($tick.'|'.$action.'|'.$uid.'|'.$token), -12, 10);
	}

	// ------------------------------------------------------------------------

	/**
	 * verify_nonce
	 *
	 * Method for verifying that a correct nonce was used with time limit.
	 * The user is given an amount of time to use the token, so therefore, since
	 * the UID and $action remain the same, the independent variable is time.
	 *
	 * @author 	Kader Bouyakoub
	 * @link 	https://goo.gl/wGXHO9
	 * @since 	1.4.0
	 *
	 * @access 	public
	 * @param 	string 	$nonce 		The nonce that was used in the action.
	 * @param 	mixed 	$action 	The action for which the nonce was created.
	 * @return 	bool 	returns true if the token is valid, else false.
	 */
	public function verify_nonce($nonce, $action = -1)
	{
		// Prepare an instance of CI object.
		$CI =& get_instance();

		// Get the current user's ID.
		/*$uid = (false !== $user = $CI->auth->user()) 
			? $user->id
			: apply_filters('nonce_user_logged_out', 0, $action);*/
		
		$uid = $this->session->userdata('user_id');
		// No nonce provided? Nothing to do.
		if (empty($nonce))
		{
			return false;
		}

		// Make sure to get the current user session's ID.
		(class_exists('CI_Session', false)) OR $CI->load->library('session');
		$token = session_id();
		$tick  = $this->nonce_tick();

		// Prepare the expected hash and make sure it equals to nonce.
		$expected = substr($this->_nonce_hash($tick.'|'.$action.'|'.$uid.'|'.$token), -12, 10);
		return ($expected === $nonce);
	}

	// ------------------------------------------------------------------------

	/**
	 * nonce_tick
	 *
	 * Method for getting the time-dependent variable used for nonce creation.
	 * A nonce has a lifespan of two ticks, it may be updated in its second tick.
	 *
	 * @author 	Kader Bouyakoub
	 * @link 	https://goo.gl/wGXHO9
	 * @since 	1.4.0
	 *
	 * @access 	public
	 * @param 	none
	 * @return 	float 	Float value rounded up to the next highest integer.
	 */
	public function nonce_tick()
	{
		$nonce_life = 2; //apply_filters('nonce_life', DAY_IN_SECONDS);
		return ceil(time() / ($nonce_life / 2));
	}

	// ------------------------------------------------------------------------

	/**
	 * _nonce_hash
	 *
	 * Method for hashing the given string and return the nonce.
	 *
	 * @author 	Kader Bouyakoub
	 * @link 	https://goo.gl/wGXHO9
	 * @since 	1.4.0
	 *
	 * @access 	protected
	 * @param 	string
	 * @return 	string
	 */
	protected function _nonce_hash($string)
	{
		// We make sure to use the encryption key provided.
		$salt = config_item('encryption_key');
		(empty($salt)) && $salt = 'CoDEiGniTrR SkELetON nOnCe SaLt';
		return hash_hmac('md5', $string, $salt);
	}
	
	function purify($dirty_text=NULL)
	{
		require_once APPPATH.'third_party/htmlpurifier/library/HTMLPurifier.auto.php';
		$config = HTMLPurifier_Config::createDefault();
		$config->set('HTML.Doctype', 'XHTML 1.0 Strict');
		$config->set('AutoFormat.RemoveEmpty',true);
		$config->set('AutoFormat.RemoveSpansWithoutAttributes',true);
		$config->set('Core.CollectErrors',true);
		$config->set('Attr.AllowedClasses','special');
		
		$purifier = new HTMLPurifier($config);
		    
	    $purified = $purifier->purify("[removed]alert();[removed]");
		$fg = preg_replace('(removed|[|]|;)',' ', $purified);
		return str_replace(['[',']'],'',$fg);
	}

}