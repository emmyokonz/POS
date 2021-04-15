<?php
defined('BASEPATH')or exit('No direct script allowed!');
/**
* Developed by Techcoderr Developers
*/

//end of line.
?>
<?=$print_header?>
<div class="row">
	<div class="col-sm-12">
		<h5 class="text-capitalize text-center m-0"><?=config_item('phone') ?>, <?=config_item('email') ?></h5></div></div>
<div class="row">
	<div class="col-sm-12">
		<h4 class="text-uppercase text-center"><?=$transaction->transaction_type ?></h4></div></div>
<div class="row" style="font-size: 1.5rem">
	<div class="col-md-8">
		<strong>consigned to:</strong><br />
		<div><?=$transaction->company ?></div>
		<div><?=$transaction->people_name ?></div>
		<div><?=$transaction->people_address ?></div>
		<div><?=$transaction->email ?> <?=$transaction->phone ?> </div>
	</div>
	<div class="col-md-4">
		<div class="text-right">
			<strong>Sales No: </strong>
			<span><?=$transaction->sales_no ?></span></div>
		<div class="text-right">
			<strong>Sales Date: </strong>
			<span><?=($transaction->created_date) ?></span></div>
		
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
						<th>Price (<?=config_item('currency')?>)</th>
						<th>Amount (<?=config_item('currency') ?>)</th>
					</tr>
				</thead>
				<tbody>
					<?php
					if (!empty($transaction->items))
						:$n='' ?>
					<?php
					foreach ($transaction->items as $sale)
						: ?>
					<tr>
						<td><?=++$n ?></td>
						<td><?=$sale->description ?></td>
						<td><?=$sale->qty ?></td>
						<td><?=my_number_format($sale->price,1) ?></td>
						<td><?=my_number_format($sale->qty * $sale->price,1)?></td>
					</tr>
					<?php endforeach; ?>
					<tr >
						<td colspan="4" align="right">
							<strong>Invoice Amount</strong></td>
						<td>
							<strong><?=config_item('currency') ?> <?=my_number_format($transaction->amount ,1) ?></strong></td>
					</tr>
					<tr >
						<td colspan="4" align="right">
							<strong>Current Balance</strong></td>
						<td>
							<strong><?=config_item('currency') ?> <?=my_number_format($transaction->balance ,1) ?></strong></td>
					</tr>
					<?php else
						: ?>
					<tr>
						<td colspan="7" align="center"><?=sprintf(lang('record_not_found'),strtoupper(lang("btn_new_customer")),'customer') ?></td>
					</tr>
					<?php endif ?>
				</tbody>
			</table>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-sm-12">
		<hr style=" margin-bottom: 150px"/>
		<div class="col-sm-6">
			<div class="row">
				<span style="border-top: 2px dotted #000; padding: 0 20px">
					<b class="text-uppercase">Client Signature</b>
				</span>
			</div>
		</div>
		<div class="col-sm-6 text-right">
			<div class="row">
				<span style="border-top: 2px dotted #000; padding: 0 20px">
					<b class="text-uppercase">Authorized Signature</b>
				</span>
			</div>
		</div>
	</div>
</div>
<div class="row">
	<div class="text-center m-t-20">
		<em class="small">Thank you for stoping by we look forward to conducting future business with you. Kind Regards, Team<br /> <strong>Business Name</strong></em></div>
</div>
<div class="text-center p-t-20">
	<a class="btn btn-primary btn-sm" href="">
		<span class="fa fa-print" ></span> Print Invoice</a>
	<!--<a class="btn btn-primary btn-sm" href="">
		<span class="fa fa-envelope" ></span> Email Invoice</a>-->
	<a class="btn btn-info btn-sm" href="">
		<span class="fa fa-file-pdf-o" ></span> PDF Download</a>
	<!--<a class="btn btn-info btn-sm" href="">
		<span class="fa fa-comment" ></span> SMS Invoice</a>-->
	<a class="btn btn-info btn-sm" href="<?=$transaction->edit_link ?>">
		<span class="fa fa-edit" ></span> Edit Invoice</a>
	<!--<a class="btn btn-success btn-sm" href="">
		<span class="fa fa-whatsapp" ></span> Whatsapp</a>-->
	<a class="btn btn-default btn-sm" href="javascrip:void(0)" onclick="window.history.back()">
		<span class="fa fa-angle-left" ></span> Go Back</a>
</div>

