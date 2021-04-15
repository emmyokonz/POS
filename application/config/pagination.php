<?php 

//$config['per_page'] = config_item('results_per_page');
$config['reuse_query_string'] = true;
$config['page_query_string'] = true;
$config['query_string_segment'] = 'page';
$config['attributes'] = ['class'=>'page-link'];

$config['full_tag_open'] = '<nav aria-label="Page navigation"><ul class="pagination justify-content-end mb-0">';
$config['full_tag_close'] = '</ul></nav>';

$config['num_links'] = 10;

$config['prev_link'] = '<i class="fa fa-angle-left"></i> <span class="sr-only">Previous</span>';
$config['prev_tag_open'] = '<li class="page-item">';
$config['prev_tag_close'] = '</li>';

$config['next_link'] = '<i class="fa fa-angle-right"></i> <span class="sr-only">Next</span>';
$config['next_tag_open'] = '<li class="page-item">';
$config['next_tag_close'] = '</li>';

$config['cur_tag_open'] = '<li class="page-item active"><a class="page-link" href="#">';
$config['cur_tag_close'] = '</a></li>';

$config['num_tag_open'] = '<li class="page-item ">';
$config['num_tag_close'] = '</li>';

$config['first_link'] = true;
$config['last_link'] = TRUE;
$config['anchor_class'] = 'page-link';
$config['use_page_numbers'] = TRUE;


