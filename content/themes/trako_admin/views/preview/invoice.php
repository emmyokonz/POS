<?php
defined('BASEPATH')or exit('No direct script allowed!');
/**
* Developed by Techcoderr Developers
*/

//end of line.
?>
<div class="row">
	<div class="col-sm-12">
		<h3 class="text-uppercase text-center">Elitraco Nigeria Enterprises</h3></div></div>
<div class="row">
	<div class="col-sm-12">
		<h5 class="text-capitalize text-center m-0">66 A-Line lock-up shop Ariaria international mkt aba, abia state.</h5></div></div>
<div class="row">
	<div class="col-sm-12">
		<h5 class="text-capitalize text-center">090898326723, 326783266782, elitraco@email.com</h5></div></div>
<div class="row">
	<div class="col-sm-12">
		<h4 class="text-uppercase text-center"><?=$transaction['transaction_type'] ?></h4></div></div>
<div class="row" style="font-size: 1.5rem">
	<div class="col-md-8">
		<strong>consigned to:</strong><br />
		<div><?=$transaction['company'] ?></div>
		<div><?=$transaction['people_name'] ?></div>
		<div><?=$transaction['people_address'] ?></div>
		<div><?=$transaction['email'] ?> <?=$transaction['phone'] ?> </div>
	</div>
	<div class="col-md-4">
		<div class="text-right">
			<strong>Sales Date: </strong>
			<span><?=$transaction['created_date'] ?></span></div>

	</div>
</div>
<div class="row m-t-10">
	<div class="col-sm-12">
		<div class="table-responsive">
			<table class="table table-bordered table-hover">
				<thead>
					<tr>
						<th>SN</th>
						<th>Description</th>
						<th>Quantity</th>
						<th>Price (<?=config_item('currency') ?>)</th>
						<th>Amount (<?=config_item('currency') ?>)</th>
					</tr>
				</thead>
				<tbody>
					<?php
					if (!empty($transaction['items']))
						:$n='' ?>
					<?php
					foreach ($transaction['items'] as $sale)
						: ?>
					<tr>
						<td><?=++$n ?></td>
						<td><?=$sale['description'] ?></td>
						<td><?=$sale['quantity'] ?></td>
						<td><?=my_number_format($sale['price'],1) ?></td>
						<td><?=my_number_format($sale['quantity'] * $sale['price'],1) ?></td>
					</tr>
					<?php endforeach; ?>
					<tr >
						<td colspan="4" align="right">
							<strong>Invoice Amount</strong></td>
						<td>
							<strong><?=config_item('currency') ?> <?=my_number_format($transaction['subtotal'] ,1) ?></strong></td>
					</tr>
					<?php else
						: ?>
					<tr>
						<td colspan="7" class="bg-warning text-white" align="center"><?=lang('error_loading_transaction') ?></td>
					</tr>
					<?php endif ?>
				</tbody>
			</table>
		</div>
	</div>
</div>
<div class="text-center p-t-20">
	<a class="btn btn-default btn-sm" href="javascript:void(0)" onclick="window.history.back()">
		<span class="fa fa-angle-left" ></span> Go Back</a>
</div>