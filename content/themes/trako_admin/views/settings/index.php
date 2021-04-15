<?php

?>
<ul class="nav nav-tabs m-b-20 text-capitalize" role="tablist">
	<?php foreach ($tabs as $tab): ?>
	<li role="presentations" class="<?php
	if ($tab['tab']==$active_tab) {
		echo 'active';} ?>">
		<a href="<?=site_url('settings/'.$tab['tab']) ?>" aria-controls="home" role="tab" aria-expanded="true">
			<span> <?=$tab['tab'] ?></span>
		</a>
	</li>
	<?php endforeach ?>
	<li role="presentations" class="<?php
		if (uri_segment(2)=='activity_log') {
		echo 'active';} ?>">
		<a href="<?=site_url('settings/activity_log') ?>" aria-controls="home" role="tab" aria-expanded="true">
			<span> activity Log</span>
		</a>
	</li>
	
</ul>
<h3 class="box-title clearfix">
	<?=ucwords($active_tab) ?>
	<div class="pull-right">

		<?php
		if (has_action("update"))
			: ?>
		<a href="<?=previous_url(NULL,true) ?> " class="btn btn-sm btn-default" >
			<i class="fa fa-reply p-r-5"></i> Cancle</a>
		<button class="btn btn-primary m-0 mb-3 btn-sm" type="submit">
			<i class="fa fa-save p-r-5"></i> Update Settings</button>
		<?php endif ?>

	</div>
</h3>

<div class="clearfix"></div>

<?php if ($settings): ?>
<?php echo form_open(current_url()) ?>
<div class="row">
	<div class="col-md-12">
	<?php foreach ($settings as $setting): ?>
		<div class="form-group">
			<label for="<?=humanize($setting->name) ?>"><?=humanize($setting->name) ?></label>
			<?php echo build_form($setting->type,$setting->name,$setting->value,$setting->options,$setting->dynamic); ?>
			<span class="help-block"><?=($setting->description) ?></span>
			
		</div>
	<?php endforeach ?>
	</div>

	<?php if (has_action("update")): ?>
	<div class="col-sm-12">
		<a href="<?=previous_url(NULL,true) ?>" class="btn btn-default" >
			<i class="fa fa-reply p-r-5"></i> Cancle</a>
		<button class="btn btn-primary" type="submit">
			<i class="fa fa-save p-r-5"></i> Update Settings</button>
	</div>
	<?php endif ?>
</div>
<?php endif ?>
<?php form_close(); ?>
