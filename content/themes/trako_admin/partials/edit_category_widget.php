<?php
defined('BASEPATH')or exit('No direct script allowed!');
/**
* Developed by Techcoderr Developers
*/

//end of line.
?>
<div class="panel">
	<div class="panel-heading">
		<?=lang("product_edit_category_title") ?>
		<div class="pull-right">
			<a href="<?=site_url(ADMIN.'products/new_category') ?>" class="btn btn-danger btn-xs">
				<span class="fa fa-plus"></span>
				<span class="hidden-xs hidden-sm"><?=lang('btn_new_category') ?></span></a>
		</div>
	</div>
	
	<div class="panel-body">
		<div class="form-group form-group-lg">
			<label class="control-label" for="sender"><?=lang('product_category_name_label') ?>
				<span class="text-danger">*</span></label>
			<div class="col-s"><input type="text" name="sender" id="sender" placeholder="<?=lang('product_category_name_label') ?>" class="form-control"></div>
		</div>

		<div class="form-group">
			<label class="control-label" for="sender"><?=lang('product_category_description_label') ?>
				<span class="text-danger">*</span></label>
			<div class="col-s"><input type="text" name="sender" id="sender" placeholder="<?=lang('product_category_description_label') ?>" class="form-control"></div>
		</div>

		<button type="submit" class="btn btn-info btn-sm">
			<span class="fa fa-check"></span>
			<span class="hidden-xs hidden-sm"><?=lang('btn_save') ?></span></button>

		<button type="submit" class="btn btn-info btn-sm">
			<span class="fa fa-save"></span>
			<span class="hidden-xs hidden-sm"><?=lang('btn_save_and_close') ?></span></button>

		<a href="<?=site_url(previous_url('dashboard',true)) ?>" class="btn btn-default btn-sm">
			<span class="fa fa-close"></span>
			<span class="hidden-xs hidden-sm"><?=lang('btn_cancle') ?></span></a>
	</div>
</div>
