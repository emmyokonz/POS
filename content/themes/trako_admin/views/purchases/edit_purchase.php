<?php
defined('BASEPATH')or exit('No direct script allowed!');
/**
* Developed by Techcoderr Developers
*/

//end of line.
?>
<h3 class="box-title clearfix">
	<?=$page_title ?>
	<div class="pull-right">
		<a href="<?=site_url('purchases/save') ?>" class="btn btn-info btn-sm edit-cart savenew">
			<span class="fa fa-check"></span>
			<span class="hidden-xs hidden-sm"><?=lang('btn_save') ?></span></a>

		<a href="<?=site_url('purchases/save') ?>" type="submit" class="btn btn-info btn-sm edit-cart saveclose">
			<span class="fa fa-save"></span>
			<span class="hidden-xs hidden-sm"><?=lang('btn_save_and_close') ?></span></a>

		<a href="<?=site_url('preview/invoice') ?>" class="btn btn-warning btn-sm">
			<span class="fa fa-eye"></span>
			<span class="hidden-xs hidden-sm"><?=lang('btn_preview') ?></span></a>

		<a href="<?=site_url(previous_url('purchases',true)) ?>" class="cancle btn btn-default btn-sm">
			<span class="fa fa-close"></span>
			<span class="hidden-xs hidden-sm"><?=lang('btn_cancle') ?></span></a>
	</div>
</h3>
<hr />
<?=$cart ?>