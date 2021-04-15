<?php
defined('BASEPATH')or exit('No direct script allowed!');
/**
* Developed by Techcoderr Developers
*/
class Report extends MY_AdminController
{
	function __construct()
	{
		parent::__construct();
		add_script('assets/js/custom_datatable');
		$this->data['print_header'] = $this->template->partial('print_header');
	}

	function index()
	{

		$this->data['page_title'] = lang('report_title');
		$this->template->build('report/index',$this->data);
		
	}
	
	function sales()
	{
		$this->data['__back'] = site_url('report');
		//set the default date to the current month range from biginning to end.
		$date_start = strtotime(date('Y-m-01 00:00:00'));
		$date_end = strtotime(date('Y-m-t 23:59:59'));

		$start = (date('j/n/Y',$date_start));
		$end = (date('t/n/Y',$date_start));

		$date = $date_start.'_'.$date_end;
		
		$this->form_validation->set_rules('start','Start','trim');
		$this->form_validation->set_rules('end','End','trim');
		
		if ($this->form_validation->run()) {
			$start = $this->input->post('start');
			$end = $this->input->post('end');
			$start =str_replace('/', '-', $start);
			$end =str_replace('/', '-', $end);
			if ($start !== '') {
				$date_start = strtotime(date("d-m-Y" ,strtotime($start)).' 00:00:00');
			
				if ($end == '') {
					$end = $this->input->post('start');
					$date_end = strtotime(date("d-m-Y" ,strtotime($end)).'23:59:59');
				} else {
					$date_end = strtotime(date("d-m-Y" ,strtotime($end)).'23:59:59');
				}
			} else {
				
				if ($end == '') {
//					exit;
					//set the default date to the current month range from biginning to end.
					$date_start = strtotime(date('Y-m-01 00:00:00'));
					$date_end = strtotime(date('Y-m-t 23:59:59'));

					$start = (date('j/n/Y',$date_start));
					$end = (date('t/n/Y',$date_start));

				} else {
					$start = $this->input->post('end');
					$date_start = strtotime(date("d-m-Y" ,strtotime($start)).'00:00:00');
					$date_end = strtotime(date("d-m-Y" ,strtotime($end)).'23:59:59');
				}
			}
			
			$date = $date_start.'_'.$date_end;
		}
		
		$this->session->set_userdata('range',$date);
		
		$this->load->model(['sales_model','people_model']);
		$result = $this->sales_model->generate_sales_report();
		$sales_count = 0;
		$sales_amount = 0;
		$profit = 0;
		$items_sold = 0;
		if ($result !==NULL) {
			foreach($result as $res){
				if ($res->transaction_type == 1) {
					$sales_count = 1+$sales_count;
					$sales_amount = $sales_amount + $res->amount;
					$profit = $profit + $res->profit;
					$items_sold = $items_sold + $res->total_items;
				}
				
				$res->sales_no=html_tag('a',['href'=>site_url('preview/transaction/'.$res->id)],$res->sales_no);
				$res->people_name = $this->people_model->get_a_person($res->people_id)->row()->name;
				$res->staff = get_display_name($res->staff);
				$res->date = my_full_time_span($res->created_date);
				$res->amount = my_number_format($res->amount,1,1);
				$res->profit = my_number_format($res->profit,1,1);
			}
		}
		
		$report = [
		'sales_count'=>my_number_format($sales_count),
		'sales_amount'=>my_number_format($sales_amount,1,1),
		'profit'=>my_number_format($profit,1,1),
		'items_sold'=>my_number_format($items_sold),
		'report'=>$result
		];
		$this->data['start'] = $date_start;
		$this->data['end'] = $date_end;
		$trans_date = $start. ' to '. $end;
		$this->data['sales_report'] = $report;
		$this->data['page_title'] = sprintf(lang('report_sales_title'),$trans_date);
		$this->template->build('report/sales',$this->data);
	}
	
	function physical_stock()
	{
		$this->load->model('product_model','product');
		$stock = $this->product->generate_physical_stock();
		
		if($stock !== NULL){
			foreach ($stock as $res) {
				$res->name = humanize($res->name,'-');
			}
		}
		$this->data['stocks'] = $stock;
		$this->data['page_title'] = lang('report_physical_stock_title');
		$this->template->build('report/physical_stock',$this->data);
	}
	
