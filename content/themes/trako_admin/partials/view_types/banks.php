<table class="table table-condensed table-hover">
	<thead>
		<tr>
			<th>sn</th>
			<th>Date</th>
			<th>Type</th>
			<th>Name</th>
			<th>Account</th>
			<th>Description</th>
			<th>Debit</th>
			<th>Credit</th>
			<th>Balance</th>
			<th></th>
		</tr>
	</thead>
	<tbody>
		<?php //print_r($account_details);exit;
		if ($account_details['details'] !== NULL && is_array($account_details['details']))
			:$n=''; /*echo "<pre>";print_r($account_details);exit;*/ ?>
		<?php
		foreach ($account_details['details'] as $details)
			: ?>
		<tr>
			<td><?=++$n ?></td>
			<td><?=my_full_time_span($details->date) ?></td>
			<td><?=$details->transaction ?></td>
			<td><?=$details->name ?></td>
			<td><?=$details->account ?></td>
			<td title="<?=$details->memo?>"><?=$details->memo_reduce ?></td>
			<td><?=$details->debit ?></td>
			<td><?=$details->credit ?></td>
			<td><?=$details->balance ?></td>
			<td><?=$details->action ?></td>
		</tr>
		<?php endforeach; ?>
		<?php endif; ?>
	</tbody>
	<tfoot>
		<tr>
			<th>sn</th>
			<th>Date</th>
			<th>Type</th>
			<th>Name</th>
			<th>Account</th>
			<th>Description</th>
			<th>Debit</th>
			<th>Credit</th>
			<th>Balance</th>
			<th></th>
		</tr>
	</tfoot>
</table>