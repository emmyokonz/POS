<?php
defined('BASEPATH')or exit('No direct script allowed!');
/**
* Developed by Techcoderr Developers
*/
class Products extends MY_AdminController {
	function __construct() {
		parent::__construct();
		$this->load->model('product_model');
		$this->load->model('transaction_model');
		$this->data['top_actions'] = '';
		$this->data['__back'] = $_SERVER['HTTP_REFERER'];
	}
	
	function index(){
		parse_str($_SERVER['QUERY_STRING'], $get);
		
		// Custom $_GET appended to pagination links and WHERE clause.
		$_get  = null;

		// Filtering by search or anyother option?
		foreach (array('search','cats') as $filter) {
			if (isset($get[$filter])) {
				$_get[$filter]  = strval(xss_clean($get[$filter]));
				$filter_where[$filter] = strval(xss_clean($get[$filter]));
			}
		}
				
		//lets join the arrary of filtering with our custom where.
		if (!isset($filter_where)) {
			$filter_where = [];
		}
		$where =['item_type'=>1,'status => 1'];
		
		// Build the query appended to pagination links.
		(empty($_get)) OR $_get = '?'.http_build_query($_get);
		
		$this->load->library('pagination');
		$config['base_url'] = ('products'.$_get);
		$config['per_page'] = config_item('results_per_page');

		// Filtering by category?
		if (isset($filter_where['cats'])) {
			$category_id = $this->product_model->where('name',html_entity_decode($filter_where['cats']))->select('id')->get_products();
			$_where['category_id'] = ($category_id->num_rows()?$category_id->row()->id:NULL);
			$where = $where+$_where;
		}
//		print_r($filter_where);exit;
		if (!isset($filter_where['search'])) {
			$config['total_rows'] =  $this->product_model
			->where($where)
			->get_all(1)
			->num_rows();
		}else{
			$config['total_rows'] =  $this->product_model
			->where($where)
			->like('name',$filter_where['search'])
			->or_like('name',$filter_where['search'])
			->get_all(1)
			->num_rows();					
		}
		


		$offset = (isset($get['page'])) ? config_item('results_per_page') * ($get['page'] - 1) : 0;
		
		if(!isset($filter_where['search'])){
			$products = $this->product_model->where($where)->limit(config_item('results_per_page'))->offset($offset)->order_by('created_date','DESC')->get_all(1)->result();
			//lets count all active records
			$count = $this->product_model->select("1",false)->where($where)->get_all(1)->num_rows();
		}else{
			$products = $this->product_model->where($where)->like('name',$filter_where['search'])->or_like('name',$filter_where['search'])->limit(config_item('results_per_page'))->offset($offset)->order_by('created_date','DESC')->get_all(1)->result();
			//lets count all active records
			$count = $this->product_model->select("1",false)->where($where)->like('name',$filter_where['search'])->or_like('name',$filter_where['search'])->get_all(1)->num_rows();
		}

		$this->pagination->initialize($config);

		$this->data['pagination'] = $this->pagination->create_links();
		$this->data['count_records'] = $count;
		
		
		if (!empty($products) && is_array($products)) {
			foreach ($products as $product) {
				$product->product_name = html_tag(
				'a',
				['class'=>"text-left m-b-0",'href'=>admin_url('product/edit/'.$product->id)],
				$product->name
				);
				$product->quantity = $this->product_model->select('qty')->where('item_id',$product->id)->get_product_metadata()->row()->qty;

				$product->date = html_tag(
				'span',
				'',
				my_full_time_span($product->created_date)
				);

				$product->category = html_tag(
				'a',
				['href'=>admin_url('products'.($_get!==NULL?$_get.'&':'?').'cats='.get_item_name($product->category_id))],
				(get_item_name($product->category_id))
				);

				$update=NULL;
				$activate=NULL;
				if (has_action('update')) {
					$update = admin_anchor(
					'products/edit/'.$product->id,
					fa_icon('edit' , true).' '."<span class='hidden-xs hidden-sm'>Edit</span>",
					['class'=>'btn btn-sm btn-info']
					);
					if ($product->status == '0') {
						$activate = admin_anchor(
						'products/activate/'.$product->id,
						fa_icon('check' , true).' '."<span class='hidden-xs hidden-sm'>Activate</span>",
						['class'=>'btn btn-sm btn-success']
						);
					}
				}
				$delete=NULL;
				if (has_action('delete') && $product->status == 1) {
					$delete = admin_anchor(
					'products/delete/'.$product->id,
					fa_icon('trash-o', TRUE).' '."<span class='hidden-xs hidden-sm'>Delete</span>",
					[
						'class'=>'btn btn-sm btn-danger',
						'onclick'=>"return confirm('".lang('confirm_delete')."')"
					]
					);
				}
				$product->actions = $update. ' '.$delete.' '. $activate;
			}
		}
		
		
		$category = html_tag(
			'a',
			[
			'href'=>site_url(ADMIN.'products/categories'),
			'class'=>'btn btn-info btn-sm',
			],
			fa_icon('tags',TRUE).' '.html_tag('span',['class'=>'hidden-xs hidden-sm'],lang('btn_category'))
		);
		$new_product = NULL;
		
		if (has_action('create')) {
			$new_product = html_tag(
				'a',
				[
				'href'=>site_url(ADMIN.'products/new'),
				'class'=>'btn btn-danger btn-sm',
				],
				fa_icon('plus',TRUE). ' '.html_tag('span',['class'=>'hidden-xs hidden-sm'],lang('btn_new_product'))
			);
			
		}
		
		
		$this->data['top_actions'] = $category. ' ' . $new_product; 
		$this->data['products'] = $products; 
		$this->data['page_title'] = lang('product_list_title'); 
		$this->template->build('products/index',$this->data);
	}
	
