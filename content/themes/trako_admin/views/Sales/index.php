<?php
defined('BASEPATH')or exit('No direct script allowed!');
/**
* Developed by Techcoderr Developers
*/

//end of line.
?>

<h3 class="box-title clearfix">
	<?=$page_title ?>
	<small class="label label-info"><?=$count_records. ' '. lang('sales') ?></small>
	<div class="pull-right">

		<a href="<?=site_url(ADMIN.'sales/new_credit_memo') ?>" data-sales="credit_memo" class="btn btn-warning __new btn-sm">
			<span class="fa fa-plus-circle"></span>
			<span class="hidden-xs hidden-sm"><?=lang('btn_new_credit_memo') ?></span></a>

		<a href="<?=site_url(ADMIN.'sales/new') ?>" data-sales="sales_cart" class="btn btn-danger __new btn-sm">
			<span class="fa fa-plus"></span>
			<span class="hidden-xs hidden-sm"><?=lang('btn_new_sales') ?></span></a>

	</div>
</h3>
<div  class="panel">
	<div  class="panel-body-table">
		<div class="table-responsive">
			<table class="table table-condensed table-hover">
				<thead>
					<tr>
						<th>sn</th>
						<th>Sales Code</th>
						<th>Customer</th>
						<th>Total Items</th>
						<th>Amount</th>
						<th>Date</th>
						<th></th>
					</tr>
				</thead>
				<tbody>
					<?php
					if (!empty($sales))
						:$n='' ?>
					<?php
					foreach ($sales as $sale)
						: ?>
					<tr>
						<td><?=++$n ?></td>
						<td><?=$sale->sales_no ?></td>
						<td><?=$sale->customer ?></td>
						<td><?=$sale->total_items ?></td>
						<td><?=$sale->amount ?></td>
						<td><?=$sale->date ?></td>
						<td align="right"><?=$sale->actions ?></td>
					</tr>
					<?php endforeach; ?>
					<?php else
						: ?>
					<tr>
						<td colspan="7" align="center"><?=sprintf(lang('record_not_found'),strtoupper(lang("btn_new_sales")),'Sales') ?></td>
					</tr>
					<?php endif ?>
				</tbody>
				<tfoot>
					<tr>
						<th>sn</th>
						<th>Sales Code</th>
						<th>Customer</th>
						<th>Total Items</th>
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
