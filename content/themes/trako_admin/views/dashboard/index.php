<?php
defined('BASEPATH') or exit('No direct access to script is allowed');
?>
<div class="row">
	<div class="col-md-6 col-lg-3">
		<div class="white-box text-info">
			<div class="row">
				<div class="col-sm-12">
					<h2 class="m-b-0 font-medium"><?=$daily_sales->invoice_count ?></h2>
					<h5 class="text-muted m-t-0">Today Invoice</h5>
				</div>
			</div>
		</div>
	</div>
	<div class="col-md-6 col-lg-3">
		<div class="white-box text-danger">
			<div class="row">
				<div class="col-sm-12">
					<h2 class="m-b-0 font-medium"><?=$monthly_sales->invoice_count ?></h2>
					<h5 class="text-muted m-t-0">This Month Invoice</h5>
				</div>
			</div>
		</div>
	</div>
	<div class="col-md-6 col-lg-3">
		<div class="white-box text-primary">
			<div class="row">
				<div class="col-sm-12">
					<h2 class="m-b-0 font-medium"><?=my_number_format($daily_sales->sales_amount,1,1) ?></h2>
					<h5 class="text-muted m-t-0">Today Sales</h5>
				</div>
			</div>
		</div>
	</div>
	<div class="col-md-6 col-lg-3">
		<div class="white-box text-success">
			<div class="row">
				<div class="col-sm-12">
					<h2 class="m-b-0 font-medium"><?=my_number_format($monthly_sales->sales_amount,1,1)?></h2>
					<h5 class="text-muted m-t-0">This Month Sales</h5>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-md-6 col-lg-8 col-sm-12">
		<div class="white-box">
			<div class=" pull-right">
				<a class="btn-sm btn-default" href="<?=site_url('sales')?>"><span class="fa fa-list"></span> <?=lang('btn_sales')?></a>
				<a class="btn-sm btn-danger" href="<?=site_url('sales/new')?>"><span class="fa fa-plus"></span> <?=lang('btn_new_sales')?></a>
			</div>
			<h3 class="box-title">Recent sales</h3>
			
			<div class="table-responsive">
				<table class="table">
					<thead>
						<tr>
							<th>#</th>
							<th>INVOICE NO</th>
							<th>NAME</th>
							<th>DATE</th>
							<th>AMOUNT</th>
						</tr>
					</thead>
					<tbody>
						<?php
						if ($recent_sales['recent_sales_invoice'] !== NULL):$n=0 ?>
							<?php foreach ($recent_sales['recent_sales_invoice'] as $sales): ?>
						<tr>
							<td><?=++$n?></td>
							<td class="txt-oflo"><?=anchor('preview/transaction/'.$sales->id,$sales->sales_no) ?></td>
							<td class="txt-oflo"><?=$sales->name?></td>
							<td class="txt-oflo"><?=$sales->date?></td>
							<td class="txt-oflo"><?=$sales->amount?></td>
							
						</tr>
						<?php endforeach?>
						<?php endif?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
	<div class="col-lg-4 col-sm-12 col-md-6">
        <div class="white-box">
            <h3 class="box-title">Stock Alert</h3>
            <ul class="feeds">
                <?php if($stock_alert !== NULL):?>
                <?php foreach($stock_alert as $item):?>
				<li><?=anchor('products/edit/'.$item->id,$item->name) ?>
					<span class="text-muted"><?=$item->qty ?></span></li>
                <?php endforeach?>
                <?php endif?>
            </ul>
        </div>
    </div>
</div>