	function new(){
		if (!has_action('create'))show_404();
		
		$this->data['new_category']=$this->template->partial('ajax_container');
		$this->form_validation->set_rules('name',lang('product_name_label'),'trim|required');
		$this->form_validation->set_rules('category_id',lang('product_category_label'),'trim|required');
		$this->form_validation->set_rules('description',lang('product_description_label'),'trim');
		$this->form_validation->set_rules('qty',lang('product_quantity_label'),'trim');
		$this->form_validation->set_rules('selling_price',lang('product_selling_label'),'trim|xss_clean');
		$this->form_validation->set_rules('cost_price',lang('product_cost_label'),'trim|xss_clean');
		
		if (!$this->form_validation->run()) {

			//pass the product data to session so as to retain it after redirect
			if (validation_errors() || $this->ion_auth->errors() || $this->session->flashdata('message')) {
				$this->session->set_flashdata($this->input->post());
				$this->session->set_flashdata('error',(validation_errors() ? validation_errors() : ($this->product_model->errors() ? $this->product_model->errors() : $this->session->flashdata('message'))));
				redirect(ADMIN."products/new",'refresh');
			}

			//prepare the form
			$this->data['product_name']=[
				'name' => 'name',
				'type' => 'text',
				'placeholder' => lang('product_name_label'),
				'required'=>'required',
				'class'   => 'form-control',
				'value'=> $this->session->flashdata('name'),
			];
			$categories = $this->product_model->select('id,name')->where(['item_type'=>2])->order_by('name','DESC')->get_products()->result();
			$option=[];
			foreach ($categories as $category) {
				$option[$category->id] = $category->name;
			}
			$option =[''=>lang('product_choose_category')]+$option;
			$this->data['product_category']=[
				'name' => 'category_id',
				'required'=>'required',
				'class'   => 'form-control',
				'id'   => 'productcategory',
				'data-container'=>"body",
				'options' => $option,
				'selected'=> $this->session->flashdata('category_id'),
			];

			$this->data['product_selling_price']=[
				'name' => 'selling_price',
				'type' => 'text',
				'placeholder' => lang('product_selling_label'),
				'class'   => 'form-control',
				'value'=> $this->session->flashdata('selling_price'),
			];

			$this->data['product_cost_price']=[
			'name' => 'cost_price',
				'type' => 'text',
				'placeholder' => lang('product_cost_label'),
				'class'   => 'form-control',
				'value'=> $this->session->flashdata('cost_price'),
			];

			$this->data['product_quantity']=[
				'name' => 'qty',
				'type' => 'text',
				'placeholder' => lang('product_quantity_label'),
				'class'   => 'form-control',
				'value'=> $this->session->flashdata('qty'),
			];
			
			$this->data['product_description']=[
				'name' => 'description',
				'type' => 'textarea',
				'placeholder' => lang('product_description_label'),
				'rows' => '3',
				'class'   => 'form-control textarea',
				'value'=> $this->session->flashdata('description'),
			];
			
			$this->data['page_title'] = lang('product_new_title');
			$this->template->build('products/new_product',$this->data);
		} else {

			$data = $this->input->post(array(
			'name',
			'category_id',
			'selling_price',
			'description',
			'cost_price',
			'qty',
			), true);

			$data['selling_price'] = ((strlen($data['selling_price']) < 1)?0:my_number_format($data['selling_price'],false));
			$data['cost_price'] = ((strlen($data['cost_price']) < 1)?0:my_number_format($data['cost_price'],false));
			$data['qty'] = ((strlen($data['qty']) < 1)?0:my_number_format($data['qty'],false));
			$data['category_id'] = (($data['category_id']==NULL)?2:$data['category_id']);
//			print_r($data);exit;

			if (($product_id=$this->product_model->save($data,'1')) == TRUE) {
				$this->session->set_flashdata('success',lang('product_add_success'));
				log_activity($this->session->userdata('user_id'),'added a new product ##'.$product_id);
				if ($this->input->post('submit', TRUE) == "saveandclose") {
					redirect(ADMIN.'products', 'refresh');
					exit;
				}else{
					redirect(ADMIN.'products/new', 'refresh');
					exit;
				}
				
			} else {
				$this->session->set_flashdata('error',($this->product_model->errors()=='')?lang('product_add_error'):$this->product_model->errors());
				redirect(ADMIN.'products/new', 'refresh');
				exit;
			}
		}
		
	}
	
