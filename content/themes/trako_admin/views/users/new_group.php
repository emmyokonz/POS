<?php defined('BASEPATH') OR exit('No direct access to script allowed.');?>
<h3 class="box-title">
	New group Creation
</h3>
<p class="text-muted m-b-30">
	Create a new group.
</p>
<div class="row">
	<?php echo form_open(current_url(),['class'=>"form-horizontal" ]);?>
	<div class="col-md-7 clo-sm-6">
		<?php if (isset($group_name)):?>
		<div class="form-group  m-t-20">
			<div class="col-xs-12">
				<label for="group_name">
					Group Name
				</label>
				<?php echo form_input($group_name);?>
			</div>
		</div>
		<?php endif?>
		
		<?php if (isset($description)):?>
		<div class="form-group  m-t-20">
			<div class="col-xs-12">
				<label for="description">
					Group Description
				</label>
				<?php echo form_input($description);?>
			</div>
		</div>
		<?php endif?>

	</div>
	<div class="col-xs-12">
		<a class="btn btn-sm btn-default" href="<?=site_url(ADMIN.'users/groups')?>"><span class="fa fa-reply fa-fw"></span><span class="hidden-xs">Cancle</span></a>
		<button class="btn btn-sm btn-info"><span class="fa fa-save fa-fw"></span><span class="hidden-xs">Add group</span></button>
	</div>
	<?php echo form_close()?>
</div>
