<?php defined('BASEPATH') OR exit('No direct access to script allowed.');?>
<h3 class="box-title">
	New User Creation
</h3>
<p class="text-muted m-b-30">
	Create a new user.
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
	<div class="col-xs-12">
		<a class="btn btn-sm btn-default" href="<?=site_url(ADMIN.'users')?>"><span class="fa fa-reply fa-fw"></span><span class="hidden-xs">Cancle</span></a>
		<button class="btn btn-sm btn-info"><span class="fa fa-save fa-fw"></span><span class="hidden-xs">Add User</span></button>
	</div>
	<?php echo form_close()?>
</div>
