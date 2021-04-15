<?php
defined('BASEPATH')or exit('No direct script allowed!');
/**
* Developed by Techcoderr Developers
*/

//end of line.
?>
<h3 class="box-title clearfix">
	<?=$page_title ?> 
	<small class="badge badge-info"><?=($count_records).' '.lang('product_categorys') ?></small>
	<div class="pull-right">
		<?=$top_actions ?>
	</div>
</h3>
<hr />
<div class="row">
	<div class="col-sm-12">
		<div class="table-responsive">
			<table class="table table-condensed table-hover">
				<thead>
					<tr>
						<td>sn</td>
						<td>Name</td>
						<td>Products</td>
						<td>Date</td>
						<td></td>
					</tr>
				</thead>
				<tbody>
					<?php
					if (!empty($categories))
						:$n='' ?>
					<?php
					foreach ($categories as $category)
						: ?>
					<tr>
						<td><?=++$n ?></td>
						<td><?=$category->name ?></td>
						<td><?=$category->products ?></td>
						<td><?=$category->date ?></td>
						<td align="right"><?=$category->actions ?></td>
					</tr>
					<?php endforeach; ?>
					<?php else
						: ?>
					<tr>
						<td colspan="5" align="center"><?=sprintf(lang('record_not_found'),strtoupper(lang("btn_new_category")),'category') ?></td>
					</tr>
					<?php endif ?>
				</tbody>
				<tfoot>
					<tr>
						<td>sn</td>
						<td>Name</td>
						<td>Products</td>
						<td>Date</td>
						<td></td>
					</tr>
				</tfoot>
			</table>
		</div>
		<div class="">
			<div class="">
				<?=$pagination ?>
			</div>
		</div>
	</div>
</div>