<?php
defined('BASEPATH')or exit('No direct script allowed!');
/**
* Developed by Techcoderr Developers
*/

//end of line.
?>

<h3 class="box-title clearfix">
	<?=$page_title ?>
	<small class="label label-info"> <?=($count_records).' '.lang('product') ?></small>
	<div class="pull-right">
		<?=$top_actions?>
	</div>
</h3>
<div class="clearfix"></div>
<div  class="panel">
<div  class="panel-body-table p-t-20">
	<div class="table-responsive">
		<table class="table table-condensed table-hover">
			<thead>
				<tr>
					<th>sn</th>
					<th>SKU</th>
					<th>Name</th>
					<th>Quantity</th>
					<th>Category</th>
					<th><?=lang('date')?></th>
					<th></th>
				</tr>
			</thead>
			<tbody>
				<?php if(!empty($products)):$n=''?>
				<?php foreach($products as $product):?>
					<tr>
						<td><?=++$n?></td>
						<td><?=$product->suk?></td>
						<td><?=$product->name ?></td>
						<td><?=$product->quantity ?></td>
						<td><?=$product->category ?></td>
						<td><?=$product->date ?></td>
						<td align="right"><?=$product->actions ?></td>
					</tr>
					<?php endforeach; ?>
					<?php else: ?>
				<tr>
						<td colspan="8" align="center"><?=sprintf(lang('record_not_found'),strtoupper(lang("btn_new_product")),'product') ?></td>
				</tr>
				<?php endif?>
			</tbody>
			<tfoot>
				<tr>
					<th>sn</th>
					<th>SKU</th>
					<th>Name</th>
					<th>Quantity</th>
					<th>Category</th>
					<th><?=lang('date') ?></th>
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
