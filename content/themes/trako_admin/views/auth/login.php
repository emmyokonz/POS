<?php

?>
<h3 class="box-title m-b-0">
	Sign In to Admin
</h3>
<?php echo form_open(ADMIN."login",['class'=>"form-horizontal new-lg-form",'id'=>"loginform" ]);?>

<div class="row"><?php echo $template['partials']['flashdata']?></div>

<div class="form-group  m-t-20">
	<div class="col-xs-12">
		<?php echo lang('login_identity_label', 'identity');?>
		<?php echo form_input($identity,$identity,['class'=>"form-control",'required'=>"","placeholder"=>"Username"]);?>

	</div>
</div>
<div class="form-group">
	<div class="col-xs-12">
		<?php echo lang('login_password_label', 'password');?>
		<?php echo form_input($password,'',['class'=>"form-control",'required'=>"","placeholder"=>"Password"]);?>
	</div>
</div>
<div class="form-group">
	<div class="col-md-12">
		<?php if($allow_remember):?>
		<div class="checkbox checkbox-info pull-left p-t-0">
			<?php echo form_checkbox('remember', '1', FALSE, 'id="remember"');?>
			<?php echo lang('login_remember_label', 'remember');?>
		</div>
		<?php endif?>
		<a href="<?=site_url(ADMIN.'forgot_password')?>" class="text-dark pull-right">
			<i class="fa fa-lock m-r-5">
			</i><?php echo lang('login_forgot_password');?>
		</a>
	</div>
</div>
<div class="form-group m-t-20">
	<div class="col-xs-12">
		<button class="btn btn-info btn-lg btn-block text-uppercase waves-effect waves-light" type="submit">
			Log In
		</button>
	</div>
</div>
<?php echo form_close();?>