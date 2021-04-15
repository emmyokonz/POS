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

<?= form_open(current_url(),['method'=>'POST']) ?>
<h3 class="box-title clearfix">
	<?=lang('contact_edit') ?>
	<div class="pull-right">
		<button type="submit" class="btn btn-info btn-sm" name="compose" value="save">
			<span class="fa fa-check-circle"></span>
			<span class="hidden-xs hidden-sm"><?=lang('btn_save_and_new') ?></span></button>
		<button type="submit" class="btn btn-info btn-sm" name="compose" value="saveClose">
			<span class="fa fa-save"></span>
			<span class="hidden-xs hidden-sm"><?=lang('btn_save_and_close') ?></span></button>
		<button type="submit" class="btn btn-default btn-sm">
			<span class="fa fa-close"></span>
			<span class="hidden-xs hidden-sm"><?=lang('btn_cancle') ?></span></button>
	</div>
</h3>
<hr />
<div class="row">
	<div class="col-sm-12">
		<div class="form-group">
			<label class="control-label" for="sender"><?=lang('contact_name_label') ?></label>
			<div class="col-s"><input type="text" name="name" id="name" placeholder="<?=lang('contact_name_label') ?>" class="form-control"/></div>
			<i class="help-block text-muted"><?=lang('contact_name_desc') ?></i>
		</div>
		<div class="form-group">
			<label class="control-label" for="sender"><?=lang('contact_phone_label') ?>
				<span class="text-danger">*</span></label>
			<div class="col-s"><input type="text" name="name" id="name" placeholder="<?=lang('contact_phone_label') ?>" class="form-control"/></div>
			<i class="help-block text-muted"><?=lang('contact_phone_desc') ?></i>
		</div>

		<div class="form-group">
			<div class="col-s">
				<label class="control-label" for="recipient"><?=lang('contact_group_label') ?>
					<span class="text-danger">*</span>
				</label>
				<a href="javascript:void(0);" data-backdrop="static" class="btn btn-xs btn-danger" data-toggle="modal" data-target="#groups" data-agenttype="2" data-whatever="<?=lang('contact_add_group') ?>">New Group</a>
				<select class="form-control select" name="group" id="group">

				</select>
				<i class="help-block text-muted"><?=lang('contact_group_desc') ?></i>
			</div>

		</div>
	</div>
</div>
<div class="row">
	<div class="col-sm-12">
		<button type="submit" class="btn btn-info btn-sm" name="submit" value="save">
			<span class="fa fa-check-circle"></span>
			<span class="hidden-xs hidden-sm"><?=lang('btn_save_and_new') ?></span></button>
		<button type="submit" class="btn btn-info btn-sm" name="submit" value="saveandclose">
			<span class="fa fa-save"></span>
			<span class="hidden-xs hidden-sm"><?=lang('btn_save_and_close') ?></span></button>
		<button type="submit" class="btn btn-default btn-sm">
			<span class="fa fa-close"></span>
			<span class="hidden-xs hidden-sm"><?=lang('btn_cancle') ?></span></button>
	</div>
</div>

<?=form_close() ?>
<?=$group ?>