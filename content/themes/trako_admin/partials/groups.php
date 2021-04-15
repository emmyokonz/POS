<?php
defined('BASEPATH')or exit('No direct script allowed!');
/**
* Developed by Techcoderr Developers
*/

//end of line.
?>

<div class="modal fade" id="groups" tabindex="-1" role="dialog" aria-labelledby="groupsLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header box-title">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<h5 class="modal-title" id="agentLabel"><?=lang('contact_add_group') ?></h5>
			</div>
			<form action="<?=site_url('contacts/add_group_ajax') ?>" method="post" id="ajax_form">
				<div class="modal-body">
					<div class="form-group">
						<label for="name" class="control-label"><?=lang('contact_group_name_label')?></label>
						<input class="form-control" name="name" id="name" placeholder="<?=lang('contact_group_name_label') ?>"/>
					</div>
					<div class="form-group">
						<label for="name" class="control-label"><?=lang('contact_group_description_label')?></label>
						<textarea class="form-control" name="name" id="name" placeholder="<?=lang('contact_group_description_label') ?>"></textarea>
					</div>
				</div>
				<div class="modal-footer">
					<button type="submit" value="add" class="btn btn-primary" id="action"><?=lang('btn_add') ?> </button>
					<button type="submit" value="addClose" class="btn btn-primary" id="action"><?=lang('btn_add_and_close') ?> </button>
					<button type="button" class="btn btn-default" data-dismiss="modal"><?=lang('btn_cancle') ?></button>
				</div>
			</form>
		</div>
	</div>
</div>