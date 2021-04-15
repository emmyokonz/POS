<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/*
|--------------------------------------------------------------------------
| References to all AJAX controllers' methods or the controller itself
|--------------------------------------------------------------------------
|
| Based on Jorge's solution: https://stackoverflow.com/a/43484330/6225838
| Key: controller name
| Possible values:
| - array: method name as key and boolean as value (TRUE => IS_AJAX)
| - boolean: TRUE if all the controller's methods are for AJAX requests
|
*/
$config[strtolower('ajax')] = [ //controller name should be all in small case
//	enter only ajax methods only, seperated with comma.
  	'add_to_cart','show_cart','load_cart','delete_cart','update_cart','check_cart','add_category_ajax',
];