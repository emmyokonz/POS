
<?php if($this->session->flashdata()):?>

<?php if($this->session->flashdata('success')): ?>
<div class="alert alert-success alert-dismissable" role="alert">
	<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
	<b><i class="fa fa-check-circle"></i> success: </b><?php echo $this->session->flashdata('success') ?>
</div>
<?php endif ?>

<?php if($this->session->flashdata('error')): ?>
<div class="alert alert-danger alert-dismissable" role="alert">
	<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
	<b><i class="fa fa-exclamation-circle"></i> ERROR: </b><?php echo $this->session->flashdata('error') ?>
</div>
<?php endif ?>

<?php if($this->session->flashdata('info')): ?>
<div class="alert alert-info alert-dismissable" role="alert">
	<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
	<b><i class="fa fa-check-triangle"></i> Info: </b><?php echo $this->session->flashdata('info') ?>
</div>
<?php endif ?>

<?php if($this->session->flashdata('warning')): ?>
<div class="alert alert-warning alert-dismissable" role="alert">
	<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
	<b><i class="fa fa-check-circle"></i> Warning: </b><?php echo $this->session->flashdata('warning') ?>
</div>
<?php endif ?>

<?php endif; ?>