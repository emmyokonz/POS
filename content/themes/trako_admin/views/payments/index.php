<?php
defined('BASEPATH')or exit('No direct script allowed!');
/**
* Developed by Techcoderr Developers
*/

//end of line.
?>

<h3 class="box-title clearfix">
	<?=$page_title ?>
	<small class="label label-info"><?= $count_records.' '.lang('payment') ?></small>
	<div class="pull-right">

		<a href="<?=site_url(ADMIN.'payments/make_payment') ?>" class="btn btn-info btn-sm">
			<span class="fa fa-reply"></span>
			<span class="hidden-xs hidden-sm"><?=lang('btn_make_payment') ?></span></a>
		<a href="<?=site_url(ADMIN.'payments/receive_payment') ?>" class="btn btn-danger btn-sm">
			<span class="fa fa-mail-forward"></span>
			<span class="hidden-xs hidden-sm"><?=lang('btn_receive_payment') ?></span></a>

	</div>
</h3>
<div  class="panel">
	<div  class="panel-body-table">
		<div class="table-responsive">
			<table class="table table-condensed table-hover">
				<thead>
					<tr>
						<th>sn</th>
						<th>Account</th>
						<th>Name</th>
						<th>Amount</th>
						<th>Date</th>
						<th></th>
					</tr>
				</thead>
				<tbody>
					<?php
				if (!empty($payments))
					:$n='' ?>
					<?php
					foreach ($payments as $payment)
					: ?>
					<tr>
						<td><?=++$n ?></td>
						<td><?=$payment->account ?></td>
						<td><?=$payment->customer ?></td>
						<td><?=$payment->amount ?></td>
						<td><?=$payment->date ?></td>
						<td align="right"><?=$payment->actions ?></td>
					</tr>
					<?php endforeach; ?>
					<?php else
					: ?>
					<tr>
						<td colspan="7" align="center"><?=sprintf(lang('record_not_found'),strtoupper(lang("btn_new_customer")),'customer') ?></td>
					</tr>
					<?php endif ?>
				</tbody>
				<tfoot>
					<tr>
						<th>sn</th>
						<th>Account</th>
						<th>Name</th>
						<th>Amount</th>
						<th>Date</th>
						<th></th>
					</tr>
				</tfoot>
			</table>
		</div>
	</div>
	<div class="panel-footer p-10">

		<div class="text-right">
			<?=$pagination ?>
		</div>
	</div>
</div>
