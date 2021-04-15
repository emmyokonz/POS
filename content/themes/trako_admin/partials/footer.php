<footer class="footer"> 
	<div class="row">
		<div class="col-md-9 text-left">
		<span class="">	
			<?=date('Y') ?> &copy; <?php print config_item('app_name'); ?> powered by
			<a href="<?php print config_item('app_developer_address'); ?>"><?php echo config_item('app_developer') ?></a> 
		</span>
	</div>
		<div class="col-md-3 text-right">
			<span class=" text-muted">Version: <?=config_item('app_version') ?></span></div></div>
</footer>