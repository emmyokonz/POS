<?php
defined('BASEPATH')or exit('No direct script allowed!');
/**
* Developed by Techcoderr Developers
*/

//end of line.
?>
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
			<span class="hidden-xs"> Customer Information</span></a></li>
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
				<button type="submit" name="submit" value="save" class="btn btn-info btn-sm">
					<span class="fa fa-check"></span>
					<span class="hidden-xs hidden-sm"><?=lang('btn_save') ?></span></button>

				<button type="submit" name="submit" value="saveandclose" class="btn btn-info btn-sm">
					<span class="fa fa-save"></span>
					<span class="hidden-xs hidden-sm"><?=lang('btn_save_and_close') ?></span></button>

				<a href="<?=site_url(previous_url('suppliers',true)) ?>" class="btn btn-default btn-sm">
					<span class="fa fa-close"></span>
					<span class="hidden-xs hidden-sm"><?=lang('btn_cancle') ?></span></a>
			</div>
		</h3>
		<hr />
		<div class="row">
			<div class="col-sm-12">
				<div class="row">
					<div class="col-sm-9">
						<div class="form-group form-group-lg">
							<label class="control-label" for="sender"><?=lang('supplier_name_label') ?>
								<span class="text-danger">*</span></label>
							<?=form_input($supplier_name) ?>
						</div>
						<div class="form-group">
							<label class="control-label" for="sender"><?=lang('supplier_company_name_label') ?>
								<span class="text-danger">*</span></label>
							<?=form_input($supplier_company) ?>
							<i class="help-block text-muted"><?=lang('supplier_company_name_desc') ?></i>
						</div>
						<div class="form-group">
							<label class="control-label" for="sender"><?=lang('supplier_billing_address_label') ?></label>
							<div class="col-s">
								<?=form_input($supplier_billing_address) ?>
								<i class="help-block text-muted"><?=lang('supplier_billing_address_desc') ?></i>
							</div>
						</div>
					</div>
					<div class="col-sm-3">
						<div class="form-group">
							<label class="control-label" for="sender"><?=lang('supplier_address_label') ?></label>
							<?=form_input($supplier_contact_address) ?>
						</div>
						<div class="form-group">
							<label class="control-label" for="sender"><?=lang('supplier_phone_label') ?></label>
							<?=form_input($supplier_phone) ?>
						</div>
						<div class="form-group">
							<label class="control-label" for="sender"><?=lang('supplier_email_label') ?></label>
							<?=form_input($supplier_email) ?>
						</div>
						<div class="form-group">
							<label class="control-label" for="sender"><?=lang('supplier_balance_label') ?></label>
							<h2><?=$balance ?></h2>
						</div>
					</div>
				</div>
				<button type="submit" class="btn btn-info btn-sm" name="submit" value="save">
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

		<h4 class="box-title"><?= $name.' Transactions' ?>
			<small class="label label-info"><?=($count_records).' '.lang('transactions') ?></small>
		</h4>

		<div class="table-responsive">
			<table class="table table-condensed table-hover">
				<thead>
					<tr>
						<th>sn</th>
						<th>Transaction</th>
						<th>Date</th>
						<th>Amount (<?=config_item('currency') ?>)</th>
						<th></th>
					</tr>
				</thead>
				<tbody>
					<?php
						if (!empty($supplier_transactions))
							:$n='' ?>
									<?php
						foreach ($supplier_transactions as $supplier)
							: ?>
					<tr>
						<td><?=++$n ?></td>
						<td><?=ucwords($supplier->transaction_type) ?></td>
						<td><?=$supplier->date ?></td>
						<td><?=$supplier->amount ?></td>
						<td align="right"><?=$supplier->actions ?></td>
					</tr>
					<?php endforeach; ?>
					<?php else
			: ?>
					<tr>
						<td colspan="5" align="center"><?=(lang('no_transaction_found')) ?></td>
					</tr>
					<?php endif ?>
				</tbody>
			</table>
		</div>
		<div class="clearfix"></div>
	</div>
</div>