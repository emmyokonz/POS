<?php

class Permissions
{

	public $tables = array();

	function __construct()
	{

		// initialize db tables data
		$this->tables = $this->config->item('tables');

	}


	/**
	* __call
	*
	* Acts as a simple way to call model methods without loads of stupid alias'
	*
	* @param string $method
	* @param array  $arguments
	*
	* @return mixed
	* @throws Exception
	*/
	public function __call($method, $arguments)
	{
		if (method_exists( $this->ion_auth_model, $method) )
		{
			return call_user_func_array( array($this->ion_auth_model,$method), $arguments);
		}
		if (method_exists( $this->ion_auth, $method) )
		{
			return call_user_func_array( array($this->ion_auth,$method), $arguments);
		}
		throw new Exception('Undefined method Permission::' . $method . '() called');
	}

	/**
	* __get
	*
	* Enables the use of CI super-global without having to define an extra variable.
	*
	* I can't remember where I first saw this, so thank you if you are the original author. -Militis
	*
	* @param    string $var
	*
	* @return    mixed
	*/
	public function __get($var)
	{
		return get_instance()->$var;
	}

	function _permissions()
	{
		return $this->db->select('pid as id,name,description')->where('protected' , 1)->get($this->tables['perms'])->result();

	}
	
	function get_permission($id = FALSE){
		
		$id || $id = $this->route->class;
		
		$permission = $this->db->where('pid',$id)->or_where('name',$id)->get($this->tables['perms']);
		if($permission->num_rows() !== NULL){
			
			return $permission->row();
			
		}
		
		return NULL;
	}

	public function add_to_permission( $group_id = FALSE ,$perms_ids = FALSE)
	{

		$data = array();
		//remove all the parmissions associated to the group.
		$this->remove_group_permissions($group_id);

		//if no permission id is present we assign dashboard.
		$perms_ids || $perms_ids = config_item('default_group_permission_id');

		//lets check if its an array then foreach through the array.
		if (!is_array($perms_ids)) {

			$perms_ids = array($perms_ids);

		}
		
		//if the default permission is not set we force
		if(!in_array(config_item('default_group_permission_id'),$perms_ids)){
			$data[] = ['gid' => $group_id,
						'pid' => config_item('default_group_permission_id'),];
		}
		foreach ($perms_ids as $perm_id) {
			
			$data[] = [
				'gid' => $group_id,
				'pid' => $perm_id,
			];
			
			/*$action_data[] = [
				'user_id' => $user_id,
				'pid' => $perm,
				'can_read' => 1,
				'can_create' => 0,
				'can_delete' => 0,
				'can_update' => 0,
			];*/

		}
		$this->db->trans_start();
		$insert = $this->db->insert_batch($this->tables['grps_perms'],$data,TRUE);
		//lets insert into users_actions
//		$insert = $this->db->insert_batch($this->tables['users_actions'],$action_data,TRUE);
		if($this->db->trans_status()==TRUE){
			$this->db->trans_commit();
			return TRUE;
		}else{
			$this->db->trans_rollback();
			return  false;
		}
	}

	///every user must belong to a single group.
	public function get_group_permissions($group_id = FALSE)
	{
		if($this->group($group_id)->row()->name === 'admin'){
			$permissions = $this->db->select($this->tables['perms'].'.pid as id , '.$this->tables['perms'].'.name , '.$this->tables['perms'].'.description , '.$this->tables['perms'].'.icon')
			->where('protected',1)
			->get($this->tables['perms']);
//		echo config_item('admin_group');exit;
		}else{
			$permissions = $this->db->select($this->tables['grps_perms'].'.pid as id , '.$this->tables['perms'].'.name , '.$this->tables['perms'].'.description , '.$this->tables['perms'].'.icon')
			->where($this->tables['grps_perms'].'.gid',$group_id)
			->join($this->tables['perms'],$this->tables['grps_perms'].'.pid='.$this->tables['perms'].'.pid')
			->get($this->tables['grps_perms']);
		}
		
		//set the default permission every group must have if non was assigned to it.
		if (!$permissions->num_rows()) {

			$permissions = $this->db->select('pid as id,name,icon,description')
			->where('name',config_item('default_group_permission_name'))
			->where('protected',1)
			->get($this->tables['perms']);

		}

		if (!$permissions->num_rows()) die('System setup not complet. Ensure that the permission list is installed in your db and "default_group_permission_name" is set in your config file.');
//echo "<pre>"; print_r($this->group(2)->result());exit;
		return $permissions;
	}
	
