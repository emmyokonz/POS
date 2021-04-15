<?php

class Permissions extends MY_AdminController {
	function __construct() {
		$this->load->library('permissions');
	}
	
	public function index()
	{
		parse_str($_SERVER['QUERY_STRING'], $get);
		
		// Custom $_GET appended to pagination links and WHERE clause.
		$_get  = null;
		
		// Filtering by module, controller or method?
		foreach (array('controller', 'method') as $filter)
		{
			if (isset($get[$filter]))
			{
				$_get[$filter]  = $get[$filter];
//				$where[$filter] = strval(xss_clean($get[$filter]));
			}
		}
		
		$where =['post_type'=>1];
		
		// Build the query appended to pagination links.
		(empty($_get)) OR $_get = '?'.http_build_query($_get);
		
		$this->load->library('pagination');
		$config['base_url'] = admin_url('permissions'.$_get);
		$config['per_page'] = config_item('results_per_page');
		
		// Filtering by user author?
		/*if (isset($get['author']))
		{
			$_get['author']     = $get['author'];
			$author_id = $this->ion_auth->where('username',strval(xss_clean($get['author'])))->select('id')->users();
			$_where['created_by'] = ($author_id->num_rows()?$author_id->row()->id:NULL);
			$where = $where+$_where;
		}*/

		// Build the query appended to pagination links.
		(empty($_get)) OR $_get = '?'.http_build_query($_get);
		
		$config['total_rows'] =  $this->post_lib->where($where)->get_all(1)->num_rows();
		
		
		$offset = (isset($get['page'])) ? config_item('results_per_page') * ($get['page'] - 1) : 0;

		$posts = $this->post_lib->where($where)->limit(config_item('results_per_page'))->offset($offset)->order_by('created_date','DESC')->get_all(1)->result();

		$this->pagination->initialize($config);
		
		$this->data['pagination'] = $this->pagination->create_links();
		
		if(!empty($posts) && is_array($posts))
		{
			foreach($posts as $post)
			{
				$post->title = html_tag(
					'a',
					['class'=>"text-left m-b-0",'href'=>admin_url('posts/edit/'.$post->id)],
					$post->post_title
				);
				$post->author = html_tag(
					'a',
					['href'=>admin_url('posts?'.'author='.get_display_name($post->created_by))],
					get_display_name($post->created_by)
				);
				if(!$post->published){
					$published = html_tag(
						'a',
						[
						'class'=>'btn btn-sm btn-link p-0 text-danger',
						'href'=>admin_url('posts/publish/'.$post->id),
						],
						fa_icon('unlock').'publish post'
					);
				}else{
					$published = html_tag(
						'a',
						[
						'class'=>'btn btn-sm btn-link text-success p-0',
						'href'=>admin_url('posts/unpublish/'.$post->id),
						'title'=> my_full_time_span($post->publish_date),
						'data-toggle' => "tooltip"
						],
						fa_icon('lock').'unpublish post'
					);
				}
				$post->published = $published;
				
				$post->date = html_tag(
					'span',
					'',
					time_span($post->created_date)
				);
				
				$post->post_views = html_tag(
					'span',
					'',
					($post->post_views)
				);
				
				$update=NULL;
				if(has_action('update'))
				{
					$update = admin_anchor(
						'posts/edit/'.$post->id,
						fa_icon('edit')."Edit",
						['class'=>'btn btn-sm btn-info']
					);
				}
				$delete=NULL;
				if(has_action('delete'))
				{
					$delete = admin_anchor(
						'posts/delete/'.$post->id,
						fa_icon('trash-o')."Delete",
						[
						'class'=>'btn btn-sm btn-danger',
						'onclick'=>"return confirm('".lang('confirm_delete')."')"
						]
					);
				}
				$post->actions = $update. ' '.$delete;
			}
		}

		$this->data['posts'] = $posts;
		$this->template->build('posts/index',$this->data);
	}
}