	function edit($product_id = NULL)
	{
		if (!has_action('update'))
			show_404('welcome',TRUE);

		$product_id = xss_clean($product_id);
		$product = $this->product_model->where('item_type',1)->get_product($product_id)->row();
		$product_meta = $this->product_model->where('item_id',$product_id)->get_product_metadata()->row();
		$product_transactions = $this->transaction_model->product_transactions_list($product_id);
		
		
		$this->data['new_category']=$this->template->partial('ajax_container');
		$this->form_validation->set_rules('name',lang('product_name_label'),'trim|required');
		$this->form_validation->set_rules('category_id',lang('product_category_label'),'trim|required');
		$this->form_validation->set_rules('description',lang('product_description_label'),'trim');
		$this->form_validation->set_rules('qty',lang('product_quantity_label'),'trim');
		$this->form_validation->set_rules('selling_price',lang('product_selling_label'),'trim|xss_clean');
		$this->form_validation->set_rules('cost_price',lang('product_cost_label'),'trim|xss_clean');

		if (!$this->form_validation->run()) {

			//pass the product data to session so as to retain it after redirect
			if (validation_errors() || $this->ion_auth->errors() || $this->session->flashdata('message')) {
				$this->session->set_flashdata($this->input->post());
				$this->session->set_flashdata('error',(validation_errors() ? validation_errors() : ($this->product_model->errors() ? $this->product_model->errors() : $this->session->flashdata('message'))));
				redirect(ADMIN."products/edit",'refresh');
			}
			//			print_r($this->session->flashdata());exit;
			if ($product==NULL || $product_meta == NULL) {
				$this->session->set_flashdata('error',sprintf(lang('record_not_exist'),'product'));
				redirect('products');
			}
			
			//prepare the form
			$this->data['product_name']=[
				'name' => 'name',
				'type' => 'text',
				'autocomplete'=> 'off',
				'placeholder' => lang('product_name_label'),
				'required'=>'required',
				'class'   => 'form-control',
				'value'=> $this->form_validation->set_value($this->session->flashdata('name'),$product->name),
			];
			$categories = $this->product_model->select('id,name')->where(['item_type'=>2])->order_by('name','DESC')->get_products()->result();

			foreach ($categories as $category) {
				$option[$category->id] = $category->name;
			}
//			print_r($categories);exit;
			$option = [''=>lang('product_choose_category')]+$option;
			$this->data['product_category']=[
				'name' => 'category_id',
				'required'=>'required',
				'class'   => 'form-control',
				'id'   => 'productcategory',
				'data-container'=>"body",
				'options' => $option,
				'selected'=> ($this->session->flashdata('category_id')?$this->session->flashdata('category_id'):$product->category_id),
			];

			$this->data['product_selling_price']=[
				'name' => 'selling_price',
				'type' => 'text',
				'autocomplete'=> 'off',
				'placeholder' => lang('product_selling_label'),
				'class'   => 'form-control digitsonly',
				'value'=> $this->form_validation->set_value($this->session->flashdata('selling_price'),$product_meta->selling_price)
			];

			$this->data['product_cost_price']=[
				'name' => 'cost_price',
				'type' => 'text',
				'autocomplete'=> 'off',
				'placeholder' => lang('product_cost_label'),
				'class'   => 'form-control digitsonly',
				'value'=> $this->form_validation->set_value($this->session->flashdata('cost_price'),$product_meta->cost_price)
			];

			$this->data['product_quantity']=[
				'name' => 'qty',
				'readonly'=> 'readonly',
				'autocomplete'=> 'off',
				'disabled'=> 'disabled',
				'type' => 'text',
				'placeholder' => lang('product_quantity_label'),
				'class'   => 'form-control digitsonly',
				'value'=> $this->form_validation->set_value($this->session->flashdata('qty'),$product_meta->qty)
			];

			$this->data['product_description']=[
				'name' => 'description',
				'type' => 'textarea',
				'autocomplete'=> 'off',
				'placeholder' => lang('product_description_label'),
				'rows' => '3',
				'class'   => 'form-control textarea',
				'value'=> $this->form_validation->set_value($this->session->flashdata('description'),$product->description)
			];
			
			if (has_action('create')) {
				$new_category = html_tag(
				'a',
				[
					'href'=>site_url(ADMIN.'products/new_category'),
					'class'=>'btn btn-danger btn-sm',
				],
				fa_icon('plus',TRUE).' '.html_tag('span',['class'=>'hidden-xs hidden-sm'],lang('btn_new_category'))
				);

			}

			$this->data['product_transactions'] = $product_transactions;
			$this->data['name'] = $product->name;
			$this->data['count_result'] = $product_transactions!==NULL?count($product_transactions): 0;
			$this->data['page_title'] = lang('product_edit_title');
			$this->template->build('products/edit_product',$this->data);
		} else {

			$data = $this->input->post(array(
			'name',
			'category_id',
			'selling_price',
			'description',
			'cost_price',
			), true);

			$data['selling_price'] = ((strlen($data['selling_price']) < 1)?0:my_number_format($data['selling_price'],false));
			$data['cost_price'] = ((strlen($data['cost_price']) < 1)?0:my_number_format($data['cost_price'],false));
			$data['category_id'] = (($data['category_id']==NULL)?2:$data['category_id']);

			if (($category_update=$this->product_model->update($product->id,$data,'1')) == TRUE) {
				$this->session->set_flashdata('success',$this->product_model->messages());
				log_activity($this->session->userdata('user_id'),'updated a category ##'.$product->id);
				if ($this->input->post('submit', TRUE) == "saveandclose") {
					redirect(ADMIN.'products', 'refresh');
					exit;
				} else {
					redirect(current_url(), 'refresh');
					exit;
				}

			} else {
				$this->session->set_flashdata('error',($this->product_model->errors()=='')?lang('product_category_update_error'):$this->product_model->errors());
				redirect(ADMIN.'products/new', 'refresh');
				exit;
			}
		}
		
	}
	
