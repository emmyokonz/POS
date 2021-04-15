<?php
defined('BASEPATH')or exit('No direct script allowed!');
/**
* Developed by Techcoderr Developers
*/

//end of line.
?>

<h3 class="box-title clearfix">
	<?=$page_title ?>
	<small class="label label-info"><?=$count_records. ' '. lang('accounts') ?></small>
	<div class="pull-right">

		<a href="<?=site_url(ADMIN.'accounts/new') ?>" class="btn btn-danger btn-sm">
			<span class="fa fa-plus"></span>
			<span class="hidden-xs hidden-sm"><?=lang('btn_new_account') ?></span></a>
	</div>
</h3>
<div  class="panel">
	<div  class="panel-body-table">
		<div class="table-responsive">
			<table class="table table-condensed table-hover">
				<thead>
					<tr>
						<th>sn</th>
						<th>Name</th>
						<th>Account Type</th>
						<th>Balance</th>
						<th></th>
					</tr>
				</thead>
				<tbody>
					<?php
					if (!empty($accounts))
						:$n='' ?>
					<?php
					foreach ($accounts as $account)
						: ?>
					<tr>
						<td><?=$account->sn?></td>
						<td><?=$account->name?></td>
						<td><?=$account->account_type?></td>
						<td><?=$account->balance?></td>
						<td align="right"><?=$account->actions?></td>
					</tr>
					<?php endforeach; ?>
					<?php else
						: ?>
					<tr>
						<td colspan="7" align="center"><?=sprintf(lang('record_not_found'),strtoupper(lang("btn_new_account")),'account') ?></td>
					</tr>
					<?php endif ?>
				</tbody>
				<tfoot>
					<tr>
						<th>sn</th>
						<th>Name</th>
						<th>Account Type</th>
						<th>Balance</th>
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
