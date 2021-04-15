<div class="panel panel-themecolor">
	<div class="panel-heading">
		Users List
	</div>
	<div class="table-responsive">
		<table class="table table-hover table-info manage-u-table">
			<thead>
				<tr>
					<th>Name</th>
					<th>Role</th>
					<th>Email Address</th>
					<th>IP Address</th>
					<th>Last Activity</th>
				</tr>
			</thead>
			<tbody>
				
				<?php if(!empty($users_list)):?>
				<?php $n=''; foreach ($users_list as $user):?>
					<tr <?=(!$user->active)?'class="danger"':''?>>
						<td><?php echo htmlspecialchars($user->first_name.' '.$user->last_name,ENT_QUOTES,'UTF-8');?></td>
						<td>
							<?php foreach ($user->groups as $group):?>
								<?php echo htmlspecialchars($group->name,ENT_QUOTES,'UTF-8') ;?><br />
			                <?php endforeach?>
						</td>
			            <td><?php echo htmlspecialchars($user->email,ENT_QUOTES,'UTF-8');?></td>
			            <td><?php echo htmlspecialchars($user->ip_address,ENT_QUOTES,'UTF-8');?></td>
						<td><?php echo ($user->last_login !== NULL)?htmlspecialchars(date('d-m-Y h:i a',$user->last_login),ENT_QUOTES,'UTF-8'):'<i>blank</i>';?>  </td>
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
</div>