<?php
defined('BASEPATH')or exit('No direct script allowed!');
/**
* Developed by Techcoderr Developers
*/

//end of line.
?>
<h3 class="box-title clearfix">
	<span class="datatable_title"><?=$page_title ?></span>
</h3>

<div class="">
	<form method="post" action="" class="form-horizonta">

		<div class="row">
			<div class="col-xs-6 col-sm-3">
				<div class="form-group-sm form-group">
					<input type="text" autocomplete="off" name="start" id="from" placeholder="FROM" class="form-control" />
				</div>
			</div>
			<div class="col-xs-6 col-sm-3">
				<div class="form-group-sm form-group">
					<input type="text" autocomplete="off" name="end" id="to" placeholder="TO" class="form-control" />
				</div>
			</div>
			<div class="col-xs-6 col-sm-2 hidden-xs">
				<div class="form-group-sm  col-sm-auto">
					<button type="submit" name="apply" class="btn btn-sm btn-primary">Apply</button>
				</div>
			</div>

			<div class="col-xs-12 col-sm-4">
				<div class="form-group-sm  pull-right col-sm-auto">
					<button type="submit" name="apply" class="btn btn-sm btn-primary visible-xs-inline">Apply</button>
					<div id="printbtn">
						<span class="" id="printtopbtn"></span>
					</div>
				</div>
			</div>

		</div>


	</form>
</div>
<div class="datatable_message_top"></div>
<div class="row">
	<div class="col-sm-12 m-t-20">

		<div class="table-responsive p-t-20 p-b-10">
			<table class="table printarea">
				<thead>
					<tr>
						<th>ITEM</th>
						<th>QUANTITY AT HAND</th>
						<th>CURRENT PRICE</th>
						<th>QUANTITY SOLD</th>
						<th>SOLD AMOUNT</th>
						<th>AVERAGE PRICE</th>
						<th>PROFIT</th>
						<th>PROFIT %</th>
						<th>SOLD %</th>
					</tr>
				</thead>
				<tbody>
					<?php
					if ($inventory !==NULL)
						: ?>
					<?php
					foreach ($inventory as $product)
						: ?>
					<tr>
						<td><?=$product->name ?></td>
						<td><?=$product->quantity ?></td>
						<td><?=$product->price ?></td>
						<td><?=$product->qty_sold ?></td>
						<td><?=$product->amount ?></td>
						<td><?=$product->a_price ?></td>
						<td><?=$product->profit ?></td>
						<td><?=$product->profit_p ?></td>
						<td><?=$product->amount_p ?></td>
					</tr>
					<?php endforeach ?>
					<?php endif ?>
				</tbody>
			</table>
		</div>

	</div>
</div>