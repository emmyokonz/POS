<?php
defined('BASEPATH')or exit('No direct script allowed!');
/**
* Developed by Techcoderr Developers
*/

//end of line.
?>
<div class="row">
	<div class="col-sm-12">
		<div class="row">
			<div class="col-sm-8 col-md-9">
				<div class="form-group">
					<label class="control-label" for="name"><?=lang('name') ?>
						<span class="text-danger">*</span></label>
					<?=form_input($name) ?>
				</div>
				<div class="form-group">
					<label class="control-label" for="account"><?=lang('payment_bank_label') ?>
						<span class="text-danger">*</span></label>
					<?php echo form_dropdown($account) ?>
					<i class="help-block text-muted"><?=lang('payment_account_desc') ?></i>
				</div>
			</div>
			<div class="col-sm-4 col-md-3">
				
				<div class="form-group">
					<label class="control-label" for="t_date"><?=lang('date') ?></label>
					<?=form_input($date) ?>
				</div>

				<div class="form-group">
					<label class="control-label" for="amount"><?=lang('sales_amount_label') ?>
						<span class="text-danger">*</span></label>
					<?=form_input($amount) ?>
				</div>
			</div>
			<div class="col-sm-12">
				<div class="form-group">
					<label class="control-label" for="amount"><?=lang('payment_description_label') ?></label>
					<?=form_textarea($description)?>
				</div>
			</div>
			
		</div>
	</div>
</div>