	///every user must belong to a single group.
	public function get_user_permissions($group_id = FALSE,$user_id = false)
	{
		if ($this->is_admin($user_id)) {
			$permissions = $this->db->select($this->tables['perms'].'.pid as id , '.$this->tables['perms'].'.name , '.$this->tables['perms'].'.description , '.$this->tables['perms'].'.icon')
			->where('protected',1)
			->get($this->tables['perms']);
		}
		else
		{
			
			$permissions = $this->db->select($this->tables['grps_perms'].'.pid as id , '.$this->tables['perms'].'.name , '.$this->tables['perms'].'.description , '.$this->tables['perms'].'.icon')
			->where($this->tables['grps_perms'].'.gid',$group_id)
			->where($this->tables['perms'].'.protected',1)
			->join($this->tables['perms'],$this->tables['grps_perms'].'.pid='.$this->tables['perms'].'.pid')
			->get($this->tables['grps_perms']);
		}

		//set the default permission every group must have if non was assigned to it.
		if (!$permissions->num_rows()) {

			$permissions = $this->db->select('pid as id,name,icon,description')
			->where('name',config_item('default_group_permission_name'))
			->where('protected',1)
			->get($this->tables['perms']);

		}

		if (!$permissions->num_rows()) die('System setup not complet. Ensure that the permission list is installed in your db and "default_group_permission_name" is set in your config file.');

		return $permissions;
	}

	public function get_users_permissions($groups = FALSE,$user_id = FALSE)
	{
		if(!$user_id)exit('Please provide the user arr: "get_users_permissions" ');
		
		if(!$groups) $groups = $this->get_users_groups($user_id)->result();
		
		$permissions = $this->_filter_permissions($groups,$user_id);
		
		return ($permissions);
	}
	
	private function _filter_group_id($groups){
		
		if(!is_array($groups)){
			
			$groups = array($groups);
		
		}
		
		foreach($groups as $group_id){
			
			$grp_ids[] = $group_id->id;
			
		}
		
		return $grp_ids;
	}

	public function has_permission($permission)
	{

		// if they're not logged in
		// bounce'm
		if (! $this->logged_in())
		{
			return false;
		}

		// if they are an admin, they can
		// do anything
		if ($this->is_admin())
		{
			return true;
		}

		//check if the permission is accessible to all
		if (in_array($permission , config_item('permissions_to_all'))) {

			return TRUE;
		}

		// the user can be in multiple groups, so we'll
		// check them all.  we return true on the first
		// one we find
		foreach ($this->get_users_groups()->result() as $group)
		{
			// logged in, but not admin
			if ($this->check_permission($permission, $group->id))
			{
				return true;
			}
		}
		// didn't find any, bounce'm
		return false;

	}

	/**
	* check permissions
	*
	*
	* @author Enliven Applications
	*
	* @return bool
	**/
	public function check_permission($perm, $group_id)
	{
		// first get the permmission info
		if ( ! ($perm_db = $this->db->where('name', $perm)->where('protected', 1)->limit(1)->get($this->tables['perms'])->row() ))
		{
			return false;
		}

		// now we have all the info we need to
		// decide if the group has permission
		// to do the thing...
		if ( $this->db->where('gid', $group_id)->where('pid', $perm_db->pid)->limit(1)->count_all_results($this->tables['grps_perms']) == 1 )
		{
			return true;
		}
		return false;
	}

	public function remove_group_permissions($group_id = FALSE)
	{

		if (!$group_id) return FALSE;

		//can not remove admin from any group.
		if ($group_id == 1) return FALSE;

		//every group must have a dashboard.
		$this->db->where('gid',$group_id)
//		->where('pid <>' , config_item('default_group_permission_id'))
		->delete($this->tables['grps_perms']);

		return $this->db->affected_rows() > 0;
	}

