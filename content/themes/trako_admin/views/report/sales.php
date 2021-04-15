<?php
defined('BASEPATH')or exit('No direct script allowed!');
/**
* Developed by Techcoderr Developers
*/

//end of line.
?>
<h3 class="box-title clearfix">
	<span class="datatable_title"><?=$page_title ?></span>
</h3>

<div class="">
	<form method="post" action="" class="form-horizonta">

		<div class="row">
			<div class="col-xs-6 col-sm-3">
				<div class="form-group-sm form-group">
					<input type="text" autocomplete="off" name="start" id="from" placeholder="FROM" class="form-control" />
				</div>
			</div>
			<div class="col-xs-6 col-sm-3">
				<div class="form-group-sm form-group">
					<input type="text" autocomplete="off" name="end" id="to" placeholder="TO" class="form-control" />
				</div>
			</div>
			<div class="col-xs-6 col-sm-2 hidden-xs">
				<div class="form-group-sm  col-sm-auto">
					<button type="submit" name="apply" class="btn btn-sm btn-primary">Apply</button>
				</div>
			</div>

			<div class="col-xs-12 col-sm-4">
				<div class="form-group-sm  pull-right col-sm-auto">
					<button type="submit" name="apply" class="btn btn-sm btn-primary visible-xs-inline">Apply</button>
					<div id="printbtn">
						<span class="" id="printtopbtn"></span>
					</div>
				</div>
			</div>

		</div>


	</form>
</div>
<div class="sales datatable_message_top">
	<div class="sales-report row clearfix">
		<div class="col-xs-12 col-sm-6 col-md-3">
			<div class="white-box">
				<h2 class="m-b-0 font-medium"><?=$sales_report['sales_amount'] ?></h2>
				<h5 class="text-muted m-t-0">Total Sales Amount</h5>
			</div>
		</div>
		<div class="col-xs-12 col-sm-6 col-md-3">
			<div class="white-box">
				<h2 class="m-b-0 font-medium"><?=$sales_report['sales_count'] ?></h2>
				<h5 class="text-muted m-t-0">Total Sales</h5>
			</div>
		</div>
		<div class="col-xs-12 col-sm-6 col-md-3">
			<div class="white-box">
				<h2 class="m-b-0 font-medium"><?=$sales_report['profit'] ?></h2>
				<h5 class="text-muted m-t-0">Profit</h5>
			</div>
		</div>
		<div class="col-xs-12 col-sm-6 col-md-3">
			<div class="white-box">
				<h2 class="m-b-0 font-medium"><?=$sales_report['items_sold'] ?></h2>
				<h5 class="text-muted m-t-0">Items Sold</h5>
			</div>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 m-t-20">
		
			<div class="table-responsive p-t-20 p-b-10">
			<table class="table printarea">
					<thead>
						<tr>
							<th>INVOICE NO</th>
							<th>NAME</th>
							<th>ITEMS</th>
							<th>STAFF</th>
							<th>DATE</th>
							<th>PROFIT</th>
							<th>AMOUNT</th>
						</tr>
					</thead>
					<tbody>
					<?php if ($sales_report['report'] !== NULL): ?>
					<?php foreach ($sales_report['report'] as $report):?>
						<tr>
							<td><?=$report->sales_no?></td>
							<td><?=$report->people_name?></td>
							<td><?=$report->total_items?></td>
							<td><?=$report->staff?></td>
							<td><?=$report->date?></td>
							<td><?=$report->profit?></td>
							<td><?=$report->amount?></td>
						</tr>
					<?php endforeach ?>
					<?php endif ?>
					</tbody>
				</table>
			</div>
		
	</div>
</div>