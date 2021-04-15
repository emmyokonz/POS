<?php
defined('BASEPATH')or exit('No direct script allowed!');
/**
* Developed by Techcoderr Developers
*/

//end of line.
?>
<?=form_open(current_url(),[])?>
<h3 class="box-title clearfix">
	<?=$page_title ?>
	<div class="pull-right">
		<button type="submit" class="btn btn-info btn-sm">
			<span class="fa fa-check"></span>
			<span class="hidden-xs hidden-sm"><?=lang('btn_save') ?></span></button>
			
		<button type="submit" class="btn btn-info btn-sm">
			<span class="fa fa-save"></span>
			<span class="hidden-xs hidden-sm"><?=lang('btn_save_and_close') ?></span></button>
		
		<a href="<?=site_url(previous_url('dashboard',true))?>" class="btn btn-default btn-sm">
			<span class="fa fa-close"></span>
			<span class="hidden-xs hidden-sm"><?=lang('btn_cancle') ?></span></a>
	</div>
</h3>
<hr />
<div class="row">
	<div class="col-sm-12">
		<div class="form-group form-group-lg">
			<label class="control-label" for="sender"><?=lang('product_name_label')?>
				<span class="text-danger">*</span></label>
			<div class="col-s"><?=form_input($product_name)?></div>
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
<?=form_close()?>
<?=$new_category ?>