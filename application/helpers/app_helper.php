<?php

if(!function_exists('generate_unique_invoice_code')){
	/**
	* 
	* @param string the table that need the unique code $table_name
	* @param string the name of the field that reqired a unique value $field_name
	* @param integer the lenght of the unigue code $len
	* 
	* @return return a unique id
	*/
	function generate_unique_invoice_code($invoice_type='sales_cart', $table_name, $field_name='id', $len=10)
	{
		$ci=&get_instance();
		$ci->load->helper('string');
		$invoice_prefix=config_item('invoice_type')[$invoice_type];
		$datestring = date('Ymd');
		$branchNumber = config_item('branch_number');
//		$sales_no = $invoice_prefix.'-'.$datestring.$branchNumber.'1';
		$last_invoice_no_query = $ci->db->select($field_name)->order_by('id','DESC')->limit(1,0)->get($table_name);
		if ($last_invoice_no_query->num_rows() == 0) {
			$numbering = 1;
		}else{
			$last_split = (explode("-",$last_invoice_no_query->row()->{$field_name}));
			$numbering = my_number_format(end($last_split)) + 1;
		}
		
		$trans_no = $invoice_prefix.'-'.$branchNumber."-".$datestring."-".$numbering;
		/*echo $trans_no;exit;
		$unique_id=$invoice_prefix.strtoupper(random_string('alnum',$len));
		while ($ci->db->where($field_name,$unique_id)->get($table_name)->num_rows()>0) {
			$unique_id=$invoice_prefix.strtoupper(random_string('alnum',$len));
		}*/
		return $trans_no;
	}
}

if(!function_exists('generate_unique_product_code')){
	/**
	* 
	* @param string the table that need the unique code $table_name
	* @param string the name of the field that reqired a unique value $field_name
	* @param integer the lenght of the unigue code $len
	* 
	* @return return a unique id
	*/
	function generate_unique_product_code($table_name, $field_name='id', $len=10)
	{
		$ci=&get_instance();
		$ci->load->helper('string');
		$unique_id=config_item('suk_prefix').strtoupper(random_string('alnum',$len));
		while ($ci->db->where($field_name,$unique_id)->get($table_name)->num_rows()>0) {
			$unique_id=config_item('suk_prefix').strtoupper(random_string('alnum',$len));
		}
		return $unique_id;
	}
}

function my_number_format($string,$format=FALSE,$currency=FALSE){
	$string = trim($string);
	if ($format==TRUE) {
		$return = number_format(floatval(preg_replace('/[^0-9.-]/','',$string)),config_item('decimal_places'));
		if ($currency == TRUE) {
			$return = config_item('currency').$return;
		}
		return( $return);
	}
	
	return floatval(preg_replace('/[^0-9-]/','',$string));
}

function cleanString($string , $replace_space = FALSE , $replace_space_with ='-'){
	$string = strip_tags($string);
	$string = strip_image_tags($string);
	$string = remove_invisible_characters($string);
	$string = preg_replace('/[^A-Za-z0-9 @.\-]/','',$string);
	$string = preg_replace('/-+/','-', $string);
	if ($replace_space ==TRUE) {
		$string = str_replace(' ',$replace_space_with, $string);
	}
	$string = trim($string);
	
	return $string;
}

if(!function_exists('generate_unique_code')){
	/**
	* 
	* @param string the table that need the unique code $table_name
	* @param string the name of the field that reqired a unique value $field_name
	* @param integer the lenght of the unigue code $len
	* 
	* @return return a unique id
	*/
	function generate_unique_code($table_name,$field_name='id',$len=10){
		$ci=&get_instance();
		$ci->load->helper('string');
		$unique_id=random_string('alnum',$len);
		while($ci->db->where($field_name,$unique_id)->get($table_name)->num_rows()>0){
			$unique_id=random_string('alnum',$len);
		}
		return $unique_id;
	}
}

function title()
{
	$_app_name = (config_item('title_show_app_name')?config_item('site_name_seperator_from_title') . config_item('site_name'):'');
	
	if ( count($args = func_get_args()) == 1 && $args[0] !== '')
	{
//		print_r($args);exit;
		is_array($args[0]) && $args = $args[0];
		
		$title = rtrim(implode(config_item('title_separator'), $args),' '.config_item('title_separator')).$_app_name;
	}
	else
	{
		//if $title is empty we try to guess the title.
		$title = _guess_title().$_app_name;
	}
	
	return $title;
}

