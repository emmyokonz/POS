<?php
defined('BASEPATH')or exit('No direct script allowed!');
/**
* Developed by Techcoderr Developers
*/

//end of line.
?>
<div class="panel">
	<div class="panel-body">
		
		<div class="row">
			<div class="col-sm-6">
				<div class="form-group">
					<label class="control-label" for="bank"><?=lang('account_bank_label') ?>
						<span class="text-danger">*</span></label>
					<?=form_dropdown($bank)?>
				</div>
			</div>
		</div>
		
		<div class="row">
			<div class="col-sm-8">
				<div class="form-group form-group-lg">
					<label class="control-label" for="name"><?=lang('name') ?></label>
					<?=form_input($name) ?>
				</div>
			</div>
			<div class="col-sm-4">
				<div class="form-group">
					<label class="control-label" for="date"><?=lang('date') ?></label>
					<?=form_input($date) ?>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-sm-8">
				<div class="form-group">
					<label class="control-label" for="account"><?=lang('payment_account_label') ?>
						<span class="text-danger">*</span> (
						<a href="<?=site_url('accounts/new')?>"><?=lang('account_new_title') ?></a>)</label>
					<?=form_dropdown($account) ?>
				</div>
			</div>
			<div class="col-sm-4">
				<div class="form-group">
					<label class="control-label" for="amount"><?=lang('sales_amount_label') ?>
						<span class="text-danger">*</span></label>
					<?=form_input($amount) ?>
				</div>
			</div>
		</div>

		<div class="form-group form-group-lg">
			<label class="control-label" for="description"><?=lang('expenses_description_label') ?>
				<span class="text-danger">*</span></label>
			<?=form_input($description)?>
		</div>
		
		<button type="submit" class="btn btn-info btn-sm" name="submit" value="save">
			<span class="fa fa-check"></span>
			<span class="hidden-xs hidden-sm"><?=lang('btn_save') ?></span></button>

		<button type="submit" class="btn btn-info btn-sm"  name="submit" value="saveandclose">
			<span class="fa fa-save"></span>
			<span class="hidden-xs hidden-sm"><?=lang('btn_save_and_close') ?></span></button>

		<a href="<?=site_url(previous_url('expenses',true)) ?>" class="btn btn-default btn-sm">
			<span class="fa fa-close"></span>
			<span class="hidden-xs hidden-sm"><?=lang('btn_cancle') ?></span></a>
	</div>
</div>