<div class="row">
	<div class=" col-sm-12 col-md-6 col-lg-7">
		
		<div class="white-box p-b-0">
			<div class="row">
				<div class="col-xs-8">
					<h2 class="text-muted m-t-0">Montly Target</h2>
					<h5 class="font-medium m-t-0">From June 1 to 30</h5>
				</div>
			</div>
			<div class="row minus-margin">
				<div class="col-sm-12 col-sm-6 p-0 m-0">
					<div class="white-box bg-info m-b-0">
						<div class="row">
							<div class="col-sm-3 text-white">
								<span class="fa fa-money fa-3x"></span>
							</div>
							<div class="col-sm-9 text-white">
								<h2 class="m-b-0 m-t-0 text-white font-medium">19,050.00</h2>
								<h5 class="text-white m-t-0">April Income</h5>
							</div>
							<div class="col-sm-12">
								<div class="progress m-b-0 m-t-2">
									<div class="progress-bar progress-bar-white progress-bar-striped" role="progressbar" style="width: 55%; role="progressbar"></div>
								</div>
								<h5 class="m-b-0 text-white">
									50,000.00
									<span class="pull-right text-white">55%</span></h5>
							</div>
						</div>
					</div>
				</div>
				<div class="col-sm-12 col-sm-6 p-0 m-0">
					<div class="white-box bg-success  m-b-0">
						<div class="row">
							<div class="col-sm-3 text-white">
								<span class="fa fa-flag fa-3x"></span>
							</div>
							<div class="col-sm-9 text-white">
								<h2 class="m-b-0 m-t-0 text-white font-medium">240,650.00</h2>
								<h5 class="text-white m-t-0">April Sales</h5>
							</div>
							<div class="col-sm-12">
								<div class="progress m-b-0">
									<div class="progress-bar progress-bar-white progress-bar-striped" role="progressbar" style="width: 25%; role="progressbar"></div>
								</div>
								<h5 class="m-b-0 text-white">
									50,000.00
									<span class="pull-right text-white">25%</span></h5>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="row minus-margin">
				<div class="col-sm-12 col-sm-6 p-0 m-0">
					<div class="white-box bg-primary  m-b-0">
						<div class="row">
							<div class="col-sm-3 text-white">
								<span class="fa fa-external-link fa-3x"></span>
							</div>
							<div class="col-sm-9 text-white">
								<h2 class="m-b-0 m-t-0 text-white font-medium">9,650.00</h2>
								<h5 class="text-white m-t-0">April Expenses</h5>
							</div>
							<div class="col-sm-12">
								<div class="progress m-b-0">
									<div class="progress-bar progress-bar-white progress-bar-striped" role="progressbar" style="width: 75%; role="progressbar"></div>
								</div>
								<h5 class="m-b-0 text-white">
									50,000.00
									<span class="pull-right text-white">75%</span></h5>
							</div>
						</div>
					</div>
				</div>
				<div class="col-sm-12 col-sm-6 p-0 m-0">
					<div class="white-box bg-danger  m-b-0">
						<div class="row">
							<div class="col-sm-3 text-white">
								<span class="fa fa-money fa-3x"></span>
							</div>
							<div class="col-sm-9 text-inbox">
								<h2 class="m-b-0 m-t-0 text-white font-medium">10,600.00</h2>
								<h5 class="text-white m-t-0">April Net Income</h5>
							</div>
							<div class="col-sm-12">
								<div class="progress m-b-0">
									<div class="progress-bar progress-bar-white progress-bar-striped" role="progressbar" style="width: 15%; role="progressbar"></div>
								</div>
								<h5 class="m-b-0 text-white">
									50,000.00
									<span class="pull-right text-white">15%</span></h5>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-sm-12 col-md-6 col-lg-5">

	<div class="panel wallet-widgets">
		<div class="panel-body">
			<ul class="side-icon-text">
				<li>
					<a href="<?=site_url('accounts')?>">
						<span class="di vm">
							<h2 class="text-muted m-t-0"><?=$cash_balance['total'] ?></h2>
								<h5 class="font-medium m-t-0">Cash Balance as at <?=date('M jS')?></h5>
						</span>
					</a>
				</li>
			</ul>
		</div>
		<div class="table-responsive">
			<table class="table">
				<tbody>
				<?php if($cash_balance['banks']!== null):?>
				<?php foreach($cash_balance['banks'] as $bank):?>
					<tr>
						<td><?=$bank->name?></td>
						<td align="right"><?=$bank->balance?></td>
					</tr>
				<?php endforeach?>
				<?php endif?>
				</tbody>
			</table>
		</div>
	</div>

	</div>
</div>
<div class="row">
	<div class="col-md-6 col-lg-4">
		<div class="white-box">
			<div class="row">
				<div class="col-sm-3">
					<span class="fa fa-file-o fa-4x"></span>
				</div>
				<div class="col-sm-9">
					<h2 class="m-b-0 font-medium"><?=$DCS_value['D'] ?></h2>
					<h5 class="text-muted m-t-0">Debtors Value</h5>
				</div>
			</div>
		</div>
	</div>
	<div class="col-md-6 col-lg-4">
		<div class="white-box">
			<div class="row">
				<div class="col-sm-3">
					<span class="fa fa-file-o fa-4x"></span>
				</div>
				<div class="col-sm-9">
					<h2 class="m-b-0 font-medium"><?=$DCS_value['C'] ?></h2>
					<h5 class="text-muted m-t-0">Creditors Value</h5>
				</div>
			</div>
		</div>
	</div>
	<div class="col-md-6 col-lg-4">
		<div class="white-box">
			<div class="row">
				<div class="col-sm-3">
					<span class="fa fa-file-o fa-4x"></span>
				</div>
				<div class="col-sm-9">
					<h2 class="m-b-0 font-medium"><?=$DCS_value['S'] ?></h2>
					<h5 class="text-muted m-t-0">Stock Value</h5>
				</div>
			</div>
		</div>
	</div>
</div>