	function delete($item = NULL){
		$item = my_number_format(cleanString($item,true,''));
		if ($this->product_model->delete($item) == TRUE) {
			$this->session->set_flashdata('success',($this->product_model->messages()));
			redirect(previous_url('products',TRUE));
		}
		$this->session->set_flashdata('error',($this->product_model->errors()));
		redirect(previous_url('products',TRUE));
	}
	
	function categories(){
		$categories = [];
		parse_str($_SERVER['QUERY_STRING'], $get);

		// Custom $_GET appended to pagination links and WHERE clause.
		$_get  = null;

		$where =['item_type'=>2,'status => 1'];
		
//		count the total rowls for pagination
		$config['total_rows'] =  $this->product_model
		->where($where)
		->get_all(1)
		->num_rows();
		// Build the query appended to pagination links.
		(empty($_get)) OR $_get = '?'.http_build_query($_get);

		$this->load->library('pagination');
		$config['base_url'] = ('categories'.$_get);
		$config['per_page'] = config_item('results_per_page');

		$offset = (isset($get['page'])) ? config_item('results_per_page') * ($get['page'] - 1) : 0;

		$categories = $this->product_model->where($where)->limit(config_item('results_per_page'))->offset($offset)->order_by('created_date','DESC')->get_all(1)->result();
		//lets count all active records
		$count = $this->product_model->select("1",false)->where($where)->get_all(1)->num_rows();

		$this->pagination->initialize($config);

		$this->data['pagination'] = $this->pagination->create_links();
		$this->data['count_records'] = $count;


		if (!empty($categories) && is_array($categories)) {
			foreach ($categories as $category) {
				$category->category_name = html_tag(
				'a',
				['class'=>"text-left m-b-0",'href'=>admin_url('product/edit/'.$category->id)],
				$category->name
				);
				$category->products = $this->product_model->select('1',false)->where('category_id',$category->id)->get_all()->num_rows();

				$category->date = html_tag(
				'span',
				'',
				my_full_time_span($category->created_date)
				);

				$update=NULL;
				$activate=NULL;
				if (has_action('update')) {
					$update = admin_anchor(
					'products/edit_category/'.$category->id,
					fa_icon('edit' , true).' '."<span class='hidden-xs hidden-sm'>Edit</span>",
					['class'=>'btn btn-sm btn-info']
					);
					if ($category->status == '0') {
						$activate = admin_anchor(
						'products/activate/'.$category->id,
						fa_icon('check' , true).' '."<span class='hidden-xs hidden-sm'>Activate</span>",
						['class'=>'btn btn-sm btn-success']
						);
					}
				}
				$delete=NULL;
				if (has_action('delete') && $category->status == 1) {
					$delete = admin_anchor(
					'products/delete/'.$category->id,
					fa_icon('trash-o', TRUE).' '."<span class='hidden-xs hidden-sm'>Delete</span>",
					[
						'class'=>'btn btn-sm btn-danger',
						'onclick'=>"return confirm('".lang('confirm_delete')."')"
					]
					);
				}
				$category->actions = $update. ' '.$activate. ' '.$delete;
			}
		}


		$product = html_tag(
		'a',
		[
			'href'=>site_url(ADMIN.'products'),
			'class'=>'btn btn-default btn-sm',
		],
		fa_icon('arrow-left',TRUE).' '.html_tag('span',['class'=>'hidden-xs hidden-sm'],lang('btn_back_to_products'))
		);
		$new_category = NULL;

		if (has_action('create')) {
			$new_category = html_tag(
			'a',
			[
				'href'=>site_url(ADMIN.'products/new_category'),
				'class'=>'btn btn-danger btn-sm',
			],
			fa_icon('plus',TRUE). ' '.html_tag('span',['class'=>'hidden-xs hidden-sm'],lang('btn_new_category'))
			);

		}


		$this->data['top_actions'] = $product. ' ' . $new_category; 
		$this->data['new_category'] = $this->template->partial('new_category_widget');
		$this->data['categories'] = $categories; 
		$this->data['page_title'] = lang('product_category_title');
		$this->template->build('products/categories',$this->data);
	}
	
