<?php
defined('BASEPATH')or exit('No direct script allowed!');
/**
* Developed by Techcoderr Developers
*/

//end of line.
?>
<h3 class="box-title clearfix">
	<span class="datatable_title"><?=$page_title ?> As At <?=date('d/m/Y')?></span>

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
<div class="datatable_message_top"></div>

<div class="row">
	<div class="col-sm-12 m-t-20">

		<div class="table-responsive p-t-20 p-b-10">
			<table class="table printarea">
				<thead>
					<tr>
						<th>PRODUCT NUMBER</th>
						<th>PRODUCT NAME</th>
						<th>PHYSICAL QUANTITY</th>
						<th></th>
					</tr>
				</thead>
				<tbody>
				<?php if($stocks !== NULL):?>
				<?php foreach($stocks as $stock):?>
					<tr>
						<td><?=$stock->product_number?></td>
						<td><?=$stock->name?></td>
						<td><?=$stock->quantity?></td>
						<td>-----------------------</td>
					</tr>
				<?php endforeach?>
				<?php endif?>
				</tbody>
			</table>
		</div>

	</div>
</div>