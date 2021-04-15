<?php
defined('BASEPATH')or exit('No direct script allowed!');
/**
* Developed by Techcoderr Developers
*/

//end of line.
?>
<?=form_open(current_url(),[]) ?>
<h3 class="box-title clearfix">
	<?=$page_title ?>
	<div class="pull-right">
		<button type="submit" name="submit" value="save" class="btn btn-info btn-sm">
			<span class="fa fa-check"></span>
			<span class="hidden-xs hidden-sm"><?=lang('btn_save') ?></span></button>

		<button type="submit" class="btn btn-info btn-sm" value="saveandclose" name="submit">
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
					<?=form_input($customer_name)?>
					</div>
					<div class="form-group">
						<label class="control-label" for="sender"><?=lang('customer_company_name_label') ?>
							<span class="text-danger">*</span></label>
					<?=form_input($customer_company) ?>
					<i class="help-block text-muted"><?=lang('customer_company_name_desc') ?></i>
					</div>
				<div class="form-group form-group-sm">
					<label class="control-label" for="sender"><?=lang('customer_opening_bal_label') ?>
						<span class="text-danger">*</span></label>
					<?=form_input($customer_opening_bal) ?>
					<i class="help-block text-muted"><?=lang('customer_opening_bal_desc') ?></i>
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
						<h2>0.00</h2>
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