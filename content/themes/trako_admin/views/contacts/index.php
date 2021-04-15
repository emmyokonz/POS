<?php
defined('BASEPATH')or exit('No direct script allowed!');
/**
* Developed by Techcoderr Developers
*/

//end of line.
?>


<h3 class="box-title clearfix"class="box-title clearfix">
<?=lang('contact_title') ?>
	<small class="label label-info">250 contacts</small>
	<div class="pull-right">
		<a href="<?=site_url(ADMIN.'contacts/new') ?>" class="btn btn-danger btn-sm">
			<span class="fa fa-plus"></span>
			<span class="hidden-xs hidden-sm"><?=lang('btn_new_contact') ?></span></a>
	</div>
</h3>
<hr />
<div class="panel">
	<div  class="panel-body-table">
		<div class="table-responsive">
			<table class="table table-condensed table-hover">
				<thead>
					<tr>
						<th>sn</th>
						<th>Name</th>
						<th>Phone</th>
						<th>Group</th>
						<th>Date</th>
						<th></th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>1</td>
						<td>ikechukwu</td>
						<td>09098987876</td>
						<td>Business</td>
						<td>10th june, 2019 10:34 AM</td>
						<td align="right">
							<a href="<?=site_url(ADMIN.'contacts/edit')?>" class="btn-info btn btn-sm"><i class="fa fa-edit"></i> Edit</a>
							<a href="" class="btn-danger btn btn-sm"><i class="fa fa-trash"></i> Trash</a>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
</div>
