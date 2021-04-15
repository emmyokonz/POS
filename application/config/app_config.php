<?php
define('SUCCESS','200');
define('ERROR','201');

$config['site_name'] = 'Site Name';
$config['tag_line']  = 'tag Line';

$config['suk_prefix']  = 'PRO-'; //what should be attached to the product suk example: PRO-832973
$config['decimal_places']  = 2; //Number of decimal places
$config['currency']  =html_entity_decode("&#8358;"); //country currency to be used.
$config['reordering_point']  = 0; //When the stock is considered low.
$config['allow_manual_price']  = TRUE; //Allow staffs to input price manually.
$config['branch_number']  = '101'; //Branch number to identify each invoice.
$config['reordering_point']  = '120'; //The level where thr product is considered low.
$config['transaction_key']  = [
	'1'=>'Sales',
	'2'=>'Credit Memo',
	'3'=>'Purchases',
	'4'=>'Returns',
	'5'=>'Payments',
	'6'=>'transfer',
]; //country currency to be used.
$config['invoice_type']  = [
	'sales_cart'=>'INV',
	'credit_memo'=>'CM',
	'purchase'=>'PO',
	'return'=>'RET',
]; //Invoice type code for generating invoices number.

$config['transaction_type']  = [
	'sales_cart'=>'1',
	'credit_memo'=>'2',
	'purchase'=>'3',
	'return'=>'4',
	'cheque'=>'5',
	'deposit'=>'6',
	'transfer'=>'7',
]; //Transaction type to know the cart to load.

$config['invoice_preview_type']  = [
	'1'=>'INVOICE',
	'2'=>'CREDIT MEMO',
	'3'=>'PURCHASE ORDER',
	'4'=>'RETURN',
]; //Transaction type to know the cart to load.

$config['title_breadcrumb_seperator'] = ' &raquo; '; //the separator of page title and app name if the application name is enabled to show'
$config['title_show_app_name'] = TRUE;  //if the application name can show on the title
$config['site_name_seperator_from_title'] = " &mdash; ";  //if the application name can show on the title
$config['results_per_page'] = 50;

$config['default_user_action'] = 'read'; //the minimum action a group can perform if no action was selected.
$config['default_group_permission_id'] = 1; //the default permission every group must have.
$config['default_group_permission_name'] = 'dashboard'; //the default permission name every group must have.

$config['default_layout'] = 'default'; // set the layout to be used by the application


$config['top_menus'] = [
	'expenses'=>['name'=>'Expenses','link'=>'expenses/new'],
	'transfer'=>['name'=>'Transfer Funds','link'=>'payments/transfer'],
	'make_payment'=>['name'=>'Make Payment','link'=>'payments/make_payment'],
	'receive_payment'=>['name'=>'Receive Payment','link'=>'payments/receive_payment'],
]; // the top menu links. Can add more to this list 

$config['tables'] = [
	'perms'				=>'permissions',
	'grps_perms'		=>'groups_permissions',
	'users_actions'		=>'user_actions',
	'settings'			=>'settings',
	'activities'		=>'activities',
	'products'			=>'item_header',
	'product_metadata'	=>'item_metadata',
	'items'				=>'item_header',
	'categories'		=>'item_header',
	'transaction_details'		=>'transaction_details',
	'transaction_header'		=>'transaction_header',
	'people_header'		=>'people_header',
	'people'		=>'people_header',
	'people_activity'		=>'people_activities',
	'people_metadata'		=>'people_metadata',
	'account_header'		=>'account_header',
	'account_metadata'		=>'account_metadata',
	'account_activity'		=>'account_activities',
	'account_types'		=>'account_types',
];

$config ['system_help_email'] = 'help@site.com';
$config['admin_email']                = "admin@example.com"; // Admin Email, admin@example.com

$config['permissions_to_all']=['account','dashboard'] ;//those permissions that can be used by all.
$config['default_settings_tab']='general' ;//The settings tab to load when non is provided.

$config['activity_limit'] = 20;

$config['contact_form_subject'] = 'Message from contact form' ;
$config['cc'] = 'admin@haselgroup.com' ;

$config['track_visitor'] = true; 
$config['hit_time_to_live'] = 864000; //Defines how many seconds a hit should be rememberd for
$config['vistors_hit_purge_time'] = 60*60*24*30; //how many seconds a visitor record will last  in the server

$config['display_name'] = 'username';//list the table fields to use as the display name like first_name,last_name

/****************************************************
* 													*
************ Core Application Settings **************
* 													*
****************************************************/

/*
	Editing the information below can have significant negative side effects.
	Some of this information is used for the update process.
 */


$config['app_version'] 	= '1.0.1';
$config['app_author']	= 'Ecosooft Applications';
$config['app_email']	= 'hello@ecosooftpos.com';
$config['app_website']	= 'http://www.ecosooftpos.com';
$config['app_name']		= 'EcosooftPOS';
$config['app_developer']		= 'Techcoderr';
$config['app_developer_address']		= 'http://www.techcoderr.com';

// API endpoints

// returns current release 
// version number
$config['app_updates_url']			= 'https://updates.ecosooftpos.com/current/';

// returns current release 
// version files
$config['app_update_download_url']	= 'https://updates.ecosooftpos.com/current/download';

// returns list of available themes 
// for the current release of ecosooft.
$config['app_themes_url']			= 'https://addons.ecosooftpos.com/api/themes/';
