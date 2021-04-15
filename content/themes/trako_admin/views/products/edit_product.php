<?php
defined('BASEPATH')or exit('No direct script allowed!');
/**
* Developed by Techcoderr Developers
*/

//end of line.
?>
<!-- Nav tabs -->
<ul class="nav nav-tabs" role="tablist">
	<li role="presentation" class="active">
		<a href="#home" aria-controls="home" role="tab" data-toggle="tab" aria-expanded="true">
			<span class="visible-xs">
				<i class="ti-home"></i></span>
			<span class="hidden-xs"> Product Information</span></a></li>
	<li role="presentation" class="">
		<a href="#profile" aria-controls="profile" role="tab" data-toggle="tab" aria-expanded="false">
			<span class="visible-xs">
				<i class="ti-user"></i></span>
			<span class="hidden-xs">Transactions</span></a></li>
</ul>
<!-- Tab panes -->
<div class="tab-content">
	<div role="tabpanel" class="tab-pane active" id="home">
		<?=form_open(current_url(),[]) ?>
		<h3 class="box-title clearfix">
			<?=$page_title ?>
			<div class="pull-right">
				<button type="submit" class="btn btn-info btn-sm">
					<span class="fa fa-check"></span>
					<span class="hidden-xs hidden-sm"><?=lang('btn_save') ?></span></button>

				<button type="submit" class="btn btn-info btn-sm">
					<span class="fa fa-save"></span>
					<span class="hidden-xs hidden-sm"><?=lang('btn_save_and_close') ?></span></button>

				<a href="<?=site_url(previous_url('products',true)) ?>" class="btn btn-default btn-sm">
					<span class="fa fa-close"></span>
					<span class="hidden-xs hidden-sm"><?=lang('btn_cancle') ?></span></a>
			</div>
		</h3>
		<hr />
		<div class="row">
			<div class="col-sm-12">
				<div class="form-group form-group-lg">
					<label class="control-label" for="sender"><?=lang('product_name_label') ?>
						<span class="text-danger">*</span></label>
					<div class="col-s"><?=form_input($product_name) ?></div>
				</div>
				<div class="row">
					<div class="col-sm-9">
						<div class="form-group">
							<label class="control-label" for="sender"><?=lang('product_category_label') ?>
								<a href="javascript:void(0);" class="dialog opendialog" data-target="#pageContainer" data-url="<?=ajax_url("pages/new_category_widget") ?>" data-toggle="modal" data-whatever="<?=lang('product_new_category_title') ?>">(<?=lang('product_new_category_label') ?>)</a>
								<span class="text-danger">*</span></label>
							<div class="col-s"><?=form_dropdown($product_category) ?></div>
							<i class="help-block text-muted"><?=lang('product_category_desc') ?></i>
						</div>
						<div class="form-group">
							<label class="control-label" for="sender"><?=lang('product_description_label') ?></label>
							<?=form_textarea($product_description) ?>
							<i class="help-block text-muted"><?=lang('product_description_desc') ?></i>
						</div>
					</div>
					<div class="col-sm-3">
						<div class="form-group">
							<label class="control-label" for="sender"><?=lang('product_quantity_label') ?></label>
							<div class="col-s"><?=form_input($product_quantity) ?></div>
						</div>
						<div class="form-group">
							<label class="control-label" for="sender"><?=lang('product_selling_label') ?></label>
							<div class="col-s"><?=form_input($product_selling_price) ?></div>
						</div>
						<div class="form-group">
							<label class="control-label" for="sender"><?=lang('product_cost_label') ?></label>
							<div class="col-s"><?=form_input($product_cost_price) ?></div>
						</div>
					</div>
				</div>
				<button type="submit" name="submit" value="save" class="btn btn-info btn-sm">
					<span class="fa fa-check"></span>
					<span class="hidden-xs hidden-sm"><?=lang('btn_save') ?></span></button>

				<button type="submit" class="btn btn-info btn-sm" name="submit" value="saveandclose">
					<span class="fa fa-save"></span>
					<span class="hidden-xs hidden-sm"><?=lang('btn_save_and_close') ?></span></button>

				<a href="<?=site_url(previous_url('dashboard',true)) ?>" class="btn btn-default btn-sm">
					<span class="fa fa-close"></span>
					<span class="hidden-xs hidden-sm"><?=lang('btn_cancle') ?></span></a>
			</div>
		</div>
		<?=form_close() ?>
		<div class="clearfix"></div>
	</div>
	<div role="tabpanel" class="tab-pane" id="profile">
		<h4 class="box-title">
			<?=$name.' ' .lang("transactions") ?>
			<small class="label label-info"><?='('.$count_result.lang("transactions").')' ?></small>
		</h4>
		<div class="panel-body-table">
			<div class="table-responsive">
				<table class="table table-condensed table-hover">
					<thead>
						<tr>
							<th>sn</th>
							<th>Invoice Id</th>
							<th>Name</th>
							<th>Quantity</th>
							<th>Date</th>
							<th></th>
						</tr>
					</thead>
					<tbody>
						<?php
						if (!is_null($product_transactions))
						:$n='' ?>
						<?php
						foreach ($product_transactions as $transaction)
						: ?>
						<tr>
							<td><?=++$n ?></td>
							<td><?=ucwords($transaction->invoice_no) ?></td>
							<td><?=$transaction->name ?></td>
							<td><?=$transaction->qty ?></td>
							<td><?=$transaction->date ?></td>
							<td align="right"></td>
						</tr>
						<?php endforeach; ?>
						<?php else
						: ?>
						<tr>
							<td colspan="6" align="center"><?=lang('no_transaction_found') ?></td>
						</tr>
						<?php endif ?>
					</tbody>
					<tbody>
						<tr>
							<td>sn</td>
							<td>Invoice Id</td>
							<td>Quantity</td>
							<td>Price</td>
							<td>Date</td>
							<td align="right"></td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
<?=$new_category ?>