<table class="table table-condensed">
	<thead>
		<tr>
			<th>sn</th>
			<th>Date</th>
			<th>Name</th>
			<th>Bank</th>
			<th>Description</th>
			<th></th>
		</tr>
	</thead>
	<tbody>
		<?php
		if ($account_details['details'] !== NULL && is_array($account_details['details']))
			:$n=''; /*echo "<pre>";print_r($account_details);exit;*/ ?>
		<?php
		foreach ($account_details['details'] as $details)
			: ?>
		<tr>
			<td><?=++$n?></td>
			<td><?=my_full_time_span($details->date)?></td>
			<td><?=$details->name?></td>
			<td><?=$details->bank?></td>
			<td><?=$details->memo?></td>
			<td align="right"><?=$details->amount?></td>
		</tr>
		<?php endforeach; ?>
		<?php endif; ?>
		<tr>
			<td colspan="5" align="right"><b>Total</b></td>
			<td align="right" style="border-top: 5px double #e4e7ea;border-bottom: 5px double #e4e7ea;">
				<b><?=($account_details)['total_amount'] ?></b></td>
		</tr>
	</tbody>
</table>