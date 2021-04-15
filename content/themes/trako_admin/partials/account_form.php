<div class="row">
	<div class="col-sm-6">
		<div class="form-group">
			<label class="control-label" for="account_type"><?=lang('account_type_label') ?></label>
			<?php
			if (isset($account_type_edit)) {
				echo form_input($account_type_edit); } 
 			else {
				echo form_dropdown($account_type);
			} ?>
		</div>
	</div>
	<div class="col-sm-6">
		<div class="form-group">
			<label class="control-label" for="name"><?=lang('account_name_label') ?>
				<span class="text-danger"></span>
			</label>
			<?=form_input($name)?>
		</div>
	</div>
	<div class="col-sm-6">
		<div class="form-group">
			<label class="control-label" for="description"><?=lang('account_description_label') ?></label>
			<?=form_input($description) ?>
		</div>
	</div>
	<div class="col-sm-6">
		<div class="form-group-sm form-group ">
			<label class="control-label" for="opening_balance"><?=$opening_balance_label?></label>
			<?=form_input($opening_balance) ?>
		</div>
	</div>
</div>