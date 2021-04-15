<?php
defined('BASEPATH')or exit('No direct script allowed!');
/**
* Developed by Techcoderr Developers
*/

//end of line.
?>

<h3 class="box-title clearfix">
	<?=$page_title ?>
	<div class="pull-right">
		<div class="pull-right">
			<a href="<?=site_url('expenses') ?>" class="btn  btn-sm btn-default">
				<span class="fa fa-arrow-left"></span> <?=lang('btn_back_to_expenses') ?></a>
		</div>
	</div>
</h3>
<div  class="row">
	<div  class="col-sm-6 col-md-8">
		<div class="table-responsive">
			<table class="table table-condensed table-hover">
				<thead>
					<tr>
						<th><input type="checkbox" id="checkbox-all"/></th>
						<th>sn</th>
						<th>Reason</th>
						<th>Staff</th>
						<th>Amount</th>
						<th>Date</th>
						<th></th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td><input class="checkbox" id="checkbox0" type="checkbox"></td>
						<td>1</td>
						<td>Reason</td>
						<td>Hardline by 5 no1</td>
						<td>70,000.00</td>
						<td>10th june, 2019 10:34 AM</td>
						<td align="right">
							<a href="<?=site_url(ADMIN.'expenses/edit') ?>" class="btn-info btn btn-sm">
								<i class="fa fa-edit"></i> <?=lang('btn_edit') ?></a>
							<a href="<?=site_url(ADMIN.'expenses/delete') ?>" class="btn-danger btn btn-sm">
								<i class="fa fa-trash"></i> <?=lang('btn_delete') ?></a>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
	<div class="col-sm-6 col-md-4"><?=$expenses ?>	</div>
</div>