	public function has_action($action)
	{
		$permission = $this->router->class;
		// if they're not logged in
		// bounce'm
		if (! $this->logged_in())
		{
			return false;
		}

		// if they are an admin, they can
		// do anything
		if ($this->is_admin())
		{
			return true;
		}
		
		if(config_item('default_user_action') == $action){
			
			return true;
			
		}

		// logged in, but not admin
		if ($this->db->where(['pid'=>$permission,
					'user_id'=>$this->session->userdata('user_id'),
					'can_'.$action=>1
				])
			->get($this->tables['users_actions'])
			->num_rows() > 0)
		{
			return true;
		}

		// didn't find any, bounce'm
		return false;

	}

	public function add_user_actions($actions,$user_id=FALSE)
	{
		
		if(!$user_id) $user_id = $this->session->userdata('user_id');

		$data = array();

		$this->remove_user_actions($user_id);

		if (!is_array($actions)) {

			$actions = array($actions);
		}
		$user_group = $this->get_users_groups($user_id)->result();
		$user_permissions = $this->get_users_permissions($user_group,$user_id);

		foreach ($actions as $perm=>$action) {

			//let remove those permissions that are not assigned to the user group.
			foreach($user_permissions as $user_perms){
				if($user_perms->id == $perm ){
					$data[$user_perms->id] = [
						'user_id' => $user_id,
						'pid' => $user_perms->id,
						'can_read' => ((isset($action['read'])||(config_item('default_user_action')=='read'))?'1':'0'),
						'can_create' => ((isset($action['create'])||(config_item('default_user_action')=='create'))?'1':'0'),
						'can_delete' => ((isset($action['delete'])|(config_item('default_user_action')=='delete'))?'1':'0'),
						'can_update' => ((isset($action['update'])||(config_item('default_user_action')=='update'))?'1':'0'),
					];
				}
			}

		}
		
//		echo "<pre>";print_r($data);exit;
		if ($data)
		{
			return $this->db->insert_batch($this->tables['users_actions'] , $data , TRUE) > 0;
		}

		return FALSE;
	}

	function remove_user_actions($user_id = FALSE)
	{
		
		if(!$user_id)$user_id = $this->session->userdata('user_id');
		//can not remove admin from any group.
//		if ($this->is_admin()) return FALSE;

		//every group must have a dashboard.
		$this->db->where('user_id',$user_id)
		->delete($this->tables['users_actions']);

		return $this->db->affected_rows() > 0;
	}

	function get_user_actions($user_id = false)
	{
		$user_id || $user_id     = $this->session->userdata('user_id');

		$permissions = $this->db->select($this->tables['perms'].'.pid as id , '.$this->tables['perms'].'.name , '.$this->tables['users_actions'].'.can_create as create, '.$this->tables['users_actions'].'.can_read as read ,'.$this->tables['users_actions'].'.can_update as update ,'.$this->tables['users_actions'].'.can_delete as delete')
		->where($this->tables['users_actions'].'.user_id',$user_id)
		->join($this->tables['perms'],$this->tables['users_actions'].'.pid = '.$this->tables['perms'].'.pid')
		->get($this->tables['users_actions']);


		return $permissions;
	}

	function get_user_prem_action($user_id,$perm_id)
	{

		return $this->db->select($this->tables['users_actions'].'.can_read as read,'.
			$this->tables['users_actions'].'.can_update as update,'.
			$this->tables['users_actions'].'.can_delete as delete,'.
			$this->tables['users_actions'].'.can_create as create,'.
			$this->tables['perms'].'.description as name,'.
			$this->tables['perms'].'.pid')
		->where([$this->tables['users_actions'].'.pid'=>$perm_id,$this->tables['users_actions'].'.user_id'=>$user_id])
		->join($this->tables['perms'],$this->tables['perms'].'.pid = '.$perm_id)
		->get($this->tables['users_actions'])->row_array();

	}
	
