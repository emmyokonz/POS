<?php defined('BASEPATH') OR exit('No direct access to script allowed.');?>
<h3 class="box-title">
	All groups
	<div class="pull-right">
		<a href="<?=site_url(ADMIN.'users/add_group')?>" class="btn btn-sm btn-info btn-outline"><span class="fa fa-plus" title="Add new group"></span>  <span class="hidden-xs">Add new group</span></a>
	</div>
</h3>
<p class="text-muted m-b-30">
	List of all registered groups those in red are the INACTIVE groups.
</p>
<div class="table-responsive">
	<table class="table table-condensed table-stripe">
		<thead>
			<tr>
				<th>Id</th>
				<th>Name</th>
				<th>Description</th>
				<th>Users</th>
				<th>Permissions</th>
				<th></th>
			</tr>
		</thead>
		<tbody>
		<?php $n=''; foreach ($groups as $group):?>
			<tr>
				<td><?php echo htmlspecialchars($group->id,ENT_QUOTES,'UTF-8');?></td>
				<td><?php echo htmlspecialchars(strtolower($group->name),ENT_QUOTES,'UTF-8');?></td>
	            <td><?php echo htmlspecialchars(ucwords($group->description),ENT_QUOTES,'UTF-8');?></td>
				<td><?php echo htmlspecialchars(($group->users),ENT_QUOTES,'UTF-8');?>  users in group</td>
				<td><?php echo htmlspecialchars(($group->permissions),ENT_QUOTES,'UTF-8');?> permissions assigned </td>
	            
				<!--<td>
					<?php foreach ($group->permissions as $perm):?>
						<div class="btn btn-sm btn-primary"><?php echo (htmlspecialchars($perm->description,ENT_QUOTES,'UTF-8')) ;?></div>
	                <?php endforeach?>
				</td>-->
				<td align="right">
					<a class="btn btn-sm btn-info" href="<?php echo admin_url('users/edit_group/'.$group->id)?>"><span class="hidden-xs">Edit</span><i title="Edit" class="fa fa-edit visible-xs"></i></a>
					
				</td>
			</tr>
		<?php endforeach;?>
		</tbody>
	</table>
</div>