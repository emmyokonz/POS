<?php
defined('BASEPATH') or exit('No direct access to script is allowed');

class Dashboard extends MY_AdminController
{

	public function __construct()
	{

		parent::__construct();

		//tell the default layout that this page is Dashboard.
		$this->data['dashboard'] = TRUE;
		$this->data['token'] = md5(time()); 
		$this->load->model('sales_model');
		$this->load->model(['product_model','accounts_model','financial_model']);
	}

	public function index()
	{
		$daily_start = strtotime(('today 00:00:00'));
		$daily_end = strtotime(('today 23:59:59'));
		
		$monthly_start = strtotime(date('Y-m-01 00:00:00'));
		$monthly_end = strtotime(date('Y-m-t 23:59:59'));
//		echo /*$daily_end.'  ' .$daily_start. '--'.*/$monthly_end. '  '.$monthly_start;exit ;
		$this->data['daily_sales'] = $this->sales_model->get_sales_range($daily_start,$daily_end);
		$this->data['monthly_sales'] = $this->sales_model->get_sales_range($monthly_start,$monthly_end);
		$this->data['recent_sales'] = $this->sales_model->recent_sales();
		$this->data['stock_alert'] = $this->product_model->stock_list();
		$this->data['cash_balance'] = $this->accounts_model->get_cash_balance();
		$this->data['DCS_value'] = $this->financial_model->get_DCS_value();
//		print_r($this->data['DCS_value']);exit;
		$this->template->build('dashboard/index',$this->data);
	}
	
	public function settings($action = FALSE)
	{
		if(!is_admin())
		{
			$this->session->set_flashdata('error',lang('permission_no_access'));
			redirect(ADMIN.'dashboard','reload');
		}
		
		
		
		if($action){
			
			if(!has_action('update'))
			{
				$this->session->set_flashdata('error',lang('action_no_access'));
			}
			elseif($action == 'activate')
			{
				$this->input->get('action',true);
				$action = xss_clean($_GET['action']);
				if($this->app->activate_widget($_GET['action'])){
					$this->session->set_flashdata('info',lang('widget_activated'));
				}
				else
				{
					$this->session->set_flashdata('error',lang('widget_activated_error'));
				}
			}
			elseif($action == 'deactivate')
			{
				$this->input->get('action',true);
				$action = xss_clean($_GET['action']);
				if($this->app->deactivate_widget($_GET['action'])){
					$this->session->set_flashdata('info',lang('widget_deactivated'));
				}
				else
				{
					$this->session->set_flashdata('error',lang('widget_deactivated_error'));
				}
			}
			redirect(ADMIN.'dashboard/settings','reload');
		}
		
		$this->data['widgets'] = $this->app->all_objects('widget');
		
		$this->template->build(ADMIN.'dashboard/settings',$this->data);
	}

	
//	private function body_dashboard_widget():
}