	function get_user_perms_actions($user_id = FALSE,$groups = FALSE){
		//if no usser or group id is set we use the logged in users id.
		
		if(!$user_id) $user_id = $this->session->usetdata('user_id');
		if(!$groups) $groups = $this->get_user_groups($user_id);
		
		
		return $this->filter_actions($user_id,$groups);
	}

	public function build_admin_links()
	{
		
		/*$user_id = $this->session->userdata('user_id');*/
		
		if ($this->is_admin()) {
			// get all permission if user is admin
			$perms = $this->db->where('protected','1')->order_by('pid', 'ASC')->get($this->tables['perms'])->result();
			$with_child = $this->child_permissions($perms);
			return $with_child;

		}
		else
		{
			$user_groups = $this->get_users_groups()->result();

		}
		
		$permissions = $this->_filter_permissions($user_groups);
		
		return $this->child_permissions($permissions);

	}

	private function _filter_permissions($groups_ids,$user_id = FALSE)
	{
		$perms = array();
		
		if(!$user_id)$user_id = $this->session->userdata('user_id');
		
		$groups_ids_arr = $this->_filter_group_id($groups_ids);
		
		foreach ($groups_ids_arr as $group_id)
		{
			$perms_arr[] = $this->get_user_permissions($group_id,$user_id)->result();

		}

		// if we've gotten to here we're using the $perms_arr
		foreach ($perms_arr as $perm_arr)
		{
			// ugh, merge ALL the arrays and then make sure they're not listed twice...
			$perms = array_unique(array_merge($perms, $perm_arr), SORT_REGULAR);
			$prems->{"link"} = admin_url($perms->name);
		}

		return $perms;
	}
	
	function child_permissions($perm)
	{
		if(!is_array($perm))
		{
			$perm = array($perm);
		}
		
		foreach ($perm as $permission){
			
			$permission->link = admin_url($permission->name);
			
			if(!$this->db->table_exists('permissions_child')){
				$permission->child = array();
			}else{
				$child = $this->db->where('parent_id',$permission->pid)
											->order_by('position','ASC')
											->get('permissions_child')->result();
			}
			
				
			
			if(!empty($child)){
				$permission->{'child'} =$child;
				foreach($permission->{'child'} as $k){
					if($k->name=='index'){
						$k->{'link'}=admin_url($permission->name.'/'.$k->slug);
					}else{
						$k->{'link'}=admin_url($permission->name.'/'.$k->name);
					}
				}
			}else{
				$permission->{'child'} = NULL;
			}
			
			
		}
		
		return $perm;
	}

	function filter_actions($user_id = FALSE , $groups = FALSE)
	{
		//we get all avaliable permissions
		$permissions = $this->permissions->_permissions();
		$cur_per = $this->get_users_permissions(FALSE,$user_id);

		foreach ($permissions as $perm) {
			
			//if user is admin activate all action
			if ($this->is_admin($user_id)) {
				$_perm_actions = [
					'read' => 1,
					'update' => 1,
					'delete' => 1,
					'create' => 1,
					'name' => $perm->description,
					'pid' => $perm->id,
					'readonly' => true,
				];
			}
			else
			{
				$_perm_actions = $this->get_user_prem_action($user_id , $perm->id);
				
				
				if ($_perm_actions == NULL)
				{

					// if the permission is not found in the user_action table we set it 0.
					$_perm_actions = [
						'read' => (config_item('default_user_action') == 'read')?1:0,
						'update' => (config_item('default_user_action') == 'update')?1:0,
						'delete' => (config_item('default_user_action') == 'delete')?1:0,
						'create' => (config_item('default_user_action') == 'create')?1:0,
						'name' => $perm->description,
						'pid' => $perm->id,
						'readonly' => TRUE,
						'disabled' => TRUE,
						
					];
						
				}
				foreach($cur_per as $grk){
					if($perm->id == $grk->id){
						$_perm_actions = array('readonly'=>FALSE)+$_perm_actions;
						break;
					}
				}
			}
			$perm_actions[] = (object)$_perm_actions;
		}
//print "<pre>";print_r($perm_actions);exit;

		return $perm_actions;
	}
}