	function customers($customer_id =NULL)
	{
		
		$this->data['__back'] = site_url('report');
		if ($customer_id !==NULL) {
		
			$this->load->model('transaction_model');
			

			$date = $this->session->userdata('range');
			$date = explode('_',$date);
			$start_start = $date[0];
			$start_end = $date[1];

			
			$transaction = $this->transaction_model->report_per_person($customer_id,$start_start,$start_end);
			if ($transaction !== NULL) {
				$amount = 0;
				foreach ($transaction as $tran) {
					$amount = $amount+$tran->amount;
					$tran->balance = my_number_format($amount,1,1);
					$tran->amount = my_number_format($tran->amount,1,1);
					$tran->date = my_full_time_span($tran->created_date);
					$view=NULL;
					if (has_action('update')) {
						$view = admin_anchor(
						'preview/'.$tran->method.'/'.$tran->id,
						fa_icon('eye' , true).' '."<span class='hidden-xs hidden-sm'>View</span>",
						['class'=>'btn btn-sm btn-info']
						);
					}
					$tran->actions = $view;
				}
			}
			$trans_date = (date('j/n/Y',$start_start)). ' to '. (date('j/n/Y',$start_end));

			$name = $this->people_model->get_a_person($customer_id)->row()->name;
			$this->data['details'] = $transaction;
			$this->data['page_title'] = sprintf(lang('report_supplier_detail_title'),humanize($name,'-'),$trans_date);
			$this->template->build('report/supplier_report',$this->data);

		}else{
			//set the default date to the current month range from biginning to end.
			$date_start = strtotime(date('Y-m-01 00:00:00'));
			$date_end = strtotime(date('Y-m-t 23:59:59'));

			$start = (date('j/n/Y',$date_start));
			$end = (date('t/n/Y',$date_start));

			$date = $date_start.'_'.$date_end;

			$this->form_validation->set_rules('start','Start','trim');
			$this->form_validation->set_rules('end','End','trim');

			if ($this->form_validation->run()) {
				$start = $this->input->post('start');
				$end = $this->input->post('end');
				if ($start !== '') {
					$start =str_replace('/', '-', $start);
					$end =str_replace('/', '-', $end);
					$date_start = strtotime(date("d-m-Y" ,strtotime($start)).' 00:00:00');


					if ($end == '') {
						$end = $this->input->post('start');
						$date_end = strtotime(date("d-m-Y" ,strtotime($end)).'23:59:59');
					} else {
						$date_end = strtotime(date("d-m-Y" ,strtotime($end)).'23:59:59');
					}
				} else {

					if ($end == '') {
						//					exit;
						//set the default date to the current month range from biginning to end.
						$date_start = strtotime(date('Y-m-01 00:00:00'));
						$date_end = strtotime(date('Y-m-t 23:59:59'));

						$start = (date('j/n/Y',$date_start));
						$end = (date('t/n/Y',$date_start));

					} else {
						$start = $this->input->post('end');
						$date_start = strtotime(date("d-m-Y" ,strtotime($start)).'00');
						$date_end = strtotime(date("d-m-Y" ,strtotime($end)).'23:59:59');
					}
				}

				$date = $date_start.'_'.$date_end;

			}

			$trans_date = $start. ' to '. $end;

			$this->load->model('people_model');
			$this->session->set_userdata('range',$date);
			
			$customers = $this->people_model->generate_people_report(1,1);
			$amount = 0;
			$profit = 0;
			if ($customers !== NULL) {
				$amount = array_sum(array_column($customers,'amount'));
				$profit = array_sum(array_column($customers,'profit'));
				foreach ($customers as $customer) {
					$customer->name = html_tag('a',['href'=>'customers/'.$customer->id],humanize($customer->name,'-'));
					$customer->amount_p = number_format(($customer->amount/$amount)*100,1);
					$customer->profit_p = number_format(($customer->profit/$profit)*100,1);
					$customer->amount = my_number_format($customer->amount,1,1);
					$customer->profit = my_number_format($customer->profit,1,1);
				}
			}
			
			$this->data['customers'] = $customers;
			$this->data['page_title'] = sprintf(lang('report_customer_title'),$trans_date);
			$this->template->build('report/people',$this->data);
		}
	}
	
