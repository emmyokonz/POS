<?php
defined('BASEPATH')or exit('No direct script allowed!');
/**
* Developed by Techcoderr Developers
*/

//end of line.
?>

<h3 class="box-title clearfix">
	<?=$page_title ?>
	<small class="label label-info"><?=($count_records).' '.lang('suppliers') ?></small>
	<div class="pull-right">

		<a href="<?=site_url(ADMIN.'suppliers/new') ?>" class="btn btn-danger btn-sm">
			<span class="fa fa-plus"></span>
			<span class="hidden-xs hidden-sm"><?=lang('btn_new_supplier') ?></span></a>

	</div>
</h3>
<div  class="panel">
	<div  class="panel-body-table">
		<div class="table-responsive">
			<table class="table table-condensed table-hover">
				<thead>
					<tr>
						<th>sn</th>
						<th>supplier Name</th>
						<th>Balance (<?=config_item('currency') ?>)</th>
						<th>Date</th>
						<th></th>
					</tr>
				</thead>
				<tbody>
					<?php
					if (!empty($suppliers))
						:$n='' ?>
					<?php
					foreach ($suppliers as $supplier)
						: ?>
					<tr>
						<td><?=++$n ?></td>
						<td><?=$supplier->name ?></td>
						<td><?=$supplier->balance ?></td>
						<td><?=$supplier->date ?></td>
						<td align="right"><?=$supplier->actions ?></td>
					</tr>
					<?php endforeach; ?>
					<?php else
						: ?>
					<tr>
						<td colspan="5" align="center"><?=sprintf(lang('record_not_found'),strtoupper(lang("btn_new_supplier")),'supplier') ?></td>
					</tr>
					<?php endif ?>
				</tbody>
				<tfoot>
					<tr>
						<th>sn</th>
						<th>supplier Name</th>
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
