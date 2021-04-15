<?php defined('BASEPATH') OR exit('No direct access to script allowed.');?>
<h3 class="box-title">
	All Users
	<div class="pull-right">
		<a href="<?=site_url(ADMIN.'users/add_user')?>" class="btn btn-sm btn-info btn-outline"><span class="fa fa-plus" title="Add new user"></span>  <span class="hidden-xs">Add new user</span></a>
	</div>
</h3>
<p class="text-muted m-b-30">
	List of all registered users those in red are the INACTIVE users.
</p>
<div class="table-responsive">
	<table class="table table-condensed table-stripe">
		<thead>
			<tr>
				<th>Id</th>
				<th>Name</th>
				<th>Email</th>
				<th>User Group</th>
				<th>Last Seen</th>
				<th></th>
			</tr>
		</thead>
		<tbody>
		<?php if(!empty($users)):?>
		<?php $n=''; foreach ($users as $user):?>
			<tr <?=(!$user->active)?'class="danger"':''?>>
				<td><?php echo htmlspecialchars($user->id,ENT_QUOTES,'UTF-8');?></td>
				<td><?php echo htmlspecialchars($user->first_name,ENT_QUOTES,'UTF-8') .' '. htmlspecialchars($user->last_name,ENT_QUOTES,'UTF-8');?></td>
	            <td><?php echo htmlspecialchars($user->email,ENT_QUOTES,'UTF-8');?></td>
	            
				<td>
					<?php foreach ($user->groups as $group):?>
						<?php echo anchor(ADMIN."users/edit_group/".$group->id, htmlspecialchars($group->name,ENT_QUOTES,'UTF-8')) ;?><br />
	                <?php endforeach?>
				</td>
				<td><?php echo htmlspecialchars(date('d-m-Y',$user->last_login),ENT_QUOTES,'UTF-8');?>  </td>
				<td align="right">
					<a class="btn btn-sm btn-info" href="<?php echo current_url().'/edit_user/'.$user->id?>"><span class="hidden-xs">Edit</span><i title="Edit" class="fa fa-edit visible-xs"></i></a>
					<?php if($user->active):?>
					<a class="btn btn-sm btn-danger" href="<?php echo current_url().'/deactivate/'.$user->id?>"><span class="hidden-xs">Deactivate</span><i title="Deactivate" class="fa fa-user-times visible-xs"></i></a>
					<?php else:?>
					<a class="btn btn-sm btn-success" href="<?php echo current_url().'/activate/'.$user->id?>"><span class="hidden-xs">Activate</span><i title="Activate" class="fa fa-user visible-xs"></i></a>
					<?php endif?>
				</td>
			</tr>
		<?php endforeach;?>
		<?php else:?>
		<tr>
			<td colspan="6" align="center"><b>No record found</b></td>
		</tr>
		<?php endif?>
		</tbody>
	</table>
</div>