function _guess_title()
{

	get_instance()->load->helper('inflector');

	$_title = null;

	if (($method = get_instance()->router->method) == 'index')
	{

		$method = get_instance()->router->class;

	}
	else
	{

		$method = get_instance()->router->class.' '. config_item('title_separator').$method;

	}

	$_title = humanize($method);

	return $_title;

}

function page_title($title = NULL)
{
	$_app_name = (config_item('title_show_app_name')?config_item('site_name_seperator_from_title') . config_item('site_name'):'');
	
	return str_replace($_app_name,'',title($title));
}

function set_breadcrumb($title=NULL,$url=NULL){
	$ci=& get_instance();
	
	$class=$ci->router->class;
	$ci->breadcrumbs->push(humanize($class),(config_item('admin').$class));
	
	if($title!==NULL && $url !== NULL){
		$ci->breadcrumbs->push(ucwords($title), (config_item('admin').$url));
		return;
	}elseif($ci->template->page_title!==NULL){
		$ci->breadcrumbs->push(ucfirst($ci->template->page_title), (config_item('admin').$ci->uri->segment(2)));
		return;
	}else{
		$ci->breadcrumbs->push(ucfirst(lang($ci->uri->segment(3))), (config_item('admin').$class.'/'.$ci->uri->segment(3)));
		return;
	}
	
	
}

if (!function_exists('invoice_date')) {

	function invoice_date($unix_time=NULL)
	{
		return date(config_item('date_format'),$unix_time);
	}
}

if(!function_exists('my_full_time_span')){
	
	function my_full_time_span($unix_time=NULL){
		
		if(NULL==$unix_time || empty($unix_time)){
			return NULL;
		}
		$day=NULL;
		$ci=&get_instance();
		$ci->load->helper('date');
		if(date('Y-m-d',$unix_time)==date('Y-m-d')){
			$day='Today';
		}
		if(date('Y-m-d',$unix_time)==date('Y-m-d',strtotime('yesterday'))){
			$day='Yesterday';
		}
		if(date('Y-m-d',$unix_time)==date('Y-m-d',strtotime('tomorrow'))){
			$day='Tomorrow';
		}
		if(date('Y',$unix_time)==date('Y',strtotime('this year'))){
			if($day!==NULL){
				return $day.', '.date('h:i a',$unix_time);
			}
			if(date('Y-m-d',$unix_time)==date('Y-m-d',strtotime('this month'))){
//				return date('D jS, h:i a',$unix_time);
				return date('D jS '.((config_item('show_time_in_date')==1)?config_item('time_format'):''),$unix_time);
			}else{
//				return date('M j, h:i a',$unix_time);
				return date('M j '.((config_item('show_time_in_date')==1)?config_item('time_format'):''),$unix_time);
				
			}
		}
		return date('M j, Y',$unix_time);
	}
}

if(!function_exists('get_display_name'))
{
	function get_display_name($user_id=NULL){
		
		$ci=&get_instance();
		
		$user_id=($user_id==NULL)?$ci->session->userdata('user_id'):xss_clean($user_id);
		
		$return ='Anonymous';
		if(($name=get_user_details(config_item('display_name'),$user_id))!==FALSE){
			
			$fields = config_item('display_name');
			
			//lets check if more than 1 fields where provided
			if(!is_array($fields)){
				$fields = array($fields);
				if(strpos($fields[0],',')){
					$fields = explode(',',$fields[0]);
				}
			}
			foreach($fields as $field){
				$output[] = $name->{$field};
			}	
			$return=ucfirst(implode(' ',$output));
		}
		
		return $return;
		
	}
}

if (!function_exists('get_item_name'))
{
	function get_item_name($item_id=NULL)
	{
		
		$ci=&get_instance();
		if ($item_id == NULL)
			$return ='UNKNOWN';
		if (!class_exists('product_model'))
			$ci->load->model('product_model');
		$query = $ci->product_model->get_product($item_id);
		if ($query->num_rows() >0) {
			$return = $query->row()->name;
		}else{
			$return ='UNKNOWN';
		}
		
		return $return;
		
	}
}

function display_name($user_id=NULL)
{
	echo get_display_name($user_id);
}