	function activate($item = NULL){
		$item = my_number_format(cleanString($item,true,''));
		if ($this->product_model->activate($item) == TRUE) {
			$this->session->set_flashdata('success',($this->product_model->messages()));
			redirect(previous_url('products',TRUE));
		}
		$this->session->set_flashdata('error',($this->product_model->errors()));
		redirect(previous_url('products',true));
	}
	function new_category()	{
		if (!has_action('create'))
			show_404();

		$this->form_validation->set_rules('name',lang('product_name_label'),'trim|required');
		$this->form_validation->set_rules('description',lang('product_description_label'),'trim');

		if (!$this->form_validation->run()) {

			//pass the product data to session so as to retain it after redirect
			if (validation_errors() || $this->ion_auth->errors() || $this->session->flashdata('message')) {
				$this->session->set_flashdata($this->input->post());
				$this->session->set_flashdata('error',(validation_errors() ? validation_errors() : ($this->product_model->errors() ? $this->product_model->errors() : $this->session->flashdata('message'))));
				redirect(ADMIN."products/new_category",'refresh');
			}
			
			$category=NULL;
			$category = html_tag(
			'a',
			[
				'href'=>site_url(ADMIN.'products/categories'),
				'class'=>'btn btn-default btn-sm',
			],
			fa_icon('arrow-left',TRUE).' '.html_tag('span',['class'=>'hidden-xs hidden-sm'],lang('btn_back_to_categories'))
			);
			
			$this->data['top_actions'] = $category;
			$this->data['page_title'] = lang('product_category_title');
			$this->data['category'] = $this->template->partial('new_category_widget');
			$this->template->build('products/new_category',$this->data);
		} else {

			$data = $this->input->post(array(
			'name',
			'description',
			), true);

			if (($category_id=$this->product_model->save($data,'2')) == TRUE) {
				$this->session->set_flashdata('success',lang('product_category_add_success'));
				log_activity($this->session->userdata('user_id'),'added a new product ##'.$category_id);
				if ($this->input->post('submit', TRUE) == "saveandclose") {
					redirect(ADMIN.'products/categories', 'refresh');
					exit;
				} else {
					redirect(ADMIN.'products/new_category', 'refresh');
					exit;
				}
			} else {
				$this->session->set_flashdata('error',($this->product_model->errors()=='')?lang('product_category_add_error'):$this->product_model->errors());
				redirect(ADMIN.'products/new_category', 'refresh');
				exit;
			}
		}
	}
	
