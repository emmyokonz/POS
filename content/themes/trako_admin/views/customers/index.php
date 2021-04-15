<?php
defined('BASEPATH')or exit('No direct script allowed!');
/**
* Developed by Techcoderr Developers
*/

//end of line.
?>

<h3 class="box-title clearfix">
	<?=$page_title ?>
	<small class="label label-info"> <?=($count_records).' '.lang('customers') ?></small>
	<div class="pull-right">

		<a href="<?=site_url(ADMIN.'customers/new') ?>" class="btn btn-danger btn-sm">
			<span class="fa fa-plus"></span>
			<span class="hidden-xs hidden-sm"><?=lang('btn_new_customer') ?></span></a>

	</div>
</h3>
<div  class="panel">
	<div  class="panel-body-table">
		<div class="table-responsive">
			<table class="table table-condensed table-hover">
				<thead>
					<tr>
						<th>sn</th>
						<th>Customer Name</th>
						<th>Balance (<?=config_item('currency')?>)</th>
						<th>Date</th>
						<th></th>
					</tr>
				</thead>
				<tbody>
					<?php
					if (!empty($customers))
						:$n='' ?>
					<?php
					foreach ($customers as $customer)
						: ?>
					<tr>
						<td><?=++$n ?></td>
						<td><?=$customer->name ?></td>
						<td><?=$customer->balance ?></td>
						<td><?=$customer->date ?></td>
						<td align="right"><?=$customer->actions ?></td>
					</tr>
					<?php endforeach; ?>
					<?php else
						: ?>
					<tr>
						<td colspan="5" align="center"><?=sprintf(lang('record_not_found'),strtoupper(lang("btn_new_customer")),'customer') ?></td>
					</tr>
					<?php endif ?>
				</tbody>
				<tfoot>
					<tr>
						<th>sn</th>
						<th>Customer Name</th>
						<th>Balance (<?=config_item('currency') ?>)</th>
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