if(!function_exists('get_user_details')){
	function get_user_details($details=null,$user_id=NULL){
		
		$ci=&get_instance();
		
		$user_id=($user_id==NULL)?$ci->session->userdata('user_id'):xss_clean($user_id);
		
		$return =NULL;
		$return=$ci->ion_auth->select($details)->user($user_id)->row();
		return $return;
		
	}
}

/**
* 
* @string undefined $name
* 
* @return
*/
if(!function_exists('get_theme_details')){
	function theme_details(){
		
		$ci=&get_instance();
		$ci->load->model('themes_m');
		
		$return = $ci->themes_m->get_current_theme_details();
		return $return;
		
	}
}

if(!function_exists('file_size_text')){
	function file_size_text($file_size){
		if($file_size<1024){
			return $file_size.'kb';
		}else{
			if(($file_size/1024)<1024){
				return ($file_size/1024).'mb';
			}else{
				return ($file_size/1024).'gb';
			}
		}
	}
}

if(!function_exists('trako_counter')){
	function trako_counter($counter){
		
		if($counter>99){
			return '99+';
		}
		if($counter>999){
			return '999+';
		}
		if($counter>999999){
			return round($counter/1000000,1).'M';
		}
		return $counter;
	}
}

if(!function_exists('uri_segment')){
	function uri_segment($segment=3){
		
		$ci=& get_instance();
		$return =$ci->uri->segment($segment);
		return $return;
	}
}


if(!function_exists('trako_message_notification')){
	function trako_message_notification(){
		
		$ci=& get_instance();
		$ci->load->model('Message_m');
		
		$data['unread_counter']=trako_counter($ci->Message_m->mail_counter(['trash'=>0,'mail_read'=>0,'recipient'=>$ci->ion_auth->get_user_id()]));
		$data['message']=$ci->Message_m->get_new_message_notification('message,id,mail_read,sender,time',['trash'=>0,'mail_read'=>0,'recipient'=>$ci->ion_auth->get_user_id()]);
		
		return $data;
	}
}

if(!function_exists('is_manager')){
	function is_manager(){
		
		$ci=& get_instance();
		
		return $ci->ion_auth->in_group(['manager']);
	}
}


if(!function_exists('is_admin')){
	function is_admin(){
		
		$ci=& get_instance();
		
		return $ci->ion_auth->is_admin();
	}
}

if(!function_exists('in_group')){
	/**
	* 
	* @param undefined $groups array()
	* 
	* @return bool
	*/
	function in_group($groups){
		
		$ci=& get_instance();
		
		return $ci->ion_auth->in_group($groups);
	}
}


function tabs($active_tab = FALSE,$permission = NULL)
{

	//if no permission is set use the current controller
	$permission || $permission = get_instance()->router->class;

	if (!array_key_exists($permission , config_item('tabs')))
	{

		return NULL;

	}
	if ($permission == 'settings')
	{
		return _build_settings_tabs(get_instance()->app->settings_tab(),$permission,$active_tab);
	}

	$_all_tabs = config_item('tabs')[$permission];

	return _build_tabs($_all_tabs,$permission,$active_tab);

}
function admin_create_btn(){
	
}
function _build_settings_tabs($tabs,$perm,$active_tab)
{
	//print_r($tabs);exit();
	$tab_build = '';
	//	lets start the tab building process you can change this to fit your need
	$tab_build .= "<ul class='nav nav-pills '>";
	//lets foreach the array to build each tab
//	print_r($tabs);exit;
	foreach ($tabs as $_tab)
	{
		$tab_build .= '<li class="'.((strtolower($active_tab) == strtolower($_tab['tab']))?'active':'').'">
		<a href="'.admin_url($perm).'/'.$_tab['tab'].'">
		<span class="visible-xs" title="'.$_tab['tab'].'">
		</span>
		<span class="hidden-xs">
		'.ucfirst($_tab['tab']).'
		</span>
		</a>
		</li>';

	}
	$tab_build .= "</ul>";

	return $tab_build;

}

