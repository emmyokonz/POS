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
	<div class="col-md-5 clo-sm-6">
		<?php if (isset($permissions)): ?>
		<div class="row">
			<div class="col-lg-12">
				<div class="panel panel-default" style="border:1px solid rgba(120,130,140,.13);">
					<div class="panel-heading">
						<i class="fa fa-users fa-fw"></i>
						Group Permissions
					</div>
					<div class="panel-wrapper collapse in">
						<div class="panel-body">
							<?php
							foreach ($permissions as $perm):
							?>
							<p class="checkbox checkbox-info pull-left" style="padding-right: 20px;">
								<input id="<?=$perm['name']?>" type="checkbox" name="permissions[]" value="<?php echo $perm['pid'];?>"<?php echo $perm['checked']; echo $perm['disabled']?>>
								<label for="<?=$perm['name']?>">
									<?php echo htmlspecialchars(ucwords($perm['name']),ENT_QUOTES,'UTF-8');?>
								</label>
							</p>
							<?php endforeach?>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php endif ?>
	</div>
	
	<div class="col-xs-12">
		<a class="btn btn-sm btn-default" href="<?=site_url(ADMIN.'users/groups')?>"><span class="fa fa-reply fa-fw"></span><span class="hidden-xs">Cancle</span></a>
		<button class="btn btn-sm btn-info"><span class="fa fa-save fa-fw"></span><span class="hidden-xs">Update group</span></button>
	</div>
	<?php echo form_close()?>
</div>