	function suppliers($supplier_id=NULL)
	{
		$this->data['__back'] = site_url('report');
		if ($supplier_id !==NULL) {
			$this->load->model('transaction_model');

			$date = $this->session->userdata('range');
			$date = explode('_',$date);
			$start_start = $date[0];
			$start_end = $date[1];

			$transaction = $this->transaction_model->report_per_person($supplier_id,$start_start,$start_end);
			if ($transaction !== NULL) {
				$amount = 0;
				foreach ($transaction as $tran) {
					$amount = $amount+$tran->amount;
					$tran->balance = my_number_format($amount,1,1);
					$tran->amount = my_number_format($tran->amount,1,1);
					$tran->date = my_full_time_span($tran->created_date);
					$view=NULL;
					if (has_action('update')) {
						$view = admin_anchor(
						'preview/'.$tran->method.'/'.$tran->id,
						fa_icon('eye' , true).' '."<span class='hidden-xs hidden-sm'>View</span>",
						['class'=>'btn btn-sm btn-info']
						);
					}
					$tran->actions = $view;
				}
			}
			$trans_date = (date('j/n/Y',$start_start)). ' to '. (date('j/n/Y',$start_end));
			
			$name = $this->people_model->get_a_person($supplier_id)->row()->name;
			$this->data['details'] = $transaction;
			$this->data['page_title'] = sprintf(lang('report_supplier_detail_title'),humanize($name,'-'),$trans_date);
			$this->template->build('report/supplier_report',$this->data);
			
		}else{
	
			$date_start = strtotime(date('Y-m-01 00:00:00'));
			$date_end = strtotime(date('Y-m-t 23:59:59'));

			$start = (date('j/n/Y',$date_start));
			$end = (date('t/n/Y',$date_start));

			$date = $date_start.'_'.$date_end;

			$this->form_validation->set_rules('start','Start','trim');
			$this->form_validation->set_rules('end','End','trim');

			if ($this->form_validation->run()) {
				$start = $this->input->post('start');
				$end = $this->input->post('end');
				if ($start !== '') {
					$start =str_replace('/', '-', $start);
					$end =str_replace('/', '-', $end);
					$date_start = strtotime(date("d-m-Y" ,strtotime($start)).' 00:00:00');


					if ($end == '') {
						$end = $this->input->post('start');
						$date_end = strtotime(date("d-m-Y" ,strtotime($end)).'23:59:59');
					} else {
						$date_end = strtotime(date("d-m-Y" ,strtotime($end)).'23:59:59');
					}
				} else {

					if ($end == '') {
						//					exit;
						//set the default date to the current month range from biginning to end.
						$date_start = strtotime(date('Y-m-01 00:00:00'));
			$date_end = strtotime(date('Y-m-t 23:59:59'));

						$start = (date('j/n/Y',$date_start));
						$end = (date('t/n/Y',$date_start));

					} else {
						$start = $this->input->post('end');
						$date_start = strtotime(date("d-m-Y" ,strtotime($start)).'00:00:00');
						$date_end = strtotime(date("d-m-Y" ,strtotime($end)).'23:59:59');
					}
				}

				$date = $date_start.'_'.$date_end;

			}
			$this->load->model('people_model');
			$this->session->set_userdata('range',$date);

			$customers = $this->people_model->generate_people_report(2,4);
			$amount = 0;
			$profit = 0;
			if ($customers !== NULL) {
				$amount = array_sum(array_column($customers,'amount'));
				$profit = array_sum(array_column($customers,'profit'));
				foreach ($customers as $customer) {
					$customer->name = html_tag('a',['href'=>'suppliers/'.$customer->id],humanize($customer->name,'-'));
					$customer->amount_p = number_format(($customer->amount/$amount)*100,1);
					$customer->amount = my_number_format($customer->amount,1,1);
				}
			}

			$this->data['customers'] = $customers;
			$this->data['start'] = $date_start;
			$this->data['end'] = $date_end;
			$trans_date = $start. ' to '. $end;
			$this->data['page_title'] = sprintf(lang('report_supplier_title'),$trans_date);
			$this->template->build('report/supplier',$this->data);
		}
	}
	
