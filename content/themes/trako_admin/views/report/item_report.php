<?php
defined('BASEPATH')or exit('No direct script allowed!');
/**
* Developed by Techcoderr Developers
*/

//end of line.
?>
<h3 class="box-title clearfix">
	<span class="datatable_title"><?=$page_title ?></span>
	<div class="pull-right">
		<div class="row">
			<div class="col-xs-12">
				<div class="form-group-sm  pull-right col-sm-auto">
					<button type="submit" name="apply" class="btn btn-sm btn-primary visible-xs-inline">Apply</button>
					<div id="printbtn">
						<span class="" id="printtopbtn"></span>
					</div>
				</div>
			</div>

		</div>
	</div>
</h3>

<div class="sales datatable_message_top">
	<div class="sales-report row clearfix">
		<div class="col-xs-6 col-lg-3">
			<div class="white-box">
				<h2 class="m-b-0 font-medium"><?=$details['qty_at_hand'] ?></h2>
				<h5 class="text-muted m-t-0">Current Quantity</h5>
			</div>
		</div>
		<div class="col-xs-6 col-lg-3">
			<div class="white-box">
				<h2 class="m-b-0 font-medium"><?=$details['amount'] ?></h2>
				<h5 class="text-muted m-t-0">Total Sales</h5>
			</div>
		</div>
		<div class="col-xs-6 col-lg-3">
			<div class="white-box">
				<h2 class="m-b-0 font-medium"><?=$details['profit'] ?></h2>
				<h5 class="text-muted m-t-0">Profit</h5>
			</div>
		</div>
		<div class="col-xs-6 col-lg-3">
			<div class="white-box">
				<h2 class="m-b-0 font-medium"><?=$details['qty_sold'] ?></h2>
				<h5 class="text-muted m-t-0">Quantity Sold</h5>
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
						<th>CUSTOMER</th>
						<th>SALES NUMBER</th>
						<th>DATE</th>
						<th>TYPE</th>
						<th>PRICE</th>
						<th>QUANTITY IN</th>
						<th>QUANTITY OUT</th>
						<th>BALANCE</th>
					</tr>
				</thead>
				<tbody>
					<?php
					if ($details['details'] !==NULL)
						: ?>
					<?php
					foreach ($details['details'] as $product)
						: ?>
					<tr>
						<td><?=$product->customer ?></td>
						<td><?=$product->sales_number ?></td>
						<td><?=$product->date ?></td>
						<td><?=$product->type ?></td>
						<td><?=$product->price ?></td>
						<td><?=$product->quantity_in ?></td>
						<td><?=$product->quantity_out ?></td>
						<td><?=$product->balance ?></td>
					</tr>
					<?php endforeach ?>
					<?php endif ?>
				</tbody>
			</table>
		</div>

	</div>
</div>