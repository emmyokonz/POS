<?php
defined('BASEPATH')or exit('No direct script allowed!');
/**
* Developed by Techcoderr Developers
*/

//end of line.
?>dsgh dsahgf ghdasf gh
<div class="row">
	<div class="col-sm-12">
		<div class="form-group form-group-lg">
			<label class="control-label" for="sender"><?=lang('product_name_label') ?>
				<span class="text-danger">*</span></label>
			<div class="col-s"><input type="text" name="sender" id="sender" placeholder="<?=lang('product_name_label') ?>" class="form-control"></div>
		</div>
		<div class="row">
			<div class="col-sm-9">
				<div class="form-group">
					<label class="control-label" for="sender"><?=lang('product_category_label') ?>
						<a href="<?=site_url('products/new_cat') ?>">(<?=lang('product_new_category_label') ?>)</a>
						<span class="text-danger">*</span></label>
					<div class="col-s"><input type="text" name="sender" id="sender" placeholder="<?=lang('product_category_label') ?>" class="form-control"></div>
					<i class="help-block text-muted"><?=lang('product_category_desc') ?></i>
				</div>
				<div class="form-group">
					<label class="control-label" for="sender"><?=lang('product_description_label') ?>
						<span class="text-danger">*</span></label>
					<div class="col-s">
						<textarea type="text" name="sender" id="sender" placeholder="<?=lang('product_description_label') ?>" class="form-control"></textarea></div>
					<i class="help-block text-muted"><?=lang('product_description_desc') ?></i>
				</div>
			</div>
			<div class="col-sm-3">
				<div class="form-group">
					<label class="control-label" for="sender"><?=lang('product_quantity_label') ?></label>
					<div class="col-s"><input readonly="" type="text" name="sender" id="sender" placeholder="<?=lang('product_quantity_label') ?>" class="form-control"></div>
				</div>
				<div class="form-group">
					<label class="control-label" for="sender"><?=lang('product_selling_label') ?>
						<span class="text-danger">*</span></label>
					<div class="col-s"><input type="text" name="sender" id="sender" placeholder="<?=lang('product_selling_label') ?>" class="form-control"></div>
				</div>
				<div class="form-group">
					<label class="control-label" for="sender"><?=lang('product_cost_label') ?>
						<span class="text-danger">*</span></label>
					<div class="col-s"><input type="text" name="sender" id="sender" placeholder="<?=lang('product_cost_label') ?>" class="form-control"></div>
				</div>
			</div>
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