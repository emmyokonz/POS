<?php

?><?php
defined('BASEPATH')or exit('No direct script allowed!');
/**
* Developed by Techcoderr Developers
*/

//end of line.
?>

<h3 class="box-title clearfix">
	<?=$page_title ?>
	<small class="label label-info"><?//=$count_records. ' '. lang('sales') ?></small>
	<div class="pull-right">

		<a href="<?=site_url(ADMIN.'accounts') ?>" class="btn btn-danger btn-sm">
			<span class="fa fa-print"></span>
			<span class="hidden-xs hidden-sm"><?=lang('btn_print') ?></span></a>
		<a href="<?=site_url(ADMIN.'accounts') ?>" class="btn btn-danger btn-sm">
			<span class="fa fa-file-pdf-o"></span>
			<span class="hidden-xs hidden-sm"><?=lang('btn_print') ?> as pdf</span></a>
		<a href="<?=site_url(ADMIN.'accounts') ?>" class="btn btn-default btn-sm">
			<span class="fa fa-angle-left"></span>
			<span class="hidden-xs hidden-sm"><?=lang('btn_back') ?></span></a>
	</div>
</h3>
<div  class="panel">
	<div  class="panel-body-table">
		<div class="table-responsive">
			<?=$view; ?>
		</div>
	</div>
	<div class="panel-footer p-10">

		<div class="text-right">
			<?//=$pagination ?>
		</div>
	</div>
</div>