function _build_tabs($tabs,$perm,$active_tab)
{

	$tab_build = '';
	//	lets start the tab building process you can change this to fit your need
	$tab_build .= "<ul class='nav nav-pills '>";
	//lets foreach the array to build each tab
	foreach ($tabs as $_tab=>$_tab_attr)
	{

		$tab_build .= '<li class="'.((strtolower($active_tab) == strtolower($_tab))?'active':'').'">
		<a href="'.admin_url($perm).(($_tab_attr[0] !== NULL)?'/'.$_tab_attr[0]:NULL).'">
		<span class="visible-xs" title="'.$_tab.'">
		<i class="'.($_tab_attr[1]).'">
		</i>
		</span>
		<span class="hidden-xs">
		'.ucfirst($_tab).'
		</span>
		</a>
		</li>';

	}
	$tab_build .= "</ul>";

	return $tab_build;

}

function has_permission($permission)
{
	return get_instance()->permissions->has_permission($permission);
}

function has_action($action = FALSE)
{
	if (!$action)
	{
		return $action;
	}
	return get_instance()->permissions->has_action($action);
}


function build_form($field_type,$name,$current_val,$options = NULL,$dynamic)
{

	return get_instance()->app->build_form_fields($field_type,$name,$current_val,$options,$dynamic);

}

function get_permission($id = FALSE)
{

	return get_instance()->permissions->get_permission($id);
}
function time_diff($date_in)
{
	$start_date = $date_in;
	$end_date   = date('Y-m-d H:i:s');

	$start_time = strtotime($start_date);
	$end_time   = strtotime($end_date);
	$difference = $end_time - $start_time;

	$seconds    = $difference % 60;            //seconds
	$difference = floor($difference / 60);

	$min        = $difference % 60;              // min
	$difference = floor($difference / 60);

	$hours      = $difference % 24;  //hours
	$difference = floor($difference / 24);

	$days       = $difference % 30;  //days
	$difference = floor($difference / 30);

	$month      = $difference % 12;  //month
	$difference = floor($difference / 12);

	$year       = $difference % 1;  //month
	$difference = floor($difference / 1);


	$result     = null;
	if ($year != 0) {
		if ($year == 1) {
			$result .= $year.' Year ';
		}
		else
		{
			$result .= $year.' Years ';
		}
	}
	if ($month != 0) {
		if ($month == 1) {
			$result .= $month.' Month ';
		}
		else
		{
			$result .= $month.' Months ';
		}
	}
	if ($days != 0) {
		if ($days == 1) {
			$result .= $days.' Day ';
		}
		else
		{
			$result .= $days.' Days ';
		}
	}
	if ($hours != 0) {
		if ($hours == 1) {
			$result .= $hours.' Hour ';
		}
		else
		{
			$result .= $hours.' Hours ';
		}
	}
	if ($min != 0) {
		if ($min == 1) {
			$result .= $min.' Minute ';
		}
		else
		{
			$result .= $min.' Minutes ';
		}
	}

	if ($result == null) {
		return 'Just Now';
	}
	return $result.' ago';
}

function time_span($time)
{
	$time_diff = (int)time() - (int)$time;

	if ($time_diff < 60)
	{
		$return = 'Just Now';
	}
	else
	if (($diff = floor($time_diff / 60)) < 60)
	{
		$return = ($diff == 1) ? $diff.' Minute ago' : $diff.' Minutes ago';
	}
	else
	if(($diff = floor($diff / 60)) < 24)
	{
		
		$return = (($diff > 3) ? 'Today '.date('h:i a',$time) : ($diff > 1)) ? $diff.' Hours ago': $diff.' Hours ago' ;	
	}
	else
	if((date('d') - date('d',$time)) == 1)
	{
		
		$return = 'yesterday '.date('h:i a',$time);	
	}
	else
	if(date('M',$time) == date('M'))
	{
		
		$return = date('D d\' h:i a',$time);	
	}
	else
	if(date('y',$time) == date('y'))
	{
		
		$return = date('M d\' h:i a',$time);	
	}
	else
	{
		$return = date('d, M\' Y h:i a',$time);
	}
	

	return $return;
}
//-------------------------------------------------------------------------------------
//widget helper
/**
* string name of widget to load.
* 
* @return
*/
if(!function_exists('get_a_widget')){
	function get_a_widget()
	{
		return call_user_func_array(array(get_instance()->widgets , 'get_a_widget') , func_get_args());
	}
}

