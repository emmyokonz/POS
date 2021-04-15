<?php

?>
<div class="row">
<div class="col-sm-12">
	<div class="white-box">
		<h3 class="box-title">
			<?=page_title('Manage Dashboard widgets')?>
		</h3>
		<p class="text-muted m-b-30">Choose the widgets you wish to display on your dashboard.</p>
		<div class="table-responsive">
			<table class="table table-hover table-info manage-u-table">
				<thead>
					<tr>
						<th>Name</th>
						<th>Description</th>
						<th>Position</th>
						<th>Permission</th>
						<th></th>
					</tr>
				</thead>
				<tbody>
					<tr>
					<?php if(count($widgets) > 0):?>
					<?php foreach($widgets as $widget):?>
						<td> <?=ucwords(humanize($widget->name))?></td>
						<td><?=$widget->description?></td>
						<td><?=$widget->position?></td>
						<td><?=get_permission($widget->permission)->name?></td>
						<td align="right">
							<?php if($widget->active):?>
							<a class="btn btn-sm btn-danger" href="<?php echo current_url().'/deactivate/?action='.$widget->id.'&token='.$token?>"><span class="hidden-xs">Deactivate</span><i title="Deactivate" class="fa fa-user-times visible-xs"></i></a>
							<?php else:?>
							<a class="btn btn-sm btn-success" href="<?php echo current_url().'/activate/?action='.$widget->id.'&token='.$token?>"><span class="hidden-xs">Activate</span><i title="Activate" class="fa fa-user visible-xs"></i></a>
							<?php endif?>
						</td>
					<?php endforeach?>
					<?php else:?>
					<tr><td colspan="5" align="center">No widget(s) found.</td></tr>
					<?php endif?>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
</div>
</div>
