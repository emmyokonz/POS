<?php
defined('BASEPATH')or exit('No direct script allowed!');
/**
* Developed by Techcoderr Developers
*/

//end of line.
?>
<h3 class="box-title clearfix">
	<span class="datatable_title"><?=$page_title ?></span>
	<div class="pull-right">
		<div class="row">
			<div class="col-xs-12">
				<div class="form-group-sm  pull-right col-sm-auto">
					<button type="submit" name="apply" class="btn btn-sm btn-primary visible-xs-inline">Apply</button>
					<div id="printbtn">
						<span class="" id="printtopbtn"></span>
					</div>
				</div>
			</div>

		</div>
	</div>
</h3>

<div class="row">
	<div class="col-sm-12 m-t-20">

		<div class="table-responsive p-t-20 p-b-10">
			<table class="table printarea">
				<thead>
					<tr>
						<th>TRANSACTION</th>
						<th>DATE</th>
						<th>AMOUNT</th>
						<th>BALANCE</th>
						<th></th>
					</tr>
				</thead>
				<tbody>
					<?php
					if ($details !==NULL)
						: ?>
					<?php
					foreach ($details as $customer)
						: ?>
					<tr>
						<td><?=$customer->transaction_type ?></td>
						<td><?=$customer->date ?></td>
						<td><?=$customer->amount ?></td>
						<td><?=$customer->balance ?></td>
						<td align="right"><?=$customer->actions?></td>
					</tr>
					<?php endforeach ?>
					<?php endif ?>
				</tbody>
			</table>
		</div>

	</div>
</div>