<?php

?>
<?php echo form_open(ADMIN."forgot_password",['class'=>"form-horizontal new-lg-form",'id'=>"loginform" ]);?>
	<div class="form-group ">
		<div class="col-xs-12">
			<h3>
				Recover Password
			</h3>
			<p class="text-muted">
				Enter your Email and instructions will be sent to you!
			</p>
		</div>
	</div>
	
	<div class="row"><?php echo $template['partials']['flashdata']?></div>

	<div class="form-group ">
		<div class="col-xs-12">
			<label for="identity"><?php echo (($type=='email') ? sprintf(lang('forgot_password_email_label'), $identity_label) : sprintf(lang('forgot_password_identity_label'), $identity_label));?></label> <br />
			<?php echo form_input($identity,'',['class'=>"form-control",'required'=>"","placeholder"=>"Email"]);?>
		</div>
	</div>
	<div class="form-group text-center m-t-20">
		<div class="col-xs-12">
			<button class="btn btn-info btn-block text-uppercase waves-effect waves-light" type="submit">
				<?php echo lang('forgot_password_submit_btn');?>
			</button>
		</div>
	</div>
<?php echo form_close();?>