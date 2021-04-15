<?php
defined('BASEPATH')or exit('No direct script allowed!');
/**
* Developed by Techcoderr Developers
*/

//end of line.
?>
<?=form_open(current_url(),['type'=>'post']) ?>
<h3 class="box-title clearfix">
	<?=$page_title ?>
	<div class="pull-right">
		<button type="submit" name="submit" class="btn btn-info btn-sm" value="save">
			<span class="fa fa-check"></span>
			<span class="hidden-xs hidden-sm"><?=lang('btn_save') ?></span></button>

		<button type="submit" name="submit" class="btn btn-info btn-sm" value="saveandclose">
			<span class="fa fa-save"></span>
			<span class="hidden-xs hidden-sm"><?=lang('btn_save_and_close') ?></span></button>

		<a href="<?=site_url(previous_url('accounts',true)) ?>" class="btn btn-default btn-sm">
			<span class="fa fa-close"></span>
			<span class="hidden-xs hidden-sm"><?=lang('btn_cancle') ?></span></a>
	</div>
</h3>
<hr />

<?=$account_form ?>

<div class="pull-left">
	<button type="submit" name="submit" class="btn btn-info btn-sm" value="save">
		<span class="fa fa-check"></span>
		<span class="hidden-xs hidden-sm"><?=lang('btn_save') ?></span></button>

	<button type="submit" name="submit" class="btn btn-info btn-sm" value="saveandclose">
		<span class="fa fa-save"></span>
		<span class="hidden-xs hidden-sm"><?=lang('btn_save_and_close') ?></span></button>

	<a href="<?=site_url(previous_url('accounts',true)) ?>" class="btn btn-default btn-sm">
		<span class="fa fa-close"></span>
		<span class="hidden-xs hidden-sm"><?=lang('btn_cancle') ?></span></a>
</div>
<div class="row"></div>
<?=form_close() ?>