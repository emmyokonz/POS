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

				<a href="<?=site_url(previous_url('customers',true)) ?>" class="btn btn-default btn-sm">
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
							<label class="control-label" for="sender"><?=lang('customer_name_label') ?>
								<span class="text-danger">*</span></label>
							<?=form_input($customer_name) ?>
						</div>
						<div class="form-group">
							<label class="control-label" for="sender"><?=lang('customer_company_name_label') ?>
								<span class="text-danger">*</span></label>
							<?=form_input($customer_company) ?>
							<i class="help-block text-muted"><?=lang('customer_company_name_desc') ?></i>
						</div>
						<div class="form-group">
							<label class="control-label" for="sender"><?=lang('customer_billing_address_label') ?></label>
							<div class="col-s">
								<?=form_input($customer_billing_address) ?>
								<i class="help-block text-muted"><?=lang('customer_billing_address_desc') ?></i>
							</div>
						</div>
					</div>
					<div class="col-sm-3">
						<div class="form-group">
							<label class="control-label" for="sender"><?=lang('customer_address_label') ?></label>
							<?=form_input($customer_contact_address) ?>
						</div>
						<div class="form-group">
							<label class="control-label" for="sender"><?=lang('customer_phone_label') ?></label>
							<?=form_input($customer_phone) ?>
						</div>
						<div class="form-group">
							<label class="control-label" for="sender"><?=lang('customer_email_label') ?></label>
							<?=form_input($customer_email) ?>
						</div>
						<div class="form-group">
							<label class="control-label" for="sender"><?=lang('customer_balance_label') ?></label>
							<h2><?=$balance ?></h2>
						</div>
					</div>
				</div>
				<button type="submit" name="submit" value="save" class="btn btn-info btn-sm">
					<span class="fa fa-check"></span>
					<span class="hidden-xs hidden-sm"><?=lang('btn_save') ?></span></button>

				<button type="submit" name="submit" value="saveandclose" class="btn btn-info btn-sm">
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

			<div class="panel-body-table">
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
							if (!empty($customer_transactions))
								:$n='' ?>
														<?php
							foreach ($customer_transactions as $customer)
								: ?>
							<tr>
								<td><?=++$n ?></td>
								<td><?=ucwords($customer->transaction_type) ?></td>
								<td><?=$customer->date ?></td>
								<td><?=$customer->amount ?></td>
								<td align="right"><?=$customer->actions ?></td>
							</tr>
							<?php endforeach; ?>
							<?php else
	: ?>
							<tr>
								<td colspan="5" align="center"><?=lang('no_transaction_found') ?></td>
							</tr>
							<?php endif ?>
						</tbody>
					</table>
				</div>
			</div>
			<div class="clearfix"></div>
		</div>
	</div>
