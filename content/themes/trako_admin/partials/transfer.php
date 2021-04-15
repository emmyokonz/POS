<?php
defined('BASEPATH')or exit('No direct script allowed!');
/**
* Developed by Techcoderr Developers
*/

//end of line.
?>
<div class="row">
	<div class="col-sm-12">
		<div class="form-group">
			<label class="control-label" for="bank_id"><?=lang('payment_transfer_from') ?>
				<span class="text-danger">*</span></label>
			<?=form_dropdown($bank)?>
		</div>
		<div class="row">
			<div class="col-sm-6">
				<div class="form-group">
					<label class="control-label" for="amount"><?=lang('sales_amount_label') ?>
						<span class="text-danger">*</span></label>
					<?=form_input($amount) ?>
				</div>
				<div class="form-group">
					<label class="control-label" for="to_account"><?=lang('payment_transfer_to') ?>
						<span class="text-danger">*</span></label>
					<?=form_dropdown($account) ?>
				</div>
			</div>
			<div class="col-sm-6">

				<div class="form-group">
					<label class="control-label" for="date"><?=lang('date') ?></label>
					<?=form_input($date) ?>
				</div>
			</div>
		</div>
	</div>
</div>