	function inventory($product_id=NULL)
	{
		$this->data['__back'] = site_url('report');
		if ($product_id !==NULL) {
			$this->load->model('product_model');

			$date = $this->session->userdata('range');
			$date = explode('_',$date);
			$start_start = $date[0];
			$start_end = $date[1];
			
			$product_name = $this->product_model->get_info($product_id)->name;
			$transaction = $this->product_model->report_per_product($product_id,$start_start,$start_end);
			if ($transaction !== NULL) {
				$amount = 0;
				$details_arr = [];
				$qty_sold = array_sum(array_column($transaction['product_details'],'quantity_out'));
				$profit = array_sum(array_column($transaction['product_details'],'profit'));
				$amount = array_sum(array_column($transaction['product_details'],'amount'));
				$qty_at_hand = $transaction['qty_at_hand'];
				$_opening_bal = ($transaction['qty_at_hand'] - $transaction['sold_balance']);
				foreach ($transaction['product_details'] as $tran) {
					$details = new stdClass();
					
					$details->customer = $tran->customer;
					$details->sales_number = anchor_popup(site_url('preview/transaction/'.$tran->transaction_id),$tran->transaction_no);
					$details->date = $tran->date;
					$details->type = $tran->type;
					$details->price = my_number_format($tran->price,1,1);
					$details->quantity_in = $tran->quantity_in;
					$details->quantity_out = $tran->quantity_out;
					$_opening_bal = $_opening_bal + $tran->quantity ;
					$details->balance =  $_opening_bal;
					$details_arr[] = $details;
				}
				$first = new stdClass();
				$first->customer = 'Balance as at '.invoice_date($start_start-(60*60*24));
				$first->sales_number = '';
				$first->date = '';
				$first->type = '';
				$first->price = '';
				$first->quantity_in = '';
				$first->quantity_out = '';
				$first->balance = $transaction['qty_at_hand'] - $transaction['sold_balance'] ;
				
				$trans_date = (date('j/n/Y',$start_start)). ' to '. (date('j/n/Y',$start_end));
				array_unshift($details_arr,$first);
				
				$result = [
					'details'=>		$details_arr,
					'amount'=>		my_number_format($amount,1,1),
					'profit'=>		my_number_format($profit,1,1),
					'qty_at_hand'=>$qty_at_hand,
					'qty_sold'=>	$qty_sold,
				];
			}
			
			$this->data['details'] = $result;
			$this->data['page_title'] = sprintf(lang('report_supplier_detail_title'),humanize($product_name,'-'),$trans_date);
			$this->template->build('report/item_report',$this->data);

		} else {

			$date_start = strtotime(date('Y-m-01 00:00:00'));
			$date_end = strtotime(date('Y-m-t 23:59:59'));

			$start = (date('j/n/Y',$date_start));
			$end = (date('t/n/Y',$date_start));

			$date = $date_start.'_'.$date_end;

			$this->form_validation->set_rules('start','Start','trim');
			$this->form_validation->set_rules('end','End','trim');

			if ($this->form_validation->run()) {
				$start = $this->input->post('start');
				$end = $this->input->post('end');
				if ($start !== '') {
					$start =str_replace('/', '-', $start);
					$end =str_replace('/', '-', $end);
					$date_start = strtotime(date("d-m-Y" ,strtotime($start)).' 00:00:00');


					if ($end == '') {
						$end = $this->input->post('start');
						$date_end = strtotime(date("d-m-Y" ,strtotime($end)).'23:59:59');
					} else {
						$date_end = strtotime(date("d-m-Y" ,strtotime($end)).'23:59:59');
					}
				} else {

					if ($end == '') {
						//					exit;
						//set the default date to the current month range from biginning to end.
						$date_start = strtotime(date('Y-m-01 00:00:00'));
			$date_end = strtotime(date('Y-m-t 23:59:59'));

						$start = (date('j/n/Y',$date_start));
						$end = (date('t/n/Y',$date_start));

					} else {
						$start = $this->input->post('end');
						$date_start = strtotime(date("d-m-Y" ,strtotime($start)).'00:00:00');
						$date_end = strtotime(date("d-m-Y" ,strtotime($end)).'23:59:59');
					}
				}

				$date = $date_start.'_'.$date_end;

			}
			$this->load->model('product_model');
			$this->session->set_userdata('range',$date);

			$inventory = $this->product_model->generate_inventory_report();
			$amount = 0;
			$profit = 0;
			if ($inventory !== NULL) {
				{
					$amount = array_sum(array_column($inventory,'amount'));
					$profit = array_sum(array_column($inventory,'profit'));
					foreach ($inventory as $customer) {
						$customer->name = html_tag('a',['href'=>'inventory/'.$customer->id],humanize($customer->name,'-'));
						$customer->amount_p = number_format(($customer->amount/$amount)*100,1);
						$customer->profit_p = number_format(($customer->profit/$profit)*100,1);
						$customer->a_price =my_number_format(number_format($customer->a_price,1),1,1);
						$customer->amount = my_number_format($customer->amount,1,1);
						$customer->profit = my_number_format($customer->profit,1,1);
						$customer->price = my_number_format($customer->price,1,1);
					}
				}
			}
		

			$this->data['start'] = $date_start;
			$this->data['end'] = $date_end;
			$trans_date = $start. ' to '. $end;
			$this->data['inventory'] = $inventory;
			$this->data['page_title'] = sprintf(lang('report_inventory_title'),$trans_date);
			$this->template->build('report/inventory',$this->data);
		}
	}
}

//end of line.
