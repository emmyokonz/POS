<?php
defined('BASEPATH')or exit('No direct script allowed!');
/**
* Developed by Techcoderr Developers
*/

//end of line.
?>
<?=form_open(current_url(),['method'=>'post']) ?>
<h3 class="box-title clearfix">
	<?=$page_title ?>
	<div class="pull-right">
		<button type="submit" class="btn btn-info btn-sm" name="submit" value="save">
			<span class="fa fa-check"></span>
			<span class="hidden-xs hidden-sm"><?=lang('btn_save') ?></span></button>

		<button type="submit" class="btn btn-info btn-sm" value="saveandclose" name="submit">
			<span class="fa fa-save"></span>
			<span class="hidden-xs hidden-sm"><?=lang('btn_save_and_close') ?></span></button>

		<a href="<?=site_url(previous_url('payments',true)) ?>" class="btn btn-default btn-sm">
			<span class="fa fa-close"></span>
			<span class="hidden-xs hidden-sm"><?=lang('btn_cancle') ?></span></a>
	</div>
</h3>
<hr />
<?php echo $payment?>
<button type="submit" class="btn btn-info btn-sm" name="submit" value="save">
	<span class="fa fa-check"></span>
	<span class="hidden-xs hidden-sm"><?=lang('btn_save') ?></span></button>

<button type="submit" class="btn btn-info btn-sm" value="saveandclose" name="submit">
	<span class="fa fa-save"></span>
	<span class="hidden-xs hidden-sm"><?=lang('btn_save_and_close') ?></span></button>

<a href="<?=site_url(previous_url('payments',true)) ?>" class="btn btn-default btn-sm">
	<span class="fa fa-close"></span>
	<span class="hidden-xs hidden-sm"><?=lang('btn_cancle') ?></span></a>
<?=form_close() ?>