	function edit_category($category_id = NULL)
	{
	
		if (!has_action('update'))
			show_404('welcome',TRUE);

		$category_id = xss_clean($category_id);
		$category = $this->product_model->where('item_type',2)->get_product($category_id)->row();
		
		$this->form_validation->set_rules('name',lang('product_name_label'),'trim|required');
		$this->form_validation->set_rules('description',lang('product_description_label'),'trim');
		
		if (!$this->form_validation->run()) {

			//pass the product data to session so as to retain it after redirect
			if (validation_errors() || $this->ion_auth->errors() || $this->session->flashdata('message')) {
				$this->session->set_flashdata($this->input->post());
				$this->session->set_flashdata('error',(validation_errors() ? validation_errors() : ($this->product_model->errors() ? $this->product_model->errors() : $this->session->flashdata('message'))));
				redirect(ADMIN."products/edit_category",'refresh');
			}
			
			if ($category==NULL) {
				$this->session->set_flashdata('error',sprintf(lang('record_not_exist'),'category'));
				redirect('products/categories');
			}
			
			//prepare the form
			$this->data['category_name']=[
				'name' => 'name',
				'type' => 'text',
				'placeholder' => lang('product_name_label'),
				'required'=>'required',
				'class'   => 'form-control',
				'value'=> $this->form_validation->set_value($this->session->flashdata('name'),$category->name),
			];


			$this->data['category_description']=[
				'name' => 'description',
				'type' => 'textarea',
				'placeholder' => lang('product_description_label'),
				'rows' => '3',
				'class'   => 'form-control textarea',
				'value'=> $this->form_validation->set_value($this->session->flashdata('description'),$category->description),
			];

			$this->data['activate'] = NULL;
			if ($category->status==0) {
				$this->data['activate']=html_tag(
				'a',
				['class'=>'btn btn-xs btn-danger','href'=>site_url('products/activate/'.$category->id)],
				lang('btn_activate')
				);
			}

			$btn_product =null;
			$btn_product = html_tag(
			'a',
			[
				'href'=>site_url(ADMIN.'products/categories'),
				'class'=>'btn btn-default btn-sm',
			],
			fa_icon('arrow-left',TRUE).' '.html_tag('span',['class'=>'hidden-xs hidden-sm'],lang('btn_back_to_categories'))
			);
			$new_category = NULL;

			$this->form_validation->set_rules('name','Post title','trim|required');
			$this->form_validation->set_rules('content','Content','trim');

			if (has_action('create')) {
				$new_category = html_tag(
				'a',
				[
					'href'=>site_url(ADMIN.'products/new_category'),
					'class'=>'btn btn-danger btn-sm',
				],
				fa_icon('plus',TRUE).' '.html_tag('span',['class'=>'hidden-xs hidden-sm'],lang('btn_new_category'))
				);

			}


			$this->data['top_actions'] = $btn_product. ' ' . $new_category;
			$this->data['page_title'] = lang('product_edit_category_title');
			//		$this->data['category'] = $this->template->partial('edit_category_widget');
			$this->template->build('products/edit_category',$this->data);
		}else{

			$data = $this->input->post(array(
			'name',
			'description',
			), true);

			if (($category_update=$this->product_model->update($category->id,$data,'2')) == TRUE) {
				$this->session->set_flashdata('success',$this->product_model->messages());
				log_activity($this->session->userdata('user_id'),'updated a category ##'.$category->id);
				if ($this->input->post('submit', TRUE) == "saveandclose") {
					redirect(ADMIN.'products/categories', 'refresh');
					exit;
				} else {
					redirect(current_url(), 'refresh');
					exit;
				}

			} else {
				$this->session->set_flashdata('error',($this->product_model->errors()=='')?lang('product_category_update_error'):$this->product_model->errors());
				redirect(ADMIN.'products/new', 'refresh');
				exit;
			}
		}
	}
}

//end of line.