/**
* Phone number validator
* Author:  Oladapo Siyanbola
* 
* This function validates Nigerian phone numbers with the country code
* 
*
* @param type $input phone number gotten from user
* @return type returns true
*/
function validate_phone_number($input) {
    $pattern = '/^[0-9]{11}/';
    if(preg_match($pattern,$input)){
    	
        return true; 
        
    }
	
	return FALSE;
}

if ( ! function_exists('label_condition'))
{
	/**
	 * This is a dummy function used to display Boostrap labels
	 * depending on a given condition.
	 *
	 * @since 	1.0.0
	 * @since 	1.3.3 	Fixed issue with translation.
	 *
	 * @param 	bool 	$cond 	The conditions result.
	 * @param 	string 	$true 	String to output if true.
	 * @param 	string 	$false 	String to output if false.
	 * @return 	string
	 */
	function label_condition($cond, $true = 'lang:CSK_YES', $false = 'lang:CSK_NO')
	{
		// Prepare the empty label.
		$label = '<span class="badge badge-%s">%s</span>';

		// Should strings be translated?
		if (sscanf($true, 'lang:%s', $true_line) === 1)
		{
			$true = __($true_line);
		}
		if (sscanf($false, 'lang:%s', $false_line) === 1)
		{
			$false = __($false_line);
		}

		return ($cond === true)
			? sprintf($label, 'success', $true)
			: sprintf($label, 'danger', $false);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('fa_icon'))
{
	/**
	 * fa_icon
	 *
	 * Function for generating FontAwesome icons.
	 *
	 * @author 	Kader Bouyakoub
	 * @link 	https://goo.gl/wGXHO9
	 * @since 	1.4.0
	 *
	 * @access 	public
	 * @param 	none
	 * @return 	void
	 */
	function fa_icon($class = '',$space = NULL)
	{
		if(isset($space)){
			return "<i class=\"fa fa-{$class}\"></i>";
		}
		return "<i class=\"fa fa-fw fa-{$class}\"></i>";
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('submit_button'))
{
	/**
	 * submit_button
	 *
	 * Function display a submit button.
	 *
	 * @author 	Kader Bouyakoub
	 * @link 	https://goo.gl/wGXHO9
	 * @since 	2.0.0
	 *
	 * @param 	string 	$text
	 * @param 	mixed 	$type
	 * @param 	string 	$name
	 * @param 	bool 	$wrap
	 * @param 	array 	$attrs
	 * @return 	string
	 */
	function submit_button($text = '', $type = 'primary btn-sm', $name = 'submit', $wrap = true, $attrs = '')
	{
		// Make sure to explode types if string.
		is_array($type) OR $type = explode(' ', $type);

		// Array of Skeleton available button.
		$types = _dashboard_buttons();

		$classes = array('btn');
		foreach ($type as $t) {
			if (('secondary' === $t OR 'btn-secondary' === $t)
				OR ('default' === $t OR 'btn-default' === $t)) {
				continue;
			}

			$classes[] = in_array($t, $types) ? 'btn-'.$t : $t;
		}

		if (function_exists('array_clean')) {
			$classes = array_clean($classes);
		} else {
			$classes = array_unique(array_filter(array_map('trim', $classes)));
		}

		// See if we provide a size.
		if (false !== ($i = array_search('tiny', $classes))) {
			$classes[$i] = 'btn-xs';
		} elseif (false !== ($i = array_search('small', $classes))) {
			$classes[$i] = 'btn-sm';
		} elseif (false !== ($i = array_search('large', $classes))) {
			$classes[$i] = 'btn-lg';
		}

		// Shall we use an icon?
		$icon = null;
		foreach ($classes as $k => $v) {
			if (1 === sscanf($v, 'icon:%s', $i)) {
				$icon = fa_icon($i);
				$classes[$k] = 'btn-icon';
				break;
			}
		}

		// Possibility to disable to wrap.
		if (false !== ($w = array_search('nowrap', $classes))) {
			$wrap = false;
			unset($classes[$w]);
		}

		// Add the default submit button.
		$attributes['type'] = 'submit';

		// Prepare button class.
		$attributes['class'] = implode(' ', $classes);

		/**
		 * Prepare text to be used.
		 * 1. If nothing provided, we use default "Save Changes".
		 * 2. If it starts with "lang:", we try to translate it.
		 * 3. If it starts with "config:" we try to get config item.
		 */
		if (empty($text)) { // Use default "Save Changes"
			$text = __('CSK_BTN_SAVE_CHANGES');
		} elseif (1 === sscanf($text, 'lang:%s', $line)) {
			$text = __($line);
		} elseif (1 === sscanf($text, 'config:%s', $item)) {
			$text = config_item($item);

			// In case the item was not found, we use default text.
			$text OR $text = __('CSK_BTN_SAVE_CHANGES');
		}

		empty($icon) OR $text = $icon.$text;

		// Use the $name as the default id unless provided in $attrs.
		$attributes['name'] = $name;
		$attributes['id']   = $name;
		if (is_array($attrs) && isset($attrs['id'])) {
			$attributes['id'] = $attrs['id'];
			unset($attrs['id']);
		}

		if (is_array($attrs) && ! empty($attrs)) {
			$attributes = array_merge($attributes, $attrs);
		}

		if (null === $icon) {
			$tag                = 'input';
			$attributes['type'] = 'submit';
			$attributes['value'] = $text;
		} else {
			$tag = 'button';
		}

		function_exists('html_tag') OR get_instance()->load->helper('html');

		$button = html_tag($tag, $attributes, $text);

		$output = $wrap ? '<div class="form-group">'.$button.'</div>' : $button;

		return $output;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('_dashboard_buttons'))
{
	/**
	 * _dashboard_buttons
	 *
	 * Function for returning an array of dashboard available buttons colors.
	 *
	 * @author 	Kader Bouyakoub
	 * @link 	https://goo.gl/wGXHO9
	 * @since 	2.0.0
	 *
	 * @param 	none
	 * @return 	array
	 */
	function _dashboard_buttons()
	{
		static $dashboard_buttons = null;

		if (null === $dashboard_buttons)
		{
			$dashboard_buttons = array(
				'add', 'apply',
				'black', 'blue', 'brown',
				'create',
				'danger', 'default', 'delete', 'donate',
				'green', 'grey',
				'info',
				'new',
				'olive', 'orange',
				'pink', 'primary', 'purple',
				'red', 'remove',
				'save', 'secondary', 'submit', 'success',
				'teal',
				'update',
				'violet',
				'warning', 'white',
				'yellow',
			);
		}

		return $dashboard_buttons;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('info_box'))
{
	/**
	 * Generates an info box
	 *
	 * @since 	2.0.1
	 *
	 * @param 	string 	$head
	 * @param 	string 	$text
	 * @param 	string 	$icon
	 * @param 	string 	$url
	 * @param 	string 	$color
	 * @return 	string
	 */
	function info_box($head = null, $text = null, $icon = null, $url = null, $color = 'primary')
	{
		$color && $color = ' bg-'.$color;

		// Opening tag.
		$output = "<div class=\"info-box{$color}\">";

		// Info box content.
		if ($head OR $text)
		{
			$output .= '<div class="inner">';
			$head && $output .= '<h3>'.$head.'</h3>';
			$text && $output .= '<p>'.$text.'</p>';
			$output .= '</div>';
		}

		// Add the icon.
		$icon && $output .= '<div class="icon">'.fa_icon($icon).'</div>';

		if ($url)
		{
			$output .= html_tag('a', array(
				'href'  => $url,
				'class' => 'info-box-footer',
			), __('CSK_BTN_MANAGE').fa_icon('arrow-circle-right ml-1'));
		}

		// Closing tag.
		$output .= '</div>';

		return $output;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('sanitize'))
{
	/**
	 * sanitize
	 *
	 * Function for sanitizing a string.
	 *
	 * @author 	Kader Bouyakoub
	 * @link 	https://goo.gl/wGXHO9
	 * @since 	1.4.0
	 *
	 * @param 	string 	$string 	The string to sanitize.
	 * @return 	string 	The string after being sanitized.
	 */
	function sanitize($string)
	{
		// Make sure required functions are available.
		$CI =& get_instance();
		(function_exists('strip_slashes')) OR $CI->load->helper('string');
		(function_exists('xss_clean')) OR $CI->load->helper('security');

		// Sanitize the string.
		return xss_clean(htmlentities(strip_slashes($string), ENT_QUOTES, 'UTF-8'));
	}
}

function my_previous_page(){
	$referrer = get_instance()->agent->referrer();
	if ($referrer == NULL) {
		$referrer = site_url();
		
	}
}