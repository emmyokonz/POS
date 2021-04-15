<?php
defined('BASEPATH')or exit('No direct script allowed!');
/**
* Developed by Techcoderr Developers
*/

//end of line.
?>

<h3 class="box-title clearfix">
	<?=$page_title ?>
	<small class="label label-info"><?=$count_records. ' '. lang('purchases') ?></small>
	<div class="pull-right">

		<a href="<?=site_url(ADMIN.'purchases/new_return') ?>" data-sales="return" class="btn __new btn-warning btn-sm">
			<span class="fa fa-plus-circle"></span>
			<span class="hidden-xs hidden-sm"><?=lang('btn_new_return') ?></span></a>

		<a href="<?=site_url(ADMIN.'purchases/new') ?>" data-sales="purchase" class="btn __new btn-danger btn-sm">
			<span class="fa fa-plus"></span>
			<span class="hidden-xs hidden-sm"><?=lang('btn_new_purchases') ?></span></a>

	</div>
</h3>
<div  class="panel">
	<div  class="panel-body-table">
		<div class="table-responsive">
			<table class="table table-condensed table-hover">
				<thead>
					<tr>
						<th>sn</th>
						<th>Purchase No</th>
						<th>Supplier</th>
						<th>Total Items</th>
						<th>Amount</th>
						<th>Date</th>
						<th></th>
					</tr>
				</thead>
				<tbody>
					<?php
					if (!empty($purchases))
						:$n='' ?>
					<?php
					foreach ($purchases as $sale)
						: ?>
					<tr>
						<td><?=++$n ?></td>
						<td><?=$sale->purchases_no ?></td>
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
						<td colspan="7" align="center"><?=sprintf(lang('record_not_found'),strtoupper(lang("btn_new_customer")),'customer') ?></td>
					</tr>
					<?php endif ?>
				</tbody>
				<tfoot>
					<tr>
						<th>sn</th>
						<th>purchases No</th>
						<th>Supplier</th>
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
