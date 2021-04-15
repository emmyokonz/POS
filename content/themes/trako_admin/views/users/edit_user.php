<?php defined('BASEPATH') OR exit('No direct access to script allowed.');?>
<h3 class="box-title">
	Edit User
	<?php if(has_action('read')):?><div class="pull-right">
		<a href="<?=site_url(ADMIN.'users/add_user')?>" class="btn btn-outline btn-sm btn-info">
			<span class="fa fa-plus" title="Add new user">
			</span>
			<span class="hidden-xs">
				Add new user
			</span>
		</a>
		<?php endif?>
	</div>
</h3>
<p class="text-muted m-b-30">
	Edit a users details
</p>
<div class="row">
	<?php echo form_open(current_url(),['class'=>"form-horizontal" ]);?>
	<div class="col-md-7 clo-sm-6">
		<?php
		if (isset($first_name)):?>
		<div class="form-group  m-t-20">
			<div class="col-xs-12">
				<label for="first_name">
					First Name
				</label>
				<?php echo form_input($first_name);?>
			</div>
		</div>
		<?php endif?>
		<?php
		if (isset($last_name)):?>
		<div class="form-group  m-t-20">
			<div class="col-xs-12">
				<label for="last_name">
					Last Name
				</label>
				<?php echo form_input($last_name);?>
			</div>
		</div>
		<?php endif?>
		<?php
		if (isset($email)):?>
		<div class="form-group  m-t-20">
			<div class="col-xs-12">
				<label for="email">
					Email Address
				</label>
				<?php echo form_input($email);?>
			</div>
		</div>
		<?php endif?>
		<?php
		if (isset($phone)):?>
		<div class="form-group  m-t-20">
			<div class="col-xs-12">
				<label for="email">
					Phone Number
				</label>
				<?php echo form_input($phone);?>
			</div>
		</div>
		<?php endif?>
		<?php
		if (isset($password)):?>
		<div class="form-group  m-t-20">
			<div class="col-xs-12">
				<label for="password">
					Password
				</label>
				<?php echo form_input($password);?>
			</div>
		</div>
		<div class="form-group  m-t-20">
			<div class="col-xs-12">
				<label for="confirm_password">
					Confirm Password
				</label>
				<?php echo form_input($password_confirm);?>
			</div>
		</div>
		<?php endif?>
		

	</div>
	<?php if ($this->ion_auth->is_admin()): ?>
	<div class="col-md-5 col-sm-6 clearfix">
		<div class="row p-t-30 p-b-30">
		
			<?php if (!$user->active): ?>
			<div class="col-md-12"><a class="btn btn-block btn-success" href="<?=site_url(ADMIN.'users/activate/'.$user->id)?>"><span class="fa fa-key fa-fw"></span><span class="hidden-xs">Activate account now</span></a></div>
			<?php else: ?>
			<div class="col-md-12"><a class="btn btn-block btn-danger" href="<?=site_url(ADMIN.'users/deactivate/'.$user->id)?>"><span class="fa fa-lock fa-fw"></span><span class="hidden-xs">Deactivate account now</span></a></div>
			<?php endif ?>
		</div>

		<?php if (isset($groups)): ?>
		<div class="row">
			<div class="col-lg-12">
				<div class="panel panel-default" style="border:1px solid rgba(120,130,140,.13);">
					<div class="panel-heading">
						<i class="fa fa-users fa-fw">
						</i><?php echo lang('edit_user_groups_heading');?>
					</div>
					<div class="panel-wrapper collapse in">
						<div class="panel-body">
							<?php
							foreach ($groups as $group):
							?>
							<p class="radio radio-info pull-left p-r-20">
								<input id="<?=$group['name']?>" type="radio" name="groups[]" value="<?php echo $group['gID'];?>"<?php echo $group['checked'];?>>
								<label for="<?=$group['name']?>">
									<?php echo htmlspecialchars(ucwords($group['name']),ENT_QUOTES,'UTF-8');?>
								</label>
							</p>
							<?php endforeach?>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php endif ?>
		<div class="row">
			<?php if (isset($permissions)): ?>
			<div class="col-lg-12">
				<div class="panel panel-default" style="border:1px solid rgba(120,130,140,.13);">
					<div class="panel-heading">
						<i class="fa fa-lock fa-fw"></i>
						Permission actions
					</div>
					<div class="panel-wrapper collapse in table-responsive">
						<table class="table table-hover">
							<thead>
								<tr>
									<th class="text-left">Permissions</th>
									<th>Read</th>
									<th>Update</th>
									<th>Create</th>
									<th>Delete</th>
								</tr>
							</thead>
							<tbody>
							<?php foreach($permissions as $actions):?>
								<tr <?php if(isset($actions->readonly) && $actions->readonly):?>class="table-color danger"<?php endif?>>
									<td align="left"><?=$actions->name?></td>
									<td align="center"><div class="checkbox checkbox-success"><input <?php echo (isset($actions->readonly) && $actions->readonly)?'disabled=""':'';?> type="checkbox" name="permissions[<?php echo $actions->pid?>][read]" value="<?php echo $actions->read;?>" <?php echo ($actions->read)?'checked=checked':''; ?>><label></label></div></td>
									<td align="center"><div class="checkbox checkbox-success"><input <?php echo (isset($actions->readonly) && $actions->readonly)?'disabled=""':'';?> type="checkbox" name="permissions[<?php echo $actions->pid?>][update]" value="<?php echo $actions->update;?>" <?php echo ($actions->update)?'checked=checked':''; ?>><label></label></div></td>
									<td align="center"><div class="checkbox checkbox-success"><input <?php echo (isset($actions->readonly) && $actions->readonly)?'disabled=""':'';?> type="checkbox" name="permissions[<?php echo $actions->pid?>][create]" value="<?php echo $actions->create;?>" <?php echo ($actions->create)?'checked=checked':''; ?>><label></label></div></td>
									<td align="center"><div class="checkbox checkbox-success"><input <?php echo (isset($actions->readonly) && $actions->readonly)?'disabled=""':'';?> type="checkbox" name="permissions[<?php echo $actions->pid?>][delete]" value="<?php echo $actions->delete;?>" <?php echo ($actions->delete)?'checked=checked':''; ?>><label></label></div></td>
								</tr>
								<?php endforeach?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
			<?php endif ?>
		</div>
	</div>
	<?php endif?>
	<div class="col-xs-12">
		<a class="btn btn-sm btn-default" href="<?=site_url(ADMIN.'users')?>"><span class="fa fa-reply fa-fw"></span><span class="hidden-xs">Cancle</span></a>
		<button class="btn btn-sm btn-info"><span class="fa fa-save fa-fw"></span><span class="hidden-xs">Update User</span></button>
	</div>
	<?php echo form_close()?>
</div>
